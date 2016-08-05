<?php

class DishOutAction extends WapAction
{
	public $session_dish_info;
	public $_cid = 0;
	public $company;
	public $mshop;

	public function _initialize()
	{
		parent::_initialize();
//		$checkFunc = new checkFunc();

//		if (!function_exists('fdsrejsie3qklwewerzdagf4ds')) {
//			exit('error-4');
//		}

//		$checkFunc->cfdwdgfds3skgfds3szsd3idsj();
		$agent = $_SERVER['HTTP_USER_AGENT'];

		if (stripos($agent, 'MicroMessenger')) {
			$this->assign('iswxbrowse', true);
		}
		else {
			$this->assign('iswxbrowse', false);
		}

		$this->_cid = $_SESSION['session_shop_' . $this->token];
		$this->_cid = 0 < $this->_cid ? $this->_cid : 0;
		$this->assign('cid', $this->_cid);
		$this->company = $_SESSION['session_shop' . $this->_cid . '_' . $this->token];
		$this->company = !empty($this->company) ? unserialize($this->company) : false;
		$this->shopmanage = $_SESSION['manage_shop' . $this->_cid . '_' . $this->token];
		$this->shopmanage = !empty($this->shopmanage) ? unserialize($this->shopmanage) : false;
		$this->session_dish_info = 'session_dish_' . $this->_cid . '_info_' . $this->token;
	}

	public function index()
	{
		$company = M('Company')->where('token=\'' . $this->token . '\' AND display=1')->select();

		if (count($company) == 1) {
			$this->redirect(U('DishOut/dishMenu', array('token' => $this->token, 'cid' => $company[0]['id'], 'wecha_id' => $this->wecha_id)));
		}
		else {
			$nowlat = ($this->_get('nowlat') ? floatval($this->_get('nowlat', 'trim')) : 0);
			$nowlng = ($this->_get('nowlng') ? floatval($this->_get('nowlng', 'trim')) : 0);
			if ((0 < $nowlat) && (0 < $nowlng)) {
				$tmpe = array();

				foreach ($company as $kk => $vv) {
					$tmpd = $this->getDistance_map($nowlat, $nowlng, $vv['latitude'], $vv['longitude']);
					$tmpdstr = (1000 < $tmpd ? round(floatval($tmpd / 1000), 2) . ' km' : intval($tmpd) . ' m');
					$vv['distance'] = $tmpd;
					$vv['distancestr'] = $tmpdstr;
					$company[$kk] = $vv;
					$tmpe[$kk] = $tmpd;
				}

				asort($tmpe);
				$newCy = array();

				foreach ($tmpe as $tk => $tv) {
					$newCy[] = $company[$tk];
				}

				$company = (!empty($newCy) ? $newCy : $company);
				$this->assign('is_dwei', true);
			}

			$this->assign('company', $company);
			$this->assign('metaTitle', '餐厅分布');
			$this->display();
		}
	}

	private function getDistance_map($lat_a, $lng_a, $lat_b, $lng_b)
	{
		$R = 6377830;
		$pk = doubleval(180 / 3.1415926000000001);
		$a1 = doubleval($lat_a / $pk);
		$a2 = doubleval($lng_a / $pk);
		$b1 = doubleval($lat_b / $pk);
		$b2 = doubleval($lng_b / $pk);
		$t1 = doubleval(cos($a1) * cos($a2) * cos($b1) * cos($b2));
		$t2 = doubleval(cos($a1) * sin($a2) * cos($b1) * sin($b2));
		$t3 = doubleval(sin($a1) * sin($b1));
		$tt = doubleval(acos($t1 + $t2 + $t3));
		return round($R * $tt);
	}

	public function MyShop()
	{
		$cid = ($this->_get('cid') ? intval($this->_get('cid', 'trim')) : 0);
		if ((0 < $cid) && ($cid == $this->_cid)) {
			$outset = $this->outManage($cid);
			$company = $this->getCompany($cid);
			$imgarr = (!empty($outset['shopimg']) ? unserialize($outset['shopimg']) : array());
			$outinfo = array();
			$outinfo['id'] = $cid;
			$timestr = '';

			if (0 < $outset['zc_sdate']) {
				$timestr = date('H:i', $outset['zc_sdate']);
			}
			else {
				$timestr = '00:00';
			}

			if (0 < $outset['zc_edate']) {
				$timestr .= ' ~ ' . date('H:i', $outset['zc_edate']);
			}
			else {
				$timestr .= ' ~ 23:59';
			}

			$outinfo['timestr'] = $timestr;
			$outinfo['sendtime'] = $outset['sendtime'];
			$outinfo['removing'] = $outset['removing'];
			$outinfo['stype'] = $outset['stype'];
			$outinfo['pricing'] = $outset['pricing'];
			$outinfo['tel'] = $company['tel'];
			$outinfo['address'] = $company['address'];
			$outinfo['latitude'] = $company['latitude'];
			$outinfo['longitude'] = $company['longitude'];
			$outinfo['intro'] = $company['intro'];
			$outinfo['logourl'] = $company['logourl'];
			$outinfo['name'] = $company['name'];
			$outinfo['mp'] = $company['mp'];
			$outinfo['area'] = $outset['area'] ? htmlspecialchars_decode($outset['area'], ENT_QUOTES) : '';
			unset($outset);
			unset($company);
			$this->assign('shopinfo', $outinfo);
			$this->assign('shopimg', $imgarr);
			$this->assign('metaTitle', '店面详情');
			$this->display();
		}
		else {
			$this->exitdisplay('没有获取到相关门店信息');
		}
	}

	public function dishMenu()
	{
		$cid = ($this->_get('cid') ? intval($this->_get('cid', 'trim')) : 0);
		$outset = $this->outManage($cid);
		$nows = strtotime(date('Y-m-d H:i'));

		if (0 < $outset['zc_sdate']) {
			$sf = date('H:i', $outset['zc_sdate']);
			$setime = strtotime(date('Y-m-d ') . $sf);

			if ($nows < $setime) {
				$this->exitdisplay('抱歉！尚未到营业时间！');
			}
		}

		if (0 < $outset['zc_edate']) {
			$ef = date('H:i', $outset['zc_edate']);
			$setime = strtotime(date('Y-m-d ') . $ef);

			if ($setime < $nows) {
				$this->exitdisplay('抱歉！已经过了营业时间了！');
			}
		}

		$company = $this->getCompany($cid);
		if ($company && is_array($company)) {
			$_SESSION['session_shop_' . $this->token] = $cid;
		}

		$dish_sort = M('Dish_sort')->where(array('cid' => $cid))->order('`sort` ASC')->select();
		$dish = M('Dish')->where(array('cid' => $cid, 'isopen' => 1, 'istakeout' => 1))->order('`sort` ASC')->select();
		$starttime = strtotime(date('Y-m') . '-01 00:00:00');
		$t = date('t');
		$endtime = strtotime(date('Y-m') . '-' . $t . ' 23:59:59');
		$Model = new Model();
		$sqlstr = 'select cid,did,sum(nums) as tnums from ' . C('DB_PREFIX') . 'dishout_salelog where cid=' . $cid . ' AND token=\'' . $this->token . '\' AND addtime>=' . $starttime . ' AND addtime<=' . $endtime . ' group by did';
		$tmp = $Model->query($sqlstr);
		$newtmp = array();

		if (!empty($tmp)) {
			foreach ($tmp as $vv) {
				$newtmp[$vv['did']] = $vv['tnums'];
			}
		}

		$fenleiarr = array();

		if (is_array($dish_sort)) {
			foreach ($dish_sort as $sk => $sv) {
				$fenleiarr[$sv['id']] = $sv['name'];
			}
		}

		$isHave = $_SESSION[$this->session_dish_info];
		$isHave = ($isHave && !empty($isHave) ? unserialize($isHave) : array());
		$isHavePt = (!empty($isHave) ? $isHave['pt'] : array());
		$isHaveTj = (!empty($isHave) ? $isHave['tj'] : array());
		$disharr = $dztjtmp = array();

		if (is_array($dish)) {
			foreach ($dish as $dk => $dv) {
				$dv['sortname'] = $fenleiarr[$dv['sid']];
				$dv['sortname'] = $dv['sortname'] ? $dv['sortname'] : '无';

				if (array_key_exists($dv['id'], $isHavePt)) {
					$dv['ornum'] = $isHavePt[$dv['id']]['ornum'];
				}

				if (array_key_exists($dv['id'], $newtmp)) {
					$dv['m_sale'] = $newtmp[$dv['id']];
				}
				else {
					$dv['m_sale'] = 0;
				}

				if (array_key_exists($dv['sid'], $disharr)) {
					$disharr[$dv['sid']][] = $dv;
				}
				else {
					$disharr[$dv['sid']] = array();
					$disharr[$dv['sid']][] = $dv;
				}

				if ($dv['ishot'] == 1) {
					if (array_key_exists($dv['id'], $isHaveTj)) {
						$dv['ornum'] = $isHaveTj[$dv['id']]['ornum'];
					}
					else {
						$dv['ornum'] = 0;
					}

					$dztjtmp[] = $dv;
					$dztj = true;
				}
			}
		}

		$DishC = $this->getDishCompany($cid);
		$disharr['dztj'] = !empty($dztjtmp) ? $dztjtmp : array();
		$this->assign('kconoff', $DishC['kconoff']);
		$this->assign('dz_tj', $dztj);
		$this->assign('stype', $outset['stype']);
		$this->assign('pricing', $outset['pricing']);
		$this->assign('cid', $cid);
		$this->assign('fenleiarr', $fenleiarr);
		$this->assign('disharr', $disharr);
		$this->assign('company', $company);
		$this->assign('metaTitle', $company['name']);
		$this->display();
	}

	public function sureOrder()
	{
		$dishtmp = $_POST['dish'];
		$dzdish = $_POST['dzdish'];
		$tmpcid = intval($_POST['mycid']);
		$disharr = array();
		$outset = $this->outManage($tmpcid);
		$tmpdisharr = $dzdisharr = array();
		if ((0 < $tmpcid) && ($tmpcid == $this->_cid)) {
			foreach ($dishtmp as $kk => $vv) {
				$vv = ($vv ? intval($vv) : 0);

				if (0 < $vv) {
					$tmpdisharr[$kk] = array('id' => $kk, 'ornum' => $vv);
				}

				$dztjvv = (isset($dzdish[$kk]) && !empty($dzdish[$kk]) ? intval($dzdish[$kk]) : 0);

				if (0 < $dztjvv) {
					$dzdisharr[$kk] = array('id' => $kk, 'ornum' => $dztjvv);
				}

				$vv = $vv + $dztjvv;

				if (0 < $vv) {
					$disharr[$kk] = array('id' => $kk, 'ornum' => $vv);
				}
			}

			if (empty($disharr)) {
				$this->exitdisplay('您尚未点菜！');
			}

			$_SESSION[$this->session_dish_info] = serialize(array('pt' => $tmpdisharr, 'tj' => $dzdisharr));
			unset($tmpdisharr);
			unset($dzdisharr);
			$idarr = array_keys($disharr);
			sort($idarr);
			$idstr = implode(',', $idarr);
			$db_dish = M('Dish');
			$dish = $db_dish->where('id in(' . $idstr . ') and cid="' . $tmpcid . '" and isopen="1" and istakeout="1"')->order('`sort` ASC')->select();
			$totl = $ornum = 0;

			foreach ($dish as $val) {
				$index = $val['id'];
				$disharr[$index] = array_merge($disharr[$index], $val);
				$totl += $disharr[$index]['price'] * $disharr[$index]['ornum'];
				$ornum += $disharr[$index]['ornum'];
			}

			$permin = (0 < $outset['permin'] ? $outset['permin'] : 15);
			$sendtime = (0 < $outset['sendtime'] ? $outset['sendtime'] : $permin);
			$starttime = $current = strtotime(date('Y-m-d H:i:s'));
			if ((0 < $outset['zc_sdate']) && ($current < $outset['zc_sdate'])) {
				$starttime = $outset['zc_sdate'];
			}

			$endtime = strtotime(date('Y-m-d ') . '23:59:59');

			if (0 < $outset['zc_edate']) {
				$endtime = $outset['zc_edate'];
			}

			$starttime = $starttime + ($sendtime * 60);
			$t1 = strtotime(date('Y-m-d H', $starttime) . ':00:00');
			$t2 = $starttime - $t1;
			$t3 = $permin * 60;
			$t4 = $sendtime * 60;

			if ($t2 < $t3) {
				$starttime = $t1 + $t3;
			}
			else if ($t4 < $t2) {
				$starttime = $t1 + $t4 + $t3;
			}
			else {
				$starttime = $t1 + (2 * $t3);
			}

			$tmptime = $endtime - $starttime;

			if (0 < $tmptime) {
				$mins = floor($tmptime / 60);
				$timearr[] = date('H:i', $starttime);

				if ($permin < $mins) {
					$i = $permin;

					for (; $i <= $mins; $i = $i + $permin) {
						$timearr[] = date('H:i', ($i * 60) + $starttime);
					}
				}
			}
			else {
				$timearr[] = date('H:i', $endtime);
			}

			$Dish_order = M('Dish_order');
			$contact = false;

			if ($this->wecha_id) {
				$orderinfo = $Dish_order->where(array('token' => $this->token, 'cid' => $tmpcid, 'wecha_id' => $this->wecha_id))->order('paid DESC,id DESC ')->find();

				if (!empty($orderinfo)) {
					$contact['youname'] = $orderinfo['name'];
					$contact['yousex'] = $orderinfo['sex'];
					$contact['youtel'] = $orderinfo['tel'];
					$contact['youaddress'] = $orderinfo['address'];
				}
			}

			$DishC = $this->getDishCompany($tmpcid);
			$this->assign('kconoff', $DishC['kconoff']);
			$this->assign('timearr', $timearr);
			$this->assign('contact', $contact);
			$this->assign('stype', $outset['stype']);
			$this->assign('pricing', $outset['pricing']);
			$this->assign('ortotl', $totl);
			$this->assign('ornum', $ornum);
			$this->assign('cid', $tmpcid);
			$this->assign('ordish', $disharr);
			$this->assign('company', $this->company);
			$this->assign('metaTitle', $this->company['name']);
			$this->display();
		}
		else {
			$this->exitdisplay('提交信息出错');
		}
	}

	private function exitdisplay($tips = '', $returnurl = false)
	{
		$this->error($tips, $returnurl);
		exit();
	}

	private function getDishCompany($cid, $cache = true)
	{
		$DishC = $_SESSION['session_dish' . $cid . '_' . $this->token];
		$DishC = (!empty($DishC) ? unserialize($DishC) : false);
		if ($cache && !empty($DishC)) {
			return $DishC;
		}
		else {
			$DishC = M('Dish_company')->where(array('cid' => $cid))->find();

			if ($cache) {
				$_SESSION['session_dish' . $cid . '_' . $this->token] = !empty($DishC) ? serialize($DishC) : '';
			}

			return $DishC;
		}
	}

	private function getCompany($cid, $cache = true)
	{
		if ($cache && !empty($this->company)) {
			return $this->company;
		}
		else {
			$company = M('Company')->where(array('token' => $this->token, 'id' => $cid))->find();
			if (empty($company) || !is_array($company)) {
				$this->redirect(U('DishOut/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
			}

			if ($cache) {
				$this->company = $company;
				$_SESSION['session_shop' . $cid . '_' . $this->token] = !empty($company) ? serialize($company) : '';
			}

			return $company;
		}
	}

	private function outManage($cid)
	{
		if (!empty($this->shopmanage)) {
			return $this->shopmanage;
		}
		else {
			$outset = M('Dishout_manage')->where(array('token' => $this->token, 'cid' => $cid))->find();
			$tmp = M('Dish_company')->where(array('cid' => $cid))->find();
			if (is_array($tmp) && ($tmp['istakeaway'] == 1)) {
				$no_outset = array('token' => $this->token, 'cid' => $cid, 'stype' => 1, 'pricing' => 0, 'sendtime' => 30, 'removing' => 5, 'permin' => 15, 'zc_sdate' => 0, 'zc_edate' => 0, 'shopimg' => '', 'area' => '');
				$outset = (empty($outset) ? $no_outset : $outset);
			}

			if (empty($outset) || !is_array($outset)) {
				$this->exitdisplay('抱歉！此商家还没有设置外卖管理相关信息');
			}

			$this->shopmanage = $outset;
			$_SESSION['manage_shop' . $cid . '_' . $this->token] = !empty($outset) ? serialize($outset) : '';
			return $outset;
		}
	}

	public function companyMap()
	{
		if (C('baidu_map')) {
			$isamap = 0;
		}
		else {
			$isamap = 1;
		}

		$this->apikey = C('baidu_map_api');
		$this->assign('apikey', $this->apikey);
		$cid = ($this->_get('cid') ? intval($this->_get('cid', 'trim')) : 0);
		$company = $this->getCompany($cid, false);
		$this->assign('thisCompany', $company);

		if (!$isamap) {
			$this->display();
		}
		else {
			$this->amap = new amap();
			$link = $this->amap->getPointMapLink($company['longitude'], $company['latitude'], $company['name']);
			header('Location:' . $link);
		}
	}

	public function OrderPay()
	{
		$disharr = $_POST['dish'];
		$shopid = intval($_POST['mycid']);
		$totalmoney = floatval(trim($_POST['totalmoney']));
		$totalnum = intval(trim($_POST['totalnum']));
		$ouserName = htmlspecialchars(trim($_POST['ouserName']), ENT_QUOTES);
		$ouserSex = intval(trim($_POST['ouserSex']));
		$ouserTel = htmlspecialchars(trim($_POST['ouserTel']), ENT_QUOTES);
		$ouserAddres = htmlspecialchars(trim($_POST['ouserAddres']), ENT_QUOTES);
		$oarrivalTime = htmlspecialchars(trim($_POST['oarrivalTime']), ENT_QUOTES);
		$omark = htmlspecialchars(trim($_POST['omark']), ENT_QUOTES);

		if (0 < $shopid) {
			$jumpurl = U('DishOut/dishMenu', array('token' => $this->token, 'cid' => $shopid, 'wecha_id' => $this->wecha_id));
			if (empty($disharr) || !0 < $totalmoney || !0 < $totalnum) {
				$this->exitdisplay('订单信息出错！', $jumpurl);
			}

			if (empty($ouserName) || empty($ouserTel) || empty($ouserAddres)) {
				$this->exitdisplay('订单中相关联系地址：姓名或联系电话或送货地址有没写的', $jumpurl);
			}

			$oarrivalTime = ($oarrivalTime ? strtotime(date('Y-m-d ') . $oarrivalTime) : 0);
			$tmparr = array();
			$tmpsubnum = 0;
			$tmpsubmoney = 0;

			foreach ($disharr as $dk => $dv) {
				if (!empty($dv)) {
					$tmpnum = intval($dv['num']);

					if (0 < $tmpnum) {
						$tmpprice = floatval($dv['price']);
						$tmparr[$dk] = array();
						$tmparr[$dk]['did'] = $dk;
						$tmparr[$dk]['num'] = $tmpnum;
						$tmparr[$dk]['price'] = $tmpprice;
						$tmparr[$dk]['name'] = $dv['name'];
						$tmpsubnum += $tmpnum;
						$tmpsubmoney += $tmpprice * $tmpnum;
					}
				}
			}

			if (empty($tmparr)) {
				$this->exitdisplay('没有订单信息', $jumpurl);
			}

			if (($tmpsubnum != $totalnum) || (bccomp($tmpsubmoney, $totalmoney, 2) != 0)) {
				$this->exitdisplay('订单的金额或点的菜的份数不对', $jumpurl);
			}

			$outset = $this->outManage($shopid);
			if (($outset['stype'] == 2) && ($tmpsubmoney < $outset['pricing'])) {
				$tmpsubmoney = $outset['pricing'];
			}

			$wecha_id = ($this->wecha_id ? $this->wecha_id : 'DishOutm_' . $ouserTel);
			$orderid = substr($wecha_id, -5) . date('YmdHis');
			$Orderarr = array('cid' => $shopid, 'wecha_id' => $wecha_id, 'token' => $this->token, 'total' => $tmpsubnum, 'price' => $tmpsubmoney, 'nums' => 1, 'info' => serialize($tmparr), 'name' => $ouserName, 'sex' => $ouserSex, 'tel' => $ouserTel, 'address' => $ouserAddres, 'tableid' => 0, 'time' => time(), 'reservetime' => $oarrivalTime, 'stype' => $outset['stype'], 'paid' => 0, 'isuse' => 0, 'orderid' => $orderid, 'printed' => 0, 'des' => $omark, 'takeaway' => 1, 'comefrom' => 'dishout');
			$orid = D('Dish_order')->add($Orderarr);

			if ($orid) {
				$_SESSION[$this->session_dish_info] = '';
				$company = $this->getCompany($shopid);
				Sms::sendSms($this->token, '顾客' . $ouserName . '刚刚叫了一份外卖，订单号：' . $orderid . '，请您注意查看并处理', $company['mp']);
				$op = new orderPrint();
				$msg = array('companyname' => $company['name'], 'des' => trim($_POST['omark']), 'companytel' => $company['tel'], 'truename' => trim($_POST['ouserName']), 'tel' => trim($_POST['ouserTel']), 'address' => trim($_POST['ouserAddres']), 'buytime' => $Orderarr['time'], 'orderid' => $Orderarr['orderid'], 'sendtime' => 0 < $oarrivalTime ? date('Y-m-d H:i', $oarrivalTime) : '尽快送达', 'price' => $Orderarr['price'], 'total' => $Orderarr['total'], 'typename' => '外卖', 'list' => $tmparr);
				$msg = ArrayToStr::array_to_str($msg, 0);
				$op->printit($this->token, $shopid, 'DishOut', $msg, 0);
				$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();

				if ($alipayConfig['open']) {
					$this->success('正在提交中...', U('Alipay/pay', array('token' => $this->token, 'wecha_id' => $wecha_id, 'success' => 1, 'from' => 'DishOut', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $tmpsubmoney)));
				}
				else {
					$this->exitdisplay('商家尚未开启支付功能', $jumpurl);
				}
			}
			else {
				$this->exitdisplay('订单录入系统出错，抱歉给您的带来了不便。请重新下单吧', $jumpurl);
			}

			if (!empty($this->wecha_id)) {
				$userinfo_model = M('Userinfo');
				$thisUser = $userinfo_model->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();

				if (empty($thisUser)) {
					$userRow = array('tel' => $ouserTel, 'truename' => $ouserName, 'address' => $ouserAddres);
					$userRow['token'] = $this->token;
					$userRow['wecha_id'] = $this->wecha_id;
					$userRow['wechaname'] = '';
					$userRow['qq'] = 0;
					$userRow['sex'] = $ouserSex;
					$userRow['age'] = 0;
					$userRow['birthday'] = '';
					$userRow['info'] = '';
					$userRow['total_score'] = 0;
					$userRow['sign_score'] = 0;
					$userRow['expend_score'] = 0;
					$userRow['continuous'] = 0;
					$userRow['add_expend'] = 0;
					$userRow['add_expend_time'] = 0;
					$userRow['live_time'] = 0;
					$userinfo_model->add($userRow);
				}
			}
		}
		else {
			$jumpurl = U('DishOut/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id));
			$this->exitdisplay('订单信息中店面信息出错', $jumpurl);
		}
	}

	public function myOrder()
	{
		$where = array('wecha_id' => $this->wecha_id, 'token' => $this->token, 'cid' => $this->_cid, 'comefrom' => 'dishout');
		$dish_order = M('Dish_order')->where($where)->order('id DESC')->limit(7)->select();
		$list = array();
		$payarr = array('alipay' => '支付宝', 'weixin' => '微信支付', 'tenpay' => '财付通[wap手机]', 'tenpaycomputer' => '财付通[即时到帐]', 'yeepay' => '易宝支付', 'allinpay' => '通联支付', 'daofu' => '货到付款', 'dianfu' => '到店付款', 'chinabank' => '网银在线');

		foreach ($dish_order as $row) {
			$row['info'] = unserialize($row['info']);
			$paystr = strtolower($row['paytype']);
			$row['paytypestr'] = !empty($paystr) && array_key_exists($paystr, $payarr) ? $payarr[$paystr] : '其他';
			if (!$row['paid'] && ($row['paytype'] != 'daofu') && ($row['paytype'] != 'dianfu')) {
				$row['paystatus'] = '未付款';
			}
			else {
				$row['paystatus'] = '';
			}

			$list[] = $row;
		}

		$company = $this->getCompany($this->_cid);
		$this->assign('company', $company);
		$this->assign('cid', $row['cid']);
		$this->assign('orderList', $list);
		$this->assign('metaTitle', '我的订单');
		$this->display();
	}

	public function payReturn()
	{
		$orderid = trim($_GET['orderid']);

		if (isset($_GET['nohandle'])) {
		}
		else {
			ThirdPayDishOut::index($orderid);
		}
	}
}

?>
