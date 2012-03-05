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
class Tx_Fed_ViewHelpers_TagCloudViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	public $tagName = 'div';

	/**
	 * Argument initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('instanceName', 'string', 'Javascript instance name for SWFObject instance', FALSE, 'wpcumulus');
		$this->registerArgument('width', 'int', 'Width of the tag cloud element, applies best to mode=flash', FALSE, '300');
		$this->registerArgument('height', 'int', 'Height of the tag cloud element, applies best to mode=flash', FALSE, '300');
		$this->registerArgument('color', 'string', 'Color of the text used - can be overridden by styling each link individually', FALSE, '000000');
		$this->registerArgument('bgcolor', 'string', 'Background color', FALSE, 'ffffff');
		$this->registerArgument('hicolor', 'string', 'Color of highlighted text', FALSE, '220000');
		$this->registerArgument('transparent', 'boolean', 'Name says it all', FALSE, TRUE);
		$this->registerArgument('speed', 'int', 'Rotation speed', FALSE, 50);
		$this->registerArgument('distribute', 'boolean', 'Distribute evenly', FALSE, TRUE);
		$this->registerArgument('fontSizeMin', 'number', 'Minimum allowed size of fonts - tag font size is scaled to match occurrence percentage', FALSE, 9);
		$this->registerArgument('fontSizeMax', 'number', 'Maximum allowed size of fonts - tag font size is scaled to match occurrence percentage', FALSE, 30);
		$this->registerArgument('mode', 'string', 'Rendering mode. Currently supported is "flash", "html" and "custom" (uses tag content instead of creating content)', FALSE, 'flash');
		$this->registerArgument('useIntervals', 'boolen', 'If TRUE, renders sizes based on 10 intervals between fontSizeMin and fontSizeMax - if FALSE, size is more precise but less distinct', FALSE, TRUE);
		$this->registerArgument('tags', 'array', 'Optional list of tags. If no supplied the content of the fed:tagCloud tag becomes the tag list. Other ViewHelpers included to register and count tags on-the-fly');
		$this->registerArgument('merge', 'string', 'If "argument", prevents gathering tags from the contents of the fed:tagCloud template tag ("argument" requires the $tags argument or cloud will be empty!). If "both", combines $tags with tags rendered via renderChildren(). If "content" only tags rendered defined by other ViewHelpers are counted. You can use this to merge or switch between two tag sets on-the-fly. See manual.', FALSE, 'both');
		$this->registerArgument('titleIsTag', 'boolean', 'If TRUE, makes the ViewHelper use the "title" property of each tag you register instead of the "tag" attribute or innerHTML. Use this if you are counting lists of model objects or UIDs for instance; and let "tag" be the UID/model object to register', FALSE, FALSE);
		$this->registerArgument('divider', 'string', 'Piece of HTML to insert between rendered tags, used in HTML mode only');
		$this->registerArgument('tagName', 'string', 'Tag name to use in HTML output', FALSE, 'div');
	}

	/**
	 * Render a Cumulus Tag Cloud - thanks to Roy Tanck
	 *
	 * @return string
	 */
	public function render() {

		$this->tagName = $this->arguments['tagName'];
		$this->setTagStorage(array());
		$content = $this->renderChildren();
		$tags = $this->processTags();


		if ($this->arguments['tags']) {
			// tags were supplied as argument, combine with ones rendered in tag content if combine=TRUE, else overwrite
			if (count($tags) > 0) {
				$tags = $this->mergeTags((array) $this->arguments['tags'], (array) $tags);
			} else {
				$tags = $this->arguments['tags'];
			}
		}

		$renderedTagCloud = $this->renderTagCloud($this->arguments['mode'], $tags);
		if ($this->arguments['mode'] === 'custom') {
			$this->setTagStorage($renderedTagCloud);
			$content = $this->renderChildren();
		} else {
			$classes = explode(' ', $this->arguments['class']);
			array_push($classes, 'fed-extbase-tagcloud');
			$classes = implode(' ', $classes);
			$this->tag->addAttribute('class', $classes);
			$this->tag->setContent($renderedTagCloud);
			$content = $this->tag->render();
		}
		$this->templateVariableContainer->remove('tags');
		return $content;
	}

	/**
	 * Process and merge tags according to settings
	 * @return array
	 */
	protected function processTags() {
		$childTags = $this->getTagStorage();
		$argTags = $this->arguments['tags'];
		$tags = array();
		switch ($this->arguments['merge']) {
			case 'argument': return $argTags;
			case 'content': return $childTags;
			case 'both': default:
		}
		if ($childTags) {
			$tags = $this->mergeTags($tags, $childTags);
		}
		if ($argTags) {
			$tags = $this->mergeTags($tags, $argTags);
		}
		return $tags;
	}

	/**
	 * Render the tag cloud HTML to be inserted in DOM. Output depends on settings
	 * @param string $mode
	 * @param array $tags
	 */
	protected function renderTagCloud($mode, $tags) {
		$totalTagOccurrences = 0;
		$min = $this->arguments['fontSizeMin'];
		$max = $this->arguments['fontSizeMax'];
		foreach ($tags as $name=>$tag) {
			$totalTagOccurrences += $tag['occurrences'];
		}
		foreach ($tags as $name=>$tag) {
			$tag['percentage'] = ($tag['occurrences'] / $totalTagOccurrences);
			$tag['percentageInterval'] = $this->getPercentageInterval($tag['percentage']);
			if ($this->arguments['useIntervals'] === TRUE) {
				$tag['size'] = $this->getSize($min, $max, $tag['percentageInterval']);
			} else {
				$tag['size'] = $this->getSize($min, $max, $tag['percentage']);
			}
			$tags[$name] = $tag;
		}
		switch ($mode) {
			case 'html': return $this->renderHTMLTagCloud($tags);
			case 'flash': default: return $this->renderFlashTagCloud($tags);
			case 'custom': return $tags;
		}
	}

	/**
	 * Get the relative size as a ratio of 0-100% of the distance between $min and $max added to $min.
	 * @param float $min
	 * @param float $max
	 * @param float $percentage
	 * @return float
	 */
	protected function getSize($min, $max, $percentage) {
		return floatval(number_format(($min + ( ($max - $min) * $percentage)), 2));
	}

	/**
	 * Calculate the positional interval of $percentage as rounded off to 0.1 precision
	 * @param float $percentage
	 * @return float
	 */
	protected function getPercentageInterval($percentage) {
		$percentageInterval = 0;
		while ($percentageInterval < $percentage) {
			$percentageInterval += 0.1;
		}
		return $percentageInterval;
	}

	/**
	 * Merge two tag arrays - increase occurrence if tag exists in either array
	 * @param array $t1
	 * @param array $t2
	 * @return array
	 */
	protected function mergeTags($t1, $t2) {
		foreach ($t2 as $name=>$tag) {
			$tag = $this->objToArray($tag);
			if (isset($t1[$name]) === FALSE) {
				$t1[$name] = $tag;
			} else {
				$t1[$name] = $this->objToArray($t1[$name]);
				$t1[$name]['occurrences'] += $tag['occurrences'];
			}
		}
		return $t1;
	}

	/**
	 * Convert a simple (stdClass or class with public properties) object to an array
	 * @param array $obj
	 */
	protected function objToArray($obj) {
		if (is_array($obj)) {
			return $obj;
		}
		$arr = array();
		foreach ($obj as $k=>$v) {
			$arr[$k] = $v;
		}
		return $arr;
	}

	/**
	 * Renders tags as HTML
	 * @param array $tags
	 * @return string
	 */
	protected function renderHTMLTagCloud($tags) {
		$html = "";
		$num = 1;
		foreach ($tags as $tag) {
			if (is_object($tag)) {
				$tag = $this->objToArray($tag);
			}
			$title = $tag['title'];
			$href = $tag['href'];
			$style = $tag['style'];
			$tagName = $tag['tag'];
			$size = $tag['size'];
			$link = "<a href='{$href}' title='{$title}' rel='tag' style='font-size: {$size}px; {$style}'>";
			if ($this->arguments['titleIsTag']) {
				$link .= $title;
			} else {
				$link .= $tagName;
			}
			$link .= "</a>";
			if ($num < count($tags)) {
				$link .= $this->arguments['divider'];
			}
			$html .= $link;
			$num++;
		}
		return $html;
	}

	/**
	 * Render the output necessary for a Flash tag cloud
	 * @author Roy Tanck
	 * @author Claus Due
	 * @param array $tags
	 */
	protected function renderFlashTagCloud($tags) {
		$elementId = uniqid('wpcumulus_');
		$movie = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Flash/com.roytanck.wpcumulus.swf';
		$tagcloud = $this->renderTags($tags);
		$tagcloud = str_replace( "&nbsp;", " ", $tagcloud);
		$encodedTagCloud = '<tags>' . $tagcloud . '</tags>';
		$hostedLibrary = "http://ajax.googleapis.com/ajax/libs/swfobject/2/swfobject.js";
		$expressInstall = "http://www.adobe.com/go/getflashplayer";
		$distribute = $this->arguments['distribute'] ? 'true' : 'false';
		$script = <<< SCRIPT
swfobject.embedSWF("{$movie}", "{$elementId}", "{$this->arguments['width']}", "{$this->arguments['height']}", "9", "{$expressInstall}",
{
	tcolor: '0x{$this->arguments['color']}',
	hicolor: '0x{$this->arguments['hicolor']}',
	tspeed: '{$this->arguments['speed']}',
	distr: '{$distribute}',
	mode: 'tags',
	tagcloud: "{$encodedTagCloud}",
	tcolor2: '0x{$this->arguments['color']}'
});
SCRIPT;
		$this->includeFile($hostedLibrary);
		$this->includeHeader($script, 'js');
		return "<div id='{$elementId}'></div>";
	}

	/**
	 * Render output when no tags are found
	 * @return string
	 */
	protected function renderNoContent() {
		return "<a href='' title='no tags'>No tags available</a>";
	}

	/**
	 * Renders $tags into proper flashvars format
	 * @param array $tags
	 * @return string
	 */
	protected function renderTags($tags) {
		$cloud = "";
		foreach ($tags as $tagData) {
			$tagData = $this->objToArray($tagData);
			extract($tagData);
			$text = $this->arguments['titleIsTag'] ? $title : $tag;
			if (strpos($href, 'http://') !== 0) {
				$href = "http://" . $_SERVER['HTTP_HOST'] . '/' . $href;
			}
			$cloud .= <<< TAG
<a href='{$href}' style='font-size: {$size}px; {$style}' rel='tag' title='{$title}'>{$text}</a>
TAG;
		}
		return $cloud;
	}

	/**
	 * Register $occurrences occurrences of tag $tagName
	 * @param string $tagName
	 * @param int $occurrences
	 */
	protected function registerOccurrence($tagName, $occurrences=1) {
		$tags = $this->getTagStorage();
		if ($tags[$tagName]) {
			$tags[$tagName]['occurrences'] += $occurrences;
		} else {
			$tags[$tagName] = array(
				'tag' => $tagName,
				'occurrences' => $occurrences,
				'href' => '',
				'title' => '',
				'style' => ''
			);
		}
		$this->setTagStorage($tags);
	}

	/**
	 * Add a tag with full configuration (occurrences, href, title etc)
	 * @param array $config
	 */
	protected function addTag($config) {
		$tagName = $config['tag'];
		$tags = $this->getTagStorage();
		if (isset($tags[$tagName]) === FALSE) {
			$tags[$tagName] = $config;
		} else {
			$tags[$tagName]['occurrences'] += $config['occurrences'];
		}
		$this->setTagStorage($tags);
	}

	/**
	 * Get the current "working copy" tag storage
	 * @return array
	 */
	protected function getTagStorage() {
		if ($this->templateVariableContainer->exists('tags')) {
			return $this->templateVariableContainer->get('tags');
		} else {
			return array();
		}
	}

	/**
	 * Set the current "working copy" tag storage
	 * @param array $tags
	 */
	protected function setTagStorage($tags) {
		if ($this->templateVariableContainer->exists('tags')) {
			$this->templateVariableContainer->remove('tags');
		}
		$this->templateVariableContainer->add('tags', $tags);
	}

}


?>