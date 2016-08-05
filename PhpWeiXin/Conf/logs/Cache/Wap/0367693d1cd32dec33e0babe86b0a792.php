<?php if (!defined('THINK_PATH')) exit();?>﻿	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	  <link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/template/music-left.css" media="all" />
		<script src="<?php echo RES;?>/css/flash/js/zepto.min.js" type="text/javascript"></script>
	<script src="<?php echo RES;?>/css/flash/js/plugmenu.js" type="text/javascript"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="./tpl/Wap/default/common/css/1337/font-awesome.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/template/reset.css" media="all" />
	<title><?php echo ($tpl["wxname"]); ?></title>
	<base href="." />
	<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/1337/index.css" />
	<script src="<?php echo RES;?>/css/1337/iscroll.js" type="text/javascript"></script>
	
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
            }            document.addEventListener('DOMContentLoaded', loaded, false);
        </script>
    </head><body><div class="wrap">
	<div class="banner">
		<div id="wrapper">
			<div id="scroller">
				<ul id="thelist"> 
				<?php if(is_array($flash)): $i = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?><li><p><?php echo ($so["info"]); ?></p><?php if($so['url'] == ''): ?><img src="<?php echo ($so["img"]); ?>" /><?php else: ?><a href="<?php echo ($so["url"]); ?>"><img src="<?php echo ($so["img"]); ?>" /></a><?php endif; ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
		<div id="nav">
			<div id="prev" onclick="myScroll.scrollToPage('prev', 0,400,3);return false">&larr; prev</div>
			<ul id="indicator">
			<?php if(is_array($flash)): $i = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?><li   <?php if($i == 1): ?>class="active"<?php endif; ?>  ><?php echo ($i); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div id="next" onclick="myScroll.scrollToPage('next', 0);return false">next &rarr;</div>
		</div>
		<div class="clr"></div>
	</div>
	<div id="insert1"></div>
	<script>
            var count = document.getElementById("thelist").getElementsByTagName("img").length;	
            for(i=0;i<count;i++){
                document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:"+document.body.clientWidth+"px";            }            document.getElementById("scroller").style.cssText = " width:"+document.body.clientWidth*count+"px";
            setInterval(function(){
                myScroll.scrollToPage('next', 0,400,count);
            },3500 );            window.onresize = function(){ 
                for(i=0;i<count;i++){
                    document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:"+document.body.clientWidth+"px";                }                document.getElementById("scroller").style.cssText = " width:"+document.body.clientWidth*count+"px";
            } 	</script>
	<div id="insert2"></div>
	<div style="display:none"> </div>
	<div style="display:none"><script language="javascript" type="text/javascript" src=""></script></div><div class="main">
    	<ul><?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php if($vo['url'] == ''): echo U('Wap/Index/lists',array('classid'=>$vo['id'],'token'=>$vo['token'])); else: echo (htmlspecialchars_decode($vo["url"])); endif; ?>">
				 <li id="m1">
                	
					<span class="icon"  style="background:url(<?php echo ($vo["img"]); ?>) no-repeat;background-size:88px 64px;"><em></em></span><span class="title" style="color:<?php echo ($vo["nameyanse"]); ?>"><?php echo ($vo["name"]); ?></span></li></a><?php endforeach; endif; else: echo "" ;endif; ?> 
       		        </ul>
  </div>
</div>
        <div id="insert2"></div>
        <div style="display:none"> </div>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
<?php if($radiogroup > 8): ?><br>
<br><?php endif; ?>
<script>
function displayit(n){
	for(i=0;i<4;i++){
		if(i==n){
			var id='menu_list'+n;
			if(document.getElementById(id).style.display=='none'){
				document.getElementById(id).style.display='';
				document.getElementById("plug-wrap").style.display='';
			}else{
				document.getElementById(id).style.display='none';
				document.getElementById("plug-wrap").style.display='none';
			}
		}else{
			if($('#menu_list'+i)){
				$('#menu_list'+i).css('display','none');
			}
		}
	}
}
function closeall(){
	var count = document.getElementById("top_menu").getElementsByTagName("ul").length;
	for(i=0;i<count;i++){
		document.getElementById("top_menu").getElementsByTagName("ul").item(i).style.display='none';
	}
	document.getElementById("plug-wrap").style.display='none';
}

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideToolbar');
});
</script> <style type="text/css">
body { margin-bottom:60px !important; }
a, button, input { -webkit-tap-highlight-color:rgba(255, 0, 0, 0); }
ul, li { list-style:none; margin:0;}
.top_bar { position: fixed; z-index: 900; bottom: 0; left: 0; right: 0; margin: auto; font-family: Helvetica, Tahoma, Arial, Microsoft YaHei, sans-serif; }
.top_menu { display:-webkit-box; border-top: 1px solid #b3b3b3; display: block; width: 100%; background: rgba(255, 255, 255, 0.7); height: 48px; display: -webkit-box; display: box; margin:0; padding:0; -webkit-box-orient: horizontal; background: -webkit-gradient(linear, 0 0, 0 100%, from(#e7e4e7), to(#b9b9b9)); }
.top_bar .top_menu>li { -webkit-box-flex:1; background: -webkit-gradient(linear, 0 0, 0 100%, from(rgba(0, 0, 0, 0.1)), color-stop(50%, rgba(0, 0, 0, 0.2)), to(rgba(0, 0, 0, 0.2))), -webkit-gradient(linear, 0 0, 0 100%, from(rgba(255, 255, 255, 0.1)), color-stop(50%, rgba(255, 255, 255, 0.3)), to(rgba(255, 255, 255, 0.1))); -webkit-background-size:1px 100%, 1px 100%; background-size:1px 100%, 1px 100%; background-position: 1px center, 2px center; background-repeat: no-repeat; position:relative; text-align:center; }
.top_menu>li:first-child { background:none; }
.top_bar .top_menu>li>a { height:48px; line-height: 48px; display:block; text-align:center; color:#4f4d4f; text-shadow: 0 1px rgba(255, 255, 255, 0.3); text-decoration:none; border-top: 1px solid #f9f9f9; -webkit-box-flex:1; }
.top_bar .top_menu>li>a label { overflow:hidden; margin: 0 0 0 0; font-size: 12px; display: block !important; line-height: 18px; text-align: center; }
.top_bar .top_menu>li>a img { margin: 2px 0 0 0; height: 24px; width: 24px; color: #fff; line-height: 48px; vertical-align:middle; }
.top_bar li:first-child a { display: block; }
.menu_font { padding: 0; position: absolute; z-index: 500; bottom: 60px; right: 10px; width: 120px; background: #e4e3e2; border: 1px solid #afaeaf; border-radius: 5px; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2); }
.menu_font.hidden { display:none; }
.top_menu li:last-of-type a { background: none; }
.top_menu>li:last-of-type>a label { padding: 0 0 0 3px; }
.menu_font li:last-of-type { background: none; }
.menu_font li a { text-align: left !important; }
.top_menu li:last-of-type a { background: none; }
.menu_font:before, .menu_font:after { content:""; display:inline-block; position:absolute; z-index:240; bottom:0; left: 85%; margin-left:-8px; margin-bottom:-16px; width:0; height:0; border:8px solid red; border-color:#afaeaf transparent transparent transparent; }
.menu_font:after { z-index:501; border-color:#e4e3e2 transparent transparent transparent; margin-bottom:-15px; margin-left:-8px; }
.menu_font li { background:-webkit-gradient(linear, 0 0, 100% 0, from(#e4e3e2), to(#e4e3e2), color-stop(50%, #f3f3f2)), -webkit-gradient(linear, 0 0, 100% 0, from(#e4e3e2), to(#e4e3e2), color-stop(50%, #c6c5c5)); background-size:100% 1px, 100% 2px; background-repeat:no-repeat; background-position: center bottom; }
.menu_font li:first-of-type { border-top: 0; }
.menu_font li:last-of-type { border-bottom: 0; }
.menu_font li a { height: 40px; line-height: 40px !important; position: relative; color: #fff; display: block; width: 100%; text-indent: 10px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; text-decoration:none; color:#4f4d4f; text-shadow: 0 1px rgba(255, 255, 255, 0.3); }
.menu_font li a img { width: 20px; height:20px; display: inline-block; margin-top:-2px; color: #fff; line-height: 40px; vertical-align:middle; }
.menu_font>li>a label { padding:3px 0 0 3px; font-size:14px; overflow:hidden; margin: 0; }
#menu_list0 { right:0; left:10px; }
#menu_list0:before, #menu_list0:after { left: 15%; }
#menu_list0:after { margin-bottom:-15px; margin-left:-8px; }
#sharemcover { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); display: none; z-index: 20000; }
#sharemcover img { position: fixed; right: 18px; top: 5px; width: 260px; height: 180px; z-index: 20001; border:0; }
.top_bar .top_menu>li>a:hover, .top_bar .top_menu>li>a:active { background-color:#CCCCCC; }
.menu_font li a:hover, .menu_font li a:active { background-color:#CCCCCC; }
.menu_font li:first-of-type a { border-radius:5px 5px 0 0; }
.menu_font li:last-of-type a { border-radius:0 0 5px 5px; }
#plug-wrap { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0); z-index:800; }
#cate18 .device {bottom: 49px;}
#cate18 #indicator {bottom: 240px;}
#cate19 .device {bottom: 49px;}
#cate19 #indicator {bottom: 330px;}
#cate19 .pagination {bottom: 60px;}
#cate17 .device {margin-bottom: 46px;}
#cate17 #indicator {bottom: 130px;}
</style>
<div class="top_bar" style="-webkit-transform:translate3d(0,0,0)">
  <nav>
    <ul id="top_menu" class="top_menu">
        <?php if(is_array($catemenu)): $i = 0; $__LIST__ = $catemenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> <a <?php if($vo['vo']){echo 'onclick="javascript:displayit('.$vo['k'].')"';}else{echo 'href="'.$vo['url'].'"';}?>><img src="<?php echo ($vo["picurl"]); ?>"><label><?php echo ($vo["name"]); ?></label></a>
        <?php if ($vo['vo']){ ?>
            <ul id="menu_list<?php echo ($vo['k']); ?>" class="menu_font" style=" display:none">
            <?php if(is_array($vo['vo'])): $i = 0; $__LIST__ = $vo['vo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["picurl"]); ?>"><label><?php echo ($vo["name"]); ?></label></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
             <?php
 } ?>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </nav>
</div>
<div id="plug-wrap" onclick="closeall()" style="display: none;"></div> 
	<?php if(ACTION_NAME == 'index'): ?><script type="text/javascript">
			window.shareData = {  
					"moduleName":"Index",
					"moduleID": '0',
					"imgUrl": "<?php echo ($homeInfo["picurl"]); ?>", 
					"timeLineLink": "<?php echo C('site_url'); echo U(Index/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"sendFriendLink": "<?php echo C('site_url'); echo U(Index/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"weiboLink": "<?php echo C('site_url'); echo U(Index/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"tTitle": "<?php echo ($homeInfo["title"]); ?>",
					"tContent": "<?php echo ($homeInfo["info"]); ?>"
				};
		</script>
	<?php else: ?>
		<script type="text/javascript">
			window.shareData = {  
				"moduleName":"Index",
				"moduleID": '1',
				"imgUrl": "<?php echo ($homeInfo["picurl"]); ?>", 
				"timeLineLink": "<?php echo C('site_url'); echo U(Index/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"sendFriendLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"weiboLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"tTitle": "<?php echo ($homeInfo["title"]); ?>",
				"tContent": "<?php echo ($homeInfo["info"]); ?>"
			};
		</script><?php endif; ?>
	<?php echo ($shareScript); ?>
<div class="copyright">
<?php if($iscopyright == 1): echo ($homeInfo["copyright"]); ?>
<?php else: ?>
<?php echo ($siteCopyright); endif; ?>
</div> 
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
<?php if($radiogroup > 8): ?><br>
<br><?php endif; ?>
<script>
function displayit(n){
	for(i=0;i<4;i++){
		if(i==n){
			var id='menu_list'+n;
			if(document.getElementById(id).style.display=='none'){
				document.getElementById(id).style.display='';
				document.getElementById("plug-wrap").style.display='';
			}else{
				document.getElementById(id).style.display='none';
				document.getElementById("plug-wrap").style.display='none';
			}
		}else{
			if($('#menu_list'+i)){
				$('#menu_list'+i).css('display','none');
			}
		}
	}
}
function closeall(){
	var count = document.getElementById("top_menu").getElementsByTagName("ul").length;
	for(i=0;i<count;i++){
		document.getElementById("top_menu").getElementsByTagName("ul").item(i).style.display='none';
	}
	document.getElementById("plug-wrap").style.display='none';
}

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideToolbar');
});
</script> <style type="text/css">
body { margin-bottom:60px !important; }
a, button, input { -webkit-tap-highlight-color:rgba(255, 0, 0, 0); }
ul, li { list-style:none; margin:0;}
.top_bar { position: fixed; z-index: 900; bottom: 0; left: 0; right: 0; margin: auto; font-family: Helvetica, Tahoma, Arial, Microsoft YaHei, sans-serif; }
.top_menu { display:-webkit-box; border-top: 1px solid #b3b3b3; display: block; width: 100%; background: rgba(255, 255, 255, 0.7); height: 48px; display: -webkit-box; display: box; margin:0; padding:0; -webkit-box-orient: horizontal; background: -webkit-gradient(linear, 0 0, 0 100%, from(#e7e4e7), to(#b9b9b9)); }
.top_bar .top_menu>li { -webkit-box-flex:1; background: -webkit-gradient(linear, 0 0, 0 100%, from(rgba(0, 0, 0, 0.1)), color-stop(50%, rgba(0, 0, 0, 0.2)), to(rgba(0, 0, 0, 0.2))), -webkit-gradient(linear, 0 0, 0 100%, from(rgba(255, 255, 255, 0.1)), color-stop(50%, rgba(255, 255, 255, 0.3)), to(rgba(255, 255, 255, 0.1))); -webkit-background-size:1px 100%, 1px 100%; background-size:1px 100%, 1px 100%; background-position: 1px center, 2px center; background-repeat: no-repeat; position:relative; text-align:center; }
.top_menu>li:first-child { background:none; }
.top_bar .top_menu>li>a { height:48px; line-height: 48px; display:block; text-align:center; color:#4f4d4f; text-shadow: 0 1px rgba(255, 255, 255, 0.3); text-decoration:none; border-top: 1px solid #f9f9f9; -webkit-box-flex:1; }
.top_bar .top_menu>li>a label { overflow:hidden; margin: 0 0 0 0; font-size: 12px; display: block !important; line-height: 18px; text-align: center; }
.top_bar .top_menu>li>a img { margin: 2px 0 0 0; height: 24px; width: 24px; color: #fff; line-height: 48px; vertical-align:middle; }
.top_bar li:first-child a { display: block; }
.menu_font { padding: 0; position: absolute; z-index: 500; bottom: 60px; right: 10px; width: 120px; background: #e4e3e2; border: 1px solid #afaeaf; border-radius: 5px; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2); }
.menu_font.hidden { display:none; }
.top_menu li:last-of-type a { background: none; }
.top_menu>li:last-of-type>a label { padding: 0 0 0 3px; }
.menu_font li:last-of-type { background: none; }
.menu_font li a { text-align: left !important; }
.top_menu li:last-of-type a { background: none; }
.menu_font:before, .menu_font:after { content:""; display:inline-block; position:absolute; z-index:240; bottom:0; left: 85%; margin-left:-8px; margin-bottom:-16px; width:0; height:0; border:8px solid red; border-color:#afaeaf transparent transparent transparent; }
.menu_font:after { z-index:501; border-color:#e4e3e2 transparent transparent transparent; margin-bottom:-15px; margin-left:-8px; }
.menu_font li { background:-webkit-gradient(linear, 0 0, 100% 0, from(#e4e3e2), to(#e4e3e2), color-stop(50%, #f3f3f2)), -webkit-gradient(linear, 0 0, 100% 0, from(#e4e3e2), to(#e4e3e2), color-stop(50%, #c6c5c5)); background-size:100% 1px, 100% 2px; background-repeat:no-repeat; background-position: center bottom; }
.menu_font li:first-of-type { border-top: 0; }
.menu_font li:last-of-type { border-bottom: 0; }
.menu_font li a { height: 40px; line-height: 40px !important; position: relative; color: #fff; display: block; width: 100%; text-indent: 10px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; text-decoration:none; color:#4f4d4f; text-shadow: 0 1px rgba(255, 255, 255, 0.3); }
.menu_font li a img { width: 20px; height:20px; display: inline-block; margin-top:-2px; color: #fff; line-height: 40px; vertical-align:middle; }
.menu_font>li>a label { padding:3px 0 0 3px; font-size:14px; overflow:hidden; margin: 0; }
#menu_list0 { right:0; left:10px; }
#menu_list0:before, #menu_list0:after { left: 15%; }
#menu_list0:after { margin-bottom:-15px; margin-left:-8px; }
#sharemcover { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); display: none; z-index: 20000; }
#sharemcover img { position: fixed; right: 18px; top: 5px; width: 260px; height: 180px; z-index: 20001; border:0; }
.top_bar .top_menu>li>a:hover, .top_bar .top_menu>li>a:active { background-color:#CCCCCC; }
.menu_font li a:hover, .menu_font li a:active { background-color:#CCCCCC; }
.menu_font li:first-of-type a { border-radius:5px 5px 0 0; }
.menu_font li:last-of-type a { border-radius:0 0 5px 5px; }
#plug-wrap { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0); z-index:800; }
#cate18 .device {bottom: 49px;}
#cate18 #indicator {bottom: 240px;}
#cate19 .device {bottom: 49px;}
#cate19 #indicator {bottom: 330px;}
#cate19 .pagination {bottom: 60px;}
#cate17 .device {margin-bottom: 46px;}
#cate17 #indicator {bottom: 130px;}
</style>
<div class="top_bar" style="-webkit-transform:translate3d(0,0,0)">
  <nav>
    <ul id="top_menu" class="top_menu">
        <?php if(is_array($catemenu)): $i = 0; $__LIST__ = $catemenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> <a <?php if($vo['vo']){echo 'onclick="javascript:displayit('.$vo['k'].')"';}else{echo 'href="'.$vo['url'].'"';}?>><img src="<?php echo ($vo["picurl"]); ?>"><label><?php echo ($vo["name"]); ?></label></a>
        <?php if ($vo['vo']){ ?>
            <ul id="menu_list<?php echo ($vo['k']); ?>" class="menu_font" style=" display:none">
            <?php if(is_array($vo['vo'])): $i = 0; $__LIST__ = $vo['vo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["picurl"]); ?>"><label><?php echo ($vo["name"]); ?></label></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
             <?php
 } ?>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </nav>
</div>
<div id="plug-wrap" onclick="closeall()" style="display: none;"></div> 
<!-- share -->

	<?php if(ACTION_NAME == 'index'): ?><script type="text/javascript">
			window.shareData = {  
					"moduleName":"Index",
					"moduleID": "0",
					"imgUrl": "<?php echo ($homeInfo["picurl"]); ?>", 
					"timeLineLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"sendFriendLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"weiboLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"tTitle": "<?php echo ($homeInfo["title"]); ?>",
					"tContent": "<?php echo ($homeInfo["info"]); ?>"
				};
		</script>
	<?php else: ?>
		<script type="text/javascript">
			window.shareData = {  
				"moduleName":"NewsList",
				"moduleID": "<?php echo (intval($_GET['classid'])); ?>",
				"imgUrl": "<?php echo ($thisClassInfo["img"]); ?>", 
				"timeLineLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"sendFriendLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"weiboLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"tTitle": "<?php echo ($thisClassInfo["name"]); ?>",
				"tContent": "<?php echo ($thisClassInfo["info"]); ?>"
			};
		</script><?php endif; ?>
	
<?php echo ($shareScript); ?>

    </body></html>