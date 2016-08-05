<?php
class ThirdPayDishOut
{	
	
	public function index($orderid,$paytype='',$third_id=''){
			$wecha_id='';
			$token='';
        if ($order = M('dish_order')->where(array('orderid' => $orderid))->find()) {
            //TODO 发货的短信提醒
			$token=$order['token'];
			$wecha_id=$order['wecha_id'];
            if ($order['paid'] || $order['paytype'] == 'daofu' || $order['paytype'] == 'dianfu') {
                $temp = unserialize($order['info']);
                $tmparr = array('token' => $token, 'cid' => $order['cid'], 'order_id' => $order['id'], 'paytype' => $order['paytype']);
                $log_db = M('Dishout_salelog');
                if (!empty($temp) && is_array($temp)) {
                    foreach ($temp as $kk => $vv) {
                        $logarr = array(
                            'did' => isset($vv['did']) ? $vv['did'] : $kk, 'nums' => $vv['num'],
                            'unitprice' => $vv['price'], 'money' => $vv['num'] * $vv['price'], 'dname' => $vv['name'],
                            'addtime' => $order['time'], 'addtimestr' => date('Y-m-d H:i:s', $order['time']),'comefrom'=>0
                        );
                        $savelogarr = array_merge($tmparr, $logarr);
                        $log_db->add($savelogarr);
                    }
                }
			    $company = M('Company')->where(array('token' => $token, 'id' => $order['cid']))->find();
				if (empty($company) || !is_array($company)) {
					header('Location:'.U('DishOut/index', array('token' => $token, 'wecha_id' => $wecha_id)));
				 }

                 Sms::sendSms($token, "顾客{$order['name']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理",$company['mp']);
                 $model = new templateNews();
                 $model->sendTempMsg('TM00820', array('href' => U('DishOut/myOrder',array('token' => $token, 'wecha_id' => $wecha_id, 'cid' => $order['cid'])), 'wecha_id' => $wecha_id, 'first' => '订餐交易提醒', 'keynote1' => '订单已支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '下单成功，感谢您的光临，欢迎下次再次光临！'));
				
				$op = new orderPrint();
				$msg = array('companyname' => $company['name'], 'des' => htmlspecialchars_decode($order['des'], ENT_QUOTES),
                'companytel' => $company['tel'], 'truename' => htmlspecialchars_decode($order['name'], ENT_QUOTES),
                'tel' => $order['tel'], 'address' => htmlspecialchars_decode($order['address'], ENT_QUOTES),
                'buytime' => $order['time'], 'orderid' => $order['orderid'],
                'sendtime' => $order['reservetime'], 'price' => $order['price'],
                'total' => $order['total'], 'typename' => '外卖',
                'ptype'=>$thisOrder['paytype'],'list' => $temp);
				$msg = ArrayToStr::array_to_str($msg, $order['paid']);
				$op->printit($token, $order['cid'], 'DishOut', $msg, $order['paid']);
                 
            }
			header('Location:'.U('DishOut/myOrder', array('token' => $token, 'wecha_id' => $wecha_id, 'cid' => $order['cid'])));
        } else {
			exit('抱歉,订单信息出错');
        }
	}
}
?>

