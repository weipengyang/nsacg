<?php if (!defined('THINK_PATH')) exit();?>﻿<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>个人资料</title>
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
<div class="beizhu"><?php if ($cardid){ if($cardInfo != false): ?>您可以修改你的会员卡信息。以下信息将作为消费凭证，请认真填写！ <?php else: ?>填写以下信息即可领取vip会员卡,红色字必填<?php endif; }else {echo '请先完善您的个人信息，红色为必填项';}?></div>
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

<?php if($conf['wechaname'] == 0): else: ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr style="display:none">
<th><font color='red'>微信昵称</font></th>
<td><input name="wechaname" onfocus="check2(this)"
onblur="check1(this)"  type="text" class="px" id="wechaname" value="<?php echo ($fans["wechaname"]); ?>" placeholder="请输入您的微信名称"></td>
</tr>
</table>
</li><?php endif; ?>

<?php if($conf['tel'] == 0): else: ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th><font color='red'>手机号码</font></th>
<td><input onfocus="check2(this)"
onblur="check3(this)" name="tel"  class="px" id="tel" value="<?php echo ($info["tel"]); ?>"  type="text" placeholder="手机号码"></td>
</tr>
</table>
</li><?php endif; ?>
<?php if($cardInfo == '' and $is_check == '1'): ?><li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th><font color='red'>短信验证</font></th>
<td><input name="code"  class="code" id="code" value=""  type="text" placeholder="效验码"><a class="is_check" href="javascript:void(0);"><em id="num"></em><b>点击获取效验码</b></a></td>
</tr>
</table>
</li><?php endif; ?>

<?php if($cardid != ''): if( ($conf['paypass'] == 0 ) or ($info["paypass"] != '') ): else: ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th><font color='red'>支付密码</font></th>
<td><input name="paypass" onblur="check5(this)" class="px" id="paypass" value="" type="password" placeholder="请输入您的支付密码"></td>
</tr>
</table>
</li><?php endif; endif; ?>
<?php if($conf['carno'] == 0): else: ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th style='width:80px'><font color='red'>车牌号码1</font></th>
<td><input onblur="check4(this)" name="carno"  type="text" class="px" id="carno" value="<?php echo ($info["carno"]); ?>" placeholder="请输入您的车牌号码"></td>
</tr>
<tr>
    <th>车牌号码2</th>
    <td><input onblur="checkCarNo(this)" name="carno1" type="text" class="px" id="carno1" value="<?php echo ($info["carno1"]); ?>" placeholder="请输入您的车牌号码"></td>
</tr>
<tr>
    <th>车牌号码3 </th>
    <td><input onblur="checkCarNo(this)" name="carno2" type="text" class="px" id="carno2" value="<?php echo ($info["carno2"]); ?>" placeholder="请输入您的车牌号码"></td>
</tr>
</table>
</li><?php endif; ?>

<?php if($conf['truename'] == 0): else: ?>
<li>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang">
<tr>
<th>真实姓名</th>
<td><input name="truename"  type="text" class="px" id="truename" value="<?php echo ($info["truename"]); ?>" placeholder="请输入您的真实姓名"></td>
</tr>
</table>
</li><?php endif; ?>

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
$('.is_check').click(function(){
	if($('#num').html() != ''){
		return false;
	}
	var phone 	= $('#tel').val();
	reg=/^0{0,1}(13[0-9]|145|15[0-9]|18[0-9])[0-9]{8}$/i;
	 if(!reg.test(phone)){   
		alert("错误,请输入11位的手机号！");
		$('#tel').css('background','red');
		return false;
	 }else{
		var num = $('#num').html();
		if(num == ''){
			$.post('index.php?g=Wap&m=Userinfo&a=get_code&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($cardid); ?>', {phone:phone},
			function(data) {
				if(data.error == 0){
					$('#num').html('60');
					$('.is_check>b').html('后重新获取');
					count_down();
				}else{
					alert(data.info);
				}
			},"json");
		}
	 }
});
function count_down(){
	var down = setInterval(function(){
		var num 	= $('#num').html();
		if(num > 0){
			$('#num').html(num-1);
		}else{
			$('#num').html('');
			$('.is_check>b').html('点击获取效验码');
			clearInterval(down);
		}
	},1000);
}
$("#showcard").bind("click",
function() {
  
  var btn = $(this);  
  var wechaname = $("#wechaname").val();
	var tel 	  = $("#tel").val();
	var carno		= $("#carno").val();
	var carno1		= $("#carno1").val();
	var carno2		= $("#carno2").val();
	var truename  = $("#truename").val();
	var paypass   = $("#paypass").val();
    if(carno=='')
    {
    	alert("请输入车牌号码");
	    return;
    }
  <?php if($cardid != '' && $conf['tel'] == 0 && $cardInfo == false): else: ?>	
    if (tel == '') {
        alert("请认真输入手机号");
        return
    }<?php endif; ?>
	<?php if($cardid != '' && $conf['paypass'] == 0 ): else: ?>
	if(paypass=='')
	{
	    alert(" 请设置支付密码");
	    return;
	}<?php endif; ?>

    var submitData = {
        wechaname : wechaname,
        tel 	  : tel,
        carno		:carno,
        truename  : truename,
        cardid 	  : <?php echo ($cardid); ?>,
        paypass   : paypass,
        action: "index"
    };

	 if(carno1)
	 {
	 	
	 	 submitData['carno1']=carno1;
	 }
	  if(carno2)
	 { 
	 	
	 	 submitData['carno2']=carno2;
	 }
    $.post('index.php?g=Wap&m=Userinfo&a=index&token=<?php echo ($_GET['token']); ?>&wecha_id=<?php echo ($_GET['wecha_id']); ?>&cardid=<?php echo ($_GET['cardid']); ?>', submitData,
    function(data) {
        if(data==1){			 
			alert('个人信息保存成功');
			<?php if ($redirectUrl){?>location.href = "<?php echo ($redirectUrl); ?>";<?php }?>
		}else if(data==2){
			alert('成功领取了会员卡');
			location.href = "<?php echo U('Card/card',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'cardid'=>$_GET['cardid']));?>";
		}else if(data==3){
			 
			alert('该商家会员卡缺货了');
		}else if(data==4){		 
			alert('您的积分不够领取该会员卡');
		}else if(data==5){
			alert('短信验证码无效');
		}else if(data==6){
			 
		    alert('该车牌号码已经绑定两个微信，每个车牌最多绑定两个微信.');
		}
		else{
			 
			alert(data);
		}
    },
    "json")
});

function check1(obj){	 
	if(obj.value == ''){
		alert("请输入您的微信名称.");
		document.getElementById(obj.id).style.background="red";
		return;
	}
}
function check5(obj){	 
    if(obj.value == ''){
        alert("请输入您的支付密码.");
        document.getElementById(obj.id).style.background="red";
        return;
    }
}
function check2(obj){   
  	document.getElementById(obj.id).style.background="white";  
}
function check3(obj){
	if(obj.value == ''){
		alert('手机号码必须填写');
		document.getElementById(obj.id).style.background="red";
		return;
	}
	reg=/^0{0,1}(13[0-9]|145|15[0-9]|18[0-9])[0-9]{8}$/i;
	  if(!reg.test(obj.value)){   
			alert("错误,请输入11位的手机号！");
			document.getElementById(obj.id).style.background="red";
			return;
	 }
}
function checkCarNo(obj)
{
    if(obj.value!='')
    {
        reg=/^[\u4e00-\u9fa5][A-Z][0-9a-zA-Z]{5}$/i
        if(!reg.test(obj.value))
        { 
            alert("请输入完整的车牌号码，格式如粤T83G5NB");
            document.getElementById(obj.id).style.background="red";
            return;
        }
    }
}
function check4(obj){	 
	if(obj.value == ''){
		alert("请输入您的车牌号码");
		document.getElementById(obj.id).style.background="red";
		return;
	}
	reg=/^[\u4e00-\u9fa5][A-Z][0-9a-zA-Z]{5}$/i
	if(!reg.test(obj.value))
	{ 
	    alert("请输入完整的车牌号码，格式如粤T83G5NB");
	    document.getElementById(obj.id).style.background="red";
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