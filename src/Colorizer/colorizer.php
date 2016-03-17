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

class ColorSpec {
	// Values expressed as integers 0-255.
	var $red;
	var $green;
	var $blue;
	
	/*
		A ColorSpec object can be initialized using integer RGB values, 
		or using the setHex() function with a 6-digit hex code.	
	*/
	function __construct($r=0, $g=0, $b=0) {
		if (is_numeric($r) === false || is_numeric($g) === false || is_numeric($b) === false) {
			throw new Exception('Cannot construct a color from non-numeric values.');
		}
		if ($r < 0 || $r > 255 || $g < 0 || $g > 255 || $b < 0 || $b > 255) {
			throw new Exception('One or more supplied color values were out of range.');
		}
		$this->red = intval($r);
		$this->green = intval($g);
		$this->blue = intval($b);
	}
	
	function setHex($hexcode) {
		if (substr($hexcode, 0, 1) == '#') {
			// Trim off the initial '#' character. 
			$hexcode = substr($hexcode, 1); 
		}
		if (preg_match('/^[a-f0-9]{6}$/i', $hexcode)) {
			// Convert the color to integer RGB values.
			$this->red = hexdec(substr($hexcode,0,2));
			$this->green = hexdec(substr($hexcode,2,2));
			$this->blue = hexdec(substr($hexcode,4,2));			
		} else {
			throw new Exception('Cannot construct a color from an invalid hex code.');
		}
	}
	
	public static function percentage($intColor=0) {
		if (is_numeric($intColor) === false || $intColor < 0 || $intColor > 255) {
			throw new Exception('Tried to get a percentage value for an invalid color.');
		}
		$pct = round(($intColor / 255) * 100);
		return $pct;
	}
}	
	
	
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