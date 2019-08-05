!function(global, $) {
    'use strict';
    
    function XanPageSelector($element, options) {
        // Call the parent constructor
        ConcretePageSelector.call(this, $element, options);
    }

    // inherit ConcretePageSelector
    XanPageSelector.prototype = XanUtility.createObject(ConcretePageSelector.prototype);
    // correct the constructor pointer because it points to ConcretePageSelector
    XanPageSelector.prototype.constructor = XanPageSelector;
    XanPageSelector.prototype.loadPage = function (cID) {
        var my = this;
        my.$element.html(my._loadingTemplate({'options': my.options, 'cID': cID}));
        ConcretePageAjaxSearch.getPageDetails(cID, function (r) {
            var page;
            if(r.pages){
                page = r.pages[0];
            } else {
                page = {'cID': cID, 'name': '<span class="text-danger">' + global.ccm_xan.i18n.pageNotFound + ' (ID: ' + cID + ')</span>', 'url': global.ccm_xan.i18n.pageNotFound};
            }

            my.$element.html(my._pageLoadedTemplate({'inputName': my.options.inputName, 'page': page}));
            var tooltips = my.$element.find('.launch-tooltip');
            if (tooltips.length && tooltips.tooltip) {
                var ttOptions = {},
                    $ttHolder = $('#ccm-tooltip-holder');
                if ($ttHolder.length) {
                    ttOptions.container = $ttHolder;
                }
                tooltips.tooltip(ttOptions);
            }
            my.$element.on('click', 'a[data-page-selector-action=clear]', function(e) {
                e.preventDefault();
                my.$element.html(my._chooseTemplate);
            });

            if(my.options.onChange) {
                my.options.onChange(my.$element, r);
            }
        });
    };

    // jQuery Plugin
    $.fn.xanPageSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new XanPageSelector($(this), options);
        });
    };

    $.fn.concretePageSelector = $.fn.xanPageSelector;

    global.XanPageSelector = XanPageSelector;
    
}(window, $);     