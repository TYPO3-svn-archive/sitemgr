	if(0 && t3lib_extMgm::isLoaded('templavoila')) {
		//load template module if templavoila is active
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['modules']['sitemgr_template'] =
		'Tx_Sitemgr_Modules_Template_TemplateController';
		Tx_Sitemgr_Utilities_CustomerUtilities::registerModule('Tx_Sitemgr_Modules_Template_TemplateController');
	}
	