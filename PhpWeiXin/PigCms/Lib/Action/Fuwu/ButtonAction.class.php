<?php

class ButtonAction extends UserAction
{
	protected $appid;

	public function _initialize()
	{
		parent::_initialize();
		$this->appid = M("Wxuser")->where(array("token" => $this->token))->getField("fuwuappid");
		$checkFunc = new checkFunc();

		if (!function_exists("fdsrejsie3qklwewerzdagf4ds")) {
			exit("error-4");
		}

		$checkFunc->cfdwdgfds3skgfds3szsd3idsj();
	}

	public function dataReturn()
	{
		$r = $this->apiData("alipay.mobile.public.menu.add", $this->ButtonCreate());

		if ($r["alipay_mobile_public_menu_add_response"]["code"] == 11013) {
			$r = $this->apiData("alipay.mobile.public.menu.update", $this->ButtonCreate());

			if ($r["alipay_mobile_public_menu_update_response"]["code"] == 200) {
				$this->success("生成菜单成功");
			}
			else {
				$this->error("生成菜单失败~，" . $r["alipay_mobile_public_menu_update_response"]["code"]);
			}
		}
		else if ($r["alipay_mobile_public_menu_add_response"]["code"] == 200) {
			$this->success("生成菜单成功");
		}
		else {
			$this->error("生成菜单失败~，" . $r["alipay_mobile_public_menu_add_response"]["code"]);
		}
	}

	public function apiData($method, $biz_content)
	{
		$url = "https://openapi.alipay.com/gateway.do";
		$data = array("app_id" => $this->appid, "method" => $method, "charset" => "UTF-8", "sign_type" => "RSA", "timestamp" => date("Y-m-d H:i:s", time()), "biz_content" => $biz_content, "version" => "1.0");
		require "/PigCms/Lib/ORG/Fuwu/config.php";
		$AlipaySign = new AlipaySign();
		$data["sign"] = $AlipaySign->rsa_sign($this->buildQuery($data), $config["merchant_private_key_file"]);
		$re = new HttpRequest();
		$result = $re->sendPostRequst($url, $data);
		return json_decode(iconv("GBK", "UTF-8", $result), true);
	}

	public function ButtonCreate()
	{
		$topMenu = M("Diymen_class")->where(array("token" => session("token"), "pid" => 0, "is_show" => 1))->limit(4)->order("sort desc")->select();
		$data = $this->getData($topMenu);
		return "{\"button\":" . $data . "}";
	}

	public function getData($list)
	{
		$data = array();
		$subArr = array();
		$i = 0;

		foreach ($list as $key => $value ) {
			$sub = M("Diymen_class")->where(array("pid" => $value["id"]))->limit(5)->select();
			$data[$i]["name"] = $value["title"];

			if ($sub) {
				$data[$i]["subButton"] = json_decode($this->getData($sub), true);
			}
			else if ($value["url"]) {
				if (strpos($value["url"], "tel:") !== false) {
					$data[$i]["actionParam"] = ltrim($value["url"], "tel:");
					$data[$i]["actionType"] = "tel";
				}
				else {
					$data[$i]["actionParam"] = $value["url"];
					$data[$i]["actionType"] = "link";
				}
			}
			else if ($value["keyword"]) {
				$data[$i]["actionParam"] = $value["keyword"];
				$data[$i]["actionType"] = "out";
			}

			$i++;
		}

		return json_encode($data);
	}

	public function buildQuery($query)
	{
		if (!$query) {
			return NULL;
		}

		ksort($query);
		$params = array();

		foreach ($query as $key => $value ) {
			$params[] = $key . "=" . $value;
		}

		$data = implode("&", $params);
		return $data;
	}
}

require "./PigCms/Lib/ORG/Fuwu/HttpRequst.php";
require "./PigCms/Lib/ORG/Fuwu/aop/AopClient.php";
require "./PigCms/Lib/ORG/Fuwu/AlipaySign.php";

?>
