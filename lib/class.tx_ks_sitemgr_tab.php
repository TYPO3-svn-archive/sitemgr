<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class tx_ks_sitemgr_tab {
	protected $file = __FILE__;
	protected $form = null;
	protected $access = array(
		'general' => 'admin'
	);
	public function getModuleJavaScript(&$pageinfo,$uid) {
	
	}
	public function getAPI() {
		return array();
	}
	protected function getModuleJavaScriptHelper($replace,$uid = 0) {
		if($this->checkAccess('general',$uid)) {
			return $this->loadFileAndReplace(
				dirname($this->file).'/extjs.js',
				$replace
			);
		}
	}
	protected function loadFileAndReplace($file,$replaceArray) {
		$fileContent = file_get_contents($file);
		if(is_array($replaceArray)) {
			foreach($replaceArray as $key => $replace) {
				$fileContent = str_replace('###'.$key.'###',$replace,$fileContent);
			}
		}
		foreach($this->loadLangFileIntoArray() as $key => $replace) {
			$fileContent = str_replace('###LANG.'.$key.'###',$replace,$fileContent);
		}
		return $fileContent;
	}
	protected function loadLangFileIntoArray() {
		global $LANG;
		#$file = 'EXT:ks_sitemgr/tabs/beuser/locallang.xml';
		$file = 'E:/devenv/www/t3alpha4.3/typo3conf/ext/ks_sitemgr/tabs/beuser/locallang.xml';
		$file = dirname($this->file).'/locallang.xml';
		$file = t3lib_div::fixWindowsFilePath($file);
		if(file_exists($file)) {
			$LANG->includeLLFile($file);
			return $GLOBALS['LOCAL_LANG'][$LANG->lang];
		}
		return array();
	}
	protected function addErrorForForm($field,$message,$value=NULL) {
		if($this->form == null) {
			$this->form = array(
				'success' => 'true',
				'errors'  => array()
			);
		}
		$this->form['success'] = false;
		$this->form['errors'][$field] = $message;
		if($value) {
			$this->form['data'][$field] = $value;
		}
		return $this->form;
	}
	protected function debug($obj) {
		ob_start();
		print_r($obj);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	protected function getReturnForForm() {
		if($this->form == null) {
			$this->form = array(
				'success' => 'true'
			);
		}
		return $this->form;
	}
	public function checkAccess($function,$uid = 0) {
		if(array_key_exists($function, $this->access)) {
			return $this->checkAccessHelper($function,$uid);
		} else {
			return $this->checkAccessHelper('general',$uid);
		}
		
	}
	protected function checkAccessHelper($function,$uid = 0) {
		if($GLOBALS['BE_USER']->user['admin']==1) {
				return true;
		}
		if($this->access[$function]=='all') {
			return true;
		}
		try{
			$customer = new tx_ks_sitemgr_customer();
			$customer->getCustomerForPage($uid);
			
			if($this->access[$function]=='admin') {
				return false;
			} elseif($this->access[$function]=='customerAdmin') {
				//in customerAdminList
				print_r($customer->getAdminUsersUids());
				if(in_array($GLOBALS['BE_USER']->user['uid'], $customer->getAdminUsersUids())) {
					return true;
				} else {
					return false;
				}
				
			} elseif($this->access[$function]=='customerMainAdmin') {
				//is customerMainAdmin?
				if($customer->getMainUserUid() == $GLOBALS['BE_USER']->user['uid']) {
					return true;
				} else {
					return false;
				}
			}
		} catch(Exception $e) {
			echo 'Exception';
			return false;
		}
	}
}