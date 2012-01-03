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
			//fetch screenshots
		$extName = substr($name,4);
		if(($name !== 'LOCAL:error') && (t3lib_extMgm::isLoaded($extName, FALSE))){
			$additionalScreenshots = t3lib_div::getFilesInDir(t3lib_extMgm::extRelPath($extName) . 'screenshots', '', TRUE);
			$additionalScreenshots = array_values($additionalScreenshots);
		}
		if(count($additionalScreenshots) === 0) {
			$additionalScreenshots = array(
				$previewIconFilename,
			);
		}
			//build config array
		$this->config = array(
			'id'           => get_class($this).'|'.$name,
			'icon'         => $previewIconFilename,
			'screens'      => $additionalScreenshots,
			'description'  => $skinInfo['description'],
			'title'        => $skinInfo['title'],
			'copyright'    => null,
			'version'      => t3lib_extMgm::getExtensionVersion($name),
			'extensionKey' => $extName,
		);
			// get copy info
		$this->config['copyright'] = $this->getCopyrightInformation();
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
		// @todo check how to init the isInUse property
	}
	function getCopyrightInformation() {
		list($class, $extensionKey) = explode('|', $this->config['id']);
		if(substr($extensionKey,0,4) == 'EXT:') {
			$_EXTKEY = substr($extensionKey,4);
			include(t3lib_extMgm::extPath($_EXTKEY) . 'ext_emconf.php');
			$copyArray = array(
				'name'             => $this->config['title'],
				'nameAdditional'   => $EM_CONF[$_EXTKEY]['title'],
				'description'      => $EM_CONF[$_EXTKEY]['description'],
				'version'          => $EM_CONF[$_EXTKEY]['version'],
				'state'            => $EM_CONF[$_EXTKEY]['state'],
				'authorName'       => $EM_CONF[$_EXTKEY]['author'],
				'authorCompany'    => $EM_CONF[$_EXTKEY]['author_company'],
				'authorEmail'      => $EM_CONF[$_EXTKEY]['author_email'],
				'license'          => 'GPL',
				'hasDocumentation' => file_exists(t3lib_extMgm::extPath($_EXTKEY).'doc/manual.sxw'),
			);
		} else {
			$copyArray = array();
		}
		return $copyArray;
	}
	/**
	 * returns environment options
	 *
	 * @param integer $pid
	 * @return array
	 */
	function getEnvironmentOptions($pid) {
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['templavoila_framework']);
		$templates = t3lib_BEfunc::getRecordsByField(
			'tx_templavoila_tmplobj',
			'pid',
			$config['templateObjectPID'],
			'AND datastructure LIKE "%templavoila_framework/core_templates/datastructures/page/f%"',
			'',
			'title'
		);
		$page                = t3lib_BEfunc::getRecord('pages',$pid);
		$options = array();
		foreach($templates as $option) {
			$options[] = array(
				$option['uid'],
				$option['title'],
				$option['previewicon']
			);
			if($option['uid'] === $page['tx_templavoila_to']) {
				$selected_tv_ts = $page['tx_templavoila_to'];
			}
			if($option['uid'] === $page['tx_templavoila_next_to']) {
				$selected_tv_ts_next = $page['tx_templavoila_next_to'];
			}
		}
			// force reset due to invalid ts / to
		if(!isset($selected_tv_ts)) {
			$selected_tv_ts = $options[0]['uid'];
		}
		if(!isset($selected_tv_ts_next)) {
			$selected_tv_ts_next = $options[0]['uid'];
		}
		return array(
			'layout' => 'form',
			'items' => array(
				array(
					'xtype'      => 'sitemgrcombobox',
					'fieldLabel' => $GLOBALS['LANG']->sL('LLL:EXT:sitemgr_template/Resources/Private/Language/Modules/Template/locallang.xml:SitemgrTemplates_rootpageTvStructure'),
					'staticData' => $options,
					'value'      => $selected_tv_ts,
					'name'       => 'options[tv_ts]',
				),
				// useless due to the option, that the starting page is a link
				/*array(
					'xtype'      => 'sitemgrcombobox',
					'fieldLabel' => $GLOBALS['LANG']->sL('LLL:EXT:sitemgr_template/Resources/Private/Language/Modules/Template/locallang.xml:SitemgrTemplates_rootpageTvStructure_next'),
					'staticData' => $options,
					'value'      => $selected_tv_ts_next,
					'name'       => 'options[tv_ts_next]',
				),*/
				array(
					'xtype'      => 'sitemgrTemplavoilaRereferenceButton',
				),
			),
		);
	}
	function setEnvironment($pid, $options) {
		$this->initializeTSParser($pid);
		list($templateClass, $templateUID) = explode('|', $this->config['id']);
		$saveId = $this->tsParserTplRow['uid'];
		$recData['sys_template'][$saveId]['skin_selector'] = $templateUID;
		$recData['sys_template'][$saveId]['root']          = 1;
		$recData['sys_template'][$saveId]['clear']         = 0;

			// set tv setttings
		if($options !== null) {
			if($options['tv_ts'] != 0) {
					// set page ts and ds
				$recData['pages'][$pid]['tx_templavoila_to'] = $options['tv_ts'];
				$recData['pages'][$pid]['tx_templavoila_ds'] = $this->getTvDs($options['tv_ts']);
					// preselect for subpages to ensure clean switches
				$recData['pages'][$pid]['tx_templavoila_next_to'] = 0;
				$recData['pages'][$pid]['tx_templavoila_next_ds'] = 0;
			}
			#if($options['tv_ts_next'] != 0) {
			#	$recData['pages'][$pid]['tx_templavoila_next_to'] = $options['tv_ts_next'];
			#	$recData['pages'][$pid]['tx_templavoila_next_ds'] =$this->getTvDs($options['tv_ts_next']);
			#}
		}
			// set general record storage page
		$tvFrameworkSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['templavoila_framework']);
		$recData['pages'][$pid]['storage_pid'] = $tvFrameworkSettings['templateObjectPID'];
			// store changes
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce = new t3lib_TCEmain();
		$tce->stripslashes_values = 0;
			// Initialize
		$user = clone $GLOBALS['BE_USER'];
		$user->user['admin'] = 1;
		$tce->start($recData, Array(), $user);
			// Save the stuff
		$tce->process_datamap();
			// Clear the cache (note: currently only admin-users can clear the cache in tce_main.php)
		$tce->clear_cacheCmd('all');
	}
	private function getTvDs($to_uid) {
		if($to_uid) {
			$to = t3lib_BEfunc::getRecord('tx_templavoila_tmplobj',$to_uid);
			if(is_array($to)) {
				return $to['datastructure'];
			} else {
				throw new Exception('Invalid TO ' . $to_uid);
			}
		} else {
			return '';
		}
	}
}