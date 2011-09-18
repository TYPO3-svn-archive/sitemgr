<?php

class Tx_SitemgrTemplate_Domain_Repository_TemplateAbstractRepository {
	/**
	 * Holds a reference to all templates in this repository
	 */
	protected $templates = array();
	function getAllTemplates() {
		return $this->templates;
	}
	function get($identifier) {
		foreach($this->templates as $template) {
				if($template->getIdentifier() == $identifier) {
					return $template;
				}
		}
	}
}