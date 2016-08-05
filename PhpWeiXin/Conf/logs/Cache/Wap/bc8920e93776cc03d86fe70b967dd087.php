<?php if (!defined('THINK_PATH')) exit();?>﻿<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo ($thisCard["cardname"]); ?>优惠券</title>
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
    <script src="/tpl/static/jquery.min.js" type="text/javascript"></script>
    <!-- <script src="/tpl/static/alert.js" type="text/javascript"></script> -->
    <script src="<?php echo RES;?>/card/js/accordian.pack.js" type="text/javascript"></script>
    <link href="/tpl/static/kindeditor/examples/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css">
    <script src="/tpl/static/kindeditor/examples/jquery-ui/js/jquery-ui-1.9.2.custom.js" type="text/javascript"></script>
</head>
<body id="cardnews" onload="new Accordian(&#39;basic-accordian&#39;,5,&#39;header_highlight&#39;);" class="mode_webapp">
    <div class="qiandaobanner"><a href="javascript:history.go(-1);"><img src="<?php echo ($thisCard["vip"]); ?>"></a> </div>
    <style>
        header {
            margin: 0 10px;
            position: relative;
            z-index: 4;
        }

            header ul {
                margin: 0 -1px;
                border: 1px solid #179f00;
                border-radius: 3px;
                width: 100%;
                overflow: hidden;
            }

                header ul li a.bl {
                    border-left: 1px solid #0b8e00;
                }

                header ul li a.on {
                    background-color: #179f00;
                    color: #ffffff;
                    background-image: -moz-linear-gradient(center bottom, #179f00 0%, #5dd300 100%);
                }

                header ul li a {
                    color: #0b8e00;
                    display: block;
                    font-size: 15px;
                    height: 28px;
                    line-height: 28px;
                    text-align: center;
                    width: 33%;
                    float: left;
                }

        .pic {
            width: 100%;
            margin-bottom: 10px;
        }

        .over {
            background: #aaa;
            border: 1px solid #aaa;
            box-shadow: 0 1px 0 #cccccc inset, 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .window .title {
            background-image: linear-gradient(#179f00, #179f00);
        }

        .ui-widget-content {
            border-color: #e9e9e9;
        }

        .ui-dialog {
            padding: 0;
        }

        .ui-widget-header {
            border: 0;
            background: #179F00;
        }

        .ui-widget-header {
            color: #fff;
        }

        .infos {
            word-break: break-all;
            overflow: auto;
        }
    </style>
    <header>
        <nav id="nav_1" class="p_10">
            <ul class="box">
                <li><a href="index.php?g=Wap&m=Card&a=my_coupon&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>&type=1" class="<?php if( $type == 1): ?>on<?php endif; ?>">优惠券(<?php echo ($couponCount); ?>)</a></li>
                <li><a href="index.php?g=Wap&m=Card&a=my_coupon&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>&type=2" class="bl <?php if( $type == 2): ?>on<?php endif; ?>">代金券(<?php echo ($cashcount); ?>)</a></li>
                <li><a href="index.php?g=Wap&m=Card&a=my_coupon&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>&type=3" class="bl <?php if( $type == 3): ?>on<?php endif; ?>">礼品券(<?php echo ($integralcount); ?>)</a></li>
            </ul>
        </nav>
    </header>
    <div id="basic-accordian">
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div id="test<?php echo ($item["id"]); ?>-header" class="accordion_headings  <?php if ($item['id']==$firstItemID){?>header_highlight<?php } ?>">
                <div class="tab  <?php if( $type == 3): ?>gift<?php else: ?>coupon<?php endif; ?>">
                    <span class="title">
                        <?php echo ($item["title"]); ?>(剩余<?php echo ($item["get_count"]); ?>张)
                        <?php if($type == 3): ?><p><?php echo ($item["integral"]); ?>积分兑换一张</p>
                            <?php else: ?>
                            <p>会员优惠价￥<?php echo ($item["money"]); ?>元</p><?php endif; ?>
                        <p>有效期至<?php echo (date('Y年m月d日',$item["enddate"])); ?></p>
                    </span>
                </div>
                <div id="test<?php echo ($item["id"]); ?>-content" >
                    <div class="accordion_child">
                        <div id="queren<?php echo ($item["id"]); ?>">
                            <img src="<?php echo ($item["pic"]); ?>" class="pic" >
                            <?php if($item["get_count"] < 1): ?><a class="submit over" href="javascript:void(0)">已经卖光</a>
                                <?php else: ?>
                                <?php if($type == 3): ?><a class="submit" href="javascript:void(0)" onclick="payformsubmit(<?php echo ($item["id"]); ?>,<?php echo ($item["integral"]); ?>)">点击兑换</a>
                                <?php else: ?>
                                    <a class="submit" href="javascript:void(0)" onclick="payformsubmit(<?php echo ($item["id"]); ?>,<?php echo ($item["money"]); ?>)">点击购买</a><?php endif; endif; ?>
                        </div>
                        <ul style="min-height:100px;">
                            <b>详情说明：</b>
                            <p class="infos"><?php echo ($item["info"]); ?></p>
                        </ul>
                        <div style="clear:both;height:20px;"></div>
                    </div>
                    <div style="clear:both;height:20px;"></div>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <script>
        var jQ = jQuery.noConflict();
        //加减
        function plus_minus(number,price) {
            var num = parseInt($('#buy_num').val());
            num = num + parseInt(number);
            if (num > parseInt($('#stock').text())) {
                num = parseInt($('#stock').text());
            }
            if (num < 0) {
                return false;
            }
            $('#buy_num').attr('value',num);
        } 
        function payformsubmit(itemid,money){
            var submitData = {
                coupon_id:itemid,
                cardid: '<?php echo ($thisCard["id"]); ?>',
                type: '<?php echo ($type); ?>',
                cat:3,
            };

            var html=[];
            html.push('<table>');
            html.push(' <tr>');
            html.push('<td><span>单价：￥'+money+'</span></td>');
            html.push('</tr>');
            html.push('<tr>');
            html.push('<td><span>购买数量：</span><input id="buy_num" name="buy_num" type="text" value="1"></td>');
            html.push('</tr>');
            html.push('</table>');
            if(submitData.type == 3){
                jQ("#message").text('兑换礼品券需要消耗'+money+'点积分，确定领取吗？');
            }else if(submitData.type == 2){
                jQ("#message").text('确定要购买此代金卷吗？');
            }else{
                //jQ("#message").text('点击确定后将从您的会员卡上扣除'+money+'元，您确定要购买此优惠卷吗？');
                jQ("#message").html(html.join(''));

            }
            jQ("#message").dialog({
                title: "提示",
                modal: true,
                resizable: false,
                buttons: {
                    "取消": function() {
                        jQ(this).dialog("close");
                    },
                    "确定": function() {
                        if(submitData.type == 3){
                            ajaxSub(submitData);//方法回调
                        }else{
                            var num=parseInt(jQ("#message :input").val());
                            submitData["buynum"]=num;
                            corfimpasswd(submitData,money*num);
                        }
                        //jQ("#message").text('');
                    }
                }
            });
        }
        function corfimpasswd(submitData,money)
        {
            jQ("#message").html('<span>总金额:￥'+money+'元</span><input type="password" name="pass" class="inp" id="pass" placeholder="支付密码" /></div>');
            jQ("#message").dialog({
                title:"输入支付密码",
                modal: true,
                buttons: {
                    "确定": function() {
                        var password=jQ("#message :input").val();
                        submitData["password"]=password
                        ajaxSub(submitData);
                        //jQ("#message").text('');
                    }
                }
            });
        }
        function ajaxSub(submitData){
            jQ.post('/index.php?g=Wap&m=Card&a=action_myCoupon&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($token); ?>', submitData,function(data) {
                jQ("#message").text(data);
                jQ("#message").dialog({
                    title:"温馨提示！",
                    modal: true,
                    buttons: {
                        "确定": function() {
                            jQ("#message").text('');
                            location.href=location.href;
                            jQ(this).dialog("close");
                        }
                    }
                });
            }, "text");
        }

        jQ(function(){
            var boardDiv = "<div id='message' style='display:none;'><span id='spanmessage'></span></div>";
            jQ(document.body).append(boardDiv);
        });
    </script>
    <div style="height:40px;"></div>
<div class="footermenu">
    <ul>
        <!--<li>
            <a href="/index.php?g=Wap&m=Card&a=index&token=<?php echo ($token); ?>&cardid=<?php echo ($thisCard["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>">
            <a href="">
                <img src="<?php echo RES;?>/card/images/home.png">
                <p>首页</p>
            </a>
        </li>
        -->
        <li>
            <a <?php if(ACTION_NAME=='card'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=card&token=<?php echo ($token); ?>&cardid=<?php echo ($thisCard["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>">
           	<img src="<?php echo RES;?>/card/images/c.png">
            <p>会员卡</p>
            </a>
        </li>
        <li>
            <a <?php if(ACTION_NAME=='my_coupon'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=my_coupon&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>">
           	<img src="<?php echo RES;?>/card/images/prev.png">
            <p>会员专享</p>
            </a>
        </li>
        <li>
            <a <?php if(ACTION_NAME=='wallet'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=wallet&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>">
             <img src="<?php echo RES;?>/card/images/intergral.png">
            <p>我的钱包</p>
            </a>
        </li>
        <li>
            <a <?php if(ACTION_NAME=='cards'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=cards&token=<?php echo ($token); ?>&cardid=<?php echo ($thisCard["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>">
           	<img src="<?php echo RES;?>/card/images/my.png">
            <p>个人设置</p>
            </a>
        </li>
    </ul>
</div>
    <script type="text/javascript">
window.shareData = {  
            "moduleName":"Card",
            "moduleID":"0",
            "imgUrl": "", 
            "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Card/index',array('token'=>$token));?>",
            "tTitle": "会员卡",
            "tContent": ""
};
</script>
<?php echo ($shareScript); ?>
</body>
</html>