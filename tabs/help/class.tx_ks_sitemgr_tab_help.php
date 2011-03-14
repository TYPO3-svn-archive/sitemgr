<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class tx_ks_sitemgr_tab_help extends tx_ks_sitemgr_tab{
	protected $file = __FILE__;
	protected $access = array(
		'general' => 'all'
	);
	function getModuleJavaScript(&$js,$uid) {
		$js.= $this->getModuleJavaScriptHelper(
			dirname(__FILE__).'/extjs.js',
			$uid
		);
	}
}
