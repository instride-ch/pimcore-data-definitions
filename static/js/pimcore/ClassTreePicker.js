/**
 * A Picker field that contains a tree panel on its popup, enabling selection of tree nodes.
 */
Ext.define('Pimcore.ux.ClassTreePicker', {
    extend: 'Ext.form.field.Picker',
    xtype: 'classTreePicker',

    uses: [
        'Ext.tree.Panel'
    ],

    triggerCls: Ext.baseCSSPrefix + 'form-arrow-trigger',

    config: {
        /**
         * @cfg {Array} columns
         * An optional array of columns for multi-column trees
         */
        columns: null,

        /**
         * @cfg {Boolean} selectOnTab
         * Whether the Tab key should select the currently highlighted item. Defaults to `true`.
         */
        selectOnTab: true,

        /**
         * @cfg {Number} maxPickerHeight
         * The maximum height of the tree dropdown. Defaults to 300.
         */
        maxPickerHeight: 300,

        /**
         * @cfg {Number} minPickerHeight
         * The minimum height of the tree dropdown. Defaults to 100.
         */
        minPickerHeight: 100
    },

    editable: false,

    /**
     * @event select
     * Fires when a tree node is selected
     * @param {Ext.ux.TreePicker} picker        This tree picker
     * @param {Ext.data.Model} record           The selected record
     */

    initComponent: function() {
        var me = this;

        me.callParent(arguments);
    },

    createPicker: function() {
        var me = this,
            picker = new Ext.tree.Panel({
                shrinkWrapDock: 2,
                floating: true,
                columns: me.columns,
                minHeight: me.minPickerHeight,
                maxHeight: me.maxPickerHeight,
                manageHeight: false,
                shadow: false,
                listeners: {
                    scope: me,
                    itemclick: me.onItemClick
                },
                viewConfig: {
                    listeners: {
                        scope: me,
                        render: me.onViewRender
                    }
                },
                autoScroll: true,
                rootVisible: true,
                root: {
                    id: '0',
                    root: true,
                    text: t('base'),
                    expanded : true
                }
            }),
            view = picker.getView();

        if (Ext.isIE9 && Ext.isStrict) {
            // In IE9 strict mode, the tree view grows by the height of the horizontal scroll bar when the items are highlighted or unhighlighted.
            // Also when items are collapsed or expanded the height of the view is off. Forcing a repaint fixes the problem.
            view.on({
                scope: me,
                highlightitem: me.repaintPickerView,
                unhighlightitem: me.repaintPickerView,
                afteritemexpand: me.repaintPickerView,
                afteritemcollapse: me.repaintPickerView
            });
        }

        this.initLayoutFields(picker, me.config.data);

        return picker;
    },

    initLayoutFields : function (tree, data) {
        var keys = Object.keys(data);
        for (var i = 0; i < keys.length; i++) {
            if (data[keys[i]]) {
                if (data[keys[i]].childs) {
                    var text = t(data[keys[i]].nodeLabel);

                    if (data[keys[i]].nodeType == 'objectbricks') {
                        text = ts(data[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    if (data[keys[i]].nodeType == 'classificationstore') {
                        text = ts(data[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    var baseNode = {
                        type: 'layout',
                        iconCls: 'pimcore_icon_' + data[keys[i]].nodeType,
                        text : text,
                        expanded : true,
                        id : false
                    };

                    baseNode = tree.getRootNode().appendChild(baseNode);
                    for (var j = 0; j < data[keys[i]].childs.length; j++) {
                        var node = this.addDataChild.call(baseNode, data[keys[i]].childs[j].fieldtype, data[keys[i]].childs[j], data[keys[i]].nodeType, data[keys[i]].className);

                        baseNode.appendChild(node);
                    }

                    baseNode.expand();
                }
            }
        }

        window.setTimeout(function() {
            this.repaintPickerView();
        }.bind(this), 10);
    },

    addDataChild: function (type, initData, objectType, className) {

        if (type != 'objectbricks' && !initData.invisible) {
            var key = initData.name;

            var newNode = Ext.Object.merge(initData, {
                text : key,
                key : initData.name,
                type : 'data',
                layout : initData,
                leaf : true,
                dataType : type,
                iconCls: 'pimcore_icon_' + type,
                expanded: true,
                loaded : true,
                objectType : objectType,
                className : className,
                val : initData
            });

            newNode = this.appendChild(newNode);

            if (this.rendered) {
                this.expand();
            }

            return newNode;
        } else {
            return null;
        }

    },

    onViewRender: function(view){
        view.getEl().on('keypress', this.onPickerKeypress, this);
    },

    /**
     * repaints the tree view
     */
    repaintPickerView: function() {
        var style = this.picker.getView().getEl().dom.style;

        // can't use Element.repaint because it contains a setTimeout, which results in a flicker effect
        style.display = style.display;
    },

    /**
     * Handles a click even on a tree node
     * @private
     * @param {Ext.tree.View} view
     * @param {Ext.data.Model} record
     * @param {HTMLElement} node
     * @param {Number} rowIndex
     * @param {Ext.event.Event} e
     */
    onItemClick: function(view, record, node, rowIndex, e) {
        this.selectItem(record);
    },

    /**
     * Handles a keypress event on the picker element
     * @private
     * @param {Ext.event.Event} e
     * @param {HTMLElement} el
     */
    onPickerKeypress: function(e, el) {
        var key = e.getKey();

        if(key === e.ENTER || (key === e.TAB && this.selectOnTab)) {
            this.selectItem(this.picker.getSelectionModel().getSelection()[0]);
        }
    },

    /**
     * Changes the selection to a given record and closes the picker
     * @private
     * @param {Ext.data.Model} record
     */
    selectItem: function(record) {
        var me = this;
        me.setValue(record.get("val"));
        me.fireEvent('select', me, record);
        me.collapse();
    },

    /**
     * Sets the specified value into the field
     * @param {Mixed} value
     * @return {Ext.ux.TreePicker} this
     */
    setValue: function(value) {
        var me = this,
            record;

        me.value = value;

        return me;
    },

    getSubmitValue: function(){
        return this.value;
    },

    /**
     * Returns the current data value of the field (the idProperty of the record)
     * @return {Number}
     */
    getValue: function() {
        return this.value;
    },

    /**
     * Handles the store's load event.
     * @private
     */
    onLoad: function() {
        var value = this.value;

        if (value) {
            this.setValue(value);
        }
    },

    onUpdate: function(store, rec, type, modifiedFieldNames){
        var display = this.displayField;

        if (type === 'edit' && modifiedFieldNames && Ext.Array.contains(modifiedFieldNames, display) && this.value === rec.getId()) {
            this.setRawValue(rec.get(display));
        }
    }

});

