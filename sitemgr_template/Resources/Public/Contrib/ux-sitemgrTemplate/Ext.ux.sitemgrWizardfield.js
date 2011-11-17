Ext.ns('Ext.ux.sitemgrWizardfield');

Ext.ux.sitemgrWizardfield= Ext.extend(Ext.form.ComboBox,
	{
		constructor: function(config) {
			config = Ext.apply({
				onTriggerClick: function(a, b, c) {
					if(this.wizardUri) {
						uri = this.wizardUri + this.getName();
						window.open(uri, 'constantWizard' + this.id, 'width=600, height=600');
					} else {
						alert('unknown handler: ' + this.internalHandler);
					}
				}
			}, config);
			Ext.ux.sitemgrWizardfield.superclass.constructor.call(this, config);
		}
	}
);

Ext.reg('sitemgrwizardfield', Ext.ux.sitemgrWizardfield);