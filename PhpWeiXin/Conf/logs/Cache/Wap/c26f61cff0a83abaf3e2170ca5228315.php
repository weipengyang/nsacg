<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>会员卡</title>
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>

<link href="./tpl/static/tpl/com/css/iscroll.css" rel="stylesheet" type="text/css" />
<script src="./tpl/static/tpl/com/js/iscroll.js" type="text/javascript"></script>
<style>
.gifts{padding:5px;color:#ff0000;border-top: 1px dashed #efefef;}
.gifts a{color:#ff0000;width: 100%;}
</style>
</head>
<script type="text/javascript">
var myScroll;

function loaded() {
myScroll = new iScroll('wrapper', {
snap: true,
momentum: false,
hScrollbar: false,
onScrollEnd: function () {
document.querySelector('#indicator > li.active').className = '';
document.querySelector('#indicator > li:nth-child(' + (this.currPageX+1) + ')').className = 'active';
}
 });
 
}

document.addEventListener('DOMContentLoaded', loaded, false);
</script>
<body id="cardunion" class="mode_webapp2" >


<!--	focus start	-->
 
<div class="banner">
<div id="wrapper">
<div id="scroller">
<ul id="thelist">
<?php if(is_array($flash)): $i = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?><li><p><?php echo ($so["info"]); ?></p><a href="<?php echo (($so["url"])?($so["url"]):'javascript:void(0)'); ?>"><img src="<?php echo ($so["img"]); ?>" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
</div>
</div>


<div id="nav">
<div id="prev" onclick="myScroll.scrollToPage('prev', 0,400,2);return false">&larr; prev</div>
<ul id="indicator">
	<?php if(is_array($flash)): $i = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?><li <?php if($i == 1): ?>class="active"<?php endif; ?> ></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<div id="next" onclick="myScroll.scrollToPage('next', 0);return false">next &rarr;</div>
</div>

<div class="clr"></div>
</div>

<!--	focus End	-->


<div class="cardexplain"> 
<ul class="round">
<li><?php if($cardsCount == 0): ?><a href="###"><span>您还没有领取任何会员卡</span></a><?php else: ?><a href="/index.php?g=Wap&m=Card&a=index&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&mycard=1"><span>我的会员卡<em class="ok"><?php echo ($cardsCount); ?></em></span></a><?php endif; ?></li>
</ul>
<ul class="round">
<?php if($allCardsCount != 0): if(is_array($allCards)): $i = 0; $__LIST__ = $allCards;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?><li class="dandanb"><a href="/index.php?g=Wap&m=Card&a=card&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($c["id"]); ?>"><span class="none"><img src="<?php echo ($c["logo"]); ?>"><h2><?php echo ($c["cardname"]); ?></h2>
<p><?php echo ($c["msg"]); ?></p> <?php if($c['applied']){ ?><em class="error">用卡</em><?php }else{ ?><em class="ok">领卡</em><?php } ?></span></a>
<?php if($c["gifts"] > 0): ?><a href="/index.php?g=Wap&m=Card&a=gifts&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($c["id"]); ?>"><div class="gifts">查看正在进行的开卡赠送活动</div></a><?php endif; ?>
</li><?php endforeach; endif; else: echo "" ;endif; ?>
<?php else: ?>
<li class="dandanb"><a href="###"><span>商家暂时未设置会员卡</span></a></li><?php endif; ?>
</ul>


<ul class="round">
<li class="tel"><a href="tel:<?php echo ($thisCompany["tel"]); ?>"><span><?php if($thisCompany["tel"] != ''): echo ($thisCompany["tel"]); else: ?>商家未设置电话<?php endif; ?></span></a></li>
<li class="address"><a href="/index.php?g=Wap&m=Card&a=companyMap&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&companyid=<?php echo ($thisCompany["id"]); ?>"><span><?php if($thisCompany["address"] != ''): echo ($thisCompany["address"]); else: ?>商家未设置地址<?php endif; ?></span></a></li>
<li class="detail"><a href="/index.php?g=Wap&m=Card&a=companyDetail&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&companyid=<?php echo ($thisCompany["id"]); ?>"><span>查看详情</span></a></li>
</ul>
</div>
<script>


var count = document.getElementById("thelist").getElementsByTagName("img").length;	
var count2 = document.getElementsByClassName("menuimg").length;


for(i=0;i<count;i++){
 document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:"+document.body.clientWidth+"px";

}
for(i=0;i<count2;i++){
  
document.getElementsByClassName("menuimg").item(i).style.cssText = " HEIGHT:"+(document.body.clientWidth/320)*111+"px";
document.getElementsByClassName("menumesg").item(i).style.cssText = " HEIGHT:"+(document.body.clientWidth/320)*111+"px";
 
}

document.getElementById("scroller").style.cssText = " width:"+document.body.clientWidth*count+"px";


 setInterval(function(){
myScroll.scrollToPage('next', 0,400,count);
},3500 );

window.onresize = function(){ 
for(i=0;i<count;i++){
document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:"+document.body.clientWidth+"px";

}
for(i=0;i<count2;i++){
 
 
document.getElementsByClassName("menuimg").item(i).style.cssText = " HEIGHT:"+(document.body.clientWidth/320)*111+"px";
document.getElementsByClassName("menumesg").item(i).style.cssText = " HEIGHT:"+(document.body.clientWidth/320)*111+"px";
  
}

 document.getElementById("scroller").style.cssText = " width:"+document.body.clientWidth*count+"px";
} 

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