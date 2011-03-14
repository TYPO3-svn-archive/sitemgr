<?php
class tx_ks_sitemgr_userstylefe{
	/**
	 * handler for non cached output processing to insert piwik tracking code
	 * if in independent mode
	 *
	 * @param	pointer    $$params: passed params from the hook
	 * @param	pointer    $reference: to the parent object
	 * @return	void       void
	 */
	function contentPostProc_output(&$params, &$reference){
		$this->extConf = $params['pObj']->tmpl->setup['plugin.']['tx_ks_sitemgr.'];
		$this->extConf = $params['pObj'];
		#print_r($this->extConf);
		#die();
	}
    /**
	 * handler for cached output processing to assure that the siteid is created
	 * in piwik	 
	 *
	 * @param	pointer    $$params: passed params from the hook
	 * @param	pointer    $reference: to the parent object
	 * @return	void       void
	 */
	function contentPostProc_all(&$params, &$reference){
		
	}
}
?>