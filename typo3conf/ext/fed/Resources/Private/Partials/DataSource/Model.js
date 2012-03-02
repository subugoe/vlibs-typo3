{namespace fed=Tx_Fed_ViewHelpers}

Ext.define('{prefix}{className}', {
    extend: 'Ext.data.Model',
	fields: [
	<f:for each="{properties}" as="def" key="name" iteration="iteration">
        {name: '{def.name}', type: '{def.type}', xtype: 'textfield' }
		<f:if condition="{iteration.isLast}" then="" else="," />
	</f:for>
	],
	idProperty: 'uid',
    proxy: {
        type: 'rest',
		api: {
			create: '<fed:raw>{urls.create}</fed:raw>',
			read: '<fed:raw>{urls.read}</fed:raw>',
			update: '<fed:raw>{urls.update}</fed:raw>',
			destroy: '<fed:raw>{urls.destroy}</fed:raw>'
		}
	}
});
