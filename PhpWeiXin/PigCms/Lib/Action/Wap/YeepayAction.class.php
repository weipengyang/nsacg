<?php

class YeepayAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $payConfig;
	public function __construct(){
		
		parent::_initialize();
		
		$this->token = $this->_get('token');
		$this->wecha_id	= $this->_get('wecha_id');
		if (!$this->token){
			$this->token = $_SESSION['yeepay']['token'];
		}
		//读取易宝配置
		if(empty($_GET['platform'])){
			$payConfig = M('Alipay_config')->where(array('token'=>$this->token))->find();
			$payConfigInfo = unserialize($payConfig['info']);
			$this->payConfig = $payConfigInfo['yeepay'];
		}else{
			$payConfigInfo['merchantaccount'] = C('platform_yeepay_merchantaccount');
			$payConfigInfo['merchantPrivateKey'] = C('platform_yeepay_merchantPrivateKey');
			$payConfigInfo['merchantPublicKey'] = C('platform_yeepay_merchantPublicKey');
			$payConfigInfo['yeepayPublicKey'] = C('platform_yeepay_yeepayPublicKey');
			$payConfigInfo['product_catalog'] = C('platform_yeepay_product_catalog');
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
		$payHandel = new payHandle($this->token,$_GET['from'],'yeepay');
		$orderInfo = $payHandel->beforePay($orderid);
		
		//判断是否已经支付过
		if($orderInfo['paid']) exit('您已经支付过此次订单！');
		
		//判断价格是否为空。此做法可顺带查出是否是不存的订单号
		if(!$orderInfo['price']) exit('必须有价格才能支付！');
		
		//因为易宝那边数据库限制，fcallbackurl只能限制死为100个长度以内。所以携带不了token,wecha_id,from这些参数，携带会超过100个字符。然而使用一张表存储
		$database_yeepay_tmp = M('Yeepay_tmp');
		$data_yeepay_tmp['order_id'] = $orderid;
		$data_yeepay_tmp['token'] = $this->token;
		$data_yeepay_tmp['wecha_id'] = $this->wecha_id;
		$data_yeepay_tmp['from'] = $_GET['from'];
		$data_yeepay_tmp['time'] = $_SERVER['REQUEST_TIME'];
		if(!empty($_GET['platform'])){
			$data_yeepay_tmp['platform'] = 1;
		}
		if(!$tmp_id = $database_yeepay_tmp->data($data_yeepay_tmp)->add()){
			$this->error('下订单出现错误！请重试。');
		}
		
		//在此次引入易宝处理类，防止引入又被价格错误返回导致终止
		import('@.ORG.Yeepay.yeepayMPay');
		$yeepay = new yeepayMPay($this->payConfig['merchantaccount'],$this->payConfig['merchantPublicKey'],$this->payConfig['merchantPrivateKey'],$this->payConfig['yeepayPublicKey']);

		$order_id 	     = 'ORDER_'.$tmp_id;                      //客户订单号
		$transtime       = time();                        //交易时间
		$product_catalog = $this->payConfig['product_catalog'];   						  //商品类别码
		$identity_id     = $this->wecha_id;               //支付身份标识
		$identity_type   = 0;   					      //支付身份标识类型码
		$user_ip         = $_SERVER['REMOTE_ADDR'];       //用户IP
		$user_ua         = $_SERVER['HTTP_USER_AGENT'];   //用户ua
		$callbackurl 	 = $this->siteUrl.'/index.php?g=Wap&m=Yeepay&a=notify_url';	//后台系统回调地址
		$fcallbackurl 	 = $this->siteUrl.'/wxpay/yeepay.php';	//前台系统回调地址
		$product_name    = $orderName;        //商品名称
		$product_desc	 = $orderName;        //商品描述
		$other = '';                           //其他支付身份信息
		$amount =floatval($orderInfo['price']*100);		                  //交易金额

		$url = $yeepay->webPay($order_id,$transtime,$amount,$product_catalog,$identity_id,$identity_type,$user_ip,$user_ua,$callbackurl,$fcallbackurl,$currency=156,$product_name,$product_desc,$other);
		
		//将当前的token存放于SESSION中。
		$_SESSION['yeepay']['token'] = $this->token;
		
		
		header('Location: '.$url);
		exit;
	
	}
	public function return_url(){
		import('@.ORG.Yeepay.yeepayMPay');
		$yeepay = new yeepayMPay($this->payConfig['merchantaccount'],$this->payConfig['merchantPublicKey'],$this->payConfig['merchantPrivateKey'],$this->payConfig['yeepayPublicKey']);
		try{
			$data = str_replace(' ','+',$_GET['data']);
			$encryptkey = str_replace(' ','+',$_GET['encryptkey']);
			$return = $yeepay->callback($data,$encryptkey);
			$database_yeepay_tmp = M('Yeepay_tmp');
			$condition_yeepay_tmp['id'] = str_replace('ORDER_','',$return['orderid']);
			$yeepay_tmp = $database_yeepay_tmp->field(true)->where($condition_yeepay_tmp)->find();
			$_GET['platform'] = $yeepay_tmp['platform'];
			$payHandel = new payHandle($yeepay_tmp['token'],$yeepay_tmp['from'],'yeepay');
			$orderInfo = $payHandel->afterPay($yeepay_tmp['order_id'],$return['yborderid']);

			$from = $payHandel->getFrom();
			unset($_SESSION['yeepay']);
			$this->redirect('/index.php?g=Wap&m='.$from.'&a=payReturn&token='.$orderInfo['token'].'&wecha_id='.$orderInfo['wecha_id'].'&orderid='.$yeepay_tmp['order_id']);
		}catch (yeepayMPayException $e) {
			$this->error('支付时发生错误！错误提示：'.$e->GetMessage().'；错误代码：'.$e->Getcode());
		}
	}
}
?>