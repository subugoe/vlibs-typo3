Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*'
]);

Ext.onReady(function() {

	var rowEditor = Ext.create('Ext.grid.plugin.RowEditing');

	var store = Ext.create('Ext.data.Store', {
		model: 'Fed.DataSource',
		autoLoad: true,
		autoSync: true
	});

    var grid = Ext.create('Ext.grid.Panel', {
        height: 350,
        width: 1024,
        title: 'Live test, ExtJS4 auto-CRUD',
        renderTo: 'fedsandboxcomponent',
        viewConfig: {
            stripeRows: true
        },
		plugins: [rowEditor],
		store: store,
        columns: [
            {
                text     : 'Name',
                width	 : 100,
                sortable : false,
                dataIndex: 'name',
				field: {
					xtype: 'textfield'
				}
            },
            {
                text     : 'Description',
                flex	 : 1,
                sortable : true,
                dataIndex: 'description',
				field: {
					xtype: 'textfield'
				}
            },
			{
                text     : 'Query',
                flex	 : 1,
                sortable : true,
                dataIndex: 'query',
				field: {
					xtype: 'textfield'
				}
            },
			{
                text     : 'Data',
                flex	 : 1,
                sortable : true,
                dataIndex: 'data',
				field: {
					xtype: 'textfield'
				}
            }
        ],
		dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text: 'Add',
                iconCls: 'icon-add',
                handler: function(){
                    // empty record
					var data = {
						name: 'New DataSource'
					};
                    store.insert(0, new Fed.DataSource(data));
                    //rowEditor.startEdit(0, data.name);
                }
            }, '-', {
                text: 'Delete',
                iconCls: 'icon-delete',
                handler: function(){
                    var selection = grid.getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        store.remove(selection);
                    }
                }
            }]
        }]
    });
});