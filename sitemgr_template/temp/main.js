	Ext.onReady(function (){
		var tpl = new Ext.XTemplate(
				'<tpl for=".">',
		            '<div class="item-wrap" id="structure{uid}" style="width:180px;height:180px;float:left;padding:2x;">',
		            	'<small style="text-align:right;">{datastructure}</small>',
						'<div class="thumb" style="text-align:center;padding:10px;height:100px;">{previewicon}</div>',
						'<h3 style="text-align:center">{title}</h3>',
				    	//'<!--<span>{description}</span>-->',
				    	
					'</div>',
		        '</tpl>',
		        '<div class="x-clear"></div>'
			);
		
		Ext.getCmp('Sitemgr_App_Tabs').add({
			title:'###LANG.title###',
			layout:'hbox',
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
					title:'###LANG.structure.title###',
					autoScroll:true,
					tbar:[
						{
							text:'###LANG.action.getDescriptionAndHints###',
							iconCls:'typo3-csh-icon',
							handler:function() {
								if(Ext.getCmp('templateStructure').getSelectedRecords().length>0) {
									desc = Ext.getCmp('templateStructure').getSelectedRecords()[0].data.description;
									if(desc.substring(0,7)=='http://' || desc.substring(0,8)=='https://') {
										window.open(desc);
									} else {
										win = new Ext.Window({
											title:'###LANG.action.getDescriptionAndHints###',
											closeAction:'destroy',
											layout:'fit',
							                width:500,
							                height:300,
							                modal:true,
											items:[
												{
													html:desc
												}
											]
										});
										win.show();
									}
								}
							}
						}
					],
					items: [
						{
							xtype:'dataview',
							loadingText:'###LANG.updateData###',
							id:'templateStructure',
							selectedClass:'selected',
							itemSelector:'div.item-wrap',
							store:new Ext.data.DirectStore({
								storeId:'templateStructureStore',
								autoLoad:true,
								directFn:TYPO3.sitemgr.tabs.dispatch,
								paramsAsHash: false,
								paramOrder:'module,fn,args',
								baseParams:{
									module:'sitemgr_template',
									fn    :'getStructures',
									args  :''
								},
								idProperty: 'uid',
								fields: [{
							        name: 'uid',
							        type: 'int'
							    },
							        'title',
							        'previewicon',
							        'description',
							        'datastructure'
							    ],
							}),
							tpl: tpl,
							singleSelect:true,
							emptyText: '###LANG.emptytext###',
							listeners: {
				            	selectionchange: {
				            		scope:this,
									fn: function(dv,nodes){
				            			record =  dv.getSelectedRecords()[0].data.uid;
				            			Ext.getCmp('templateColorTheme').enable();
										Ext.getCmp('templateColorTheme').get(0).getStore().load({
											params:{
												args:record
											}
										});
				            		}
				            	}
				            }
						}
					]
				},{
					title:'###LANG.colorTheme.title###',
					id:'templateColorTheme',
					disabled:true,
					layoutConfig: {
						padding:'0',
						align:'stretch'
					},
					defaults: {
						flex:1,
						border:false
					},
					tbar:new Ext.Toolbar([
						{
							text:'&nbsp;',
							disabled:true
						}
					]),
					autoScroll:true,
					items: [
						{
							xtype:'dataview',
							loadingText:'###LANG.colorTheme.title###',
							selectedClass:'selected',
							itemSelector:'div.item-wrap',
							store:new Ext.data.DirectStore({
								storeId:'templateColorThemeStore',
								autoLoad:false,
								directFn:TYPO3.sitemgr.tabs.dispatch,
								paramsAsHash: false,
								paramOrder:'module,fn,args',
								baseParams:{
									module:'sitemgr_template',
									fn    :'getColorThemes',
									args  :''
								},
								idProperty: 'uid',
								fields: [
							    	'uid',
								    'title',
							        'previewicon',
							        'description',
							        'datastructure'
							    ],
							}),
							tpl: tpl,
							singleSelect:true,
							emptyText: '###LANG.emptytext###',
							listeners: {
				            	selectionchange: {
				            		scope:this,
									fn: function(dv,nodes){
										record =  dv.getSelectedRecords()[0].data.uid;
										loadingMask.show();
										TYPO3.sitemgr.tabs.dispatch(
											'sitemgr_template',
											'getTemplateOptions',
											record,
											function(provider,response) {
												loadingMask.hide();
												win = new Ext.Window({
													title:'###LANG.templateOptions.title###',
													id   :'templateWindow',
													modal:true,
													border:false,
													items:[
														{
															xtype:'form',
															id:'templateForm',
															autoHeight: true,
															api:{
																submit:TYPO3.sitemgr.tabs.handleForm
															},
															border:false,
															paramOrder: 'module,fn,args',
															viewConfig : {
													    		forceFit: true,
															},
															fileUpload:true,
															labelWidth:260,
															width:520,
															defaults:{
																style:'margin:5px;',
																width:510
															},
															items:[
																{
																	xtype:'displayfield',
																	html:'###LANG.field.templateHint###',
																	hideLabel:true,
																	cls:'typo3-message message-information'
																},{
																	title:'Titel',
																	xtype:'fieldset',
																	defaultType:'textfield',
																	defaults: {
																		xtype:'textfield',
																		width:200,
																		msgTarget: 'side',
																		style:'margin:5px;'
																	},
																	items:response.result.form
																}
															]
															
														}
													],
													buttons:[
														{
															text:'###LANG.action.saveTemplate###',
															iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
															handler:function() {
																Ext.getCmp('templateForm').getForm().submit(
																{
																	waitMsg: '###LANG.action.saveTemplate###',
																	params: {
																		module:'sitemgr_template',
																		fn    :'saveTemplateSettings',
																		args  :[
																			record+':'+ksSitemgrTools.customerId
																		]
																	},
																	success: function(f,a){
																		Ext.getCmp('templateWindow').hide();
																		Ext.getCmp('templateWindow').destroy();
																	}
																});
															}
														}
													],
													listeners:{
														
													}
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
	});