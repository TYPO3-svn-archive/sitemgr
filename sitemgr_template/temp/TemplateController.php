<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_SitemgrTemplate_Modules_Template_TemplateController extends Tx_Sitemgr_Modules_Abstract_AbstractController{
	protected $file = __FILE__;
	protected $access = array(
		'general' => 'customerAdmin'
	);
	function getModuleJavaScript(&$js,$uid) {
		$js     .= $this->getModuleJavaScriptHelper(
			array(),
			$uid
		);
	}
	function getTemplates($id) {
		$TemplateRepository = new Tx_SitemgrTemplate_Domain_Repository_TemplateRepository();
		return $TemplateRepository->getAllTemplatesAsArray();
	}

	function setTemplateAndOptions($customer, $template, $options) {
		return array(
			'success' => true
		);
	}
	/**
	 * @todo
	 *  - security check if allowed	 
	 *  - relative path for @import @BACKPATH ...	 
	 */	 	
	function saveTemplateSettings($arg) {
		// config
			list($TVtemplate,$skin,$cid) = explode(':',$arg['args']);
			$customer = new tx_ks_sitemgr_customer($cid);
			$customer->init();
			$saveFolder = $customer->getFolder().'layout/';
			t3lib_div::mkdir_deep($customer->getFolder(),'layout');
		//create dynamic css	
			$file = PATH_site.$this->getTemplatePath($TVtemplate).$skin.'/install.ini';
			$iniArray = parse_ini_file($file,TRUE);
			$css = '@import "/'.$this->getTemplatePath($TVtemplate).$skin.'/main.css" all;'."\n";
			
			foreach($iniArray as $sectionName=>$sectionContent) {
				if(substr($sectionName,0,4)=='FILE'){
					//move / modify file
					$source = $arg[$sectionName]['tmp_name'];
					$dest   = $saveFolder.$sectionContent['filename']; 
					if(move_uploaded_file($source,$dest)) {
						$css.= $sectionContent['css']."\n";
					} else {
						$css.='/* no file uploaded - '.$sectionContent['css']."*/\n";
					}
				} elseif(substr($sectionName,0,8)=='CONSTANT') {
					tx_ks_sitemgr_div::setTSConstantValue($customer->getPage(),$sectionContent['constant'],trim($arg[$sectionName]));
					$css.= '/* '.$sectionContent['description'].' : '.trim($arg[$sectionName])." */\n";
				}
			}
		//update page template
			$TVdatastructure = t3lib_BEfunc::getRecord(
				'tx_templavoila_tmplobj',
				$TVtemplate
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'pages',
				'uid = '.$customer->getPage(),
				array(
					'tx_templavoila_ds' => $TVdatastructure['datastructure'], 
					'tx_templavoila_to' => $TVtemplate,
				)
			);
		//read template
			$buffer     = t3lib_div::getURL(dirname(__FILE__).'/screen.css');
			$replace    = array(
				'###CID###' => $cid,
				'###UID###' => $customer->getMainUserUid(),
				'###CSS###' => $css,
			);
			foreach($replace as $key => $value) {
				$buffer = str_replace($key,$value,$buffer);
			}
			t3lib_div::writeFile($saveFolder.'screen.css',$buffer);
		
		return array(
			'success'     =>true,
		);
	}
}