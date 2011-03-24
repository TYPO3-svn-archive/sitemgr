<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_Sitemgr_Modules_Customer_CustomerController extends Tx_Sitemgr_Modules_Abstract_AbstractController{
	protected $file = __FILE__;
	function getModuleJavaScript(&$js,$uid) {
		$extConfig       = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ks_sitemgr']);
		$customerPidPage = $GLOBALS["BE_USER"]->getTSConfig(
		  	'mod.web_txkssitemgrM1.customerPidPage',
			t3lib_BEfunc::getPagesTSconfig($uid)
		);
		if((!$extConfig['customerPidPageTS']) || ($extConfig['customerPidPageTS'] && $uid == $customerPidPage['value'] && $uid!=0))
		$js.= $this->getModuleJavaScriptHelper(
			array(),
			$uid
		);
	}
	function getCustomers($args) {
		$rows  =  array_values($GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				'uid,pid,title',
				'tx_kssitemgr_customer',
				'deleted=0',
				'',
				$args['sort'].' '.$args['dir'],
				$args['start'].','.$args['stop']));
		$count =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				'count(*) as count',
				'tx_kssitemgr_customer',
				'deleted=0'
				);
		foreach($rows as $i=>$row) {
			try {
				$customer = new tx_ks_sitemgr_customer($rows[$i]['uid']);
				$customer->init();
				$users = array();
				foreach($customer->getAllUsers() as $user) {
					$users[] = $user['username'];
				}
				$rows[$i]['users'] = implode(', ',$users);
			} catch(Exception $e) {
				$rows[$i]['users'] = '-';
			}
		}				
		return array(
			'rows'  => $rows,
			'count' => $count[0]['count']
		);
	}
	function addCustomer($arg) {
		$this->loadLangFileIntoArray();
		/**
		 * check if customer already exists
		 */		 
			$t = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				'*',
				'tx_kssitemgr_customer',
				'title='.$GLOBALS['TYPO3_DB']->fullQuoteStr($arg['customerName']).' AND deleted=0',
				'',
				'');
			if(count($t)>0) {
				$this->addErrorForForm(
					'customerName',
					$GLOBALS['LANG']->getLL('error.customerName.alreadyTaken')
				);
			}
			$t = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				'*',
				'be_users',
				'username='.$GLOBALS['TYPO3_DB']->fullQuoteStr($arg['customerName']).' AND deleted=0',
				'',
				'');
				
			if(count($t)>0) {
				$this->addErrorForForm(
					'customerName',
					$GLOBALS['LANG']->getLL('error.customerName.conflictBeUser')
				);
			}
		/**
		 * check email
		 */		 
			/*$t = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				'*',
				'be_users',
				'email='.$GLOBALS['TYPO3_DB']->fullQuoteStr($arg['customerEmail']).' AND deleted=0',
				'',
				'');
			if(count($t)>0) {
				$this->addErrorForForm(
					'customerEmail',
					$GLOBALS['LANG']->getLL('error.customerEmail.alreadyTaken')
				);
			}*/
		/**
		 * return form control 
		 */		 		
			$r = $this->getReturnForForm();
			if($r['success']) {
				$this->addCustomerCreate($arg);
			}
			return $this->getReturnForForm();;
	}
	function deleteCustomer($uid) {
		//pre
			$customer = new tx_ks_sitemgr_customer($uid);
			$pid      = $customer->getPage();
		//fetch related be users
			$users = array();
			foreach($customer->getAllUsersUids() as $user) {
				$users[$user] = array('delete' => 1);
			}
		//fetch releated groups
			$groups = array();
			$t_groups = $customer->getGroups();
			$t_groups = explode(',',$t_groups);
			foreach($t_groups as $group) {
				$groups[$group] = array('delete' => 1);
			}
		//process data	
			$tcemain = t3lib_div::makeInstance('t3lib_TCEmain');
			$tcemain->deleteTree = true;
			$cmd     = array(
				//drop page
				'pages' => array(
					$pid => array(
						'delete' => 1
					),
				),
				'be_users'  => $users,
				'be_groups' => $groups
				//drop *users*!!!
				//drop group
			);
			$tcemain->start($data,$cmd);
			$tcemain->process_cmdmap();
		//drop file
	}
	protected function addCustomerCreate($arg) {
		/***********************************************************************
		 * fetch needed options		 
		 */
	 		$tgroup = $GLOBALS["BE_USER"]->getTSConfig(
			  	'mod.web_txkssitemgrM1.createUser.group',
				t3lib_BEfunc::getPagesTSconfig($arg['uid'])
			);
		/***********************************************************************
		 * create first step records
		 */		 		
			//------------------------------------------------------------------
			//be_groups & be_users
			$data = array(
				//create page
				'pages' => array(
					'NEW11' => array(
						'pid'                   => $arg['uid'],
						'doktype'               => 4,
						'title'                 => $arg['customerName'],
						'nav_title'             => $arg['customerName'],
						'description'           => $arg['description'],
						'hidden'                => 0,
						'shortcut_mode'         => 1,
						'alias'                 => $arg['customerName'],
						'editlock'              => 1,
					),
					//create dummy page
					'NEW13' => array(
						'pid'                   => 'NEW11',
						'hidden'                => '0',
						'title'                 => 'Start'
					),
				),
				//create group
				'be_groups' => array (
					'NEW41' => array (
						'pid'                    => 0,
						'title'                  => 'E: '.$arg['customerName'],
						'hidden'                 => 0,
						'subgroup'               => $tgroup['value'], //add to group redakteure //needs to be changed, to be dynamic.
						'db_mountpoints'         => 'NEW11',
					),
				),
				//create user
				'be_users' => array (
					'NEW31' => array(
						'pid'                    => 0,
						'username'               => $arg['customerName'],
						'realName'               => $arg['customerName'].'-admin',
						'email'                  => $arg['customerEmail'],
						'password'               => $arg['password'],
						'usergroup'              => 'NEW41',
						'fileoper_perms'		 => 15,
						'lang'                   => $GLOBALS['BE_USER']->uc['lang'],  // set this user lang as default language for the new user 
						'options'                => 2,
						'db_mountpoints'         => 'NEW11',
					),
				),
				
				
			);
			$tcemain = t3lib_div::makeInstance('t3lib_TCEmain');
			$tcemain->start($data,array());
			$tcemain->process_datamap();
			$groupId = $tcemain->substNEWwithIDs['NEW41'];
			$userId = $tcemain->substNEWwithIDs['NEW31'];
			$pageId = $tcemain->substNEWwithIDs['NEW11'];
			unset($tgroup);
		/***********************************************************************
		 * create second step records
		 */	
			$data = array(
				//create template
				'sys_template' => array(
					'NEW21' => array(
						'pid'                    => $pageId,
						'constants'              => '######################################################################'."\n".
													'# EXT:ks_sitemgr'."\n".
													'# createdate: '.date('r')."\n".
													'# userfolder: '.$TYPO3_CONF_VARS['BE']['userHomePath'].$userId."\n".
						                            '  usr_name                       = '.$userId."\n".
													'  usr_root                       = '.$pageId."\n".
													'  plugin.tx_ks_sitemgr.username  = '.$arg['customerName']."\n".
													'  plugin.tx_ks_sitemgr.useremail = '.$arg['customerEmail']."\n".
													'  plugin.tx_ks_sitemgr.userId    = '.$userId."\n".
													'  plugin.tx_ks_sitemgr.rootPage  = '.$pageId."\n".
													'######################################################################'."\n",
						'sitetitle'              => $arg['customerName'],
						'title'                  => 'template for ext:ks_sitemgr, contains username const. only',
					),
				),
				//create customer
				'tx_kssitemgr_customer' => array(
					'NEW61' => array(
						'pid'                    => $pageId,
						'title'                  => $arg['customerName'],
						'main_be_user'           => $userId,
						'be_groups'              => $groupId,
					),
				),
				//create acl
				'tx_beacl_acl' => array(
					'NEW51' => array(
						'pid'                    => $pageId,
						'type'                   => 0,
						'object_id'              => $userId,
						'cruser_id'              => $userId,   //set creator to owner
						'permissions'            => 27,       //do not delete rootpage, but allow all other things
						'recursive'              => 0,
					),
					'NEW52' => array(
						'pid'                    => $pageId,
						'type'                   => 0,
						'object_id'              => $userId,   //allow all for subpages
						'cruser_id'              => $userId,   //set creator to owner
						'permissions'            => 31,
						'recursive'              => 1,
					),
				),
				//modify be user
				'be_users' => array (
					$userId => array(
						'db_mountpoints'         => $pageId,
						'password'               => $arg['password'],
					),
				),
			);
			
			$tcemain = t3lib_div::makeInstance('t3lib_TCEmain');
			$tcemain->start($data,array());
			$tcemain->process_datamap();
		/***********************************************************************
		 * Fix problem with updating password
		 */		 		
			$data['be_users'][$userId]['password'] = md5($arg['password']);
			$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'be_users',
				'uid='.$userId,
				$data['be_users'][$userId]
			);
		/***********************************************************************
		 * create user and group folder
		 */		 		
			if(t3lib_div::isAllowedAbsPath($GLOBALS['TYPO3_CONF_VARS']['BE']['groupHomePath'])) {	
				t3lib_div::mkdir($GLOBALS['TYPO3_CONF_VARS']['BE']['groupHomePath'].$groupId);
			}
			// user folder
			if(t3lib_div::isAllowedAbsPath($GLOBALS['TYPO3_CONF_VARS']['BE']['userHomePath'])) {
				t3lib_div::mkdir($GLOBALS['TYPO3_CONF_VARS']['BE']['userHomePath'].$userId);
			}
		/***********************************************************************
		 * clear cache
		 */
			if($arg['copyCheck']=='on') {
				$tcemain = t3lib_div::makeInstance('t3lib_TCEmain');
				$tcemain->copyTree = 99;
				$tcemain->copyWhichTables = '*';
				$cmd     = array(
					'pages' => array(
						$arg['customerCopyFrom'] => array(
							'copy' => $pageId
						),
					),
				);
				$tcemain->start(array(),$cmd);
				$tcemain->process_cmdmap();
			}
		/***********************************************************************
		 * clear cache
		 */
		 	$tcemain->clear_cacheCmd('pages');	
	}
}