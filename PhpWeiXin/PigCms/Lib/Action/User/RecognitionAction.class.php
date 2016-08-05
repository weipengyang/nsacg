<?php
class RecognitionAction extends UserAction{
	public function _initialize(){
		parent::_initialize();
		$diymen=M('Diymen_set')->where(array('token'=>$_SESSION['token']))->find();
		if($diymen ==false){
			//$this->error('必须审请微信高级接口认证<br />若您已经审请了该接口，请填写高级接口配置文件',U('Set/index'));
			//$this->error('只有微信官方认证的高级服务号才能使用本功能','?g=User&m=Index&a=edit&id='.$this->thisWxUser['id']);
		}
	}
	
	public function index(){
		if(IS_POST){
			$this->all_insert('Recognition');
		}else{
			$db=D('Recognition');
			$where=array('token'=>session('token'));
			$count=$db->where($where)->count();
			$page=new Page($count,25);
			$wechat_group_db=M('Wechat_group');
			$list=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
			foreach ($list as $key => $value) {
				$list[$key]['group'] = $wechat_group_db->where(array('token'=>$this->token,'wechatgroupid'=>$value['groupid']))->getField('name');
			}
			
			$groups=$wechat_group_db->where(array('token'=>$this->token))->order('id ASC')->select();
			$this->assign('groups',$groups);
			$this->assign('page',$page->show());
			$this->assign('list',$list);
			$this->display();
		}
	}
	public function get_code(){
			$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
			$GetDb=M('Recognition');
			$recognition=$GetDb->where($where)->field('id')->find();
			if($recognition == false) $this->error('非法操作');
			//查询appid appkey是否存在
			$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
			//dump($api);
			if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
			//获取微信认证

			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.trim($api['appid']).'&secret='.trim($api['appsecret']);
			$json=json_decode($this->curlGet($url_get));
			$qrcode_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$json->access_token;
			//{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
			$data['action_name']='QR_LIMIT_SCENE';
			$data['action_info']['scene']['scene_id']=$recognition['id'];
			$post=$this->api_notice_increment($qrcode_url,json_encode($data));
			if($post ==false ) $this->error('微信接口返回信息错误，请联系管理员');
			$update=$GetDb->where(array_merge(array('id'=>$recognition['id']),$where))->save(array('code_url'=>$post));
			if($update !=false){
				$this->success('获取成功');
			}else{
				$this->error('操作失败');
			}
	}
	/*****微餐饮后台餐桌及餐桌二维码获取*******/
	public function get_Wxticket(){
		    $rid=$this->_get('rid') ? $this->_get('rid','intval'):0;
			$tid=$this->_get('tid') ? $this->_get('tid','intval'):0;
			/*$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
			$GetDb=M('Recognition');
			$recognition=$GetDb->where($where)->field('id')->find();
			if($recognition == false) $this->error('非法操作');
			*/
			$db_dish_reply=M('Dish_reply');
			$tmp=$db_dish_reply->where(array('id' => $rid,'token'=>session('token')))->find();
			$reg_id=$tmp['reg_id'];
			$GetDb=M('Recognition');
			if(!($tmp['reg_id']>0)){
			   $reg_id=$GetDb->add(array('token'=>session('token'),'title'=>'餐饮二维码','attention_num'=>0,'keyword'=>$tmp['keyword'],'scene_id'=>0,'status'=>0));
				$db_dish_reply->where(array('id'=>$tmp['id']))->save(array('reg_id'=>$reg_id));
			}
			//查询appid appkey是否存在
			$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
			if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
			//获取微信认证

			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.trim($api['appid']).'&secret='.trim($api['appsecret']);
			$json=json_decode($this->curlGet($url_get),true);
			if(isset($json['access_token'])){
			$qrcode_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$json['access_token'];
			//{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
			$data['action_name']='QR_LIMIT_SCENE';
			$data['action_info']['scene']['scene_id']=$reg_id;
			$post=$this->api_notice_increment($qrcode_url,json_encode($data));
			if($post ==false ) $this->error('微信接口返回信息错误，请联系管理员');
			$update=$GetDb->where(array('id'=>$reg_id,'token'=>session('token')))->save(array('code_url'=>$post));
			if($update !=false){
				if($tid>0){
				   $this->success('获取成功',U('Repast/tableEwm',array('token'=>$this->token,'tid'=>$tid)));
				}else{
				  $this->success('获取成功',U('Repast/company',array('token'=>$this->token)));
				}
			}else{
				$this->error('操作失败');
			}
		}else{
		  $this->error('access_token获取失败');
		}
	}
	public function del(){
		$data=D('Recognition');
		$where['id']=$this->_get('id','intval');
		if($where['id']==false) $this->error('非法操作');
		$where['token']=$this->token;
		//dump($where);exit;
		$back=$data->where($where)->delete();
		if($back==false){
			$this->error('操作失败');
		}else{
			$this->success('操作成功');
		}
	}	
	public function status(){
		$data=D('Recognition');
		$where['id']=$this->_get('id','intval');
		if($where['id']==false) $this->error('非法操作');
		$where['token']=session('token');
		$type=$this->_get('type','intval');
		if($type==0){
			$back=$data->where($where)->setInc('status');
		}else{
			$back=$data->where($where)->setDec('status');
		}
		if($back==false){
			$this->error('操作失败');
		}else{
			$this->success('操作成功');
		}
	}
	function api_notice_increment($url, $data){
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
			$this->error('发生错误：curl error'.$errorno);
			
		}else{

			$js=json_decode($tmpInfo,true);
			
			if (isset($js['ticket'])){
				return $js['ticket'];
			}else {
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
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