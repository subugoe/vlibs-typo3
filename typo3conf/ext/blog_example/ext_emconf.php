<?php

########################################################################
# Extension Manager/Repository config file for ext "blog_example".
#
# Auto generated 04-01-2011 10:25
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'A Blog Example for the Extbase Framework',
	'description' => 'An example extension demonstrating the features of the Extbase Framework. It is the back-ported and tweaked Blog Example package of FLOW3. Have fun playing with it!',
	'category' => 'example',
	'shy' => 0,
	'version' => '1.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'TYPO3 core team',
	'author_email' => '',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.4.0-4.4.99',
			'extbase' => '1.2.0-0.0.0',
			'fluid' => '1.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:55:{s:16:"ext_autoload.php";s:4:"91f9";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"ff01";s:14:"ext_tables.php";s:4:"c2bf";s:14:"ext_tables.sql";s:4:"33b2";s:37:"Classes/Controller/BlogController.php";s:4:"4e78";s:40:"Classes/Controller/CommentController.php";s:4:"acde";s:37:"Classes/Controller/PostController.php";s:4:"d533";s:38:"Classes/Domain/Model/Administrator.php";s:4:"7052";s:29:"Classes/Domain/Model/Blog.php";s:4:"c877";s:32:"Classes/Domain/Model/Comment.php";s:4:"4886";s:31:"Classes/Domain/Model/Person.php";s:4:"df1c";s:29:"Classes/Domain/Model/Post.php";s:4:"7cdb";s:28:"Classes/Domain/Model/Tag.php";s:4:"5e00";s:53:"Classes/Domain/Repository/AdministratorRepository.php";s:4:"e274";s:44:"Classes/Domain/Repository/BlogRepository.php";s:4:"05af";s:46:"Classes/Domain/Repository/PersonRepository.php";s:4:"a93d";s:44:"Classes/Domain/Repository/PostRepository.php";s:4:"3446";s:42:"Classes/Domain/Validator/BlogValidator.php";s:4:"d0e6";s:42:"Classes/ViewHelpers/GravatarViewHelper.php";s:4:"d27d";s:41:"Configuration/FlexForms/flexform_list.xml";s:4:"b00b";s:25:"Configuration/TCA/tca.php";s:4:"a89c";s:38:"Configuration/TypoScript/constants.txt";s:4:"b865";s:34:"Configuration/TypoScript/setup.txt";s:4:"eb1a";s:46:"Resources/Private/Backend/Layouts/default.html";s:4:"652d";s:50:"Resources/Private/Backend/Templates/Blog/edit.html";s:4:"a574";s:51:"Resources/Private/Backend/Templates/Blog/index.html";s:4:"7863";s:49:"Resources/Private/Backend/Templates/Blog/new.html";s:4:"7e67";s:50:"Resources/Private/Backend/Templates/Post/edit.html";s:4:"ef27";s:51:"Resources/Private/Backend/Templates/Post/index.html";s:4:"869e";s:50:"Resources/Private/Backend/Templates/Post/index.txt";s:4:"ac46";s:49:"Resources/Private/Backend/Templates/Post/new.html";s:4:"fdfc";s:50:"Resources/Private/Backend/Templates/Post/show.html";s:4:"3763";s:40:"Resources/Private/Language/locallang.xml";s:4:"43a2";s:44:"Resources/Private/Language/locallang_csh.xml";s:4:"5e95";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"e603";s:44:"Resources/Private/Language/locallang_mod.xml";s:4:"cd5d";s:38:"Resources/Private/Layouts/default.html";s:4:"b132";s:42:"Resources/Private/Partials/formErrors.html";s:4:"cc71";s:44:"Resources/Private/Partials/postMetaData.html";s:4:"577f";s:40:"Resources/Private/Partials/postTags.html";s:4:"872c";s:42:"Resources/Private/Templates/Blog/edit.html";s:4:"c22e";s:43:"Resources/Private/Templates/Blog/index.html";s:4:"62b6";s:41:"Resources/Private/Templates/Blog/new.html";s:4:"c006";s:42:"Resources/Private/Templates/Post/edit.html";s:4:"41b0";s:43:"Resources/Private/Templates/Post/index.html";s:4:"2f1e";s:42:"Resources/Private/Templates/Post/index.txt";s:4:"391c";s:41:"Resources/Private/Templates/Post/new.html";s:4:"54ad";s:42:"Resources/Private/Templates/Post/show.html";s:4:"36f1";s:40:"Resources/Public/Icons/icon_relation.gif";s:4:"9e5c";s:64:"Resources/Public/Icons/icon_tx_blogexample_domain_model_blog.gif";s:4:"50a3";s:67:"Resources/Public/Icons/icon_tx_blogexample_domain_model_comment.gif";s:4:"50a3";s:66:"Resources/Public/Icons/icon_tx_blogexample_domain_model_person.gif";s:4:"50a3";s:64:"Resources/Public/Icons/icon_tx_blogexample_domain_model_post.gif";s:4:"50a3";s:63:"Resources/Public/Icons/icon_tx_blogexample_domain_model_tag.gif";s:4:"50a3";}',
);

?>