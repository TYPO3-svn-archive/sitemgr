<?php
/*******************************************************************************
 * register handler for Ext.Direct
 */ 
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.ks_sitemgr.tabs'] =
 	'EXT:ks_sitemgr/lib/class.tx_ks_sitemgr_direct.php:tx_ks_sitemgr_direct';
 	#$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] =
 	#t3lib_extMgm::extPath('ks_sitemgr', 'backend_ext.php');

/*******************************************************************************
 * Register shipped modules
 */ 
	
	//load customer module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_customer'] =
	'EXT:ks_sitemgr/tabs/customer/class.tx_ks_sitemgr_tab_customer.php:tx_ks_sitemgr_tab_customer';
	#$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.ks_sitemgr.customer'] =
 	#'EXT:ks_sitemgr/tabs/customer/class.tx_ks_sitemgr_tab_customer.php:tx_ks_sitemgr_tab_customer';
 	
	//load user module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_beuser'] =
	'EXT:ks_sitemgr/tabs/beuser/class.tx_ks_sitemgr_tab_beuser.php:tx_ks_sitemgr_tab_beuser';
	
	//load userrights module
	//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_beuserrights'] =
	//'EXT:ks_sitemgr/tabs/beuserrights/class.tx_ks_sitemgr_tab_beuser_rights.php:tx_ks_sitemgr_tab_beuser_rights';
	
	if(t3lib_extMgm::isLoaded('templavoila')) {
		//load template module if templavoila is active
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_template'] =
		'EXT:ks_sitemgr/tabs/template/class.tx_ks_sitemgr_tab_template.php:tx_ks_sitemgr_tab_template';
	}
	
	//load statistics module
	if(t3lib_extmgm::isLoaded('piwikintegration',0) && false) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_piwikintegration'] =
		'EXT:ks_sitemgr/tabs/piwikintegration/class.tx_ks_sitemgr_tab_piwikintegration.php:tx_ks_sitemgr_tab_piwikintegration';
	}
	//load help module
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ks_sitemgr']['hook']['ks_sitemgr_help'] =
	'EXT:ks_sitemgr/tabs/help/class.tx_ks_sitemgr_tab_help.php:tx_ks_sitemgr_tab_help';

/*******************************************************************************
 * load fe hooks
 */ 
	if(TYPO3_MODE=='FE') {
		require_once(t3lib_extMgm::extPath('ks_sitemgr').'lib/class.tx_ks_sitemgr_userstylefe.php');
		if($_EXTCONF['enableIndependentMode']) {
			$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'tx_ks_sitemgr_userstylefe->contentPostProc_output'; 
		}
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'tx_ks_sitemgr_userstylefe->contentPostProc_all'; 
	}