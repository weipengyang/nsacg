<?php
class AllinpayAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $payConfig;
	public function __construct(){
		parent::_initialize();
		
		if($_GET['wid']){
			$database_userinfo = D('Userinfo');
			$condition_userinfo['id'] = $_GET['wid'];
			$now_user_info = $database_userinfo->field('`wecha_id`,`token`')->where($condition_userinfo)->find();

			$this->wecha_id = $now_user_info['wecha_id'];
			$this->token = $now_user_info['token'];
		}else{
			$this->wecha_id = $this->_get('wecha_id');
			$this->token = $this->_get('token');
		}
		
		//读取通联支付配置
		if(empty($_GET['platform'])){
			$payConfig = M('Alipay_config')->where(array('token'=>$this->token))->find();
			$payConfigInfo = unserialize($payConfig['info']);
			$this->payConfig = $payConfigInfo['allinpay'];
		}else{
			$payConfigInfo['merchantId'] = C('platform_allinpay_merchantId');
			$payConfigInfo['merchantKey'] = C('platform_allinpay_merchantKey');
			$this->payConfig = $payConfigInfo;
		}
	}
	public function pay(){
		//得到GET传参的订单名称，若为空则使用系统时间
		$orderName = $_GET['orderName'];
		if (!$orderName){
			$orderName = microtime();
		}
		
		//得到GET传参的系统唯一订单号
		$orderid = $_GET['orderid'];
		if (!$orderid){
			$orderid = $_GET['single_orderid']; //单个订单
		}
		
		//惯例，获取此订单号的信息
		$payHandel = new payHandle($this->token,$_GET['from'],'allinpay');
		$orderInfo = $payHandel->beforePay($orderid);
		
		//判断是否已经支付过
		if($orderInfo['paid']) exit('您已经支付过此次订单！');
		
		//判断价格是否为空。此做法可顺带查出是否是错误的订单号
		if(!$orderInfo['price'])exit('必须有价格才能支付');
		
		//为了应用 通联支付坑爹的要求（跳转地址长度为100个以内，通联数据库字段就是100的长度），，，将数据转换成ID。。。。
		//微信用户
		$database_userinfo = D('Userinfo');
		$condition_userinfo['wecha_id'] = $this->wecha_id;
		$now_user_info = $database_userinfo->field('`id` `wid`')->where($condition_userinfo)->find();
		if(empty($now_user_info)){
			$this->error('查询数据异常！请重试。');
		}
		
		if(empty($_GET['platform'])){
			$return_url = $this->siteUrl.'/index.php?g=Wap&m=Allinpay&a=r_u&wid='.$now_user_info['wid'].'&from='.$_GET['from'];
		}else{
			$return_url = $this->siteUrl.'/index.php?g=Wap&m=Allinpay&a=r_u&wid='.$now_user_info['wid'].'&from='.$_GET['from'].'&platform=1';
		}
		
		//在此引入通联处理类，防止引入又被价格错误返回导致终止
		import('@.ORG.Allinpay.allinpayCore');
		$allinpayClass = new allinpayCore();
		$allinpayClass->setParameter('payUrl','https://service.allinpay.com/mobilepayment/mobile/SaveMchtOrderServlet.action'); //提交地址
		$allinpayClass->setParameter('pickupUrl',$return_url); //跳转通知地址
		$allinpayClass->setParameter('receiveUrl',$this->siteUrl.'/index.php?g=Wap&m=Allinpay&a=notify_url'); //异步通知地址
		$allinpayClass->setParameter('merchantId',$this->payConfig['merchantId']); //商户号
		$allinpayClass->setParameter('orderNo',$orderInfo['orderid']); //订单号
		$allinpayClass->setParameter('orderAmount',floatval($orderInfo['price'])*100); //订单金额(单位分)
		$allinpayClass->setParameter('orderDatetime',date('YmdHis',$_SERVER['REQUEST_TIME'])); //订单提交时间
		$allinpayClass->setParameter('productName',$orderName); //商品名称
		$allinpayClass->setParameter('payType',0); //支付方式
		$allinpayClass->setParameter('key',$this->payConfig['merchantKey']); //支付方式
		
		//开始跳转支付
		$allinpayClass->sendRequestForm();
	}
	public function r_u(){
		import('@.ORG.Allinpay.allinpayCore');
		$allinpayClass = new allinpayCore();
		$verify_result = $allinpayClass->verify_pay($this->payConfig['merchantKey']);
		if(!$verify_result['error']){
			$payHandel = new payHandle($this->token,$_GET['from'],'allinpay');
			$orderInfo = $payHandel->afterPay($verify_result['order_id'],$verify_result['paymentOrderId']);
			$from = $payHandel->getFrom();
			$this->redirect('/index.php?g=Wap&m='.$from.'&a=payReturn&token='.$orderInfo['token'].'&wecha_id='.$orderInfo['wecha_id'].'&orderid='.$verify_result['order_id']);
		}else{
			$this->error($verify_result['msg']);
		}
	}
	public function notify_url(){
		echo 'SUCCESS';
	}
}
?>