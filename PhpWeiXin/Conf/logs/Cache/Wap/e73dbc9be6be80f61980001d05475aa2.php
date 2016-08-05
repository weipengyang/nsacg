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
		<style>
            .nav_footer>ul a.a_collect.on{
            	color: #ff0000;
				
            }
            .nav_footer>ul a.on span.icons_love {
            background-position: center -608px;
            }
            
            .list_article>article>section>*{
	            padding-left:10px;
                padding-right:10px;
            }
        </style>
		<script>
			function delComment(thi ,cid){
				alert('确定删除："'+thi.parentNode.innerText.slice(0, 7)+'..."吗？',null, function(){
					loading(true);
					$.ajax({
						url: "<?php echo U('Forum/delComment',array('token'=>$_GET['token'],'wecha_id'=>$wecha_id));?>",
						type:"post",
						data:'cid='+cid,
						success: function(res){
							loading(false);
							
							if(1 == res){
								$(thi).closest("li").remove();
								location.reload();
							}else{
								alert("失败", 1500);
							}
						},
						error: function(){
							loading(false);
							alert("删除失败", 1500);
						}
					});
				}, function(){});
			}

			function showOperate(type, thi, evt){
				var pop_operate = document.getElementById("pop_operate");
				if(type){
					var btns = thi.innerHTML;
					pop_operate.querySelectorAll("div.pop_container")[0].innerHTML = btns;
					pop_operate.classList.add("on");
				}else{
					pop_operate.classList.remove("on");
				}
			}


	       function collectTrends(tid, uid){
		        loading(true);
	    	    $.post("<?php echo U('Forum/likeAjax',array('token'=>$_GET['token']));?>",{
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
	    	    $.post("<?php echo U('Forum/favourAjax',array('token'=>$_GET['token']));?>",{
	    	        "tid"  : tid,
	    	        "uid"  : uid,
		    	    },function(res){
		    	    	loading(false);
		    	        if(res) {
			    	        location.reload();
		    	        }

		    	    });
	        }
	        
	       function commentPraise(id,uid) {
	    	    loading(true);
	    	    $.post("<?php echo U('Forum/commentFavourAjax',array('token'=>$_GET['token']));?>",{
	    	        "id"  : id,
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
	<body onselectstart="return true;" ondragstart="return false;" class="discuss_detail">
		<div id="container" class="container" <?php if($bgurl != NULL): ?>style="background:url('<?php echo ($bgurl); ?>') repeat-y center 0"<?php endif; ?>>
			<header></header>
			<div class="body pt_5">
				<div id="
				" class="list_article">
					<article>
						<section>
								<h5><?php echo ($topics["title"]); ?></h5>
								<h6>
									<?php echo ($topics["uname"]); ?><small><?php echo date('Y-m-d H:i:s',$topics['createtime']);?></small>
								</h6>
											<?php if(!empty($topics['photos'])){ $count = count(explode(',',$topics['photos'])); echo '<figure data-count="'.$count.'张图片">
													<div>'; $photos = explode(',',$topics['photos']); for($i=0;$i<count($photos);$i++){ echo '<img src="'.$photos[$i].'" id="img'.$i.'" data-src="'.$photos[$i].'" data-gid="g7" onload="preViewImg(this, event);"/>'; } echo '</div></figure>'; } ?>
											<div style="clear:both"></div>	
							    			<div><?php echo htmlspecialchars_decode($topics['content'],ENT_QUOTES);?></div>
						</section>
						<footer>
							<ul class="box">
								<li>
									<a href="javascript:;" class="a_collect"><span class="number"><label></label><?php echo ($cnum); ?></span>评论</a>
								</li>
								<li>
                                	<a href="javascript:;" class="a_like <?php if(in_array($wecha_id,explode(',',$topics['favourid']))){echo 'on';}else{ } ?>" onclick="praiseTrends(<?php echo ($topics["id"]); ?>,'<?php echo ($wecha_id); ?>');"><span class="icons icons_like">&nbsp;</span><label><?php if(empty($topics['favourid'])): ?>0<?php else: echo count(explode(',',$topics['favourid'])); endif; ?></label></a>
								</li>
								<li>
								    <a href="javascript:;" class="a_collect <?php if(in_array($wecha_id,explode(',',$topics['likeid']))){echo 'on';}else{ } ?>" onclick="collectTrends(<?php echo ($topics["id"]); ?>,'<?php echo ($wecha_id); ?>');" ><span class="icons icons_collect" >&nbsp;</span><label><?php if(empty($topics['likeid'])): ?>0<?php else: echo count(explode(',',$topics['likeid'])); endif; ?></label></a>
								</li>
							</ul>
							
						</footer>
					</article>
				</div>

				<div>
					<ul id="list_comment" class="list_comment">
						<?php if(is_array($comment)): $i = 0; $__LIST__ = $comment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$comment): $mod = ($i % 2 );++$i;?><li>
							<dl class="tbox">
								<dd>
									<span class="head_img">
										<img src="<?php echo ($comment["uinfo"]["portrait"]); ?>" onerror="this.src='<?php echo STATICS;?>/forum/images/face.png';" />
									</span>
								</dd>
								<dd><!-- xx  reply xx -->
									<h7><?php if($comment['replyid'] != NULL): echo ($comment["uname"]); ?> <font style="font-weight:bold;color:blue">回复</font> <?php echo ($comment["reuname"]); else: echo ($comment["uname"]); endif; ?></h7>
									<p><?php echo (htmlspecialchars_decode($comment["content"])); if($comment['uid'] == $wecha_id): ?><span class="icons icons_del" onclick="delComment(this,<?php echo ($comment["id"]); ?>);">&nbsp;</span><?php endif; ?></p>
									<time>
										<?php echo date('Y-m-d H:i:s',$comment['createtime']);?>
										<span>赞 <?php if($comment['favourid'] != NULL): echo count(explode(',',$comment['favourid'])); else: ?>0<?php endif; ?></span>
									</time>
								</dd>
								<dd>
									<span class="icons icons_op" onclick="showOperate(true, this, event);">
										<a href="/index.php?g=Wap&m=Forum&a=recomment&cid=<?php echo ($comment["id"]); ?>&tid=<?php echo ($topics["id"]); ?>&reid=<?php echo ($comment["uid"]); ?>&wecha_id=<?php echo ($wecha_id); ?>&token=<?php echo ($_GET['token']); ?>" class="btn">回复</a>
										<a href="javascript:commentPraise(<?php echo ($comment["id"]); ?>,'<?php echo ($wecha_id); ?>');" class="btn"><?php if(in_array($wecha_id,explode(',',$comment['favourid']))): ?>取消赞<?php else: ?>赞<?php endif; ?></a>
										
										<a href="javascript:showOperate(false);" class="btn calcel">取消</a>
									</span>
								</dd>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>	
					</ul>
			    </div>
			</div>
			<footer>
				<section class="nav_footer">
					<ul class="box">
						<li>
							<a href="<?php echo U('Forum/index',array('wecha_id'=>$wecha_id,'token'=>$_GET['token']));?>">
								<span class="icons_back">&nbsp;</span>
								<label>返回</label>
							</a>
						</li>
						<li>
							<a href="<?php echo U('Forum/commentAdd',array('wecha_id'=>$wecha_id,'token'=>$_GET['token'],'tid'=>$topics['id']));?>">
								<span class="icons_comment">&nbsp;</span>
								<label>评论</label>
							</a>
						</li>
						<li>
    						<a href="javascript:;" <?php if(in_array($wecha_id,explode(',',$topics['favourid']))){echo 'class="on"';}else{ } ?> onclick="praiseTrends(<?php echo ($topics["id"]); ?>,'<?php echo ($wecha_id); ?>');">	
								<span class="icons_like">&nbsp;</span>
								<label>赞</label>
    						</a>
    						</li>
    						<li>
    						    <a href="javascript:;" <?php if(in_array($wecha_id,explode(',',$topics['likeid']))){echo 'class="on"';}else{ } ?> onclick="collectTrends(<?php echo ($topics["id"]); ?>,'<?php echo ($wecha_id); ?>');">
									<span class="icons_love">&nbsp;</span>
    								<label>喜欢</label>
    							</a>
    						</li>
					</ul>
				</section>
			</footer>
		</div>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Forum",
            "moduleID":"<?php echo ($topics["id"]); ?>",
            "imgUrl": document.getElementById('img0').src, 
            "timeLineLink": "<?php echo C('site_url'); echo U('Forum/comment',array('token'=>$_GET['token'],'tid'=>intval($_GET['tid'])));?>",
            "sendFriendLink": "<?php echo C('site_url'); echo U('Forum/comment',array('token'=>$_GET['token'],'tid'=>intval($_GET['tid'])));?>",
            "weiboLink": "<?php echo C('site_url'); echo U('Forum/comment',array('token'=>$_GET['token'],'tid'=>intval($_GET['tid'])));?>",
            "tTitle": "<?php echo ($topics["title"]); ?>",
            "tContent": "<?php echo (strip_tags(htmlspecialchars_decode($topics["content"]))); ?>"
        };
		
</script>
<?php echo ($shareScript); ?>
	</body>
	
	<section id="pop_operate" class="pop_operate">
		<div class="pop_container" onclick="showOperate(false);">
			<a href="javascript:;" class="btn">回复</a>
			<a href="javascript:;" class="btn">赞</a>
			<a href="javascript:showOperate(false);" class="btn calcel">取消</a>
		</div>
		<div class="pop_masker"></div>
	</section>

</html>