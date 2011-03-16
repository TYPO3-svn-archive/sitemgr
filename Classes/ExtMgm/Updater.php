<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * class.ext_update.php
 *
 * extmgm update script
 *
 * $Id: class.ext_update.php 42961 2011-02-02 08:25:05Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

class Tx_sitemgr_ExtMgm_Updater extends Tx_sitemgr_ExtMgm_AbstractUpdater{
	function main() {
		global $LANG;
		$LANG->includeLLFile('EXT:sitemgr/Resources/Private/Language/locallang_extmgm.xml');
		$func = trim(t3lib_div::_GP('func'));
		if(t3lib_div::_GP('do_update')) {
			if (method_exists($this, $func)) {
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$this->$func(),
					'',
					t3lib_FlashMessage::OK
			    );
				$buffer.= $flashMessage->render();
			} else {
				$buffer.=$LANG->getLL('methodNotFound');
			}
		}
		$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('message'),
					'',
					t3lib_FlashMessage::INFO
			    );
		
		$buffer.= $flashMessage->render();
		$buffer.= $this->getHeader($LANG->getLL('header.installation'));
		$buffer.= $this->getFooter();

		$buffer.= $this->getHeader($LANG->getLL('header.configuration'));
		$buffer.= $this->getFooter();

		$buffer.= $this->getHeader($LANG->getLL('header.ks_sitemgr'));
		$buffer.= $this->getButton('importFromKsSitemgr');
		$buffer.= $this->getFooter();
		return $buffer;
	}
	function importFromKsSitemgr() {
		//touch be_users
		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE be_users DROP tx_sitemgr_manager_for_be_groups');
		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE be_users CHANGE COLUMN tx_sitemgr_manager_for_be_groups                        tx_kssitemgr_manager_for_be_groups');

		//touch tv table
		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_templavoila_tmplobj DROP tx_sitemgr_manager_allowed_for_customer');
		$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_templavoila_tmplobj CHANGE COLUMN tx_kssitemgr_manager_allowed_for_customer tx_sitemgr_manager_allowed_for_customer');

		//migrate table
		$GLOBALS['TYPO3_DB']->admin_query('DROP TABLE tx_sitemgr_customer');
		$GLOBALS['TYPO3_DB']->admin_query('RENAME TABLE tx_kssitemgr_customer TO tx_sitemgr_customer');
		
		return 'If no errors are displayed, everything worked fine.';
	}
}