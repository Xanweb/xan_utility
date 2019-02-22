!function(global, $) {
    'use strict';
    
    function XanFileSelector($element, options) {
        // Call the parent constructor
        ConcreteFileSelector.call(this, $element, options);
    }
    
    // inherit ConcreteFileSelector
    XanFileSelector.prototype = XanUtility.createObject(ConcreteFileSelector.prototype);
    // correct the constructor pointer because it points to ConcretePageSelector
    XanFileSelector.prototype.constructor = XanFileSelector;
    XanFileSelector.prototype.loadFile = function(fID, callback) {
        var my = this;

        var extendedCallback = function(r) {
            if(my.options.onChange !== undefined) {
                my.options.onChange(my.$element, r);
            }

            if (callback) {
                callback(r);
            }
        };

        ConcreteFileSelector.prototype.loadFile.apply(this, [fID, extendedCallback]);
    };

    // jQuery Plugin
    $.fn.xanFileSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new XanFileSelector($(this), options);
        });
    };

    global.XanFileSelector = XanFileSelector;
    
}(window, $);     