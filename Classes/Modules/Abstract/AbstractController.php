<?php 
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_Sitemgr_Modules_Abstract_AbstractController{
	private function calculatePathToModule() {
		$path = str_replace('Tx_Sitemgr_','',get_class($this));
		$path = str_replace('_','/',$path);
		$path = substr($path,0,-10);
		$path = '../typo3conf/ext/sitemgr/Classes/Modules/'.basename($path).'/';
		return $path;
	}
	function getModuleJsFile() {
		return $this->calculatePathToModule().'main.js';
	}
	function getModuleCssFile() {
		return $this->calculatePathToModule().'main.css';
	}
	function getModuleLLFile() {
		$file = $this->calculatePathToModule().'locallang.xml';
		$file = substr($file,17);
		return 'EXT:'.$file;
	}
}