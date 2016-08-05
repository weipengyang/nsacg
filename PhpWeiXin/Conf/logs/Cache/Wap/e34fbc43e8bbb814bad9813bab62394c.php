<?php if (!defined('THINK_PATH')) exit();?><!--<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /><meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta name="format-detection" content="telephone=no"/>
<title><?php echo ($metaTitle); ?></title>
<script src="<?php echo $staticFilePath;?>/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="<?php echo $staticFilePath;?>/js/jquery.lazyload.js" type="text/javascript"></script>
<script src="<?php echo $staticFilePath;?>/js/notification.js" type="text/javascript"></script>
<script src="<?php echo $staticFilePath;?>/js/swiper.min.js" type="text/javascript"></script>
<script src="<?php echo $staticFilePath;?>/js/main.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $staticFilePath;?>/css/style_touch.css">
<link type="text/css" rel="stylesheet" href="/tpl/static/store/style/<?php echo ($productSet['headerid']); ?>.css">
</head>
<script>
$(document).ready(function(){
	$(".m-hd .cat").parent('div').click( function() {
	    var docH=$(document).height();
	  	$('.sub-menu-list').toggle();
	    $(".m-right-pop-bg2").addClass("on").css('min-height',docH);
	});
	$(".m-right-pop-bg2").click( function() {
	    $('.sub-menu-list').hide();
		$(".m-right-pop-bg2").removeClass("on").removeAttr("style");
	});
});
</script>
<body>
<div id="top"></div>
<div id="scnhtm5" class="m-body">
<div class="m-detail-mainout">



<div class="m-hd">
<div><a href="javascript:history.go(-1);" class="back">返回</a></div>
<div><a href="javascript:void(0);" class="cat">商品分类</a></div>
<div class="tit"><?php echo ($metaTitle); ?></div>
<div><a href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>" class="uc">用户中心</a></div>
<div><a href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>" class="cart">购物车<i class="cart_com"><?php if($totalProductCount != 0): echo ($totalProductCount); endif; ?></i></a></div>
</div>

<ul class="sub-menu-list">
<li><a href="<?php echo U('Store/select',array('token' => $_GET['token'], 'wecha_id' => $wecha_id, 'twid' => $twid));?>">浏览店铺</a></li>
<li><a href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">商城首页</a></li>
<?php if(is_array($cats)): $i = 0; $__LIST__ = $cats;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hostlist): $mod = ($i % 2 );++$i; if($hostlist['isfinal'] == 1): ?><li><a href="<?php echo U('Store/products',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'twid' => $twid, 'cid' => $cid));?>"><?php echo ($hostlist["name"]); ?></a></li>
<?php else: ?>
<li><a href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'cid' => $hostlist['cid'], 'parentid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'twid' => $twid, 'cid' => $cid));?>"><?php echo ($hostlist["name"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
</ul>
-->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
    <script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
    <link href="http://at.alicdn.com/t/font_1452909907_4403944.css" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $staticFilePath;?>/css/style_touch.css">
    <title><?php echo ($metaTitle); ?></title>
    <style>
        .infodetail img { width:100%
        }
    </style>
</head>
<body>
    <div class="page">
        <nav class="bar bar-tab">
            <a class="tab-item" data-no-cache="true" href="javascript:void(0);" onclick="history.go(-1);">
                <span class="icon icon-left"></span>
                <span class="tab-label">返回</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-home"></span>
                <span class="tab-label">首页</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-cart"></span>
                <?php if($count != 0): ?><span class="badge" id='badge'><?php echo ($count); ?></span>
                    <?php else: ?>
                    <span class="badge" id='badge' style="display:none"><?php echo ($count); ?></span><?php endif; ?>
               <span class="tab-label">购物车</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-me"></span>
                <span class="tab-label">我</span>
            </a>
        </nav>
        <div class="content">
            <div class="card">
                <div class="card-content">
                    <div class="swiper-container" data-space-between='10'>
                        <div class="swiper-wrapper">
                            <?php if(empty($imageList) != true): if(is_array($imageList)): $i = 0; $__LIST__ = $imageList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="swiper-slide"><img src="<?php echo ($img["image"]); ?>" style="width:100%;height:100%"></div><?php endforeach; endif; else: echo "" ;endif; ?>
                                <?php else: ?>
                                <div class="swiper-slide"><img src="<?php echo ($product["logourl"]); ?>"></div><?php endif; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><?php echo ($product["name"]); ?></div>
                <div class="card-content">
                    <div class="card-content-inner">
                        <div class="item-title" style="color:#FF0000;font-size:12px;"><del>原价:￥<?php echo ($product["oprice"]); ?>元</del></div>
                        <div class="item-title" style="color:#0055CC;font-size:12px;"><span>积分卡会员价：<em id="vprice">￥<?php echo ($product["discount1"]); ?></em></span></div>
                        <div class="item-title" style="color:#0055CC;font-size:12px;"> <span>白金卡会员价：<em id="vprice">￥<?php echo ($product["discount2"]); ?></em></span></div>
                        <div class="item-title" style="color:#0055CC;font-size:12px;"> <span>钻石卡会员价：<em id="vprice">￥<?php echo ($product["discount3"]); ?></em></span></div>
                        <div class="item-title">
                        <div>
                         <span>数量：</span> <i style="cursor: hand;color:#FF6600;font-weight:bold" class="iconfont icon-jianqu"  onclick="plus_minus(<?php echo ($product["id"]); ?>, -1,<?php echo ($product["price"]); ?>)"></i>
                          <input id="buy_num" name="goods[num]" style="width:50px;" type="text" value="1">
                          <i style="cursor: pointer;color:#FF6600;font-weight:bold" class="iconfont icon-tianjia" onclick="plus_minus(<?php echo ($product["id"]); ?>,1,<?php echo ($product["price"]); ?>)"></i>
                          <span class="stock">(库存<span id="stock"><?php echo ($product["num"]); ?></span>)</span>
                        </div>

                        </div>

                        <div class="item-title" style="padding-top:10px">
                            <div class="row">
                                <div class="col-50"><a href="#" onclick="QuickBuy()" class="button button-big button-fill button-danger">立即购买</a></div>
                                <div class="col-50"><a href="#" onclick="add_cart();" id="btn_add_cart" class="button button-big button-fill button-success">加入购物车</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-info">
                <div style="display:none;" class="price">促销：<font color="red">新春热卖~~~</font></div>
                <?php if(empty($catData['color']) != true): ?><div class="i-row">
                        <div class="t"><?php echo ($catData["color"]); ?>：</div>
                        <div class="c">
                            <ul class="ys cm">
                                <?php if(is_array($colorData)): $colorId = 0; $__LIST__ = $colorData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$detail): $mod = ($colorId % 2 );++$colorId;?><li style="cursor:pointer" class="color" id="color_<?php echo ($detail['color']); ?>">
                                        <?php if($detail['logo'] != ''): ?><img src="<?php echo ($detail['logo']); ?>"><?php else: echo ($detail['colorName']); endif; ?>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </div>
                    </div><?php endif; ?>
                <?php if(empty($catData['norms']) != true): ?><div class="i-row">
                        <div class="t"><?php echo ($catData["norms"]); ?>：</div>
                        <div class="c">
                            <ul id="shoe_size_list" class="ys cm">
                                <?php if(is_array($formatData)): $formatId = 0; $__LIST__ = $formatData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$detail): $mod = ($formatId % 2 );++$formatId;?><li style="cursor:pointer" class="norms" id="norms_<?php echo ($detail['format']); ?>"><?php echo ($detail['formatName']); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </div>
                    </div><?php endif; ?>
                <?php if(empty($catData['norms']) != true OR empty($catData['color']) != true): if(is_array($productDetail)): $i = 0; $__LIST__ = $productDetail;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pro): $mod = ($i % 2 );++$i;?><input type="hidden" id="color_<?php echo ($pro["color"]); ?>_norms_<?php echo ($pro["format"]); ?>" value="<?php echo ($pro["num"]); ?>" did="<?php echo ($pro["id"]); ?>" price="<?php echo ($pro["price"]); ?>" vprice="<?php echo ($pro["vprice"]); ?>" class="hidden" /><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            </div>
            <div class="card">
            <div class="buttons-tab">
                <a href="#tab1" class="tab-link active button">详情</a>
                <a href="#tab2" class="tab-link button">评论<i>(<?php echo ($num); ?>)</i></a>
            </div>
            <div class="tabs">
                <div id="tab1" class="tab active" style="width:100%">
                    <div class="content-block">
                        <div class="buttons-row">
                            <ul class="detail-list">
                                <!-- <li><?php echo ($product["name"]); ?></li> -->
                                <?php if(is_array($attributeData)): $i = 0; $__LIST__ = $attributeData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attribute): $mod = ($i % 2 );++$i;?><li><label><?php echo ($attribute["name"]); ?>：</label><span><?php echo ($attribute["value"]); ?></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                <div class="infodetail"><?php echo ($product["intro"]); ?></div>
                            </ul>
                            <div class="m-page more_detailinfo" style="display:none;">
                                <div class="pg-num"><a href="javascript:void(0);">查看图文详情</a></div>
                            </div>
                            <div style="display:none;" class="m-page hide_detailinfo">
                                <div class="pg-num"><a href="javascript:void(0);">收起图文详情</a></div>
                            </div>
                            <div class="goods_intro"></div>
                        </div>
                    </div>
                </div>
                <div id="tab2" class="tab">
                    <div class="detailinfo">
                        <div class="com-dec"><span class="star"><span class="st-box" style="width:<?php echo ($percent); ?>"><i></i></span></span>评价<b><?php echo ($score); ?></b>分，共<b><?php echo ($num); ?></b>位参与</div>
                        <ul class="com-list">
                            <?php if(is_array($comment)): $i = 0; $__LIST__ = $comment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$com): $mod = ($i % 2 );++$i;?><li>
                                    <p>
                                        <?php echo ($com['wecha_id']); ?>：<?php echo ($com['score']); ?>分　<?php echo ($com['productinfo']); ?>
                                    </p>
                                    <p><?php echo ($com['content']); ?><i>&nbsp;&nbsp;<?php echo (date("Y-m-d H:i",$com["dateline"])); ?></i></p>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                        <?php if($page > 0): ?><div class="m-page more_comment_list">
                                <div class="com_pg-num pg-num" id="<?php echo ($page); ?>"><a href="javascript:void(0);">查看更多评论</a></div>
                            </div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script type="text/javascript">
        $.config = {
            // 路由功能开关过滤器，返回 false 表示当前点击链接不使用路由
            routerFilter: function ($link) {
                // 某个区域的 a 链接不想使用路由功能
                if ($link.is('.disable-router a')) {
                    return false;
                }
                return true;
            }
        };
        $(function () {
            'use strict';
            //对话框
            $.init();
        });
        var SysSecond;
        var InterValObj;
        var buyDetailId = '';
        $(document).ready(function () {
            $(".com_pg-num").click(function () {
                var page = parseInt($(this).attr('id'));
                $.get("<?php echo U('Store/getcomment',array('token'=>$token,'pid'=>$product['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid,'cid' => $cid));?>" + '&page=' + page, function (response) {
                    if (response.error_code == false) {
                        var html = '';
                        $.each(response.data, function (i, data) {
                            html += '<li><p>' + data.wecha_id + '：' + data.score + '分　' + data.productinfo + '</p>';
                            html += '<p>' + data.content + '<i>&nbsp;&nbsp;' + data.dateline + '</i></p></li>';
                        });
                        if (html != '') {
                            $(".com-list").append(html);
                        }
                        if (response.page > 0) {
                            $(".com_pg-num").attr('id', response.page);
                        } else {
                            $(".m-page").hide();
                        }
                    }
                }, 'json');
            });


            SysSecond = parseInt($("#remainSeconds").html()); //这里获取倒计时的起始时间
            InterValObj = window.setInterval(SetRemainTime, 1000); //间隔函数，1秒执行
            $(".color").click(function () {
                if ($(this).attr('class') != 'color on') {
                    $(this).addClass('on').siblings().removeClass('on');
                    var id = $(this).attr('id');
                    var nextid = 'norms_0';
                    $('.norms').each(function () {
                        if ($(this).attr('class') == 'norms on') {
                            nextid = $(this).attr('id');
                        }
                    });
                    if ($("#" + id + "_" + nextid).val() != null && $("#" + id + "_" + nextid).val() != '') {
                        buyDetailId = id + "_" + nextid;
                        $("#stock").text($("#" + id + "_" + nextid).val());
                        $("#xsprice").text('￥' + $("#" + id + "_" + nextid).attr('price'));
                        $("#vprice").text('￥' + $("#" + id + "_" + nextid).attr('vprice'));
                    } else {
                        $("#stock").text(0);
                    }
                } else {
                    $(this).removeClass('on');
                }
            });
            $(".norms").click(function () {
                if ($(this).attr('class') != 'norms on') {
                    $(this).addClass('on').siblings().removeClass('on');
                    var id = $(this).attr('id');
                    var nextid = 'color_0';
                    $('.color').each(function () {
                        if ($(this).attr('class') == 'color on') {
                            nextid = $(this).attr('id');
                        }
                    });
                    if ($("#" + nextid + "_" + id).val() != '' && $("#" + nextid + "_" + id).val() != null) {
                        buyDetailId = nextid + "_" + id;
                        $("#stock").text($("#" + nextid + "_" + id).val());
                        $("#xsprice").text('￥' + $("#" + nextid + "_" + id).attr('price'));
                        $("#vprice").text('￥' + $("#" + nextid + "_" + id).attr('vprice'));
                    } else {
                        $("#stock").text(0);
                    }
                } else {
                    $(this).removeClass('on');
                }
            });
        });

        //将时间减去1秒，计算天、时、分、秒
        function SetRemainTime() {
            if (SysSecond > 0) {
                SysSecond = SysSecond - 1;
                var second = Math.floor(SysSecond % 60);             // 计算秒
                var minite = Math.floor((SysSecond / 60) % 60);      //计算分
                var hour = Math.floor((SysSecond / 3600) % 24);      //计算小时
                var day = Math.floor((SysSecond / 3600) / 24);        //计算天
                $("#remainTime").html('&nbsp;&nbsp;还剩' + day + "天" + hour + "小时" + minite + "分" + second + "秒");
            } else {//剩余时间小于或等于0的时候，就停止间隔函数
                window.clearInterval(InterValObj);
                //这里可以添加倒计时时间为0后需要执行的事件
            }
        }
        //加减
        function plus_minus(rowid, number, price) {
            var num = parseInt($('#buy_num').val());
            num = num + parseInt(number);
            if (num > parseInt($('#stock').text())) {
                num = parseInt($('#stock').text());
            }
            if (num < 0) {
                return false;
            }
            $('#buy_num').attr('value', num);
        }
        function add_cart() {

            $("#btn_add_cart").attr("disable", false);
            var count = parseInt($('#buy_num').val());
            var did = parseInt($("#" + buyDetailId).attr('did'));
            if ($('.hidden').eq(0).val() != null && $('.hidden').eq(0).val() != '' && $('.hidden').eq(0).val() != 'undefined') {
                if (isNaN(did)) {
                    return $.toast('请选择相应属性的产品');
                }
            } else {
                did = 0;
            }
            if (count > parseInt($("#stock").text())) {
                return $.toast('抱歉，您的购买量超过了库存了');
            }
            $.ajax({
                url: "<?php echo U('Store/addProductToCart',array('token'=>$token,'id'=>$product['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid,'cid' => $cid));?>" + '&count=' + count + '&did=' + did,
                success: function (data) {
                    if (data) {
                        var datas = data.split('|');
                        $('.cart_com').text(datas[0]);
                        $("#btn_add_cart").attr("disable", true);
                        $("#badge").show();
                        var num = parseInt($("#badge").text()) + count;
                        $("#badge").text(num);
                        return $.toast('加入购物车成功');
                    } else {
                        return $.toast('抱歉，您的请求不正确');
                    }
                }
            });
        }
        function QuickBuy() {
            var count = parseInt($('#buy_num').val());
            var did = parseInt($("#" + buyDetailId).attr('did'));
            if ($('.hidden').eq(0).val() != null && $('.hidden').eq(0).val() != '' && $('.hidden').eq(0).val() != 'undefined') {
                if (isNaN(did)) {
                    return $.toast('请选择相应属性的产品');
                    return false;
                }
            } else {
                did = 0;
            }
            if (count > parseInt($("#stock").text())) {
                return $.toast('抱歉，您的购买量超过了库存了');
            }
            $.ajax({
                url: "<?php echo U('Store/addProductToCart',array('token'=>$token,'id'=>$product['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid,'cid' => $cid));?>" + '&count=' + count + '&did=' + did,
                success: function (data) {
                    if (data) {
                        location.href = "<?php echo U('Store/cart',array('token' => $token,'wecha_id' => $_GET['wecha_id'], 'twid' => $twid,'cid' => $cid));?>";;
                    } else {
                        return $.toast('抱歉，您的请求不正确');
                    }
                }
            });
        }
    </script>
    <script type="text/javascript">
        window.shareData = {
            "moduleName": "Store",
            "moduleID": "<?php echo ($product['id']); ?>",
            "imgUrl": "<?php echo ($product['logourl']); ?>",
            "timeLineLink": "<?php echo ($f_siteUrl); echo U('Store/product',array('token' => $_GET['token'],'id'=>$product['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Store/product',array('token' => $_GET['token'],'id'=>$product['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "weiboLink": "<?php echo ($f_siteUrl); echo U('Store/product',array('token' => $_GET['token'],'id'=>$product['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "tTitle": "<?php echo ($metaTitle); ?>",
            "tContent": "<?php echo ($metaTitle); ?>"
        };
    </script>
    <?php echo ($shareScript); ?>
</body>
</html>