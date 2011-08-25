<?php 
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_Sitemgr_Modules_Abstract_AbstractController{
	private function calculatePathToModule() {
		throw new Exception('deprecated function');
	}
	private function getModuleJsPath() {
		return str_replace('Classes/Modules', 'Resources/Public/JavaScripts/Modules', $this->getModuleGenericPath()).'/';
	}
	private function getModuleCssPath() {
		return str_replace('Classes/Modules', 'Resources/Public/Stylesheets/Modules', $this->getModuleGenericPath()).'/';
	}
	private function getModuleLangPath() {
		return str_replace('Classes/Modules', 'Resources/Private/Language/Modules', $this->getModuleGenericPath()).'/';
	}
	private function getModuleGenericPath() {
		$className = get_class($this);
		$classNameParts = explode('_', $className, 3);
		$extensionKey = t3lib_div::camelCaseToLowerCaseUnderscored($classNameParts[1]);
		if (t3lib_extMgm::isLoaded($extensionKey)) {
			$classFilePathAndName = t3lib_extMgm::extRelPath($extensionKey) . 'Classes/' . strtr($classNameParts[2], '_', '/') . '.php';
		} else {
			throw new Exception('Extension not loaded: '.$extensionKey);
		}
		return dirname($classFilePathAndName);
	}
	function getModuleJsFile() {
		return $this->getModuleJsPath().'main.js';
	}
	function getModuleCssFile() {
		return $this->getModuleCssPath().'main.css';
	}
	function getModuleLLFile() {
		$file = $this->getModuleLangPath().'locallang.xml';
		$file = substr($file,17);
		return 'EXT:'.$file;
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
	protected function getReturnForForm() {
		if($this->form == null) {
			$this->form = array(
				'success' => 'true'
			);
		}
		return $this->form;
	}
}