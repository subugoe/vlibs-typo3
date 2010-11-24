<?php

########################################################################
# Extension Manager/Repository config file for ext "sqlbuddyadmin".
#
# Auto generated 23-11-2010 15:51
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'SQL Buddy',
	'description' => 'A SQL Buddy integration for TYPO3 as a alternative to phpmyadmin',
	'category' => 'module',
	'shy' => 0,
	'version' => '0.0.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Tim Lochmueller',
	'author_email' => 'webmaster@fruit-lab.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:123:{s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"3354";s:9:"index.php";s:4:"9134";s:12:"mod1/LICENSE";s:4:"2a1c";s:11:"mod1/README";s:4:"b826";s:24:"mod1/ajaxcreatetable.php";s:4:"efec";s:21:"mod1/ajaxfulltext.php";s:4:"e468";s:23:"mod1/ajaximportfile.php";s:4:"dc98";s:18:"mod1/ajaxquery.php";s:4:"be39";s:27:"mod1/ajaxsavecolumnedit.php";s:4:"5dfc";s:21:"mod1/ajaxsaveedit.php";s:4:"6415";s:25:"mod1/ajaxsaveuseredit.php";s:4:"cee4";s:15:"mod1/browse.php";s:4:"4553";s:13:"mod1/conf.php";s:4:"8b25";s:15:"mod1/config.php";s:4:"117b";s:19:"mod1/dboverview.php";s:4:"9c09";s:13:"mod1/edit.php";s:4:"8711";s:19:"mod1/editcolumn.php";s:4:"3734";s:17:"mod1/edituser.php";s:4:"3872";s:15:"mod1/export.php";s:4:"b07d";s:18:"mod1/functions.php";s:4:"be9d";s:13:"mod1/home.php";s:4:"60bb";s:15:"mod1/import.php";s:4:"2d43";s:14:"mod1/index.php";s:4:"fcd3";s:15:"mod1/insert.php";s:4:"29ea";s:18:"mod1/locallang.xml";s:4:"0b29";s:22:"mod1/locallang_mod.xml";s:4:"f6fc";s:14:"mod1/login.php";s:4:"cba4";s:15:"mod1/logout.php";s:4:"0f0e";s:19:"mod1/moduleicon.gif";s:4:"8074";s:14:"mod1/query.php";s:4:"36a3";s:14:"mod1/serve.php";s:4:"49e1";s:18:"mod1/structure.php";s:4:"3863";s:14:"mod1/users.php";s:4:"6c9a";s:19:"mod1/css/common.css";s:4:"b4a6";s:23:"mod1/css/navigation.css";s:4:"e6af";s:18:"mod1/css/print.css";s:4:"059b";s:22:"mod1/images/button.png";s:4:"e4f1";s:21:"mod1/images/close.png";s:4:"6e5c";s:26:"mod1/images/closeHover.png";s:4:"ff40";s:27:"mod1/images/closedArrow.png";s:4:"4e2a";s:20:"mod1/images/goto.png";s:4:"0ce0";s:20:"mod1/images/info.png";s:4:"89a5";s:25:"mod1/images/infoHover.png";s:4:"e517";s:24:"mod1/images/initLoad.png";s:4:"3707";s:23:"mod1/images/loading.gif";s:4:"73e5";s:20:"mod1/images/logo.png";s:4:"66ed";s:25:"mod1/images/openArrow.png";s:4:"e7be";s:28:"mod1/images/schemaHeader.png";s:4:"06f9";s:23:"mod1/images/sortasc.gif";s:4:"309a";s:24:"mod1/images/sortdesc.gif";s:4:"197f";s:27:"mod1/images/transparent.png";s:4:"8c55";s:29:"mod1/images/window-button.png";s:4:"36b2";s:29:"mod1/images/window-center.png";s:4:"5aa2";s:28:"mod1/images/window-close.png";s:4:"616a";s:36:"mod1/images/window-header-center.png";s:4:"198a";s:34:"mod1/images/window-header-left.png";s:4:"546e";s:35:"mod1/images/window-header-right.png";s:4:"f454";s:29:"mod1/images/window-resize.png";s:4:"f7c3";s:41:"mod1/images/window-shadow-bottom-left.png";s:4:"5a49";s:42:"mod1/images/window-shadow-bottom-right.png";s:4:"0f8f";s:36:"mod1/images/window-shadow-bottom.png";s:4:"3428";s:34:"mod1/images/window-shadow-left.png";s:4:"7a3b";s:35:"mod1/images/window-shadow-right.png";s:4:"5dee";s:24:"mod1/includes/browse.php";s:4:"2a7b";s:23:"mod1/includes/types.php";s:4:"a5a7";s:37:"mod1/includes/class/GetTextReader.php";s:4:"21b1";s:32:"mod1/includes/class/Sql-php4.php";s:4:"8731";s:27:"mod1/includes/class/Sql.php";s:4:"c6a1";s:15:"mod1/js/core.js";s:4:"ae83";s:18:"mod1/js/helpers.js";s:4:"ddc8";s:28:"mod1/js/mootools-1.2-core.js";s:4:"3a38";s:19:"mod1/js/movement.js";s:4:"a573";s:21:"mod1/locale/ar_DZ.pot";s:4:"0625";s:21:"mod1/locale/ca_AD.pot";s:4:"6a85";s:21:"mod1/locale/cs_CZ.pot";s:4:"9f4a";s:21:"mod1/locale/da_DK.pot";s:4:"9e8e";s:21:"mod1/locale/de_DE.pot";s:4:"9cd6";s:21:"mod1/locale/en_US.pot";s:4:"e136";s:21:"mod1/locale/eo_EO.pot";s:4:"46fa";s:21:"mod1/locale/es_AR.pot";s:4:"3fda";s:21:"mod1/locale/es_ES.pot";s:4:"925f";s:21:"mod1/locale/fa_IR.pot";s:4:"d3d0";s:21:"mod1/locale/fi_FI.pot";s:4:"93bd";s:21:"mod1/locale/fr_FR.pot";s:4:"72c3";s:21:"mod1/locale/gl_ES.pot";s:4:"071b";s:21:"mod1/locale/he_IL.pot";s:4:"8324";s:21:"mod1/locale/hu_HU.pot";s:4:"e2b1";s:21:"mod1/locale/id_ID.pot";s:4:"4e13";s:21:"mod1/locale/it_IT.pot";s:4:"faa1";s:21:"mod1/locale/ja_JP.pot";s:4:"f555";s:21:"mod1/locale/lo_LA.pot";s:4:"06d3";s:21:"mod1/locale/lv_LV.pot";s:4:"a752";s:21:"mod1/locale/ms_ID.pot";s:4:"4a1e";s:21:"mod1/locale/nl_NL.pot";s:4:"5677";s:21:"mod1/locale/pl_PL.pot";s:4:"283a";s:21:"mod1/locale/pt_BR.pot";s:4:"3af9";s:21:"mod1/locale/pt_PT.pot";s:4:"ebb6";s:21:"mod1/locale/ro_RO.pot";s:4:"0be4";s:21:"mod1/locale/ru_RU.pot";s:4:"1d44";s:21:"mod1/locale/sk_SK.pot";s:4:"38eb";s:21:"mod1/locale/sl_SL.pot";s:4:"7829";s:21:"mod1/locale/sp_RS.pot";s:4:"c2e2";s:21:"mod1/locale/sr_RS.pot";s:4:"6e81";s:21:"mod1/locale/sv_SE.pot";s:4:"9fe3";s:21:"mod1/locale/tl_PH.pot";s:4:"1877";s:21:"mod1/locale/tr_TR.pot";s:4:"639e";s:21:"mod1/locale/uk_UA.pot";s:4:"ebd0";s:21:"mod1/locale/vi_VN.pot";s:4:"fbe2";s:21:"mod1/locale/zh_CN.pot";s:4:"df27";s:21:"mod1/locale/zh_TW.pot";s:4:"2270";s:34:"mod1/themes/bittersweet/css/ie.css";s:4:"deb8";s:36:"mod1/themes/bittersweet/css/main.css";s:4:"37fc";s:41:"mod1/themes/bittersweet/images/header.png";s:4:"ee82";s:48:"mod1/themes/bittersweet/images/initLoad-dark.png";s:4:"ab81";s:30:"mod1/themes/classic/css/ie.css";s:4:"3be0";s:32:"mod1/themes/classic/css/main.css";s:4:"922c";s:40:"mod1/themes/classic/images/corner-bl.png";s:4:"86a9";s:40:"mod1/themes/classic/images/corner-br.png";s:4:"81ea";s:40:"mod1/themes/classic/images/corner-tl.png";s:4:"e753";s:40:"mod1/themes/classic/images/corner-tr.png";s:4:"9477";s:37:"mod1/themes/classic/images/header.png";s:4:"e553";s:38:"mod1/themes/classic/images/shading.png";s:4:"0fb3";}',
);

?>