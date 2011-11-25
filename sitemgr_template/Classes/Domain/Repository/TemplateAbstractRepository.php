<?php

class Tx_SitemgrTemplate_Domain_Repository_TemplateAbstractRepository {
	/**
	 * Holds a reference to all templates in this repository
	 */
	protected $templates = array();
	function getAllTemplates() {
		return $this->templates;
	}
	function getAllTemplatesAsArray() {
		$output = array();
		foreach($this->templates as $template) {
			$output[] = $template->getConfig();
		}
		return $output;
	}
	/**
	 * @param $pid pageid
	 * @return array
	 */
	function getAllTemplatesAsArrayMarkInUse($pid) {
		foreach($this->templates as $key=>$template) {
			$this->templates[$key]->isInUseOnPage($pid);
		}
		return $this->getAllTemplatesAsArray();
	}
	/**
	 * @param string $identifier
	 * @return Tx_SitemgrTemplate_Domain_Model_TemplateAbstractModel
	 */
	function get($identifier) {
		foreach($this->templates as $template) {
				if($template->getIdentifier() == $identifier) {
					return $template;
				}
		}
	}
}