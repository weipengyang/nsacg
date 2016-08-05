<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<title></title>
		<meta charset="utf-8">
		<meta content="" name="description">
		<meta content="" name="keywords">
		<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
		<meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
		<meta content="no-cache" http-equiv="pragma">
		<meta content="0" http-equiv="expires">
		<meta content="telephone=no, address=no" name="format-detection">
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
		<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/forum/css/main.css" ></link>
		<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/forum/css/dialog.css" ></link>
		<script src="<?php echo STATICS;?>/forum/js/jquery_min.js" ></script>
		<script src="<?php echo STATICS;?>/forum/js/main.js" ></script>
		<script src="<?php echo STATICS;?>/forum/js/helper_min.js" ></script>
		<script src="<?php echo STATICS;?>/forum/js/dialog_min.js" ></script>
		<script>

	       function collectTrends(tid, uid){
		        loading(true);
	    	    $.post("/index.php?g=Wap&m=Forum&a=likeAjax&token=<?php echo ($_GET['token']); ?>",{
	    	        "tid"  : tid,
	    	        "uid"  : uid,
		    	    },function(res){
		    	    	loading(false);
		    	        if(res) {
			    	        location.reload();
		    	        }

		    	    });
	        }
	       function praiseTrends(tid, uid){
	    	    loading(true);
	    	    $.post("/index.php?g=Wap&m=Forum&a=favourAjax&token=<?php echo ($_GET['token']); ?>",{
	    	        "tid"  : tid,
	    	        "uid"  : uid,
		    	    },function(res){
		    	    	loading(false);
		    	        if(res) {
			    	        location.reload();
		    	        }

		    	    });
	        }
		</script>
	</head>
	<body onselectstart="return true;" ondragstart="return false;" class="discuss_my discuss_my_tiezi">
		<div id="container" class="container" <?php if($bgurl != NULL): ?>style="background:url('<?php echo ($bgurl); ?>') repeat-y center 0"<?php endif; ?>>
			<header>
				<div class="header_1">
					<ul class="tbox">
						<li>
							<span id="upload_header" class="head_img">
								<img src="<?php echo ($fans["portrait"]); ?>" onerror="this.src='<?php echo STATICS;?>/forum/images/face.png';" />
							</span>
						</li>
						<li>
							<h5 id="nickName"><label><?php echo ($uname); ?></label><!--<a href="javascript:void(0);" class="icons icons_edit">&nbsp;</a>--></h5>
							<p>
								
								<!--<a href="#" class="label"><span class="icons icons_vip_0">&nbsp;</span>非会员</a>-->
							</p>
						</li>
					</ul>
				</div>
				<div class="header_2">
					<nav>
						<ul class="box">
							<li>
								<a href="<?php echo U('Forum/myContent',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
									<label>帖子</label>
									<span><?php echo ($mytopicsnum); ?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo U('Forum/myLike',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
									<label>喜欢</label>
									<span><?php echo ($mylikenum); ?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo U('Forum/myMessage',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>" class="on">
									<label>消息</label>
									<span><?php echo count($list);?></span>
								</a>
							</li>
						</ul>
					</nav>
				</div>
			</header>
			<div class="body">
				<div class="list_article">
					<article>
						<a style="color:#5d5d5d" href="<?php echo U('Userinfo/index',array('token'=>$_GET['token'],'wecha_id'=>$wecha_id,'redirect'=>'Forum/myContent|wecha_id:'.$wecha_id));?>"><div style="margin:10px;text-align:center;">修改个人资料</div></a>
					</article>
				</div>
				<div class="list_article list_message">
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><article>
						<header>
							<ul class="tbox">
								<li>
									<a href="<?php echo U('Forum/otherUser',array('token'=>$_GET['token'],'wecha_id'=>$wecha_id,'uid'=>$list['fromuid']));?>" class="head_img">
										<img src="<?php echo ($list["uinfo"]["portrait"]); ?>" onerror="this.src='<?php echo STATICS;?>/forum/images/face.png';" />
									</a>
								</li>
								<li>
								    <h5><?php echo ($list["fromuname"]); ?></h5>
							    	<p><?php echo date('Y-m-d H:i:s',$list['createtime']);?></p>
								</li>
							</ul>
						</header>
						<section>
							<div>
								<h5><?php echo ($list["content"]); ?></h5>
							</div>
						</section>
						
					</article><?php endforeach; endif; else: echo "" ;endif; ?>
					
				</div>
			</div>
			<footer>
				<section class="nav_footer">
					<ul class="box">
						<li>
							<a href="<?php echo U('Forum/index',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
								<span class="icons_home">&nbsp;</span>
								<label>首页</label>
							</a>
						</li>
						<li>
							<a href="<?php echo U('Forum/add',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>" class="nav_add">
								<span class="icons_home_edit">&nbsp;</span>
							</a>
						</li>
						<li>
							<a href="<?php echo U('Forum/myContent',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>" class="on">
														    <span class="icons_my">&nbsp;</span>
															<label>我的</label>
							</a>
						</li>
					</ul>
				</section>
			</footer>
		</div>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Forum",
            "moduleID":"",
            "imgUrl": "", 
            "timeLineLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "sendFriendLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "weiboLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "tTitle": "<?php echo ($uname); ?>",
            "tContent": "<?php echo ($uname); ?>"
        };
</script>
<?php echo ($shareScript); ?>
		
	</body>
</html>