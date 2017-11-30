!function(global, $) {
    'use strict';

    function XanItemList($element, options) {
        'use strict';
        var my = this;
        my.options = options;
        my.$element = $element.addClass('ccm-block-edit-container');
        my.$container = $element.find('.ccm-item-list');
        my._templateItem = _.template($('#itemTemplate' + options.bID).html());
        my.loadItems();
        my.setupItemHeaderAction();
        my.setupDeleteItemAction();
        my.setupAddItemAction();

        // Setup Items Sort
        my.$container.sortable({
            handle: ".panel-heading",
            update: function(){
                my.doSortCount();
            }
        });
    }
    
    XanItemList.prototype = {
        getNewItemDefaults: function(itemsCount) {
            return {
                sortOrder: itemsCount
            };
        },
        loadItems: function() {
            var my = this;
            var items = my.options.items;
            if(!items || items.length === 0){
                return;
            }

            items.forEach(function(item) {
                my.addItem(item);
            });
            
            my.doSortCount();
        },
        addItem: function(item) {
            var my = this;
            my.$container.append(my._templateItem({item:item}));
            var $newItem = my.$container.find('.ccm-item-entry').last();
            my.initPageSelectors($newItem);
            my.initFileSelectors($newItem);
            my.initRedactor($newItem);
            if(my.extraItemLoad) {
                my.extraItemLoad($newItem, item);
            }
            return $newItem;
        },
        setupItemHeaderAction: function() {
            this.$container.on('click', '.ccm-item-entry > .panel-heading', function () {
                $(this).parent().find(".panel-body").toggle();
            });
        },
        setupDeleteItemAction: function(){
            var my = this;
            my.$container.on('click', '.btn-delete-item', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm(my.options.i18n.confirm)) {
                    $(this).closest('.ccm-item-entry').hide('fade', function(){
                        $(this).remove();
                        my.doSortCount();
                    });
                }
            });
        },
        setupAddItemAction: function() {
            var my = this;
            my.$element.find('.ccm-add-item-entry').click(function () {
                var itemsCount = my.$container.find(".ccm-item-entry").length;
                var $newItem = my.addItem(my.getNewItemDefaults(itemsCount));
                $newItem.find('.panel-heading').trigger('click');
                my.doSortCount();
            });
        },
        initPageSelectors: function ($item) {
            $item.find('div[data-field=page-selector]').each(function(i){
                $(this).concretePageSelector({
                    inputName: $(this).data('name'),
                    cID: parseInt($(this).data('value'))
                });
            });
        },
        initFileSelectors: function ($item) {
            $item.find('div[data-field=file-selector]').each(function(i){
                $(this).concreteFileSelector({
                    inputName: $(this).data('name'),
                    fID: parseInt($(this).data('value'))
                });
            });
        },
        initRedactor: function ($item) {
            var my = this;
            if($item.find('.redactor-content').length > 0) {
                $item.find('.redactor-content').redactor({
                    minHeight: '200',
                    'concrete5': {
                        filemanager: my.options.permissions.canAccessFileManager,
                        sitemap: my.options.permissions.canAccessSitemap,
                        lightbox: true
                    }
                });
            }
        },
        doSortCount: function () {
            this.$container.find('.ccm-item-entry').each(function (index) {
                $(this).find('.ccm-item-entry-sort').val(index);
            });
        },
    };
    
    // jQuery Plugin
    $.fn.xanItemList = function(options) {
        return $.each($(this), function(i, obj) {
            new XanItemList($(this), options);
        });
    };
    global.XanItemList = XanItemList;
    /*
     Example of ItemList Class extension
      
     function MyNewClass($element, options) {
        var my = this;
        // Call the parent constructor
        XanItemList.call(my, $element, options);
     }
     // inherit ItemList
     MyNewClass.prototype = XanUtility.createObject(XanItemList.prototype);
     // correct the constructor pointer because it points to XanItemList
     MyNewClass.prototype.constructor = MyNewClass;
    
    */
}(window, $);    