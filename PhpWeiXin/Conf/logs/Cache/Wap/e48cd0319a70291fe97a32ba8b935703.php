<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">   
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

<title><?php echo ($metaTitle); ?></title>
<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
<link href="http://at.alicdn.com/t/font_1452909907_4403944.css" rel="stylesheet" type="text/css" />
</head>
<body >
<div class="page" style="margin:0px">
  <header class="bar bar-nav" >
  <div class="searchbar">
    <a class="searchbar-cancel">取消</a>
    <div class="search-input">
    	  <input type="hidden" name="wecha_id" value="<?php echo ($wecha_id); ?>" />
        <input type="hidden" name="token" value="<?php echo ($token); ?>" />
        <input type="hidden" name="twid" value="<?php echo ($twid); ?>" />
      <label class="icon icon-search" for="search"></label>
      <input type="search" id='search' placeholder='输入关键字...'/>
    </div>
  </div>

  </header>
  <nav class="bar bar-tab">
  	<a class="tab-item" href="#" onclick="history.go(-1);" >
      <span class="icon icon-left"></span>
      <span class="tab-label">返回</span>
    </a>
    <a class="tab-item" href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
      <span class="icon icon-home"></span>
      <span class="tab-label">首页</span>
    </a>
      <a class="tab-item" href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>">
     <span class="icon icon-cart"></span>
     <?php if($productcount != 0): ?><span class="badge" id='badge'><?php echo ($productcount); ?></span>
        <?php else: ?>
        <span class="badge" id='badge' style="display:none"><?php echo ($productcount); ?></span><?php endif; ?>
    <span class="tab-label">购物车</span>
   </a>
    <a class="tab-item" href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
      <span class="icon icon-me"></span>
      <span class="tab-label">我</span>
    </a>
  </nav>
  <div class="content" style="margin:0px">
  <div class="list-block media-list" style="margin:0px">
  <ul id="m_list">
    <?php if(is_array($products)): $i = 0; $__LIST__ = $products;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hostlist): $mod = ($i % 2 );++$i;?><li>
        	<a href="<?php echo U('Store/product',array('token' => $_GET['token'], 'id' => $hostlist['id'], 'wecha_id' => $_GET['wecha_id'], 'twid' => $twid));?>" class="item-link item-content" data-no-cache="true">
          <div class="item-media"><img src="<?php echo ($hostlist["logourl"]); ?>" style='width: 120px;height:100px'></div>
         <div class="item-inner">
         	 <div class="item-title" style="font-size:15px;"><?php echo ($hostlist["name"]); ?></div>
          <div class="item-title" style="color:#FF0000;font-size:12px;"><del>原价:￥<?php echo ($hostlist["oprice"]); ?>元</del></div>
          <div class="item-title" style="color:#0055CC;font-size:12px;"><span >积分卡会员价：<em id="vprice">￥<?php echo ($hostlist["discount1"]); ?></em></span></div>
          <div class="item-title" style="color:#0055CC;font-size:12px;"> <span >白金卡会员价：<em id="vprice">￥<?php echo ($hostlist["discount2"]); ?></em></span></div>
          <div class="item-title" style="color:#0055CC;font-size:12px;"> <span >钻石卡会员价：<em id="vprice">￥<?php echo ($hostlist["discount3"]); ?></em></span></div>
          <div class="item-title" style="color:#0055CC;font-size:12px;"><span style="color:#999;font-size:10px;margin-top:15px;"><?php echo ($hostlist['salecount'] + $hostlist['fakemembercount']); ?>人付款</span></div>
					</div>	
        </a>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
</div>
</div>
</div>
<script>$.init()</script>
</body>
<script type="text/javascript">
    window.shareData = {
        "moduleName":"Store",
        "moduleID":"<?php echo ($products[0]['id']); ?>",
        "imgUrl": "<?php echo ($products[0]['logourl']); ?>",
        "timeLineLink": "<?php echo ($f_siteUrl); echo U('Store/products',array('token' => $_GET['token'], 'catid' => $thisCat['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Store/products',array('token' => $_GET['token'], 'catid' => $thisCat['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "weiboLink": "<?php echo ($f_siteUrl); echo U('Store/products',array('token' => $_GET['token'], 'catid' => $thisCat['id'], 'twid' => $mytwid, 'cid' => $cid));?>",
        "tTitle": "<?php echo ($metaTitle); ?>",
        "tContent": "<?php echo ($metaTitle); ?>"
    };
</script>

</html>