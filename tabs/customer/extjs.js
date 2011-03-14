Ext.getCmp('ks_sitemgr_tabs').add({
	title:'###LANG.title###',
	xtype:'pagedgrid',
	loadMask:true,
	id:'customerGrid',
	store:new Ext.data.DirectStore({
		storeId:'customerStore',
		directFn:TYPO3.ks_sitemgr.tabs.dispatchPaged,
		paramsAsHash: false,
		remoteSort :true,
		paramOrder:'module,fn,args,start,limit,sort,dir',
		baseParams:{
			module:'ks_sitemgr_customer',
			fn    :'getCustomers',
			args  :ksSitemgrTools.uid,
			start: 0,
        	limit: 25,
        	sort:  'title',
        	dir :  'ASC'
		},
		root: 'rows',
	    totalProperty: 'count',
		idProperty: 'uid',
		fields: [{
	        name: 'uid',
	        type: 'int'
	    },
	        'title',
	        'pid',
	        'users'
	    ],
	    listeners: {
			load:function() {
				if(ksSitemgrTools.customerId!=0) {
					records = [Ext.getCmp('customerGrid').getStore().getById(ksSitemgrTools.customerId)];
					Ext.getCmp('customerGrid').getSelectionModel().selectRecords(records,false)
				}
			}
		}
	}),
	tbar:[
		{
			//text:'###LANG.action.newCustomer###',
			tooltip:'###LANG.title### ###LANG.action.newCustomer###',
			iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-new',
			handler:function() {
				win = new Ext.Window({
					//title:'###LANG.action.newCustomer###',
					modal:true,
					layout:'form',
					id:'newCustomerForm',
					border:false,
					items:[
						{
							xtype:'form',
							border:false,
							api:{
								submit:TYPO3.ks_sitemgr.tabs.handleForm
							},
							defaults:{
								style:'margin:5px;'
							},
							items:[
								{
									xtype:'fieldset',
									title:'###LANG.field.customerData###',
									width:400,
									defaults: {
										msgTarget: 'side'
									},
									items:[
										{
											xtype:'hidden',
											value:ksSitemgrTools.uid,
											name:'uid'
										},{
											fieldLabel: '###LANG.field.customerName###',
											xtype:'textfield',
											name:'customerName',
											width: 250
										},{
											fieldLabel: '###LANG.field.customerEmail###',
											xtype:'textfield',
											name:'customerEmail',
											vtype:'email',
											width: 250
										},{
											fieldLabel: '###LANG.field.password###',
											xtype:'textfield',
											name:'password',
											width: 250
										},{
											fieldLabel: '###LANG.field.description###',
											xtype:'textarea',
											name:'description',
											width: 250
										}
									]
								},{
									xtype:'fieldset',
									title:'###LANG.field.customerSettings###',
									items:[
										{
											fieldLabel: '###LANG.field.copyCheck###',
											xtype:'checkbox',
											name:'copyCheck',
											handler:function(field) {
												if(field.checked) {
													Ext.getCmp('customerCopyFrom').enable();
													Ext.getCmp('customerCopyFromTree').enable();
												} else {
													Ext.getCmp('customerCopyFrom').disable();
													Ext.getCmp('customerCopyFromTree').disable();
												}
											}
										},{
											xtype:'hidden',
											fieldLabel: '###LANG.field.copyFrom###',
											name:'customerCopyFrom',
											id:'customerCopyFrom',
											disabled:true,
											width: 250
										},{
											xtype:'treepanel',
											disabled:true,
											fieldLabel:'###LANG.field.copyFrom###',
											id:'customerCopyFromTree',
											width: 250,
											height:100,
											autoScroll:true,
											loader: new Ext.tree.TreeLoader({
												directFn:TYPO3.ks_sitemgr.tabs.getSubpages
											}),
											root: new Ext.tree.AsyncTreeNode({
									            expanded: true,
									            id:'0',
									            text:'ROOT',
									            leaf:false,
									            expandable:true
									        }),
									        listeners: {
									            click: function(n) {
													Ext.getCmp('customerCopyFrom').setValue(n.attributes.id);
									            }
									        }
										},{
											fieldLabel: '###LANG.field.createGroupFolder###',
											xtype:'checkbox',
											name:'createGroupFolder',
											checked:true
										},{
											fieldLabel: '###LANG.field.createUserFolder###',
											xtype:'checkbox',
											name:'createUserFolder',
											checked:true
										}
									]
								}
							],
							success:function() {
								Ext.getCmp('newCustomerForm').close();
							}
						}
					],
					buttons:[
						{
							text:'###LANG.action.saveCustomer###',
							iconCls:'t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-save-close',
							handler:function() {
								form = Ext.getCmp('newCustomerForm').get(0).getForm();
								form.submit({
									waitMsg: '###LANG.action.newCustomer###',
									params: {
										module:'ks_sitemgr_customer',
										fn    :'addCustomer',
										args  :ksSitemgrTools.uid
									},
									success: function(f,a){
										Ext.getCmp('newCustomerForm').hide();
										Ext.getCmp('newCustomerForm').destroy();
										//Ext.Msg.alert('Success', 'It worked');
										Ext.getCmp('customerGrid').getStore().reload();
										Ext.getCmp('userGrid').getStore().reload();
									},
								});
							}
						}
					]
				});
				win.show();
			}
		},{
			//text:'###LANG.action.editCustomer###',
			tooltip:'###LANG.title### ###LANG.action.editCustomer###',
			iconCls:'t3-icon-actions t3-icon-actions-document t3-icon-document-open',
			handler:function() {
				var sm  = Ext.getCmp('customerGrid').getSelectionModel();
				var sel = sm.getSelected();
				if(sm.hasSelection()) {
					if(sel.data.uid!='') {
						window.open('alt_doc.php?returnUrl=close.html&edit[tx_kssitemgr_customer]['+sel.data.uid+']=edit','','width=600,height=600');
					}
				}
						
			}
		},{
			//text:'###LANG.action.deleteCustomer###',
			tooltip:'###LANG.title### ###LANG.action.deleteCustomer###',
			iconCls:'t3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-delete',
			handler:function() {
				var sm  = Ext.getCmp('customerGrid').getSelectionModel();
				var sel = sm.getSelected();
				if(sm.hasSelection()) {
					if(sel.data.uid!='') {
						Ext.Msg.show({
							title:'###LANG.action.deleteCustomer###?',
							msg: '###LANG.action.deleteCustomer###? <br> - '+sel.data.title,
							buttons: Ext.Msg.YESNO,
							fn: function(btn) {
								if(btn=='yes') {
									TYPO3.ks_sitemgr.tabs.dispatch(
										'ks_sitemgr_customer',
										'deleteCustomer',
										sel.data.uid,
										function() {
											Ext.getCmp('customerGrid').getStore().reload();
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
		}
	],
	colModel: new Ext.grid.ColumnModel({
			defaults: {
			sortable: true
		},
		columns: [
			{id: 'uid', header: 'ID', width: 200, sortable: true, dataIndex: 'uid',hidden:true},
			{header: '###LANG.grid.type###', width:30,fixed:true,sortable:false,renderer:function(val){
				return '<span class="t3-icon t3-icon-tcarecords t3-icon-tcarecords-tx_kssitemgr_customer t3-icon-tx_kssitemgr_customer-default"></span>';
			}},
			{header: '###LANG.grid.customer###', dataIndex: 'title'},
			{header: '###LANG.grid.users###', dataIndex: 'users',sortable:false}
		],
	}),
	viewConfig: {
		forceFit: true,
	}
});