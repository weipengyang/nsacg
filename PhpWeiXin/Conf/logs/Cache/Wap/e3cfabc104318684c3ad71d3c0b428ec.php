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
		<script src="<?php echo STATICS;?>/forum/js/dialog_min.js" ></script>

	

		<script>
	       function collectTrends(tid, uid){
		        loading(true);
	    	    $.post("<?php echo U('Forum/likeAjax',array('token'=>$_GET['token'],'wecha_id'=>"+uid+"));?>",{
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
	    	    $.post("<?php echo U('Forum/favourAjax',array('token'=>$_GET['token'],'wecha_id'=>"+uid+"));?>",{
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
	<body onselectstart="return true;" ondragstart="return false;" class="discuss_index">

		<!-- 提示关注 -->
		<?php if($wecha_id == NULL): echo ($memberNotice); endif; ?>
		
<?php if($bgurl != NULL): ?><style type="text/css">
		.container{
			background-image:url('<?php echo ($bgurl); ?>');
			background-attachment: inherit;
			background-size: cover;
			background-position: 50% 50%;
			background-repeat: no-repeat;
		}
	</style><?php endif; ?>
		<div id="container" class="container">
			
			<header>
				<div class="header_1">
					<ul class="tbox" >
						<li>
							<span class="head_img">
								<img src="<?php echo ($config["logo"]); ?>" onerror="this.src='<?php echo STATICS;?>/forum/images/face.png';" />
							</span>
						</li>
						<li>
							<h5><?php echo ($config["forumname"]); ?></h5>
							<p>
								<a href="javascript:;" class="label"><span class="icons icons_post">&nbsp;</span><?php echo ($count); ?>帖子</a>
								<a href="javascript:;" class="label"><span class="icons icons_visisted">&nbsp;</span><?php echo ($config["pv"]); ?>访问</a>
							</p>
						</li>
					</ul>
				</div>
			</header>
			
			<div class="body">
				<div id="list_article" class="list_article">
					
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><article><!--帖子-->
					
						    <header>
    							<ul class="tbox">
    								<li><!--头像-->
    									<a href="<?php echo U('Forum/otherUser',array('wecha_id'=>$wecha_id,'uid'=>$list['uid'],'token'=>$_GET['token']));?>" class="head_img">
    										<img src="<?php echo ($list["uinfo"]["portrait"]); ?>" onerror="this.src='<?php echo STATICS;?>/forum/images/face.png';" />
									    </a>
    								</li>
									
    								<li><!--用户名、发布时间-->
    									<h5><?php echo ($list["uname"]); ?></h5>
									    <p><?php echo date('Y-m-d H:i:s',$list['createtime']);?></p>
    								</li>
    							</ul>
    						</header>
				    		
							<section>
							
								<?php if($list['photoArr'] != ''): ?><figure data-count="<?php echo (count($list["photoArr"])); ?>张图片">
										<div>
											<?php $__FOR_START_17589__=0;$__FOR_END_17589__=count($list['photoArr']);for($i=$__FOR_START_17589__;$i < $__FOR_END_17589__;$i+=1){ ?><img src="<?php echo ($list['photoArr'][$i]); ?>" data-src="<?php echo ($list['photoArr'][$i]); ?>" data-gid="g7" onload="preViewImg(this, event);"/><?php } ?>
										</div>
									</figure><?php endif; ?>
										
								<div style="clear:both"></div>				
    							<div>
    								<h5><?php echo ($list["title"]); ?></h5><!--发帖标题-->
								    <div><?php echo htmlspecialchars_decode($list['content'],ENT_QUOTES);?></div><!--发帖内容-->
							    </div>
							    <a href="/index.php?g=Wap&m=Forum&a=comment&tid=<?php echo ($list["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>">查看全文</a>
								
    						</section>
							
    						<footer>
    						    <ul class="box">
    								<li><!--喜欢-->
										<a href="javascript:;" class="a_collect <?php if(in_array($wecha_id,explode(',',$list['likeid']))){echo 'on';}else{ } ?>" onclick="collectTrends(<?php echo ($list["id"]); ?>,'<?php echo ($wecha_id); ?>')" ><span class="icons icons_collect" >&nbsp;</span><label><?php if(empty($list['likeid'])): ?>0<?php else: echo count(explode(',',$list['likeid'])); endif; ?></label></a>
    								</li>
    								<li><!--评论-->
    									<a href="/index.php?g=Wap&m=Forum&a=comment&tid=<?php echo ($list["id"]); ?>&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="a_comment"><span class="icons icons_comment" >&nbsp;</span><label><?php echo ($list["cnum"]); ?></label></a>
    								</li>
    								<li><!--赞-->
    								    <a href="javascript:;" class="a_like <?php if(in_array($wecha_id,explode(',',$list['favourid']))){echo 'on';}else{ } ?>" onclick="praiseTrends(<?php echo ($list["id"]); ?>,'<?php echo ($wecha_id); ?>')"><span class="icons icons_like">&nbsp;</span><label><?php if(empty($list['favourid'])): ?>0<?php else: echo count(explode(',',$list['favourid'])); endif; ?></label></a>
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
							<a href="/index.php?g=Wap&m=Forum&a=index&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="on">
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
							<a href="/index.php?g=Wap&m=Forum&a=myMessage&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>">
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
            "moduleID":"0",
            "imgUrl": "<?php echo ($config["logo"]); ?>", 
            "timeLineLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "sendFriendLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "weiboLink": "<?php echo C('site_url'); echo U('Forum/index',array('token'=>$_GET['token']));?>",
            "tTitle": "<?php echo ($config["forumname"]); ?>",
            "tContent": "<?php echo ($config["forumname"]); ?>"
        };
</script>
<?php echo ($shareScript); ?>

	</body>

	
<script type="text/javascript">
    $(document).ready(function(){ 
        var range = 50;             //距下边界长度/单位px  
		var _srollPos = 0;
		var pageNum = 2;
        var totalheight = 0; 
        var main = $("#list_article");
		var js = 0;
        $(window).scroll(function(){
            var srollPos = $(window).scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  
           if(srollPos<_srollPos){
                return;
            }else{
            	_srollPos = srollPos;
            }
            totalheight = parseFloat($(window).height()) + parseFloat(srollPos);  
            if(($(document).height()-range) <= totalheight ) {
			
					$.ajax({
						url: "/index.php?g=Wap&m=Forum&a=moreList&token=<?php echo ($_GET['token']); ?>&wecha_id=<?php echo ($wecha_id); ?>&p="+pageNum,
						type:"post",
						data:"pageNum="+pageNum,
						success: function(res){
							if(res == 0){
								if(js == 0){
									$('#list_article').append('<article style="display:block" id="noMore"><header style="margin:7px;text-align:center;color:#828282;">没有更多帖子了</header></article>');
								}
								js = 1;
							}else{
								main.append(res);
								pageNum ++;
								
							}
							
						}
					});
            }
        });
		
    }); 
</script> 
</html>