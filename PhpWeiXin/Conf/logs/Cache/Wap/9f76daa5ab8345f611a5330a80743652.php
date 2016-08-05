<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo ($thisCard["cardname"]); ?>积分使用</title>
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
<script src="/tpl/static/jquery.min.js" type="text/javascript"></script>
<script src="/tpl/static/alert.js" type="text/javascript"></script>
<script src="<?php echo RES;?>/card/js/accordian.pack.js" type="text/javascript"></script>
<style>
header {
    margin: 0 10px;
    position: relative;
    z-index: 4;
}
header ul {
	margin:0 -1px;
	border: 1px solid #179f00;
	border-radius: 3px;
	width: 100%;
	overflow: hidden;
}
header ul li a.on {
    background-color:#179f00;
    color: #ffffff;
    background-image: -moz-linear-gradient(center bottom , #179f00 0%, #5dd300 100%);
}
header ul li a {
    color: #0b8e00;
    display: block;
    font-size: 15px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    width:50%;
    float:left;
}
.pic{width:100%;margin-bottom:10px;}
.over{background:#aaa;border:1px solid #aaa;box-shadow: 0 1px 0 #cccccc inset, 0 1px 2px rgba(0, 0, 0, 0.5);}
.window .title{background-image: linear-gradient(#179f00, #179f00);}
</style>
</head>
<body id="cardnews" onLoad="new Accordian(&#39;basic-accordian&#39;,5,&#39;header_highlight&#39;);" class="mode_webapp">
<div class="qiandaobanner">
	<a href="javascript:history.go(-1);"><img src="<?php echo ($thisCard["vip"]); ?>" ></a>
</div>
<header>
	<nav id="nav_1" class="p_10">
		<ul class="box">
			<li><a href="index.php?g=Wap&m=Card&a=integral&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>&type=<?php echo ($type); ?>&is_use=0" class="<?php if($is_use == '0'): ?>on<?php endif; ?>">未使用</a></li>
			<li><a href="index.php?g=Wap&m=Card&a=integral&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($thisCard["id"]); ?>&type=<?php echo ($type); ?>&is_use=1" class="<?php if($is_use == '1'): ?>on<?php endif; ?>">已使用</a></li>
		</ul>
	</nav>
</header>
<div id="basic-accordian">
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div id="test<?php echo ($item["id"]); ?>-header" class="accordion_headings  <?php if ($item['id']==$firstItemID){?>header_highlight<?php } ?>">
<div class="tab gift">
<span class="title"><?php echo ($item["title"]); ?><p>有效期至<?php echo (date('Y年m月d日',$item["over_time"])); if($item["isovertime"] == 1): ?>(已过期)<?php endif; ?></p></span>
</div>
<div id="test<?php echo ($item["id"]); ?>-content">
<div class="accordion_child">
<img src="<?php echo ($item["pic"]); ?>" class="pic">
<div id="queren<?php echo ($item["id"]); ?>" style="display:none;">
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
									<a id="showcard0" class="submit" href="javascript:void(0)" onclick="use(<?php echo ($item["id"]); ?>)">确定使用</a>
								</p><?php endif; ?>
							</div>
							<p class="explain_sn" id="shiyong<?php echo ($item["id"]); ?>" style="width: 100%;margin: 0px auto;color: #fff;">
                                <?php if($item["isovertime"] == 1): ?><a style="height: 25px;line-height: 25px;" class="submit over" href="###" onclick="javascript:void(0);">已经过期了</a>
                                    <?php else: ?>
                                    <a style="height: 25px;line-height: 25px;" class="submit" href="###" onclick="this.style.display='none';document.getElementById('queren<?php echo ($item["id"]); ?>').style.display='block'">点击立即使用</a><?php endif; ?>
			           		</p>
<b>详情说明</b>
<ul style="min-height:200px;"><?php echo ($item["useinfo"]); ?></ul>
<div style="clear:both;height:20px;"></div>
</div> 
<div style="clear:both;height:20px;"></div>
</div>
</div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<script>
var jQ = jQuery.noConflict();
function use(i){
	var username =  jQ('#username'+i).val();
	var password =  jQ('#password'+i).val();
	var notes 	 =  jQ('#notes'+i).val();
	if(!username){
		alert('请输入商家用户名');
		return false;
	}
	if(!password){
		alert('请输入商家密码');
		return false;
	}
	var itemid=i;
	var submitData = {
			password:password,
			record_id:itemid,
			username:username,
			notes:notes,
			coupon_type:3,
		};	
	jQ.post('/index.php?g=Wap&m=Card&a=action_useIntergral&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($token); ?>', submitData,
	function(data) {
		if (data.success == 1) {
			document.getElementById('queren'+i).style.display='none';
			document.getElementById('password'+i).value = '';
			alert(data.msg);
		}else{
			alert(data.msg);
		}
	}, "json");
}
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