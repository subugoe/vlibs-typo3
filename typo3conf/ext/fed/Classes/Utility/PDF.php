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
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_PDF implements t3lib_Singleton {

	/**
	 * @var string
	 */
	protected $wkhtmltopdf;

	/**
	 * Designed for Bootstrap usage. Overrides headers and outputs PDF source
	 * @return void
	 */
	public function run() {
		$post = $_POST['tx_fed_pdf'];
		$source = $post['html'];
		$filename = $post['filename'];
		$this->stylesheet = $post['stylesheet'];
		$this->wkhtmltopdf = $post['wkhtmltopdf'];
		$pdf = $this->grabPDF($source);
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen($pdf));
		header("Content-disposition: attachment; filename={$filename}");
		echo $pdf;
		exit();
	}

	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @return array
	 */
	public function getViewHelperArguments() {
		$raw = $this->getArguments();
		return $raw;
	}

	private function grabPDF($source) {
		// place the source code as a temporary file, then request it through HTTP
		// source contains the computed source as viewed by the browser; the
		// most realistic representation possible. When used with the corresponding
		// widget, this allows injection of a stylesheet into the computed source,
		// allowing for style overrides when PDF-"printing".
		// The temp file is necessary because WebKit supports JS and even AJAX -
		// and AJAX requires a security context, meaning an HTTP request.
		$source = stripslashes($source);
		$tmp = tempnam(PATH_site . 'typo3temp/', 'wspdfhtml') . ".html";
		file_put_contents($tmp, $source);
		$cmd = $this->buildCommand('http://' . $_SERVER['HTTP_HOST'] . str_replace(PATH_site, '/', $tmp));
		$output = shell_exec($cmd);
		unlink($tmp);
		return $output;
	}

	/**
	 * Generate the command to run.
	 * @param $url string: the URL to open.
	 * @param $outputFile string: path and filename for output file.
	 * @return string Command string
	 */
	public function buildCommand($url) {
		if ($this->wkhtmltopdf !== 'wkhtmltopdf') {
			$cmd = $this->wkhtmltopdf;
		} else {
			$cmd = "wkhtmltopdf";
		}
		if (strlen($this->stylesheet) > 0) {
			$path = PATH_site . '/';
			$cmd .= " --user-style-sheet \"{$path}{$this->stylesheet}\"";
		}
		$cmd .= " \"{$url}\"";
		$cmd .= " - ";
		return $cmd;
	}

	/**
	 * All possible arguments for the PDF generator. Used by shell script and
	 * ViewHelper.
	 *
	 * @var array
	 */
	protected $arguments = array(
		// GLOBAL options
		'collate' => array('boolean', 'Collate when printing multiple copies (default)', FALSE, TRUE),
		'nocollate' => array('boolean', 'Do not collate when printing multiple copies', FALSE, FALSE),
		'cookieJar' => array('string', 'Read and write cookies from and to the supplied cookie jar file', FALSE, FALSE),
		'copies' => array('number', 'Number of copies to print into the pdf file (default 1)', FALSE, 1),
		'dpi' => array('boolean', 'Change the dpi explicitly (this has no effect on X11 based systems)', FALSE, FALSE),
		'grayscale' => array('boolea', 'PDF will be generated in grayscale', FALSE, FALSE),
		'imageDpi' => array('int', 'When embedding images scale them down to this dpi (default 600)', FALSE, 600),
		'imageQuality' => array('int', 'When jpeg compressing images use this quality (default 94)', FALSE, 94),
		'lowQuality' => array('boolean', 'Generates lower quality pdf/ps. Useful to shrink the result document space', FALSE, FALSE),
		'marginBottom' => array('string', 'Set the page bottom margin (default 10mm)', FALSE, '10mm'),
		'marginLeft' => array('string', 'Set the page left margin (default 10mm)', FALSE, '10mm'),
		'marginRight' => array('string', 'Set the page right margin (default 10mm)', FALSE, '10mm'),
		'marginTop' => array('string', 'Set the page top margin (default 10mm)', FALSE, '10mm'),
		'orientation' => array('string', 'Set orientation to Landscape or Portrait (default Portrait)', FALSE, 'Portrait'),
		'ouputFormat' => array('string', 'Specify an output format to use pdf or ps, instead of looking at the extention of the output filename', FALSE),
		'pageHeight' => array('string', 'Page height, real units', FALSE, FALSE),
		'pageWidth' => array('string', 'Page width, real units', FALSE, FALSE),
		'pageSize' => array('string', 'Set paper size to: A4, Letter, etc. (default A4)', FALSE, 'A4'),
		'noPdfCompression' => array('boolean', 'Do not use lossless compression on pdf objects', FALSE, FALSE),
		'title' => array('string', 'The title of the generated pdf file (The title of the first document is used if not specified)', FALSE, FALSE),
		'useXServer' => array('boolean', 'Use the X server (some plugins and other stuff might not work without X11)', FALSE, FALSE),

		// OUTLINE options
		'dumpOutline' => array('string', 'Dump the outline to a file', FALSE, FALSE),
		'outline' => array('boolean', 'Put an outline into the pdf (default)', FALSE, TRUE),
		'noOutline' => array('boolean', 'Do not put an outline into the pdf', FALSE, FALSE),
		'outlineDepth' => array('int', 'Set the depth of the outline (default 4)', FALSE),

		// PAGE options
		'allow' => array('array', 'Allow the file or files from the specified folder to be loaded'),
		'background' => array('boolean', 'Do print the background (default)', FALSE, TRUE),
		'noBackground' => array('boolean', 'Do not print background', FALSE, FALSE),
		'checkboxCheckedSvg' => array('string', 'Use this SVG file when rendering checked checkboxes', FALSE, FALSE),
		'checkboxSvg' => array('string', 'Use this SVG file when rendering unchecked checkboxes', FALSE, FALSE),
		'cookies' => array('array', 'Associative array of cookie->value', FALSE, FALSE),
		'customHeaders' => array('array', 'Associative array of headerName->content', FALSE, FALSE),
		'debugJavascript' => array('boolean', 'Show javascript debugging output', FALSE, FALSE),
		'noDebugJavascript' => array('boolean', 'Do not show javascript debugging output (default)', FALSE, TRUE),
		'defaultHeader' => array('booleab', 'Add a default header, with the name of the page to the left, and the page number to the right', FALSE, FALSE),
		'encoding' => array('string', 'Set the default text encoding, for input', FALSE, FALSE),
		'disableExternalLinks' => array('boolean', 'Do not make links to remote web pages', FALSE, FALSE),
		'enableExternalLinks' => array('boolean', 'Make links to remote web pages (default)', FALSE, TRUE),
		'disableForms' => array('boolean', 'Do not turn HTML form fields into pdf form fields (default)', FALSE, TRUE),
		'enableForms' => array('boolean', 'Turn HTML form fields into pdf form fields', FALSE, FALSE),
		'images' => array('boolean', 'Do load or print images (default)', FALSE, TRUE),
		'noImages' => array('boolean', 'Do not load or print images', FALSE, FALSE),
		'disableInternalLinks' => array('boolean', 'Do not make local links', FALSE, TRUE),
		'enableInternalLinks' => array('boolean', 'Make local links (default)', FALSE, FALSE),
		'disableJavascript' => array('boolean', 'Do not allow web pages to run javascript', FALSE, FALSE),
		'enableJavascript' => array('boolean', 'Do allow web pages to run javascript (default)', FALSE, TRUE),
		'javascriptDelay' => array('int', 'Wait some milliseconds for javascript finish (default 200)', FALSE, 200),
		'loadErrorHandling' => array('string', 'Specify how to handle pages that fail to load: abort, ignore or skip (default abort)', FALSE, 'abort'),
		'disableLocalFileAccess' => array('boolean', 'Do not allowed conversion of a local file to read in other local files, unless explecitily allowed with "allow"', FALSE, FALSE),
		'enableLocalFileAccess' => array('boolean', 'Allowed conversion of a local file to read in other local files. (default)', FALSE, TRUE),
		'minimumFontSize' => array('int', 'Minimum font size', FALSE, FALSE),
		'excludeFromOutline' => array('boolean', 'Do not include the page in the table of contents and outlines', FALSE, FALSE),
		'includeInOutline' => array('boolean', 'Include the page in the table of contents and outlines (default)', FALSE, TRUE),
		'pageOffset' => array('int', 'Set the starting page number (default 0)', FALSE, 0),
		'password' => array('string', 'HTTP Authentication password', FALSE, FALSE),
		'disablePlugins' => array('boolean', 'Disable installed plugins (default)', FALSE, TRUE),
		'enablePlugins' => array('boolean', 'Enable installed plugins (plugins will likely not work)', FALSE, FALSE),
		'post' => array('array', 'Adds additional post fields', FALSE, FALSE),
		'postFiles' => array('array', 'Post an additional files', FALSE, FALSE),
		'printMediaType' => array('boolean', 'Use print media-type instead of screen', FALSE, FALSE),
		'noPrintMediaType' => array('boolean', 'Do not use print media-type instead of screen (default)', FALSE, TRUE),
		'proxy' => array('string', 'Use a proxy', FALSE, FALSE),
		'radiobuttonCheckedSvg' => array('string', 'Use this SVG file when rendering checked radiobuttons', FALSE, FALSE),
		'radiobuttonSvg' => array('string', 'Use this SVG file when rendering unchecked radiobuttons', FALSE, FALSE),
		'runScript' => array('string', 'Run this additional javascript after the page is done loading', FALSE, FALSE),
		'disableSmartyShrinking' => array('booelan', 'Disable the intelligent shrinking strategyused by WebKit that makes the pixel/dpi ratio none constant', FALSE, FALSE),
		'enableSmartShrinking' => array('booelan', 'Enable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio none constant (default)', FALSE, TRUE),
		'stopSlowScripts' => array('booelan', 'Stop slow running javascripts (default)', FALSE, TRUE),
		'noStopSlowScripts' => array('booelan', 'Do not Stop slow running javascripts (default)', FALSE, TRUE),
		'disableTocBackLinks' => array('boolean', 'Do not link from section header to toc (default)', FALSE, TRUE),
		'enableTocBackLinks' => array('boolean', 'Link from section header to toc', FALSE, FALSE),
		'userStyleSheet' => array('string', 'Specify a user style sheet, to load with every page', FALSE, FALSE),
		'username' => array('string', 'HTTP Authentication username', FALSE, FALSE),
		'windowStatus' => array('int', 'Wait until window.status is equal to this string before rendering page', FALSE, FALSE),
		'zoom' => array('float', 'Use this zoom factor (default 1)', FALSE, 1),

		// HEADER AND FOOTER options
		'footerCenter' => array('string', 'Centered footer text', FALSE, FALSE),
		'footerFontName' => array('string', 'Set footer font name (default Arial)', FALSE, 'Arial'),
		'footerFontSize' => array('int', 'Set footer font size (default 12)', FALSE, 12),
		'footerHtml' => array('string', 'Adds a html footer (file/url)', FALSE, FALSE),
		'footerLeft' => array('string', 'Left aligned footer text', FALSE, FALSE),
		'footerLine' => array('string', 'Display line above the footer', FALSE, FALSE),
		'noFooterLine' => array('boolean', 'Do not display line above the footer (default)', FALSE, TRUE),
		'footerRight' => array('string', 'Right aligned footer text', FALSE, FALSE),
		'footerSpacing' => array('string', 'Spacing between footer, real units', FALSE, '0mm'),
		'headerCenter' => array('string', 'Centered header text', FALSE, FALSE),
		'headerFontName' => array('string', 'Set header font name (default Arial)', FALSE, 'Arial'),
		'headerFontSize' => array('int', 'Set header font size (default 12)', FALSE, 12),
		'headerHtml' => array('string', 'Adds a html header (file/url)', FALSE, FALSE),
		'headerLeft' => array('string', 'Left aligned header text', FALSE, FALSE),
		'headerLine' => array('string', 'Display line below the header', FALSE, FALSE),
		'noHeaderLine' => array('boolea', 'Do not display line below the header (default)', FALSE, TRUE),
		'headerRight' => array('string', 'Right aligned header text', FALSE, FALSE),
		'hedaerSpacing' => array('string', 'Spacing between header and content in real units', FALSE, '0mm'),
		'replace' => array('array', 'Array of key=>value replacements for header and footer', FALSE, FALSE),

		// TOC options
		'disableDottedLine' => array('boolean', 'Do not use dottet lines in the toc', FALSE, FALSE),
		'tocHeaderText' => array('string', 'The header text of the toc (default Table of Content)', FALSE, 'Table of Content)'),
		'tocLevelIndentation' => array('string', 'For each level of headings in the toc indent by this length (default 1em)', FALSE, '1em'),
		'disableTocLinks' => array('boolean', 'Do not link from toc to sections', FALSE, FALSE),
		'tocTextSizeShrink' => array('float', 'For each level of headings in the toc the font is scaled by this facter (default 0.8)', FALSE, 0.8),
		'xslStyleSheet' => array('string', 'Use the supplied xsl style sheet for printing the table of content', FALSE, FALSE),
	);

}


?>