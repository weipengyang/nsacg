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
							<!--	
								<a href="#" class="label"><span class="icons icons_vip_0">&nbsp;</span>非会员</a>
							-->
							</p>
						</li>
					</ul>
				</div>
				<div class="header_2">
					<nav>
						<ul class="box">
							<li>
								<a href="<?php echo U('Forum/myContent',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>" class="on">
									<label>帖子</label>
									<span><?php echo count($list);?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo U('Forum/myLike',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
									<label>喜欢</label>
									<span><?php echo ($mylikenum); ?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo U('Forum/myMessage',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
									<label>消息</label>
									<span><?php echo ($mymessagenum); ?></span>
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
			
				<div class="list_article">
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><article>
						<header>
							<div class="article_state">
								<?php if($list['status'] == -1): ?><span class="state1" style="background:red">暂未审核</span><?php else: ?><span class="state1">审核通过</span><?php endif; ?>
							</div>
							<ul class="tbox">
								<li></li>
								<li>
									<h5><?php echo ($list["title"]); ?></h5>
									<p><?php echo date('Y-m-d H:i:s',$list['createtime']);?></p>
								</li>
								<li>
									<a href="/index.php?g=Wap&m=Forum&a=myContentEdit&tid=<?php echo ($list["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="icons icons_edit">&nbsp;</a>
								</li>
	
							</ul>
						</header>
						<section>
											<?php if(!empty($list['photos'])){ $count = count(explode(',',$list['photos'])); echo '<figure data-count="'.$count.'张图片">
													<div>'; $photos = explode(',',$list['photos']); for($i=0;$i<count($photos);$i++){ echo '<img src="'.$photos[$i].'" data-src="'.$photos[$i].'" data-gid="g7" onload="preViewImg(this, event);"/>'; } echo '</div></figure>'; } ?>
											<div style="clear:both"></div>	
							<div>
								<div><?php echo htmlspecialchars_decode($list['content'],ENT_QUOTES);?></div>
							</div>
							<a href="/index.php?g=Wap&m=Forum&a=comment&tid=<?php echo ($list["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>">查看全文</a>
						</section>
						<footer>
							<ul class="box">
								<li>
									<a class="a_collect"><span class="icons icons_collect" >&nbsp;</span><label><?php if($list['likeid'] != NULL): echo count(explode(',',$list['likeid'])); else: ?>0<?php endif; ?></label></a>
								</li>
								<li>
									<a class="a_comment"><span class="icons icons_comment" >&nbsp;</span><label><?php echo ($list["cnum"]); ?></label></a>
								</li>
								<li>
									<a class="a_like"><span class="icons icons_like">&nbsp;</span><label><?php if($list['favourid'] != NULL): echo count(explode(',',$list['favourid'])); else: ?>0<?php endif; ?></label></a>
								</li>
							</ul>
						</footer>
					</article><?php endforeach; endif; else: echo "" ;endif; ?>
					
				</div>
			</div>
			<footer>
				<section class="nav_footer">
					<ul class="box">
						<li>
							<a href="/index.php?g=Wap&m=Forum&a=index&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>">
								<span class="icons_home">&nbsp;</span>
								<label>首页</label>
							</a>
						</li>
						<li>
												    <a href="/index.php?g=Wap&m=Forum&a=add&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="nav_add">
														<span class="icons_home_edit">&nbsp;</span>
							</a>
						</li>
						<li>
							<a href="/index.php?g=Wap&m=Forum&a=myMessage&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="on">
														    <span class="icons_my" data-tip="<?php echo ($messageNum); ?>">&nbsp;</span>
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