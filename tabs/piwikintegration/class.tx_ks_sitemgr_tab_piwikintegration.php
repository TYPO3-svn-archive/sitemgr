<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_tracking.php');

class tx_ks_sitemgr_tab_piwikintegration extends tx_ks_sitemgr_tab{
	protected $file = __FILE__;
	protected $access = array(
		'general' => 'customerAdmin'
	);
	function getModuleJavaScript(&$js,$uid) {
		try{
			$tracker = new tx_piwikintegration_tracking();
			$js     .= $this->getModuleJavaScriptHelper(
				array(
					'PiwikSiteId' => $tracker->getPiwikSiteIdForPid($uid),
				),
				$uid
			);
		}catch(Exception $e) {
		
		}
	}
}