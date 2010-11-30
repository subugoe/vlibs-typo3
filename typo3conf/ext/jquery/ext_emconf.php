<?php

########################################################################
# Extension Manager/Repository config file for ext "jquery".
#
# Auto generated 26-11-2010 17:13
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'jQuery',
	'description' => 'The Write Less, Do More, JavaScript Library',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.2.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Joerg Schoppet',
	'author_email' => 'joerg@schoppet.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
			'cms' => '',
			'jsmanager' => '1.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:157:{s:19:"class.tx_jquery.php";s:4:"3124";s:12:"ext_icon.gif";s:4:"00ff";s:17:"ext_localconf.php";s:4:"5ce7";s:24:"ext_typoscript_setup.txt";s:4:"27b9";s:25:"doc/example_jsmanager.txt";s:4:"7fa7";s:14:"doc/manual.sxw";s:4:"2444";s:20:"plugins/accordion.js";s:4:"eebe";s:19:"plugins/carousel.js";s:4:"f4a1";s:17:"plugins/easing.js";s:4:"9a53";s:18:"plugins/fisheye.js";s:4:"a7eb";s:25:"plugins/iautocompleter.js";s:4:"ed9c";s:24:"plugins/iautoscroller.js";s:4:"0b66";s:16:"plugins/idrag.js";s:4:"44a3";s:16:"plugins/idrop.js";s:4:"cef0";s:20:"plugins/iexpander.js";s:4:"ee54";s:14:"plugins/ifx.js";s:4:"26e6";s:19:"plugins/ifxblind.js";s:4:"3077";s:20:"plugins/ifxbounce.js";s:4:"726c";s:18:"plugins/ifxdrop.js";s:4:"b15d";s:18:"plugins/ifxfold.js";s:4:"92b8";s:23:"plugins/ifxhighlight.js";s:4:"de91";s:23:"plugins/ifxopenclose.js";s:4:"2d2d";s:21:"plugins/ifxpulsate.js";s:4:"e46c";s:19:"plugins/ifxscale.js";s:4:"e605";s:22:"plugins/ifxscrollto.js";s:4:"8e55";s:19:"plugins/ifxshake.js";s:4:"d75a";s:19:"plugins/ifxslide.js";s:4:"73f5";s:22:"plugins/ifxtransfer.js";s:4:"8155";s:19:"plugins/imagebox.js";s:4:"d123";s:20:"plugins/interface.js";s:4:"8474";s:21:"plugins/iresizable.js";s:4:"f330";s:18:"plugins/iselect.js";s:4:"e234";s:18:"plugins/islider.js";s:4:"5f32";s:21:"plugins/islideshow.js";s:4:"08df";s:21:"plugins/isortables.js";s:4:"4cfa";s:19:"plugins/itooltip.js";s:4:"1264";s:17:"plugins/ittabs.js";s:4:"f8ee";s:16:"plugins/iutil.js";s:4:"ea5d";s:23:"plugins/jquery.color.js";s:4:"8738";s:28:"plugins/jquery.compat-1.1.js";s:4:"ff11";s:24:"plugins/jquery.cookie.js";s:4:"8300";s:25:"plugins/jquery.history.js";s:4:"b195";s:23:"plugins/jquery.xpath.js";s:4:"f910";s:13:"src/jquery.js";s:4:"b9f3";s:33:"uncompressed_plugins/accordion.js";s:4:"990b";s:32:"uncompressed_plugins/carousel.js";s:4:"40ce";s:30:"uncompressed_plugins/easing.js";s:4:"8089";s:31:"uncompressed_plugins/fisheye.js";s:4:"a0ed";s:38:"uncompressed_plugins/iautocompleter.js";s:4:"2023";s:37:"uncompressed_plugins/iautoscroller.js";s:4:"155d";s:29:"uncompressed_plugins/idrag.js";s:4:"1989";s:29:"uncompressed_plugins/idrop.js";s:4:"0f80";s:33:"uncompressed_plugins/iexpander.js";s:4:"67d3";s:27:"uncompressed_plugins/ifx.js";s:4:"8114";s:32:"uncompressed_plugins/ifxblind.js";s:4:"9071";s:33:"uncompressed_plugins/ifxbounce.js";s:4:"dd25";s:31:"uncompressed_plugins/ifxdrop.js";s:4:"6926";s:31:"uncompressed_plugins/ifxfold.js";s:4:"bfc5";s:36:"uncompressed_plugins/ifxhighlight.js";s:4:"dd72";s:36:"uncompressed_plugins/ifxopenclose.js";s:4:"bd97";s:34:"uncompressed_plugins/ifxpulsate.js";s:4:"3b83";s:32:"uncompressed_plugins/ifxscale.js";s:4:"b23e";s:35:"uncompressed_plugins/ifxscrollto.js";s:4:"956d";s:32:"uncompressed_plugins/ifxshake.js";s:4:"cc2b";s:32:"uncompressed_plugins/ifxslide.js";s:4:"16f1";s:35:"uncompressed_plugins/ifxtransfer.js";s:4:"99ec";s:32:"uncompressed_plugins/imagebox.js";s:4:"e6d3";s:33:"uncompressed_plugins/interface.js";s:4:"8474";s:34:"uncompressed_plugins/iresizable.js";s:4:"989f";s:31:"uncompressed_plugins/iselect.js";s:4:"7661";s:31:"uncompressed_plugins/islider.js";s:4:"6de3";s:34:"uncompressed_plugins/islideshow.js";s:4:"8996";s:34:"uncompressed_plugins/isortables.js";s:4:"ebe6";s:32:"uncompressed_plugins/itooltip.js";s:4:"17dc";s:30:"uncompressed_plugins/ittabs.js";s:4:"2060";s:29:"uncompressed_plugins/iutil.js";s:4:"7af0";s:36:"uncompressed_plugins/jquery.color.js";s:4:"8738";s:41:"uncompressed_plugins/jquery.compat-1.1.js";s:4:"ff11";s:37:"uncompressed_plugins/jquery.cookie.js";s:4:"8300";s:38:"uncompressed_plugins/jquery.history.js";s:4:"b195";s:36:"uncompressed_plugins/jquery.xpath.js";s:4:"f910";s:26:"uncompressed_src/jquery.js";s:4:"47f2";s:45:"versions/1.1.4/plugins/minimized/accordion.js";s:4:"eebe";s:44:"versions/1.1.4/plugins/minimized/carousel.js";s:4:"f4a1";s:42:"versions/1.1.4/plugins/minimized/easing.js";s:4:"9a53";s:43:"versions/1.1.4/plugins/minimized/fisheye.js";s:4:"a7eb";s:50:"versions/1.1.4/plugins/minimized/iautocompleter.js";s:4:"ed9c";s:49:"versions/1.1.4/plugins/minimized/iautoscroller.js";s:4:"0b66";s:41:"versions/1.1.4/plugins/minimized/idrag.js";s:4:"44a3";s:41:"versions/1.1.4/plugins/minimized/idrop.js";s:4:"cef0";s:45:"versions/1.1.4/plugins/minimized/iexpander.js";s:4:"ee54";s:39:"versions/1.1.4/plugins/minimized/ifx.js";s:4:"26e6";s:44:"versions/1.1.4/plugins/minimized/ifxblind.js";s:4:"3077";s:45:"versions/1.1.4/plugins/minimized/ifxbounce.js";s:4:"726c";s:43:"versions/1.1.4/plugins/minimized/ifxdrop.js";s:4:"b15d";s:43:"versions/1.1.4/plugins/minimized/ifxfold.js";s:4:"92b8";s:48:"versions/1.1.4/plugins/minimized/ifxhighlight.js";s:4:"de91";s:48:"versions/1.1.4/plugins/minimized/ifxopenclose.js";s:4:"2d2d";s:46:"versions/1.1.4/plugins/minimized/ifxpulsate.js";s:4:"e46c";s:44:"versions/1.1.4/plugins/minimized/ifxscale.js";s:4:"e605";s:47:"versions/1.1.4/plugins/minimized/ifxscrollto.js";s:4:"8e55";s:44:"versions/1.1.4/plugins/minimized/ifxshake.js";s:4:"d75a";s:44:"versions/1.1.4/plugins/minimized/ifxslide.js";s:4:"73f5";s:47:"versions/1.1.4/plugins/minimized/ifxtransfer.js";s:4:"8155";s:44:"versions/1.1.4/plugins/minimized/imagebox.js";s:4:"d123";s:46:"versions/1.1.4/plugins/minimized/iresizable.js";s:4:"f330";s:43:"versions/1.1.4/plugins/minimized/iselect.js";s:4:"e234";s:43:"versions/1.1.4/plugins/minimized/islider.js";s:4:"5f32";s:46:"versions/1.1.4/plugins/minimized/islideshow.js";s:4:"08df";s:46:"versions/1.1.4/plugins/minimized/isortables.js";s:4:"4cfa";s:44:"versions/1.1.4/plugins/minimized/itooltip.js";s:4:"1264";s:42:"versions/1.1.4/plugins/minimized/ittabs.js";s:4:"f8ee";s:41:"versions/1.1.4/plugins/minimized/iutil.js";s:4:"ea5d";s:42:"versions/1.1.4/plugins/normal/accordion.js";s:4:"990b";s:41:"versions/1.1.4/plugins/normal/carousel.js";s:4:"40ce";s:39:"versions/1.1.4/plugins/normal/easing.js";s:4:"8089";s:40:"versions/1.1.4/plugins/normal/fisheye.js";s:4:"a0ed";s:47:"versions/1.1.4/plugins/normal/iautocompleter.js";s:4:"2023";s:46:"versions/1.1.4/plugins/normal/iautoscroller.js";s:4:"155d";s:38:"versions/1.1.4/plugins/normal/idrag.js";s:4:"1989";s:38:"versions/1.1.4/plugins/normal/idrop.js";s:4:"0f80";s:42:"versions/1.1.4/plugins/normal/iexpander.js";s:4:"67d3";s:36:"versions/1.1.4/plugins/normal/ifx.js";s:4:"8114";s:41:"versions/1.1.4/plugins/normal/ifxblind.js";s:4:"9071";s:42:"versions/1.1.4/plugins/normal/ifxbounce.js";s:4:"dd25";s:40:"versions/1.1.4/plugins/normal/ifxdrop.js";s:4:"6926";s:40:"versions/1.1.4/plugins/normal/ifxfold.js";s:4:"bfc5";s:45:"versions/1.1.4/plugins/normal/ifxhighlight.js";s:4:"dd72";s:45:"versions/1.1.4/plugins/normal/ifxopenclose.js";s:4:"bd97";s:43:"versions/1.1.4/plugins/normal/ifxpulsate.js";s:4:"3b83";s:41:"versions/1.1.4/plugins/normal/ifxscale.js";s:4:"b23e";s:44:"versions/1.1.4/plugins/normal/ifxscrollto.js";s:4:"956d";s:41:"versions/1.1.4/plugins/normal/ifxshake.js";s:4:"cc2b";s:41:"versions/1.1.4/plugins/normal/ifxslide.js";s:4:"16f1";s:44:"versions/1.1.4/plugins/normal/ifxtransfer.js";s:4:"99ec";s:41:"versions/1.1.4/plugins/normal/imagebox.js";s:4:"e6d3";s:43:"versions/1.1.4/plugins/normal/iresizable.js";s:4:"989f";s:40:"versions/1.1.4/plugins/normal/iselect.js";s:4:"7661";s:40:"versions/1.1.4/plugins/normal/islider.js";s:4:"6de3";s:43:"versions/1.1.4/plugins/normal/islideshow.js";s:4:"8996";s:43:"versions/1.1.4/plugins/normal/isortables.js";s:4:"ebe6";s:41:"versions/1.1.4/plugins/normal/itooltip.js";s:4:"17dc";s:39:"versions/1.1.4/plugins/normal/ittabs.js";s:4:"2060";s:38:"versions/1.1.4/plugins/normal/iutil.js";s:4:"7af0";s:50:"versions/1.1.4/plugins/normal/jquery.compat-1.0.js";s:4:"2767";s:46:"versions/1.1.4/plugins/normal/jquery.cookie.js";s:4:"8300";s:47:"versions/1.1.4/plugins/normal/jquery.history.js";s:4:"b195";s:31:"versions/1.1.4/source/jquery.js";s:4:"45cf";s:36:"versions/1.1.4/source/jquery.pack.js";s:4:"34ca";s:50:"versions/1.2.1/plugins/normal/jquery.compat-1.1.js";s:4:"ff11";s:31:"versions/1.2.1/source/jquery.js";s:4:"05d7";s:35:"versions/1.2.1/source/jquery.min.js";s:4:"1fd5";s:36:"versions/1.2.1/source/jquery.pack.js";s:4:"ebe0";s:50:"versions/1.2.2/plugins/normal/jquery.compat-1.1.js";s:4:"ff11";s:31:"versions/1.2.2/source/jquery.js";s:4:"b4fd";s:35:"versions/1.2.2/source/jquery.min.js";s:4:"cf68";s:36:"versions/1.2.2/source/jquery.pack.js";s:4:"c0c3";}',
);

?>