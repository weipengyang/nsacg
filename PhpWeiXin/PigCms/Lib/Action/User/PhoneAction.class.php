<?php

class PhoneAction extends UserAction
{
	public function _initialize()
	{
		parent::_initialize();
		$this->canUseFunction("Phone");
	   /*$checkFunc = new checkFunc();

		if (!function_exists("fdsrejsie3qklwewerzdagf4ds")) {
			exit("error-4");
		}

		$checkFunc->cfdwdgfds3skgfds3szsd3idsj();*/
	}

	public function index()
	{
		$this->baseSet();
	}

	public function baseSet()
	{
		$info = M("Mobilesite")->where(array("token" => session("token")))->find();
		if (is_array($info) && $info["tjscript"]) {
			$info["tjscript"] = base64_decode($info["tjscript"]);
			$info["tjscript"] = urldecode(str_replace("jshtmltag", "script", $info["tjscript"]));
		}

		$autodomain = "";
		$mbid = (is_array($info) && (0 < $info["id"]) ? $info["id"] : 0);

		if (in_array(C("server_topdomain"), array("pigcms.cn", "pigcms.com"))) {
			$autodomain = "m_" . session("token") . ".maopan.com";
		}
	
		if ($autodomain) {
			$nomodify = true;
		}
		else {
			$nomodify = false;
		}

		$this->assign("infos", $info);
		$this->assign("autodomain", $autodomain);
		$this->assign("nomodify", $nomodify);
		$this->assign("mbid", $mbid);
		$this->display("baseSet");
	}

	public function saveData()
	{
		$id = $this->_post("id", "trim");
		$owndomain = $this->_post("dnm", "trim");

		if ($owndomain == "nomodify") {
			$owndomain = "m_" . session("token") . ".maopan.com";
		}
		else {
			if (!$owndomain || !preg_match("/(\w+\.){2}\w+/", $owndomain)) {
				$this->dexit(array("error" => 1, "msg" => "域名格式不对"));
			}
		}

		$tjscript = $this->_post("tjscr", "trim");

		if ($tjscript) {
			$tjscript = str_replace("script", "jshtmltag", $tjscript);
			$tjscript = base64_encode($tjscript);
		}
		else {
			$tjscript = "";
		}

		$token = trim(session("token"));
		$db_mobilesite = M("Mobilesite");
		$tmps = $db_mobilesite->where(array("owndomain" => $owndomain))->select();

		if ($tmps) {
			if (1 < count($tmps)) {
				$this->dexit(array("error" => 1, "msg" => "此域名已经被多人绑定过了"));
			}
			else if ($tmps[0]["token"] != $token) {
				$this->dexit(array("error" => 1, "msg" => "此域名已经被他人绑定过了"));
			}
		}

		$tmp = $db_mobilesite->where(array("id" => $id, "token" => $token))->find();
		if ($tmp) {
			$db_mobilesite->where(array("id" => $tmp["id"]))->save(array("tjscript" => $tjscript, "owndomain" => $owndomain));
			$this->dexit(array("error" => 0, "msg" => $tmp["id"]));
		}
		else {
			$data["token"] = $token;
			$data["owndomain"] = $owndomain;
			$data["admindomain"] = $_SERVER["HTTP_HOST"];
			$data["tjscript"] = $tjscript;
			$data["addtime"] = time();
			$mbid = $db_mobilesite->add($data);

			if (0 < $mbid) {
				$this->dexit(array("error" => 0, "msg" => $mbid));
			}
		}

		$this->dexit(array("error" => 1, "msg" => "操作失败"));
	}

	public function downloadFile()
	{
		$tjscript='';
		$info = M("Mobilesite")->where(array("token" => session("token")))->find();
		if($info){
			$tjscript = $info["tjscript"];
		}
		//$tjscript = base64_encode($this->_get("tjscr", "trim"));
		$content = "<?php\r\n\t/* * *服务器必须要安装php libcurl功能扩展库（Client URL Library）****** */\r\n\t/* * *将此文件放在站点根目录下并改名成 index.php即可****** */\r\n\t/* * *Author LiHongShun**2015-01-07**** */\r\n\t/* * *这种方式 cookie存在跨域获取不到问题，目前已解决全民经纪人cookie存取**** */\r\n\t/* * *其他功能如用到cookie 存在跨域问题 请参照下面json格式的解析处理**** */\r\n\t/* * *功能程序员端可用BaseAction 中\$owndomain和\$rget变量来判断是否是手机站独立部署请求的**** */\r\n\t/* * *支持ajax的post和get请求**** */\r\n\tdefine(\"REQUEST_METHOD\", strtoupper(trim(\$_SERVER[\"REQUEST_METHOD\"])));\r\n\tdefine(\"IS_AJAX\", ((isset(\$_SERVER[\"HTTP_X_REQUESTED_WITH\"]) && strtolower(\$_SERVER[\"HTTP_X_REQUESTED_WITH\"]) == \"xmlhttprequest\") || !empty(\$_POST[\"ajax\"]) || !empty(\$_GET[\"ajax\"])) ? true : false);\r\n\t\$tjscript=\"" . $tjscript . "\";  /**第三方统计js脚本**/\r\n\t\$token=\"" . session("token") . "\";\r\n\t\$Request_url=\"\";\r\n\t\$Request_site=\"" . $this->siteUrl . "\";\r\n\tif(isset(\$_SERVER[\"QUERY_STRING\"]) && !empty(\$_SERVER[\"QUERY_STRING\"])){\r\n\t\t\$Request_url = \$Request_site.\"/index.php?\".\$_SERVER['QUERY_STRING'] . \"&rget=3&owndomain=\" . \$_SERVER['HTTP_HOST'];\r\n\t}else if(\$_SERVER[\"QUERY_STRING\"]==\"\" || !strpos(\$_SERVER[\"REQUEST_URI\"],\"g=Wap\")){\r\n\t\t\$Request_url=\$Request_site.\"/index.php?g=Wap&m=Index&a=index&token=\".\$token.\"&rget=3&owndomain=\" . \$_SERVER['HTTP_HOST'];\r\n\t}else{\r\n\t\t\$REQUEST_Uri=preg_replace('/\/(.*)\?/i',\"/index.php?\",\$_SERVER[\"REQUEST_URI\"]);  /***支持不带index.php URL请求***/\r\n\t\t\$Request_url = \$Request_site . \$REQUEST_Uri . \"&rget=3&owndomain=\" . \$_SERVER['HTTP_HOST'];\r\n\t}\r\n\tif(strpos(\$Request_url, \"token=\".\$token) && strpos(\$Request_url, \"g=Wap\") && strpos(\$Request_url, \"m=\") && strpos(\$Request_url, \"a=\")){\r\n\tif (REQUEST_METHOD == \"POST\") {\r\n\t\tif(IS_AJAX) \$_POST[\"ajax\"]=1;\r\n\t\t\$responsearr = httpRequest(\$Request_url, REQUEST_METHOD, \$_POST);\r\n\t\t\$tmpcontent=\$responsearr[\"1\"];\r\n\t} else {\r\n\t\tif(IS_AJAX) \$Request_url=\$Request_url.\"&ajax=1\";\r\n\t\t\$responsearr = httpRequest(\$Request_url, REQUEST_METHOD, null);\r\n\t\t/**get方式请求页面时**正则匹配换掉 图片 css js等引用文件用的相对地址成绝对地址**POST方式不需要替换**/\r\n\t\t\$tplstatic1REG='/(src|href)=([\\\"\'])([\.]{0,2}[\/]?)tpl\/static/i';\r\n\t\t\$tplstatic2REG='/url\(([\\\"\'])([\.]{0,2}[\/]?)tpl\/static/i';\r\n\t\t\$tplWap1REG='/(src|href)=([\\\"\'])([\.]{0,2}[\/]?)tpl\/wap/i';\r\n\t\t\$tplWap2REG='/url\(([\\\"\'])([\.]{0,2}[\/]?)tpl\/wap/i';\r\n\t\t\$tmpcontent=preg_replace(\$tplstatic1REG,\"\\\\1=\\\\2\".\$Request_site.\"/tpl/static\",\$responsearr[\"1\"]);\r\n\t\t\$tmpcontent=preg_replace(\$tplstatic2REG,\"url(\\\\1\".\$Request_site.\"/tpl/static\",\$tmpcontent);\r\n\t\t\$tmpcontent=preg_replace(\$tplWap1REG,\"\\\\1=\\\\2\".\$Request_site.\"/tpl/Wap\",\$tmpcontent);\r\n\t\t\$tmpcontent=preg_replace(\$tplWap2REG,\"url(\\\\1\".\$Request_site.\"/tpl/Wap\",\$tmpcontent);\r\n\r\n\t\t/*\$tmpcontent=preg_replace(\$tplstatic1REG,\"\$1=\$2\".\$Request_site.\"/tpl/static\",\$responsearr[\"1\"]);\r\n\t\t\$tmpcontent=preg_replace(\$tplstatic2REG,\"url(\$1\".\$Request_site.\"/tpl/static\",\$tmpcontent);\r\n\t\t\$tmpcontent=preg_replace(\$tplWap1REG,\"\$1=\$2\".\$Request_site.\"/tpl/Wap\",\$tmpcontent);\r\n\t\t\$tmpcontent=preg_replace(\$tplWap2REG,\"url(\$1\".\$Request_site.\"/tpl/Wap\",\$tmpcontent);*/\r\n\t}\r\n\t /***Header(\"Access-Control-Allow-Origin:\$Request_site\");****/\r\n\t\t/**ajax请求时 json封装带过来的数据 是否需要解析**/\r\n\t\t/**格式为**{\"analyze\":1,\"error\":0,\"msg\":\"opt_cookie\",\"data\":{\"ckkey\":\"bfdhdfhdf\",\"ckv\":2,\"expire\":3600}}**这样的json**/\r\n\t\t/**analyze为数字：指明是否需要解析 大于0的值时需要解析 0不需要解析，请不要写成布尔值****/\r\n\t\t/**error为数字：指明一个状态**msg为字符串：指明操作**data为数据库：指明要操作的数据**/\r\n\t\t/***目前仅支持cookie存取，如有需要以后再扩展此功能******/\r\n\t\t\$jsonREG='/^\{[\\\"\']analyze[\\\"\']\:\d\,[\\\"\']error[\\\"\']\:\d\,[\\\"\']msg[\\\"\']\:(.*)data[\\\"\']\:(.*)\}\}$/i';\r\n\t\tif(preg_match(\$jsonREG,\$tmpcontent,\$matches)){\r\n\t\t   \$jsonstr=\$matches[0];\r\n\t\t   \$jsonarr=!empty(\$jsonstr)? json_decode(\$jsonstr,TRUE):false;\r\n\t\t   if(\$jsonarr && is_array(\$jsonarr)){\r\n\t\t\t  \$is_analyze=isset(\$jsonarr[\"analyze\"]) ? intval(\$jsonarr[\"analyze\"]):0;\r\n\t\t\t  if(\$is_analyze > 0){\r\n\t\t       \$tmpcontent=\$jsonarr[\"error\"];\r\n\t\t\t   \r\n\t\t\t   switch(\$jsonarr[\"msg\"]){\r\n\t\t\t\t  case \"opt_cookie\":\r\n\t\t\t\t\t  \$tmpdata=\$jsonarr[\"data\"];\r\n\t\t\t\t\t  \$expire=intval(\$tmpdata[\"expire\"]);\r\n\t\t\t\t\t  \$expire=\$expire>0 ? time()+\$expire :0;\r\n\t\t\t\t\t  setcookie(\$tmpdata[\"ckkey\"], urlencode(json_encode(\$tmpdata[\"ckv\"])), \$expire, \"/\",\$_SERVER[\"HTTP_HOST\"]);\r\n\t\t\t\t\t  break;\r\n\t\t\t\tdefault:\r\n\t\t\t\t\t\r\n\t\t\t\t\tbreak;\r\n\t\t\t\t}\r\n\t\t      }\r\n\t\t   }\r\n\t\t}\r\n\techo \$tmpcontent; /*输出页面或数据*/\r\n\tif (!IS_AJAX && !empty(\$tjscript)) {\r\n\t\t\$tjscript =urldecode(str_replace(\"jshtmltag\", \"script\", base64_decode(\$tjscript)));\r\n\t\techo \$tjscript;\r\n\t}\r\n\texit();\r\n\t}else{\r\n\t\theader(\"Content-type: text/html; charset=utf-8\");\r\n\t\t\$tipsstr=\"<style type='text/css'>.mytips{height: 60%;margin: 20px;width: 100%;} .mytips p{font-size: 18px;font-weight: bold;margin: 10px;padding-top: 10px;}</style>\";\r\n\t\t\$tipsstr.=\"<div class='mytips'><p>请求地址出错！</p></div>\";\r\n\t\techo \$tipsstr;\r\n\t}\r\n\r\n\tfunction httpRequest(\$url, \$method, \$postfields = null, \$headers = array(), \$debug = false) {\r\n\t\t\$Cookiestr=\"\"; /**cUrl COOKIE处理**/\r\n\t\tif(!empty(\$_COOKIE)){\r\n\t\t   foreach(\$_COOKIE as \$vk=>\$vv){\r\n\t\t       \$tmp[]=\$vk.\"=\".\$vv;\r\n\t\t   }\r\n\t\t\t\$Cookiestr=implode(\";\",\$tmp);\r\n\t\t}\r\n\t\t\$ci = curl_init();\r\n\t\t/* Curl settings */\r\n\t\tcurl_setopt(\$ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);\r\n\t\tcurl_setopt(\$ci, CURLOPT_USERAGENT, \"Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0\");\r\n\t\tcurl_setopt(\$ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */\r\n\t\tcurl_setopt(\$ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */\r\n\t\tcurl_setopt(\$ci, CURLOPT_RETURNTRANSFER, true);\r\n\t\tswitch (\$method) {\r\n\t\t\tcase \"POST\":\r\n\t\t\t\tcurl_setopt(\$ci, CURLOPT_POST, true);\r\n\t\t\t\tif (!empty(\$postfields)) {\r\n\t\t\t\t\t\$tmpdatastr = is_array(\$postfields) ? http_build_query(\$postfields) : \$postfields;\r\n\t\t\t\t\tcurl_setopt(\$ci, CURLOPT_POSTFIELDS, \$tmpdatastr);\r\n\t\t\t\t}\r\n\t\t\t\tbreak;\r\n\t\t\tdefault:\r\n\t\t\t\tcurl_setopt(\$ci, CURLOPT_CUSTOMREQUEST, \$method); /* //设置请求方式 */\r\n\t\t\t\tbreak;\r\n\t\t}\r\n\t\tcurl_setopt(\$ci, CURLOPT_URL, \$url);\r\n\t\tcurl_setopt(\$ci, CURLOPT_HTTPHEADER, \$headers);\r\n\t\tcurl_setopt(\$ci, CURLINFO_HEADER_OUT, true);\r\n\t\tcurl_setopt(\$ci, CURLOPT_COOKIE, \$Cookiestr);/***COOKIE带过去***/\r\n\t\t\$response = curl_exec(\$ci);\r\n\t\t\$http_code = curl_getinfo(\$ci, CURLINFO_HTTP_CODE);\r\n\r\n\t\tif (\$debug) {\r\n\t\t\techo \"=====post data======\\r\\n\";\r\n\t\t\tvar_dump(\$postfields);\r\n\t\t\techo \"=====info===== \\r\\n\";\r\n\t\t\tprint_r(curl_getinfo(\$ci));\r\n\r\n\t\t\techo \"=====\$response=====\\r\\n\";\r\n\t\t\tprint_r(\$response);\r\n\t\t}\r\n\t\tcurl_close(\$ci);\r\n\t\treturn array(\$http_code, \$response);\r\n\t}\r\n\r\n?>";
		$fname = "index.php";
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition: attachment; filename=$fname");
		header("Content-Transfer-Encoding: binary");
		echo $content;
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
