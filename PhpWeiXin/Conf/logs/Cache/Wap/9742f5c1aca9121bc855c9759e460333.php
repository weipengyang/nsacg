<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo ($thisCompany["name"]); ?></title>
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link href="<?php echo RES;?>/card/style/style.css" rel="stylesheet" type="text/css">
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
</head>
<style>

.integral_table th{
	font-size:14px;
	font-weight:normal;
	background-color:#eee;
	border-top: 1px solid #e3e3e3;
	border-bottom: 1px solid #e3e3e3;
	text-align:left;
	padding:3px 5px 3px 5px;
}

.integral_table td{
	font-size:12px;
	color: #797979;
	border-bottom: 1px solid #e3e3e3;
	text-align:left;
	background-color:#fff;
	padding:10px 10px 8px 10px;
}
.integral_table tfoot td{
	border-bottom: 0;
}

.integral_table td .yqian{
	color: #02ae02;
}
.integral_table td .wqian{
	color: #797979;
}
.integral_table td.right{
	text-align:right;
}
.integral_table td .heji{
	color: #02ae02;
}
	.but {
		width:100%;
		background-color: #179F00;
		padding: 10px 20px;
		font-size: 16px;
		text-decoration: none;
		border: 1px solid #0B8E00;
		background-image: linear-gradient(bottom, #179F00 0%, #5DD300 100%);
		background-image: -o-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
		background-image: -moz-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
		background-image: -webkit-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
		background-image: -ms-linear-gradient(bottom, #179F00 0%, #5DD300 100%);
		background-image: -webkit-gradient(
		 linear,
		 left bottom,
		 left top,
		 color-stop(0, #179F00),
		 color-stop(1, #5DD300)
		 );
		-webkit-box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
		-moz-box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
		box-shadow: 0 1px 0 #94E700 inset, 0 1px 2px rgba(0, 0, 0, 0.5);
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		-o-border-radius: 5px;
		border-radius: 5px;
		color: #ffffff;
		display: block;
		text-align: center;
		text-shadow: 0 1px rgba(0, 0, 0, 0.2);

	}
	
	.box {
		text-align:center;
	}

.jifen-box {
	margin:10px 5px;
}
</style>
<body id="cardunion" class="mode_webapp2">

<div class="qiandaobanner"><a href="javascript:history.go(-1);"><img src="<?php echo ($thisCard["recharge"]); ?>" ></a> </div>

	<div class="jifen-box">


		<h3>会员卡充值</h3>
		<div class="box">
			<form action="<?php echo U('Card/payAction',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$thisCard['id']));?>" method="post">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="integral_table">
					<tr>
						<td><b>卡号</b></td>
						<td><?php echo ($card["number"]); ?></td>
					</tr>
					<tr>
						<td><b>金额</b></td>
						<td><input type="text" name="price" id="price" style="width:90%;height:35px;line-height:35px;border:1px solid #eee" /> 元</p></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="hidden" name="cardid" value="<?php echo ($info["cardid"]); ?>" />
							<input type="hidden" name="number" value="<?php echo ($card["number"]); ?>" />
							<input type="hidden" name="token" value="<?php echo ($info["token"]); ?>" />
							<input type="hidden" name="wecha_id" value="<?php echo ($info["wecha_id"]); ?>" />
							<input type="submit"  value="充值" class="but" />
						</td>
						
					</tr>
				

			</form>
		</div>
		<div class="clr"></div>
	</div>
    <script>
        //$(".but").click(function () {
        //    var price = $("#price").val();
        //    if (price && price > 0 && price % 500 == 0) {
        //        return true;
        //    }
        //    else {
        //        alert("输入的金额必须为500的整数倍");
        //        return false;
        //    }
        //});
    </script>
<div class="footermenu">
    <ul>
    <li>
            <a href="javascript:history.go(-1);">
            <img src="<?php echo RES;?>/card/images/return.png">
            <p>返回</p>
            </a>
        </li>
    <li>
            <a <?php if($infoType=='memberCardHome'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=index&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>">
            <img src="<?php echo RES;?>/card/images/home.png">
            <p>会员卡首页</p>
            </a>
        </li>
        <li>
            <a <?php if($infoType=='companyDetail'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=companyDetail&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&companyid=<?php echo ($thisCompany["id"]); ?>">
            <img src="<?php echo RES;?>/card/images/detaila.png">
            <p>商家详情</p>
            </a>
        </li>
        <li>
            <a <?php if($infoType=='myCard'){ ?>class="active"<?php } ?> href="/index.php?g=Wap&m=Card&a=card&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>&cardid=<?php echo ($memberCard["cardid"]); ?>">
            <span class="num2" ><?php echo ($cardsCount); ?></span><img src="<?php echo RES;?>/card/images/my.png">
            <p>我的会员卡</p>
            </a>
        </li>
    </ul>
</div>
<script type="text/javascript">
window.shareData = {  
            "moduleName":"Card",
            "moduleID":"0",
            "imgUrl": "", 
            "sendFriendLink": "<?php echo ($f_siteUrl); echo U('Card/index',array('token'=>$token));?>",
            "tTitle": "会员卡",
            "tContent": ""
};
</script>
<?php echo ($shareScript); ?>
</body>
</html>