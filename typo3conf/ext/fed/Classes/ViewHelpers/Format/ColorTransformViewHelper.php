<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * I f'ing love Coffee.
 *
 * Does what fed:style does but renders the style sheet as if it were a Fluid template.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Extbase\Widget
 */
class Tx_Fed_ViewHelpers_Format_ColorTransformViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('hex', 'string', 'If set, processes color as [r,g,b] converted from HEX');
		$this->registerArgument('rgb', 'array', 'If set, processes color as [r,g,b] from this value');
		$this->registerArgument('r', 'integer', 'If set, assumes that all of "r", "g" and "b" attributes are given, then processes as [r,g,b]');
		$this->registerArgument('g', 'integer', 'If set, assumes that all of "r", "g" and "b" attributes are given, then processes as [r,g,b]');
		$this->registerArgument('b', 'integer', 'If set, assumes that all of "r", "g" and "b" attributes are given, then processes as [r,g,b]');
		$this->registerArgument('transformAll', 'integer', 'Modification to apply to all color channels, positive/negative values allowed');
		$this->registerArgument('transformRed', 'integer', 'Modification to apply to red color channel, positive/negative values allowed');
		$this->registerArgument('transformGreen', 'integer', 'Modification to apply to green color channel, positive/negative values allowed');
		$this->registerArgument('transformBlue', 'integer', 'Modification to apply to blue color channel, positive/negative values allowed');
	}

	/**
	 * Make CSSPresso.
	 *
	 * @return string
	 */
	public function render() {

		if (is_array($this->arguments['rgb'])) {
			list ($r, $g, $b) = $this->arguments['rgb'];
		} else if ($this->arguments['r'] && $this->arguments['g'] && $this->arguments['b']) {
			foreach (array('r', 'g', 'b') as $color) {
				$$color = $this->arguments[$color];
			}
		} else if ($this->arguments['hex']) {
			$splitHex = str_split($this->arguments['hex'], 2);
			$r = hexdec($splitHex[0]);
			$g = hexdec($splitHex[1]);
			$b = hexdec($splitHex[2]);
		} else {
			throw new Exception('ColorTransformViewHelper requires at least one color input argument, none given (or not all three r, g, b arguments used)', 1311080315);
		}

		$hex = "";
		if (isset($this->arguments['transformAll'])) {
			$converted = $this->transformAll($r, $g, $b, $this->arguments['transformAll']);
		} else {
			$converted = array(
				$this->transformColor($r, $this->arguments['transformRed']),
				$this->transformColor($g, $this->arguments['transformGreen']),
				$this->transformColor($b, $this->arguments['transformBlue'])
			);
		}
		foreach ($converted as $color) {
			$hexColor = dechex($color);
			$hexColor = str_pad($hexColor, 2, '0', STR_PAD_LEFT);
			$hex .= $hexColor;
		}
		$hex = strtoupper($hex);
		return $hex;
	}

	/**
	 * Transform all colors using $modification value
	 *
	 * @param integer $r
	 * @param integer $g
	 * @param integer $b
	 * @param integer $modification
	 * @return array
	 */
	protected function transformAll($r, $g, $b, $modification) {
		return array(
			'r' => $this->transformColor($r, $modification),
			'g' => $this->transformColor($g, $modification),
			'b' => $this->transformColor($b, $modification)
		);
	}

	/**
	 * Transform a single color using $modification value
	 *
	 * @param integer $color
	 * @param integer $modification
	 * @return int
	 */
	protected function transformColor($color, $modification) {
		$newColor = ($color + $modification);
		if ($newColor < 0) {
			$newColor = 0;
		} else if ($newColor > 255) {
			$newColor = 255;
		}
		return $newColor;
	}

}

?>
