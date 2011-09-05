<?php
class Tx_SitemgrTemplate_Domain_Model_TemplateTemplavoilaFrameworkModel
	extends Tx_SitemgrTemplate_Domain_Model_TemplateAbstractModel {
	function __construct($name) {
		$skinInfo = tx_templavoilaframework_lib::getSkinInfo($name);
		
		if ($skinInfo['icon']) {
			$previewIconFilename = $GLOBALS['BACK_PATH'] . $skinInfo['icon'];
		} else {
			$previewIconFilename = $GLOBALS['BACK_PATH'].'../'.t3lib_extMgm::siteRelPath('templavoila_framework').'/default_screenshot.gif';
		}
		
		$this->config = array(
			'id'         => get_class($this).'|'.$name,
			'icon'       => $previewIconFilename,
			'screens'    => array(
				$previewIconFilename,
			),
			'description'=> $skinInfo['description'],
			'title'      => $skinInfo['title'],
			'copyright'  => 'unsupported',
			'version'    => t3lib_extMgm::getExtensionVersion($name),
		);
	}
}