<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo ($thisCard["cardname"]); ?></title>
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
    <script src="/tpl/static/jquery.min.js" type="text/javascript"></script>
    <script src="/tpl/static/alert.js" type="text/javascript"></script>
</head>
<style>
    .integral_table th {
        font-size: 14px;
        font-weight: normal;
        background-color: #eee;
        border-top: 1px solid #e3e3e3;
        border-bottom: 1px solid #e3e3e3;
        text-align: left;
        padding: 3px 5px 3px 5px;
    }

    .integral_table td {
        font-size: 12px;
        color: #797979;
        border-bottom: 1px solid #e3e3e3;
        text-align: left;
        background-color: #fff;
        padding: 5px 0;
    }

    .integral_table tfoot td {
        border-bottom: 0;
    }

    .integral_table td .yqian {
        color: #02ae02;
    }

    .integral_table td .wqian {
        color: #797979;
    }

    .integral_table td.right {
        text-align: right;
    }

    .integral_table td .heji {
        color: #02ae02;
    }

    .but {
        width: 100%;
        background-color: #179F00;
        padding: 10px 20px;
        font-size: 16px;
        text-decoration: none;
        border: 1px solid #0B8E00;
        background-image: linear-gradient(bottom, #179F00 0%, #5DD300 100%);
        background-image: -o-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
        background-image: -moz-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
        background-image: -webkit-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
        background-image: -ms-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
        background-image: -webkit-gradient( linear, left bottom, left top, color-stop(0, #179F00), color-stop(1, #5DD300) );
        -webkit-box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
        box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        -o-border-radius: 5px;
        border-radius: 5px;
        color: #ffffff;
        display: block;
        text-align: center;
        text-shadow: 0 1px rgba(0, 0, 0, 0.2);
    }

    .box {
        text-align: center;
    }

    .jifen-box {
        margin: 10px 5px;
    }

    .select {
        border-radius: 1px;
        font-size: 13px;
        padding: 4px 8px;
        background-color: #fff;
        border: 1px solid #ccc;
        width: 120px;
    }

    .window .title {
        background-image: linear-gradient(#179f00, #179f00);
    }
</style>
<body id="cardunion" class="mode_webapp2">

    <div class="qiandaobanner"><a href="javascript:history.go(-1);"><img src="<?php echo ($thisCard["recharge"]); ?>"></a> </div>

    <div class="jifen-box">


        <h3>会员消费</h3>
        <div class="box">
            <form action="" method="post">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="integral_table">
                    <tr>
                        <td width="70"><b>会员卡号</b></td>
                        <td><?php echo ($card["number"]); ?></td>
                    </tr>
                    <tr>
                        <td><b>消费金额</b></td>
                        <td><input type="text" id="price" name="price" style="width:85%;height:35px;line-height:35px;border:1px solid #eee" /> 元</td>
                    </tr>
                    <?php if($consume_info != ''): ?><tr>
                            <td><b>　优惠方式</b></td>
                            <td>
                                <select name="consume_id" class="select" id="consume_id">
                                    <option value="">请选择...</option>
                                    <?php if(is_array($consume_info)): $i = 0; $__LIST__ = $consume_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$consume_info): $mod = ($i % 2 );++$i;?><option value="<?php echo ($consume_info["id"]); ?>"><?php echo ($consume_info["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </td>
                        </tr><?php endif; ?>
                    <tr>
                        <td><b>支付方式</b></td>
                        <td>
                            <select name="pay_type" class="select" id="pay_type">
                               <option value="1">会员卡支付</option>
                               <option value="3">微信支付</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><b>消费类型</b></td>
                        <td>
                            <select name="consume_type" class="select" id="consume_type">
                                <option value="discount1">代办服务</option>
                                <option value="discount2">汽车美容</option>
                                <option value="discount3">汽车维修</option>
                                <option value="discount4">汽车商品</option>
                                <option value="discount5">汽车轮胎</option>
                           </select>
                        </td>
                    </tr>
                    <tr class="com_user">
                        <td><b>商家名称</b></td>
                        <td>
                            <input type="text" name="username" id="username" style="width:90%;height:35px;line-height:35px;border:1px solid #eee" />
                        </td>
                    </tr>
                    <tr class="com_pwd">
                        <td><b>商家密码</b></td>
                        <td>
                            <input type="password" name="password" id="pwd" style="width:90%;height:35px;line-height:35px;border:1px solid #eee" />
                        </td>
                    </tr>
                    <tr class="com_pwd">
                        <td><b>商家备注</b></td>
                        <td>
                            <textarea id="notes" name="notes" id="notes" style="width:90%;border:1px solid #eee"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="确定" class="but" />
                        </td>
                    </tr>
            </form>
        </div>
        <div class="clr"></div>
    </div>
    <script>
        $(function () {
            if ($('#pay_type').val() != 2) {
                $('.com_pwd').css('display', 'none');
                $('.com_user').css('display', 'none');
            }
            
            $('#pay_type').change(function () {
                if ($(this).val() == 2) {
                    $('.com_pwd').css('display', '');
                    $('.com_user').css('display', '');
                } else {
                    $('.com_pwd').css('display', 'none');
                    $('.com_user').css('display', 'none');
                }
            });

            $('.but').click(function () {
                var price = $('#price').val();
                var pay_type = $('#pay_type').val();
                var username = $('#username').val();
                var pwd = $('#pwd').val();
                var consume_id = $('#consume_id').val();

                var prg = /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
                if (!prg.test(price)) {
                    alert('请填写正确的消费金额');
                    return false;
                }
                /*		if(consume_id == ''){
                            alert('请选择优惠方式');
                            return false;
                        }*/
                if (pay_type == 2) {
                    if (username == '') {
                        alert('请填写商家用户名');
                        return false;
                    }
                    if (pwd == '') {
                        alert('请填写商家密码');
                        return false;
                    }
                }
            });
        });
    </script>
    <div class="footermenu">
    <ul>
    <li>
            <a href="javascript:history.go(-1);">
            <img src="<?php echo RES;?>/card/images/return.png">
            <p>返回</p>
            </a>
        </li>
    <li>
            <a <?php if($infoType=='memberCardHome'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=index&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>">
            <img src="<?php echo RES;?>/card/images/home.png">
            <p>会员卡首页</p>
            </a>
        </li>
        <li>
            <a <?php if($infoType=='companyDetail'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=companyDetail&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&companyid=<?php echo ($thisCompany["id"]); ?>">
            <img src="<?php echo RES;?>/card/images/detaila.png">
            <p>商家详情</p>
            </a>
        </li>
        <li>
            <a <?php if($infoType=='myCard'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=card&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($memberCard["cardid"]); ?>">
            <span class="num2" ><?php echo ($cardsCount); ?></span><img src="<?php echo RES;?>/card/images/my.png">
            <p>我的会员卡</p>
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