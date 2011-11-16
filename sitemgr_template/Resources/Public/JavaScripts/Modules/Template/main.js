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
	Ext.ComponentMgr.create = Ext.ComponentMgr.create.createInterceptor(function(config, defaultType) {
		var type = config.xtype || defaultType;
		if ( !Ext.ComponentMgr.isRegistered(type))  {
			throw 'xtype ""'+type+'"" is not a registered component';
		}
		return true;
	});
	
	/**
	  * recreate the alias 
	  */
	Ext.create  =  Ext.ComponentMgr.create;
	
	Ext.onReady(function (){
		TYPO3.Sitemgr.TemplateApp.init();
	});
	
	TYPO3.Sitemgr.TemplateApp = {
		tpl: new Ext.XTemplate(
			'<tpl for=".">',
				'<tpl if="isInUse==0"><div class="template-item-wrap" id="structure{uid}"></tpl>',
				'<tpl if="isInUse==1"><div class="template-item-wrap template-item-selected" id="structure{uid}"></tpl>',
						'<div class="thumb">',
						'<img src="{icon}">',
						'<small>{title}</small>',
					'</div>',
				'</div>',
				'</tpl>',
			'<div class="x-clear"></div>'
		),
		/**
		 * show the preview window of the given template record
		 * @param record extjs record of template
		 */
		showTemplatePreview: function(record) {
			record.uid = TYPO3.settings.sitemgr.uid
			var win = new Ext.Window({
				title:TYPO3.lang.SitemgrTemplates_templatePreview + ': ' + record.title,
				id   :'templatePreviewWindow',
				modal:true,
				closeAction: 'close',
				resizeable:false,
				layout: 'fit',
				maximized: true,
				tbar: [
					{
						tooltip:TYPO3.lang.SitemgrBeUser_action_saveRight,
						iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save',
						handler:function() {
							Ext.getCmp('templatePreviewWindow').close();
							this.showTemplateOptions(record);
						},
						scope:this
					}
				],
				items:[
					{
						layout:'fit',
						xtype: 'panel',
						autoScroll: true,
						padding:15,
						html: new Ext.XTemplate(
								'<table width="100%"><tr><td>',
									'<tpl for="copyright">',
										'<p>{nameAdditional} - {version} - {state}<p>',
										'<br></p><p>{parent.description}</p>',
										'<br><p>&copy;{license} by <a href="mailto:{authorEmail}">{authorName}</a> - {authorCompany}</small></p>',
									'</tpl>',
								'</td><td>',
									'<tpl for="screens">',
										'<img src="{.}">',
									'</tpl>',
								'</td></tr></table>'
							  ).apply(record)
					}
						// @todo process copyright info of template
				]
			});
			win.show();
		},
		showTemplateOptions: function(record) {
			Ext.Msg.wait(
				'<h3>' + record.title + '</h3>'
				+ '<center><img src="' + record.icon + '"></center>',
				TYPO3.lang.SitemgrTemplates_loadingForm
			);
			TYPO3.sitemgr.tabs.dispatch(
				'sitemgr_template',
				'setTemplateAndGetOptions',
				record,
				function(provider,response) {
					Ext.Msg.hide();
					var win = new Ext.Window({
						title:TYPO3.lang.SitemgrTemplates_settings,
						id   :'templateWindow',
						modal:true,
						closeAction: 'close',
						maximized:true,
						height:450,
						width:500,
						resizeable:false,
						layout: 'fit',
						tbar: [
							{
								tooltip:TYPO3.lang.SitemgrBeUser_action_saveRight,
								iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
								handler:function() {
									form = Ext.getCmp('templateForm').getForm();
									form.submit({
										waitMsg: TYPO3.lang.SitemgrBeUser_action_addRight,
										params: {
											module:'sitemgr_template',
											fn    :'setTemplateAndOptions',
											args  : record.id + ';' + TYPO3.settings.sitemgr.uid
										},
										success: function(f,a){
											Ext.getCmp('templateWindow').close();
											//Ext.getCmp('templateSelector').getStore().reload();
										}
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
								//fileUpload:true, //needs to be enabled for uploades!
								items: [
									{
										border: false,
										activeTab:0,
										xtype: 'tabpanel',
										anchor:'100% 100%',
										//deferredRender:false,
										defaults: {
											autoScroll: true
										},
										enableTabScroll: true,
										items: response.result.form
									}
								]
							}
						]
					});
					win.show();
				}
			);
		},
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
										args  :TYPO3.settings.sitemgr.uid
									},
									idProperty: 'uid',
									fields: [
										'id',
										'icon',
										'screens',
										'description',
										'title',
										'copyright',
										'version',
										'isInUse'
									]
								}),
								tpl: this.tpl,
								multiSelect:false,
								singleSelect:true,
								listeners: {
									selectionchange: function(dv,nodes){
										var record    =  dv.getSelectedRecords()[0].data;
										this.showTemplatePreview(record);
									},
									scope:this
								}
							}
						]
					}
				]
			});
		}
	};