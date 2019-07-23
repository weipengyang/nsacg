<?php

class FuwuAction extends BaseAction
{
	protected $FuwuToken;

	protected function _initialize()
	{
		parent::_initialize();
		$checkFunc = new checkFunc();

		if (!function_exists("fdsrejsie3qklwewerzdagf4ds")) {
			exit("error-4");
		}

		$checkFunc->cfdwdgfds3skgfds3szsd3idsj();
		$this->FuwuToken = strip_tags($this->_get("token"));
		$appid = M("Wxuser")->where(array("token" => $this->FuwuToken))->getField("fuwuappid");
		define("FUWU_APPID", $appid);
	}

	public function api()
	{
		require_once FUWU_PATH . "aop/AopClient.php";
		require_once FUWU_PATH . "HttpRequst.php";
		$serviceType = HttpRequest::getRequest("service");
		$biz_content = HttpRequest::getRequest("biz_content");

		switch ($serviceType) {
		case "alipay.service.check":
			$success = "<success>true</success>";
			$biz_content = "<biz_content>MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDVwtjFJVYyf4/sZY+GE3FSeLx7RyOmt+KoWnLi9XsRpQdaXRd+X7mO8kr8Yw6KN9TwgZV8o7iVi3OsuuCD/hgua4Go2oyIWG/NjcaqM3nXOYripfV+BlOdslKBVyAhY6SNuavLt97CVpAe2bIcZH/heNQnHoMQtb/X+KoC6kwouQIDAQAB</biz_content>";
			$tmpArr = array($biz_content, $success);
			$aop = new AopClient();
			$sign = $aop->rsaSign($tmpArr);
			$xmlTmp = "<?xml version=\"1.0\" encoding=\"GBK\"?><alipay><response><success>true</success>" . $biz_content . "</response><sign>" . $sign . "</sign><sign_type>RSA</sign_type></alipay>";
			echo $xmlTmp;
			break;

		case "alipay.mobile.public.message.notify":
			require_once FUWU_PATH . "Message.php";
			$post = file_get_contents("php://input");
			$str = urldecode($post);
			$arr = explode("&", $str);
			$arr = explode("=", $arr[1]);
			$msg = new Message($arr[1], $this->FuwuToken);
			break;
		}
	}
}

define("FUWU_PATH", "./PigCms/Lib/ORG/Fuwu/");

?>
