pimcore.registerNS('pimcore.plugin.importdefinitions.provider.sql');

pimcore.plugin.importdefinitions.provider.sql = Class.create(pimcore.plugin.importdefinitions.provider.abstractprovider, {
    getItems : function() {
        return [{
            xtype: 'textfield',
            name: 'host',
            fieldLabel: t('importdefinitions_sql_host'),
            anchor : '100%',
            value: this.data['host'] ? this.data.host : ''
        },{
            xtype: 'textfield',
            name: 'username',
            fieldLabel: t('importdefinitions_sql_username'),
            anchor : '100%',
            value: this.data['username'] ? this.data.username : ''
        },{
            xtype: 'textfield',
            name: 'password',
            fieldLabel: t('importdefinitions_sql_password'),
            anchor : '100%',
            value: this.data['password'] ? this.data.password : ''
        },{
            xtype: 'textfield',
            name: 'database',
            fieldLabel: t('importdefinitions_sql_database'),
            anchor : '100%',
            value: this.data['database'] ? this.data.database : ''
        },{
            xtype: 'textfield',
            name: 'port',
            fieldLabel: t('importdefinitions_sql_port'),
            anchor : '100%',
            value: this.data['port'] ? this.data.port : '3306'
        },{
            xtype: 'textfield',
            name: 'adapter',
            fieldLabel: t('importdefinitions_sql_adapter'),
            anchor : '100%',
            value: this.data['adapter'] ? this.data.adapter : 'Pdo_Mysql'
        },{
            xtype : 'textarea',
            fieldLabel : t('importdefinitions_sql_query'),
            name : 'query',
            grow : true,
            anchor : '100%',
            minHeight : 300,
            value : this.data['query'] ? this.data.query : ''
        }];
    }
});