<?php
/*******************************************************************************
 * register handler for Ext.Direct
 */ 
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.sitemgr.tabs'] =
 	'EXT:sitemgr/Classes/ExtDirect/Dispatcher.php:Tx_Sitemgr_ExtDirect_Dispatcher';
 	#$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] =
 	#t3lib_extMgm::extPath('ks_sitemgr', 'backend_ext.php');

/*******************************************************************************
 * Register shipped modules
 */ 
	
	//load customer module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_customer'] =
	'Tx_Sitemgr_Modules_Customer_CustomerController';
 	
	//load user module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_beuser'] =
	'Tx_Sitemgr_Modules_BeUser_BeUserController';
	
	//load userrights module
	//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_beuserrights'] =
	//'EXT:ks_sitemgr/tabs/beuserrights/class.tx_ks_sitemgr_tab_beuser_rights.php:tx_ks_sitemgr_tab_beuser_rights';
	
	if(0 && t3lib_extMgm::isLoaded('templavoila')) {
		//load template module if templavoila is active
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_template'] =
		'Tx_Sitemgr_Modules_Template_TemplateController';
	}
	
	//load statistics module
	if(0 && t3lib_extmgm::isLoaded('piwikintegration',0) && false) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_piwikintegration'] =
		'Tx_Sitemgr_Modules_Piwikintegration_PiwikintegrationController';
	}
	//load help module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_help'] =
	'Tx_Sitemgr_Modules_Help_HelpController';

/*******************************************************************************
 * load fe hooks
 */ 
	if(TYPO3_MODE=='FE') {
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'tx_Sitemgr_Fe_ContentPostProc->contentPostProc_output'; 
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][]    = 'tx_Sitemgr_Fe_ContentPostProc->contentPostProc_all'; 
	}