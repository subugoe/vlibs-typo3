<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Steffen Ritter <info@steffen-ritter.net>
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
 * Generates sprites for t3skin and extensions
 *
 * @package TYPO3
 * @subpackage tx_extdeveval
 * @author	Steffen Ritter <info@steffen-ritter.net>
 */
class tx_extdeveval_sprites {
	/**
	 * Renders the view.
	 *
	 * @param template $template
	 * @return string
	 */
	public function renderView(template $template) {
		$content = 'Displays available sprites and their names.';

		$content .= $template->section('
			Sprite Overview',
			'Displays available sprites and their names.',
			0, 1
		);
		$content .= $template->sectionEnd();
		$content .= $template->spacer(10);
		$content .= $this->renderSpritesMenu();

		$content .= $template->section(
			'Sprite Generation',
			'Regenerates the sprites for t3skin (core). WARNING: Core files will be modified.',
			0, 1
		);
		$content .= $template->sectionEnd();
		$content .= $template->spacer(10);
		$content .= '<input type="submit" name="generateSprites" value="Generate sprites" />';

		$content .= $template->divider(20);

		if (t3lib_div::_POST('overview')) {
			$content .= $template->section('Sprite Overview', $this->renderSpriteOverview(), 0, 1);
			$content .= $template->sectionEnd();
		} elseif (t3lib_div::_POST('generateSprites')) {
			$content .= $template->section('Generating Sprites', $this->createSpritesForT3Skin(), 0, 1);
			$content .= $template->sectionEnd();
		}

		return $content;
	}

	/**
	 * Generates the sprites for t3skin.
	 *
	 * @return	string		HTML content
	 */
	public function createSpritesForT3Skin() {
		/** @var $generator t3lib_SpriteManager_SpriteGenerator */
		$generator = t3lib_div::makeInstance('t3lib_SpriteManager_SpriteGenerator', 't3skin');

		$this->unlinkT3SkinFiles();

		$data = $generator
			->setSpriteFolder(TYPO3_mainDir . 'sysext/t3skin/images/sprites/')
			->setCSSFolder(TYPO3_mainDir . 'sysext/t3skin/stylesheets/sprites/')
			->setOmmitSpriteNameInIconName(TRUE)
			->setIncludeTimestampInCSS(TRUE)
			->generateSpriteFromFolder(array(TYPO3_mainDir . 'sysext/t3skin/images/icons/'));

		$version = Tx_Extdeveval_Compatibility::convertVersionNumberToInteger(TYPO3_version);

			// IE6 fallback sprites have been removed with TYPO3 4.6
		if ($version < 4006000) {
			$gifSpritesPath = PATH_typo3 . 'sysext/t3skin/stylesheets/ie6/z_t3-icons-gifSprites.css';
			if (FALSE === rename($data['cssGif'], $gifSpritesPath)) {
				throw new tx_extdeveval_exception('The file "' . $data['cssGif'] . '" could not be renamed to "' . $gifSpritesPath . '"');
			}
		}

		$stddbPath = PATH_site . 't3lib/stddb/tables.php';
		$stddbContents = file_get_contents($stddbPath);
		$newContent = '$GLOBALS[\'TBE_STYLES\'][\'spriteIconApi\'][\'coreSpriteImageNames\'] = array(' . LF . TAB . '\''
			. implode('\',' . LF . TAB . '\'', $data['iconNames']) . '\'' . LF . ');' . LF;
		$stddbContents = preg_replace('/\$GLOBALS\[\'TBE_STYLES\'\]\[\'spriteIconApi\'\]\[\'coreSpriteImageNames\'\] = array\([\s\',\w-]*\);/' , $newContent, $stddbContents);

		if (FALSE === t3lib_div::writeFile($stddbPath, $stddbContents)) {
			throw new tx_extdeveval_exception('Could not write file "' . $stddbPath . '"');
		}

		$output = 'Sprites successfully regenerated';

		return $output;
	}

	/**
	 * @return string
	 */
	public function renderSpritesMenu() {
		$output = '
			<input type="submit" name="overview[all]" value="Available sprite icons" />
			<input type="submit" name="overview[single]" value="Single sprite icons" />
			<input type="submit" name="overview[overlays]" value="Sprite Overlays" />
		';

		return $output;
	}

	/**
	 * Renders the sprites overview.
	 *
	 * @return string
	 */
	public function renderSpriteOverview() {
		$command = t3lib_div::_POST('overview');
		switch (key($command)) {
			case 'all':
				$output = $this->renderAllSprites();
			break;
			case 'single':
				$output = $this->renderSingleSprites();
			break;
			case 'overlays':
				$output = $this->renderOverlays();
			break;
		}

		return $output;
	}


	/**
	 * Renders all available sprite icons.
	 *
	 * @return string
	 */
	protected function renderAllSprites() {
		$output = '';

		$iconsAvailable = $GLOBALS['TBE_STYLES']['spriteIconApi']['iconsAvailable'];
		foreach ($iconsAvailable as $icon) {
			$output .= '<div class="iconItem">' . t3lib_iconWorks::getSpriteIcon($icon) . ' ' . $icon . '</div>';
		}
		$output .= '<br style="clear:both" />';
		$output .= '<hr />';
		$output .= 'You can use any of these icons with:<br />';
		$example = 't3lib_iconWorks::getSpriteIcon($icon);';
		$output .= highlight_string($example, true);
		$output .= '<hr />';
		$output .= 'You add further icons from your own extension using:<br />';
		$example = '
// ext_tables.php:
$icons = array(\'extensions-myextension-icon1\', \'extensions-myextension-icon2\');
t3lib_SpriteManager::addIconSprite($icons,t3lib_extMgm::siteRelPath(\'myextension\') . \'myextension_sprite.css\');

// myextension_sprite.css:
.t3-icon-extensions-myextension {
	background-image:url(../../typo3conf/ext/myextension/myextension_sprite.gif);
}
.t3-icon-extensions-myextension-icon1 {	background-position: 0px 0px; }
.t3-icon-extensions-myextension-icon2 {	background-position: 0px -16px; }
				';
		$output .= highlight_string($example, true);

		return $output;
	}

	/**
	 * Renders single sprite icons.
	 *
	 * @return string
	 */
	protected function renderSingleSprites() {
		$output = '';

		$singleIcons = $GLOBALS['TBE_STYLES']['spritemanager']['singleIcons'];
		foreach ($singleIcons as $name => $icon) {
			$output .= '<div class="iconItem">' . t3lib_iconWorks::getSpriteIcon($name) . ' ' . $name . '</div>';
		}
		$output .= '<br style="clear:both" />';
		$output .= '<hr />';
		$output .= 'You can add further icons from your own extension using:<br />';

		$example = 'if(version_compare(TYPO3_version,\'4.4\',\'>\')) {
$icons = array(
	\'myicon\' => t3lib_extMgm::extRelPath($_EXTKEY) . \'myicon.gif\',
);
t3lib_SpriteManager::addSingleIcons($icons, $_EXTKEY);
}';

		$output .= highlight_string($example, true);

		return $output;
	}

	/**
	 * Renders sprite overlays.
	 *
	 * @return string
	 */
	protected function renderOverlays() {
		$output = '';

		$overlays = $GLOBALS['TBE_STYLES']['spriteIconApi']['spriteIconRecordOverlayNames'];
		$overlayOptions = array(
			'class' => 't3-icon-overlay'
		);
		foreach ($overlays as $overlay) {
			$output .= '<div class="iconItem">' . t3lib_iconWorks::getSpriteIcon('mimetypes-other-other', array(), array($overlay => array())) . ' ' . $overlay . '</div>';
		}
		$output .= '<br style="clear:both" />';
		$output .= '<hr />';
		$output .= 'You can add any of those overlays to your icons using:<br />';
		$example = 't3lib_iconWorks::getSpriteIcon(\'mimetypes-other-other\',array(), array(\'overlayname\'=>array()))';

		$output .= highlight_string($example, true);

		return $output;
	}



	/**
	 * Unlinks old T3Skin files.
	 *
	 * @return void
	 */
	protected function unlinkT3SkinFiles() {
		$files = array(
			'stylesheets/ie6/z_t3-icons-gifSprites.css',
			'stylesheets/sprites/t3skin.css',
			'images/sprites/t3skin.png',
			'images/sprites/t3skin.gif',
		);

		foreach ($files as $file) {
			$filePath = PATH_typo3 . 'sysext/t3skin/' . $file;
			if (file_exists($filePath) && (FALSE === unlink($filePath))) {
				throw new tx_extdeveval_exception('The file "' . $filePath . '" could not be removed');
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extdeveval/mod1/class.tx_extdeveval_sprites.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extdeveval/mod1/class.tx_extdeveval_sprites.php']);
}
?>