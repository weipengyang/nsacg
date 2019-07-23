<?php if (!defined('THINK_PATH')) exit();?>﻿<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>我的优惠券</title>
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
    <script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
    <link href="http://at.alicdn.com/t/font_1452909907_4403944.css" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $staticFilePath;?>/css/style_touch.css">
    <style>
        .pic {
            width: 100%;
            margin-bottom: 10px;
        }
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
        		margin:0px;
        		padding:0px;
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
            <div class="buttons-tab">
                <a data-no-cache="true" href="#tab1" class="tab-link <?php if( $is_use == '0' ): ?>active<?php endif; ?> button">未使用</a>
                <a data-no-cache="true" href="#tab2" class="tab-link <?php if( $is_use == '1' ): ?>active<?php endif; ?> button">已使用</a>
            </div>
            <div class="tabs" style="margin:0px;width:100%">
                <div id="tab1" class="tab active" style="margin:0px;width:100%">
                    <div class="content-block" style="margin:0px;width:100%">
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div class="card bg">
                <div class="card-content" style="margin:0px;padding:0px;">
                    <div class="card-content-inner" style="margin:0px;padding:0px;">
                    	     <div class="item-title">
                            <div class="row">
                                <div class="col-40"><img src='<?php echo ($item["pic"]); ?>' class='logo' /></div>
                                <div class="col-60" style='margin-top:1rem;'><?php echo ($item["title"]); ?></div>
                              </div>
                            </div>
                        </div>
                        <div class="item-title line"><span style='margin-top:50px'>NO.ACGH2016010002</span></div>
                        <div class='row'>
                        <div class="col-40">
                        	 <a style="margin:10px;height:25px;line-height:25px;font-size:0.6rem" class="button button-big button-round button-fill button-warning" href="#" 
                        	 	onclick="use(1)">点击立即使用</a>
                        </div>
                        <div class='col-60'>
                        <div class="item-after" style=' text-align:right;padding-top:10px'>
                         有效期至<?php echo (date('Y年m月d日',$item["over_time"])); ?>
                         </div>
                        </div>	
                        </div>
                     </div>
                     <div id="queren<?php echo ($item["id"]); ?>" style="display:none;">
                       <form action="/index.php?g=Wap&m=Card&a=action_useCoupon&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($token); ?>" method="post" id="payform<?php echo ($item["id"]); ?>">
                          <p style=" margin:10px 0 0 0">
                              <input name="username" class="px" id="username<?php echo ($item["id"]); ?>" value="" type="text" placeholder="请输入商家用户名">
                          </p>
                          <p style=" margin:10px 0 0 0">
                              <input name="password" class="px" id="password<?php echo ($item["id"]); ?>" value="" type="password" placeholder="请输入商家密码">
                          </p>
                          <p style=" margin:10px 0 0 0">
                              <textarea placeholder="备注信息" class="px" id="notes<?php echo ($item["id"]); ?>" name="notes"></textarea>
                          </p>
                          <?php if($item["is_use"] == '0'): ?><p style=" margin:10px 0">
                                  <input type="hidden" name="coupon_type" value="<?php echo ($type); ?>" />
                                  <input type="hidden" name="record_id" value="<?php echo ($item["id"]); ?>" />
                                  <a id="showcard0" class="submit" href="javascript:void(0)" onclick="use(<?php echo ($item["id"]); ?>)">确定使用</a>
                              </p><?php endif; ?>
                      </form>
                    </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    </div>
                 <div id="tab2" class="tab">
                    <div class="content-block">
                        <p>This is tab 2 content</p>
                    </div>
                </div>
                <div id="tab3" class="tab">
                    <div class="content-block">
                        <p>This is tab 3 content</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            'use strict';
            //对话框
            $.init();
        });
        function use(i) {
        	alert(1);
            var password = $('#password' + i).val();
            var username = $('#username' + i).val();
            var notes = $('#notes' + i).val();

            if (!username) {
                alert('请输入商家用户名');
                return false;
            }
            if (!password) {
                alert('请输入商家密码');
                return false;
            }
            if (!notes) {
                alert('请输入备注信息');
                return false;
            }
            var itemid = i;
            var submitData = {
                password: password,
                record_id: itemid,
                username: username,
                notes: notes,
                coupon_type: { pigcms: $type },
                cat: 0,
            };

            $.post('/index.php?g=Wap&m=Card&a=action_useCoupon&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($token); ?>', submitData,
            function (data) {
                if (data.success == 1) {
                    document.getElementById("test" + i + "-header").style.display = 'none';
                    document.getElementById('password' + i).value = '';

                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
            }, "json");
        }
    </script>
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