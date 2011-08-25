<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_Sitemgr_Modules_Help_HelpController extends Tx_Sitemgr_Modules_Abstract_AbstractController{
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
