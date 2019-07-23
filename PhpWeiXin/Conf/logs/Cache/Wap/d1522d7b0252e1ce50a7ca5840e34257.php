<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /><meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta name="format-detection" content="telephone=no"/>
<title><?php echo ($metaTitle); ?></title>
<script src="../tpl/static/hotels/js/piwik.js"></script>
<script src="../tpl/static/hotels/js/zepto.min.js"></script>
<script src="../tpl/static/hotels/js/underscore-min.js"></script>
<script src="../tpl/static/hotels/js/backbone-min.js"></script>
<script src="../tpl/static/hotels/js/jquery.hammer.min.js"></script>
<script src="../tpl/static/hotels/js/common.js"></script>
<script src="../tpl/static/hotels/js/hotel.js"></script>
<link rel="stylesheet" type="text/css" href="../tpl/static/hotels/css/common.css" media="all" />
</head>
<body>
	<div class="html">
		<div class="main">
			<header style="border-bottom: 1px solid #732124;background: #9C1A24;">
				<h1>商城浏览</h1>
			</header>
			<div class="contxt cont-artical">
				<?php if(is_array($company)): $i = 0; $__LIST__ = $company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$com): $mod = ($i % 2 );++$i;?><div class="box-cont box-list-hotel">
					<div class="list-li" onclick="javascript:window.location.href='<?php echo U('Store/cats',array('token' => $_GET['token'], 'wecha_id' => $wecha_id, 'cid' => $com['id'], 'twid' => $twid));?>'">
					<div class="hd"><img src="<?php echo ($com['logourl']); ?>" height="193px"></div>
					<div class="bd">
					<b><?php echo ($com['name']); ?></b>
					<p>
					<span class="tleft"><?php echo ($com['address']); ?></span>
					</p>
					</div>
					<div class="ft"><span class="arrow arr-small"></span></div>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
				<div class="tcenter"></div>
				<div class="tcenter"><div class="isloading loading" style="display: none">加载中...</div></div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Store",
            "moduleID":"<?php echo ($company['id']); ?>",
            "imgUrl": "<?php echo ($company['logourl']); ?>", 
            "timeLineLink": "<?php echo ($f_siteUrl); echo U('Store/index',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Store/index',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "weiboLink": "<?php echo ($f_siteUrl); echo U('Store/index',array('token' => $_GET['token'], 'twid' => $mytwid, 'cid' => $cid));?>",
            "tTitle": "<?php echo ($metaTitle); ?>",
            "tContent": "<?php echo ($metaTitle); ?>"
        };
</script>
<?php echo ($shareScript); ?>
</html>