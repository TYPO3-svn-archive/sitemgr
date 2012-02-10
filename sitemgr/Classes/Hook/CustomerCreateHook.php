<?php

class Tx_Sitemgr_Hook_CustomerCreateHook {
	function round3($fields,$params, $parent) {
		$templateName = $GLOBALS["BE_USER"]->getTSConfig(
					  	'mod.web_txsitemgr.template.defaultTemplate',
						t3lib_BEfunc::getPagesTSconfig($params['customerRootPid'])
					);
		$templateName = $templateName['value'];
		if(strlen(trim($templateName))!==0) {
			$TemplateRepository = new Tx_SitemgrTemplate_Domain_Repository_TemplateRepository();
			$TemplateRepository->get($templateName)->setEnvironment($params['customerRootPid'], null);
		}
	}
}