<?php
class subscribe {

	public $token;
	public $wecha_id;
	public $thisWxUser = array();

	public function __construct($token,$wecha_id,$data,$siteurl){
		
		$this->token      = $token;
		$this->wecha_id   = $wecha_id;
		$this->thisWxUser = M('Wxuser')->field('appid,appsecret,winxintype')->where(array('token'=>$token))->find();
	}


	public function sub(){
		
		if($this->thisWxUser['appid'] && $this->thisWxUser['appsecret']&&$this->thisWxUser['winxintype']==3){
			//服务号
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->thisWxUser['appid'].'&secret='.$this->thisWxUser['appsecret'];

			$json=json_decode($this->curlGet($url_get));

			$url='https://api.weixin.qq.com/cgi-bin/user/info?openid='.$this->wecha_id.'&access_token='.$json->access_token;
			$classData=json_decode($this->curlGet($url));
			
			if ($classData->subscribe&&$classData->subscribe==1){
				
				$datainfo['wechaname'] = str_replace(array("'","\\"),array(''),$classData->nickname);
				$datainfo['sex']       = $classData->sex;
				$datainfo['portrait']  = $classData->headimgurl;
				$datainfo['token']     = $this->token;
				$datainfo['wecha_id']  = $this->wecha_id;
				$datainfo['city']      = $classData->city;
				$datainfo['province']  = $classData->province;
				$datainfo['tel']             = '';
				$datainfo['birthday']        = '';
				$datainfo['address']         = '';
				$datainfo['info']            = '';
				$datainfo['sign_score']      = 0;
				$datainfo['expend_score']    = 0;
				$datainfo['continuous']      = 0;
				$datainfo['add_expend']      = 0;
				$datainfo['add_expend_time'] = 0;
				$datainfo['live_time']       = 0;
				$datainfo['getcardtime']     = 0;
			}

		}else{
			//订阅号
				$datainfo['wechaname']       = '';
				$datainfo['sex']             = '';
				$datainfo['portrait']        = '';
				$datainfo['tel']             = '';
				$datainfo['birthday']        = '';
				$datainfo['address']         = '';
				$datainfo['info']            = '';
				$datainfo['sign_score']      = 0;
				$datainfo['expend_score']    = 0;
				$datainfo['continuous']      = 0;
				$datainfo['add_expend']      = 0;
				$datainfo['add_expend_time'] = 0;
				$datainfo['live_time']       = 0;
				$datainfo['getcardtime']     = 0;
				$datainfo['token']           = $this->token;
				$datainfo['wecha_id']        = $this->wecha_id;
		}

		if(! M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->getField('id')){
			//首次关注
			M('Userinfo')->add($datainfo);
		}

	}


	public function unsub(){
		
	}

	public function curlGet($url,$method='get',$data=''){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
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
