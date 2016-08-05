<?php if (!defined('THINK_PATH')) exit();?>﻿<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>修改消费密码</title>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<link href="<?php echo RES;?>/css/userinfo/fans.css" rel="stylesheet" type="text/css"> 
<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>  -->
<script type="text/javascript" src="/tpl/User/default/common/js/select/js/jquery.js"></script>
<script type="text/javascript">window.jQuery || document.write('<script type="text/javascript" src="<?php echo RES;?>/css/guajiang/js/jquery.js">\x3C/script>')</script>
<script src="/tpl/static/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="/tpl/static/artDialog/plugins/iframeTools.js"></script>
<script src="/tpl/static/upyun.js?2013"></script>
<script src="/tpl/static/alert.js"></script>
<style>

.footFix{width:100%;text-align:center;position:fixed;left:0;bottom:0;z-index:99;}
#footReturn a, #footReturn2 a {
display: block;
line-height: 41px;
color: #fff;
text-shadow: 1px 1px #282828;
font-size: 14px;
font-weight: bold;
}
#footReturn, #footReturn2 {
z-index: 89;
display: inline-block;
text-align: center;
text-decoration: none;
vertical-align: middle;
cursor: pointer;
width: 100%;
outline: 0 none;
overflow: visible;
Unknown property name.-moz-box-sizing: border-box;
box-sizing: border-box;
padding: 0;
height: 41px;
opacity: .95;
border-top: 1px solid #181818;
box-shadow: inset 0 1px 2px #b6b6b6;
background-color: #515151;
Invalid property value.background-image: -ms-linear-gradient(top,#838383,#202020);
background-image: -webkit-linear-gradient(top,#838383,#202020);
Invalid property value.background-image: -moz-linear-gradient(top,#838383,#202020);
Invalid property value.background-image: -o-linear-gradient(top,#838383,#202020);
background-image: -webkit-gradient(linear,0% 0,0% 100%,from(#838383),to(#202020));
Invalid property value.filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#838383',endColorstr='#202020');
Unknown property name.-ms-filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#838383',endColorstr='#202020');
}
.code{float:100%;float:left;margin:8px 10px 0 5px;padding:5px;width:80px;}
.is_check{float:left;margin:8px 0;padding:2px 10px;font-size:12px;border:1px solid #c1c1c1;background:#e6e6e6;border-radius:3px;}
.is_check:hover{background:#c1c1c1;}
#num{padding-right:5px;}
.window .title{background-image: linear-gradient(#179f00, #179f00);}
</style>
</head>
<body id="fans" >
<div class="qiandaobanner"> <img src="<?php echo ($homepic); ?>" > </div>
<div class="cardexplain">
<li class="nob">
<div class="beizhu">您可以修改你的会员卡支付密码。消费密码将在支付时使用，请妥善保管！
</div>
</li>
<ul class="round">
<?php if($cardInfo != false): ?><li>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
	<tr>
	<th><font color='red'>会员卡号</font></th>
	<td><input  type="text" class="px" readonly value="<?php echo ($cardnum); ?>"></td>
	</tr>
	</table>
	</li><?php endif; ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th>原始密码</th>
<td><input name="oldpassword" type="password"  class="px" id="oldpassword" value="<?php echo ($info["truename"]); ?>" placeholder="请输入您的原始支付密码"></td>
</tr>
</table>
</li>

<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th>新的密码</th>
<td><input name="newpassword"  class="px" id="newpassword"   type="password"  placeholder="请输入您的新的支付密码"></td>
</tr>
</table>
</li>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th>确认密码</th>
<td><input name="confirmpassword" class="px" id="confirmpassword" value="" type="password" placeholder="请再次输入新的支付密码"></td>
</tr>
</table>
</li>
</ul>
<div class="footReturn">
<a id="showcard"  class="submit" >提交信息</a>
<div class="window" id="windowcenter" >
<div id="title" class="wtitle"><span class="close" id="alertclose"></span></div>
<div class="content">
<div id="txt"></div>
</div>
</div>
</div>
<div style="height:60px;" id="msg">&nbsp;</div>

<script type="text/javascript">
$("#showcard").bind("click",
function() {
    var btn = $(this);
    var oldpassword  = $("#oldpassword").val();
    var newpassword = $("#newpassword").val();
    if ($("#newpassword").val() != newpassword)
    {
        alert("两次输入的密码不同，请重新输入");
        $("#confirmpassword").val('');
        $("#newpassword").val('');
        return;
    }
    var submitData = {
        oldpassword : oldpassword,
        newpassword 	  : newpassword,
        action: "index"
    };

    $.post('index.php?g=Wap&m=Card&a=modifypass&token=<?php echo ($_GET['token']); ?>&wecha_id=<?php echo ($_GET['wecha_id']); ?>&cardid=<?php echo ($_GET['cardid']); ?>', submitData,
    function(data) {
        if(data==1){			 
			alert('修改支付密码成功');
			//location.href = "<?php echo U('Card/card',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'cardid'=>$_GET['cardid']));?>";
		}else if(data==2){
			alert('原始支付密码不正确');
			//location.href = "<?php echo U('Card/card',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'cardid'=>$_GET['cardid']));?>";
		}else{
			 
			alert('修改支付密码失败，请联系管理员.');
		}
    },
    "json")
});

function check1(obj){	 
	if(obj.value == ''){
		alert("请输入你的支付密码.");
		document.getElementById(obj.id).style.background="red";
		return;
	}
}
function check2(obj) {
    if (obj.value == '') {
        alert("请输入你的新的支付密码.");
        document.getElementById(obj.id).style.background = "red";
        return;
    }
}
function check3(obj) {
    if (obj.value == '') {
        alert("请再次输入你的支付密码.");
        document.getElementById(obj.id).style.background = "red";
        return;
    }
}

</script>
</div>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Index",
            "moduleID":"0",
            "imgUrl": "", 
            "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Index/index',array('token'=>$token));?>",
            "tTitle": "",
            "tContent": ""
};
</script>
<?php echo ($shareScript); ?>
</body>
</html>