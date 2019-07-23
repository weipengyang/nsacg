<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>会员等级变更</title>
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
        <table class="userinfoArea" style=" margin-left:20px;" border="0" cellspacing="0" cellpadding="0" width="70%">
            <tr>
                <th>姓名：</th>
                <td><?php echo ($truename); ?></td>
            </tr>
            <tr>
                <th>车牌号：</th>
                <td><?php echo ($carno); ?></td>
            </tr>

            <tr>
                <td>会员等级：</td>
                <td>
                    <select id="newcardid">
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><option id="<?php echo ($list["id"]); ?>" value="<?php echo ($list["id"]); ?>"><?php echo ($list["cardname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td>
                    <button type="button" name="button" class="btnGreen" onclick="changeGrade();">确认</button>
                </td>
            </tr>
        </table>
     </div>
    
   </form> 
    <script src="/tpl/static/jquery.min.js" type="text/javascript"></script>
    <script>
        var jQ = jQuery.noConflict();
        function changeGrade() {
            var data = {
                newcardid: jQ("#newcardid").val()
            };
            jQ.post(location.href,data,
            function (message) {
                alert(message);
                top.location.href = top.location.href;
                top.art.dialog({ id: "changegrade" }).close();
            });
        }
    </script>
</body>
</html>