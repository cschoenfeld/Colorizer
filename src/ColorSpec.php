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

?>