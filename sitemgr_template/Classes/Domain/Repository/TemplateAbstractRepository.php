<?php

class Tx_SitemgrTemplate_Domain_Repository_TemplateAbstractRepository {
	/**
	 * Holds a reference to all templates in this repository
	 */
	protected $templates = array();
	function getAllTemplates() {
		return $this->templates;
	}
}