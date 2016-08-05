<?php
class RecordsAction extends BackAction{
	public function index(){
		$records=M('indent');
		//$db=M('Users');
		$count=$records->count();
		$page=new Page($count,25);
		$show= $page->show();
		$info=$records->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
		if ($info){
			$i=0;
			foreach ($info as $item){
				
				if (!$info[$i]['uname']){
					$thisUser=M('Users')->where(array('id'=>$item['uid']))->find();
					$info[$i]['uname']=$thisUser['username'];
			
				}
				$i++;
			}
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}
	public function send(){
		$money=$this->_get('price','intval');
		$data['id']=$this->_get('uid','intval');
		if ($money <= 0){
			$this->error('金额小于等于0，不能入金');
			exit;
		}
	//	dump($money);exit;
		if($money!=false&&$data['id']!=false){
			$thisR=M('Indent')->where(array('id'=>$this->_get('iid','intval')))->find();
			if ($thisR['status']!=2){
			//dump($money);exit;
			$back=M('Users')->where($data)->setInc('money',$money);
			$back=M('Users')->where($data)->setInc('moneybalance',$money);
			$status=M('Indent')->where(array('id'=>$this->_get('iid','intval')))->setField('status',2);
			if($back!=false&&$status!=false){
				$this->success('充值成功');
			}else{
				$this->error('充值失败');
			}
			}else {
				$this->error('已经入金过了');
			}
		}else{
			$this->error('非法操作');
		}
	}
	public function del(){
		$id=$this->_get('iid','intval');
		$db=D('Indent');
		if($db->delete($id)){
			$this->success('删除成功',U(MODULE_NAME.'/index',array('pid'=>$pid,'level'=>3)));
			exit;
		}else{
			$this->error('删除失败',U(MODULE_NAME.'/index',array('pid'=>$pid,'level'=>3)));
			exit;
		}
	}
}