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
<script type="text/javascript">
<?php if($isMember AND $discount > 0): ?>var discount="<?php echo ($discount); ?>";
<?php else: ?>
var discount=0;<?php endif; ?>

</script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/dialog.js"></script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/scroller.js"></script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/dmain.js"></script>
<script type="text/javascript" src="<?php echo ($staticPath); ?>/tpl/static/repastnew/js/menu.js"></script>
<body onselectstart="return true;" ondragstart="return false;">
<script type="text/javascript">
var totalPrice=totalNum=0;
<?php if($cid>0){ echo "var mycid=$cid;"; }else{ echo "var mycid=0;"; } ?>

function check_i_like(id,islove){
	$.post("<?php echo U('Repast/doLike', array('token'=>$token, 'cid'=>$cid, 'wecha_id'=>$wecha_id));?>", {did:id,islove:islove}, function(msg){});			
}
var islock=false;
function next(){
	totalPrice=$.trim($('#allmoney').text());
	totalPrice=parseFloat(totalPrice);
	totalNum=$.trim($('#menucount').text());
	totalNum=parseInt(totalNum);
	if((totalNum>0) && (totalPrice>0)){
		var data=getMenuChecklist();//[{'id':id,'count':count},{'id':id,'count':count}]
		if((data.length>0) && !islock){
			islock=true;
			$('#nextstep').removeClass('orange show').addClass('gray disabled');
			$.ajax({
				type: "POST",
				url: "<?php echo U('Repast/processOrder', array('token'=>$token, 'cid'=>$cid, 'wecha_id'=>$wecha_id,'orid'=>$orid));?>",
				data: {"cart":data,"mycid":mycid},
				async:true,
				success: function(res){
					islock=false;
					$('#nextstep').removeClass('gray disabled').addClass('orange show');
					if(res.error==0) //成功
					{ 
					  window.location.href="<?php echo U('Repast/sureOrder', array('token'=>$token, 'cid'=>$cid, 'wecha_id'=>$wecha_id,'orid'=>$orid));?>";
					}else{
					  alert(res.msg);
					}
				},
				dataType: "json"
			  });
			}else{
				return false;
			}
		}else{
			return false;
		}
}

</script>
<div data-role="container" class="container menu">
<section data-role="body">
<div class="left">
	<div class="top">
		<div id="ILike"><a><span class="icon hartblckgray"></span>我喜欢</a></div>
	</div>
	<div class="content">
	<ul id="typeList"><!--class="on"-->
		<?php if(!empty($fenleiarr)): if(is_array($fenleiarr)): $ik = 0; $__LIST__ = $fenleiarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($ik % 2 );++$ik;?><li id="li_type<?php echo ($key); ?>"><?php echo ($item); ?></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	</ul>
	</div>
</div>

<div class="right" id="usermenu">
<div class="all" id="menuList">
	<?php if(!empty($disharr)): if(is_array($disharr)): $dk = 0; $__LIST__ = $disharr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ditem): $mod = ($dk % 2 );++$dk;?><ul id="ul_type<?php echo ($key); ?>">
	   <?php if(is_array($ditem)): $ddk = 0; $__LIST__ = $ditem;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dditem): $mod = ($ddk % 2 );++$ddk;?><li id="dish_li<?php echo ($dditem['id']); ?>" <?php if($dditem['mylove'] == 1): ?>class="like"<?php endif; ?>>
		 <div class="licontent">
			<div class="span showPop">
				<?php if(!empty($dditem['image'])): ?><img src="<?php echo ($dditem['image']); ?>" alt="" url="<?php echo ($dditem['image']); ?>"><?php endif; ?>
				<?php if($dditem['ishot'] == 1): ?><span class="ishot" style="left: -5px;">推荐</span><?php endif; ?>
			   <?php if($isMember AND $discount > 0 AND $dditem['isdiscount']): ?><span class="ishot" style="left: 55px;"><?php echo ($discount); ?>折</span><?php endif; ?>
			</div>
			<div class="menudesc showPop">
				<h3><?php echo ($dditem['name']); ?></h3>
				<p class="salenum">月售<span class="sale_num"> <?php echo ($dditem['m_sale']); ?> </span><?php if(!empty($dditem['unit'])): echo ($dditem['unit']); else: ?>份<?php endif; ?>
				<?php if($kconoff == 1): ?>库存：<?php echo ($dditem['instock']); endif; ?>
				<!--<span class="sales"><strong class="sale_8"></strong></span>--></p>
				<p class="mylovedish"> <span class="icon hart"><input autocomplete="off" class="thisdid" type="hidden" value="<?php echo ($dditem['id']); ?>"></span></p>
				<div class="info"><?php echo (htmlspecialchars_decode($dditem['des'],ENT_QUOTES)); ?></div>
			</div>
			<div class="price_wrap">
				<strong>￥<span class="unit_price"><?php echo ($dditem['price']); ?><input type="hidden" class="tureunit_price" <?php if(isset($dditem['zkprice']) AND $dditem['zkprice'] > 0): ?>value="<?php echo ($dditem['zkprice']); ?>"<?php else: ?>value="<?php echo ($dditem['price']); ?>"<?php endif; ?>></span></strong>
				<div class="fr" <?php if($kconoff == 1): ?>max="<?php echo ($dditem['instock']); ?>" <?php else: ?>max="-1"<?php endif; ?>>
				    <?php if($kconoff == 0 OR $dditem['instock'] > 0): ?><a href="javascript:void(0);" class="btn plus" <?php if(isset($dditem['ornum']) && !empty($dditem['ornum'])) echo "data-num=".$dditem['ornum']; else echo "data-num=''"; ?>></a><?php endif; ?>
				</div>
				<input autocomplete="off" class="number" type="hidden" name="dish[<?php echo ($dditem['id']); ?>]" value="0">
			</div>
			</div>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul><?php endforeach; endif; else: echo "" ;endif; endif; ?>
 </div>
</div>
</section>
</div>
	<footer data-role="footer">			
		<nav class="g_nav">
			<div>
				<span class="cart"></span>
				<span> <span class="money">￥<label id="allmoney">0</label> </span>/<label id="menucount">0</label>个菜</span>
				<a href="javascript:next();" class="btn gray disabled" id="nextstep">选好了</a>
			</div>
		</nav>
	</footer>

	<div class="menu_detail" id="menuDetail">
		<img style="display: none;">
		<div class="nopic"></div>
		<a href="javascript:void(0);" class="comm_btn" id="detailBtn">来一<?php if(!empty($dditem['unit'])): echo ($dditem['unit']); else: ?>份<?php endif; ?></a>
		<dl>
			<dt>价格：</dt>
			<dd class="highlight">￥<span class="price"></span></dd>
		</dl>
		<p class="sale_desc"></p>
		<dl class="desc">
			<dt>介绍：</dt>
			<dd class="info"></dd>
		</dl>
	</div>

<script type="text/javascript">
window.shareData = {  
            "moduleName":"Repast",
            "moduleID":"<?php echo ($company['id']); ?>",
            "imgUrl": "<?php echo ($company['logourl']); ?>", 
            "timeLineLink": "<?php echo $f_siteUrl . U('Repast/dishMenu',array('token' => $token,'cid'=>$cid));?>",
            "sendFriendLink": "<?php echo $f_siteUrl . U('Repast/dishMenu',array('token' => $token,'cid'=>$cid));?>",
            "tTitle": "<?php echo ($metaTitle); ?>",
            "tContent": "<?php echo ($metaTitle); ?>"
        };

</script>
<?php echo ($shareScript); ?>

</body>
</html>