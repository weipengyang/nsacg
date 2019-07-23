<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo ($vote["title"]); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width,height=device-height,maximum-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="description" content="<?php echo ($vote["title"]); ?>">

<link href="<?php echo STATICS;?>/vote/css/vote.css" rel="stylesheet" type="text/css">
<script src="<?php echo STATICS;?>/vote/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo STATICS;?>/vote/js/jquery.icheck.min.js" type="text/javascript"></script>
<style>
#vote-img li {width: 50%;float: left;padding: 0 1%;width: 48%;}
.votecontent .disabled{background: #a9a9a9;border-color: #a1a1a1;}

.beizhu {
    background-color: #FFF5C5;
    border: 1px solid #EDE17E;
    border-radius: 5px;
    box-shadow: 0 1px 1px #F6F6F6;
    color: #BCA24B;
    font-size: 14px;
    line-height: 22px;
    margin: 11px 0 8px;
    padding: 4px 10px 5px;
    text-align: center;
}

.deploy_ctype_tip{z-index:1001;width:100%;text-align:center;position:fixed;top:50%;margin-top:-23px;left:0;}.deploy_ctype_tip p{display:inline-block;padding:13px 24px;border:solid #d6d482 1px;background:#f5f4c5;font-size:16px;color:#8f772f;line-height:18px;border-radius:3px;}

</style>
</head>
<body id="vote-<?php if($vote['type'] == 'text'): ?>text<?php else: ?>img<?php endif; ?>" class="wrap">

<div class="vote">
 <form class="form" method="post" action="" target="_top" enctype="multipart/form-data">
<div class="votecontent">
  <h2><?php echo ($vote["title"]); ?></h2>
  <span class="date"><?php echo (date("Y-m-d",$vote["statdate"])); ?> / <?php echo (date("Y-m-d",$vote["enddate"])); ?></span>
   <?php if(($vote['picurl'] != '') AND ($vote['showpic'] == 1) ): ?><div class="voteimg"><img src="<?php echo ($vote["picurl"]); ?>"></div><?php endif; ?> 
   <p class="content"><?php echo (htmlspecialchars_decode(htmlspecialchars_decode($vote['info']))); ?></p>
 

 <p class="modus"><?php if($vote['cknums'] == 1): ?>单选<?php else: ?>多选<?php endif; ?>投票，<span class="number">共有<?php echo ($vote['count']); ?> 人参与投票</span></p>
 <ul class="list" id="list">
<?php if(is_array($vote_item)): $key = 0; $__LIST__ = $vote_item;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($key % 2 );++$key; if($vote['type'] == 'text'): ?><!--文字投票开始-->  

    <li>
      <label for="square-checkbox-<?php echo ($key); ?>">
          <input class="ckbx" tabindex="9" name="vid[]" value="<?php echo ($li["id"]); ?>" type="<?php if($vote['cknums'] == 1): ?>radio<?php else: ?>checkbox<?php endif; ?>" id="square-checkbox-<?php echo ($key); ?>" style="position: absolute; opacity: 0;">
          <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
        <span><?php echo ($li["item"]); ?></span>
      </label>    
      <div id="voteshow1" class="votebar">
          <div class="pbg">
            <div style="width: <?php echo (($li["per"])?($li["per"]):0); ?>%;background-color:#ffcc00" class="pbr"></div>
          </div>
          <span class="percentage" style="color:#ffcc00"><?php echo ($li["per"]); ?>%<span class="user">(<?php echo ($li['pro']); ?>)</span></span>
        </div>                    
    </li>  

  <?php else: ?> <!--图片投票开始--> 

    <li>
        <p class="voteimg2">
            <a href="<?php if($li["tourl"] == '#' or ($li["tourl"] == '')): ?>javascript:void(0);<?php else: echo ($li["tourl"]); endif; ?>">
              <img src="<?php echo ($li["startpicurl"]); ?>">
            </a>
        </p>
        <label for="square-checkbox-<?php echo ($key); ?>">
          <input tabindex="9" name="vid[]"   value="<?php echo ($li["id"]); ?>" type="<?php if($vote['cknums'] == 1): ?>radio<?php else: ?>checkbox<?php endif; ?>" id="square-checkbox-<?php echo ($key); ?>" style="position: absolute; opacity: 0;">
          <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
        <span><?php echo ($li["item"]); ?></span>
      </label>
        <div id="voteshow1" class="votebar">
          <div class="pbg">
            <div style="width: <?php echo (($li["per"])?($li["per"]):0); ?>%;background-color:#ffcc00" class="pbr"></div>
          </div>
          <span class="percentage" style="color:#ffcc00"><?php echo ($li["per"]); ?>%<span class="user">(<?php echo ($li['pro']); ?>)</span></span>
        </div>
    </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>

             
<div class="clr"></div>
 </ul>
      <?php if($success["err"] == 1): ?><a href="javascript:showTip('等待投票开始');" class="pxbtn disabled"><?php echo ($success["info"]); ?></a>
      
      <?php elseif($success["err"] == 2): ?>  
        <a href="javascript:showTip('投票已经结束了');" class="pxbtn disabled"/><?php echo ($success["info"]); ?></a>
     
      <?php elseif($success["err"] == 3): ?>  
        <a href="javascript:showTip('请不要重复投票');" class="pxbtn disabled"/><?php echo ($success["info"]); ?></a>
       <!-- <p class="beizhu">
          你投了：
          <span class="number">
                <?php echo ($success['hasitems']); ?>
          </span>
        </p>     --> 
      <?php else: ?>
          <a href="javascript:void(0);" class="pxbtn" id="sub">我要投票</a><?php endif; ?>
  </div>

</form>
</div>    

<script>
  $(document).ready(function(){
     $('.list input').each(function(){
       var self = $(this),
        label = self.next(),
        label_text = label.text();
        label.remove();
       self.iCheck({
         checkboxClass: 'icheckbox_flat',
         radioClass: 'iradio_flat',
         insert: '<div class="icheck_line-icon"></div>' + label_text
       });

    });

  $("#sub").bind("click",function(){

    var self = $(this);
    var chk = document.getElementsByName('vid[]');
    var objarray = chk.length;
    var chid = "";
    var wecha_id = "<?php echo ($wecha_id); ?>";
    var token  = "<?php echo ($token); ?>";
    var tid = "<?php echo ($id); ?>";

    for (i=0;i<objarray;i++)
    {
      if(chk[i].checked == true)
      {
         chid+=chk[i].value+",";
      }
    }

    if(wecha_id == '' && <?php echo ($vote['is_reg']); ?> == 1){
      showTip("请关注“<?php echo ($user_name); ?>”然后发送关键词“<?php echo ($vote["keyword"]); ?>”再进行投票");
      return;
    }

    if(chid == ""){
      showTip("请选择投票的选项");
      return;
    }else{
        var submitData={
            wecha_id : wecha_id,
            tid      : tid,
            chid     : chid,
            token    : token,
            action   : "add_vote"
        };
        $.post('index.php?g=Wap&m=Vote&a=add_vote&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>', submitData, function(bakcdata) {
          var obj=eval('('+bakcdata+')');
            showTip(obj.info);
            if(obj.success == 1){
               setTimeout(function(){window.location.reload()},2000);
            }else if(obj.success == -5){
              setTimeout(function(){
                window.location="<?php echo U('Userinfo/index',array('token'=>$token,'wecha_id'=>$wecha_id,'redirect'=>Vote.'/index|id:'.intval($_GET['id'])));?>";
              },2000);
            }
        });
    }
  });

});

function showTip(tipTxt) {
  var div = document.createElement('div');
  div.innerHTML = '<div class="deploy_ctype_tip"><p>' + tipTxt + '</p></div>';
  var tipNode = div.firstChild;
  $(".wrap").after(tipNode);
  setTimeout(function () {
    $(tipNode).remove();
  }, 1500);
}
</script>

<script type="text/javascript">
window.shareData = {  
            "moduleName":"Vote",
            "moduleID":"<?php echo ($vote["id"]); ?>",
            "imgUrl": "<?php echo ($vote["picurl"]); ?>",
            "timeLineLink":"<?php echo C('site_url'); echo U('Vote/index',array('token'=>$token,'id'=>$id));?>",
            "sendFriendLink":"<?php echo C('site_url'); echo U('Vote/index',array('token'=>$token,'id'=>$id));?>",
            "weiboLink": "<?php echo C('site_url'); echo U('Vote/index',array('token'=>$token,'id'=>$id));?>",
            "tTitle": "<?php echo ($vote["title"]); ?>",
            "tContent": "<?php echo ($vote["title"]); ?>",
        };
</script>
<?php echo ($shareScript); ?>

</body>
</html>