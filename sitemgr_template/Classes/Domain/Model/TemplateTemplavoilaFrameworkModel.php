<?php
class Tx_SitemgrTemplate_Domain_Model_TemplateTemplavoilaFrameworkModel extends Tx_SitemgrTemplate_Domain_Model_TemplateAbstractModel {
	/**
	 * @param $name Name of the skin
	 */
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
	function isInUseOnPage($pid) {
		$tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$tplRow = $tmpl->ext_getFirstTemplate($pid);
		list($class, $_EXTKEY) = explode('|', $this->config['id']);
		if($tplRow['skin_selector'] == $_EXTKEY) {
			$this->config['isInUse'] = TRUE;
		} else {
			$this->config['isInUse'] = FALSE;
		}
	}
	function getCopyrightInformation() {
		list($class, $_EXTKEY) = explode('|', $this->config['id']);
		if(substr($_EXTKEY,0,4) == 'EXT:') {
			$_EXTKEY = substr($_EXTKEY,4);
			include(t3lib_extMgm::extPath($_EXTKEY) . 'ext_emconf.php');
			//$EM_CONF[$_EXTKEY]
			$copyTab = array(
				'title'  => 'Copyright',
				'layout' => 'fit',
				'items'  => array(
					array(
						'xtype' => 'propertygrid',
						'autoHeight' => true,
						'source' => array(
							'name'           => $this->config['title'],
							'name additional'=> $EM_CONF[$_EXTKEY]['title'],
							'version'        => $EM_CONF[$_EXTKEY]['version'],
							'state'          => $EM_CONF[$_EXTKEY]['state'],
							'author name'    => $EM_CONF[$_EXTKEY]['author'],
							'author company' => $EM_CONF[$_EXTKEY]['author_company'],
							'author email'   => $EM_CONF[$_EXTKEY]['author_email'],
							'license'        => 'GPL'
						),
					)
				)
			);
		} else {
			$copyTab = array();
		}
		return $copyTab;
	}
}