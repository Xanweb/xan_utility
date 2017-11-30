!function(global, $) {
    'use strict';
    
    function XanFileSelector($element, options, callback) {
        // Call the parent constructor
        ConcreteFileSelector.call(this, $element, options);
        this.callback = callback;
    }
    
    // inherit ConcreteFileSelector
    XanFileSelector.prototype = createObject(ConcreteFileSelector.prototype);
    // correct the constructor pointer because it points to ConcretePageSelector
    XanFileSelector.prototype.constructor = XanFileSelector;
    XanFileSelector.prototype.loadFile = function(fID, callback) {
        var my = this;
        if(typeof fID =='object') {
            return my.loadHtml(fID);
        }
        my.$element.html(my._loadingTemplate({'inputName': my.options.inputName, 'fID': fID}));
        ConcreteFileManager.getFileDetails(fID, function(r) {
            var file = r.files[0];
            my.loadHtml(file);
            if(my.callback) {
                my.callback(r);
            }
            if (callback) {
                callback(r);
            }
        });
    };
    XanFileSelector.prototype.loadHtml = function (file) {
        var my = this;
        my.$element.html(my._fileLoadedTemplate({'inputName': my.options.inputName, 'file': file}));
        my.$element.find('.ccm-file-selector-file-selected').on('click', function(event) {
            var menu = file.treeNodeMenu;
            if (menu) {
                var concreteMenu = new ConcreteFileMenu($(this), {
                    menuLauncherHoverClass: 'ccm-file-manager-menu-item-hover',
                    menu: $(menu),
                    handle: 'none',
                    container: my
                });
                concreteMenu.show(event);
            }
        });
    };
    XanFileSelector.prototype.clear = function () {
        var my = this;
        my.$element.html(my._chooseTemplate);
    };
    // jQuery Plugin
    $.fn.xanFileSelector = function(methodOrOptions, callbackOrArgs) {
        if ( XanFileSelector.prototype.hasOwnProperty(methodOrOptions) && methodOrOptions === 'loadFile') {
            return XanFileSelector.prototype.loadFile.apply($(this).data('xanFileSelector'), callbackOrArgs);
            
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            if (this.length === 0 || this.length > 1) {
                return this;
            }

            var fileSelector = new XanFileSelector($(this), methodOrOptions, callbackOrArgs);
            $(this).data('xanFileSelector', fileSelector);
            return this;
        } else {
            $.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.xanFileSelector' );
        } 
    };
    global.XanFileSelector = XanFileSelector;
    
}(window, $);     