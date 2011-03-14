<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
class tx_ks_sitemgr_div {
	static function getTSConstantValue($pid,$name) {
		$template_uid = 0;
		$pageId = $pid;
		$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");	// Defined global here!
		$tmpl->tt_track = 0;	// Do not log time-performance information
		$tmpl->init();

		$tplRow = $tmpl->ext_getFirstTemplate($pageId,$template_uid);
		if (is_array($tplRow) || 1)	{	// IF there was a template...
				// Gets the rootLine
			$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
			$rootLine = $sys_page->getRootLine($pageId);
			$tmpl->runThroughTemplates($rootLine,$template_uid);	// This generates the constants/config + hierarchy info for the template.
			$theConstants = $tmpl->generateConfig_constants();	// The editable constants are returned in an array.
			$tmpl->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
			$tmpl->ext_regObjectPositions($tplRow["constants"]);		// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
		} else {
			throw new Exception('No Template found!!!');
		}
		return $tmpl->setup['constants'][$name];
	}
	static function setTSConstantValue($pid,$name,$value) {
		$template_uid = 0;
		$pageId = $pid;
		$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");	// Defined global here!
		$tmpl->tt_track = 0;	// Do not log time-performance information
		$tmpl->init();

		$pageId = self::getTSConstantValue($pageId,'usr_root');
		if(intval($pageId)===0) {
			throw new Exception('TSConstant usr_root must be defined');
		}

		$tplRow = $tmpl->ext_getFirstTemplate($pageId,$template_uid);			
		
		if (is_array($tplRow) || 1)	{
			$userStatus = $GLOBALS['BE_USER']->user['admin'];
			$GLOBALS['BE_USER']->user['admin']=1;

			$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
			$rootLine = $sys_page->getRootLine($pageId);
			$tmpl->runThroughTemplates($rootLine,$template_uid);	// This generates the constants/config + hierarchy info for the template.
			$theConstants = $tmpl->generateConfig_constants();	// The editable constants are returned in an array.
			$tmpl->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
			$tmpl->ext_regObjectPositions($tplRow["constants"]);		// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
			
			$tmpl->ext_putValueInConf($name,$value);
			
			$recData=array();
			$saveId = $tplRow['_ORIG_uid'] ? $tplRow['_ORIG_uid'] : $tplRow['uid'];
			$recData["sys_template"][$saveId]["constants"] = implode($tmpl->raw,chr(10));
			
			// Create new  tce-object
			$tce = t3lib_div::makeInstance("t3lib_TCEmain");
			$tce->stripslashes_values=0;
			// Initialize
			$tce->start($recData,Array());
			// Saved the stuff
			$tce->process_datamap();
			// Clear the cache (note: currently only admin-users can clear the cache in tce_main.php)
			$tce->clear_cacheCmd("all");
			
			$GLOBALS['BE_USER']->user['admin']=$userStatus;
		 	
		} else {
			throw new Exception('No Template found!!!');
		}
	}
	
}