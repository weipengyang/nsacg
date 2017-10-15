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
	 
        $jsapi=$this->getConfig('29443806');
        $this->getuser();
        $this->assign('jsapi',$jsapi);
		$this->assign('corpId',C('CORPID'));
		$this->assign('second',C('SECOND')); 
		$this->display();
	  
	}
    function changeordertime(){
        if(IS_POST){
            $id=$_POST['id'];
            $lb=$_POST['lb'];
            if($lb=='1'){
                $data['销售单ID']=$id;
                $data['送货人']=$_POST['user'];
                $data['开始时间']=date('Y-m-d H:i',time());
                $record=M('销售单跟踪','dbo.','difo')->where(array('销售单ID'=>$id))->find();
                if($record){
                    M('销售单跟踪','dbo.','difo')->where(array('ID'=>$id))->save($data);
                }else{
                    M('销售单跟踪','dbo.','difo')->add($data);
                }
            }else{
                M('销售单跟踪','dbo.','difo')->where(array('销售单ID'=>$id))->save(array('送达时间'=>date('Y-m-d H:i',time())));
            }
            echo '操作成功';
        }
        else{
            $jsapi=$this->getConfig('29443806');
            //$this->getuser();
            $this->assign('jsapi',$jsapi);
            $this->assign('corpId',C('CORPID'));
            $this->assign('second',C('SECOND')); 
            $this->display();
        }

    }

	public function record(){
        if(IS_POST){
            $comment=$_POST['comment'];              
            $id=$_POST['id'];
            $issell=$_POST['issell'];
            $membernum=$_POST['membernum'];
            $lb=$_POST['lb'];
            $user=$_POST['user'];
            $tracedata['跟踪时间']=date('Y-m-d H:i',time());
            if($lb==1){
                $type='保险';
            }elseif($lb==2){
                $type='年审';
            }else{
                $type='保养';
            }
            
            $traceinfo=M('客户跟踪','dbo.','difo')->where(array('流水号'=>$id))->find();
            $tracedata['类别']=$type;
            $tracedata['内容']=$comment;
            $tracedata['年份']=date('Y',time());;
            $tracedata['跟踪类型']='现场交流';
            $tracedata['跟踪人']=$user;
            $tracedata['车牌号码']=$traceinfo['车牌号码'];
            $tracedata['车主']=$membernum;
            $tracedata['登记人']=$user;
            M('客户跟踪','dbo.','difo')->add($tracedata);
            M('客户跟踪','dbo.','difo')->where(array('流水号'=>$id))
                ->save(array('是否反馈'=>'是',
                    '跟踪人'=>$user,
                    '反馈时间'=>date('Y-m-d H:i',time()),
                    '是否成交'=>($issell=='true'?'是':'否'),
                    '反馈内容'=>$comment));
            echo 'carno='.$traceinfo['车牌号码'].'&lb='.$type;

        }
        else{
            $jsapi=$this->getConfig('29443806');
            //$this->getuser();
            $this->assign('jsapi',$jsapi);
            $this->assign('corpId',C('CORPID'));
            $this->assign('second',C('SECOND')); 
            $this->display();
        }
	  
	}
	public function history(){

        $lb=$_GET['lb'];
        $carno=$_GET['carno'];
        if($lb==1){
            $type='保险';
        }elseif($lb==2){
            $type='年审';
        }else{
            $type='保养';
        }
        $traceinfo=M('客户跟踪','dbo.','difo')->where(array('车牌号码'=>$carno,'类别'=>$type))->order('跟踪时间 desc')->select();
        $jsapi=$this->getConfig('29443806');
        $this->assign('traceinfo',$traceinfo);
        $this->assign('jsapi',$jsapi);
        $this->assign('corpId',C('CORPID'));
        $this->assign('second',C('SECOND')); 
        $this->display();
        
        
    }
	
	public function dingtalk(){
		
		if(session('?userid')){
			
		}else{
			//换取用户资料
			$url="https://oapi.dingtalk.com/user/getuserinfo?access_token=".$this->token."&code=".$_GET['code'];
			$result=$this->dingtalkcurl($url);
            Log::write(json_encode('用户信息'.$result));

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
	
	
	public function getuser($mobile='')
    {
        $url='https://oapi.dingtalk.com/user/get_by_mobile?access_token='.$this->token.'&mobile=18824160215';
        $result=$this->dingtalkcurl($url);
        Log::write(json_encode('手机号码'.json_encode($result)));

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
		$data['touser']='@all';//收件人
		$data['agentid']='29443806';//微应用id
		$data['msgtype']="text";		
		$datasb['content']='测试消息';
		$data['text']=$datasb;
		$send=$this->dingtalkcurl($url,json_encode($data));
        Log::write(json_encode('发送消息'.json_encode($send)));

	}

}