<?php

class MicroBrokerAction extends UserAction
{
	public function _initialize()
	{
		parent::_initialize();
		$this->canUseFunction("MicroBroker");
		//$checkZend = new checkZend();
		//$checkFunc = new checkFunc();

		//if (!function_exists("fdsrejsie3qklwewerzdagf4ds")) {
		//	exit("error-4");
	//	}

	//	$checkFunc->cfdwdgfds3skgfds3szsd3idsj();
	}

	public function index()
	{
		$where = array("token" => session("token"), "isdel" => 0);
		$list = M("Broker")->where($where)->order("id DESC")->select();
		$count = M("Broker")->where($where)->count();
		$this->assign("count", $count);
		$this->assign("list", $list);
		$this->display();
	}

	public function mbkadd()
	{
		if (IS_POST) {
			$_POST["token"] = session("token");
			$_POST["title"] = $this->_post("title", "trim");

			if (empty($_POST['title'])) {
				$this->error("图文消息标题");
			}

			$_POST["keyword"] = $this->_post("keyword", "trim");
			$_POST["picurl"] = $this->_post("picurl");
			$_POST["imgreply"] = $this->_post("imgreply");
			$_POST["bgimg"] = $this->_post("bgimg");
			$_POST["invitecode"] = $this->_post("invitecode", "trim");
			$_POST["ruledesc"] = $this->_post("ruledesc");
			$_POST["registration"] = $this->_post("registration");
			$_POST["statdate"] = strtotime($this->_post("statdate"));
			$_POST["enddate"] = strtotime($this->_post("enddate"));
			$_POST["addtime"] = time();
			$_POST["uptime"] = time();

			if ($_POST["enddate"] < $_POST["statdate"]) {
				$this->error("结束时间不能小于开始时间!");
				exit();
			}

			$adds = (!empty($_POST['add']['id']) && is_array($_POST['add']['id']) ? $_POST['add'] : $_REQUEST['add']);
			if (empty($adds) || empty($adds['xmname'][0]) || empty($adds['xmimg'][0]) || empty($adds['xmnum'][0])) {
				$this->error("产品项目选项你还没有填写完整");
				exit();
			}

			foreach ($adds as $ke => $value ) {
				foreach ($value as $k => $v ) {
					$item_add[$k][$ke] = trim($v);
				}
			}

			unset($_POST["add"]);
			$data = M("Broker");
			$t_item = M("Broker_item");

			if ($data->create() != false) {
				if ($id = $data->add()) {
					foreach ($item_add as $k => $v ) {
						if (!empty($v['xmname']) && !empty($v['xmimg']))  {
							$data2["bid"] = $id;
							$data2["xmname"] = $v["xmname"];
							$data2["xmtype"] = (empty($v['xmtype']) ? 1 : $v["xmtype"]);
							$data2["xmnum"] = (empty($v['xmnum']) ? 0 : $v["xmnum"]);
							$data2["xmimg"] = $v["xmimg"];
							$data2["tourl"] = $v["tourl"];
							$t_item->add($data2);
						}
					}

					$this->handleKeyword($id, "MicroBroker", $this->_post("keyword", "trim"));
					$this->success("添加成功", U("MicroBroker/index", array("token" => session("token"))));
				}
				else {
					$this->error("服务器繁忙,请稍候再试");
				}
			}
			else {
				$this->error($data->getError());
			}
		}
		else {
			$mbkarr = array("imgreply" => $this->staticPath . "/tpl/static/microbroker/images/imgreply-default.jpg", "picurl" => $this->staticPath . "/tpl/static/microbroker/images/bg-loader-default.jpg", "bgimg" => $this->staticPath . "/tpl/static/microbroker/images/register_bg.png");
			$this->assign("mbk", $mbkarr);
			$invitecode = $this->get_rand(7);
			$this->assign("invitecode", $invitecode);
			$this->assign("mbkid", 0);
			$this->display();
		}
	}

	public function mbkedit()
	{
		if (IS_POST) {
			$token = session("token");
			$_POST["title"] = $this->_post("title", "trim");

			if (empty($_POST['title'])) {
				$this->error("图文消息标题");
			}

			$_POST["keyword"] = $this->_post("keyword", "trim");
			$_POST["picurl"] = $this->_post("picurl");
			$_POST["imgreply"] = $this->_post("imgreply");
			$_POST["bgimg"] = $this->_post("bgimg");
			$_POST["invitecode"] = $this->_post("invitecode", "trim");
			$_POST["ruledesc"] = $this->_post("ruledesc");
			$_POST["registration"] = $this->_post("registration");
			$_POST["statdate"] = strtotime($this->_post("statdate", "trim"));
			$_POST["enddate"] = strtotime($this->_post("enddate", "trim"));
			$_POST["uptime"] = time();
			$bid = intval($this->_post("id", "trim"));

			if ($_POST["enddate"] < $_POST["statdate"]) {
				$this->error("结束时间不能小于开始时间!");
				exit();
			}

			$data = D("Broker");
			$check = NULL;
			$where = array("id" => $bid, "token" => $token);

			if (0 < $bid) {
				$check = $data->where($where)->find();
			}
			else {
				exit($this->error("非法操作!"));
			}

			if ($check == NULL) {
				exit($this->error("非法操作"));
			}

			$adds = (!empty($_POST['add']['id']) && is_array($_POST['add']['id']) ? $_POST['add'] : $_REQUEST['add']);
	  	if (empty($adds) || empty($adds['xmname'][0]) || empty($adds['xmimg'][0]) || empty($adds['xmnum'][0])) {
				$this->error("投票选项你还没有填写完整");
				exit();
			}

			unset($_POST["id"]);
			unset($_POST["add"]);
			$item_add = array();

			foreach ($adds as $ke => $value ) {
				foreach ($value as $kk => $vv ) {
					$item_add[$kk][$ke] = trim($vv);
				}
			}

			$t_item = M("Broker_item");

			foreach ($item_add as $k => $v ) {
				if (!empty($v['xmname']) && !empty($v['xmimg'])) {
					$itemid = intval(trim($v["id"]));
					unset($v["id"]);

					if (0 < $itemid) {
						$data2["xmname"] = trim($v["xmname"]);
						$data2["xmtype"] = (empty($v['xmtype']) ? 1 : $v["xmtype"]);
						$data2["xmnum"] = (empty($v['xmnum']) ? 0 : $v["xmnum"]);
						$data2["xmimg"] = $v["xmimg"];
						$data2["tourl"] = $v["tourl"];
						$t_item->where(array("id" => $itemid, "bid" => $bid))->save($data2);
					}
					else {
						$addarr["bid"] = $bid;
						$addarr["xmname"] = trim($v["xmname"]);
						$addarr["xmtype"] = (empty($v['xmtype']) ? 1 : $v["xmtype"]);
						$addarr["xmnum"] = (empty($v['xmnum']) ? 0 : $v["xmnum"]);
						$addarr["xmimg"] = $v["xmimg"];
						$addarr["tourl"] = $v["tourl"];
						$t_item->add($addarr);
					}
				}
			}

			if ($data->create()) {
				if ($data->where($where)->save($_POST)) {
					$this->handleKeyword($bid, "MicroBroker", $this->_post("keyword", "trim"));
					$this->success("修改成功!", U("MicroBroker/index", array("token" => session("token"))));
					exit();
				}
				else {
					$this->success("修改成功", U("MicroBroker/index", array("token" => session("token"))));
					exit();
				}
			}
			else {
				$this->error($data->getError());
			}
		}
		else {
			$bid = (int) $this->_get("id", "trim");
			$where = array("id" => $bid, "token" => session("token"));
			$data = M("Broker");
			$check = $data->where($where)->find();
			if (($check == NULL) || !is_array($check)) {
				$this->error("非法操作");
			}

			if ($check["isdel"] == 1) {
				Header("Location:" . U("MicroBroker/index", array("token" => session("token"))));
			}

			$items = M("Broker_item")->where("bid=" . $bid)->order("id ASC")->select();
			$this->assign("items", $items);
			$this->assign("mbk", $check);
			$this->assign("invitecode", $check["invitecode"]);
			$this->assign("mbkid", $bid);
			$this->display("mbkadd");
		}
	}

	private function get_rand($length)
	{
		$chars = "01234567899876543210";
		$strs = "";

		for ($i = 0; $i < $length; $i++) {
			$strs .= $chars[mt_rand(0, strlen($chars) - 1)];
		}

		return $strs;
	}

	public function delitem()
	{
		$itemid = intval($this->_post("id", "trim"));
		$bid = intval($this->_post("bid", "trim"));
		$bkdb = M("Broker");
		$find = array("id" => $bid, "token" => session("token"));
		$result = $bkdb->where($find)->find();
		if ($result && is_array($result)) {
			M("Broker_item")->where(array("bid" => $bid, "id" => $itemid))->delete();
			$this->dexit(array("iserror" => 0));
		}
		else {
			$this->dexit(array("iserror" => 1));
		}
	}

	public function mbkdel()
	{
		$id = intval($this->_post("id", "trim"));
		$db_Broker = M("Broker");
		$tmp = $db_Broker->where(array("id" => $id, "token" => session("token")))->find();
		M("Broker")->where(array("id" => $id, "token" => session("token")))->save(array("isdel" => 1));
		$this->handleKeyword($id, "MicroBroker", $tmp["keyword"], 0, true);
		$this->dexit(array("error" => 0));
	}

	private function mbkcheck($id)
	{
		$check = M("Broker")->where(array("id" => $id, "token" => session("token")))->find();
		if(empty($check) || !is_array($check) || ($check["isdel"] == 1)) {
			Header("Location:" . U("MicroBroker/index", array("token" => session("token"))));
		}
	}

	public function mbkClients()
	{
		$bid = (int) $this->_get("id", "trim");
		$this->mbkcheck($bid);
		$db_client = M("broker_client");
		$db_user = M("Broker_user");
		$where = array("bid" => $bid, "token" => session("token"));
		$count = $db_client->where($where)->count();
		$Page = new Page($count, 20);
		$show = $Page->show();
		$joinitem = C("DB_PREFIX") . "broker_item";
		$joinuser = C("DB_PREFIX") . "broker_user";
		$db_client->join("as b_c LEFT JOIN " . $joinitem . " as b_i on b_c.proid=b_i.id");
		$db_client->join("LEFT JOIN " . $joinuser . " as b_u on b_c.tjuid=b_u.id");
		$db_client->where(array("b_c.bid" => $bid, "b_c.token" => session("token")))->order("b_c.id DESC")->limit($Page->firstRow . "," . $Page->listRows);
		$clientList = $db_client->field("b_c.*,b_i.xmname,b_i.tourl,b_u.username,b_u.tel")->select();

		if (is_array($clientList)) {
			foreach ($clientList as $k => $v ) {
				if (0 < $v["verifyuid"]) {
					$tmp = $db_user->where(array("id" => $v["verifyuid"], "bid" => $v["bid"]))->find();
					$clientList[$k]["verifyname"] = $tmp["username"];
					$clientList[$k]["verifytel"] = $tmp["tel"];
				}
				else {
					$clientList[$k]["verifyname"] = "";
					$clientList[$k]["verifytel"] = "";
				}
			}
		}

		$statusarr = array("新用户", "已跟进", "已到访", "已认筹", "已认购", "已签约", "已回款", "完成");
		$this->assign("statusarr", $statusarr);
		$this->assign("clientList", $clientList);
		$this->assign("pageshow", $show);
		$this->assign("bid", $bid);
		$this->display();
	}

	public function Consultant()
	{
		$bid = (int) $this->_get("bid", "trim");
		$clienid = (int) $this->_get("id", "trim");

		if (IS_POST) {
			$clienid = intval($this->_post("clienid", "trim"));
			$bid = intval($this->_post("bid", "trim"));
			$itemid = intval($this->_post("itemid", "trim"));
			$token = session("token");

			if (0 < $itemid) {
				M("Broker_client")->where(array("id" => $clienid, "bid" => $bid, "token" => $token))->save(array("verifyuid" => $itemid));
			}

			echo "<script type='text/javascript'>parent.location.reload();</script>";
			exit();
		}
		else {
			$db_user = M("Broker_user");
			$res = $db_user->where(array("bid" => $bid, "is_verify" => 1, "token" => session("token")))->field("id,tel,username")->select();
			$this->assign("consultant", $res);
			$this->assign("clienid", $clienid);
			$this->assign("bid", $bid);
			$this->display();
		}
	}

	public function addOneConsultant()
	{
		$bid = (int) $this->_get("id", "trim");

		if (IS_POST) {
			$id = intval($this->_post("bid", "trim"));

			if ($bid == $id) {
				$data = array("bid" => $id, "token" => session("token"));
				$telREG = "/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/";
				$tel = $this->_post("tel", "trim");

				if (!empty($tel)) {
					if (preg_match($telREG, $tel)) {
						$data["tel"] = $tel;
					}
					else {
						$this->error("手机号码格式不对");
						exit();
					}
				}
				else {
					$this->error("手机号码不能为空");
					exit();
				}

				$username = $this->_post("username", "trim");

				if (empty($username)) {
					$this->error("产品顾问名称不能为空");
					exit();
				}

				$data["username"] = htmlspecialchars($username, ENT_QUOTES);
				$password = $this->_post("password", "trim");
				$tmp = strlen($password);

			if (empty($password)) {
					$this->error("密码不能为空");
					exit();
				}
				else {
					if (!is_numeric($password) || ($tmp < 6) || (8 < $tmp)) {
						$this->error("密码必须为你6到8位的数字");
						exit();
					}
				}

				$db_brokeruser = M("Broker_user");
				$tmpTel = $db_brokeruser->where(array("bid" => $id, "tel" => $data["tel"]))->find();

				if (!empty($tmpTel)) {
					$this->error("此手机号码已经被注册过了！");
					exit();
				}

				$data["pwd"] = md5($password);
				$company = $this->_post("company", "trim");
				$data["company"] = htmlspecialchars($company, ENT_QUOTES);
				$data["identity"] = 7;
				$data["is_verify"] = 1;
				$data["wecha_id"] = "MBK_" . $id . "_temp" . $data["tel"];
				$data["addtime"] = time();
				$userid = $db_brokeruser->add($data);

				if (0 < $userid) {
					$this->success("添加成功!", U("MicroBroker/mbkBrokers", array("token" => session("token"), "id" => $id)));
				}
				else {
					$this->error("添加失败");
					exit();
				}
			}
			else {
				$this->error("添加失败");
				exit();
			}
		}
		else {
			$this->assign("id", $bid);
			$this->display();
		}
	}

	public function mbkBrokers()
	{
		$bid = (int) $this->_get("id", "trim");
		$this->mbkcheck($bid);
		$jointable = C("DB_PREFIX") . "broker_translation";
		$db_user = M("Broker_user");
		$where = array("bid" => $bid, "token" => session("token"));
		$count = $db_user->where($where)->count();
		$Page = new Page($count, 20);
		$show = $Page->show();
		$db_user->join("as b_u LEFT JOIN " . $jointable . " as b_t on b_u.identity=b_t.id");
		$db_user->where(array("b_u.bid" => $bid, "b_u.token" => session("token")))->order("b_u.id DESC")->limit($Page->firstRow . "," . $Page->listRows);
		$userList = $db_user->field("b_u.*,b_t.description")->select();
		$this->assign("id", $bid);
		$this->assign("userList", $userList);
		$this->assign("pageshow", $show);
		$this->display();
	}

	public function mbkBlacklist()
	{
		$id = intval($this->_post("id", "trim"));
		$bid = intval($this->_post("bid", "trim"));
		$st = intval($this->_post("st", "trim"));
		$token = $this->_post("token", "trim");
		M("Broker_user")->where(array("id" => $id, "bid" => $bid, "token" => session("token")))->save(array("status" => $st));
		$this->dexit(array("error" => 0));
	}

	public function mbkDetail()
	{
		$bid = (int) $this->_get("bid", "trim");
		$id = (int) $this->_get("id", "trim");
		$jointable = C("DB_PREFIX") . "broker_translation";
		$db_user = M("Broker_user");
		$db_user->join("as b_u LEFT JOIN " . $jointable . " as b_t on b_u.identity=b_t.id");
		$thisuser = $db_user->where(array("b_u.id" => $id, "b_u.bid" => $bid, "b_u.token" => session("token")))->field("b_u.*,b_t.description")->find();
		$this->assign("thisuser", $thisuser);
		$this->display();
	}

	public function mbkMoneyOperatLog()
	{
		$bid = (int) $this->_get("bid", "trim");
		$id = (int) $this->_get("id", "trim");
		$userarr = M("Broker_user")->where(array("id" => $id, "bid" => $bid, "token" => session("token")))->find();
		$db_optlog = M("Broker_optionlog");
		$where = array("tjuid" => $id, "bid" => $bid, "token" => session("token"));
		$count = $db_optlog->where($where)->count();
		$Page = new Page($count, 20);
		$show = $Page->show();
		$logarr = $db_optlog->where($where)->order("id DESC")->limit($Page->firstRow . "," . $Page->listRows)->select();
		$this->assign("thisuser", $userarr);
		$this->assign("logarr", $logarr);
		$this->assign("pagshow", $show);
		$this->display();
	}

	public function mbkOperatMoney()
	{
		$bid = (int) $this->_get("bid", "trim");
		$id = (int) $this->_get("id", "trim");
		$cmmid = ($this->_get("cmmid") ? intval($this->_get("cmmid", "trim")) : 0);
		$db_user = M("Broker_user");

		if (IS_POST) {
			$uid = intval($this->_post("u_id", "trim"));
			$bid = intval($this->_post("bid", "trim"));
			$cmmid = intval($this->_post("cmmid", "trim"));
			$opttype = intval($this->_post("opttype", "trim"));
			$tomoney = $this->_post("tomoney", "trim");
			$totalcash = $this->_post("totalcash", "trim");
			$token = session("token");

			if (is_numeric($tomoney)) {
				if ($opttype == 1) {
					M("Broker_user")->where(array("id" => $uid, "bid" => $bid, "token" => $token))->setInc("totalcash", $tomoney);

					if (0 < $cmmid) {
						$upwhere = array("id" => $cmmid, "bid" => $bid, "tjuid" => $uid);
						M("Broker_commission")->where($upwhere)->save(array("status" => 1));
						M("Broker_commission")->where($upwhere)->setInc("money", $tomoney);
					}

					M("Broker_optionlog")->add(array("token" => $token, "bid" => $bid, "tjuid" => $uid, "logstr" => "佣金操作充值", "addtime" => time(), "money" => $tomoney));
					echo "<script type='text/javascript'>alert('充值成功！');parent.location.reload();</script>";
					exit();
				}
				else if ($opttype == 2) {
					if ($tomoney <= $totalcash) {
						M("Broker_user")->where(array("id" => $uid, "bid" => $bid, "token" => $token))->setDec("totalcash", $tomoney);
						M("Broker_user")->where(array("id" => $uid, "bid" => $bid, "token" => $token))->setInc("extractcash", $tomoney);
						M("Broker_optionlog")->add(array("token" => $token, "bid" => $bid, "tjuid" => $uid, "logstr" => "佣金操作提取", "addtime" => time(), "money" => $tomoney));
						echo "<script type='text/javascript'>alert('提取成功！');parent.location.reload();</script>";
						exit();
					}
					else {
						echo "<script type='text/javascript'>alert('当前可提取的佣金余额不足！');parent.location.reload();</script>";
						exit();
					}
				}
			}
			else {
				echo "<script type='text/javascript'>alert('充值/提取佣金额度必须是数字');parent.location.reload();</script>";
				exit();
			}
		}
		else {
			$thisuser = $db_user->where(array("id" => $id, "bid" => $bid, "token" => session("token")))->find();
			$this->assign("thisuser", $thisuser);
			$this->assign("u_id", $id);
			$this->assign("bid", $bid);
			$this->assign("cmmid", $cmmid);
			$this->display();
		}
	}

	public function mbkConfirm()
	{
		$bid = (int) $this->_get("id", "trim");
		$this->mbkcheck($bid);
		$token = $this->_get("token", "trim");
		$tmps = array();

		if ($token == session("token")) {
			$db_commission = M("Broker_commission");
			$where = array("bid" => $bid, "client_status" => 6);
			$count = $db_commission->where($where)->count();
			$Page = new Page($count, 20);
			$show = $Page->show();
			$jointable = C("DB_PREFIX") . "broker_item";
			$db_commission->join("as b_c LEFT JOIN " . $jointable . " as b_i on b_c.proid=b_i.id");
			$tmps = $db_commission->where("b_c.bid=" . $bid . " AND b_c.client_status=6")->order("b_c.id DESC")->limit($Page->firstRow . "," . $Page->listRows)->field("b_c.*,b_i.xmname,b_i.xmtype,b_i.xmnum")->select();
		}

		$this->assign("commdatas", $tmps);
		$this->assign("pageshow", $show);
		$this->display();
	}

	public function mbkSureMoney()
	{
		$id = intval($this->_post("id", "trim"));
		$tjuid = intval($this->_post("tjuid", "trim"));
		$bid = intval($this->_post("bid", "trim"));
		$money = $this->_post("money", "trim");
		$token = $this->_post("token", "trim");
		if (($token == session("token")) && (0 <= $money)) {
			M("Broker_commission")->where(array("id" => $id, "bid" => $bid, "tjuid" => $tjuid))->save(array("status" => 1));
			M("Broker_user")->where(array("id" => $tjuid, "bid" => $bid, "token" => $token))->setInc("totalcash", $money);
			M("Broker_optionlog")->add(array("token" => $token, "bid" => $bid, "tjuid" => $tjuid, "logstr" => "佣金审核充值", "addtime" => time(), "money" => $money));
			$this->dexit(array("error" => 0));
		}

		$this->dexit(array("error" => 1));
	}

	private function dexit($data)
	{
		if (is_array($data)) {
			echo json_encode($data);
		}
		else {
			echo $data;
		}

		exit();
	}
}


?>
