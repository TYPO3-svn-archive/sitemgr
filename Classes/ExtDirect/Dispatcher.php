<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
require_once(t3lib_extMgm::extPath('sitemgr').'lib/class.tx_ks_sitemgr_tab.php');
require_once(t3lib_extMgm::extPath('sitemgr').'lib/class.tx_ks_sitemgr_customer.php');
require_once(t3lib_extMgm::extPath('sitemgr').'lib/class.tx_ks_sitemgr_div.php');


class Tx_Sitemgr_ExtDirect_Dispatcher{ 	 	
	public function test() {
		return 'test';
	}
	public function dispatch($module,$function,$args) {
		try {
			if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['hook'][$module])) {
				$_classRef = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['hook'][$module];
				$_procObj = &t3lib_div::getUserObj($_classRef);
				if(method_exists($_procObj, $function)) {
					#if($_procObj->checkAccess($function,$args['uid'])) {
						return $_procObj->$function($args);
					#} else {
					#	return 'Access denied '.$function.' in module'.$module;
					#}
				} else {
					//throw new Exception('unknown action');
					return 'Unknown function '.$function.' in module'.$module;
				}
			} else {
				return 'Unknown module '.$module;
			}
		} catch(Exception $e) {
			return array(
				'success' => false,
				'errorMessage' => $e->getMessage()
			);
		}
	}
	/**
	 * special function to allow paged display of grids
	 */	 	
	public function dispatchPaged($module,$function,$args,$start,$stop,$sort,$dir) {
		$args = array(
			'args'   => $args,
			'start'  => intval($start),
			'stop'   => intval($stop),
			'sort'   => $sort,
			'dir'    => $dir == 'DESC' ? 'DESC' : 'ASC',
		);
		return $this->dispatch($module,$function,$args);
	}
	/**
	 * handles the form
	 *	 	
	 * @formHandler
	 */	 	
	public function handleForm($arg) {
		return $this->dispatch($arg['module'],$arg['fn'],$arg);
	}
	public function getSubpages($uid=0) {
		$pages  = t3lib_BEfunc::getRecordsByField ('pages', 'pid', $uid);
		$buffer = array();
		foreach($pages as $page) {
			$buffer[]=array(
				'id'  => $page['uid'],
				'text' => $page['title'],
			);
		}
		return array_values($buffer);
	}
}