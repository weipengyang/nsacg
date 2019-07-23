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
</head>
<body>
    <div class="page">
        <nav class="bar bar-tab">
            <a class="tab-item" href="javascript:void(0);" onclick="history.go(-1);">
                <span class="icon icon-left"></span>
                <span class="tab-label">返回</span>
            </a>
            <a class="tab-item" href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-home"></span>
                <span class="tab-label">首页</span>
            </a>
            <a class="tab-item active" href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-cart"></span>
                <span class="tab-label">购物车</span>
            </a>
            <a class="tab-item" href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-me"></span>
                <span class="tab-label">我</span>
            </a>
        </nav>
        <div class="content" style="margin:0px">
            <div class="list-block media-list" style="margin:0px">
                <?php if(empty($products) != true ): ?><ul>
                        <?php if(is_array($products)): $i = 0; $__LIST__ = $products;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$p): $mod = ($i % 2 );++$i; if(empty($p['detail']) != true): if(is_array($p['detail'])): $i = 0; $__LIST__ = $p['detail'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li number="1">
                                        <div class="item-content" style="font-size:0.8rem">
                                            <div class="item-media"><a href="<?php echo U('Store/product',array('token'=>$_GET['token'],'id'=>$p['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>"><img src="<?php echo ($p["logourl"]); ?>" width="75" height="75"></a></div>
                                            <div class="item-inner">
                                                <div class="item-title">
                                                    <a href="<?php echo U('Store/product',array('token'=>$_GET['token'],'id'=>$p['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>" class="t"><?php echo ($p["name"]); ?></a>
                                                    <i class="d"><?php if(empty($p['formatTitle']) != true): echo ($p["formatTitle"]); ?>：<?php echo ($row["formatName"]); endif; ?> <?php if(empty($p['colorTitle']) != true): ?>，<?php echo ($p["colorTitle"]); ?>：<?php echo ($row["colorName"]); endif; ?></i>
                                                </div>
                                                <div class="item-title">
                                                    <div class="item-title-row">
                                                        <span>数量：</span>
                                                        <i style="cursor: pointer;color:#FF6600;font-weight:bold" class="iconfont icon-jianqu" onclick="plus_minus(<?php echo ($p["id"]); ?>, -1, <?php echo ($row["id"]); ?>)" class="dec"></i>
                                                        <input type="text" value="<?php echo ($row["count"]); ?>" style="width:50px;" onchange="change_minus(<?php echo ($p["id"]); ?>, <?php echo ($row["id"]); ?>)" id="num_<?php echo ($p["id"]); ?>_<?php echo ($row["id"]); ?>">
                                                        <i style="cursor: pointer;color:#FF6600;font-weight:bold" class="iconfont icon-tianjia" onclick="plus_minus(<?php echo ($p["id"]); ?>, 1, <?php echo ($row["id"]); ?>)" class="add"></i>
                                                    </div>
                                                </div>
                                                <div class="item-title">
                                                    <label>库存：</label>
                                                    <span id="stock"><?php echo ($row["num"]); ?></span>
                                                </div>
                                                <div class="item-title">
                                                    <label>价格：</label><span class="price">￥<?php echo ($row["price"]); ?></span>
                                                    <label style="cursor:pointer" onclick="location.href='<?php echo U('Store/deleteCart',array('token'=>$_GET['token'],'id'=>$p['id'],'did'=>$row['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>'" class="del">删除</label>
                                                </div>
                                            </div>
                                        </div>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                <?php else: ?>
                                <li number="<?php echo ($p["count"]); ?>">
                                    <div class="item-content" style="font-size:0.8rem">
                                        <div class="item-media"><a href="<?php echo U('Store/product',array('token'=>$_GET['token'],'id'=>$p['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>"><img src="<?php echo ($p["logourl"]); ?>" width="75" height="75"></a></div>
                                        <div class="item-inner">
                                            <div class="item-title">
                                                <a href="<?php echo U('Store/product',array('token'=>$_GET['token'],'id'=>$p['id'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>" class="t"><?php echo ($p["name"]); ?></a>
                                            </div>
                                            <div class="item-title">
                                                <div class="item-title-row">
                                                    <span>数量：</span>
                                                    <i style="cursor:pointer;color:#FF6600;font-weight:bold" class="iconfont icon-jianqu" onclick="plus_minus(<?php echo ($p["id"]); ?>, -1, 0)"></i>
                                                    <input type="text" value="<?php echo ($p["count"]); ?>" style="width:50px;" onchange="change_minus(<?php echo ($p["id"]); ?>, 0)" id="num_<?php echo ($p["id"]); ?>_0">
                                                    <i style="cursor: pointer;color:#FF6600;font-weight:bold" class="iconfont icon-tianjia" onclick="plus_minus(<?php echo ($p["id"]); ?>, 1, 0)"></i>
                                                </div>
                                            </div>
                                            <div class="item-title">
                                                <label>库存：</label>
                                                <span id="stock"><?php echo ($p["num"]); ?></span>
                                            </div>
                                            <div class="item-title">
                                                <label>销售价：</label><span class="price">￥<?php echo ($p["price"]); ?></span>
                                                <a style="cursor:pointer" href="javascript:;" onclick="location.href='<?php echo U('Store/deleteCart',array('token'=>$_GET['token'],'id'=>$p['id'],'did'=>0,'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>'" class="button button-danger">删除</a>
                                            </div>
                                        </div>
                                    </div>
                                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                    <div class="card" style="margin:0px">
                        <div class="card-content">
                            <div class="card-content-inner">
                                <div class="item-title" style="font-size:0.8rem">商品总数:<b id="total_count"><?php echo ($totalCount); ?></b>　商品总额:<b id="total_price">￥<?php echo ($totalFee); ?></b></div>
                                <div class="item-title-row" style="margin-top:10px">
                                    <a href="<?php echo U('Store/index',array('token'=>$_GET['token'], 'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>" class="button button-big button-round button-fill button-warning" style="width:150px">继续购物</a>
                                    <a href="<?php echo U('Store/orderCart',array('token'=>$_GET['token'], 'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>" class="button button-big button-round button-fill button-danger" style="width:150px">下单结算</a>

                                </div>
                                </div>
                            </div>
                    </div>
                    <?php else: ?>
                    <div class="m-cart-e">
                        <div class="icon"></div>
                        <div class="txt">您还没有挑选商品哦</div>
                        <a href="<?php echo U('Store/cats',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>" class="gobuy">去挑选商品</a>
                    </div><?php endif; ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function full_update(rowid,price) {
            var _this = $('#qty'+rowid);
            var this_val = parseInt($(_this).val());
            if (this_val < 1 || isNaN(this_val)) {
                alert('购买数量不能小于1！');
                $(_this).focus();
                return false;
            }
            update_cart(rowid, this_val,price);
        }
        //加减
        function plus_minus(rowid, number, did) {
            var num = parseInt($('#num_'+rowid + '_' + did).val());
            num = num + number;
            if (num < 1) {
                return false;
            }
            $('#num_'+rowid + '_' + did).attr('value',num);
            update_cart(rowid, num, did);
        }
        function change_minus(rowid, did) {
            var num = parseInt($('#num_'+rowid + '_' + did).val());
            if (num < 1) {
                return false;
            }
            $('#num_'+rowid + '_' + did).attr('value',num);
            update_cart(rowid, num, did);
        }
        //更新购物车
        function update_cart(rowid, num, did) {
            if (num > parseInt($("#stock").text())) {
                num = parseInt($("#stock").text());
                $('#num_'+rowid + '_' + did).val(num);
                floatNotify.simple('抱歉，您的购买量超过了库存了');
            }
            $.ajax({
                url: '<?php echo U('Store/ajaxUpdateCart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid));?>&id='+rowid+'&count='+num+'&did='+ did,
            success: function( data ) {
                if(data){
                    var datas=data.split('|');
                    //$('#p_buy #all_price').html('￥'+datas[1]);
                    $('#total_count').html(datas[0]);
                    $('#total_price').html('￥'+datas[1]);
                }
            }
        });
        }
    </script>
</body>
<script type="text/javascript">
    window.shareData = {
        "moduleName":"Store",
        "moduleID":"0",
        "imgUrl": "",
        "timeLineLink": "<?php echo ($f_siteUrl); echo U('Store/cart',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Store/cart',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "weiboLink": "<?php echo ($f_siteUrl); echo U('Store/cart',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "tTitle": "<?php echo ($metaTitle); ?>",
        "tContent": "<?php echo ($metaTitle); ?>"
    };
</script>
<?php echo ($shareScript); ?>
</html>