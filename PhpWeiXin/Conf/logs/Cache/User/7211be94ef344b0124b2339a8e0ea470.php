<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> <?php echo ($f_siteTitle); ?> <?php echo ($f_siteName); ?></title>
<meta name="keywords" content="<?php echo ($f_metaKeyword); ?>" />
<meta name="description" content="<?php echo ($f_metaDes); ?>" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
<script>var SITEURL='';</script>

<script src="<?php echo RES;?>/js/common.js" type="text/javascript"></script>
</head>
<body id="nv_member" class="pg_CURMODULE">
	
<div id="wp" class="wp">
	<?php if($usertplid == 0): ?><link href="<?php echo ($staticPath); echo ltrim(RES,'.');?>/css/style.css?id=103" rel="stylesheet" type="text/css" />
	<?php else: ?>
		<link href="<?php echo ($staticPath); echo ltrim(RES,'.');?>/css/style-<?php echo ($usertplid); ?>.css?id=103" rel="stylesheet" type="text/css" /><?php endif; ?>
<link rel="stylesheet" type="text/css" href="<?php echo ($staticPath); echo ltrim(RES,'.');?>/css/style_2_common.css?BPm" />
<style>
a.a_upload,a.a_choose{border:1px solid #3d810c;box-shadow:0 1px #CCCCCC;-moz-box-shadow:0 1px #CCCCCC;-webkit-box-shadow:0 1px #CCCCCC;cursor:pointer;display:inline-block;text-align:center;vertical-align:bottom;overflow:visible;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;vertical-align:middle;background-color:#f1f1f1;background-image: -webkit-linear-gradient(bottom, #CCC 0%, #E5E5E5 3%, #FFF 97%, #FFF 100%); background-image: -moz-linear-gradient(bottom, #CCC 0%, #E5E5E5 3%, #FFF 97%, #FFF 100%); background-image: -ms-linear-gradient(bottom, #CCC 0%, #E5E5E5 3%, #FFF 97%, #FFF 100%); color:#000;border:1px solid #AAA;padding:2px 8px 2px 8px;text-shadow: 0 1px #FFFFFF;font-size: 14px;line-height: 1.5;
}

.pages{padding:3px;margin:3px;text-align:center;}
.pages a{border:#eee 1px solid;padding:2px 5px;margin:2px;color:#036cb4;text-decoration:none;}
.pages a:hover{border:#999 1px solid;color:#666;}
.pages a:active{border:#999 1px solid;color:#666;}
.pages .current{border:#036cb4 1px solid;padding:2px 5px;font-weight:bold;margin:2px;color:#fff;background-color:#036cb4;}
.pages .disabled{border:#eee 1px solid;padding:2px 5px;margin:2px;color:#ddd;}
</style>
 <script src="<?php echo STATICS;?>/jquery-1.4.2.min.js" type="text/javascript"></script>
 <?php if(session('isQcloud') == true): ?><link type="text/css" rel="stylesheet" href="http://qzonestyle.gtimg.cn/qcloud/app/open/v1/css/wxcloud.min.css" />


<style>
.px {
	background:#fff;

	border-color:#c9c9c9;

}


input[type=radio] {

	border-radius:55px;

	border: none;

}
.btnGreen {
	border:1px solid #FFFFFF;
	box-shadow:0 1px 1px #0A8DE4;
	-moz-box-shadow:0 1px 1px #0A8DE4;
	-webkit-box-shadow:0 1px 1px #0A8DE4;
	padding:5px 20px;
	cursor:pointer;
	display:inline-block;
	text-align:center;
	vertical-align:bottom;
	overflow:visible;
	border-radius:3px;
	-moz-border-radius:3px;
	-webkit-border-radius:3px;
*zoom:1;
	background-color:#5ba607;
	background-image:linear-gradient(bottom, #107BAD  3%, #18C2D1 97%, #18C2D1 100%);
	background-image:-moz-linear-gradient(bottom, #107BAD  3%, #0A8DE40 97%, #18C2D1 100%);
	background-image:-webkit-linear-gradient(bottom, #107BAD  3%,#0A8DE4 97%, #18C2D1 100%);
	color:#fff; font-size:14px; line-height: 1.5;
}
.btnGreen:hover {
	background-color:#5ba607;
	background-image:linear-gradient(bottom, #2F8BC9 3%, #2F8BC9 97%, #6AA2D6  100%);
	background-image:-moz-linear-gradient(bottom, #2F8BC9 3%, #2F8BC9 97%, #6AA2D6  100%);
	background-image:-webkit-linear-gradient(bottom, #2F8BC9 3%, #2F8BC9 97%, #6AA2D6  100%);
	color:#fff
}
.btnGreen:active {
	background-color:#5ba607;
	background-image:linear-gradient(bottom, #69b310 3%, #3d810c 97%, #fff 100%);
	background-image:-moz-linear-gradient(bottom, #69b310 3%, #3d810c 97%, #fff 100%);
	background-image:-webkit-linear-gradient(bottom, #69b310 3%, #3d810c 97%, #fff 100%);
	color:#fff
}

.btnGreen{border:1px solid #3d810c;box-shadow:0 1px 1px #aaa;-moz-box-shadow:0 1px 1px #aaa;-webkit-box-shadow:0 1px 1px #aaa;padding:5px 20px;cursor:pointer;display:inline-block;text-align:center;vertical-align:bottom;overflow:visible;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;*zoom:1;background-color:#5ba607;background-image:linear-gradient(bottom,#4d910c 3%,#69b310 97%,#fff 100%);background-image:-moz-linear-gradient(bottom,#4d910c 3%,#69b310 97%,#fff 100%);background-image:-webkit-linear-gradient(bottom,#4d910c 3%,#69b310 97%,#fff 100%);color:#fff;font-size:14px;line-height:1.5;}.btnGreen:hover{background-color:#5ba607;background-image:linear-gradient(bottom,#3d810c 3%,#69b310 97%,#fff 100%);background-image:-moz-linear-gradient(bottom,#3d810c 3%,#69b310 97%,#fff 100%);background-image:-webkit-linear-gradient(bottom,#3d810c 3%,#69b310 97%,#fff 100%);color:#fff}.btnGreen:active{background-color:#5ba607;background-image:linear-gradient(bottom,#69b310 3%,#3d810c 97%,#fff 100%);background-image:-moz-linear-gradient(bottom,#69b310 3%,#3d810c 97%,#fff 100%);background-image:-webkit-linear-gradient(bottom,#69b310 3%,#3d810c 97%,#fff 100%);color:#fff}

</style><?php endif; ?>
<?php
if (!isset($_SESSION['isQcloud'])){ ?>
<div class="topbg">
<div class="top">
<div class="toplink">
<style>
<?php if($usertplid == 1): ?>.topbg{background:url(<?php echo ($staticPath); ?>/tpl/static/newskin/images/top.gif) repeat-x; height:101px; /*box-shadow:0 0 10px #000;-moz-box-shadow:0 0 10px #000;-webkit-box-shadow:0 0 10px #000;*/}
.top {
    margin: 0px auto; width: 988px; height: 101px;
}

.top .toplink{ height:30px; line-height:27px; color:#999; font-size:12px;}
.top .toplink .welcome{ float:left;}
.top .toplink .memberinfo{ float:right;}
.top .toplink .memberinfo a{ color:#999;}
.top .toplink .memberinfo a:hover{ color:#F90}
.top .toplink .memberinfo a.green{ background:none; color:#0C0}

.top .logo {width: 990px; color: #333; height:70px; font-size:16px;}
.top h1{ font-size:25px;float:left; width:230px; margin:0; padding:0; margin-top:6px; }
.top .navr {WIDTH:750px; float:right; overflow:hidden;}
.top .navr ul{ width:850px;}
.navr li {text-align:center; float: left; height:70px; font-size:1em; width:103px; margin-right:5px;}
.navr li a {width:103px; line-height:70px; float: left; height:100%; color: #333; font-size: 1em; text-decoration:none;}
.navr li a:hover { background:#ebf3e4;}
.navr li.menuon {background:#ebf3e4; display:block; width:103px;}
.navr li.menuon a{color:#333;}
.navr li.menuon a:hover{color:#333;}
.banner{height:200px; text-align:center; border-bottom:2px solid #ddd;}
.hbanner{ background: url(img/h2003070126.jpg) center no-repeat #B4B4B4;}
.gbanner{ background: url(img/h2003070127.jpg) center no-repeat #264C79;}
.jbanner{ background: url(img/h2003070128.jpg) center no-repeat #E2EAD5;}
.dbanner{ background: url(img/h2003070129.jpg) center no-repeat #009ADA;}
.nbanner{ background: url(img/h2003070130.jpg) center no-repeat #ffca22;}
<?php else: ?>

.topbg{BACKGROUND-IMAGE: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/top.png);BACKGROUND-REPEAT: repeat-x; height:124px; box-shadow:0 0 10px #000;-moz-box-shadow:0 0 10px #000;-webkit-box-shadow:0 0 10px #000;}
.top {
	MARGIN: 0px auto; WIDTH: 988px; HEIGHT: 124px;
}

.top .toplink{ height:27px; line-height:27px; color:#999; font-size:12px;}
.top .toplink .welcome{ float:left;}
.top .toplink .memberinfo{ float:right;}
.top .toplink .memberinfo a{ color:#999;}
.top .toplink .memberinfo a:hover{ color:#F90}
.top .toplink .memberinfo a.green{ background:none; color:#0C0}

.top .logo {WIDTH: 990px;COLOR: #333; height:70px;  FONT-SIZE:16px; PADDING-TOP:25px}
.top h1{ font-size:25px; margin-top:20px; float:left; width:230px; margin:0; padding:0;}
.top .navr {WIDTH:750px; float:right; overflow:hidden; margin-top:10px;}
.top .navr ul{ width:850px;}
.navr LI {TEXT-ALIGN:center;FLOAT: left;HEIGHT:32px;FONT-SIZE:1em;width:103px; margin-right:5px;}
.navr LI A {width:103px;TEXT-ALIGN:center; LINE-HEIGHT:30px; FLOAT: left;HEIGHT:32px;COLOR: #333; FONT-SIZE: 1em;TEXT-DECORATION: none
}
.navr LI A:hover {BACKGROUND: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/navhover.gif) center no-repeat;color:#009900;}
.navr LI.menuon {BACKGROUND: url(<?php echo ($staticPath); echo ltrim(RES,'.');?>/images/navon.gif) center no-repeat; display:block;width:103px;HEIGHT:32px;}
.navr LI.menuon a{color:#FFF;}
.navr LI.menuon a:hover{BACKGROUND: url(img/navon.gif) center no-repeat;}
.banner{height:200px; text-align:center; border-bottom:2px solid #ddd;}
.hbanner{ background: url(img/h2003070126.jpg) center no-repeat #B4B4B4;}
.gbanner{ background: url(img/h2003070127.jpg) center no-repeat #264C79;}
.jbanner{ background: url(img/h2003070128.jpg) center no-repeat #E2EAD5;}
.dbanner{ background: url(img/h2003070129.jpg) center no-repeat #009ADA;}
.nbanner{ background: url(img/h2003070130.jpg) center no-repeat #ffca22;}<?php endif; ?>
</style>
<div class="welcome">欢迎使用多用户微信营销服务平台!</div>
    <div class="memberinfo"  id="destoon_member">	
		<?php if($_SESSION[uid]==false): ?><a href="<?php echo U('Index/login');?>">登录</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo U('Index/login');?>">注册</a>
			<?php else: ?>
			你好,<a href="/#" hidefocus="true"  ><span style="color:red"><?php echo (session('uname')); ?></span></a>（uid:<?php echo (session('uid')); ?>）
			<a href="/#" onclick="Javascript:window.open('<?php echo U('System/Admin/logout');?>','_blank')" >退出</a><?php endif; ?>	
	</div>
</div>
    <div class="logo">
        <div style="float:left"></div>
            <h1><a href="/" title="多用户微信营销服务平台"><img src="<?php echo ($f_logo); ?>" height="55" /></a></h1>
            <div class="navr">
        <ul id="topMenu">           
            <li <?php if((ACTION_NAME == 'index') and (GROUP_NAME == 'Home')): ?>class="menuon"<?php endif; ?> ><a href="/">首页</a></li>
                <li <?php if((ACTION_NAME) == "fc"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/fc');?>">功能介绍</a></li>
                <li <?php if((ACTION_NAME) == "about"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/about');?>">关于我们</a></li>
                <li <?php if((ACTION_NAME) == "price"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/price');?>">资费说明</a></li>
                <li <?php if((ACTION_NAME) == "common"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/common');?>">微信导航</a></li>
                <li <?php if((GROUP_NAME) == "User"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('User/Index/index');?>">管理中心</a></li>
                <li <?php if((ACTION_NAME) == "help"): ?>class="menuon"<?php endif; ?>><a href="<?php echo U('Home/Index/help');?>">帮助中心</a></li>
            
            </ul>
        </div>
        </div>
    </div>
</div>
<div id="aaa"></div>
<?php
} ?>

  <!--中间内容-->

  <div class="contentmanage"<?php if (isset($_SESSION['isQcloud'])){?> style="width:100%"<?php }?>>
  <?php
if (!isset($_SESSION['isQcloud'])){ ?>
    <div class="developer">
       <div class="appTitle normalTitle2">
        <div class="vipuser">


<div class="logo">
<a href="<?php echo U('Function/welcome',array('token'=>$token));?>"><img src="<?php echo ($wecha["headerpic"]); ?>" width="100" height="100" /></a>
</div>

<div id="nickname">
<strong><a href="<?php echo U('Function/welcome',array('token'=>$token));?>"><?php echo ($wecha["wxname"]); ?></a></strong><a href="#" target="_blank" class="vipimg vip-icon<?php echo $userinfo['taxisid']-1; ?>" title=""></a></div>
<div id="weixinid">微信号:<?php echo ($wecha["weixin"]); ?></div>
</div>

        <div class="accountInfo">
<table class="vipInfo" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td><strong>VIP有效期：</strong><?php echo (date("Y-m-d",$thisUser["viptime"])); ?></td>
<td><strong>图文自定义：</strong><?php echo ($thisUser["diynum"]); ?>/<?php echo ($userinfo["diynum"]); ?></td>
<td><strong>活动创建数：</strong><?php echo ($thisUser["activitynum"]); ?>/<?php echo ($userinfo["activitynum"]); ?></td>
<td><strong>请求数：</strong><?php echo ($thisUser["connectnum"]); ?>/<?php echo ($userinfo["connectnum"]); ?></td>
</tr>
<tr>
<td><strong>请求数剩余：</strong><?php echo ($userinfo['connectnum']-$_SESSION['connectnum']); ?></td>
<td><strong>已使用：</strong><?php echo $_SESSION['diynum']; ?></td>
<td><strong>当月赠送请求数：</strong><?php echo ($userinfo["connectnum"]); ?></td>
<td><strong>当月剩余请求数：</strong><?php echo $userinfo['connectnum']-$_SESSION['connectnum']; ?></td>
</tr>

</table>
    </div>
        <div class="clr"></div>
      </div>
      <!--左侧功能菜单-->


<style type="text/css">
#sideBar{
border-right: 0px solid #D3D3D3 !important;
float: left;
padding: 0 0 10px 0;
width: 170px;
background: #fff;
}
.tableContent {
background: none repeat scroll 0 0 #f5f6f7;
padding: 0;
}
.tableContent .content {
border-left: 1px solid #D7DDE6 !important;
}
ul#menu, ul#menu ul {
  list-style-type:none;
  margin: 0;
  padding: 0;
  background: #fff;
}

ul#menu a {
  display: block;
  text-decoration: none;
}

ul#menu li {
  margin: 1px;
}
ul#menu li ul li{
  margin: 1px 0;
}
ul#menu li a {
  background: #EBEEF1;
  color: #464D6A;
  padding: 0.5em;
}
ul#menu li .nav-header{
font-size:14px;
border-bottom: 1px solid #D7DDE6;
}
ul#menu li .nav-header:hover {
  background: #DDE4EB;
}

ul#menu li ul li a {
  background: #FCFCFC;
  color: #8288A4;
  padding-left: 20px;
}
ul#menu li ul li:last-child {
    border-bottom: 1px solid #D7DDE6;
}
ul#menu li ul li a:hover {
  background: #fff;
  border-left: 5px #4A98E0 solid;

}
ul#menu li.selected a{
  background: #fff;
  border-left: 5px #4A98E0 solid;
  padding-left: 15px;
  color: #4A98E0;
}
.code { border: 1px solid #ccc; list-style-type: decimal-leading-zero; padding: 5px; margin: 0; }
.code code { display: block; padding: 3px; margin-bottom: 0; }
.code li { background: #ddd; border: 1px solid #ccc; margin: 0 0 2px 2.2em; }
.indent1 { padding-left: 1em; }
.indent2 { padding-left: 2em; }
.tableContent .content{min-height: 1328px;}

a.nav-header{background:url(/tpl/static/images/arrow_click.png) center right no-repeat;cursor:pointer}
a.nav-header-current{background:url(/tpl/static/images/arrow_unclick.png) center right no-repeat;}
</style>
<?php
} ?>
      <div class="tableContent">
        <?php
if (!isset($_SESSION['isQcloud'])){ ?>
        <!--左侧功能菜单-->
 <div  class="sideBar" id="sideBar">
<div class="catalogList">
<ul id="menu">
<?php
$menus=array( array( 'name'=>'基础设置', 'iconName'=>'base', 'display'=>0, 'subs'=>array( array('name'=>'功能管理','link'=>U('Function/index',array('token'=>$token,'id'=>session('wxid'))),'new'=>0,'selectedCondition'=>array('m'=>'Function','a'=>'index')), array('name'=>'关注时回复与帮助','link'=>U('Areply/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Areply')), array('name'=>'微信－文本回复','link'=>U('Text/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Text')), array('name'=>'微信－图文回复','link'=>U('Img/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Img','a'=>'index')), array('name'=>'微信－图文编辑器','link'=>'./wx/index.html?token='.$token,'new'=>1,'selectedCondition'=>array('m'=>'Img','a'=>'index')), array('name'=>'微信－多图文回复','link'=>U('Img/multi',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Img','a'=>'multi')), array('name'=>'微信－语音回复','link'=>U('Voiceresponse/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Voiceresponse')), array('name'=>'微信－群发消息','link'=>U('Message/sendHistory',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Message')), array('name'=>'微信－模板消息','link'=>U('TemplateMsg/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'TemplateMsg')), array('name'=>'自定义LBS回复','link'=>U('Company/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Company')), array('name'=>'自定义菜单','link'=>U('Diymen/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Diymen')), array('name'=>'微信用户信息授权','link'=>U('Auth/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Auth')), array('name'=>'回答不上来的配置','link'=>U('Other/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Other')), )), array( 'name'=>'微网站', 'iconName'=>'site', 'display'=>0, 'subs'=>array( array('name'=>'首页回复配置','f'=>'Home','link'=>U('Home/set',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Home','g'=>'User','a'=>'set')), array('name'=>'分类管理','f'=>'Home','link'=>U('Classify/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Classify')), array('name'=>'模板管理','f'=>'Home','link'=>U('Tmpls/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Tmpls')), array('name'=>'文章管理','f'=>'Home','link'=>U('Img/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Img','a'=>'index')), array('name'=>'首页幻灯片','f'=>'Home','link'=>U('Flash/index',array('token'=>$token,'tip'=>1)),'new'=>0,'selectedCondition'=>array('m'=>'Flash','tip'=>1)), array('name'=>'轮播背景图','f'=>'Home','link'=>U('Flash/index',array('token'=>$token,'tip'=>2)),'new'=>1,'selectedCondition'=>array('m'=>'Flash','tip'=>2)), array('name'=>'底部导航菜单','f'=>'Home','link'=>U('Catemenu/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Catemenu')), array('name'=>'版权设置','f'=>'Home','link'=>U('Home/plugmenu',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Home','g'=>'User','a'=>'plugmenu')), array('name'=>'微场景','f'=>'Home','link'=>U('Live/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Live')), array('name'=>'微名片','link'=>U('Vcard/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Vcard')), array('name'=>'在线预览','link'=>U('Yulan/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Yulan')), )), array( 'name'=>'最新功能', 'iconName'=>'scene', 'display'=>0, 'subs'=>array( array('name'=>'微名片','link'=>U('Vcard/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Vcard')), array('name'=>'活动报名','link'=>U('Baoming/index',array('token'=>$token,'id'=>session('wxid'))),'new'=>1,'selectedCondition'=>array('m'=>'Baoming')), array('name'=>'RippleOS设置','link'=>U('RippleOS/set',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'RippleOS','a'=>'set')), )), array( 'name'=>'PC网站[独立]', 'iconName'=>'scene', 'display'=>0, 'subs'=>array( array('name'=>'基本设置','f'=>'Web','link'=>U('Web/set',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'set')), array('name'=>'选择模板','f'=>'Web','link'=>U('Web/choose_tpl',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'choose_tpl')), array('name'=>'导航菜单','f'=>'Web','link'=>U('Web/nav',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'nav')), array('name'=>'图集管理','f'=>'Web','link'=>U('Web/flash',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'flash')), array('name'=>'新闻管理','f'=>'Web','link'=>U('Web/news',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'news')), array('name'=>'产品管理','f'=>'Web','link'=>U('Web/product',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'product')), array('name'=>'下载管理','f'=>'Web','link'=>U('Web/down',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'down')), array('name'=>'自定义页面','f'=>'Web','link'=>U('Web/page',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Web','a'=>'page')), )), array( 'name'=>'手机站[独立]', 'iconName'=>'site', 'display'=>0, 'subs'=>array( array('name'=>'手机站配置','f'=>'Phone','link'=>U('Phone/baseSet',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Phone','a'=>'baseSet')) )), array( 'iconName'=>'zhida', 'name'=>'百度直达号', 'display'=>0, 'subs'=>array( array('name'=>'对接配置','f'=>'Zhida','link'=>U('Zhida/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Zhida','a'=>'index')), )), array( 'name'=>'微互动', 'iconName'=>'interact', 'display'=>0, 'subs'=>array( array('name'=>'留言板','f'=>'Reply','link'=>U('Reply/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Reply')), array('name'=>'微论坛','f'=>'Forum','link'=>U('Forum/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Forum')), array('name'=>'3G图集(相册)','f'=>'Photo','link'=>U('Photo/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Photo')), array('name'=>'3G微投票','f'=>'Vote','link'=>U('Vote/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Vote')), array('name'=>'微预约(万能表单,报名)','f'=>'Custom','link'=>U('Custom/record',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Custom')), array('name'=>'微调研','f'=>'Research','link'=>U('Research/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Research')), array('name'=>'祝福贺卡','f'=>'Greeting_card','link'=>U('Greeting_card/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Greeting_card')), array('name'=>'分享管理','f'=>'Share','link'=>U('Share/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Share')), array('name'=>'邀请函','f'=>'Invite','link'=>U('Invite/add',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Invite')), )), array( 'name'=>'行业应用', 'iconName'=>'business', 'display'=>0, 'subs'=>array( array('name'=>'全民经纪人','f'=>'MicroBroker','link'=>U('MicroBroker/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'MicroBroker'),'test'=>0), array('name'=>'微餐饮（无线打印）','f'=>'Repast','link'=>U('Repast/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Repast')), array('name'=>'微外卖[新版]','f'=>'DishOut','link'=>U('DishOut/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'DishOut'),'test'=>0), array('name'=>'360°全景','f'=>'Panorama','link'=>U('Panorama/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Panorama')), array('name'=>'微婚庆（喜帖）','f'=>'Wedding','link'=>U('Wedding/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Wedding')), array('name'=>'微汽车','f'=>'Car','link'=>U('Car/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Car')), array('name'=>'楼盘房产','f'=>'Estate','link'=>U('Estate/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Estate')), array('name'=>'微教育','f'=>'School','link'=>U('School/index',array('token'=>$token,'type'=>'semester')),'new'=>0,'selectedCondition'=>array('m'=>'School')), array('name'=>'微医疗','f'=>'Medical','link'=>U('Medical/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Medical'),'test'=>0), array('name'=>'酒店宾馆','f'=>'Hotels','link'=>U('Hotels/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Hotels')), array('name'=>'通用订单(酒吧KTV)','f'=>'Host','link'=>U('Host/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Host')), array('name'=>'微美容','f'=>'Beauty','link'=>U('Business/index',array('token'=>$token,'type'=>'beauty')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'beauty'),'test'=>0), array('name'=>'微健身','f'=>'Fitness','link'=>U('Business/index',array('token'=>$token,'type'=>'fitness')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'fitness'),'test'=>0,'show'=>1), array('name'=>'微政务','f'=>'Gover','link'=>U('Business/index',array('token'=>$token,'type'=>'gover')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'gover'),'test'=>0,'show'=>1), array('name'=>'微食品','f'=>'Food','link'=>U('Business/index',array('token'=>$token,'type'=>'food')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'food'),'test'=>0), array('name'=>'微旅游','f'=>'Travel','link'=>U('Business/index',array('token'=>$token,'type'=>'travel')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'travel'),'test'=>0), array('name'=>'微花店','f'=>'Flower','link'=>U('Business/index',array('token'=>$token,'type'=>'flower')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'flower'),'test'=>0), array('name'=>'微物业','f'=>'Property','link'=>U('Business/index',array('token'=>$token,'type'=>'property')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'property'),'test'=>0), array('name'=>'微KTV','f'=>'Ktv','link'=>U('Business/index',array('token'=>$token,'type'=>'ktv')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'ktv'),'test'=>0), array('name'=>'微酒吧','f'=>'Bar','link'=>U('Business/index',array('token'=>$token,'type'=>'bar')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'bar'),'test'=>0), array('name'=>'微装修','f'=>'Fitment','link'=>U('Business/index',array('token'=>$token,'type'=>'fitment')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'fitment'),'test'=>0), array('name'=>'微婚庆','f'=>'Wedding','link'=>U('Business/index',array('token'=>$token,'type'=>'wedding')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'wedding'),'test'=>0), array('name'=>'微宠物','f'=>'Affections','link'=>U('Business/index',array('token'=>$token,'type'=>'affections')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'affections'),'test'=>0), array('name'=>'微家政','f'=>'Housekeeper','link'=>U('Business/index',array('token'=>$token,'type'=>'housekeeper')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'housekeeper'),'test'=>0), array('name'=>'微租赁','f'=>'Lease','link'=>U('Business/index',array('token'=>$token,'type'=>'lease')),'new'=>0,'selectedCondition'=>array('m'=>'Business','type'=>'lease'),'test'=>0), array('name'=>'微招聘','link'=>U('Zhaopin/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Zhaopin')), )), array( 'name'=>'微现场', 'iconName'=>'scene', 'display'=>0, 'subs'=>array( array('name'=>'微信签到','f'=>'Signin','link'=>U('Signin/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Signin')), array('name'=>'摇一摇','f'=>'Shake','link'=>U('Shake/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Shake')), array('name'=>'微信墙','f'=>'Wall','link'=>U('Wall/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Wall')), array('name'=>'现场活动','f'=>'Scene','link'=>U('Scene/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Scene')), )), array( 'name'=>'电商系统', 'iconName'=>'store', 'display'=>0, 'subs'=>array( array('name'=>'在线支付设置','link'=>U('Alipay_config/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Alipay_config')), array('name'=>'平台支付对帐','link'=>U('Platform/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Platform')), array('name'=>'微信团购系统','f'=>'Groupon','link'=>U('Groupon/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Groupon')), array('name'=>'微信商城系统','f'=>'Store','link'=>U('Store/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Store')), array('name'=>'商城分佣管理','f'=>'Store','link'=>U('Store/twitter',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Store','a'=>'twitter')), array('name'=>'微信分销系统','f'=>'Distribution','link'=>U('Distribution/statistics',array('token'=>$token)),'new'=>1,'test'=>1,'selectedCondition'=>array('m'=>'Distribution')), array('name'=>'微商圈','f'=>'Market','link'=>U('Market/index',array('token'=>$token)),'test'=>0,'selectedCondition'=>array('m'=>'Market')), array('name'=>'微众筹','f'=>'Crowdfunding','link'=>U('Crowdfunding/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Crowdfunding')), array('name'=>'一元夺宝','f'=>'Unitary','link'=>U('Unitary/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Unitary')), array('name'=>'微秒杀','f'=>'Seckill','link'=>U('Seckill/index',array('token'=>$token,'type'=>'seckill')),'new'=>1,'test'=>1,'selectedCondition'=>array('m'=>'Seckill')), array('name'=>'微信生活圈','f'=>'LivingCircle','link'=>U('LivingCircle/index',array('token'=>$token,'type'=>'LivingCircle')),'new'=>0,'test'=>1,'selectedCondition'=>array('m'=>'LivingCircle')), array('name'=>'微砍价','f'=>'Bargain','link'=>U('Bargain/index',array('token'=>$token,'type'=>'Bargain')),'new'=>1,'selectedCondition'=>array('m'=>'Bargain')), )), array( 'name'=>'微游戏', 'iconName'=>'game', 'display'=>0, 'subs'=>array( array('name'=>'信息设置','link'=>U('Game/config',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Game','a'=>'config')), array('name'=>'我的游戏','link'=>U('Game/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Game','a'=>'index')), array('name'=>'游戏库','link'=>U('Game/gameLibrary',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Game','a'=>'gameLibrary')), )), array( 'name'=>'微粉丝管理CRM', 'iconName'=>'crm', 'display'=>0, 'subs'=>array( array('name'=>'粉丝管理','link'=>U('Wechat_group/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Wechat_group','a'=>'index')), array('name'=>'分组管理','link'=>U('Wechat_group/groups',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Wechat_group','a'=>'groups')), array('name'=>'粉丝行为分析','link'=>U('Wechat_behavior/statistics',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Wechat_behavior')), )), array( 'name'=>'微硬件', 'iconName'=>'hardware', 'display'=>0, 'subs'=>array( array('name'=>'RippleOS设置','link'=>U('RippleOS/set',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'RippleOS','a'=>'set')), array('name'=>'微信wifi设置','link'=>U('Router/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Router')), array('name'=>'无线打印机','link'=>U('Hardware/orderprint',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Hardware','a'=>'orderprint')), array('name'=>'照片打印机','link'=>U('Hardware/photoprint',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Hardware','a'=>'photoprint')), )), array( 'name'=>'微渠道', 'iconName'=>'ditch', 'display'=>0, 'subs'=>array( array('name'=>'渠道二维码','link'=>U('Recognition/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Recognition')), array('name'=>'DIY宣传页','link'=>U('Adma/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Adma')), )), array( 'name'=>'微客服', 'iconName'=>'service', 'display'=>0, 'subs'=>array( array('name'=>'人工客服','link'=>U('ServiceUser/wechatService',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'ServiceUser')), )), array( 'name'=>'微活动', 'iconName'=>'active', 'display'=>0, 'subs'=>array( array('name'=>'分享助力','f'=>'Helping','link'=>U('Helping/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Helping')), array('name'=>'人气冲榜','f'=>'Popularity','link'=>U('Popularity/index',array('token'=>$token)),'new'=>1,'test'=>0,'selectedCondition'=>array('m'=>'Popularity')), array('name'=>'幸运大转盘','f'=>'Lottery','link'=>U('Lottery/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Lottery')), array('name'=>'幸运九宫格','f'=>'Jiugong','link'=>U('Jiugong/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Jiugong')), array('name'=>'优惠券','f'=>'Coupon','link'=>U('Coupon/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Coupon')), array('name'=>'刮刮卡','f'=>'Guajiang','link'=>U('Guajiang/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Guajiang')), array('name'=>'幸运水果机','f'=>'LuckyFruit','link'=>U('LuckyFruit/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'LuckyFruit')), array('name'=>'砸金蛋','f'=>'GoldenEgg','link'=>U('GoldenEgg/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'GoldenEgg')), array('name'=>'走鹊桥','f'=>'AppleGame','link'=>U('AppleGame/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'AppleGame')), array('name'=>'摁死小情侣','f'=>'Lovers','link'=>U('Lovers/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Lovers')), array('name'=>'中秋吃月饼','f'=>'Autumn','link'=>U('Autumn/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Autumn')), array('name'=>'拆礼盒','f'=>'Autumns','link'=>U('Autumns/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Autumns')), array('name'=>'一战到底','f'=>'Problem','link'=>U('Problem/index',array('token'=>$token)),'new'=>0,'test'=>0,'selectedCondition'=>array('m'=>'Problem')), array('name'=>'微信红包','f'=>'Red_packet','link'=>U('Red_packet/index',array('token'=>$token)),'new'=>0,'test'=>0,'selectedCondition'=>array('m'=>'Red_packet')), array('name'=>'惩罚台','f'=>'Punish','link'=>U('Punish/index',array('token'=>$token)),'new'=>0,'test'=>0,'selectedCondition'=>array('m'=>'Punish')), array('name'=>'趣味测试','f'=>'Test','link'=>U('Test/index',array('token'=>$token,'type'=>'Test')),'new'=>0,'test'=>1,'selectedCondition'=>array('m'=>'Test')), )), array( 'name'=>'会员卡', 'iconName'=>'card', 'display'=>0, 'subs'=>array( array('name'=>'会员卡','f'=>'Member_card','link'=>U('Member_card/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Member_card'),'exceptCondition'=>array('a'=>'replyInfoSet,focus,custom,center,member')), array('name'=>'会员中心','f'=>'Member_card','link'=>U('Member_card/center',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Member_card','a'=>'center')), array('name'=>'回复设置','f'=>'Member_card','link'=>U('Member_card/replyInfoSet',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Member_card','a'=>'replyInfoSet')), array('name'=>'幻灯片广告','f'=>'Member_card','link'=>U('Member_card/focus',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Member_card','a'=>'focus')), array('name'=>'自定义输入项','f'=>'Member_card','link'=>U('Member_card/custom',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Member_card','a'=>'custom')), )), array( 'name'=>'数据魔方', 'iconName'=>'datacube', 'display'=>0, 'subs'=>array( array('name'=>'请求数详情','link'=>U('Requerydata/index',array('token'=>$token)),'new'=>0,'selectedCondition'=>array('m'=>'Requerydata')), array('name'=>'粉丝行为分析','link'=>U('Wechat_behavior/statistics',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Wechat_behavior','a'=>'statistics')), )), array( 'name'=>'二次开发', 'iconName'=>'secondary', 'display'=>0, 'subs'=>array( array('name'=>'融合第三方','link'=>U('Api/index',array('token'=>$token)),'new'=>1,'selectedCondition'=>array('m'=>'Api')), )), ); ?>
<?php
 foreach ($menus as $mk => $mv){ foreach ($mv['subs'] as $mvk => $mvv){ if ($mvv['selectedCondition']['m'] == 'Web') { $path=str_replace($_SERVER['SCRIPT_NAME'],'',dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR.'PigCms'.DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR.'Action'.DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR; if (!file_exists($path.'Web_indexAction.class.php')) { unset($menus[$mk]); } } if(!isset($not_exist)) $not_exist=array(); if(in_array($mvv['selectedCondition']['m'],$not_exist)){ if($mvv['selectedCondition']['m'] == 'Home'){ unset($menus[$mk]); }else{ unset($menus[$mk]['subs'][$mvk]); } }elseif($mvv['selectedCondition']['m'] == 'Business'){ if($mvv['selectedCondition']['type'] == 'wedding') $mvv['selectedCondition']['type'] = 'buswedd'; if(in_array(ucfirst($mvv['selectedCondition']['type']),$not_exist)){ unset($menus[$mk]['subs'][$mvk]); } } if($menus[$mk]['subs'] == NULL){ unset($menus[$mk]); } } } $i=0; $parms=$_SERVER['QUERY_STRING']; $parms1=explode('&',$parms); $parmsArr=array(); if ($parms1){ foreach ($parms1 as $p){ $parms2=explode('=',$p); $parmsArr[$parms2[0]]=$parms2[1]; } } $subMenus=array(); $t=0; $currentMenuID=0; $currentParentMenuID=0; foreach ($menus as $m){ $loopContinue=1; if ($m['subs']){ $st=0; foreach ($m['subs'] as $s){ $includeArr=1; if(!isset($parmsArr)) $parmsArr=array(); if ($s['selectedCondition']){ foreach ($s['selectedCondition'] as $condition){ if (!in_array($condition,$parmsArr)){ $includeArr=0; break; } } } if ($includeArr){ if ($s['exceptCondition']){ foreach ($s['exceptCondition'] as $epkey=>$eptCondition){ if ($epkey=='a'){ $parm_a_values=explode(',',$eptCondition); if ($parm_a_values){ if (in_array($parmsArr['a'],$parm_a_values)){ $includeArr=0; break; } } }else { if (in_array($eptCondition,$parmsArr)){ $includeArr=0; break; } } } } } if ($includeArr){ $currentMenuID=$st; $currentParentMenuID=$t; $loopContinue=0; break; } $st++; } if ($loopContinue==0){ break; } } $t++; } foreach ($menus as $m){ $displayStr=''; if ($currentParentMenuID!=0||0!=$currentMenuID){ $m['display']=0; } if (!$m['display']){ $displayStr=' style="display:none"'; } if ($currentParentMenuID==$i){ $displayStr=''; } $aClassStr=''; if ($displayStr){ $aClassStr=' nav-header-current'; } if($i == 0){ echo '<a class="nav-header'.$aClassStr.'" style="border-top:none !important;"><b class="'.$m['iconName'].'"></b>'.$m['name'].'</a><ul class="ckit"'.$displayStr.'>'; }else{ echo '<a class="nav-header'.$aClassStr.'"><b class="'.$m['iconName'].'"></b>'.$m['name'].'</a><ul class="ckit"'.$displayStr.'>'; } if ($m['subs']){ $j=0; foreach ($m['subs'] as $s){ $selectedClassStr='subCatalogList'; if ($currentParentMenuID==$i&&$j==$currentMenuID){ $selectedClassStr='selected'; } $newStr=''; if ($s['new']){ $newStr.='<span class="new"></span>'; } if ($s['name']!='微信墙'&&$s['name']!='摇一摇'&&$s['name']!='现场活动'){ echo '<li class="'.$selectedClassStr.'"> <a href="'.$s['link'].'">'.$s['name'].'</a>'.$newStr.'</li>'; }else { switch ($s['name']){ case '微信墙': case '摇一摇': case '现场活动': $path=str_replace($_SERVER['SCRIPT_NAME'],'',dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR.'PigCms'.DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR.'Action'.DIRECTORY_SEPARATOR.'User'.DIRECTORY_SEPARATOR; if (file_exists($path.'WallAction.class.php')&&file_exists($path.'ShakeAction.class.php')){ echo '<li class="'.$selectedClassStr.'"> <a href="'.$s['link'].'">'.$s['name'].'</a>'.$newStr.'</li>'; } break; } } if ($s['name']=='模板管理'&&is_dir($_SERVER['DOCUMENT_ROOT'].'/cms')&&!strpos($_SERVER['HTTP_HOST'],'pigcms')){ echo '<li class="subCatalogList"> <a href="/cms/manage/index.php">高级模板</a><span class="new"></span></li>'; } $j++; } } echo '</ul>'; $i++; } ?>

<div style="clear:both"></div>
</ul>
</div>
</div>
<?php
} ?>
<script type="text/javascript">

	$(document).ready(function(){
		$(".nav-header").mouseover(function(){
			$(this).addClass('navHover');
		}).mouseout(function(){
			$(this).removeClass('navHover');
		}).click(function(){
			$(this).toggleClass('nav-header-current');
			$(this).next('.ckit').slideToggle();
		})
	});

</script>
        <div class="content">

<div class="cLineB">
  <h4>关注时自动回复内容 <span class="FAQ">可参考右边的范例来写,关注回复文字的信息，回复帮助也是此信息！</span></h4>
 </div>
         <div class="zdhuifu">
                  <form method="post"  action="<?php echo U('Areply/insert');?>">
                  <input type="hidden" name="wxid" value="gh_858dwjkeww5"  />
  
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr><td height="5"></td><td></td></tr>
<tr>
<td valign="top" width="420"><textarea class="px" style="width:420px; height:500px; margin:5px 0"  id="Hfcontent" name="content">
<?php echo ($areply["content"]); ?>
</textarea><p> <input name="home" type="checkbox" <?php if($areply['home'] == 1): ?>checked="checked"<?php endif; ?> value="1"   />若是想关注回复多图，此项必须沟选 </p><p>
关键词：<input type="input" style="width:100px;" class="px" id="keyword" value="<?php echo ($areply["keyword"]); ?>" name="keyword" style="width:500px" ><br/>例：填写"功能",系统会检索包含最近发布的9条信息，若想关注回复回复首页,此项请填写 首页<br/></td>
<td valign="top">
     	<div class="zdhuifu" style="margin-left:20px">
<h4>参考范例：</h4>
1.附近周边信息查询lbs<br/>
2.音乐查询　音乐＋音乐名  例：音乐爱你一万年<br/>
3.天气查询　城市名＋天气　例上海天气<br/>
4.手机归属地查询(吉凶 运势) 手机＋手机号码　例：手机13917778912<br/>
5.身份证查询　身份证＋号码　　例：身份证342423198803015568<br/>
6.公交查询　公交＋城市＋公交编号　例：上海公交774<br/>
7.火车查询　火车＋城市＋目的地　例火车上海南京<br/>
8.翻译 支持 及时翻译，语音翻译　翻译＋关键词 例：翻译你好<br/>
9.彩票查询　彩票＋彩票名  例如:彩票双色球<br/>
10.周公解梦　梦见+关键词　例如:梦见父母<br/>
11.陪聊　直接输入聊天关键词即可<br/>
12.聊天　直接输入聊天关键词即可<br/>
13.藏头诗 藏头诗+关键词　例：藏头诗我爱你　<br/>
14.笑话　直接发送笑话<br/>
15.糗事　直接发送糗事<br/>
16.快递 快递＋快递名＋快递号　例：快递顺丰117215889174<br/>
17.健康指数查询　健康＋高，＋重　例：健康170,65<br/>
18.朗读 朗读＋关键词　例：朗读<?php echo ($f_siteName); ?>多用户营销系统<br/>
19.计算器 计算器使用方法　例：计算50-50　，计算100*100<br/>
20.输入价格了解<?php echo ($f_siteName); ?>平台系统的价格<br/>
21.输入服务了解<?php echo ($f_siteName); ?>平台系统的售后服务<br/>
23.输入抽奖，即可玩幸运大抽奖<br/>
2４.输入会员即可填写会员资料<br/>
25.更多功能请回复帮助，或者help<br/>

</div></td>
</tr>
<tr>
<td height="50">
       
<input type="submit" value="保存"  name="sbmt"   class="btnGreen left"  />　<a href="<?php echo U('Img/add');?>"  class="btnGray vm"  >切换到图文模式</a>
<div class="right"  >
<ul>
<li class="biaoqing"><span>表情</span>
<ul>
<li><img src="<?php echo RES;?>/images/face/0.gif" alt="微笑" onclick="jsbq('微笑')"></li>
<li><img src="<?php echo RES;?>/images/face/1.gif" alt="撇嘴" onclick="jsbq('撇嘴')"></li>
<li><img src="<?php echo RES;?>/images/face/2.gif" alt="色" onclick="jsbq('色')"></li>
<li><img src="<?php echo RES;?>/images/face/3.gif" alt="发呆" onclick="jsbq('发呆')"></li>
<li><img src="<?php echo RES;?>/images/face/4.gif" alt="得意" onclick="jsbq('得意')"></li>
<li><img src="<?php echo RES;?>/images/face/5.gif" alt="流泪" onclick="jsbq('流泪')"></li>
<li><img src="<?php echo RES;?>/images/face/6.gif" alt="害羞" onclick="jsbq('害羞')"></li>
<li><img src="<?php echo RES;?>/images/face/7.gif" alt="闭嘴" onclick="jsbq('闭嘴')"></li>
<li><img src="<?php echo RES;?>/images/face/8.gif" alt="睡" onclick="jsbq('睡')"></li>
<li><img src="<?php echo RES;?>/images/face/9.gif" alt="大哭" onclick="jsbq('大哭')"></li>
<li><img src="<?php echo RES;?>/images/face/10.gif" alt="尴尬" onclick="jsbq('尴尬')"></li>
<li><img src="<?php echo RES;?>/images/face/11.gif" alt="发怒" onclick="jsbq('发怒')"></li>
<li><img src="<?php echo RES;?>/images/face/12.gif" alt="调皮" onclick="jsbq('调皮')"></li>
<li><img src="<?php echo RES;?>/images/face/13.gif" alt="呲牙" onclick="jsbq('呲牙')"></li>
<li><img src="<?php echo RES;?>/images/face/14.gif" alt="惊讶" onclick="jsbq('惊讶')"></li>
<li><img src="<?php echo RES;?>/images/face/15.gif" alt="难过" onclick="jsbq('难过')"></li>
<li><img src="<?php echo RES;?>/images/face/16.gif" alt="酷" onclick="jsbq('酷')"></li>
<li><img src="<?php echo RES;?>/images/face/17.gif" alt="冷汗" onclick="jsbq('冷汗')"></li>
<li><img src="<?php echo RES;?>/images/face/18.gif" alt="抓狂" onclick="jsbq('抓狂')"></li>
<li><img src="<?php echo RES;?>/images/face/19.gif" alt="吐" onclick="jsbq('吐')"></li>
<li><img src="<?php echo RES;?>/images/face/20.gif" alt="偷笑" onclick="jsbq('偷笑')"></li>
<li><img src="<?php echo RES;?>/images/face/21.gif" alt="可爱" onclick="jsbq('可爱')"></li>
<li><img src="<?php echo RES;?>/images/face/22.gif" alt="白眼" onclick="jsbq('白眼')"></li>
<li><img src="<?php echo RES;?>/images/face/23.gif" alt="傲慢" onclick="jsbq('傲慢')"></li>
<li><img src="<?php echo RES;?>/images/face/24.gif" alt="饥饿" onclick="jsbq('饥饿')"></li>
<li><img src="<?php echo RES;?>/images/face/25.gif" alt="困" onclick="jsbq('困')"></li>
<li><img src="<?php echo RES;?>/images/face/26.gif" alt="惊恐" onclick="jsbq('惊恐')"></li>
<li><img src="<?php echo RES;?>/images/face/27.gif" alt="流汗" onclick="jsbq('流汗')"></li>
<li><img src="<?php echo RES;?>/images/face/28.gif" alt="憨笑" onclick="jsbq('憨笑')"></li>
<li><img src="<?php echo RES;?>/images/face/29.gif" alt="大兵" onclick="jsbq('大兵')"></li>
<li><img src="<?php echo RES;?>/images/face/30.gif" alt="奋斗" onclick="jsbq('奋斗')"></li>
<li><img src="<?php echo RES;?>/images/face/31.gif" alt="咒骂" onclick="jsbq('咒骂')"></li>
<li><img src="<?php echo RES;?>/images/face/32.gif" alt="疑问" onclick="jsbq('疑问')"></li>
<li><img src="<?php echo RES;?>/images/face/33.gif" alt="嘘" onclick="jsbq('嘘')"></li>
<li><img src="<?php echo RES;?>/images/face/34.gif" alt="晕" onclick="jsbq('晕')"></li>
<li><img src="<?php echo RES;?>/images/face/35.gif" alt="折磨" onclick="jsbq('折磨')"></li>
<li><img src="<?php echo RES;?>/images/face/36.gif" alt="衰" onclick="jsbq('衰')"></li>
<li><img src="<?php echo RES;?>/images/face/37.gif" alt="骷髅" onclick="jsbq('骷髅')"></li>
<li><img src="<?php echo RES;?>/images/face/38.gif" alt="敲打" onclick="jsbq('敲打')"></li>
<li><img src="<?php echo RES;?>/images/face/39.gif" alt="再见" onclick="jsbq('再见')"></li>
<li><img src="<?php echo RES;?>/images/face/40.gif" alt="擦汗" onclick="jsbq('擦汗')"></li>
<li><img src="<?php echo RES;?>/images/face/41.gif" alt="抠鼻" onclick="jsbq('抠鼻')"></li>
<li><img src="<?php echo RES;?>/images/face/42.gif" alt="鼓掌" onclick="jsbq('鼓掌')"></li>
<li><img src="<?php echo RES;?>/images/face/43.gif" alt="糗大了" onclick="jsbq('糗大了')"></li>
<li><img src="<?php echo RES;?>/images/face/44.gif" alt="坏笑" onclick="jsbq('坏笑')"></li>
<li><img src="<?php echo RES;?>/images/face/45.gif" alt="左哼哼" onclick="jsbq('左哼哼')"></li>
<li><img src="<?php echo RES;?>/images/face/46.gif" alt="右哼哼" onclick="jsbq('右哼哼')"></li>
<li><img src="<?php echo RES;?>/images/face/47.gif" alt="哈欠" onclick="jsbq('哈欠')"></li>
<li><img src="<?php echo RES;?>/images/face/48.gif" alt="鄙视" onclick="jsbq('鄙视')"></li>
<li><img src="<?php echo RES;?>/images/face/49.gif" alt="委屈" onclick="jsbq('委屈')"></li>
<li><img src="<?php echo RES;?>/images/face/50.gif" alt="快哭了" onclick="jsbq('快哭了')"></li>
<li><img src="<?php echo RES;?>/images/face/51.gif" alt="阴险" onclick="jsbq('阴险')"></li>
<li><img src="<?php echo RES;?>/images/face/52.gif" alt="亲亲" onclick="jsbq('亲亲')"></li>
<li><img src="<?php echo RES;?>/images/face/53.gif" alt="吓" onclick="jsbq('吓')"></li>
<li><img src="<?php echo RES;?>/images/face/54.gif" alt="可怜" onclick="jsbq('可怜')"></li>
<li><img src="<?php echo RES;?>/images/face/55.gif" alt="菜刀" onclick="jsbq('菜刀')"></li>
<li><img src="<?php echo RES;?>/images/face/56.gif" alt="西瓜" onclick="jsbq('西瓜')"></li>
<li><img src="<?php echo RES;?>/images/face/57.gif" alt="啤酒" onclick="jsbq('啤酒')"></li>
<li><img src="<?php echo RES;?>/images/face/58.gif" alt="篮球" onclick="jsbq('篮球')"></li>
<li><img src="<?php echo RES;?>/images/face/59.gif" alt="乒乓" onclick="jsbq('乒乓')"></li>
<li><img src="<?php echo RES;?>/images/face/60.gif" alt="咖啡" onclick="jsbq('咖啡')"></li>
<li><img src="<?php echo RES;?>/images/face/61.gif" alt="饭" onclick="jsbq('饭')"></li>
<li><img src="<?php echo RES;?>/images/face/62.gif" alt="猪头" onclick="jsbq('猪头')"></li>
<li><img src="<?php echo RES;?>/images/face/63.gif" alt="玫瑰" onclick="jsbq('玫瑰')"></li>
<li><img src="<?php echo RES;?>/images/face/64.gif" alt="凋谢" onclick="jsbq('凋谢')"></li>
<li><img src="<?php echo RES;?>/images/face/65.gif" alt="示爱" onclick="jsbq('示爱')"></li>
<li><img src="<?php echo RES;?>/images/face/66.gif" alt="爱心" onclick="jsbq('爱心')"></li>
<li><img src="<?php echo RES;?>/images/face/67.gif" alt="心碎" onclick="jsbq('心碎')"></li>
<li><img src="<?php echo RES;?>/images/face/68.gif" alt="蛋糕" onclick="jsbq('蛋糕')"></li>
<li><img src="<?php echo RES;?>/images/face/69.gif" alt="闪电" onclick="jsbq('闪电')"></li>
<li><img src="<?php echo RES;?>/images/face/70.gif" alt="炸弹" onclick="jsbq('炸弹')"></li>
<li><img src="<?php echo RES;?>/images/face/71.gif" alt="刀" onclick="jsbq('刀')"></li>
<li><img src="<?php echo RES;?>/images/face/72.gif" alt="足球" onclick="jsbq('足球')"></li>
<li><img src="<?php echo RES;?>/images/face/73.gif" alt="瓢虫" onclick="jsbq('瓢虫')"></li>
<li><img src="<?php echo RES;?>/images/face/74.gif" alt="便便" onclick="jsbq('便便')"></li>
<li><img src="<?php echo RES;?>/images/face/75.gif" alt="月亮" onclick="jsbq('月亮')"></li>
<li><img src="<?php echo RES;?>/images/face/76.gif" alt="太阳" onclick="jsbq('太阳')"></li>
<li><img src="<?php echo RES;?>/images/face/77.gif" alt="礼物" onclick="jsbq('礼物')"></li>
<li><img src="<?php echo RES;?>/images/face/78.gif" alt="拥抱" onclick="jsbq('拥抱')"></li>
<li><img src="<?php echo RES;?>/images/face/79.gif" alt="强" onclick="jsbq('强')"></li>
<li><img src="<?php echo RES;?>/images/face/80.gif" alt="弱" onclick="jsbq('弱')"></li>
<li><img src="<?php echo RES;?>/images/face/81.gif" alt="握手" onclick="jsbq('握手')"></li>
<li><img src="<?php echo RES;?>/images/face/82.gif" alt="胜利" onclick="jsbq('胜利')"></li>
<li><img src="<?php echo RES;?>/images/face/83.gif" alt="抱拳" onclick="jsbq('抱拳')"></li>
<li><img src="<?php echo RES;?>/images/face/84.gif" alt="勾引" onclick="jsbq('勾引')"></li>
<li><img src="<?php echo RES;?>/images/face/85.gif" alt="拳头" onclick="jsbq('拳头')"></li>
<li><img src="<?php echo RES;?>/images/face/86.gif" alt="差劲" onclick="jsbq('差劲')"></li>
<li><img src="<?php echo RES;?>/images/face/87.gif" alt="爱你" onclick="jsbq('爱你')"></li>
<li><img src="<?php echo RES;?>/images/face/88.gif" alt="NO" onclick="jsbq('NO')"></li>
<li><img src="<?php echo RES;?>/images/face/89.gif" alt="OK" onclick="jsbq('OK')"></li>
<li><img src="<?php echo RES;?>/images/face/90.gif" alt="爱情" onclick="jsbq('爱情')"></li>
<li><img src="<?php echo RES;?>/images/face/91.gif" alt="飞吻" onclick="jsbq('飞吻')"></li>
<li><img src="<?php echo RES;?>/images/face/92.gif" alt="跳跳" onclick="jsbq('跳跳')"></li>
<li><img src="<?php echo RES;?>/images/face/93.gif" alt="发抖" onclick="jsbq('发抖')"></li>
<li><img src="<?php echo RES;?>/images/face/94.gif" alt="怄火" onclick="jsbq('怄火')"></li>
<li><img src="<?php echo RES;?>/images/face/95.gif" alt="转圈" onclick="jsbq('转圈')"></li>
<li><img src="<?php echo RES;?>/images/face/96.gif" alt="磕头" onclick="jsbq('磕头')"></li>
<li><img src="<?php echo RES;?>/images/face/97.gif" alt="回头" onclick="jsbq('回头')"></li>
<li><img src="<?php echo RES;?>/images/face/98.gif" alt="跳绳" onclick="jsbq('跳绳')"></li>
<li><img src="<?php echo RES;?>/images/face/99.gif" alt="挥手" onclick="jsbq('挥手')"></li>
<li><img src="<?php echo RES;?>/images/face/100.gif" alt="激动" onclick="jsbq('激动')"></li>
<li><img src="<?php echo RES;?>/images/face/101.gif" alt="街舞" onclick="jsbq('街舞')"></li>
<li><img src="<?php echo RES;?>/images/face/102.gif" alt="献吻" onclick="jsbq('献吻')"></li>
<li><img src="<?php echo RES;?>/images/face/103.gif" alt="左太极" onclick="jsbq('左太极')"></li>
</ul>
</li>
</ul>
</div>
<script type="text/javascript">
function jsbq(srt){
document.getElementById("Hfcontent").value=document.getElementById("Hfcontent").value+"/"+srt;
}
</script>


</td><td valign="top">
</tr>
</table>
</form>
      </div>
        </div>

        <div class="clr"></div>
      </div>
    </div>
  </div>
  <script>
$(document).ready( function(){ 
$('.checkall').click(function(){

$('.checkitem').each(function(){
$(this).attr('checked',$('.checkall').attr('checked'));
});
});

});
  </script>
  <!--底部-->
  	</div>
</div>
</div>
</div>

<style>
.IndexFoot {
	BACKGROUND-COLOR: #333; WIDTH: 100%; HEIGHT: 39px
}
.foot{ width:988px; margin:0px auto; font-size:12px; line-height:39px;}
.foot .foot_page{ float:left; width:600px;color:white;}
.foot .foot_page a{ color:white; text-decoration:none;}
#copyright{ float:right; width:380px; text-align:right; font-size:12px; color:#FFF;}
</style>
<div class="IndexFoot" style="height:120px;clear:both">
<div class="foot" style="padding-top:20px;">
<div class="foot_page" >
<a href="<?php echo ($f_siteUrl); ?>"><?php echo ($f_siteName); ?>,微信公众平台营销</a><br/>
帮助您快速搭建属于自己的营销平台,构建自己的客户群体。
</div>
<div id="copyright" style="color:white;">
	<?php echo ($f_siteName); ?>(c)版权所有 <a href="http://www.miibeian.gov.cn" target="_blank" style="color:white"><?php echo C('ipc');?></a><br/>
	技术支持：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo ($f_qq); ?>&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo ($f_qq); ?>:51" alt="联系我吧" title="联系我吧"/></a>

</div>
    </div>
</div>
<!-- 帮助悬浮开始 -->

<!-- upgrade start-->
<?php
if ($upgradeNews){ ?>
<script>
  	function closeUserUpgrade(){
  		$('#userUpgradeNotice').animate({opacity: "hide"}, "slow");
  		
  	}
  	</script>
<style>
/* 底部浮动层 */
.qyer_layer_fix { color:#fff; position:fixed; left:0; bottom:0; height:70px; width:100%; z-index:900; min-width:980px;display:none;}

/* 左侧活动图片 */
.qyer_layer_hot_img { position:absolute; bottom:0; left:0; display:inline-block;}

/* 右侧关闭图标 */
.qyer_layer_close { background:url(<?php echo ($staticPath); ?>/tpl/static/help/qyer_layer_close.png) no-repeat right center; text-indent:-9999px; width:31px; height:29px; position:absolute; right:20px; top:20px; cursor:pointer;}
.qyer_layer_close:hover { background-position:center center;}
.qyer_layer_close:active { background-position:left center;}

/* 浮动层信息 */
.qyer_layer_main { width:980px; min-width:980px; margin:0 auto; height:70px; position:relative;}
</style>
<div id="userUpgradeNotice" class="qyer_layer_fix _jsbeforelogindiv" style="background: url(<?php echo ($staticPath); ?>/tpl/static/help/qyer_layer_bg.png) repeat scroll 0% 0% transparent; display: block;">
	<div data-bn-ipg="bl-plansnow-left-1" class="qyer_layer_hot_img"><!--设置可显示隐藏 -->
    	<!--左侧热门图片 -->
    	    
    	    </div>
    <div class="qyer_layer_main">
    	<div style="font-size:22px; font-weight:bold; line-height:70px; text-align:center; font-family:Microsoft Yahei; color:red">
        	<?php echo $upgradeNews['title'];?> <span style="color:green">请联系管理员处理</span>
        </div>
    </div>
    <div data-bn-ipg="bl-plansnow-close" class="qyer_layer_close" onclick="closeUserUpgrade()">关闭</div><!--设置可显示隐藏 --></div>
    <?php
} ?>
<!-- upgrade end-->

<?php if(!empty($content)): ?><link href="<?php echo ($staticPath); ?>/tpl/static/help_xuanfu/css/zuoce.css" type="text/css" rel="stylesheet"/>
	<div class="zuoce zuoce_clear">
		<div id="Layer1">
			<a href="javascript:"><img src="<?php echo ($staticPath); ?>/tpl/static/help_xuanfu/images/ou_03.png"/></a>
		</div>
		<div id="Layer2" style="display:none;">
			<p class="xiangGuan zuoce_clear">相关帮助</p>
			<?php if(is_array($content)): $i = 0; $__LIST__ = $content;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><p class="lianjie zuoce_clear"><a href="javascript:openwin('/index.php?g=User&m=Index&a=help&helpParm=/oa/admin_help_<?php echo ($list['id']); ?>.html',768,960)" <?php if($list['is_video'] == 1): ?>class="video"<?php else: ?>class="writing"<?php endif; ?>><?php echo ($list["title"]); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
			<!--p class="anNiuo clear"><a href="#">进入帮助中心</a></p-->
			<p class="anNiut zuoce_clear"><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('site_qq');?>&site=qq&menu=yes" target="_blank">在线客服</a></p>
		</div>
	</div>
	<script type="text/javascript">
		window.onload = function(){
			var oDiv1 = document.getElementById('Layer1');
			var oDiv2 = document.getElementById('Layer2');
			oDiv1.onclick = function(){
				oDiv2.style.display = oDiv2.style.display == 'block' ? 'none' : 'block';
			}
		}
		function openwin(url,iHeight,iWidth){
			var iTop = (window.screen.availHeight-30-iHeight)/2,iLeft = (window.screen.availWidth-10-iWidth)/2;
			window.open(url, "newwindow", "height="+iHeight+", width="+iWidth+", toolbar=no, menubar=no,top="+iTop+",left="+iLeft+",scrollbars=yes, resizable=no, location=no, status=no");
		}
	</script><?php endif; ?>
<!-- 帮助悬浮结束 -->
<div style="display:none">
<?php echo ($alert); ?> 
<?php echo base64_decode(C('countsz'));?>
</div>

</body>

<?php if(MODULE_NAME == 'Function' && ACTION_NAME == 'welcome'){ ?>
<script src="<?php echo ($staticPath); ?>/tpl/static/myChart/js/echarts-plain.js"></script>

<script type="text/javascript">


    var myChart = echarts.init(document.getElementById('main')); 
   

    var option = {
        title : {
            text: '<?php if($charts["ifnull"] == 1): ?>本月关注和文本请求数据统计示例图(您暂时没有数据)<?php else: ?>本月关注和文本请求数据统计<?php endif; ?>',
            x:'left'
        },
        tooltip : {
            trigger: 'axis'
        },
        legend: {
            data:['文本请求数','关注数'],
            x: 'right'
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: false},
                dataView : {show: false, readOnly: false},
                magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                restore : {show: false} ,
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : [<?php echo ($charts["xAxis"]); ?>]
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'文本请求数',
                type:'line',
                tiled: '总量',
                data: [<?php echo ($charts["text"]); ?>]
            },    
            {
                "name":'关注数',
                "type":'line',
                "tiled": '总量',
                data:[<?php echo ($charts["follow"]); ?>]
            }
       

        ]

    };                   

    myChart.setOption(option); 


    var myChart2 = echarts.init(document.getElementById('pieMain')); 
               
    var option2 = {
        title : {
            text: '<?php if($pie["ifnull"] == 1): ?>7日内粉丝行为分析示例图(您暂时没有数据)<?php else: ?>7日内粉丝行为分析<?php endif; ?>',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'粉丝行为统计',
                type:'pie',
                radius : ['50%', '70%'],
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true,
                            position : 'center',
                            textStyle : {
                                fontSize : '18',
                                fontWeight : 'bold'
                            }
                        }
                    }
                },
                data:[ 
                    <?php echo ($pie["series"]); ?>
                
                ]
            }
        ]
    };
     myChart2.setOption(option2,true); 


    var myChart3 = echarts.init(document.getElementById('pieMain2')); 
    var option3 = {
        title : {
            text: '<?php if($sex_series["ifnull"] == 1): ?>会员性别统计示例图(您暂时没有数据)<?php else: ?>会员性别统计<?php endif; ?>',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'会员性别统计',
                type:'pie',
                radius : ['50%', '70%'],
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true,
                            position : 'center',
                            textStyle : {
                                fontSize : '18',
                                fontWeight : 'bold'
                            }
                        }
                    }
                },
                data:[
                  <?php echo ($sex_series['series']); ?>
                ]
            }
        ]
    };                     

  myChart3.setOption(option3,true); 



    </script>
<?php } ?>
</html>