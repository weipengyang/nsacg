<?php
class ThirdPayGroupon
{	
	
	public function index($orderid,$paytype,$third_id){
		$product_cart_model=M('product_cart');
		$out_trade_no=$orderid;
		$order=$product_cart_model->where(array('orderid'=>$out_trade_no))->find();
		if (!$this->wecha_id){
			$this->wecha_id=$order['wecha_id'];
		}
		$sepOrder=0;
		if (!$order){
			$order=$product_cart_model->where(array('id'=>$out_trade_no))->find();
			$sepOrder=1;
		}
		if($order){
			if($order['paid']!=1){exit('该订单还未支付');}
			/************************************************/
			Sms::sendSms($this->token,'您的微信里有团购订单已经付款');
			/************************************************/
			header('Location:/index.php?g=Wap&m=Groupon&a=myOrders&token='.$order['token'].'&wecha_id='.$order['wecha_id']);
			
		}else{
			exit('订单不存在：'.$out_trade_no);
		}
	}
}
?>

