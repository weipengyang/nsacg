<?php
class Wechat
{
    public $token;
    public $wxuser;
    public $pigsecret;
    private $data = array();
    public function __construct($token, $wxuser = '')
    {
        $this->auth($token, $wxuser) || exit;
        if (IS_GET) {
        	  ob_clean();
            echo ($_GET['echostr']);
            exit;
        } else {
            $this->token = $token;
            if (!$wxuser) {
                $wxuser = M('wxuser')->where(array(
                    'token' => $this->token
                ))->find();
            }
            $this->wxuser = $wxuser;
            if (!$this->wxuser['pigsecret']) {
                $this->pigsecret = $this->token;
            } else {
                $this->pigsecret = $this->wxuser['pigsecret'];
            }
            $xml = file_get_contents("php://input");
            if ($this->wxuser['encode'] == 2) {
                $this->data = $this->decodeMsg($xml);
            } else {
                $xml = new SimpleXMLElement($xml);
                $xml || exit;
                foreach ($xml as $key => $value) {
                    $this->data[$key] = strval($value);
                }
            }
        }
    }
    public function encodeMsg($sRespData)
    {
        $sReqTimeStamp = time();
        $sReqNonce     = $_GET['nonce'];
        $encryptMsg    = "";
        import("@.ORG.aes.WXBizMsgCrypt");
        $pc        = new WXBizMsgCrypt($this->pigsecret, $this->wxuser['aeskey'], $this->wxuser['appid']);
        $sRespData = str_replace('<?xml version="1.0"?>', '', $sRespData);
        $errCode   = $pc->encryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $encryptMsg);
        if ($errCode == 0) {
            return $encryptMsg;
        } else {
            return $errCode;
        }
    }
    public function decodeMsg($msg)
    {
        import("@.ORG.aes.WXBizMsgCrypt");
        $sReqMsgSig    = $_GET['msg_signature'];
        $sReqTimeStamp = $_GET['timestamp'];
        $sReqNonce     = $_GET['nonce'];
        $sReqData      = $msg;
        $sMsg          = "";
        $pc            = new WXBizMsgCrypt($this->pigsecret, $this->wxuser['aeskey'], $this->wxuser['appid']);
        $errCode       = $pc->decryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
        if ($errCode == 0) {
            $data = array();
            $xml  = new SimpleXMLElement($sMsg);
            $xml || exit;
            foreach ($xml as $key => $value) {
                $data[$key] = strval($value);
            }
            return $data;
        } else {
            return $errCode;
        }
    }
    public function request()
    {
        return $this->data;
    }
    public function response($content, $type = 'text', $flag = 0)
    {
        $this->data = array(
            'ToUserName' => $this->data['FromUserName'],
            'FromUserName' => $this->data['ToUserName'],
            'CreateTime' => NOW_TIME,
            'MsgType' => $type
        );
        $this->$type($content);
        $this->data['FuncFlag'] = $flag;
        $xml                    = new SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
        if (isset($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
            exit($this->encodeMsg($xml->asXML()));
        } else {
            exit($xml->asXML());
        }
    }
    private function text($content)
    {
        $this->data['Content'] = $content;
    }
    private function music($music)
    {
        list($music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']) = $music;
        $this->data['Music'] = $music;
    }
    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
            list($articles[$key]['Title'], $articles[$key]['Description'], $articles[$key]['PicUrl'], $articles[$key]['Url']) = $value;
            if ($key >= 9) {
                break;
            }
        }
        $this->data['ArticleCount'] = count($articles);
        $this->data['Articles']     = $articles;
    }
    private function transfer_customer_service($content)
    {
        $this->data['Content'] = '';
    }
    private function data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }
    private function auth($token, $wxuser = '')
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        if (!$wxuser) {
        }
        if ($wxuser && strlen($wxuser['pigsecret'])) {
        }
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if (trim($tmpStr) == trim($signature)) {
            return true;
        } else {
            return true;
        }
    }
    public function getuserinfo($openid){
        $access_token=$this->get_access_token();
        $url_get='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $userinfo=json_decode($this->curlGet($url_get));
        Log::write('获取用户信息'.json_encode($userinfo));
        return $userinfo;
    }
    public function get_access_token()
    {  
        $data=null;
        $data = S('weixin_access_token');
        if (!empty($data)&&$data->expire_time > time()){
            $access_token = $data->access_token;
            Log::write('wechat从缓存中获取token->'.$access_token);
        }
        else{
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wxuser['appid'].'&secret='.$this->wxuser['appsecret'];
            $res = json_decode($this->curlGet($url_get));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                S('weixin_access_token',$data);
                Log::write('wechat重新获取token->'.$access_token);

            }
        } 
        return $access_token;
    }
    public function remark($openid,$remark){
      $data='{"openid":"'.$openid.'","remark":"'.$remark.'"}';
      $rt=$this->curlPost('https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token='.$this->get_access_token(),$data,0);
      return $rt;
    }
    public function gettaglist($openid){
      Log::write($openid);
      $data='{"openid":"'.$openid.'"}';
      $access_token=$this->get_access_token();
      $rt=$this->curlPost('https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token='.$access_token,$data,0);
      //$rt=json_decode($rt);
      $taglist=$rt['msg']['tagid_list'];
      return $taglist;
    }
   public function send($content,$openid){
       if($content!=''){
           $data='{"touser":"'.$openid.'","msgtype":"text","text":{"content":"'.$content.'"}}';
           $access_token=$this->get_access_token();
           $result=$this->curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token,$data,0);
           return $result;
       }
       return true;
	}
   public function sendnews($content,$openid){
       if($content!=''){
           $data='{"touser":"'.$openid.'","msgtype":"news","text":{"content":"'.$content.'"}}';
           $access_token=$this->get_access_token();
           $result=$this->curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token,$data,0);
           return $result;
       }
       return true;
	}
	function curlPost($url, $data,$showError=1){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
        Log::write($tmpInfo);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if (intval($js['errcode']==0)){
				return array('rt'=>true,'msg'=>$js);
			}else {
                if($js['errcode']==40001){
                    Log::write('清空缓存---');
                    S('weixin_access_token',null);
                }
                return array('rt'=>false,'errorno'=>$js['errcode'],'errmsg'=>$js['errmsg']);
			}
		}
	}
	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
}
?> 