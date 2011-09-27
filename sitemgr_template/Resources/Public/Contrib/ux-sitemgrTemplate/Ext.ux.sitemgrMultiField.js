Ext.ns('Ext.ux.sitemgrMultiField');
//Ext.ux.Typo3MultiField = Ext.extend(Ext.form.CompositeField,  {
Ext.ux.sitemgrMultiField = Ext.extend(Ext.Panel,  {
		constructor: function(config) {
				// settings for the value field
			config.fieldConfig.width  = 200;
			config.fieldConfig.name   = 'data[' + config.subname + ']';
			config.fieldConfig.hidden = !config.checkState;
				//unset unused settings
			fieldConfig = config.fieldConfig;
			config.fieldConfig = undefined;
					//global settings
			config = Ext.apply({
				height:25,
				layout: 'hbox',
				bodyCssClass: 'sitemgr_template-tsconst',
				items: [
					{
						xtype: 'button',
						iconCls: 't3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-undo',
						autoShow: true,
						hidden: !config.checkState,
						handler: function(button, event) {
							panel = button.findParentByType('panel');
							panel.get(4).setValue(false);
						}
					},{
						xtype: 'button',
						autoShow: true,
						iconCls: 't3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-open',
						hidden: config.checkState,
						handler: function(button, event) {
							panel = button.findParentByType('panel');
							panel.get(4).setValue(true);
						}
					},
					fieldConfig,
					{
						hidden: config.checkState,
						xtype: 'displayfield',
						cls:   'sitemgr_template-constdisplayfield',
						value: fieldConfig.defaultValue,
						width: 200
					},{
						xtype: 'checkbox',
						name : 'check[' + config.subname + ']',
						checked: config.checkState,
						hidden: true,
						width: 20,
						listeners: {
							check: function(checkbox, value) {
								field = checkbox.findParentByType('panel');
								if(checkbox.getValue()) {
									field.get(0).show();
									field.get(1).hide();
									field.get(2).show();
									field.get(3).hide();
								} else {
									field.get(0).hide();
									field.get(1).show();
									field.get(2).hide();
									field.get(3).show();
									field.get(2).setValue(field.get(2).defaultValue);
								}
								field.doLayout();
							}
						}
					}
				]
	        }, config);
			Ext.ux.sitemgrMultiField.superclass.constructor.call(this, config);
		}
	}
);

Ext.reg('sitemgrMultiField', Ext.ux.sitemgrMultiField);