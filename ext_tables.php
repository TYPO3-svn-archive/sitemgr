<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
/**
 * Module
 */ 
	if (TYPO3_MODE == 'BE') {
		t3lib_extMgm::addModulePath('web_txsitemgrM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');		
		t3lib_extMgm::addModule('web', 'txsitemgrM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}

/**
 * Toolbar item
 */ 
	if (TYPO3_MODE == 'BE') {
		$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_sitemgr::searchCustomer']    
			= t3lib_extMgm::extPath($_EXTKEY).'Classes/ToolbarItems/CustomerSelector/Item.php:Tx_Sitemgr_ToolbarItems_CustomerSelector_Item->searchCustomer';
		$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['tx_sitemgr::openPageOfCustomer']
			= t3lib_extMgm::extPath($_EXTKEY).'Classes/ToolbarItems/CustomerSelector/Item.php:Tx_Sitemgr_ToolbarItems_CustomerSelector_Item->openPageOfCustomer';
		$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][]
			= t3lib_extMgm::extPath($_EXTKEY).'Classes/ToolbarItems/CustomerSelector/Hook.php';
	}
/**
 * be_user table
 */ 
	$tempColumns = array (
		'tx_sitemgr_manager_for_be_groups' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:sitemgr/locallang_db.xml:be_users.tx_sitemgr_manager_for_be_groups',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'be_groups',	
				'foreign_table_where' => 'ORDER BY be_groups.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 22,
			)
		),
	);
	t3lib_div::loadTCA('be_users');
	t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('be_users','tx_sitemgr_manager_for_be_groups;;;;1-1-1');

/**
 * tx_templavoila_tmplobj table
 */ 
	$tempColumns = array (
		'tx_sitemgr_manager_allowed_for_customer' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:sitemgr/locallang_db.xml:tx_templavoila_tmplobj.tx_sitemgr_manager_allowed_for_customer',		
			'config' => array (
				'type'    => 'check',
				'default' => 0	
			)
		),
	);
	t3lib_div::loadTCA('tx_templavoila_tmplobj');
	t3lib_extMgm::addTCAcolumns('tx_templavoila_tmplobj',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('tx_templavoila_tmplobj','tx_sitemgr_manager_allowed_for_customer;;;;1-1-1');


/**
 * tx_sitemgr_customer
 */ 
	t3lib_extMgm::allowTableOnStandardPages('tx_sitemgr_customer');
	/*
	$TCA['tx_kssitemgr_customer']['columns']['rows']['wizards']=array(
		 '_PADDING' => 1,
		  '_VERTICAL' => 1,
		   'edit' => Array(
				'type' => 'popup',
				'title' => 'Edit filemount',
				'script' => 'wizard_edit.php',
				'icon' => 'edit2.gif',
				'popup_onlyOpenIfSelected' => 1,
				'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
			),
	);*/
	$TCA['tx_sitemgr_customer'] = array (
		'ctrl' => array (
			'title'     => 'LLL:EXT:sitemgr/locallang_db.xml:tx_sitemgr_customer',		
			'label'     => 'title',	
			'tstamp'    => 'tstamp',
			'crdate'    => 'crdate',
			'cruser_id' => 'cruser_id',
			'versioningWS' => TRUE, 
			'origUid' => 't3_origuid',
			'default_sortby' => 'ORDER BY title',	
			'delete' => 'deleted',
			'dividers2tabs'=>1,
			'adminOnly'=>1,
			'canNotCollapse'=>1,
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sitemgr_customer.gif',
		),
	);
?>
