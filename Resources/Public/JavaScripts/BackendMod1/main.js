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
 * backendviewport
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
/*******************************************************************************
 * Register Namespace
 * Initialize some vars 
 ******************************************************************************/ 	
	Ext.ns('TYPO3.Sitemgr.App');                           //main Application
	Ext.ns('TYPO3.Sitemgr.Components');	                   //extended components
	Ext.ns('TYPO3.Sitemgr.AdditionalApplicationItems');	   //additional tabs
	Ext.ns('TYPO3.Sitemgr.AdditionalWindows');	           //additional windows
	TYPO3.Sitemgr.AdditionalApplicationItems = [];         //init ns
	TYPO3.Sitemgr.AdditionalWindows          = [];         //init ns
/*******************************************************************************
 * created modified components
 ******************************************************************************/
	TYPO3.Sitemgr.Components.PagedGrid = Ext.extend(Ext.grid.GridPanel, {
		constructor: function(config) {
			config = Ext.apply({
				stripeRows:true,
				border:false,
	            bbar:new Ext.PagingToolbar({
					store: config.store,
					displayInfo: true,
					pageSize: config.store.baseParams.limit,
					prependButtons: true
				}),
				listeners:{
					bodyresize:{
						scope:this,
						fn:function() {
							this.getStore().baseParams.limit = Math.floor(this.getInnerHeight()/24)-1;
							this.getBottomToolbar().pageSize = Math.floor(this.getInnerHeight()/24)-1;
							this.getBottomToolbar().changePage(1);
						}
					}
				}
	        }, config);
			TYPO3.Sitemgr.Components.PagedGrid.superclass.constructor.call(this, config);
		}
	});
	Ext.reg('pagedgrid',TYPO3.Sitemgr.Components.PagedGrid);

/*******************************************************************************
 * initialization script - executed, after dom ready
 ******************************************************************************/
	Ext.onReady(function (){		
		/*Ext.state.Manager.setProvider(new TYPO3.state.ExtDirectProvider({
			key: 'moduleData.tools_sitemgr.States',
			autoRead: false
		}));
		if (Ext.isObject(TYPO3.settings.EM.States)) {
			Ext.state.Manager.getProvider().initState(TYPO3.settings.EM.States);
		}*/
		Ext.QuickTips.init();
		Ext.Direct.on('event',function(e,provider) {
			if(e.result) {
				if(e.result.errorMessage) {
					Ext.Msg.alert('',e.result.errorMessage);
				}
			} else {
				if(e.type == 'exception') {
					Ext.Msg.alert('Server Exception:','<div style="width:100px;overflow:auto">'+e.xhr.responseText+'</div>');
				}
			}
		});
		//var loadingMask = new Ext.LoadMask(Ext.getBody());
		var Sitemgr = new TYPO3.Sitemgr.App.init();
	});
/*******************************************************************************
 * Application object
 ******************************************************************************/
	TYPO3.Sitemgr.App = {
		init: function() {
			this.sitemgrViewport = new Ext.Viewport({
				layout:'border',
				renderTo:Ext.getBody(),
				defaults:{
					padding:0,
					autoScroll:true
				},
				items:[{
					region:'north',
					xtype:'panel',
					contentEl:'typo3-docheader',
					height:50,
					border:false
				},{
					title:'Blub',
					id:'Sitemgr_App_Tabs',
					region:'center',
					xtype:'tabpanel',
					activeTab: 0,
					border:false,
					items:TYPO3.Sitemgr.AdditionalApplicationItems
				},{
					region:'south',
					height:15,
					border:false,
					bbar:[
						{
							xtype:'panel',
							html:'Customer: <b>'+TYPO3.settings.sitemgr.customerName+'</b> [<b>'+TYPO3.settings.sitemgr.customerId+'</b>]'
						},'->',{
							xtype:'panel',
							html:'<a onClick="window.open(\'http://www.sn.schule.de\');">Sponsored by SBS</a>'
						},'-',{
							xtype:'panel',
							html:'<a onClick="window.open(\'http://www.kay-strobach.de\');">&copy;KS</a>'
						},'-',{
							xtype:'panel',
							html:'<a onClick="window.open(\'http://typo3.org/extensions/repository/view/sitemgr/current/\');">Powered by sitemgr Version '+TYPO3.settings.sitemgr.version+'</a>'
						}	
					]
				}]
			});
		}
	};