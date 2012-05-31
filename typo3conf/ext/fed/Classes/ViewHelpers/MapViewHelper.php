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
***************************************************************/

/**
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_MapViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	protected $tagName = 'div';

	protected $instanceName;

	/**
	 * @var array
	 */
	protected $options = array();

	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('lat', 'float', 'Latitude');
		$this->registerArgument('lng', 'float', 'Longitude');
		$this->registerArgument('icon', 'string', 'Icon filename', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Icons/MapMarker.png');
		$this->registerArgument('iconCenterX', 'int', 'Icon pivot coordinate X');
		$this->registerArgument('iconCenterY', 'int', 'Icon pivot coordinate Y');

	}

	/**
	 * @param string $api Optional full URL to the Google Maps API you wish to use - must be v3
	 * @param string $width Width of map element
	 * @param string $height Height of map element
	 * @param string $backgroundColor Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as the user pans. This option can only be set when the map is initialized.
	 * @param boolean $disableDefaultUI Enables/disables all default UI. May be overridden individually.
	 * @param boolean $disableDoubleClickZoom Enables/disables zoom and center on double click. Enabled by default.
	 * @param boolean $draggable If TRUE, enables cursor(s) to be dragged around on the map. Dragging is disabled by default.
	 * @param string $draggableCursor The name or url of the cursor to display on a draggable object.
	 * @param string $draggingCursor The name or url of the cursor to display when an object is dragging.
	 * @param string $keyboardShortcuts If false, prevents the map from being controlled by the keyboard. Keyboard shortcuts are enabled by default.
	 * @param boolean $mapTypeControl The initial enabled/disabled state of the Map type control.
	 * @param float $maxZoom The maximum zoom level which will be displayed on the map. If omitted, or set to null, the maximum zoom from the current map type is used instead.
	 * @param float $minZoom The minimum zoom level which will be displayed on the map. If omitted, or set to null, the minimum zoom from the current map type is used instead.
	 * @param boolean $noClear If true, do not clear the contents of the Map div.
	 * @param boolean $panControl The enabled/disabled state of the pan control.
	 * @param boolean $scaleControl The initial enabled/disabled state of the scale control.
	 * @param boolean $scrollwheel If false, disables scrollwheel zooming on the map. The scrollwheel is enabled by default.
	 * @param boolean $streetViewControl The initial enabled/disabled state of the Street View pegman control.
	 * @param float $zoom The initial Map zoom level. Required.
	 * @param boolean $zoomControl The enabled/disabled state of the zoom control.
	 * @param string $instanceName Javascript instance name to use. Default is "map".
	 * @param string $registerWith Javascript function to call once map object is ready.
	 * @param string $mapTypeId Type of map to display, defaults to google.maps.MapTypeId.ROADMAP

	 */
	public function render(
			// CUSTOM parameters
			$api=NULL,
			$width="450px",
			$height="550px",
			// next is Google Map parameters
			$backgroundColor=NULL,
			$disableDefaultUi=FALSE,
			$disableDoubleClickZoom=TRUE,
			$draggable=FALSE,
			$draggableCursor=NULL,
			$draggingCursor=NULL,
			$keyboardShortcuts=TRUE,
			$mapTypeControl=NULL,
			$maxZoom=NULL,
			$minZoom=NULL,
			$noClear=FALSE,
			$panControl=TRUE,
			$scaleControl=TRUE,
			$scrollWheel=TRUE,
			$streetViewControl=TRUE,
			$zoom=7,
			$zoomControl=TRUE,
			$instanceName=NULL,
			$registerWith=NULL,
			$mapTypeId='google.maps.MapTypeId.ROADMAP'
			) {
		if ($api === NULL) {
			$api = "http://maps.google.com/maps/api/js?v=3.2&sensor=true";
		}
		$min = 100000;
		$max = 999999;
		$elementId = 'gm' . rand($min, $max);

		if ($instanceName) {
			$this->instanceName = $instanceName;
		} else {
			$this->instanceName = $instanceName = uniqid('map');
		}

		$this->options['mapTypeId'] = $mapTypeId;

		$this->includeFile($api);

		$this->templateVariableContainer->add('layers', array());
		$this->templateVariableContainer->add('infoWindows', array());

		$this->inheritArguments();
		$children = $this->renderChildren();

		$markers = $this->renderMarkers();

		$lat = $this->arguments['lat'] ? $this->arguments['lat'] : 56.25;
		$lng = $this->arguments['lng'] ? $this->arguments['lng'] : 10.45;
		$lat = strval($lat);
		$lng = strval($lng);

		$options = $this->getMapOptions();

		if (strlen($registerWith) > 0) {
			$register = "{$registerWith}({$instanceName}, {$instanceName}markers);";
		}

		if (is_numeric($width)) {
			$width .= 'px';
		}
		if (is_numeric($height)) {
			$height .= 'px';
		}
		$js = <<< INIT
var {$instanceName};
var {$instanceName}markers = [];
var {$instanceName}timeout;
var {$instanceName}refreshList = function() {
	var i;
	var markerlist = jQuery('.fed-maplist');
	for (i=0; i<{$instanceName}markers.length; i++) {
		var marker = {$instanceName}markers[i];
		var row = markerlist.find('tr.' + marker.get('id'));
		if ({$instanceName}.getBounds().contains(marker.getPosition())) {
			row.removeClass('off');
		} else {
			row.addClass('off');
		}
	};
	if (typeof tableSorter != 'undefined') {
		tableSorter.fnDraw();
	};
};
var {$instanceName}timer = function() {
	clearTimeout({$instanceName});
	{$instanceName}timeout = setTimeout({$instanceName}refreshList, 400);
};

jQuery(document).ready(function() {
	var myLatlng = new google.maps.LatLng({$lat}, {$lng});
	var myOptions = {$options};
	var infoWindow = infoWindow = new google.maps.InfoWindow({maxWidth: 400, maxHeight: 400});
	{$instanceName} = new google.maps.Map(document.getElementById("{$elementId}"), myOptions);
{$markers}
	// check for a map list instance. If found, hook it up to various map events
	var listElement = jQuery('.fed-maplist');
	if (listElement.html() != '') {
		//{$instanceName}refreshList();
		google.maps.event.addListener({$instanceName}, 'zoom_changed', {$instanceName}timer);
		google.maps.event.addListener({$instanceName}, 'bounds_changed', {$instanceName}timer);
		google.maps.event.addListener({$instanceName}, 'center_changed', {$instanceName}timer);
		google.maps.event.addListener({$instanceName}, 'resize', {$instanceName}timer);
	};
	{$register}
});

INIT;

		$css = <<< CSS
#{$elementId} {
	width: {$width};
	height: {$height};
}
CSS;

		$this->includeHeader($js, 'js');
		$this->includeHeader($css, 'css');
		$this->includeFile(t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/Base64.js');

		$this->tag->addAttribute('id', $elementId);
		$this->tag->addAttribute('class', $this->arguments['class']);

		$this->tag->setContent($children);

		return $this->tag->render();
	}

	public function get($name) {
		if ($this->templateVariableContainer->exists($name)) {
			return $this->templateVariableContainer->get($name);
		} else {
			return FALSE;
		}
	}

	public function reassign($name, $value) {
		if ($this->templateVariableContainer->exists($name)) {
			$this->templateVariableContainer->remove($name);
		}
		$this->templateVariableContainer->add($name, $value);
	}

	public function inheritArguments() {
		$config = $this->get('config');
		if ($config === FALSE) {
			$config = array();
		}
		$arguments = $this->getArguments();
		foreach ($arguments as $name=>$value) {
			$config[$name] = $value;
		}
		$this->reassign('config', $config);
		return $config;
	}

	public function getArguments() {
		$args = array();
		$defs = $this->prepareArguments();
		foreach ($defs as $def) {
			$name = $def->getName();
			if ($this->arguments[$name]) {
				$args[$name] = $this->arguments[$name];
			}
		}
		return $args;
	}

	public function renderMarkers() {
		$layers = $this->get('layers');
		$allMarkers = array();
		foreach ($layers as $name=>$markers) {
			foreach ($markers as $index=>$marker) {
				$markerId = $marker['id'];
				$infoWindow = $marker['infoWindow'];
				unset($marker['infoWindow'], $marker['properties'], $marker['data']);
				$options = $this->getMarkerOptions($marker);
				$str = "var {$markerId} = new google.maps.Marker($options); {$markerId}.set('id', '{$markerId}'); {$this->instanceName}markers.push({$markerId}); ";
				if ($infoWindow) {
					$infoWindow = str_replace("\n", "", $infoWindow);
					$infoWindow = stripslashes($infoWindow);
					$infoWindow = base64_encode($infoWindow);
					$str .= "google.maps.event.addListener({$markerId}, 'click', function(event) {
						var infoWindowContent = Base64.decode(\"{$infoWindow}\");
						infoWindow.close();
						infoWindow.setOptions({maxWidth: 600});
						infoWindow.open({$this->instanceName}, {$markerId});
						infoWindow.setContent(infoWindowContent);
					}); ";
				}
				array_push($allMarkers, $str);
			}
		}
		$this->reassign('layers', $layers);
		return implode("\n", $allMarkers);
	}

	public function getOptions($object) {
		$lines = array();
		foreach ($object as $name=>$value) {
			if (is_numeric($value)) {
				// NOOP
			} else if (is_string($value)) {
				$value = "\"{$value}\"";
			} else if (is_null($value)) {
				continue;
			} else if (is_bool($value)) {
				$value = $value ? 'true' : 'false';
			} else if (is_array($value)) {
				$value = "\"" . implode(',', $value) . "\"";
			}
			$value = str_replace("\n", "", trim($value));
			$lines[] = "\"{$name}\":{$value}";
		}
		return $lines;
	}

	public function getMapOptions() {
		$lines = array(
			"center: myLatlng",
        	"mapTypeId: " . $this->options['mapTypeId'],
			"size: new google.maps.Size(500,500)"
		);
		$removables = array('mapTypeId');
		$args = $this->getArguments();
		foreach ($args as $k=>$v) {
			if (in_array($k, $removables)) {
				unset($args[$k]);
			}
		}
		$lines = array_merge($this->getOptions($args), $lines);
		return $this->objWrap($lines);
	}

	public function getMarkerOptions($marker) {
		$removables = array(
			"width", "height", "disableDefaultUi", "disableDoubleClickZoom", "draggable",
			"keyboardShortcuts", "mapTypeControl", "noClear", "panControl", "scaleControl",
			"scrollWheel", "streetViewControl", "zoom", "zoomControl", "instanceName", "class",
			"data", "properties"
		);
		$icon = trim($marker['icon']);
		$lat = strval(floatval($marker['lat']));
		$lng = strval(floatval($marker['lng']));
		if ($marker['iconCenterX'] || $marker['iconCenterY']) {
			if (!$marker['iconCenterY']) {
				$marker['iconCenterY'] = 16;
			}
			if (!$marker['iconCenterX']) {
				$marker['iconCenterX'] = 8;
			}
			$markerPivot = "new google.maps.Point({$marker['iconCenterX']}, {$marker['iconCenterY']})";
		} else {
			$markerPivot = 'null';
		}
		$lines = array(
			"position: new google.maps.LatLng({$lat},{$lng})",
			"icon: new google.maps.MarkerImage('{$icon}', null, null, {$markerPivot})",
			"map: {$this->instanceName}",
		);
		unset($marker['icon']);
		$lines = array_merge($lines, $this->getOptions($marker));
		foreach ($lines as $k=>$v) {
			$key = substr($v, 0, strpos($v, ':'));
			if (in_array($key, $removables)) {
				unset($lines[$k]);
			}
		}
		return $this->objWrap($lines);
	}

	public function objWrap($lines) {
		$str = "{".implode(", ", $lines)."}";
		return $str;
	}



}
?>
