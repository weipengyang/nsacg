

(function ($) {

    var hide, nextRow, count;
    var index = 0;

    //初始化，通过ajax获取消息列表
    (function _init() {
        $.ajax({
            type: 'POST',
            url: '/Index/GetNotificationListForPop',
            data: 'url=' + window.location.pathname,
            dataType: "jsonp",
            complete: ajaxComplete
        });
    })();

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);

        if (r != null) return unescape(r[2]); return null;
    }

    function ajaxComplete(msg) {
        //用户获取到消息后，页面中添加弹出窗口(可能当前用户还未进行消息分配，所以不进行任何操作)
        if (msg.status == 200 && JSON.parse(msg.responseText).length > 0) {
            AddPopWindowToPage(msg.responseText);
        }
    }

    //添加弹出窗口到页面中
    function AddPopWindowToPage(str) {
        var arry = JSON.parse(str);
        if (arry == null || arry.length < 1 || isFull())
            return;
        count = arry.length;
        var tables = AddContentToPopWin(arry);

        setTimeout(tips_pop, 800); //3秒后调用tips_pop()这个函数 setInterval
        intervalNextRow();

        var text = '<div class="dialog-win wyd-shadow wyd-radius">';
        text += '<div class="dialog-pannle">';
        text += '<div class="dialog-tit">';
        text += ' <div class="dialog-notice">通知</div>';
        text += '<div style="float:left; margin-left:80px">';
        text += '<div class="dialog-prev"></div>';
        text += '<div style="float:left;width:20px; height:28px; margin:0 10px "><span style="color:#f00">1</span>/' + count + '</div>';
        text += '<div class="dialog-next"></div>';
        text += '</div>';
        text += ' <div class="dialog-close">关闭</div>';
        text += '</div>';
        text += '<div class="dialog-content"></div>';
        //        text += ' <input type="hidden" id="indexPage" value=0>';
        text += ' </div>';
        text += ' </div>';

        $(document.body).append(text);
        $(".dialog-pannle").find(".dialog-content").append(tables);
        $(".dialog-pannle").find(".dialog-prev").click(getForwardMessage);
        $(".dialog-pannle").find(".dialog-next").click(getBackMessage);
        $(".dialog-pannle").find(".dialog-close").click(close);
        $($(".dialog-pannle").find(".dialog-content").children("div").get(0)).show();
    }

    //添加消息的内容到弹出窗口中
    function AddContentToPopWin(arry) {
        var items = '';
        for (i = 0; i < arry.length; i++) {
            var linkUrl = arry[i]["LinkUrl"] || '#';
            var text = '<div class="dialog-item" style="display:none;">';
            text += ' <div class="dialog-inner">';
            text += ' <div class="dialog-inner-tit">' + arry[i]["Title"] + '</div>';
            text += ' <div class="dialog-inner-text">' + arry[i]["Content"] + '</div>';
            text += ' </div>';
            text += ' <div class="dialog-more"><a href="' + linkUrl + '">查看</a></div>';
            text += '</div>';
            items = items + text;
        }
        return items;
    }

    function getValue(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
    //如果关闭弹出窗口2次以上，就不在弹出窗口
    function isFull() {
        var popclosecount = $.cookie("popclosecount") || 0;
        var num = parseInt(popclosecount);
        if (num > 2)
            return true;
        return false;
    }

    function close() {
        var popclosecount = $.cookie("popclosecount") || 0;
        $.cookie("popclosecount", (parseInt(popclosecount) + 1), { expires: 1, path: '/', secure: false });
        clearInterval(nextRow); //如果关闭了窗口就不在调用；

        tips_pop();
    }

    function tips_pop() {
        var MsgPop = $(".dialog-pannle").parent(); //获取窗口这个对象,即ID为winpop的对象
        var popH = parseInt(MsgPop.css("height")); //用parseInt将对象的高度转化为数字,以方便下面比较
        if (popH == 0) {//如果窗口的高度是0
            MsgPop.css("display", "block"); //那么将隐藏的窗口显示出来
            hide = setInterval('changeH("up")', 2);  //开始以每0.002秒调用函数changeH('up'),即每0.002秒向上移动一次
        }
        else {//否则
            hide = setInterval('changeH("down")', 2);  //开始以每0.002秒调用函数changeH('down'),即每0.002秒向下移动一次
        }
    }

    function changeH(str) {
        var MsgPop = $(".dialog-pannle").parent();
        var popH = parseInt(MsgPop.css("height"));
        if (str == 'up') {//如果这个参数是UP
            if (popH <= 180) {//如果转化为数值的高度小于等于180
                MsgPop.css("height", ((popH + 8).toString() + 'px')); //高度增加4个象素
            }
            else {
                clearInterval(hide); //否则就取消这个函数调用,意思就是如果高度超过180象度了,就不再增长了
            }
        }
        if (str == 'down') {
            if (popH >= 8) {//如果这个参数是down
                MsgPop.css("height", ((popH - 8).toString() + 'px')); //那么窗口的高度减少4个象素
            }
            else {//否则
                clearInterval(hide); //否则就取消这个函数调用,意思就是如果高度小于4个象度的时候,就不再减了
                MsgPop.css("display", "none"); //因为窗口有边框,所以还是可以看见1~2象素没缩进去,这时候就把DIV隐藏掉
            }
        }
    };

    function intervalNextRow() {
        if (count > 1) {
            nextRow = setInterval('changeRow()', 15000); //每隔15秒显示下一条记录
        }
    }

    //下一条信息
    function getBackMessage() {
        var pannle = $(".dialog-pannle");
        var contentDiv = pannle.find(".dialog-content");
        if (index < (count - 1)) {
            $(contentDiv.children("div").get(index + 1)).show();
            $(contentDiv.children("div").get(index)).hide();
            index++;
            pannle.find("span").html(index + 1);
        }
        else if (index == (count - 1)) {
            getFirstRow();
        }

        clearInterval(nextRow);
        intervalNextRow();
    }

    //    上一条信息
    function getForwardMessage() {
        var pannle = $(".dialog-pannle");
        var contentDiv = pannle.find(".dialog-content");
        if (index > 0) {
            $(contentDiv.children("div").get(index - 1)).show();
            $(contentDiv.children("div").get(index)).hide();
            index--;
            pannle.find("span").html(index + 1);
        }
        else if (index == 0 && count > 1) {
            getLastRow();
        }
        clearInterval(nextRow);
        intervalNextRow();
    }

    //获取第一条记录
    function getFirstRow() {
        var pannle = $(".dialog-pannle");
        var contentDiv = pannle.find(".dialog-content");
        if (index > 0) {
            $(contentDiv.children("div").get(0)).show();
            $(contentDiv.children("div").get(index)).hide();
            index = 0;
            pannle.find("span").html(index + 1);
        }
    }
    //获取最后一条记录
    function getLastRow() {
        var pannle = $(".dialog-pannle");
        var contentDiv = pannle.find(".dialog-content");
        if (index == 0) {
            $(contentDiv.children("div").get(count -1)).show();
            $(contentDiv.children("div").get(0)).hide();
            index = count - 1;
            pannle.find("span").html(count);
        }
    }

    function changeRow() {
        getBackMessage();
    }

    this.changeH = changeH;
    this.changeRow = changeRow;

})(jQuery);
