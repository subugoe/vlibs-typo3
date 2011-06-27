/**
 * SyntaxHighlighter
 * http://alexgorbatchev.com/SyntaxHighlighter
 *
 * SyntaxHighlighter is donationware. If you are using it, please donate.
 * http://alexgorbatchev.com/SyntaxHighlighter/donate.html
 *
 * @version
 * 3.0.83 (July 02 2010)
 *
 * @copyright
 * Copyright (C) 2004-2010 Alex Gorbatchev.
 *
 * @license
 * Dual licensed under the MIT and GPL licenses.
 */


/***************************************************************
*  TYPO3 TypoScript Brush for SyntaxHighlighter
*
*  (c) 2011 Nicole Cordes <cordes@cps-it.de>
*  http://www.cps-it.de
***************************************************************/

;(function()
{
	// CommonJS
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

	function Brush()
	{
		var funcs	=	'addheight addwidth admpanel allwrap atagparams ' +
						'bodytag ' +
						'cobject case code collapse content content_from_pid_allowoutsidedomain crop ' +
						'defaulttemplateobjectmain defaulttemplateobjectsub disableprefixcomment displayactiveonload donthideonmouseup ' +
						'entrylevel expall exttarget ' +
						'field file fontcolor fontfile fontsize freezemouseover ' +
						'gmenu_layers ' +
						'headercomment headerdata hidemenuwhennotover ' +
						'if image imgparams index_enable index_externals inheritmaintemplates inheritsubtemplates inlinestyle2tempfile ' +
						'inttarget isfalse istrue ' +
						'layerstyle linkwrap lockposition lockposition_addself ' +
						'meta mode ' +
						'nicetext noblur ' +
						'offset ' +
						'placement ' +
						'range relativetoparentlayer relativetotriggeritem removedefaultjs required ' +
						'shortcuticon simulatestaticdocuments_notypeifnotitle spamprotectemailaddresses ' +
						'spamprotectemailaddresses_atsubst stdwrap stylesheet ' +
						'target templateobjects templatetype text textmaxlength tmenu_layers topoffset transparentbackground ' +
						'typenum ' +
						'upper user_* userfunc ' +
						'value ' +
						'workonsubpart wrap ' +
						'ypmenu';

		var keywords	=	'act actifsub ' +
						'case coa coa_int cleargif columns content ctable cur ' +
						'db ' +
						'editpanel ' +
						'file form ' +
						'gmenu gmenu_layers ' +
						'hmenu hruler html ' +
						'ifsub image img_resource imgtext ' +
						'load_register ' +
						'multimedia ' +
						'no ' +
						'otable ' +
						'page php_script php_script_int php_script_ext ' +
						'records restore_register ro ' +
						'searchresult ' +
						'template text tmenu tmenu_layers ' +
						'user user_int ' +
						'xy';

		var constants	=	'_gifbuilder ' +
						'config constants ' +
						'fedata frame frameset ' +
						'includeLibs ' +
						'lib ' +
						'page plugin ' +
						'resources ' +
						'sitetitle styles ' +
						'temp tt_content types';

		this.regexList = [
			{ regex: SyntaxHighlighter.regexLib.singleLineCComments,				css: 'comments' },		// one line comments
			{ regex: SyntaxHighlighter.regexLib.multiLineCComments,					css: 'comments' },		// multiline comments
			{ regex: /\#.*/gm ,																							css: 'comments' },		// # comments
			{ regex: /\.\d+|\d\.|\d =|\d\s+\{/g,														css: 'variable' },		// variables
			{ regex: new RegExp(this.getKeywords(funcs), 'gmi') ,						css: 'functions' },		// functions
			{ regex: new RegExp(this.getKeywords(keywords), 'gmi'),					css: 'keyword' },			// keyword
			{ regex: new RegExp(this.getKeywords(constants), 'gmi'),				css: 'constants' },		// constants
			];

		this.forHtmlScript(SyntaxHighlighter.regexLib.aspScriptTags);
	}

	Brush.prototype	= new SyntaxHighlighter.Highlighter();
	Brush.aliases	= ['ts','typoscript'];

	SyntaxHighlighter.brushes.Typoscript = Brush;

	// CommonJS
	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
