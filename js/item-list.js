!function(global, $, xan) {
    'use strict';

    function XanItemList($element, options) {
        'use strict';
        var my = this;
        options = options || {};
        my.options = $.extend({
            i18n: xan.i18n,
            items: [],
            maxItemsCount: 0,
            initContentEditors: xan.editor.initCompactEditor
        }, options);

        my.$element = $element.addClass('ccm-block-edit-container');
        my.$container = $element.find('.ccm-item-list');
        my._templateItem = _.template($('#itemTemplate' + options.bID).html());
        my.loadItems();
        my.setupItemHeaderAction();
        my.setupDeleteItemAction();
        my.setupAddItemAction();
        my.setupFloatingActionsBar();

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
            var my = this;
            var headerActionOn = (my.options.editBtn !== undefined) ? my.options.editBtn : '.ccm-item-entry > .panel-heading';
            my.$container.on('click', headerActionOn, function () {
                if (my.options.editBtn !== undefined) {
                    $(this).closest('.panel-heading').parent().find('.panel-body').toggle();
                }else{
                    $(this).parent().find('.panel-body').toggle();
                }
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
        },
        setupFloatingActionsBar: function () {
            var my = this;
            var $actionsBar = my.$element.find('.floating-block-actions');
            if ($actionsBar.length > 0) {
                $actionsBar.appendTo(my.$element.closest('.ui-dialog.ui-widget'));
                this.enableFloatingActionsBar();

                var $bFormContainer = $('#ccm-block-form');
                if ($bFormContainer.find('ul.nav-tabs.nav').length > 0) {
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.attributeName === "class") {
                                $(mutation.target).trigger('classChange');
                            }
                        });
                    });

                    $bFormContainer.find('ul.nav-tabs.nav > li').each(function () {
                        observer.observe(this, {
                            attributes: true
                        });
                    });

                    $bFormContainer.find('ul.nav-tabs.nav > li').on('classChange', function (e) {
                        var $widgetContent = my.$element.closest('.ui-dialog-content.ui-widget-content');
                        if (my.$element.is(':visible')) {
                            $widgetContent.css('padding-bottom', '');
                            my.enableFloatingActionsBar();
                        } else {
                            $widgetContent.css('padding-bottom', $widgetContent.css('margin-bottom'));
                            my.disableFloatingActionsBar();
                        }
                    });
                }
            }
        },
        enableFloatingActionsBar: function () {
            var my = this;
            var $actionsBar = my.$element.closest('.ui-dialog.ui-widget').find('.floating-block-actions');
            if ($actionsBar.length > 0) {
                this.$element.closest('.ui-dialog.ui-widget').addClass('has-floating-block-actions');
            }
        },
        disableFloatingActionsBar: function () {
            this.$element.closest('.ui-dialog.ui-widget').removeClass('has-floating-block-actions');
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
            var my = this;
            $item.find('div[data-field=page-selector]').each(function(){
                $(this).xanPageSelector({
                    inputName: $(this).data('name'),
                    cID: parseInt($(this).data('value')),
                    onChange: my.options.onSelectPage
                });
            });
        },
        initFileSelectors: function ($item) {
            var my = this;
            $item.find('div[data-field=file-selector]').each(function(){
                $(this).xanFileSelector({
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

                my.options.initContentEditors($editors);
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

}(window, $, ccm_xan);
