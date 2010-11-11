<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Creates meta tags.
 * See static_template 'plugin.meta'
 *
 * Revised for TYPO3 3.6 June/2003 by Kasper Skårhøj
 * XHTML compliant
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */


if (!is_object($this))	die('Not called from cObj!');

$globalMeta = $conf['global.'];
$local = $conf['local.'];
$regular = array();
$DC = array();

$localDescription = trim($this->stdWrap($local['description'],$local['description.']));
$localKeywords = trim($this->stdWrap($local['keywords'],$local['keywords.']));

// Unsetting secondary description and keywords if constant is not substituted!
if (substr($globalMeta['description_2'],0,2)=='{$')		{$globalMeta['description_2'] = '';}
if (substr($globalMeta['keywords_2'],0,2)=='{$')		{$globalMeta['keywords_2'] = '';}
if (!$conf['flags.']['useSecondaryDescKey'])	{
	unset($globalMeta['keywords_2']);
	unset($globalMeta['description_2']);
}

// Process them:
if ($globalMeta['description'] || $globalMeta['description_2'] || $localDescription)	{
	$val = trim($globalMeta['description']);
	if ($globalMeta['description_2'])	{
		$val = ($val?ereg_replace('\.$','',$val).'. ':'').$globalMeta['description_2'];
	}
	if ($localDescription)	{
		if ($conf['flags.']['alwaysGlobalDescription'] )	{
			$val = ereg_replace('\.$','',$localDescription).'. '.$val;
		} else {
			$val = $localDescription;
		}
	}
	$val=trim($val);
	$regular[] = '<meta name="description" content="'.htmlspecialchars($val).'" />';
	$DC[] = '<meta name="DC.Description" content="'.htmlspecialchars($val).'" />';
}
if ($globalMeta['keywords'] || $globalMeta['keywords_2'] || $localKeywords)	{
	$val = trim($globalMeta['keywords']);
	if ($globalMeta['keywords_2'])	{
		$val = ereg_replace(',$','',$val).','.$globalMeta['keywords_2'];
	}
	if ($localKeywords)	{
		if ($conf['flags.']['alwaysGlobalKeywords'] )	{
			$val = ereg_replace(',$','',$localKeywords).','.$val;
		} else {
			$val = $localKeywords;
		}
	}
	$val=trim(ereg_replace(',$','',trim($val)));
	$val=implode(',',t3lib_div::trimExplode(',',$val,1));
	$regular[] = '<meta name="keywords" content="'.htmlspecialchars($val).'" />';
	$DC[] = '<meta name="DC.Subject" content="'.htmlspecialchars($val).'" />';
}
if ($globalMeta['robots'])	{
	$regular[] = '<meta name="robots" content="'.htmlspecialchars($globalMeta['robots']).'" />';
}
if ($globalMeta['copyright'])	{
	$regular[] = '<meta name="copyright" content="'.htmlspecialchars($globalMeta['copyright']).'" />';
	$DC[] = '<meta name="DC.Rights" content="'.htmlspecialchars($globalMeta['copyright']).'" />';
}
if ($globalMeta['language'])	{
	$regular[] = '<meta http-equiv="content-language" content="'.htmlspecialchars($globalMeta['language']).'" />';
	$DC[] = '<meta name="DC.Language" scheme="NISOZ39.50" content="'.htmlspecialchars($globalMeta['language']).'" />';
}
if ($globalMeta['email'])	{
	$regular[] = '<link rev="made" href="mailto:'.htmlspecialchars($globalMeta['email']).'" />';
	$regular[] = '<meta http-equiv="reply-to" content="'.htmlspecialchars($globalMeta['email']).'" />';
}
if ($globalMeta['author'])	{
	$regular[] = '<meta name="author" content="'.htmlspecialchars($globalMeta['author']).'" />';
	$DC[] = '<meta name="DC.Creator" content="'.htmlspecialchars($globalMeta['author']).'" />';
}
if ($globalMeta['distribution'])	{
	$regular[] = '<meta name="distribution" content="'.htmlspecialchars($globalMeta['distribution']).'" />';
}
if ($globalMeta['rating'])	{
	$regular[] = '<meta name="rating" content="'.htmlspecialchars($globalMeta['rating']).'" />';
}
if ($globalMeta['revisit'])	{
	$regular[] = '<meta name="revisit-after" content="'.htmlspecialchars($globalMeta['revisit']).'" />';
}

$DC[] = '<link rel="schema.dc" href="http://purl.org/metadata/dublin_core_elements" />';


if (!$conf['flags.']['DC'])	{$DC=array();}

$content ='';
$content.= implode($regular,chr(10)).chr(10);
$content.= implode($DC,chr(10)).chr(10);

?>