<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>赠送卡券</title>
<meta http-equiv="MSThemeCompatible" content="Yes" />
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/style_2_common.css?BPm" />
<script src="<?php echo RES;?>/js/common.js" type="text/javascript"></script>
<link href="<?php echo RES;?>/css/style.css" rel="stylesheet" type="text/css" />
<style>

	.name {
		font-size:22px;
		text-align:left;
		font-weight:bold;
		
	}
	#nv_member {
		background: #fff;
	}



</style>
</head>
<body id="nv_member" >

<form class="form" method="post" id="form" action=""> 
<input type="hidden" name="uid" value="<?php echo ($uinfo["id"]); ?>" />
<input type="hidden" name="cardid" value="<?php echo ($cardid); ?>" />
    <div class="msgWrap bgfc"> 
	 <p class="name"><?php echo ($truename); ?>（<?php echo ($carno); ?>）</p>
     <table class="userinfoArea" style=" margin-left:100px;" border="0" cellspacing="0" cellpadding="0" width="70%"> 
         <thead>
             <tr>
             <th>券名称</th>
              <th>赠送数量(张)</th> 
             </tr>
         </thead>
      <tbody> 
          <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                  <td><?php echo ($list["title"]); ?></td>
                  <td><input type="text" name="<?php echo ($list["id"]); ?>" value="0" class="px" style="width:150px;" /></td>
              </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              <tr>
                  <th><br /></th>
                  <td><br /></td>
              </tr>
              <tr>
               <th>&nbsp;</th>
                  <td>
                      <input type="hidden" name="groupon" value="1" />
                      <button type="submit" name="button" class="btnGreen">确认</button>
                  </td>
              </tr>
          </tbody> 
     </table> 
     </div>
    
   </form> 
   
</body>
</html>