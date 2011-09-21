Ext.ns('Ext.ux.sitemgrCombobox');

Ext.ux.sitemgrCombobox= Ext.extend(Ext.form.ComboBox,
	{
		constructor: function(config) {
			config = Ext.apply({
				store: new Ext.data.ArrayStore({
					fields: ['value', 'title'],
					data: config.staticData
				}),
				displayField: 'title',
				valueField: 'value',
				mode:'local',
				triggerAction: 'all',
				selectOnFocus: true,
				forceSelection: true
			}, config);
			Ext.ux.sitemgrCombobox.superclass.constructor.call(this, config);
		}
	}
);

Ext.reg('sitemgrCombobox', Ext.ux.sitemgrCombobox);