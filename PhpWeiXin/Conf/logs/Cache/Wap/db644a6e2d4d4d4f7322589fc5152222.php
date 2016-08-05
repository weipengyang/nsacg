<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo ($staticPath); ?>/tpl/static/repastnew/css/common.css" media="all">
<link rel="stylesheet" type="text/css" href="<?php echo ($staticPath); ?>/tpl/static/repastnew/css/color.css" media="all">
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/jquery_min.js"></script>
<title><?php echo ($metaTitle); ?></title>	
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<!-- Mobile Devices Support @begin -->
		<meta content="telephone=no, address=no" name="format-detection">
		<meta name="apple-mobile-web-app-capable" content="yes"> <!-- apple devices fullscreen -->
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<!-- Mobile Devices Support @end -->
</head>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/dialog.js"></script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/showdialog.js"></script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/nav.js"></script>
<link href="<?php echo ($staticPath); ?>/tpl/static/repastnew/css/nav.css" rel="stylesheet">
<!--<link rel="stylesheet" type="text/css" href="http://developer.amap.com/Public/css/demo.Default.css"/>--地图样式，这里不需要显示出地图-->
<script language="javascript" src="http://webapi.amap.com/maps?v=1.3&key=b6c2c19cf45cd3d2b82541bc857eed7c"></script>
<script language="javascript">
var mapObj,toolBar,locationInfo;
var locationX,locationY;
//初始化地图对象，加载地图
function mapInit(){
	mapObj = new AMap.Map("iCenter");
	//地图中添加地图操作ToolBar插件
	mapObj.plugin(["AMap.ToolBar"],function(){		
		toolBar = new AMap.ToolBar(); //设置地位标记为自定义标记
		mapObj.addControl(toolBar);		
		AMap.event.addListener(toolBar,'location',function callback(e){	
			locationInfo = e.lnglat;
			if(locationInfo){
			  window.location.href=window.location.href+"&nowlat="+locationInfo.lat+"&nowlng="+locationInfo.lng;
			}
		});
		toolBar.doLocation();
	});
}
	
//获取定位位置信息
function showLocationInfo()
{
	 locationX = locationInfo.lng; //定位坐标经度值
	 locationY = locationInfo.lat; //定位坐标纬度值
}
</script>
<body onselectstart="return true;" ondragstart="return false;" <?php if(!isset($is_dwei)): ?>onLoad="mapInit()"<?php endif; ?>>
	
<div data-role="container" class="container list">
	<section data-role="body" class="section_scroll_content">
		<ul class="list order" id="booklist">
		<div style="color:#888888;padding-left:10px;line-height:30px;font-size:12px;<?php if(isset($is_dwei)): ?>display:none;<?php endif; ?>">定位中...</div>
		<?php if(is_array($company)): $i = 0; $__LIST__ = $company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$com): $mod = ($i % 2 );++$i;?><li>
		<a href="<?php echo U('Repast/ShopPage', array('token'=>$token, 'cid'=>$com['id'], 'wecha_id'=>$wecha_id,'dt'=>$com['distancestr']));?>">
			<table cellpadding="0" cellspacing="0"><tbody>
			<tr><td class="img"><img src="<?php echo ($com['logourl']); ?>"></td><td class="info">
			<div class="name"><?php echo ($com['name']); ?></div><div class="address"><span class="icon addr"></span><label><?php echo ($com['address']); ?></label></div></td><td class="opt">
			<?php if($select == 1): ?><a href="<?php echo U('Repast/dishMenu', array('token'=>$token, 'cid'=>$com['id'], 'wecha_id'=>$wecha_id));?>" onclick="javascript:;" class="btn orange">立即点菜</a>
			<?php else: ?>
			<a href="<?php echo U('Repast/preMeal', array('token'=>$token, 'cid'=>$com['id'], 'wecha_id'=>$wecha_id));?>" onclick="javascript:;" class="btn orange">立即预订</a><?php endif; ?>
			<div><?php if(isset($com['distancestr']) && ($com['distance'] > 0)): echo ($com['distancestr']); endif; ?></div>
			</td></tr></tbody></table>
		</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</section>
	<footer data-role="footer">
		
			
<nav class="nav">
<ul class="box">
	<li>
		<a href="<?php echo U('Index/index', array('token'=>$token, 'wecha_id'=>$wecha_id));?>">
		<span class="home">&nbsp;</span>
		<label>首页</label>				
		</a>
	</li>
	<li <?php if($select == 1): ?>class="on"<?php endif; ?>>
		<a href="<?php echo U('Repast/index', array('token'=>$token, 'st'=>'1','wecha_id'=>$wecha_id));?>">
		<span class="order">&nbsp;</span>
		<label>在线点餐</label>				
		</a>
	</li>
	<li <?php if($select == 2): ?>class="on"<?php endif; ?>>
		<a href="<?php echo U('Repast/index', array('token'=>$token, 'st'=>'2','wecha_id'=>$wecha_id));?>">
		<span class="book">&nbsp;</span>
		<label>在线订位</label>				
		</a>
	</li>
	<li class="more">
		<a href="<?php echo U('Repast/myOrders', array('token'=>$token,'wecha_id'=>$wecha_id));?>">
		<span class="my">&nbsp;</span>
		<label>我的订单</label>
		</a>
	</li>
	<!--
	<li class="more">
		<a href="javascript:;">
		<span class="my">&nbsp;</span>
		<label>我的</label>
		</a>
		<span class="adron"></span>			
		<ul>
			<li>
			<a href="<?php echo U('Repast/myOrders', array('token'=>$token,'wecha_id'=>$wecha_id));?>">我的订单</a>
			</li>
		</ul>
	</li>-->
</ul>
</nav>

</footer>
	<div class="layer transparent"> </div>
	<div class="layer popup">
		<div class="dialogX list">
			<span>选择需点菜的订单</span>
			<ul id="orderInfoList">
			</ul>
			<div class="see"><label><input type="radio" name="orderlist"></label>先看看</div>
		</div>			
	</div>
</div>
<div id="iCenter"style="display:none;"></div>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Repast",
            "moduleID":"<?php echo ($company[0]['id']); ?>",
            "imgUrl": "<?php echo ($company[0]['logourl']); ?>", 
            "timeLineLink": "<?php echo $f_siteUrl . U('Repast/index',array('token' => $token));?>",
            "sendFriendLink": "<?php echo $f_siteUrl . U('Repast/index',array('token' => $token));?>",
            "tTitle": "<?php echo ($metaTitle); ?>",
            "tContent": "<?php echo ($metaTitle); ?>"
        };
</script>
<?php echo ($shareScript); ?>
</body>
</html>