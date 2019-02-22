!function(global, $) {
    'use strict';

    function XanItemList($element, options) {
        'use strict';
        var my = this;
        options = options || {};
        my.options = $.extend({
            i18n: ccm_xan.i18n,
            items: [],
            maxItemsCount: 0
        }, options);

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

        // Clean Exit
        my.$element.closest('.ui-dialog').on('dialogclose', function(event) {
            my.destroyRichTextEditors(my.$container);
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
            my.initRichTextEditors($newItem);
            my.detectCheckboxes($newItem);
            my.setupChoiceToggler($newItem);

            if(my.extraItemLoad) {
                my.extraItemLoad($newItem, item);
            }

            if(my.options.extraItemLoad !== undefined) {
                my.options.extraItemLoad($newItem, item);
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
                        // Properly Destroy Rich Text Editors
                        my.destroyRichTextEditors($(this));
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

                if(my.options.maxItemsCount > 0 && itemsCount >= my.options.maxItemsCount) {
                    alert(my.options.i18n.maxItemsExceeded);
                    return false;
                }

                var defaultItem = (my.options.getNewItemDefaults !== undefined)?
                    my.options.getNewItemDefaults(itemsCount):
                    my.getNewItemDefaults(itemsCount);

                var $newItem = my.addItem(defaultItem);
                $newItem.find('.panel-heading').trigger('click');
                my.doSortCount();
                if(my.$element.closest('.ui-dialog.ui-widget').find('.floating-block-actions').length > 0) {
                    var scroll = $newItem.position().top;
                    if(!my.$container.parent().hasClass('ccm-block-edit-container')) {
                        scroll += my.$container.parent().position().top;
                    }

                    my.$element.closest('.ui-dialog-content.ui-widget-content').animate({
                        scrollTop: scroll
                    }, 'slow');
                }
            });
            if(my.$element.find('.floating-block-actions').length > 0) {
                my.$element.find('.floating-block-actions').appendTo(my.$element.closest('.ui-dialog.ui-widget'));
            }
        },
        setupChoiceToggler: function($item) {
            $item.find('[data-choice]').each(function(){
                var group = $(this).data('choice');
                $(this).change(function (e) {
                    var $choiceGroup = $item.find('[data-choice-group="'+group+'"]');
                    $choiceGroup.hide();
                    $choiceGroup.filter('[data-choice-value="'+$(this).val()+'"]').show();
                }).trigger('change');
            });
        },
        initPageSelectors: function ($item) {
            $item.find('div[data-field=page-selector]').each(function(i){
                $(this).xanPageSelector({
                    inputName: $(this).data('name'),
                    cID: parseInt($(this).data('value')),
                    onChange: my.options.onSelectPage
                });
            });
        },
        initFileSelectors: function ($item) {
            $item.find('div[data-field=file-selector]').each(function(i){
                $(this).concreteFileSelector({
                    inputName: $(this).data('name'),
                    fID: parseInt($(this).data('value')),
                    onChange: my.options.onSelectFile
                });
            });
        },
        initRichTextEditors: function ($item) {
            var my = this;
            var $editors = $item.find('.editor-content');
            if($editors.length > 0) {
                // Ensure a unique id for all editors
                $editors.each(function(){
                    if(!$(this).attr('id')) {
                        $(this).attr('id', _.uniqueId('editor'));
                    }
                });
                ccm_xan.editor.initCompactEditor($editors);
            }
        },
        destroyRichTextEditors: function ($container) {
            $container.find('.editor-content').each(function(){
                var id = $(this).attr('id');
                $(this).remove();
                if(CKEDITOR.instances[id] !== undefined) {
                    $("#cke_"+id).remove();
                    CKEDITOR.instances[id].destroy(false);
                }
            });
        },
        detectCheckboxes: function($item) {
            $item.find('.checkbox').each(function (index) {
                var $checkboxField = $(this).find('[type="checkbox"]');
                if($checkboxField.val() == "1") {
                    $checkboxField.parent().append('<input type="hidden" name="'+$checkboxField.attr('name')+'" >');
                    $checkboxField.removeAttr("name");
                    $checkboxField.change(function (e) {
                        if($(this).is(':checked')) {
                            $checkboxField.parent().find('input[type="hidden"]').val(1);
                        } else {
                            $checkboxField.parent().find('input[type="hidden"]').val(0);
                        }
                    }).trigger('change');
                }
            });
        },
        doSortCount: function () {
            this.$container.find('.ccm-item-entry').each(function (index) {
                $(this).find('.ccm-item-entry-sort').val(index);
            });
        }
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
