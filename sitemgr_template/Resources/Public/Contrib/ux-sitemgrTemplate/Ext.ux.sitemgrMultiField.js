Ext.ns('Ext.ux.sitemgrMultiField');
//Ext.ux.Typo3MultiField = Ext.extend(Ext.form.CompositeField,  {
Ext.ux.sitemgrMultiField = Ext.extend(Ext.Panel,  {
		constructor: function(config) {
				// settings for the value field
			config.fieldConfig.width = 200;
			config.fieldConfig.name  = 'data[' + config.subname + ']';
				//global settings
			config = Ext.apply({
				height:30,
				labelAlign:'left',
				layout: 'hbox',
				items: [
					{
						xtype: 'checkbox',
						name : 'check[' + config.subname + ']',
						checked: config.checkState,
						width: 20,
						listeners: {
							check: function(checkbox, value) {
								field = checkbox.findParentByType('panel');
								checkbox.fireEvent('afterrender', checkbox);
								field.doLayout();
							},
							afterrender: function(checkbox) {
								field = checkbox.findParentByType('panel');
								if(checkbox.getValue()) {
									field.get(1).show();
									field.get(2).hide();
								} else {
									field.get(1).hide();
									field.get(1).setValue(field.get(1).defaultValue);
									field.get(2).show();
								}
							}
						}
					},
					config.fieldConfig,
					{
						xtype: 'displayfield',
						value: '<p>' + config.fieldConfig.defaultValue + '</p>'
					}
				]
	        }, config);
			Ext.ux.sitemgrMultiField.superclass.constructor.call(this, config);
		}
	}
);

Ext.reg('sitemgrMultiField', Ext.ux.sitemgrMultiField);