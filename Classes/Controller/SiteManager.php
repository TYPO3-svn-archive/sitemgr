<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Controller for the Room object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

 class Tx_Sitemgr_Controller_SiteManagerController extends Tx_Extbase_MVC_Controller_ActionController {
 	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {

	}
	/**
	 * Displays all Rooms
	 *
	 * @return string The rendered list view
	 */
	public function indexAction() {
		$this->view->assign('modules', $this->getModules());
		$this->view->assign('settings', $this->getSettings());
	}
	
	protected function getSettings() {
		$customer       = new Tx_Sitemgr_Utilities_CustomerUtilities();
		$currentRequest = $this->controllerContext->getRequest();
		$pageId         = intval($currentRequest->id);
		try {
			$settings = array(
				'customerId'       => $customer->getCustomerForPage($pageId), // ###CID###,
				'customerSelected' => true,                                   // ###CS?###,
				'customerName'     => addslashes($customer->getName()),
				'customerRootPid'  => $customer->getPage(),                   // ###CIDROOTPID###,
				'customerRootName' => addslashes($customer->getName()),
				'beUserAdmin'      => $GLOBALS['BE_USER']->user['admin'],
				'uid'              => $pageId,                                // ###UID###,
				'version'          => t3lib_extMgm::getExtensionVersion('sitemgr'),
			);
		} catch(Exception $e) {
			$settings = array(
				'customerId'       => 0,                                      // ###CID###,
				'customerSelected' => false,                                  // ###CS?###,
				'customerName'     => '-',
				'customerRootPid'  => 0,                                      //###CIDROOTPID###,
				'customerRootName' => 'ROOT - unknown',
				'beUserAdmin'      => $GLOBALS['BE_USER']->user['admin'],
				'uid'              => $pageId,                                //###UID###,
				'version'          => t3lib_extMgm::getExtensionVersion('sitemgr'),
			);
		}
		return $settings;
	}
	protected function getModules() {
		$settings = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sitemgr']['hook'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['hook'] as $_classRef) {
		      #$_procObj = &t3lib_div::getUserObj($_classRef);
		      #$_procObj->getModuleJavascript($js,$this->pageinfo['uid']);
		   }
		}
		return $settings;
	} 
}