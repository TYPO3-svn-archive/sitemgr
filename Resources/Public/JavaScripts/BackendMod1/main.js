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
 
//create components
pagedGrid = Ext.extend(Ext.grid.GridPanel, {
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
		pagedGrid.superclass.constructor.call(this, config);
	}
});
Ext.reg('pagedgrid',pagedGrid);

//Load Ext.Direct API ...
Ext.onReady(function (){
	Ext.QuickTips.init();
	Ext.apply(Ext.QuickTips.getQuickTip(), {
	    maxWidth: 200,
	    minWidth: 100,
	    showDelay: 50      // Show 50ms after entering target
	    //trackMouse: true
	    //qclass:'quicktips'
	});

	
	Ext.Direct.on('event',function(e,provider) {
		if(e.result) {
			if(e.result.errorMessage) {
				Ext.Msg.alert('',e.result.errorMessage);
			}
		} else {
			if(e.type == 'exception') {
				Ext.Msg.alert('Server Exception:',e.xhr.responseText);
			}
		}
		
	});
	
	var loadingMask = new Ext.LoadMask(Ext.getBody());
	//loadingMask.enable();
	//Ext.Ajax.on('beforerequest',    loadingMask.show, this);
	//Ext.Ajax.on('requestcomplete',  loadingMask.hide, this);
	//Ext.Ajax.on('requestexception', loadingMask.hide, this);
	

	var sitemgrViewport = new Ext.Viewport({
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
			id:'sitemgr_tabs',
			region:'center',
			xtype:'tabpanel',
			activeTab: 0,
			border:false,
			items:[]
		},{
			region:'south',
			height:15,
			border:false,
			bbar:[
				{
					xtype:'panel',
					html:'Customer: <b>'+ksSitemgrTools.customerName+'</b> [<b>'+ksSitemgrTools.customerId+'</b>]'
				},'->',{
					xtype:'panel',
					html:'<a onClick="window.open(\'http://www.sn.schule.de\');">Sponsored by SBS</a>'
				},'-',{
					xtype:'panel',
					html:'<a onClick="window.open(\'http://www.kay-strobach.de\');">&copy;KS</a>'
				},'-',{
					xtype:'panel',
					html:'<a onClick="window.open(\'http://typo3.org/extensions/repository/view/sitemgr/current/\');">Powered by sitemgr Version '+ksSitemgrTools.version+'</a>'
				}	
			]
		}]
	});
	//placeholder for more js
	//###AdditionalJs###
	//Acitvate first tab
	//Ext.getCmp('sitemgr_tabs').activate(0);
});