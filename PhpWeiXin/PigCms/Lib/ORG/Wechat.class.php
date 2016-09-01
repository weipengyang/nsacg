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

    public function send($content,$openid){
        $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wxuser['appid'].'&secret='.$this->wxuser['appsecret'];
        $json=json_decode($this->curlGet($url_get));
        if (!$json->errmsg){
            $data='{"touser":"'.$openid.'","msgtype":"text","text":{"content":"'.$content.'"}}';
            $rt=$this->curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$json->access_token,$data,0);
        }
	}
	function curlPost($url, $data,$showError=1){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if (intval($js['errcode']==0)){
				return array('rt'=>true,'errorno'=>0,'media_id'=>$js['media_id'],'msg_id'=>$js['msg_id']);
			}else {
				if ($showError){
					$this->error('发生了Post错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
				}
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
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
}
?>