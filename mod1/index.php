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
 * mod1/index.php
 *
 * backendmodule
 *
 * $Id: index.php 37353 2010-08-28 08:45:36Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */


$LANG->includeLLFile('EXT:sitemgr/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
require_once(t3lib_extMgm::extPath('sitemgr').'lib/class.tx_ks_sitemgr_tab.php');
require_once(t3lib_extMgm::extPath('sitemgr').'lib/class.tx_ks_sitemgr_customer.php');
// DEFAULT initialization of a module [END]



/**
 * Module 'Statistics' for the 'piwikintegration' extension.
 *
 * @author	Kay Strobach <info@kay-strobach.de>
 * @package	TYPO3
 * @subpackage	tx_piwikintegration
 */
	class  tx_piwikintegration_module1 extends t3lib_SCbase {
		var $pageinfo;

		/**
		 * Initializes the Module
 		 *
		 * @return	void
		 */
		function init()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			parent::init();
			$this->extKey = 'ks_sitemgr';

			/*
			if (t3lib_div::_GP('clear_all_cache'))	{
				$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
			}
			*/
		}

		/**
		 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
		 *
		 * @return	void
		 */
		function menuConfig()	{
			global $LANG;
		}

		/**
		 * Main function of the module. Write the content to $this->content
		 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
		 *
		 * @return	void
		 */
		function main()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;			// initialize doc

			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->getPageRenderer()->loadExtJS();
			$set = t3lib_div::_GP('SET');
			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('sitemgr') . 'mod1/mod_template.html');
			$this->doc->getPageRenderer()->addJsFile('ajax.php?ajaxID=ExtDirect::getAPI&namespace=TYPO3.ks_sitemgr','text/javascript',NULL,FALSE);
			$this->doc->getPageRenderer()->addCssInlineBlock('TYPO3ThemeFix','.x-tab-panel-body .x-panel-body {padding:0px;} .item-wrap{margin:5px; background-color:#DCDCDC} .item-wrap .x-view-selected{background-color:#AEAEAE}');
			$this->doc->getPageRenderer()->addCssFile(t3lib_extMgm::extRelPath('sitemgr') . 'mod1/ext-icons.css');
			
			$tgroup = $GLOBALS["BE_USER"]->getTSConfig(
			  	'mod.web_txkssitemgrM1.createUser.group',
				t3lib_BEfunc::getPagesTSconfig($this->pageinfo['uid'])
			);
			if(!$tgroup) {
				if($this->id) {
					$flashMessage = t3lib_div::makeInstance(
					    't3lib_FlashMessage',
					    $LANG->getLL('error.configuration'),
					    $LANG->getLL('warning'),
					    t3lib_FlashMessage::WARNING
					);
					t3lib_FlashMessageQueue::addMessage($flashMessage);
				} else {
					$flashMessage = t3lib_div::makeInstance(
					    't3lib_FlashMessage',
					    $LANG->getLL('error.pageSelect'),
					    $LANG->getLL('warning'),
					    t3lib_FlashMessage::WARNING
					);
					t3lib_FlashMessageQueue::addMessage($flashMessage);				
				}
			} elseif(!t3lib_extMgm::isLoaded('be_acl')) {
				$flashMessage = t3lib_div::makeInstance(
				    't3lib_FlashMessage',
				    $LANG->getLL('error.beaclmissing'),
				    $LANG->getLL('warning'),
				    t3lib_FlashMessage::WARNING
				);
				t3lib_FlashMessageQueue::addMessage($flashMessage);
			} else {
				if(version_compare ($GLOBALS['TYPO_VERSION'],'4.4.0','>=')) {
					$this->content = '';
					
					$js =  file_get_contents(t3lib_extMgm::extPath('sitemgr') . 'mod1/extjs.js');
					$markers['###AdditionalJs###']    = $this->getModules();
					try{
						$customer = new tx_ks_sitemgr_customer();
						$markers['###CID###']         = $customer->getCustomerForPage($this->pageinfo['uid']);
						$markers['###CIDNAME###']     = addslashes($customer->getName());
						$markers['###CS?###']         = 'true';
						$markers['###CIDROOTPID###']  = $customer->getPage();
						$markers['###CIDROOTNAME###'] = addslashes($customer->getName()); //Should be PageName lateron
						$markers['###ADMIN?###']      = $GLOBALS['BE_USER']->user['admin'];
						$markers['###UID###']         = intval($this->pageinfo['uid']);
						$markers['###ExtVersion###']  = t3lib_extMgm::getExtensionVersion('ks_sitemgr');
					} catch(Exception $e) {
						$markers['###CID###']         = 0;
						$markers['###CIDNAME###']     = '-';
						$markers['###CS?###']         = 'false';
						$markers['###CIDROOTPID###']  = 0;
						$markers['###CIDROOTNAME###'] = 'ROOT';
						$markers['###ADMIN?###']      = $GLOBALS['BE_USER']->user['admin'];
						$markers['###UID###']         = intval($this->pageinfo['uid']);
						$markers['###ExtVersion###']  = t3lib_extMgm::getExtensionVersion('ks_sitemgr');
					}
					foreach($markers as $markerName => $marker)  {
						$js = str_replace($markerName,$marker,$js);
					}
					$this->doc->getPageRenderer()->addJsInlineCode(
						'ksSitemgrModules',
						$js,
						false
					);
				} else {
					$this->content.='YOU need atleast TYPO3 4.4.x - requirements are no fun.';
				}
			}
			// compile document
				$docHeaderButtons = $this->getButtons();
				$markers['CSH']      = $this->docHeaderButtons['csh'];
				$markers['CONTENT']  = $this->content;
			
			//add browserwarning
				if(!$flashMessage) {
					$flashMessage = t3lib_div::makeInstance(
					    't3lib_FlashMessage',
					    $LANG->getLL('error.browser'),
					    $LANG->getLL('warning'),
					    t3lib_FlashMessage::WARNING
					);
					t3lib_FlashMessageQueue::addMessage($flashMessage);
				}
			
			// Build the <body> for the module
				$this->content = $this->doc->startPage($LANG->getLL('title'));
				$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
				$this->content.= $this->doc->endPage();
				$this->content = $this->doc->insertStylesAndJS($this->content);
			

		}
		function getModules($js = '') {
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['hook'])) {
			   foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['hook'] as $_classRef) {
			      $_procObj = &t3lib_div::getUserObj($_classRef);
			      $_procObj->getModuleJavascript($js,$this->pageinfo['uid']);
			   }
			}
			return $js;
		}
		function getAPI() {
			$API = array();
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['hook'])) {
			   foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['hook'] as $_classRef) {
			      $_procObj = &t3lib_div::getUserObj($_classRef);
			      $API[] =$_procObj->getAPI($js);
			   }
			}
			return $API;
		}
		/**
		 * Prints out the module HTML
		 *
		 * @return	void
		 */
		function printContent()	{
			#$this->content.=$this->doc->endPage();
			echo $this->content;
		}

		/**
		 * Create the panel of buttons for submitting the form or otherwise perform operations.
		 *
		 * @return	array		all available buttons as an assoc. array
		 */
		protected function getButtons()	{

			$buttons = array(
				'csh' => '',
				'shortcut' => '',
				'save' => ''
			);
				// CSH
			$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

				// SAVE button
			#$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';


				// Shortcut
			if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
				$buttons['shortcut'] = $this->doc->makeShortcutIcon('tt', 'function', $this->MCONF['name']);
			}

			return $buttons;
		}
	}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_piwikintegration_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
