<?php 
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

class Tx_Sitemgr_Modules_BeUser_BeUserController extends Tx_Sitemgr_Modules_Abstract_AbstractController{
	protected $file = __FILE__;
	protected $access = array(
		'general' => 'customerAdmin'
	);
	function getModuleJavaScript(&$js,$uid) {
		$js.= $this->getModuleJavaScriptHelper(
			array(
				'title' => 'User',
			),
			$uid
		);
	}
	function getUsers($args) {
		try{
			$customer = new Tx_Sitemgr_Utilities_CustomerUtilities();
			$customer->getCustomerForPage($args['args']);
			$uids     = $customer->getAllUsersUids();
			$name     = $customer->getName();
			
			$rows  =  array_values($GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
					'uid,username,realname,email,admin',
					'be_users',
					'deleted=0 AND uid IN ('.implode(',',$uids).')',
					'',
					$args['sort'].' '.$args['dir'],
					$args['start'].','.$args['stop']));
			$count =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
					'count(*) as count',
					'be_users',
					'deleted=0 AND uid IN ('.implode(',',$uids).')'
					);

			foreach($rows as $k=>$val) {
				$rows[$k]['customerName']=$name;
			}
			
		} catch(Exception $e) {
			if($GLOBALS['BE_USER']->user['admin']==1) {
				$rows  =  array_values($GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
						'uid,username,realname,email,admin',
						'be_users',
						'deleted=0',
						'',
						$args['sort'].' '.$args['dir'],
						$args['start'].','.$args['stop']));
				$count =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
						'count(*) as count',
						'be_users',
						'deleted=0'
						); 
			} else {
				return array(
					'count' => 0
				);
			}
		}
		foreach($rows as $k=>$val) {
			$customer = new Tx_Sitemgr_Utilities_CustomerUtilities();
			$rows[$k]['customerName']=$customer->getCustomerForUserAsString($val['uid']);
		}
		return array(
				'count' => $count[0]['count'],
				'rows'  => array_values($rows),
			);
	}
	function getUser($arg) {
		if($arg->uid == 0) {
			return array(
				'success' => true,
				'data'    => array(
					'uid' => '0',
					'username' => '',
					'password' => '',
					'realName' => '',
					'email'    => '',
				),
			);
		} elseif($GLOBALS['BE_USER']->user['admin']) {
			$user =  array(
				'success' =>true,
				'data'    => t3lib_BEfunc::getRecord(
					'be_users',
					$arg->uid
				),
			);
			$user['data']['password'] = '';
			return $user;
		} else {
			$customer = new Tx_Sitemgr_Utilities_CustomerUtilities($arg->cid);
			$customer->init();
			if($customer->isAllowedToModifyUser($arg->uid)) {
				$user = array(
					'success' =>true,
					'data'    => t3lib_BEfunc::getRecord(
						'be_users',
						$arg->uid
					),
				);
				$user['data']['password'] = '';
				return $user;
			} else {
				if($arg->uid == $GLOBALS['BE_USER']->user['uid']) {
					return array(
						'success' => false,
						'errorMessage' => $GLOBALS['LANG']->getLL('error.accessDeniedToYourself'),
					);
				} else {
					return array(
						'success' => false,
						'errorMessage' => $GLOBALS['LANG']->getLL('error.accessDenied'),
					);
				}
			}
		}
		return array(
			'success' => true,
			'data'    => array(
				'realName' => 'test',
				'username' => 'test'
			),
		);
	}
	function deleteUser($arg) {
		list($uid,$cid) = explode(':',$arg);
		$customer = new Tx_Sitemgr_Utilities_CustomerUtilities($cid);
		$customer->init();
		if(!$customer->isAllowedToModifyUser($uid)) {
			return array(
				'success' => false,
				'errorMessage' => 'Access denied uid'.$uid.' cid'.$cid
			);
		} else {
			$erg = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'be_users',
				'uid='.intval($uid)
			);
			$erg = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_beacl_acl',
				'type=0 AND object_id='.intval($uid)
			);
			return array(
				'success' => false,
				'errorMessage' => 'Success'
			);
		}
		return array('errorMessage' => $arg);
	}
	function addOrUpdateUser($arg) {
		//check access
			if($arg['cid']) {
				$customer = new Tx_Sitemgr_Utilities_CustomerUtilities($arg['cid']);
				$customer->init();
				if($arg['uid']) {
					if(!$customer->isAllowedToModifyUser($arg['uid'])) {
						return array(
							'success' => false,
							'errorMessage' => 'Access denied uid'.$arg['uid'].' cid'.$arg['cid']
						);
					}
				}
			} else {
				return array(
					'success' => false,
					'errorMessage' => 'Need to select customer'
				);
			}
		//check duplicates
			$users = t3lib_BEfunc::getRecordsByField('be_users','username',$arg['username']);
			if(count($users)!=0) {
				if($users[0]['uid']!=$arg['uid']) {
					$this->addErrorForForm(
						'username',
						$GLOBALS['LANG']->getLL('error.username.duplicate')
					);
					return $this->getReturnForForm();
				}
			}
		//check wether prefix should be forced
			$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ks_sitemgr']);
			if($extConfig['forceBeUserPrefix']){
				if((strlen($arg['username'])<strlen($customer->getName())) && !(substr($arg['username'],0,strlen($customer->getName()))==$customer->getName())) {
					$this->addErrorForForm(
							'username',
							$GLOBALS['LANG']->getLL('error.username.prefixmissing'),
							$customer->getName().'-'.$arg['username']
						);
					return $this->getReturnForForm();
				}
			}
		// create user
			$user = new Tx_Sitemgr_Utilities_CustomerUtilities(null);
			$customer->getCustomerForPage($this->id);
			$dbFields = array(
					'username'       => $arg['username'],
					'realName'       => $arg['realName'],
					'email'          => $arg['email'],
					'password'       => md5($arg['password']),
					'disable'        => $arg['disable'] ? 1 : 0,
					'lang'           => $GLOBALS['BE_USER']->uc['lang'],  // set this user lang as default language for the new user
					'options'        => 2,
					'fileoper_perms' => 15,
			);
			if($arg['password']=='') {
				unset($dbFields['password']);
			}
			if($arg['uid']==0) {
				//create user
				$customer->init();
				$dbFields['usergroup'] = $customer->getGroups();
				$erg = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'be_users',
					$dbFields
				);
				if($arg['password']=='') {
					$this->addErrorForForm(
						'password',
						$GLOBALS['LANG']->getLL('error.password.required')
					);
					return $this->getReturnForForm();
				} else {
					$customer->addUserById($GLOBALS['TYPO3_DB']->sql_insert_id());
				}
			} else {
				//update user
				$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'be_users',
					'uid='.$arg['uid'],
					$dbFields
				);
			}
			if(t3lib_div::isAllowedAbsPath($GLOBALS['TYPO3_CONF_VARS']['BE']['userHomePath'])) {
				t3lib_div::mkdir($GLOBALS['TYPO3_CONF_VARS']['BE']['userHomePath'].intval($arg['uid']));
			}
		// return form
			return $this->getReturnForForm();
	}
	function getAccessForUser($uid) {
		$user = t3lib_BEfunc::getRecord(
			'be_users',
			intval($uid)
		); 
		$grants   = t3lib_BEfunc::getRecordsByField(
			'tx_beacl_acl',
			'object_id',
			$uid,
			'AND type="0"',
			'pid'
		);
		$return = array();
		foreach($grants as $grant) {
			$path     = t3lib_BEfunc::getRecordPath(
				$grant['pid']
			);
			$return[] = array(
				'username' => $user['username'],
				'path'     => $path,
				'uid'      => $grant['uid'],
				'right'    => 'R/W',
				'pid'      => $grant['pid'],
			);
		}
		return $return;
	}
	/**
	 * potential security risk, if not check if user is customer admin	
	 * root node of a customer admin should not be removed!	 
	 * @todo
	 */	 	
	function deleteGrant($args) {
		//check uid
			if($GLOBALS['BE_USER']->user['uid'] == $args->user) {
				return array(
					'errorMessage' => 'Access denied',
				);
			}
		//drop acls
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_beacl_acl',
				'pid='.intval($args->pid).' AND object_id='.intval($args->user).' AND type=0'
			);
		//drop mountpoints
			$user = t3lib_BEfunc::getRecord(
				'be_users',
				$args->user
			);
			$user['db_mountpoints'] = t3lib_div::rmFromList($args->pid,$user['db_mountpoints']);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'be_users',
				'uid='.intval($args->user),
				$user
			);
		
	}
	/**
	 * @todo
	 * add check, if pages is into the same customer	 
	 */	 	
	function addGrant($args) {
		// check selection
			if(intval($args['grantPid'])!=$args['grantPid']) {
				return array(
					'errorMessage' => 'No PID selected'
				);
			}
		//add mountpoint
			$user = t3lib_BEfunc::getRecord(
				'be_users',
				$args['userID']
			);
			$user['db_mountpoints'] = t3lib_div::uniqueList($user['db_mountpoints'].','.intval($args['grantPid']));
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'be_users',
				'uid='.intval($args['userID']),
				$user
			);
		//add grants
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_beacl_acl',
				array(
					'pid'         => $args['grantPid'],
					'cruser_id'   => $GLOBALS['BE_USER']->user['uid'],
					'type'        => 0, //user
					'object_id'   => $args['userID'],
					'permissions' => 27,
					'recursive'   => 0
				)
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_beacl_acl',
				array(
					'pid'         => $args['grantPid'],
					'cruser_id'   => $GLOBALS['BE_USER']->user['uid'],
					'type'        => 0, //user
					'object_id'   => $args['userID'],
					'permissions' => 31,
					'recursive'   => 1
				)
			);
		/*ob_start();
		print_r($args);
		$buffer = ob_get_contents();
		ob_end_clean();
		return array(
			'errorMessage' => $buffer
		);*/
		return array(
			'success' => true
		);
	}
	function getUsersRights($cid) {
		include_once(t3lib_extMgm::extPath('ks_sitemgr').'tabs/beuser/class.tx_ks_sitemgr_tab_beuser.php');
		$beUser       = new tx_ks_sitemgr_tab_beuser();
		$customer     = new Tx_Sitemgr_Utilities_CustomerUtilities($cid);
		$customer->init();
		$users        = $customer->getAllUsers();
		//$return[]     = t3lib_BEfunc::getRecord('pages', $customer->getPage());
		$this->rights = array();
		foreach($users as $user) {
			$rights = $beUser->getAccessForUser($user['uid']);
			foreach($rights as $right) {
				$this->rights[$right['pid']][$user['username']] = 1;
			}
		}
		$return       = $this->getPages($customer->getPage(),$customer->getName().'/');
		//$return       = $this->getPages($customer->getPage(),'&nbsp;&nbsp;');
		return array(
			'success' =>true,
			//'errorMessage' => $this->debug($this->rights),
			'rows' => array_values($return)
		);
	}
	protected function getPages($uid,$prefix='') {
		$pages  = t3lib_BEfunc::getRecordsByField ('pages', 'pid', $uid);
		$buffer = array();
		foreach($pages as $page) {
			$buffer[$page['uid']] = array(
					'id'  => $page['uid'],
					'title' => $prefix.$page['title'],
				); 
			
			if($this->rights[$page['pid']]) {
				if(!$this->rights[$page['uid']]) {
					$this->rights[$page['uid']] = array();
				}
				$this->rights[$page['uid']] = array_merge(
					$this->rights[$page['pid']],
					$this->rights[$page['uid']]
				);
			}

			if($this->rights[$page['uid']]) {
				$buffer[$page['uid']] = array_merge(
					$buffer[$page['uid']],
					$this->rights[$page['uid']]
				);
			}
			
			
			$buffer = array_merge(
				$buffer,
				$this->getPages(
					$page['uid'],
					$prefix.$page['title'].'/'
				)
			);
			
			/*$buffer = array_merge(
				$buffer,
				$this->getPages(
					$page['uid'],
					$prefix.'&nbsp;&nbsp;'
				)
			);*/
		}
		return $buffer;
	}
}