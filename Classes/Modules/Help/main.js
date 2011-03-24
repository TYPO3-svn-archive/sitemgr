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
 * mod1/extjs.js
 *
 * help module
 * show jst some links to provide help for the users. 
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

	function loadHelpTabFrame(url) {
		buffer = '<iframe width="100%" height="100%" frameborder="0" src="'+url+'">';
		Ext.getCmp('SitemgrHelp').update(buffer);
	}

	Ext.onReady(function (){
		Ext.getCmp('Sitemgr_App_Tabs').add({
			title:TYPO3.lang.SitemgrHelp_Title,
			html :TYPO3.lang.SitemgrHelp_Description,
			id   :'SitemgrHelp',
			tbar :[{
				text   :'TYPO3 SBS',
				iconCls:'t3-icon-text-html',
				handler:function() {
					loadHelpTabFrame('http://cms.sn.schule.de/admin/administrative-informationen/grundlagen/');
				}
			},'-',{
				text   :'TYPO3 Videos',
				iconCls:'t3-icon-text-html',
				handler:function() {
					loadHelpTabFrame('http://typo3.org/documentation/videos/tutorials-v4-de/');
				}
			},{
				text   :'TYPO3 Reference',
				iconCls:'t3-icon-text-html',
				handler:function() {
					loadHelpTabFrame('http://typo3.org/documentation/videos/quick-reference-v4-de/');
				}
			},{
				text   :'TYPO3 Wiki',
				iconCls:'t3-icon-text-html',
				handler:function() {
					loadHelpTabFrame('http://wiki.typo3.org/Main_Page');
				}
			}]
		});
	});