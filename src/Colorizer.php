<?php
namespace CSchoenfeld;
use Exception;

/*
	Color helper classes.
	
	Generate SVG filters that can be used to apply color overlays 
	to images in a web page via CSS.
	
	For usage details, see the file: README
	@author Charles Schoenfeld
	@version 1.0

*/	
	
class Colorizer {

	public static function makeFilter($name, $color, $tintStrength=100) {
		// Check for valid input.
		if (is_string($name) === false || empty($name) === true) {
			throw new Exception('Cannot make an SVG filter without a name.');
		}
		if (is_object($color) === false) {
			throw new Exception('The color used to make an SVG filter must be a ColorSpec object.');
		}
		$fullClass = get_class($color);
		$classPcs = explode('\\', $fullClass);
		$className = array_pop($classPcs);
		if ($className != 'ColorSpec') {
			throw new Exception('The color supplied to make an SVG filter must be a ColorSpec object.');
		}
		if (is_numeric($tintStrength) === false || $tintStrength < 0 || $tintStrength > 100) {
			throw new Exception('The color applied to your SVG filter must have a strength between 0 and 100%.');
		}
		
		// Calculate the colors to use in the matrix.
		$tintRed = (ColorSpec::percentage($color->red) * $tintStrength / 100) / 100;
		$origRed = 1 - ($tintStrength / 100);
		$tintGreen = (ColorSpec::percentage($color->green) * $tintStrength / 100) / 100;
		$origGreen = 1 - ($tintStrength / 100);
		$tintBlue = (ColorSpec::percentage($color->blue) * $tintStrength / 100) / 100;
		$origBlue = 1 - ($tintStrength / 100);
		
		// Write the SVG matrix.
		$out = "<svg class=\"defs-only\">
			<filter id=\"" . $name . "\" color-interpolation-filters=\"sRGB\" x=\"0\" y=\"0\" height=\"100%\" width=\"100%\">
			<feColorMatrix type=\"matrix\"
				values=\"$origRed 0 0 0  $tintRed 
					    $origGreen 0 0 0  $tintGreen 
						$origBlue 0 0 0  $tintBlue 
						0 0 0 1  0\" />
			</filter>\n</svg>\n";
		return $out;
	}
	
}
?>