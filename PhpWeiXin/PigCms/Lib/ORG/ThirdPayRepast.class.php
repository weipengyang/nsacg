<?php
class ThirdPayRepast
{	
	
	public function index($orderid,$paytype='',$third_id=''){
		$wecha_id='';
		$token='';
		$cid=0;
        $dish_order_db = M('dish_order');
        if ($order = $dish_order_db->where(array('orderid' => $orderid))->find()) {
            //TODO 发货的短信提醒
			$token=$order['token'];
			$wecha_id=$order['wecha_id'];
            $cid = $order['cid'];
            $company = M('Company')->where(array('token' => $token, 'id' => $cid))->find();
            if (empty($company) || !is_array($company)) {
				header('Location:'.U('Repast/myOrders', array('token' => $token, 'wecha_id' => $wecha_id)));
            }
            $temp = !empty($order['info']) ? unserialize($order['info']) : array();
            $temp = isset($temp['list']) ? $temp['list'] : $temp;
            //$order['paid'] = 1;
            if ($order['paid']) {
                if (!empty($temp) && is_array($temp)) {
                    $log_db = M('Dishout_salelog');
                    $tmparr = array('token' => $token, 'cid' => $order['cid'], 'order_id' => $order['id'], 'paytype' => $order['paytype']);
                    foreach ($temp as $kk => $vv) {
                        $did = isset($vv['did']) ? $vv['did'] : $kk;
                        if ($did > 0) {
                            $flag = isset($vv['flag']) ? $vv['flag'] : '';
                            $newk = $flag . 'jc' . $did;
                            if (!($order['paycount'] > 0) || ($kk == $newk)) {
                                M('Dish')->where(array('id' => $did, 'cid' => $order['cid']))->setDec('instock', $vv['num']);
                                $logarr = array(
                                    'did' => $did, 'nums' => $vv['num'],
                                    'unitprice' => $vv['price'], 'money' => $vv['num'] * $vv['price'], 'dname' => $vv['name'],
                                    'addtime' => $order['time'], 'addtimestr' => date('Y-m-d H:i:s', $order['time']), 'comefrom' => 1
                                );
                                $savelogarr = array_merge($tmparr, $logarr);
                                $log_db->add($savelogarr);
                            }
                        }
                    }
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->setInc('paycount', 1);
                }
                if (($order['takeaway'] == 2) && ($order['isover'] == 2)) {
                    M('Dining_table')->where(array('id' => $order['tableid'], 'cid' => $order['cid']))->save(array('status' => 0));
                } elseif (($order['takeaway'] == 2) && ($order['isover'] == 1)) {
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->save(array('paid' => 0));
                }
                if ((empty($temp) || ((count($temp) == 1) && isset($temp['table']))) && ($temp['total'] == 0)) {
                    $temp = false;
                    $order['total'] = 1;
                    $bookTable = $order['price'];
                } elseif (($order['takeaway'] == 2) && ($order['advancepay'] > 0)) {
                    $bookTable = false;
                    if ($order['paycount'] == 1) {
                        $realpay = $myorder['price'] - $myorder['havepaid'];
                    } elseif ($order['paycount'] == 0) {
                        $advancepay = $order['advancepay'];
                    }
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->save(array('havepaid' => $order['advancepay'], 'advancepay' => 0));
                } else {
                    $bookTable = false;
                    if (isset($temp['table']) && !empty($temp['table'])) {
                        $bookTable = $temp['table']['price'];
                        unset($temp['table']);
                    }
                    $realpay = $myorder['price'] - $myorder['havepaid'];
                }
                $op = new orderPrint();
                $msg = array('companyname' => $company['name'], 'des' => $order['des'], 'companytel' => $company['tel'], 'truename' => $order['name'], 'tel' => $order['tel'], 'address' => $order['address'], 'buytime' => $order['time'], 'orderid' => $order['orderid'], 'bookTable' => $bookTable, 'price' => $order['price'], 'total' => $order['total'], 'list' => $temp, 'advancepay' => isset($advancepay) ? $advancepay : false, 'realpay' => isset($realpay) ? $realpay : false);
				$msg['typename'] = $order['takeaway'] == 2 ? '现在点餐' : '预约点餐';
				if($order['takeaway'] == 1){
				  $msg['sendtime']=$order['reservetime'];
				  $msg['typename']='外卖';
				}
                if ($order['tableid']) {
                    $t_table = M("Dining_table")->where(array('id' => $order['tableid']))->find();
                    $msg['tablename'] = isset($t_table['name']) ? $t_table['name'] : '';
                }

                $msg = ArrayToStr::array_to_str($msg, 1);
                $op->printit($token, $cid, 'Repast', $msg, 1);

                Sms::sendSms($token . "_" . $cid, "顾客{$order['name']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理");
                $model = new templateNews();
                $model->sendTempMsg('TM00820', array('href' => U('Repast/myOrders', array('token' => $token, 'wecha_id' => $wecha_id, 'cid' => $cid)), 'wecha_id' => $wecha_id, 'first' => '订餐交易提醒', 'keynote1' => '订单已支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '支付成功，感谢您的光临，欢迎下次再次光临！'));
            }
            header('Location:'.U('Repast/myOrders', array('token' => $token, 'wecha_id' => $wecha_id, 'cid' => $cid)));
        } else {
            exit('订单不存在');
        }
	}
}
?>

