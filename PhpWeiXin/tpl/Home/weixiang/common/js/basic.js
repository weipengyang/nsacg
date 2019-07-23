/*基本js,所有工具类等*/

//获取input框焦点
function ofheadertxt(id, value) { //headertxt(this,'请输入您要找的课程')" onfocus="ofheadertxt(this,'请输入您要找的课程');"
    if (id.value == value) {
        id.value = "";
        id.style.color = "#666";
    }
}
function obheadertxt(id, value) {
    if (id.value == "") {
        id.value = value;
        id.style.color = "#666";
    }
}

//加入收藏
function addCookie() {
    if (document.all) {
        window.external.addFavorite('http://www.baidu.com/', '万惠投融');
    }
    else if (window.sidebar) {
        window.sidebar.addPanel('万惠投融', 'http://www.baidu.com/', "");
    }
}

//设为首页
function setHomepage() {
    if (document.all) {
        document.body.style.behavior = 'url(#default#homepage)';
        document.body.setHomePage('http://www.baidu.com/');
    }
    else if (window.sidebar) {
        if (window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            }
            catch (e) {
                alert("该操作被浏览器拒绝，假如想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true");
            }
        }
        var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
        prefs.setCharPref('browser.startup.homepage', 'http://www.baidu.com/');
    }
}

/***************************************************************************************/
/**************************后端程序员使用的js代码(放在此处) start**************************/
//格式化日期时间 formatDateTime(date,fmt)
function formatDateTime(date, fmt) {
    if (typeof (date) == "string")
        date = new Date(parseInt(date.replace("/Date(", "").replace(")/", ""), 10));

    if (fmt == null || fmt == undefined)
        fmt = "yyyy-MM-dd hh:mm";

    var o = {
        "M+": date.getMonth() + 1,                 //月份 
        "d+": date.getDate(),                    //日 
        "h+": date.getHours(),                   //小时 
        "m+": date.getMinutes(),                 //分 
        "s+": date.getSeconds(),                 //秒 
        "q+": Math.floor((date.getMonth() + 3) / 3), //季度 
        "S": date.getMilliseconds()             //毫秒 
    };

    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));

    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));

    return fmt;
};

//格式化数值
function formatNumber(nData, opts) {
    opts = $.extend({},
    {
        decimalSeparator: ".",
        thousandsSeparator: ",",
        decimalPlaces: 0,
        round: false,
        prefix: "",
        suffix: "",
        defaulValue: 0
    }, opts);
    if (!(typeof (nData) === 'number' && isFinite(nData))) {
        nData *= 1;
    }
    if (typeof (nData) === 'number' && isFinite(nData)) {
        var bNegative = (nData < 0);
        nData = Math.abs(nData);
        var sOutput = nData + "";
        var sDecimalSeparator = (opts.decimalSeparator) ? opts.decimalSeparator : ".";
        var nDotIndex;
        if (typeof (opts.decimalPlaces) === 'number' && isFinite(opts.decimalPlaces)) {
            // Round to the correct decimal place
            var nDecimal, nDecimalPlaces = opts.decimalPlaces;
            if (opts.round) {
                nDecimal = Math.pow(10, nDecimalPlaces);
                sOutput = Math.round(nData * nDecimal) / nDecimal + "";
            } else {
                nDecimal = Math.pow(10, (nDecimalPlaces + 3));
                sOutput = Math.floor(Math.round(nData * nDecimal) / 1000) / (nDecimal / 1000) + "";
            }
            nDotIndex = sOutput.lastIndexOf(".");
            if (nDecimalPlaces > 0) {
                // Add the decimal separator
                if (nDotIndex < 0) {
                    sOutput += sDecimalSeparator;
                    nDotIndex = sOutput.length - 1;
                }
                    // Replace the "."
                else if (sDecimalSeparator !== ".") {
                    sOutput = sOutput.replace(".", sDecimalSeparator);
                }
                // Add missing zeros
                while ((sOutput.length - 1 - nDotIndex) < nDecimalPlaces) {
                    sOutput += "0";
                }
            }
        }
        if (opts.thousandsSeparator) {
            var sThousandsSeparator = opts.thousandsSeparator;
            nDotIndex = sOutput.lastIndexOf(sDecimalSeparator);
            nDotIndex = (nDotIndex > -1) ? nDotIndex : sOutput.length;
            var sNewOutput = sOutput.substring(nDotIndex);
            var nCount = -1;
            for (var i = nDotIndex; i > 0; i--) {
                nCount++;
                if ((nCount % 3 === 0) && (i !== nDotIndex) && (!bNegative || (i > 1))) {
                    sNewOutput = sThousandsSeparator + sNewOutput;
                }
                sNewOutput = sOutput.charAt(i - 1) + sNewOutput;
            }
            sOutput = sNewOutput;
        }
        // Prepend prefix
        sOutput = (opts.prefix) ? opts.prefix + sOutput : sOutput;
        // Append suffix
        sOutput = (opts.suffix) ? sOutput + opts.suffix : sOutput;
        return (bNegative ? '-' : '') + sOutput;

    } else {
        return opts.defaulValue;
    }
}

//格式化金额
function formatCurrency(num, opts, length) {
    if (typeof (length) != "number") length = 2;
    opts = $.extend({},
    {
        decimalSeparator: ".",
        thousandsSeparator: ",",
        decimalPlaces: length,
        round: false,
        prefix: "",
        suffix: "",
        defaulValue: 0
    }, opts);
    return formatNumber(num, opts);
}
/**************************后端程序员增加的js代码 end**************************/
/***************************************************************************************/
























