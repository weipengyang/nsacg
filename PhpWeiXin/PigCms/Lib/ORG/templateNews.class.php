<?php
class templateNews{
	

	public $thisWxUser;

	public function __construct(){
		
		$where=array('token'=>session('token'));

		$this->thisWxUser=M('Wxuser')->field('appid,appsecret')->where($where)->find();

	}

    public function get_access_token()
    {
        $data=null;
        $data = S('weixin_access_token');
        if (!empty($data)&&$data->expire_time > time()){
            $access_token = $data->access_token;
            Log::write('templateNews从缓存中获取token->'.$access_token);
        }
        else{
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->thisWxUser['appid'].'&secret='.$this->thisWxUser['appsecret'];
            $res = json_decode($this->curlGet($url_get));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                S('weixin_access_token',$data);
                Log::write('templateNews重新获取token->'.$access_token);

            }
        } 
        return $access_token;
        
    }

	public function sendTempMsg($tempKey,$dataArr){

        // 获取配置信息
        $templates = $this->templates();
        $keys=$templates["$tempKey"]['vars'];
        $data =$this->getData($keys,$dataArr,'#000000');
        $tempid=$templates["$tempKey"]['keys'];
	//	获取access_token  $json->access_token
	// 准备发送请求的数据 
		$requestUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->get_access_token();
	    $sendData = '{"touser":"'.$dataArr["wecha_id"].'","template_id":"'.$tempid.'","url":"'.$dataArr["url"].'","topcolor":"#029700","data":'.$data.'}';

		$this->postCurl($requestUrl,$sendData);
        //Log::write(json_encode($r),Log::DEBUG);
        Log::write($sendData,Log::DEBUG);


	}

// Get Data.data
	public function getData($keys,$dataArr,$color){
		$data = array_flip($keys);
        $jsonData='';
		foreach($dataArr as $k => $v){
			if(in_array($k,array_flip($data))){
				$jsonData .= '"'.$k.'":{"value":"'.$v.'","color":"'.$color.'"},';
			}
		}
		$jsonData = rtrim($jsonData,',');
		return "{".$jsonData."}"; 
	}

	
	public function templates(){
		return array(

		'TM151125' =>
			array(
				'name'=>'充值通知',
                 'keys'=>'UE_uxQV6_8FmVsT2wTj3Zv5sRz0PtJFBE0IJBxDIX0A',
				'vars'=>array('first','accountType','account','amount','result','remark'),
				'content'=>	
                '{{first.DATA}}

                {{accountType.DATA}}：{{account.DATA}}
                充值金额：{{amount.DATA}}
                充值状态：{{result.DATA}}
                {{remark.DATA}}'
				                ),
		'TM151203' =>
			array(
				'name'=>'购买成功通知',
                 'keys'=>'b18OUWQFVWhCOUJ2YySuyWrQsulJqMgYCYCSskGS25I',
				'vars'=>array('first','keyword1','keyword2','keyword3','remark'),
				'content'=>'{{first.DATA}}
                    商品名称：{{keyword1.DATA}}
                    消费金额：{{keyword2.DATA}}
                    购买时间：{{keyword3.DATA}}
                    {{remark.DATA}}'
			),
		'OPENTM201444641' =>
			array(
				'name'=>'会员支付提醒',
				'vars'=>array('first','keyword1','keyword2','keyword3','remark'),
                 'keys'=>'-Q2EHYOtEKXUY_AfDDUi3SdLGF9-i7TuBfe_id1mgj4',
				'content'=>'{{first.DATA}}
                            订单号：{{keyword1.DATA}}
                            消费金额：{{keyword2.DATA}}
                            付款方式：{{keyword3.DATA}}
                            {{remark.DATA}}'
			),
            'TM151213' =>
			array(
				'name'=>'维修完毕结算提醒',
                 'keys'=>'HHXjlsyK0No5AChCON1Q3zPxx5jESDW3OZrJQsffXZ4',
				'vars'=>array('first','keyword1','keyword2','keyword3','keyword4','remark'),
				            'content'=>'{{first.DATA}}
                            车牌号：{{keyword1.DATA}}
                            完工时间：{{keyword2.DATA}}
                            服务顾问：{{keyword3.DATA}}
                            维修费用：{{keyword4.DATA}}
                            {{remark.DATA}}'
			),
            'TM151215' =>
			array(
				'name'=>'保养已过期通知',
				'vars'=>array('first','keynote1','keynote2','keynote3','remark'),
                 'keys'=>'KX9qFkupqOQo1VFIFAFjI2sLgu6uSs77_iWYXwPyL70',
				'content'=>'{{first.DATA}}

                        保养到期时间：{{keynote1.DATA}}
                        上次保养时间：{{keynote2.DATA}} 
                        上次保养里程：{{keynote3.DATA}} 
                        {{remark.DATA}}'
			),
             'TM151126' =>
			array(
				'name'=>'车辆保险到期提醒',
				'vars'=>array('first','keyword1','keyword2','keyword3','keyword4','remark'),
                 'keys'=>'JWgG--5sUt2Y4B5RXFrYy6q8m7pern7Y0yhV_6sA6r8',
				'content'=>'{{{first.DATA}}
                            会员姓名：{{keyword1.DATA}}
                            车牌号码：{{keyword2.DATA}}
                            车型信息：{{keyword3.DATA}}
                            到期时间：{{keyword4.DATA}}
                            {{remark.DATA}}'
			),
            'OPENTM206161737' =>
			array(
				'name'=>'车辆年检到期提醒',
                 'keys'=>'CiEHTE8Fa_kR8ZP0OjV8ktea74b7o7z8VPAd3d35stE',
				'vars'=>array('first','keyword1','keyword2','remark'),
				'content'=>'{{first.DATA}}
                            车辆号牌：{{keyword1.DATA}}
                            年检日期：{{keyword2.DATA}}
                            {{remark.DATA}}'
			),
        'OPENTM200474379' =>
			array(
				'name'=>'兑换码领取成功通知',
                 'keys'=>'i1shAFueQ16hlEIrNFZUTHOd28ho_CEPIOIl4e2lDXU',
				'vars'=>array('first','keyword1','keyword2','keyword3','remark'),
				'content'=>'{{first.DATA}}
                            名称：{{keyword1.DATA}}
                            兑换码：{{keyword2.DATA}}
                            有效期：{{keyword3.DATA}}
                            {{remark.DATA}}'
			),
            'TM160112' =>
			array(
				'name'=>'优惠活动变更通知',
                 'keys'=>'X3RtPAaf9Aw3CVwX9l_ox1mtUwdLosGcQCKqw73NHJg',
				'vars'=>array('first','keyword1','keyword2','remark'),
				'content'=>'{{first.DATA}}
                活动名称：{{keyword1.DATA}}
                变更内容：{{keyword2.DATA}}
                {{remark.DATA}}'
			),
             'TM151214' =>
			array(
				'name'=>'兑换券使用通知',
                 'keys'=>'X3RtPAaf9Aw3CVwX9l_ox1mtUwdLosGcQCKqw73NHJg',
				'vars'=>array('productType','name','certificateNumber','number','remark'),
				'content'=>'{{productType.DATA}}：{{name.DATA}}
                            兑换券号：{{certificateNumber.DATA}}
                            兑换数量：{{number.DATA}}
                            {{remark.DATA}}'
			)		);
	}

// Post Request
	function postCurl($url, $data){
		$ch = curl_init();
        $header =array("Accept-Charset:utf-8");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				//$this->error('模板消息发送失败。错误代码'.$js['errcode'].',错误信息：'.$js['errmsg']);
				return array('rt'=>false,'errorno'=>$js['errcode'],'errmsg'=>$js['errmsg']);

			}
		}
	}
	function postMessage($url, $data){
		$ch = curl_init();
        $header =array("Accept-Charset:utf-8","Content-Type:application/json");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				//$this->error('模板消息发送失败。错误代码'.$js['errcode'].',错误信息：'.$js['errmsg']);
				return array('rt'=>false,'errorno'=>$js['errcode'],'errmsg'=>$js['errmsg']);

			}
		}
	}




// Get Access_token Request
	function curlGet($url){
		$ch = curl_init();
        $header =array("Accept-Charset: utf-8");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}



}
