

if (!window.whtr) window.whtr = {};

var dialog_title = "提示信息";
var button_ok_text = "确定";
var button_cancel_text = "取消";

// 对话框(包括操作提示,操作确认,弹出窗口).
whtr.dialog = {

    //提示框(提示消息, 点击确定后的JS事件, 弹出框ID, 弹出的高度, 弹出的宽度).
    alert: function (message, callback, id, width, height,icon) {
        id = id || "whtr_alert";
        height = height || 120;
        width = width || 300;

        if (frameElement == null || !frameElement.api || frameElement.api == undefined) {
            //根页面弹出
            $.dialog({
                id: id,
                lock: true,
                fixed: true,
                title: dialog_title || "系统提示",
                content: message,
                icon: icon,
                width: width,
                height: height,
                min: false,
                max: false,
                okVal: button_ok_text || "Ok",
                ok: callback || true
            });
        } else {
            //弹出的页面再弹出
            frameElement.api.opener.$.dialog({
                id: id,
                lock: false,
                fixed: true,
                title: dialog_title || "系统提示",
                content: message,
                icon: icon,
                width: width,
                height: height,
                min: false,
                max: false,
                okVal: button_ok_text || "Ok",
                ok: callback || true
            });
        }

        return false;
    },
    //图标为成功型
    SuccessAlert: function (message, callback, id, width, height) {
        id = id || "whtr_alert";
        height = height || 120;
        width = width || 300;
  
        return whtr.dialog.alert(message, callback, id, width, height, 'success.gif');
    },

    //确认提示框(提示消息, 点击确定后的JS事件, 弹出框ID, 弹出的高度, 弹出的宽度)
    confirm: function (message, callback, id, width, height) {
        id = id || "whtr_confirm";
        height = height || 120;
        width = width || 300;
        if (frameElement == null || !frameElement.api || frameElement.api == undefined) {
            //根页面弹出
            $.dialog({
                id: id,
                lock: true,
                fixed: true,
                title: dialog_title || "系统提示",
                content: message,
                width: width,
                height: height,
                min: false,
                max: false,
                ok: callback || true,
                cancel: true,
                okVal: button_ok_text || "Ok",
                cancelVal: button_cancel_text || "Cancel"
            });
        }
        else {
            //弹出的页面再弹出
            frameElement.api.opener.$.dialog({
                id: id,
                lock: true,
                fixed: true,
                title: dialog_title || "系统提示",
                content: message,
                width: width,
                height: height,
                min: false,
                max: false,
                ok: callback || true,
                cancel: true,
                okVal: button_ok_text || "Ok",
                cancelVal: button_cancel_text || "Cancel"
            });
        }

        return false;
    },
    //添加按钮, 以及为按钮指定点击事件(按钮上的文字, 点击按钮的JS函数)
    addButton: function (name, callback, id) {
        var api = frameElement.api;
        var win = api.opener;

        id = id || "whtr_btn";
        name = name || button_ok_text || "确定";
       api.button({
            id: id,
            focus: true,
            name: name,
            callback: callback
        });
    },
    // 添加关闭按钮.
    addCancelButton: function (buttonText) {

        var api = frameElement.api;
        var win = api.opener;
        
        var id = id || "whtr_btn_cancel";
        var name = buttonText || button_cancel_text || "Cancel";

        api.button({
            id: id,

            name: name,
            callback: api.cancel
        });
        return false;
    },
    // 添加按钮组(确定和关闭)
    addButtons: function (callback, okButtonText, cancelButtonText) {
        okButtonText = okButtonText || button_ok_text;
        cancelButtonText = cancelButtonText || button_cancel_text;

        this.addButton(okButtonText, callback);
        this.addCancelButton(cancelButtonText);
    }, 
    //打开对话框窗口(窗口标题, 页面地址, 弹出框ID, 弹出的高度, 弹出的宽度)  
    open: function (title, url, id, width, height) {
        id = id || "whtr_open";
        title = title || "";
        height = height || 500;
        width = width || 800;
        if (frameElement == null || !frameElement.api || frameElement.api == undefined) {
            //根页面弹出
            $.dialog({
                id: id,
                lock: true,
                fixed: true,
                title: title,
                content: 'url:' + url,
                width: width,
                height: height,
                min: false
            });
        } else {
            //弹出的页面再弹出
            frameElement.api.opener.$.dialog({
                id: id,
                lock: true,
                fixed: true,
                title: title,
                content: 'url:' + url,
                width: width,
                height: height,
                min: false
            });
        }

        return false;
    },
    //Loading遮罩
    loading: function (loadingText, appPath) {
        var mask = $(document.createElement("DIV"));
        mask.attr("id", "whtr_DIALOG_LOADING_MASK")
            .css({
                "height": $(window).height() + "px",
                "width": "100%",
                "background": "#808080",
                "opacity": "0.1",
                "position": "absolute",
                "top": "0px",
                "left": "0px"
            })
            .appendTo($("body"));
        window.onresize = function () {
            mask.height($(window).height());
        };

        var wrap = $(document.createElement("DIV"));
        wrap.attr("id", "whtr_DIALOG_LOADING_WRAP")
            .css({
                "background": "#808080",
                "opacity": "0.7",
                "border-radius": "8px",
                "width": "80px",
                "height": "80px",
                "padding": "20px",
                "margin-left": "-40px",
                "margin-top": "-50px",
                "position": "absolute",
                "top": "50%",
                "left": "50%",
                "text-align": "center"
            })
            .appendTo($("body"));

        var imgObj = new Image();
        imgObj.src = appPath + "images/loading.gif";
        var img = $(document.createElement("IMG"));
        img.attr("src", imgObj.src)
            .css({
                "width": "50px",
                "height": "50px"
            })
            .appendTo(wrap);

        var text = $(document.createElement("SPAN"));
        text.css({
            "width": "80px",
            "font-size": "12px",
            "font-family": "宋体",
            "color": "#ffffff",
            "display": "inline-block",
            "margin-top": "10px"
        })
        .html(loadingText)
        .appendTo(wrap);
    },
    /*提示信息*/
    tips: function (msg) {
        if (msg!="")
            $("[tips]").html("提示：" + msg);
        else
            $("[tips]").html("");
    },
    clearTips: function () {
        $("[tips]").html("");
    }
};
//获取url参数
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
        return unescape(r[2]);
    return null;
}

