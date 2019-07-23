<?php
 class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp =time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
      $data=null;
      $data = S('ticketData');
      if (!empty($data)&&$data->expire_time > time()){
          $ticket = $data->jsapi_ticket;
      }
      else {
          $accessToken = $this->getAccessToken();
          // 如果是企业号用以下 URL 获取 ticket
          // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
          $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
          $res = json_decode($this->httpGet($url));
          $ticket = $res->ticket;
          if ($ticket) {
              $data->expire_time = time() + 7000;
              $data->jsapi_ticket = $ticket;
              S('ticketData',$data);
          }
      }
      return $ticket;
  }
  private function getAccessToken()
  {  
      $data=null;
      $data = S('weixin_access_token');
      if (!empty($data)&&$data->expire_time > time()){
          $access_token = $data->access_token;
          Log::write('JSSDK从缓存中获取token->'.$access_token);
      }
      else{
          // 如果是企业号用以下URL获取access_token
          // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
          $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
          $res = json_decode($this->httpGet($url));
          $access_token = $res->access_token;
          if ($access_token) {
              $data->expire_time = time() + 7000;
              $data->access_token = $access_token;
              S('weixin_access_token',$data);
              Log::write('JSSDK重新获取token->'.$access_token);

          }
      } 
      return $access_token;
  }
  public function remark($openid,$remark){
      $data='{"openid":"'.$openid.'","remark":"'.$remark.'"}';
      $rt=$this->curlPost('https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token='.$this->getAccessToken(),$data,0);
      return $rt;
  }
  public function gettaglist($openid){
      Log::write($openid);
      $data='{"openid":"'.$openid.'"}';
      $access_token=$this->getAccessToken();
      $rt=$this->curlPost('https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token='.$access_token,$data,0);
      //$rt=json_decode($rt);
      $taglist=$rt['msg']['tagid_list'];
      return $taglist;
  }
  public function send($content,$openid){
      if($content!=''){
          $data='{"touser":"'.$openid.'","msgtype":"text","text":{"content":"'.$content.'"}}';
          $access_token=$this->getAccessToken();
          $result=$this->curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token,$data,0);
          return $result;
      }
      return true;
  }
  function curlPost($url, $data,$showError=1){
      $ch = curl_init();
      $header =array("Accept-Charset: utf-8");
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
      $header =array("Accept-Charset: utf-8");
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
  public function getuserinfo($openid){
      $access_token=$this->get_access_token();
      $url_get='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
      $userinfo=json_decode($this->curlGet($url_get));
      Log::write('获取用户信息'.json_encode($userinfo));
      return $userinfo;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

}

