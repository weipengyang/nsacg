<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo ($thisCard["cardname"]); ?>开卡赠送</title>
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
<script src="/tpl/static/jquery.min.js" type="text/javascript"></script>
<script src="/tpl/static/alert.js" type="text/javascript"></script>
<style>
.window .title{background-image: linear-gradient(#179f00, #179f00);}
.accordion_headings .tab  {background-size:40px 40px;}
.accordion_headings .integral_info{padding-left:46px;}
</style>
</head>
<body id="cardnews" onLoad="new Accordian(&#39;basic-accordian&#39;,5,&#39;header_highlight&#39;);" class="mode_webapp">
<div id="basic-accordian">
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div id="test<?php echo ($item["id"]); ?>-header" class="accordion_headings  <?php if ($item['id']==$firstItemID){?>header_highlight<?php } ?>">

<div class="tab  <?php if($item["type"] == '1'): ?>integral_info<?php else: ?>coupon<?php endif; ?>">
<div>
	<?php echo ($item["name"]); if($item["type"] == '1'): ?>(<?php echo ($item["item_value"]); ?>点)<?php endif; ?>
	<p><?php if ($item['type']&&$item['end']){?>有效期至<?php echo (date('Y年m月d日',$item["end"])); }else{ ?>无限期<?php } ?></p>
</div>
</div>

</div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>


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