<?php

class Tx_SitemgrTemplate_Domain_Model_TemplateAbstractModel {
	/**
	 * $config = array(
	 *    'id'         => $name,
	 *    'icon'       => 'path',
	 *    'screens'    => array()
	 *    'description'=> $skinInfo['description'],
	 *    'title'      => $skinInfo['title'],
	 *    'copyright'  => 'someone',
	 *    'version'    => t3lib_extMgm::getExtensionVersion($name),
	 * );
	*/
	protected $config = array();
	function __construct() {

	}
	function getConfig() {
		return $this->config;
	}
	function getScreenshots() {
		return $this->config['screens'];
	}
	function getMainScreenshot() {
		return $this->config['screens'][0];
	}
	function getDescription() {
		return $this->config['description'];
	}
	function getTSConstants() {
		return $this->config['tsConstants'];
	}
	function getCopyright() {
		return $this->config['copyright'];
	}
	function getTitle() {
		return $this->config['title'];
	}
	function getIdentifier() {
		return $this->config['id'];
	}
	function getCategory() {
		return get_class($this);
	}
	function setup() {
		$this->setConstants();
		$this->setPageTS();
		$this->setTemplateTS();
		$this->setEnvironment();
	}
	function setConstants() {
		
	}
	function setPageTS() {
	
	}
	function setTemplateTS() {
	
	}
	function setEnvironment() {
	
	}
}