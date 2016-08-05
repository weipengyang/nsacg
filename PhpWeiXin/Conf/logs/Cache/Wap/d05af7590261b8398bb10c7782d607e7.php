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
    <link href="http://at.alicdn.com/t/font_1452909907_4403944.css" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $staticFilePath;?>/css/style_touch.css">
    <title><?php echo ($metaTitle); ?></title>
    <style>
        .logo {
            display: block;
            width: 2.5rem;
            height: 2.5rem;
            color: white;
            text-align: center;
            line-height: 2rem;
            margin: 2px;
            border-radius: 100%;
        }

        .line {
            width: 100%;
            height: 2.5rem;
            line-height: 2.5rem;
            overflow: hidden;
            margin: 0px;
            text-align: center;
            padding: 0px;
            color: #FF6600;
            font-size: 1.4rem;
            background: #333333;
            z-index: 9999;
            font-weight: bold;
            filter: alpha(opacity=40);
            -moz-opacity: 0.4;
            -khtml-opacity: 0.4;
            opacity: 0.4;
        }

        .bg {
            margin-top: 5px;
            border-radius: 4%;
            color: white;
            background: #fcecfc; /* Old browsers */
            background: -moz-linear-gradient(left, #fcecfc 0%, #fd89d7 33%, #fba6e1 53%, #fd89d7 61%, #ff7cd8 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, right top, color-stop(0%,#fcecfc), color-stop(33%,#fd89d7), color-stop(53%,#fba6e1), color-stop(61%,#fd89d7), color-stop(100%,#ff7cd8)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(left,#2496FF 0%,#2859FF 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(left, #fcecfc 0%,#fd89d7 33%,#fba6e1 53%,#fd89d7 61%,#ff7cd8 100%); /* Opera11.10+ */
            background: -ms-linear-gradient(left, #fcecfc 0%,#fd89d7 33%,#fba6e1 53%,#fd89d7 61%,#ff7cd8 100%); /* IE10+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcecfc', endColorstr='#ff7cd8',GradientType=1 ); /* IE6-9 */
            background: linear-gradient(left, #fcecfc 0%,#fd89d7 33%,#fba6e1 53%,#fd89d7 61%,#ff7cd8 100%); /* W3C */
            filter: alpha(opacity=50);
            -moz-opacity: 0.5;
            -khtml-opacity: 0.5;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="page" style="margin:0px">
        <nav class="bar bar-tab">
            <a class="tab-item" href="#" data-no-cache="true" onclick="history.go(-1);">
                <span class="icon icon-left"></span>
                <span class="tab-label">返回</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-home"></span>
                <span class="tab-label">首页</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Card/index',array('token' => $_GET['token'], 'wecha_id' => $wecha_id));?>">
                <span class="icon icon-friends"></span>
                <span class="tab-label">会员中心</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-cart"></span>
                <?php if($productcount != 0): ?><span class="badge" id='badge'><?php echo ($productcount); ?></span>
                    <?php else: ?>
                    <span class="badge" id='badge' style="display:none"><?php echo ($productcount); ?></span><?php endif; ?>
                <span class="tab-label">购物车</span>
            </a>
            <a class="tab-item active" data-no-cache="true" href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-me"></span>
                <span class="tab-label">我</span>
            </a>
        </nav>
        <div class="content" style="margin:0px">
            <div class="card bg">
                <div class="card-content" style="margin:0px;padding:0px;">
                    <div class="card-content-inner" style="margin:0px;padding:0px;">
                        <div class="item-title">
                            <div class="row">
                                <div class="col-20"><img src='<?php echo ($fans[' portrait']); ?>' class='logo' /></div>
                                <div class="col-80" style='margin-top:1rem;'><?php echo ($fans['username']); ?></div>
                            </div>
                        </div>
                        <div class="row" style="margin:0px;padding-left:20px;">
                            <div class="col-50">余额</div>
                            <div class="col-50">积分</div>
                        </div>
                        <div class="row" style="margin:0px;padding-left:20px;">
                            <div class="col-50">500元</div>
                            <div class="col-50">2000分</div>
                        </div>
                        <div class="row" style="margin:0px;padding-left:20px;">
                            <div class='col-50'>会员类型</div>
                            <div class="col-50">普通会员</div>
                        </div>
                        <div class="item-title line"><span style='margin-top:50px'>NO.ACGH2016010002</span></div>
                        <div class="item-after" style=' text-align:right;padding-top:10px'>爱养车汽车服务有限公司</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="card-content-inner">
                        <div class="item-title">
                            <div class="row">
                                <div class="col-50"> <a href="#" class="button button-big button-round button-fill button-warning" style="width:150px">充值</a></div>
                                <div class="col-50"> <a href="#" class="button button-big button-round button-fill button-success" style="width:150px">消费</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="list-block">
                        <ul>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-dingdanguanli" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">我的订单</div>
                                        <span class="badge" style='background:red;color:white' id='badge'>2</span>
                                    </div>
                                </a>
                            </li>
                            <!--<li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-friends" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">会员中心</div>
                                    </div>
                                </a>
                            </li>-->
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/coupon',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cardid' => 5, 'type' => 1));?>">
                                    <div class="item-media"><i class="icon iconfont icon-wofabude" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">我的优惠券</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-552cc5b4659c9" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">我的礼品券</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-xxi" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">我的消息</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-meirong" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">我的车辆</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-huangou" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">积分兑换</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-gerenziliao" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">个人信息</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="item-link item-content" data-no-cache="true" href="<?php echo U('Store/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid));?>">
                                    <div class="item-media"><i class="icon iconfont icon-shezhi" style='color:green;font-size:1rem'></i></div>
                                    <div class="item-inner">
                                        <div class="item-title">设置</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.shareData = {
            "moduleName": "Store",
            "moduleID": "",
            "imgUrl": "",
            "timeLineLink": "<?php echo C('site_url') . U('Store/my',array('token' => $_GET['token'], 'twid' => $mytwid));?>",
            "sendFriendLink": "<?php echo C('site_url') . U('Store/my',array('token' => $_GET['token'], 'twid' => $mytwid));?>",
            "weiboLink": "<?php echo C('site_url') . U('Store/my',array('token' => $_GET['token'], 'twid' => $mytwid));?>",
            "tTitle": "<?php echo ($metaTitle); ?>",
            "tContent": "<?php echo ($metaTitle); ?>"
        };
    </script>
</body>
<?php echo ($shareScript); ?>
</html>