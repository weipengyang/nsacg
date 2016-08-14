<?php
class DingdingAction extends BaseAction {
    public function _initialize(){
		 
		$url='https://oapi.dingtalk.com/gettoken?corpid='.C('CORPID').'&corpsecret='.C('CORPSECRET');
		$result=$this->dingtalkcurl($url);
		if($result['errcode']!==0){
			$this->error('获取token失败'.$result['errmsg']);
		}else{
			$this->token=$result['access_token'];
		}

	}

	public function index(){
		//判断是否存在session,不存在就获取code
		session(null);//使用时请删除这句
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
			//换取用户资料
			$dingtalkf=$result['access_token'];
			$url="https://oapi.dingtalk.com/user/getuserinfo?access_token=".$this->token."&code=".$_GET['code'];
			$result=$this->dingtalkcurl($url);
			if($result['errcode']!==0){
			  $this->error('获取个人资料失败'.$result['errmsg']);
			  exit();
			}
		
			session('userid',$result['userid']);//以userid生成session，此id对与该企业是唯一的，可以写进数据库，用户免密码登录
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
        
        
        //获取jsapi
		$url='https://oapi.dingtalk.com/get_jsapi_ticket?access_token='.$this->token;
		$result=$this->dingtalkcurl($url);
		if($result['errcode']!=0){
		    $this->error('获取jsapi失败，原因：'.$result['errmsg']);
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
	
	//封装钉钉发送请求curl方法
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
		$data['touser']=session('userid');//收件人
		$data['agentid']=C('AGENTID');//微应用id
		$data['msgtype']="oa";
		
		$datau['message_url']='http://www.yemxing.com/index.php/Home/Index/index?dd_nav_bgcolor=FFFF6600';//点击跳转地址
	
		$datas['head']=array('bgcolor'=>'FFff6600','text'=>'测试OA消息');
		
		
		$datasb['title']='这是测试消息';
		$datasb['form']=array(
		                array('key'=>'消息类型：',
						      'value'=>'OA'),
						array('key'=>'userid：',
						      'value'=>session('userid')),
						array('key'=>'作者QQ：',
						      'value'=>'773477953'),
						array('key'=>'群号：',
						      'value'=>'560364185'),
						array('key'=>'欢迎：',
						      'value'=>'入群交流'));
		$datasb['content']='如需开发更多功能，请找联系QQ';
		$datab['body']=$datasb;
		
		$data['oa']=array_merge($datau,$datas,$datab);
		$send=$this->dingtalkcurl($url,json_encode($data));
		if($send['errcode']!==0){
			$this->error('发送失败！原因：'.$send['errmsg']);
		}else{
			$this->success('发送成功，请注意查收');
		}
	}

}