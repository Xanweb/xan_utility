!function(global, $) {
    'use strict';
    var _template = _.template('<div id="ccm-alert-dialog" class="ccm-ui"><div class="container-fluid <%=css%>" style="margin-bottom:0;">'
                +'<div class="row"><div class="col-xs-offset-1 col-xs-10">'
                +'<div class="spacer-row-1"></div><h4><%=message%></h4><div class="spacer-row-2"></div>'
                +'</div></div></div></div>');
        
    function dialog($elt, title, onCloseFn) {
        $elt.dialog({
            title: title,
            dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper',
            autoOpen: true,
            width: 'auto',
            minHeight:125,
            resizable: false,
            modal: true,
            onDestroy: onCloseFn,
            close: function (event, ui) {
                $(this).remove();
            }
        });
    }
    var AlertDialog = {
        show: function(title, message, onCloseFn) {
            dialog($(_template({css: '', message: message})), title, onCloseFn);
        },
        info: function(title, message, onCloseFn) {
            dialog($(_template({css: 'alert alert-info', message: message})), title, onCloseFn);
        },
        warn: function(title, message, onCloseFn) {
            dialog($(_template({css: 'alert alert-warning', message: message})), title, onCloseFn);
        },
        error: function(title, message, onCloseFn) {
            dialog($(_template({css: 'alert alert-danger', message: message})), title, onCloseFn);
        },
        confirm : function(title, message, confirm, cancel, okCallback, cancelCallback) {
            $(_template({css: '', message: message})).dialog({
                title: title,
                dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper ccm-ui',
                autoOpen: true,
                width: '400px',
                resizable: false,
                modal: true,
                buttons: {
                    Confirm:  {
                        click: function () {
                            okCallback();
                            $(this).dialog("close");
                        },
                        text: confirm,
                        class: 'btn btn-danger btn-sm'
                    },
                    cancel: {
                        click: function () {
                            if(cancelCallback){
                                cancelCallback();
                            }
                            $(this).dialog("close");
                        },
                        text: cancel,
                        class: 'btn btn-default btn-sm'
                    }
                },
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        },
    };

    global.AlertDialog = AlertDialog;

}(this, $);
            


