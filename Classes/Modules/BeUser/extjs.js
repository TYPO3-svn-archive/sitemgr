var userForm = new Ext.Window({
	title:'###LANG.action.editUser###',
	modal:true,
	layout:'form',
	id:'newUserForm',
	closeAction :'hide',
	border:false,
	width:400,
	height:400,
	items:[
		{
			xtype:'form',
			border:false,
			api:{
				load:TYPO3.ks_sitemgr.tabs.dispatch,
				submit:TYPO3.ks_sitemgr.tabs.handleForm
			},
			defaults:{
				style:'margin:5px;',
			},
			paramOrder: 'module,fn,args',
			items:[
				{
					xtype:'fieldset',
					title:'###LANG.field.userData###',
					width:350,
					defaults: {
						width:200,
						msgTarget: 'side'
					},
					items:[
						{
							xtype:'hidden',
							value:ksSitemgrTools.uid,
							name:'uid'
						},{
							xtype:'hidden',
							value:ksSitemgrTools.customerId,
							name:'cid'
						},{
							fieldLabel: '###LANG.field.userName###',
							xtype:'textfield',
							name:'username',
							allowBlank:false
						},{
							fieldLabel: '###LANG.field.password###',
							xtype:'textfield',
							name:'password',
							emptyText:'******'
						},{
							fieldLabel: '###LANG.field.disable###',
							xtype:'checkbox',
							name:'disable'
						}
					]
				},{
					xtype:'fieldset',
					title:'###LANG.field.userAdditionalData###',
					width:350,
					defaults: {
						width:200,
						msgTarget: 'side'
					},
					items:[
						{
							fieldLabel: '###LANG.field.userRealName###',
							xtype:'textfield',
							name:'realName',
							allowBlank:false
						},{
							fieldLabel: '###LANG.field.userEmail###',
							xtype:'textfield',
							name:'email',
							vtype:'email',
							allowBlank:false
						}
					]
				},{
					xtype:'displayfield',
					html:'###LANG.field.rightsHint###',
					hideLabel:true,
					width:350,
					cls:'typo3-message message-information'
				}
			],
			success:function() {
				Ext.getCmp('newUserForm').hide();
			}
		}
	],
	buttons:[
		{
			text:'###LANG.action.saveUser###',
			iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
			handler:function() {
				form = Ext.getCmp('newUserForm').get(0).getForm();
				form.submit({
					waitMsg: '###LANG.action.newUser###',
					params: {
						module:'ks_sitemgr_beuser',
						fn    :'addOrUpdateUser',
						args  :{
							uid:ksSitemgrTools.uid,
							cid:ksSitemgrTools.customerId
						}
					},
					success: function(f,a){
						Ext.getCmp('newUserForm').hide();
						//Ext.Msg.alert('Success', 'It worked');
						Ext.getCmp('userGrid').getStore().reload();
					}
				});
			}
		}
	]
});
Ext.getCmp('ks_sitemgr_tabs').add({
	title:'###LANG.title###',
	layout:'vbox',
	layoutConfig: {
		padding:'0',
		align:'stretch'
	},
	defaults: {
		flex:1
	},
	items: [
		{
			//title:'###LANG.userGrid.title###',
			xtype:'pagedgrid',
			loadMask:true,
			flex:1.5,
			id:'userGrid',
			store:new Ext.data.DirectStore({
				storeId:'beuserStore',
				directFn:TYPO3.ks_sitemgr.tabs.dispatchPaged,
				paramsAsHash: false,
				remoteSort :true,
				paramOrder:'module,fn,args,start,limit,sort,dir',
				baseParams:{
					module:'ks_sitemgr_beuser',
					fn    :'getUsers',
					args  :ksSitemgrTools.uid,
					start: 0,
		        	limit: 10,
		        	sort:  'username',
		        	dir :  'ASC'
				},
				root: 'rows',
	    		totalProperty: 'count',
				idProperty: 'uid',
				fields: [{
			        name: 'uid',
			        type: 'int'
			    },
			        'username',
			        'realName',
			        'admin',
			        'email',
			        'customerName'
			    ]
			}),
			tbar:[
				{
					//text:'###LANG.action.newUser###',
					tooltip:'###LANG.title### ###LANG.action.newUser###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-new',
					disabled:!ksSitemgrTools.customerSelected,
					handler:function() {
						Ext.getCmp('newUserForm').get(0).getForm().load({
							waitMsg:'###LANG.action.newUser###',
							params:{
								module:'ks_sitemgr_beuser',
								fn:    'getUser',
								args  :{
									uid:'0',
									cid:ksSitemgrTools.customerId
								}
							},
							success:function() {
								Ext.getCmp('newUserForm').show();
							}
						});
					}
				},{
					//text:'###LANG.action.editUser###',
					tooltip:'###LANG.title### ###LANG.action.editUser###',
					iconCls:'t3-icon-actions t3-icon-actions-document t3-icon-document-open',
					handler:function() {
						var sm  = Ext.getCmp('userGrid').getSelectionModel();
						var sel = sm.getSelected();
						if(sm.hasSelection()) {
							if(sel.data.uid!='') {
								Ext.getCmp('newUserForm').get(0).getForm().load({
									waitMsg:'###LANG.action.editUser###',
									params:{
										module:'ks_sitemgr_beuser',
										fn:    'getUser',
										args  :{
											uid:sel.data.uid,
											cid:ksSitemgrTools.customerId
										}
									},
									success:function() {
										Ext.getCmp('newUserForm').show();
									}
								});
							}
						}
					}
				},{
					//text:'###LANG.action.deleteUser###',
					tooltip:'###LANG.title### ###LANG.action.deleteUser###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-delete',
					handler:function() {
						var sm  = Ext.getCmp('userGrid').getSelectionModel();
						var sel = sm.getSelected();
						if(sm.hasSelection()) {
							if(sel.data.uid!='') {
								Ext.Msg.show({
									title:'###LANG.action.deleteUser###?',
									msg: '###LANG.action.deleteUser###? <br> - '+sel.data.username,
									buttons: Ext.Msg.YESNO,
									fn: function(btn) {
										if(btn=='yes') {
											TYPO3.ks_sitemgr.tabs.dispatch(
												'ks_sitemgr_beuser',
												'deleteUser',
												sel.data.uid+':'+ksSitemgrTools.customerId,
												function() {
													Ext.getCmp('userGrid').getStore().reload();
												}
											);
										}
									},
									icon: Ext.MessageBox.QUESTION
								});
								
							}
						}
								
					}
				},{
					//text:'###LANG.action.usersRightsOverview###',
					tooltip:'###LANG.action.usersRightsOverview###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-pagetree-backend-user',
					disabled:!ksSitemgrTools.customerSelected,
					handler:function() {
						if(!Ext.getCmp('beUserRightsWindow')) {
							cols = new Array(
								{
									id: 'uid',
									header: '###LANG.field.pid###',
									dataIndex: 'uid',
									width:50,
									hidden:true
								},{
									id: 'title',
									header: '###LANG.field.title###',
									dataIndex:'title',
									width:400,
								}
							);
							fields  = new Array(
								'uid',
								'title'
							);
							for(i=0;i<Ext.getCmp('userGrid').getStore().getCount();i++) {
								fields.push(
									Ext.getCmp('userGrid').getStore().getAt(i).data.username
								);
								cols.push({
									header       : '<span style="height:100px;display:block;"><span style="filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);-webkit-transform:rotate(90deg);-moz-transform:rotate(90deg);display:block;">'+Ext.getCmp('userGrid').getStore().getAt(i).data.username+'</span></span>',
									dataIndex    : Ext.getCmp('userGrid').getStore().getAt(i).data.username,
									tooltip      : Ext.getCmp('userGrid').getStore().getAt(i).data.username+' - '+Ext.getCmp('userGrid').getStore().getAt(i).data.realName,
									fixed        : true,
									renderer     :  function(value,metaData) {
										if(value) {
											metaData.css = 't3-icon message-ok';
										}
										return '';
									}
								});
							}
							win = new Ext.Window({
								id        : 'beUserRightsWindow',
								title     : '###LANG.action.usersRightsOverview###',
								modal     : true,
								closeAction : 'close', 
								maximized:true,
								layout:'fit',
								layoutConfig: {
									margin: '5',
								},
								margin:10,
								items:[
									{
										id:'userrightsGrid',
										xtype:'grid',
										layout:'fit',
										loadMask:true,
										columnLines:true,
										stripeRows:true,
										border:false,
										store:new Ext.data.DirectStore({
											storeId:'beuserRightsStore',
											autoLoad:false,
											directFn:TYPO3.ks_sitemgr.tabs.dispatch,
											paramsAsHash: false,
											paramOrder:'module,fn,args',
											baseParams:{
												module:'ks_sitemgr_beuser',
												fn    :'getUsersRights',
												args  :ksSitemgrTools.customerId
											},
											root:'rows',
											idProperty: 'uid',
											fields: fields
										}),
										cm   :new Ext.grid.ColumnModel({
											defaults: {
												width   : 28,
												sortable: false,
												menuDisabled :true
											},
											columns: cols
										}),
										sm:new Ext.grid.RowSelectionModel({
											singleSelect:true
										}),
										viewConfig: {
									    	//forceFit: true
									    }
									}
								]
							});
						} else {
							win = Ext.getCmp('beUserRightsWindow');
						}
						win.show();
						Ext.getCmp('userrightsGrid').getStore().load();
						
					}
				},{
					//text:'###LANG.action.switchUser###',
					tooltip:'###LANG.action.switchUser###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-system t3-icon-system-backend-user-emulate',
					disabled:!###ADMIN?###,
					handler:function() {
						var sm  = Ext.getCmp('userGrid').getSelectionModel();
						var sel = sm.getSelected();
						if(sm.hasSelection()) {
							if(sel.data.uid!='') {
								top.location.href='mod.php?M=tools_beuser&SwitchUser='+sel.data.uid+'&switchBackUser=1';
							}
						}
					}
				}
			],
			sm: new Ext.grid.RowSelectionModel({
				singleSelect:true,
				listeners: {
					rowselect: function(sm, rowIndex, record){
						if(record.data.admin==1) {
							Ext.getCmp('userGrantsGrid').getStore().removeAll();
							Ext.getCmp('userGrantsGrid').disable();
						} else {
							Ext.getCmp('userGrantsGrid').getStore().load({
								params:{
									args:record.data.uid
								}
							});
							Ext.getCmp('userGrantsGrid').enable();
						}
					}
				}
			}),
			autoExpandColumn:'realName',
			colModel: new Ext.grid.ColumnModel({
					defaults: {
					sortable: true
				},
				columns: [
					{id: 'uid', header: 'ID', width: 200, sortable: true, dataIndex: 'uid',hidden:true},
					{header: '###LANG.grid.admin###', dataIndex: 'admin', width:30, fixed:true, renderer:function(val){
						if(val != 1) {
							return '<span class="t3-icon t3-icon-status t3-icon-status-user t3-icon-user-backend"></span>';
						} else {
							return '<span class="t3-icon t3-icon-status t3-icon-status-user t3-icon-user-admin"></span>'
						}
					}},
					{header: '###LANG.grid.username###'    , dataIndex: 'username',width:150},
					{header: '###LANG.grid.realname###'    , dataIndex: 'realName'},
					{header: '###LANG.grid.email###'       , dataIndex: 'email'},
					{header: '###LANG.grid.customerName###', dataIndex: 'customerName'}
					
				],
			}),
			viewConfig: {
				forceFit: true,
			}
		},{
			xtype:'grid',
			title:'###LANG.userAccessGrid.title###',
			loadMask:true,
			id:'userGrantsGrid',
			disabled:true,
			store:new Ext.data.DirectStore({
				storeId:'beUserAccessStore',
				autoLoad:false,
				directFn:TYPO3.ks_sitemgr.tabs.dispatch,
				paramsAsHash: false,
				paramOrder:'module,fn,args',
				baseParams:{
					module:'ks_sitemgr_beuser',
					fn    :'getAccessForUser',
					args  :ksSitemgrTools.uid
				},
				idProperty: 'uid',
				fields: [{
			        name: 'uid',
			        type: 'int'
			    },
			        'username',
			        'realName',
			        'admin',
			        'path',
			        'right',
			        'pid'
			    ]
			}),
			tbar:[
				{
					//text:'###LANG.action.addRight###',
					tooltip:'###LANG.userAccessGrid.title### ###LANG.action.addRight###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-new',
					handler:function() {
						var sm  = Ext.getCmp('userGrid').getSelectionModel();
						var sel = sm.getSelected();
						win = new Ext.Window({
							title:'###LANG.action.addRight###',
							modal:true,
							layout:'form',
							id:'addRightForm',
							width    : 400,
							items:[
								{
									xtype:'form',
									border:false,
									api:{
										submit:TYPO3.ks_sitemgr.tabs.handleForm
									},
									padding:5,
									defaults: {
										msgTarget: 'side'
									},
									items:[
										{
											xtype:'fieldset',
											title:'###LANG.action.addRight###',
											items:[
												{
													xtype:'hidden',
													value:ksSitemgrTools.uid,
													name:'uid',
													fieldLabel :'uid',
												},{
													xtype:'hidden',
													value:sel.data.uid,
													fieldLabel :'userId',
													name:'userID'
												},{
													xtype:'hidden',
													fieldLabel: '###LANG.action.addRight###',
													name:'grantPid',
													id:'grantPid',
													width: 250
												},{
													xtype:'treepanel',
													fieldLabel:'###LANG.action.addRight###',
													width: 250,
													height:300,
													autoScroll:true,
													loader: new Ext.tree.TreeLoader({
														directFn:TYPO3.ks_sitemgr.tabs.getSubpages
													}),
													root: new Ext.tree.AsyncTreeNode({
											            expanded: true,
											            id  :ksSitemgrTools.customerRootPid,
											            text:ksSitemgrTools.customerRootName,
											            leaf:false,
											            expandable:true
											        }),
											        listeners: {
											            click: function(n) {
															Ext.getCmp('grantPid').setValue(n.attributes.id);
											            }
											        }
												}
											]
										}
									],
									buttons:[
										{
											text:'###LANG.action.saveRight###',
											iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
											handler:function() {
												form = Ext.getCmp('addRightForm').get(0).getForm();
												form.submit({
													waitMsg: '###LANG.action.addRight###',
													params: {
														module:'ks_sitemgr_beuser',
														fn    :'addGrant',
														args  :ksSitemgrTools.uid
													},
													success: function(f,a){
														Ext.getCmp('addRightForm').hide();
														Ext.getCmp('addRightForm').destroy();
														Ext.getCmp('userGrantsGrid').getStore().reload();
													},
												});
											}
										}
									],
									success:function() {
										Ext.getCmp('newCustomerForm').close();
									}
								}
							]
						});
						win.show();
					}
				},{
					//text:'###LANG.action.deleteRight###',
					tooltip:'###LANG.userAccessGrid.title### ###LANG.action.deleteRight###',
					iconCls:'t3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-delete',
					handler:function() {
						var sm  = Ext.getCmp('userGrantsGrid').getSelectionModel();
						var sel = sm.getSelected();
						if(sm.hasSelection()) {
							if(sel.data.uid!='') {
								Ext.Msg.show({
									title:'###LANG.action.deleteRight###?',
									msg: '###LANG.action.deleteRight###?',
									buttons: Ext.Msg.YESNO,
									fn: function(btn) {
										if(btn=='yes') {
										TYPO3.ks_sitemgr.tabs.dispatch(
												'ks_sitemgr_beuser',
												'deleteGrant',
												{
													pid :sel.data.pid,
													user:Ext.getCmp('userGrid').getSelectionModel().getSelected().data.uid,
												},
												function() {
													Ext.getCmp('userGrantsGrid').getStore().reload();
												}
											);
										}
									},
									icon: Ext.MessageBox.QUESTION
								});
								
							}
						}
					}
				}
			],
			sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
			autoExpandColumn:'path',
			colModel: new Ext.grid.ColumnModel({
					defaults: {
					sortable: true
				},
				columns: [
					{id: 'uid', header: 'ID', width: 200, sortable: true, dataIndex: 'uid',hidden:true},
					{header: '###LANG.grid.admin###', dataIndex: 'admin', width:30, fixed:true,renderer:function(val){
						if(val != 1) {
							return '<span class="t3-icon t3-icon-status t3-icon-status-user t3-icon-user-backend"></span>';
						} else {
							return '<span class="t3-icon t3-icon-status t3-icon-status-user t3-icon-user-admin"></span>'
						}
					}},
					{header: '###LANG.grid.username###'    , dataIndex: 'username',width:150},
					{header: '###LANG.grid.path###'        , dataIndex: 'path'},
					{header: '###LANG.grid.right###'        , dataIndex: 'right',width:25}
					
				],
			}),
			viewConfig: {
				forceFit: true,
			}
		}
	]
});