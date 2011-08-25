<?php

########################################################################
# Extension Manager/Repository config file for ext "ks_sitemgr".
#
# Auto generated 01-02-2011 06:13
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Sitemanager and Customer Management',
	'description' => 'Sitemanager and Customer Management made easy. With this extension you can create ´small admins´.',
	'category' => 'module',
	'author' => 'Kay Strobach',
	'author_email' => 'typo3@kay-strobach.de',
	'shy' => '',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.8.1',
	'constraints' => array(
		'depends' => array(
			'be_acl'  => '1.4.1-1.4.2',
			'cms'     => '',
			'extbase' => '',
			'fluid'   => '',
		),
		'conflicts' => array(
			'be_acl' => '1.4.0',
		),
		'suggests' => array(
			'templavoila' => '1.4.0-1.5.99',			
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:58:{s:13:"CHANGELOG.TXT";s:4:"b1f2";s:10:"README.txt";s:4:"ee2d";s:9:"Thumbs.db";s:4:"d6e4";s:21:"ext_conf_template.txt";s:4:"dd23";s:12:"ext_icon.gif";s:4:"4f5b";s:17:"ext_localconf.php";s:4:"8cd9";s:14:"ext_tables.php";s:4:"ae18";s:14:"ext_tables.sql";s:4:"f9b8";s:30:"icon_tx_sitemgr_customer.gif";s:4:"4f5b";s:16:"locallang_db.xml";s:4:"1154";s:7:"tca.php";s:4:"cf6f";s:14:"doc/manual.sxw";s:4:"33e9";s:19:"doc/wizard_form.dat";s:4:"6b36";s:20:"doc/wizard_form.html";s:4:"ecf1";s:36:"lib/class.tx_ks_sitemgr_customer.php";s:4:"3f86";s:34:"lib/class.tx_ks_sitemgr_direct.php";s:4:"413a";s:31:"lib/class.tx_ks_sitemgr_div.php";s:4:"1ab0";s:31:"lib/class.tx_ks_sitemgr_tab.php";s:4:"ed6d";s:39:"lib/class.tx_ks_sitemgr_toolbaritem.php";s:4:"3f02";s:39:"lib/class.tx_ks_sitemgr_userstylefe.php";s:4:"4f4f";s:13:"mod1/conf.php";s:4:"9c9e";s:18:"mod1/ext-icons.css";s:4:"6e9a";s:13:"mod1/extjs.js";s:4:"61c9";s:14:"mod1/index.php";s:4:"d71d";s:18:"mod1/locallang.xml";s:4:"7b5f";s:22:"mod1/locallang_mod.xml";s:4:"6855";s:22:"mod1/mod_template.html";s:4:"e74c";s:19:"mod1/moduleicon.gif";s:4:"dc56";s:31:"mod1/ext-icons/document-new.png";s:4:"b2a5";s:32:"mod1/ext-icons/document-open.png";s:4:"b986";s:38:"mod1/ext-icons/document-save-close.png";s:4:"20e7";s:32:"mod1/ext-icons/document-view.png";s:4:"a16f";s:27:"mod1/ext-icons/edit-add.png";s:4:"c577";s:30:"mod1/ext-icons/edit-delete.png";s:4:"33a3";s:40:"mod1/ext-icons/pagetree-backend-user.png";s:4:"6a47";s:46:"mod1/ext-icons/system-backend-user-emulate.png";s:4:"f7c6";s:35:"mod1/ext-icons/system-help-open.png";s:4:"c6fd";s:35:"mod1/ext-icons/system-list-open.png";s:4:"1817";s:28:"mod1/ext-icons/text-html.png";s:4:"5d86";s:46:"tabs/beuser/class.tx_ks_sitemgr_tab_beuser.php";s:4:"4fa6";s:20:"tabs/beuser/extjs.js";s:4:"ad4c";s:25:"tabs/beuser/locallang.xml";s:4:"de32";s:50:"tabs/customer/class.tx_ks_sitemgr_tab_customer.php";s:4:"637c";s:22:"tabs/customer/extjs.js";s:4:"50d8";s:27:"tabs/customer/locallang.xml";s:4:"7038";s:42:"tabs/help/class.tx_ks_sitemgr_tab_help.php";s:4:"71e6";s:18:"tabs/help/extjs.js";s:4:"99cc";s:23:"tabs/help/locallang.xml";s:4:"a161";s:66:"tabs/piwikintegration/class.tx_ks_sitemgr_tab_piwikintegration.php";s:4:"fa17";s:30:"tabs/piwikintegration/extjs.js";s:4:"4b15";s:35:"tabs/piwikintegration/locallang.xml";s:4:"2a37";s:50:"tabs/template/class.tx_ks_sitemgr_tab_template.php";s:4:"2b21";s:22:"tabs/template/extjs.js";s:4:"8e21";s:27:"tabs/template/locallang.xml";s:4:"b609";s:24:"tabs/template/screen.css";s:4:"eded";s:17:"toolbar/index.php";s:4:"dff9";s:22:"toolbar/ks_sitemgr.css";s:4:"b583";s:21:"toolbar/ks_sitemgr.js";s:4:"e0ae";}',
);

?>