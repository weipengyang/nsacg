<?php 
class WechatShare 
{
	private $appId		= '';
	private $appSecret	= '';
	public $error 		= array();
	public $token 		= '';  
	public $wecha_id 		= '';
	
	
	//构造函数获取access_token
	function __construct($appId,$appSecret,$token,$wecha_id){
		$this->appId		= $appId;
		$this->appSecret	= $appSecret;
		$this->token		= $token;
		$this->wecha_id		= $wecha_id;
	}

	public function getSgin(){
		$now 	= time();
		$share_data 	= M('Wxuser')->where(array('token'=>$this->token))->field('share_ticket,share_dated')->find();
		if( (empty($share_data['share_ticket']) || empty($share_data['share_dated']) ) || ($share_data['share_ticket']!='' && $share_data['share_dated']!='' && $share_data['share_dated'] < $now ) ){
			$access_token 	= $this->get_access_token();
			$ticketData 	= $this->getTicket($access_token);
			if($ticketData['errcode']>0){
				$this->error['ticket_error'] 	= array('errcode'=>$ticketData['errcode'],'errmsg'=>$ticketData['errmsg']);
			}else{
				M('Wxuser')->where(array('token'=>$this->token))->save(array('share_ticket'=>$ticketData['ticket'],'share_dated'=>$now+$ticketData['expires_in']));
				$ticket = $ticketData['ticket'];
			}
		}else{
			$ticket = $share_data['share_ticket'];
		}
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$sign_data 	= $this->addSign($ticket,$url);
		$share_html = $this->createHtml($sign_data);
		return $share_html;
	}

	public function getError(){
		dump($this->error);
	}
	
	public function addSign($ticket,$url){
		$timestamp = time();
		$nonceStr  = rand(100000,999999);
		$array 	= array(
			"noncestr"		=> $nonceStr,		
			"jsapi_ticket"	=> $ticket,
			"timestamp"		=> $timestamp,
			"url"			=> $url,
		);
		
		ksort($array);
		$signPars	= '';
	
		foreach($array as $k => $v) {
			if("" != $v && "sign" != $k) {
				if($signPars == ''){
					$signPars .= $k . "=" . $v;
				}else{
					$signPars .=  "&". $k . "=" . $v;
				}
			}
		}
		
		$result = array(
			'appId' 	=> $this->appId,
			'timestamp' => $timestamp,
			'nonceStr'  => $nonceStr,
			'url' 		=> $url,
			'signature'  => SHA1($signPars),
		);
		
		return $result;
	}



	public function getUrl(){
 		$url 	= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		if(isset($_GET['code']) && isset($_GET['state']) && ($_GET['state'] == 'oauth')){
			$url 		= $this->clearUrl($url);
			if(isset($_GET['wecha_id'])){
				$url .= '&wecha_id='.$this->wecha_id;
			}
			return $url;
		}else{
			return $url;
		}

	}
	
	public function clearUrl($url){
		$param 	= explode('&', $url);
		for ($i=0,$count=count($param); $i < $count; $i++) {
			if(preg_match('/^(code=|state=|wecha_id=).*/', $param[$i])){
				unset($param[$i]);
			}
		}
		return join('&',$param);
	}
    public function get_access_token()
    {  
        $data=null;
        $data = S('weixin_access_token');
        if (!empty($data)&&$data->expire_time > time()){
            $access_token = $data->access_token;
            Log::write('wechatshare从缓存中获取token->'.$access_token);
        }
        else{
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId."&secret=".$this->appSecret;
            $res = json_decode($this->https_request($url_get));
            $access_token = $res->access_token;
            if ($access_token) {
                Log::write('wechatshare重新获取token->'.$access_token);

                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                S('weixin_access_token',$data);
            }
        } 
        return $access_token;
    }
     
	//获取token
	public function  getToken(){
		$data=null;
        $data = S('weixin_access_token');
        if (!empty($data)&&$data->expire_time > time()){
            $access_token = $data->access_token;
            Log::write('wechatshare从缓存中获取token->'.$access_token);
        }
        else{

            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId."&secret=".$this->appSecret;
            $res = json_decode($this->https_request($url_get));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                Log::write('wechatshare重新获取token->'.$access_token);
                $data->access_token = $access_token;
                S('weixin_access_token',$data);
            }
        } 
        return $access_token;
	}

	public function getTicket($token){
		$url 	= "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
		return $this->https_request($url);
	}


	/*创建分享html*/
	public function createHtml($sign_data){

	$html 	= <<<EOM
	<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
	<script type="text/javascript">
		wx.config({
		  debug: false,
		  appId: 	'{$sign_data['appId']}',
		  timestamp: {$sign_data['timestamp']},
		  nonceStr: '{$sign_data['nonceStr']}',
		  signature: '{$sign_data['signature']}',
		  jsApiList: [
		    'checkJsApi',
		    'onMenuShareTimeline',
		    'onMenuShareAppMessage',
		    'onMenuShareQQ',
		    'onMenuShareWeibo',
			'openLocation',
			'getLocation'
		  ]
		});
	</script>
	<script type="text/javascript">
	wx.ready(function () {
	  // 1 判断当前版本是否支持指定 JS 接口，支持批量判断
	  /*document.querySelector('#checkJsApi').onclick = function () {
	    wx.checkJsApi({
	      jsApiList: [
	        'getNetworkType',
	        'previewImage'
	      ],
	      success: function (res) {
	        //alert(JSON.stringify(res));
	      }
	    });
	  };*/

	  // 2. 分享接口
	  // 2.1 监听“分享给朋友”，按钮点击、自定义分享内容及分享结果接口
	    wx.onMenuShareAppMessage({
			title: window.shareData.tTitle,
			desc: window.shareData.tContent,
			link: window.shareData.sendFriendLink,
			imgUrl: window.shareData.imgUrl,
		    type: '', // 分享类型,music、video或link，不填默认为link
		    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		    success: function () { 
				shareHandle('frined');
		        //alert('分享朋友成功');
		    },
		    cancel: function () { 
		        //alert('分享朋友失败');
		    }
		});


	  // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
		wx.onMenuShareTimeline({
			title: window.shareData.tTitle,
			link: window.shareData.sendFriendLink,
			imgUrl: window.shareData.imgUrl,
		    success: function () { 
				shareHandle('frineds');
		        //alert('分享朋友圈成功');
		    },
		    cancel: function () { 
		        //alert('分享朋友圈失败');
		    }
		});	

	  // 2.4 监听“分享到微博”按钮点击、自定义分享内容及分享结果接口
		wx.onMenuShareWeibo({
			title: window.shareData.tTitle,
			desc: window.shareData.tContent,
			link: window.shareData.sendFriendLink,
			imgUrl: window.shareData.imgUrl,
		    success: function () { 
				shareHandle('weibo');
		       	//alert('分享微博成功');
		    },
		    cancel: function () { 
		        //alert('分享微博失败');
		    }
		});
		
	});
		
	function shareHandle(to) {
		var submitData = {
			module: window.shareData.moduleName,
			moduleid: window.shareData.moduleID,
			token:'{$this->token}',
			wecha_id:'{$this->wecha_id}',
			url: window.shareData.sendFriendLink,
			to:to
		};
		$.post('index.php?g=Wap&m=Share&a=shareData&token={$this->token}&wecha_id={$this->wecha_id}',submitData,function (data) {},'json')
	}
</script>
EOM;
		return $html;
	}
	
	//https请求（支持GET和POST）
	protected function https_request($url, $data = null)
	{
		$curl = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		$errorno= curl_errno($curl);
		if ($errorno) {
			return array('curl'=>false,'errorno'=>$errorno);
		}else{
			$res = json_decode($output,1);

			if ($res['errcode']){
				return array('errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']);
			}else{
				return $res;
			}
		}
		curl_close($curl);
	}
}

?>