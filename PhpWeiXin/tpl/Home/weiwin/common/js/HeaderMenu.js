// JavaScript Document
$(document).ready(function () {
    var $MenuLiA = $(".Menu > li > a");
    var ProSerPic = ".ProSerPic";
    var $MenuChildren = $(".MenuChildren");
    var TimeOutMenu = 200; //鼠标移开隐藏间隔时间（毫秒）
    var ProSerPicW = 11; //箭头的宽度
    var ProSerPicH = 9; //箭头的高度
    var MenuMinL = 0;
    var ChildrenLi = -1;
    $MenuLiA.hover(function () {
        var MenuLi = $MenuLiA.index(this);
        if (ChildrenLi == MenuLi) clearTimeout(ChildrenTime);
        var MenuLiL = $(this).position().left + 204;
        var MenuLiT = $(this).position().top;
        var MenuLiW = $(this).parent("li").outerWidth(true);
        //alert(MenuLiW)
        var MenuLiH = $(this).parent().outerHeight(true);
        if ($(this).attr("class") == "HasChildren") {
            var $ProSer = $("#ProSer" + MenuLi);
            var ChildrenW = $ProSer.width();
            ProSerL = MenuLiL + (MenuLiW / 2) - (ChildrenW / 2) - ProSerPicW;
            ProSerPicL = ChildrenW / 2 - ProSerPicW;
            if (ProSerL < MenuMinL) {
                ProSerPicL += ProSerL;
                ProSerL = MenuMinL;
            };
            ProSerT = MenuLiT + MenuLiH;
            $ProSer.find(ProSerPic).css({ "left": ProSerPicL });

            //            var position = $('#liAbout').position();
            //            $('#ProSer1').css('left', position.left + 'px'); liFunction
            if ($ProSer.attr('id') == 'ProSer1') {
                ProSerL = '43%';
            } else {
                ProSerL = '30%';
            }
            var _top = 0, _left = 0;
            var _op = document.getElementById('liAbout');
            while (_op != null) {
                _top += _op.offsetTop;
                _left += _op.offsetLeft;
                _op = _op.offsetParent;
            }
            if ($(this).attr("text") == 1) {
                $ProSer.css({ "left": _left+12, "top": ProSerT - ProSerPicH-30 }).show();
            }
            else {
                $ProSer.css({ "left": _left+120, "top": ProSerT - ProSerPicH-30 }).show();
            }
        }
    }, function () {
        ChildrenLi = -1;
        var MenuLi = $MenuLiA.index(this);
        MenuTime = setTimeout(function () { $("#ProSer" + MenuLi).hide() }, TimeOutMenu);
    });
    $MenuChildren.hover(function () {
        clearTimeout(MenuTime);
        ChildrenLi = parseInt($(this).attr("id").replace("ProSer", ""));
        $(this).show();
    }, function () {
        ThisChildren = $(this);
        ChildrenTime = setTimeout(function () { ThisChildren.hide(); ChildrenLi = -1; }, TimeOutMenu);
    });
});