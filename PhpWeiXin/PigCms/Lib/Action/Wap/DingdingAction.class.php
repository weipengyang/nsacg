<?php
class DingdingAction extends BaseAction {
    public function _initialize(){
		 
		$url='https://oapi.dingtalk.com/gettoken?corpid='.C('CORPID').'&corpsecret='.C('CORPSECRET');
		$result=$this->dingtalkcurl($url);
		if($result['errcode']!==0){
			$this->error('��ȡtokenʧ��'.$result['errmsg']);
		}else{
			$this->token=$result['access_token'];
		}

	}

	public function index(){
		//�ж��Ƿ����session,�����ھͻ�ȡcode
		session(null);//ʹ��ʱ��ɾ�����
	  if(session('?userid')){
	     $this -> redirect(U('dingtalk/dd_nav_bgcolor=FFFF6600'));
	  }else{
		  $this->assign('corpId',C('CORPID'));
		  $this->assign('second',C('SECOND')); 
		  $this->display();
	  }
		
	  
	}
	
	public function dingtalk(){
		
		if(session('?userid')){
			
		}else{
			//��ȡ�û�����
			$dingtalkf=$result['access_token'];
			$url="https://oapi.dingtalk.com/user/getuserinfo?access_token=".$this->token."&code=".$_GET['code'];
			$result=$this->dingtalkcurl($url);
			if($result['errcode']!==0){
			  $this->error('��ȡ��������ʧ��'.$result['errmsg']);
			  exit();
			}
		
			session('userid',$result['userid']);//��userid����session����id�������ҵ��Ψһ�ģ�����д�����ݿ⣬�û��������¼
		}
		
		$jsapi=$this->getConfig(C('AGENTID'));
		$this->assign('userid',session('userid'));
		$this->assign('jsapi',$jsapi);
		$this->assign('userid',session('userid'));
		$this->display();
	}
	
	
	
	public function getConfig($agen)
    {
        $corpId = C('CORPID');
        $agentId = $agen;
        $nonceStr = '123456';
        $timeStamp = (String)time();
        
        
        //��ȡjsapi
		$url='https://oapi.dingtalk.com/get_jsapi_ticket?access_token='.$this->token;
		$result=$this->dingtalkcurl($url);
		if($result['errcode']!=0){
		    $this->error('��ȡjsapiʧ�ܣ�ԭ��'.$result['errmsg']);
		   exit();
		}
		$ticket=$result['ticket'];
		
		$pageURL = 'http';

        if (array_key_exists('HTTPS',$_SERVER)&&$_SERVER["HTTPS"] == "on")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
		$url = $pageURL;
		
       
		
		$signature = sha1('jsapi_ticket=' . $ticket .'&noncestr=' . $nonceStr .'&timestamp=' . $timeStamp .'&url=' . $url);
			
        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $agentId,
            'timeStamp' => $timeStamp,
            'corpId' => $corpId,
            'signature' => $signature);
        return $config;
    }
	
	//��װ������������curl����
	public function dingtalkcurl($url,$data=null){
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: application/json;charset=UTF-8'));
		}
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output,true);
	}
	
	public function sendoa(){
		$url='https://oapi.dingtalk.com/message/send?access_token='.$this->token;
		$data['touser']=session('userid');//�ռ���
		$data['agentid']=C('AGENTID');//΢Ӧ��id
		$data['msgtype']="oa";
		
		$datau['message_url']='http://www.yemxing.com/index.php/Home/Index/index?dd_nav_bgcolor=FFFF6600';//�����ת��ַ
	
		$datas['head']=array('bgcolor'=>'FFff6600','text'=>'����OA��Ϣ');
		
		
		$datasb['title']='���ǲ�����Ϣ';
		$datasb['form']=array(
		                array('key'=>'��Ϣ���ͣ�',
						      'value'=>'OA'),
						array('key'=>'userid��',
						      'value'=>session('userid')),
						array('key'=>'����QQ��',
						      'value'=>'773477953'),
						array('key'=>'Ⱥ�ţ�',
						      'value'=>'560364185'),
						array('key'=>'��ӭ��',
						      'value'=>'��Ⱥ����'));
		$datasb['content']='���迪�����๦�ܣ�������ϵQQ';
		$datab['body']=$datasb;
		
		$data['oa']=array_merge($datau,$datas,$datab);
		$send=$this->dingtalkcurl($url,json_encode($data));
		if($send['errcode']!==0){
			$this->error('����ʧ�ܣ�ԭ��'.$send['errmsg']);
		}else{
			$this->success('���ͳɹ�����ע�����');
		}
	}

}