!function(global, $) {
    'use strict';
    
    function XanUtility() {
        
    }
    
    XanUtility.createObject = function (proto) {
        function ctor() { }
        ctor.prototype = proto;
        return new ctor();
    };
    
    global.XanUtility = XanUtility;
}(window, $);     