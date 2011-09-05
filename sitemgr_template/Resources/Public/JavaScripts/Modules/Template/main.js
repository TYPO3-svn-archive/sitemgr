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

/*******************************************************************************
 * Register Namespace
 * Initialize some vars 
 ******************************************************************************/ 	
	Ext.ns('TYPO3.Sitemgr.TemplateApp');

/*******************************************************************************
 * Application object
 ******************************************************************************/
	Ext.onReady(function (){
		TYPO3.Sitemgr.TemplateApp.init();
	});
	
	TYPO3.Sitemgr.TemplateApp = {
		tpl: new Ext.XTemplate(
			'<tpl for=".">',
				'<div class="template-item-wrap" id="structure{uid}">',
						'<div class="thumb">',
						'<img src="{icon}">',
						'<small>{title}</small>',
					'</div>',
				'</div>',
				'</tpl>',
			'<div class="x-clear"></div>'
		),
		init: function() {
			this.tab = Ext.getCmp('Sitemgr_App_Tabs').add({
				title:TYPO3.lang.SitemgrTemplates_title,
				layout:'vbox',
				disabled:!TYPO3.settings.sitemgr.customerSelected,
				layoutConfig: {
					//padding:'5',
					border:false,
					align:'stretch'
				},
				defaults: {
					flex:1
				},
				items:[
					{
						autoScroll:true,
						items: [
							{
								xtype:'dataview',
								loadingText: TYPO3.lang.SitemgrTemplates_loading,
								emptyText: TYPO3.lang.SitemgrTemplates_norecords,
								id:'templateSelector',
								selectedClass:'template-item-selected',
								itemSelector:'div.template-item-wrap',
								store:new Ext.data.DirectStore({
									storeId:'templateStructureStore',
									autoLoad:true,
									directFn:TYPO3.sitemgr.tabs.dispatch,
									paramsAsHash: false,
									paramOrder:'module,fn,args',
									baseParams:{
										module:'sitemgr_template',
										fn    :'getTemplates',
										args  :''
									},
									idProperty: 'uid',
									fields: [
										'id',
										'icon',
										'screens',
										'description',
										'title',
										'copyright',
										'version'
									],
								}),
								tpl: this.tpl,
								singleSelect:true,
								listeners: {
									selectionchange: {
										scope:this,
										fn: function(dv,nodes){
											var record    =  dv.getSelectedRecords()[0].data;
											Ext.Msg.wait(
												'<h3>' + record.title + '</h3>'
												+ '<center><img src="' + record.icon + '"></center>'
												+ '<small>' + record.description + '</small>'
												,
												TYPO3.lang.SitemgrTemplates_loadingForm
											);
											TYPO3.sitemgr.tabs.dispatch(
												'sitemgr_template',
												'getTemplateOptions',
												record,
												function(provider,response) {
													Ext.Msg.hide();
													var formItems = [
														{
															title: TYPO3.lang.SitemgrTemplates_templateProperties,
															xtype: 'panel',
															html: record.title + '<p><br>test</p>'
																   + '<p><img src="' + record.icon + '" height="200"></p>'
														},{
															title: 'second',
															html : '<p>Lorem ipsum dolor sit amet consectetuer urna aliquet lorem fermentum Aliquam. Leo lacus facilisi Sed et Quisque risus Vivamus eget In Fusce. Sit felis amet consequat justo hendrerit sem tristique egestas et leo. Maecenas faucibus semper ut sit leo ipsum quis a.</p>'
														}
													];
													formItems = formItems.concat(response.result.form);
													var win = new Ext.Window({
														title:TYPO3.lang.SitemgrTemplates_settings,
														id   :'templateWindow',
														modal:true,
														closeAction: 'close',
														border:false,
														height:400,
														width:550,
														resizeable:false,
														layout: 'fit',
														bbar: [
															'->',
															{
																text:TYPO3.lang.SitemgrBeUser_action_saveRight,
																iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
																handler:function() {
																	form = Ext.getCmp('templateWindow').get(0).getForm();
																	form.submit({
																		waitMsg: TYPO3.lang.SitemgrBeUser_action_addRight,
																		params: {
																			module:'sitemgr_template',
																			fn    :'setTemplateAndOptions',
																			args  :TYPO3.settings.sitemgr.uid
																		},
																		success: function(f,a){
																			Ext.getCmp('templateWindow').close();
																			Ext.getCmp('templateSelector').getStore().reload();
																		},
																	});
																}
															}
														],
														items:[
															{
																xtype:'form',
																id:'templateForm',
																api:{
																	submit:TYPO3.sitemgr.tabs.handleForm
																},
																border:false,
																paramOrder: 'module,fn,args',
																fileUpload:true,
																items: [
																	{
																		border: false,
																		activeTab:0,
																		xtype: 'tabpanel',
																		anchor:'100% 100%',
																		deferredRender:false,
																		defaults: {
																			autoScroll: true
																		},
																		enableTabScroll: true,
																		items: formItems
																	}
																]
															}
														]
													});
													win.show();
												}
											);
										}
									}
								}
							}
						]
					}
				]
			});
		}
	};