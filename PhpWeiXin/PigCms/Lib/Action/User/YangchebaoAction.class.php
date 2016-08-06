<?php
class YangchebaoAction extends UserAction{
	public $member_card_set_db;
	public $thisCard;
	public function _initialize() {
		parent::_initialize();
		$this->assign('token',$this->token);
		//
		//$this->canUseFunction('huiyuanka');
		//权限
		if ($this->token!=$_GET['token']){
			//$this->error('非法操作');
		}
		$this->wxuser_db=M("Wxuser");
		//获取所在组的开卡数量
		$thisWxUser=$this->wxuser_db->where(array('token'=>$this->token))->find();
		$thisUser=$this->user;
		$thisGroup=$this->userGroup;
		$this->wxuser_db->where(array('token'=>$this->token))->save(array('allcardnum'=>$thisGroup['create_card_num']));
		//总数
		//if (!$thisUser['card_num']){
			$allcards=M('Member_card_create')->where(array('token'=>$this->token))->select();
			
			$cardTotal=count($allcards);
		
			M('Users')->where(array('id'=>$thisUser['id']))->save(array('card_num'=>$cardTotal));
			M('Wxuser')->where(array('token'=>$this->token))->save(array('yetcardnum'=>$cardTotal));
		//}else {
			//$cardTotal=$thisUser['card_num'];
		//}
		//
		$can_cr_num = $thisWxUser['allcardnum'] - $cardTotal;
		if($can_cr_num > 0){
			$data['cardisok'] = 1;
		}else{
			$data['cardisok'] = 0;
		}
		$this->wxuser_db->where(array('uid'=>session('uid'),'token'=>session('token')))->save($data);
		//
		$this->member_card_set_db=M('Member_card_set');
		//
		$id=intval($_GET['id']);
		if ($id){
			$this->thisCard=$this->member_card_set_db->where(array('id'=>$id))->find();
			if ($this->thisCard&&$this->thisCard['token']!=$this->token){
				$this->error('非法操作');
			}
			$this->assign('thisCard',$this->thisCard);
		}
		
		//transfer start
		$data=M('Member_card_create');
		$cardByToken=$this->member_card_set_db->where(array('token'=>$this->token))->order('id ASC')->find();
		if ($cardByToken){
			$data->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
			M('Member_card_exchange')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
			M('Member_card_coupon')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
			M('Member_card_vip')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
			M('Member_card_integral')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		}
		//transfer end
		$type 	= $this->_get('type','intval');
		$this->assign('type',$type?$type:1);
	}
    public function setting()
    {   
        $db = M('yangchebao_setting');
        $item=$db->where(array('token'=>$this->token))->find();
		$this->assign('item',$item);
        $this->display();
    }
    public function changestate()
    {
        if(IS_POST){
            $db = M('yangchebao_withdraw');
            $id=$_GET['id'];
            $record=$db->where(array('id'=>$id))->find();
            $record['state']=$_POST['state'];
            if(intval($record['state'])==4)
            {
                M('userinfo')->where(array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id']))->setInc('balance',$record['money']);
            }
            $record['end_time']=time();
            if($db->save($record))
            {
                echo '操作成功';
                exit();
            }
            else{
                echo '操作失败，请稍后再试';
                exit();
            }
        }
        else
        {
            $this->display();
        }
    }
    public function customSave(){


		$db = M('yangchebao_setting');
		if($db->create() === false){
			$this->error('请稍后再试~');
		}else{
			unset($_POST['__hash__']);
			unset($_POST['token']);
			$_POST['token'] = $this->token;
            $data['token']= $this->token;
            $data['rate']=$_POST['rate'];
            $data['max_price']=$_POST['max_price'];
            $data['rate_date']= date("Y-m-d H:i:s", time());
			if($db->where(array('token'=>$this->token))->find() == NULL){
				if($db->add($_POST)){
                    M('yangchebao_rate')->add($data);
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				if($db->where(array('token'=>$this->token))->save($_POST)){
                    M('yangchebao_rate')->add($data);
					$this->success('保存成功');
				}else{
					
					$this->error('保存失败');
				}
			}
            
		}
	}

	public function index(){
		$cards=$this->member_card_set_db->where(array('token'=>$this->token))->order('id ASC')->select();
		if ($cards){
			$card_create_data=M('Member_card_create');
			$i=0;
			foreach ($cards as $card){
				$cards[$i]['usercount']=$card_create_data->where('cardid='.intval($card['id']).' AND token="'.$this->token.'" AND wecha_id!=""')->count();
				$cards[$i]['cardcount']=$card_create_data->where('cardid='.intval($card['id']).' AND token="'.$this->token.'"')->count();
				$i++;
			}
		}
		$this->assign('cards',$cards);
		$this->display();
	}

	
	public function add_gifts(){
		$cardid 	= $this->_get('id','intval');
		$gid 		= $this->_get('gid','intval');
		if(IS_POST){
			if(D('Member_card_gifts')->create()){
					$_POST['cardid']= $cardid;
					$_POST['token'] = $this->token;
					$_POST['start'] = strtotime($this->_post('start','trim'));
					$_POST['end'] 	= strtotime($this->_post('end','trim'));
					if(D('Member_card_gifts')->add($_POST)){
						$this->success('添加成功',U('Member_card/gifts',array('token'=>$this->token,'id'=>$cardid)));
					}
			}else{
				$this->error(D('Member_card_gifts')->getError());
			}
		}else{
			$this->display();
		}

	}
	
	public function edit_gifts(){
		$cardid 	= $this->_get('id','intval');
		$gid 		= $this->_get('gid','intval');
		$info 		= D('Member_card_gifts')->where(array('token'=>$this->token,'cardid'=>$cardid,'id'=>$gid))->find();

		$item_name 	= M('Member_card_coupon')->where(array('token'=>$this->token,'cardid'=>$cardid,'id'=>$info['item_value']))->getField('title');
		$info['item_name'] 	= $item_name;

		if(IS_POST){
			if(D('Member_card_gifts')->create()){
				$_POST['start'] = strtotime($this->_post('start','trim'));
				$_POST['end'] 	= strtotime($this->_post('end','trim'));
				$where 	= array('token'=>$this->token,'cardid'=>$cardid,'id'=>$this->_post('gid','intval'));		
				D('Member_card_gifts')->where($where)->save($_POST);
				$this->success('修改成功',U('Member_card/gifts',array('token'=>$this->token,'id'=>$cardid)));
			}else{
				$this->error(D('Member_card_gifts')->getError());
			}
		}else{
			$this->assign('set',$info);
			$this->display();
		}
	}

	public function del_gifts(){
		$id 	= $this->_get('id','intval');
		$gid 	= $this->_get('gid','intval');
		$where  = array('id'=>$gid,'token'=>$this->token,'cardid'=>$id);
		if(M('Member_card_gifts')->where($where)->delete()){
			$this->success('删除成功',U('Member_card/gifts',array('token'=>$this->token,'id'=>$id)));
		}
	}
	
	public function get_value(){	
		$cardid 	= $this->_get('cardid','intval');
		$item_attr 	= $this->_get('item_attr','intval');
		$now 		= time();
		$result 	= array();
		if($item_attr == 1){
			$result['err'] 	= 0;
			$result['info'] = M('Member_card_coupon')->where(array('token'=>$this->token,'cardid'=>$cardid,'attr'=>'1','type'=>1,'statdate'=>array('lt',$now),'enddate'=>array('gt',$now)))->field('id,title')->select();	
		}else if($item_attr == 2){
			$result['err'] 	= 0;
			$result['info'] = M('Member_card_coupon')->where(array('token'=>$this->token,'cardid'=>$cardid,'attr'=>'1','type'=>0,'statdate'=>array('lt',$now),'enddate'=>array('gt',$now)))->field('id,title')->select();	
		}else{
			$result['err'] 	= 1;
			$result['info'] = '';
		}		
		echo json_encode($result);
	}
	/**
	*积分设置 设置会员卡积分策略及会员卡级别
	*
	*/
	public function exchange(){
		$data=M('Member_card_exchange')->where(array('token'=>$this->token,'cardid'=>$this->thisCard['id']))->find();
		if(IS_POST){
			$_POST['token']=$this->token;
			$_POST['cardid']=$this->thisCard['id'];
			$_POST['create_time'] = time();		
			if($data==false){				
				$this->all_insert('Member_card_exchange','/exchange?token='.$this->token.'&id='.$this->thisCard['id']);
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Member_card_exchange','/exchange?token='.$this->token.'&id='.$this->thisCard['id']);
			}
		}else{
			$this->assign('exchange',$data);
			$this->display();
		}	
	}
	public function notice(){
		$member_card_notice_db=M('Member_card_notice');
		$where=array('cardid'=>$this->thisCard['id']);
		$count      = $member_card_notice_db->where($where)->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $member_card_notice_db->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
		
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	function noticeSet(){
		$member_card_notice_db=M('Member_card_notice');
		if (IS_POST){
			$_POST['cardid']=$this->thisCard['id'];
			$_POST['time']=time();
			$enddates=explode('-',$_POST['enddate']);
			$_POST['endtime']=mktime(23,59,59,$enddates[1],$enddates[2],$enddates[0]);
			if (!isset($_GET['noticeid'])){
				$member_card_notice_db->add($_POST);
			}else {
				$member_card_notice_db->where(array('id'=>intval($_GET['noticeid'])))->save($_POST);
			}
			$this->success('设置成功',U('Member_card/notice',array('token'=>session('token'),'id'=>$this->thisCard['id'])));
		}else {
			if (isset($_GET['noticeid'])){
				$thisNotice=$member_card_notice_db->where(array('id'=>intval($_GET['noticeid'])))->find();
			}else {
				$thisNotice['endtime']=time()+10*24*3600;
			}
			$this->assign('thisNotice',$thisNotice);
			$this->display('noticeAdd');
		}
	}
	function noticeDelete(){
		$member_card_notice_db=M('Member_card_notice');
		$member_card_notice_db->where(array('id'=>intval($_GET['noticeid'])))->delete();
		$this->success('操作成功',U('Member_card/notice',array('token'=>session('token'),'id'=>$this->thisCard['id'])));
	}
	public function privilege(){
		$data=M('Member_card_vip')->where(array('token'=>$this->token,'cardid'=>$this->thisCard['id']))->order('id desc')->select();
		$this->assign('data_vip',$data);
		$this->display();	
	}
	public function privilege_add(){
		$member_card_vip=M('Member_card_vip');
		if(IS_POST){
			$_POST['cardid']=$this->thisCard['id'];
			$_POST['token']=$this->thisCard['token'];
			$_POST['create_time']=time();
			$enddates=explode('-',$_POST['enddate']);
			$_POST['enddate']=0;
			if (!$_POST['type']){
				$_POST['enddate']=mktime(23,59,59,$enddates[1],$enddates[2],$enddates[0]);
			}
			//
			$startdates=explode('-',$_POST['statdate']);
			$_POST['statdate']=0;
			if (!$_POST['type']){
				$_POST['statdate']=mktime(0,0,0,$startdates[1],$startdates[2],$startdates[0]);
			}
			if (!isset($_GET['itemid'])){
				$member_card_vip->add($_POST);
			}else {
				$member_card_vip->where(array('id'=>intval($_GET['itemid'])))->save($_POST);
			}
			$this->success('操作成功',U('Member_card/privilege',array('token'=>session('token'),'id'=>$this->thisCard['id'])));
		}else{
			if (isset($_GET['itemid'])){
				$thisItem=$member_card_vip->where(array('id'=>intval($_GET['itemid'])))->find();
			}else {
				//$thisNotice['endtime']=time()+10*24*3600;
			}
			$this->assign('vip',$thisItem);
			$this->display('privilege_add');
		}
	}
	public function privilege_del(){
		$this->_isUseRecordExist(1,$_GET['itemid']);
		$member_card_vip=M('Member_card_vip');
		$data=$member_card_vip->where(array('token'=>$this->token,'id'=>$this->_get('itemid')))->delete();
		if($data==false){
			$this->error('服务器繁忙请稍后再试');
		}else{
			$this->success('操作成功',U('Member_card/privilege',array('id'=>$this->thisCard['id'],'token'=>$this->token,'type'=>4)));
		}
	}
	//会员优惠卷
	public function coupon(){
		$member_card_coupon_db=M('Member_card_coupon');
		$data=$member_card_coupon_db->where("(token='".$this->token."' and cardid='".$this->thisCard['id']."') or attr='2"."'")->order('id desc')->select();
        foreach ($data as $k=>$n){
            $data[$k]['count']= M('member_card_coupon_record')->where(array('coupon_id'=>$n['id'],'coupon_type'=>$n['type']==1?1:2))->count();
        }
		$this->assign('data_vip',$data);
		$this->display();	
	}
	public function coupon_edit(){
		$member_card_coupon_db=M('Member_card_coupon');
		if(IS_POST){
			$_POST['cardid']=$this->thisCard['id'];
			if (!isset($_GET['itemid'])){
				$this->all_insert('Member_card_coupon','/coupon?id='.$this->thisCard['id']);
			}else {
				$this->all_save('Member_card_coupon','/coupon?id='.$this->thisCard['id']);
			}
		}else{
			$now=time();
			if (isset($_GET['itemid'])){
				$data=$member_card_coupon_db->where(array('token'=>session('token'),'id'=>$this->_get('itemid')))->find();
			}else {
				$data['statdate']=$now;
				$data['enddate']=$now+10*24*3600;;
			}
			$this->assign('vip',$data);
			$this->display('coupon_edit');
		}
		
	}	
	public function coupon_del(){
		$this->_isUseRecordExist(3,$_GET['itemid']);
			$data=M('Member_card_coupon')->where(array('token'=>session('token'),'id'=>$this->_get('itemid')))->delete();
			if($data==false){
				$this->error('没删除成功');
			}else{
				$this->success('操作成功',U('Member_card/coupon',array('id'=>$this->thisCard['id'],'token'=>$this->token,'type'=>2)));
			
			}
	}
	//会员礼卷
	public function integral(){
		$member_card_inergral_db=M('Member_card_integral');
		$data=$member_card_inergral_db->where(array('token'=>$this->token,'cardid'=>$this->thisCard['id']))->order('id desc')->select();
        foreach ($data as $k=>$n){
            $data[$k]['count']= M('member_card_coupon_record')->where(array('coupon_id'=>$n['id'],'coupon_type'=>3))->count();
        }
		$this->assign('data_vip',$data);
		$this->display();	
	}
	public function integral_edit(){
		$member_card_inergral_db=M('Member_card_integral');
		if(IS_POST){
			$_POST['cardid']=$this->thisCard['id'];
			if (isset($_GET['itemid'])){
				$this->all_save('Member_card_integral','/integral?id='.$this->thisCard['id']);
			}else {
				$this->all_insert('Member_card_integral','/integral?id='.$this->thisCard['id']);
			}
		}else{
			$now=time();
			if (isset($_GET['itemid'])){
				$data=$member_card_inergral_db->where(array('token'=>$this->token,'id'=>$this->_get('itemid')))->find();
			}else {
				$data['statdate']=$now;
				$data['enddate']=$now+10*24*3600;;
			}
			$this->assign('vip',$data);
			$this->display();
			
		}
		
	}	

	public function integral_del(){
		$this->_isUseRecordExist(2,$_GET['itemid']);
			$data=M('Member_card_integral')->where(array('token'=>session('token'),'id'=>$this->_get('itemid')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功',U('Member_card/integral',array('id'=>$this->thisCard['id'],'token'=>$this->token,'type'=>3)));
			
			}
	}
	public function staff(){
		$company_staff_db=M('Company_staff');
		$data=$company_staff_db->where(array('token'=>$this->token))->order('id desc')->select();
		$company_db=M('Company');
		$companys=$company_db->where(array('token'=>$this->token))->order('id ASC')->select();
		//
		$companysByID=array();
		if ($companys){
			foreach ($companys as $c){
				$companysByID[$c['id']]=$c;
			}
		}
		//
		if ($data){
			$i=0;
			foreach ($data as $d){
				$data[$i]['companyName']=$companysByID[$d['companyid']]['name'];
				$i++;
			}
		}
		$this->assign('staffs',$data);
		$this->display();	
	}
	public function staffSet(){
		$company_staff_db=M('Company_staff');
		if(IS_POST){
			if (!trim($_POST['name'])||!trim($_POST['username'])||!intval($_POST['companyid'])){
				$this->error('姓名、用户名和店铺都不能为空');
				exit();
			}
			$_POST['token']=$this->token;
			$_POST['time']=time();
			$_POST['password']=md5($_POST['password']);
			if (!isset($_GET['itemid'])){
				$company_staff_db->add($_POST);
			}else {
				if (!trim($_POST['password'])){
					unset($_POST['password']);
				}
				$company_staff_db->where(array('id'=>intval($_GET['itemid'])))->save($_POST);
			}
			$this->success('操作成功',U('Member_card/staff',array('token'=>session('token'),'id'=>$this->thisCard['id'])));
		}else{
			$company_db=M('Company');
			$companys=$company_db->where(array('token'=>$this->token))->order('id ASC')->select();
			$this->assign('companys',$companys);
			if (isset($_GET['itemid'])){
				$thisItem=$company_staff_db->where(array('id'=>intval($_GET['itemid'])))->find();
			}else {
				$thisItem['companyid']=0;
				//$thisNotice['endtime']=time()+10*24*3600;
			}
			$this->assign('item',$thisItem);
			$this->display('staffSet');
		}
	}
	public function staffDelete(){
		$company_staff_db=M('Company_staff');
		$thisItem=$company_staff_db->where(array('id'=>intval($_GET['itemid'])))->find();
		if ($thisItem['token']==$this->token){
		$data=$company_staff_db->where(array('token'=>session('token'),'id'=>$this->_get('itemid')))->delete();
		$this->success('操作成功',U('Member_card/staff',array('id'=>$this->thisCard['id'],'token'=>$this->token)));
		}
	}

	public function useRecords(){
		$types	= array('vip'=>4,'coupon'=>1,'integral'=>2);
		$type	= $_GET['type'];
		if (!$types[$type]){
			exit('no type');
		}
		switch ($type){
			case 'vip':
				$a='privilege';
				break;
			case 'integral':
				$a=$type;
				break;
			case 'coupon':
				$a=$type;
				break;
		}
		$this->assign('a',$a);
		$db			= M('Member_card_'.$type);
		$wheres 	= array('id'=>intval($_GET['itemid']));
		
		$thisItem	= $db->where($wheres)->find();	
		$this->assign('thisItem',$thisItem);
		$record_db 	= M('Member_card_use_record');
		$where		= array('itemid'=>$thisItem['id'],'cat'=>$types[$type]);
		$count      = $record_db->where($where)->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $record_db->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		$wecha_ids=array();
		$staffids=array();
		if ($list){
			foreach ($list as $l){
				if (!in_array($l['wecha_id'],$wecha_ids)){
					array_push($wecha_ids,$l['wecha_id']);
				}
				if (!in_array($l['staffid'],$staffids)){
					array_push($staffids,$l['staffid']);
				}
			}
			//
			$userinfo_where['wecha_id']=array('in',$wecha_ids);
			$users=M('Userinfo')->where($userinfo_where)->select();
			$usersArr=array();
			if ($users){
				foreach ($users as $u){
					$usersArr[$u['wecha_id']]=$u;
				}
			}
			$cards=M('Member_card_create')->where($userinfo_where)->select();
			$cardsArr=array();
			if ($cards){
				foreach ($cards as $u){
					$cardsArr[$u['wecha_id']]=$u;
				}
			}
			$staffWhere=array('in',$staffids);
			$staffs=M('Company_staff')->where($staffWhere)->select();
			$staffsArr=array();
			if ($staffs){
				foreach ($staffs as $s){
					$staffsArr[$s['id']]=$s;
				}
			}
		}
		//
		if ($list){
			$i=0;
			foreach ($list as $l){
				$list[$i]['userName']=$usersArr[$l['wecha_id']]['truename'];
				$list[$i]['userTel']=$usersArr[$l['wecha_id']]['tel'];
				$list[$i]['cardNum']=$cardsArr[$l['wecha_id']]['number'];
				$list[$i]['operName']=$staffsArr[$l['staffid']]['name'];
				$i++;
			}
		}
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->assign('count',$count);
		$this->assign('types',$types[$type]);
		$this->display();
	}

	public function useRecord_del(){
		$record_db=M('Member_card_use_record');
		$thisRecord=$record_db->where(array('id'=>intval($_GET['itemid'])))->find();

		if ($thisRecord['token']!=$this->token){
			exit('error');
		}

		if ($thisRecord['cat']){
			switch ($thisRecord['cat']){
				case 1:
					$type='vip';
					break;
				case 2:
					$type='integral';
					break;
				case 3:
					$type='coupon';
					break;
			}
			
			if ($type){
				$db=M('Member_card_'.$type);
				$thisItem=$db->where(array('id'=>$thisRecord['itemid']))->find();
			}
		}
		$record_db->where(array('id'=>intval($_GET['itemid'])))->delete();

		$userinfo_db=M('Userinfo');
    	$thisUser = $userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$thisRecord['wecha_id']))->find();
		$userArr=array();
		$userArr['total_score']=$thisUser['total_score']-$thisRecord['score'];
		$userArr['expensetotal']=$thisUser['expensetotal']-$thisRecord['expense'];
		$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$thisRecord['wecha_id']))->save($userArr);

		if ($thisRecord['itemid']){
			$useCount=$thisItem['usetime'];
			$useCount=intval($useCount)-$thisRecord['usecount'];
			$db->where(array('id'=>$thisRecord['itemid']))->save(array('usetime'=>$useCount));
		}
		$this->success('操作成功');
    }

	
	
	function _isUseRecordExist($cat,$itemid){
		$record_db=M('Member_card_use_record');
		$thisRecord=$record_db->where(array('itemid'=>intval($itemid),'cat'=>intval($cat)))->find();
		if ($thisRecord){
			$this->error('请先删除该信息下的所有使用记录，然后再删除本信息');
		}
	}
    public function resetpass()
    {
      if(IS_POST){
          $update['wecha_id']=$_GET['wecha_id'];
          $data['paypass'] = md5('123456');
          if(M('Userinfo')->where($update)->save($data)){
          }
          echo'{"success":1,"msg":"密码重置成功"}';	
       
      }
    }
    public function change()
    {
		//$uid = (int)$_GET['uid'];
        if(IS_POST){
           $member_card_set_db=M('Member_card_set'); 
           $cardid=$_POST['newcardid'];
           $thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>$cardid))->find();  
           $card=M('Member_card_create')->field('id,number')->where("token='".$this->token."' and cardid=".intval($thisCard['id'])." and wecha_id = ''")->order('id ASC')->find();
           $now=time();
           if($card)
           {

               $gwhere = array('token'=>$this->token,'cardid'=>$cardid,'is_open'=>'1','start'=>array('lt',$now),'end'=>array('gt',$now));
			   $gifts 	= M('Member_card_gifts')->where($gwhere)->select();
               foreach($gifts as $key=>$value){
                   if($value['type'] == "1"){
                       //赠积分
                       $arr=array();
                       $arr['itemid']	= 0;
                       $arr['token']	= $this->token;
                       $arr['wecha_id']= $_GET['wecha_id'];
                       $arr['expense']	= 0;
                       $arr['time']	= $now;
                       $arr['cat']		= 3;
                       $arr['staffid']	= 0;
                       $arr['notes']	= '开卡赠送积分';
                       $arr['score']	= $value['item_value'];
                       
                       M('Member_card_use_record')->add($arr);
                       M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id']))->setInc('total_score',$arr['score']);
                   }else{
                       $data['token']		= $this->token;
                       $data['wecha_id']	= $_GET['wecha_id'];
                       $data['coupon_id']	= $value['item_value'];
                       $data['is_use']		= '0';
                       $data['cardid']		= $cardid;
                       $data['add_time']	= $now;
                       $where 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'id'=>$data['coupon_id']);
                       $coupon=M('Member_card_coupon')->where($where)->find();
                       $days=$coupon['days'];
                       $data['over_time']=strtotime(date('Y-m-d',time)."+$days day");
                       $data['coupon_num']=date('YmdHis',time()).mt_rand(1000,9999);
                       //赠卷
                       if($value['item_attr'] == '1'){						
                           $data['coupon_type']	= '1';
                           M('Member_card_coupon_record')->add($data);
                       }else{
                           $data['coupon_type']	= '2';
                           M('Member_card_coupon_record')->add($data);
                       }
                   }
               }                 
               M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id']))->delete();
               M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$_GET['wecha_id']));
               echo "操作成功";
           }else{
               echo "会员卡数量不够";
           }
        }
        else{
            $db = M('member_card_set');
 		    $list= $db->where(array('token'=>$this->token))->order("grade")->select();
            $this->assign('list',$list);
            $this->assign('truename',$_GET['truename']);
            $this->assign('carno',$_GET['carno']);
            $this->display();
        }
    }

    public function sendmessage()
    {
        if(IS_POST){
           $model  = new templateNews();
           $dataKey='';
           $dataArr=array();
           $card=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id']))->find();
           $cardid=$card['cardid'];
           $user=M('userinfo')->where(array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id']))->find();
           $carinfo = $_POST['carinfo'];
           switch($_POST['mtype']){
               case '1'://维修服务完成
                   $dataKey    = 'TM151213';
                   $wxinfo=$_POST['wxinfo'];
                   $dataArr    = array(
                       'first'         => '尊敬的车主，您的爱车已经维修完毕。',
                       'keyword1'      =>$user['carno'],//车牌号
                       'keyword2'      => date("Y-m-d",time()),//完工时间
                       'keyword3'      => $wxinfo['接车人'].'（电话：'.$wxinfo['接车人电话'].')',//接车人与联系电话
                       'keyword4'      => $wxinfo['应收金额'].'元',//维修费用
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '请您安排时间到店取车，如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                       'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               case '2'://保险到期通知
                   $dataKey    = 'TM151126';
                   $dataArr    = array(
                       'first'         => '车辆保险到期时间提前一个月通知您',
                       'keyword1'      => $user['truename'],
                       'keyword2'      =>$user['carno'],
                       'keyword3'      =>$carinfo['车型'],
                       'keyword4'      => $carinfo['交保到期'],
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '您的车辆保险这个月到期,如您有需要，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                       'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               case '3'://年检通知
                   $dataKey    = 'OPENTM206161737';
                   $dataArr    = array(
                       'first'         => '尊敬的['.$user['carno'].']车主您好，您的车辆年检即将到期。',
                       'keyword1'      =>$user['truename'],
                       'keyword2'      => $carinfo['年检日期'],
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '在办理年检之前，请确认无任何未处理违法记录。我们提供待办年检业务，可以立刻在微信平台上进行预约,或致电020-39099139进行电话预约。',
                       'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               case '4':
                   $dataKey    = 'TM160112';
                   $dataArr    = array(
                       'first'         => '尊敬的['.$user['carno'].']白金卡会员您好，您参加的白金会员卡洗车活动有重大的变更，请注意我们的会员通知。',
                       'keyword1'      =>'白金卡会员充值洗车服务',
                       'keyword2'      =>'洗车业务由原来会员卡支付升级成洗车券消费，价格由原来的19.9元调整为25元，券的有效期为1年，对于白金卡会员我们推出买4张送1张的优惠活动，活动截止日期2016年1月18日，为了保障您的权益，请您安排时间尽快购买。',
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '您可以致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                       'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               default://保养通知
                   $dataKey    = 'TM151215';
                   $dataArr    = array(
                       'first'         => '尊敬的车主您好，您车牌为'.$user['carno'].'的汽车保养已到期。',
                       'keynote1'      =>$carinfo['下次保养'],//保养到期时间
                       'keynote2'      => $carinfo['最近保养'],//上次保养时间
                       'keynote3'      => $carinfo['保养里程'].'公里',//上次保养里程
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '如有需要，致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                       'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;

           }
           $model->sendTempMsg($dataKey,$dataArr);
           echo "发送成功";
           
        }
        else{
            $this->assign('truename',$_GET['truename']);
            $this->assign('carno',$_GET['carno']);
            $this->display();
        }
    }
    public function present(){
        $db = M('member_card_coupon');
		//$uid = (int)$_GET['uid'];
		$cardid = $this->_get('cardid','intval');
		$list= $db->where(array('token'=>$this->token,'cardid'=>$cardid,'attr'=>'1','ispublic'=>'1'))->field('id,title,type,days')->select();
        if(IS_POST){
            if ($list){
                foreach ($list as $item){
                    if($_POST[$item['id']]&&$this->_post($item['id'],'intval')>0)
                    {
                        for($i=0;$i<$this->_post($item['id'],'intval');$i++)
                        {
                            $data['use_time'] 		= '';
                            $data['add_time'] 		= time();
                            $data['coupon_id'] 		= $item['id'];
                            $data['cardid'] 		= $cardid;
                            $days=$item['days'];                           
                            $data['over_time']=strtotime(date('Y-m-d',time())."+$days day");
                            $data['token'] 			= $this->token;
                            $data['is_use'] 			= 0;
                            $data['wecha_id'] 		= $_GET['wecha_id'];
                            $data['coupon_type'] 	=  $item['type'];
                            $coupon_num=date('YmdHis',time()).mt_rand(1000,9999);
                            $data['coupon_num'] 	=  $coupon_num;
                            M('Member_card_coupon_record')->add($data);
                            $model  = new templateNews();
                            $dataKey    = 'OPENTM200474379';
                            $dataArr    = array(
                                'first'         => '恭喜您获得一张'.$item['title'],
                                'keyword1'      => $item['title'],
                                'keyword2'      => $coupon_num,
                                'keyword3'      => date("Y-m-d",$data['over_time']),
                                'wecha_id'      => $_GET['wecha_id'],
                                'remark'        => '如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                                'url'      => U('wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                            );
                            $model->sendTempMsg($dataKey,$dataArr);
                        }
                    }
                }
            }
            echo '操作成功';
        }
        else{
            $this->assign('list',$list);
            $this->assign('truename',$_GET['truename']);
            $this->assign('carno',$_GET['carno']);
            $this->display();
        }

    }
	public function members(){
               
		$card_create_db=M('Member_card_create');
		$where=array();
		$where['cardid']= intval($_GET['id']);
		$itemid 		= intval($_GET['itemid']);
		$itemid 		= intval($_GET['itemid']);
		if($itemid){
			$where['id']	= $itemid;
		}		
		$where['tp_member_card_create.token']	= $this->token;
		$where['tp_member_card_create.wecha_id']=array('neq','');
        $parms=$_GET;
		if (IS_POST){
			if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
                $searchkey='%'.trim($_POST['searchkey']).'%';
                $parms['searchkey']=$_POST['searchkey'];
			}
		}
        else
        {
            if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
                $searchkey='%'.trim($_GET['searchkey']).'%';
			}
        }
        if($searchkey){       
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= $card_create_db->join('tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->count();
		$Page       = new Page($count,35,$parms);
		$show       = $Page->show();
		$list 		= $card_create_db->join('tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->order('tp_userinfo.getcardtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$members	= $list;
		if ($members){		
			$this->assign('members',$members);
			//
			$this->assign('page',$show);
		}
		$this->display();
	}

	public function member(){
		$card_create_db=M('Member_card_create');
		$where='cardid='.intval($_GET['id']).' AND token=\''.$this->token.'\' AND wecha_id!=\'\'';
		$thisMember=$card_create_db->where(array('wecha_id'=>$_GET['wecha_id']))->find();
		//
		$thisUser=M('Userinfo')->where(array('token'=>$thisMember['token'],'wecha_id'=>$thisMember['wecha_id']))->find();
		$this->assign('thisUser',$thisUser);

        $Dao = M();
        $coupons = $Dao->query("SELECT  b.title,a.* from tp_member_card_coupon_record a,tp_member_card_coupon b WHERE a.coupon_id=b.id and a.token='".$thisMember['token']."' and a.wecha_id='".$thisMember['wecha_id']."'");
        if($coupons){
            $this->assign('coupons', $coupons);
            $this->display();
        } 
		$members[0]=$thisMember;
		$members[0]['truename']=$thisUser['truename'];
		$members[0]['carno']=$thisUser['carno'];
		$members[0]['wechaname']=$thisUser['wechaname'];
		$members[0]['qq']=$thisUser['qq'];
		$members[0]['tel']=$thisUser['tel'];
		$members[0]['getcardtime']=$thisUser['getcardtime'];
		$members[0]['expensetotal']=$thisUser['expensetotal'];
		$members[0]['total_score']=$thisUser['total_score'];
		$members[0]['balance']=$thisUser['balance'];
		$members[0]['uid']=$thisUser['id'];
		$this->assign('members',$members);
		//
		$record_db=M('Member_card_use_record');
		$pay_record=M('Member_card_pay_record');
		$where=array('wecha_id'=>$thisUser['wecha_id'],'token'=>$this->token);
		$count      = $record_db->where($where)->count();
		$count2      = $pay_record->where($where)->count();
		$Page       = new Page($count,15);
		$Page2       = new Page($count2,15);
		
		$list = $record_db->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		$rmb = $pay_record->where($where)->limit($Page2->firstRow.','.$Page2->listRows)->order('createtime DESC')->select();
		$show       = $Page->show();
		$show2       = $Page2->show();
		
		$this->assign('records',$list);
		$this->assign('rmb',$rmb);
		$this->assign('page',$show);
		$this->assign('page2',$show2);
		$this->display();
	}
	
	public function recharge(){
		$db = M('Userinfo');
		$uid = (int)$_GET['uid'];
		$cardid = $this->_get('cardid','intval');

		$uinfo 		= $db->where(array('token'=>$this->token,'id'=>$uid))->field('wechaname,wecha_id,truename,id,balance')->find();
		//$cardinfo   = M('Member_card_set')->where(array('token'=>$this->token,'cardid'=>$cardid))->find();
        $mycard   	= M('Member_card_create')->where(array('token'=>$this->token,'cardid'=>$cardid,'wecha_id'=>$this->wecha_id))->find();

		if(IS_POST){

			//if($db->create() === false) $this->error($db->getError());
			$id = (int)$_POST['uid'];
			if($db->where(array('token'=>$this->token,'id'=>$id))->setInc('balance',$_POST['price'])){
				$orderid = date('YmdHis',time()).mt_rand(1000,9999);
				M('Member_card_pay_record')->add(array('orderid' => $orderid , 'ordername' => '后台手动充值' , 'createtime' => time() , 'token' => $this->token , 'wecha_id' => $uinfo['wecha_id'] , 'price' => $_POST['price'] , 'type' => 1 , 'paid' => 1 , 'module' => 'Card' , 'paytime' => time() , 'paytype' => 'recharge'));

                /*模板消息*/
                $model  = new templateNews();
                $dataKey    = 'TM151125';
                $dataArr    = array(
                    'first'         => '您好，后台手会员卡充值成功。',
                    'accountType'      => '会员卡号',
                    'account'      => $mycard['number'],
                    'amount'      => $_POST['price'].'元',
                    'result'      => '充值成功',
                    'wecha_id'      => $uinfo['wecha_id'],
                    'remark'        => '会员充值如有疑问，请致电020-39099139联系我们。',
                    'url'      => U('Wap/Card/wallet',array('token'=>$this->token,'wecha_id'=>$uinfo['wecha_id'],'cardid'=>$cardid),true,false,true),
                );

                $model->sendTempMsg($dataKey,$dataArr);
				echo '充值成功';
				//$this->success('充值成功');

			}else{
				echo '充值失败，请稍后再试';
			}

		}else{

			$this->assign('cardid',$cardid);
			$this->assign('uinfo',$uinfo);
			$this->display();
		}
		
	}
	
	public function signrecords(){
		$card_create_db=M('Member_card_create');
		$where='cardid='.intval($_GET['id']).' AND token=\''.$this->token.'\' AND wecha_id!=\'\'';
		$thisMember=$card_create_db->where(array('id'=>intval($_GET['itemid'])))->find();
		//
		$thisUser=M('Userinfo')->where(array('token'=>$thisMember['token'],'wecha_id'=>$thisMember['wecha_id']))->find();
		$this->assign('thisUser',$thisUser);
		$members[0]=$thisMember;
		$members[0]['truename']=$thisUser['truename'];
		$members[0]['wechaname']=$thisUser['wechaname'];
		$members[0]['qq']=$thisUser['qq'];
		$members[0]['tel']=$thisUser['tel'];
		$members[0]['getcardtime']=$thisUser['getcardtime'];
		$members[0]['expensetotal']=$thisUser['expensetotal'];
		$members[0]['total_score']=$thisUser['total_score'];
		$this->assign('members',$members);
		//
		$record_db=M('Member_card_sign');
		$where=array('wecha_id'=>$thisUser['wecha_id'],'token'=>$this->token);
		$count      = $record_db->where($where)->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $record_db->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('sign_time desc')->select();
		$this->assign('records',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function memberExpense(){
		//score
		if (IS_POST){
			$arr=array();
			$arr['itemid']=intval($this->_post('itemid'));
			$arr['wecha_id']=$this->_post('wecha_id');
			$arr['expense']=intval($this->_post('money'));
			$arr['time']=time();
			$arr['token']=$this->token;
			$arr['cat']	 = intval($this->_post('cat'));
			$arr['staffid']=intval($this->_post('staffid'));
			$arr['usecount']=0;

			$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($this->thisCard['id'])))->find();
			$arr['score'] = intval($arr['expense']*$set_exchange['reward']);
			M('Member_card_use_record')->add($arr);
			$userArr=array();
			$thisUser=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$arr['wecha_id']))->find();
			$userArr['total_score']=$thisUser['total_score']+$arr['score'];
			$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
			M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$arr['wecha_id']))->save($userArr);
		}
		$this->success('操作成功');
	}
	public function member_del(){
		$card_create_db=M('Member_card_create');
		//
		$thisUser=M('Userinfo')->where(array('id'=>intval($_GET['itemid'])))->find();
		//
		$where=array('wecha_id'=>$thisUser['wecha_id'],'token'=>$this->token);
		M('Member_card_sign')->where($where)->delete();
		M('Member_card_use_record')->where($where)->delete();
		M('Userinfo')->where($where)->delete();
		$card_create_db->where($where)->save(array('wecha_id'=>''));
		$this->success('操作成功');
	}
	public function delete(){
		$tokenWhere=array('token'=>$this->token,'cardid'=>$_GET['id']);
		if (M('Member_card_create')->where($tokenWhere)->find()){
			$this->error('请先删除创建的卡号');
		}
		M('Member_card_set')->where(array('token'=>$this->token,'id'=>intval($_GET['id'])))->delete();
		$this->success('操作成功');
	}
	

	
	public function getuserinfo(){
		$wecha_id = $this->_get("id");

		$uinfo = M('Userinfo')->where(array('wecha_id'=>$wecha_id ,'token'=>$_SESSION['token']))->order('id DESC')->find();
		$this->assign('list',$uinfo);
	
		$this->display();	
	}

	
	
	
	
	//会员详情
	public function info(){
		$data=M('Member_card_info')->where(array('token'=>$_SESSION['token']))->find();
		if(IS_POST){
			//dump($_POST);EXIT;
			$_POST['token']=$_SESSION['token'];			
			if($data==false){				
				$this->all_insert('Member_card_info','/info');
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Member_card_info','/info');
			}
		}else{
			$this->assign('info',$data);
			$contact=M('Member_card_contact')->where(array('token'=>$_SESSION['token']))->order('sort desc')->select();
			$this->assign('contact',$contact);
			$this->display();
		}	
	}
	public function contact(){
		if(IS_POST){			
			$this->all_insert('Member_card_contact','/info');
		}else{
			$this->error('非法操作');	
		}
	}
	public function contact_edit(){
		if(IS_POST){			
			$this->all_save('Member_card_contact','/info');
		}else{
			$this->error('非法操作');			
		}		
	}
	
//导出积分，线下消费记录
	public function exportCardUseRecord(){
		$wecha_id = $this->_get('wecha_id');
		header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=cardUseRecord.xls");
		
		$arr = array(
			array('en'=>'itemid','cn'=>'消费产品ID'),
			array('en'=>'number','cn'=>'会员卡号'),
			array('en'=>'staffid','cn'=>'店员ID'),
			array('en'=>'cat','cn'=>'类型'),
			array('en'=>'expense','cn'=>'消费金额'),
			array('en'=>'score','cn'=>'积分数'),
			array('en'=>'usecount','cn'=>'使用次数'),
			array('en'=>'time','cn'=>'时间'),
			array('en'=>'notes','cn'=>'备注')
		);
		
		$i = 0;
		$fieldCount = count($arr);
		$s = 0;
		//thead
		foreach ($arr as $f){
			if ($s<$fieldCount-1){
				echo iconv('utf-8','gbk',$f['cn'])."\t";
			}else {
				echo iconv('utf-8','gbk',$f['cn'])."\n";
			}
			$s++;
		}
		//data
		$db = M('Member_card_use_record');
		if($this->_get('type') != 'all'){
			$where = array('token'=>$this->token,'wecha_id'=>$wecha_id);
		}else{
			$where = array('token'=>$this->token);
		}
		
		$sns = $db->where($where)->order('time DESC')->select();

		if($sns){
			foreach ($sns as $sn){
				$number 	= M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$sn['wecha_id']))->getField('number');
				$sn['number']	= $number;				
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $sn[$field['en']];
					switch($field['en']){		
						case 'time':
							if ($fieldValue){
								$fieldValue=iconv('utf-8','gbk',date('Y年m月d日 H时i分s秒',$fieldValue));
								
							}else {
								$fieldValue='';
							}
							break;
						case 'cat':
							switch($fieldValue){
								case 2:
									$fieldValue = iconv('utf-8','gbk','兑换');
									break;
								case 3:
									$fieldValue = iconv('utf-8','gbk','赠送');
									break;
								case 98:
									$fieldValue = iconv('utf-8','gbk','分享');
									break;
								default:
									$fieldValue = iconv('utf-8','gbk','消费');
									
							}
						default:
							break;
					}
					
					if ($j<$fieldCount-1){
						echo $fieldValue."\t";
					}else {
						echo $fieldValue."\n";
					}
					$j++;
				}
				$i++;
			}
		
		}
		exit();
	
	}
	
//导出人民币在线消费记录

	public function exportrmb(){
		$wecha_id = $this->_get('wecha_id');
		header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=cardPayRecord.xls");
		
		$arr = array(
			array('en'=>'orderid','cn'=>'订单号'),
			array('en'=>'ordername','cn'=>'订单名称'),
			array('en'=>'transactionid','cn'=>'第三方订单号'),
			array('en'=>'paytype','cn'=>'支付类型'),
			array('en'=>'createtime','cn'=>'订单创建时间'),
			array('en'=>'price','cn'=>'金额'),
			array('en'=>'paytime','cn'=>'支付时间'),
			array('en'=>'paid','cn'=>'支付状态'),
			array('en'=>'number','cn'=>'会员卡号'),
			array('en'=>'module','cn'=>'来源模块'),
			array('en'=>'type','cn'=>'类型')
		);
		
		$i = 0;
		$fieldCount = count($arr);
		$s = 0;
		//thead
		foreach ($arr as $f){
			if ($s<$fieldCount-1){
				echo iconv('utf-8','gbk',$f['cn'])."\t";
			}else {
				echo iconv('utf-8','gbk',$f['cn'])."\n";
			}
			$s++;
		}
		//data
		$db = M('Member_card_pay_record');
		if($this->_get('type') != 'all'){
			$where = array('token'=>$this->token,'wecha_id'=>$wecha_id);
		}else{
			$where = array('token'=>$this->token);
		}
		
		$sns = $db->where($where)->order('id DESC')->select();
				
		if($sns){
			foreach ($sns as $sn){
				$number 	= M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$sn['wecha_id']))->getField('number');
				$sn['number']	= $number;
				$j = 0;
				foreach ($arr as $field){
					$fieldValue = $sn[$field['en']];
					switch($field['en']){
						case 'orderid':
							$fieldValue = iconv('utf-8','gbk',"单号".$fieldValue);
							break;
							
						case 'transactionid':
							if($fieldValue != ''){
								$fieldValue = iconv('utf-8','gbk',"单号".$fieldValue);
							}
							
							break;
							
						case 'createtime':
							if ($fieldValue){
								$fieldValue=iconv('utf-8','gbk',date('Y年m月d日 H时i分s秒',$fieldValue));
								
							}else {
								$fieldValue='';
							}
							break;
						case 'paytime':
							if ($fieldValue){
								$fieldValue=iconv('utf-8','gbk',date('Y年m月d日 H时i分s秒',$fieldValue));
								
							}else {
								$fieldValue='';
							}
							break;
							
						case 'type':
							switch($fieldValue){
								case 1:
									$fieldValue = iconv('utf-8','gbk','充值');
									break;
								case 0:
									$fieldValue = iconv('utf-8','gbk','消费');
									break;	
							}
							break;
							
						case 'paid':
							if($fieldValue == 1){
								$fieldValue = iconv('utf-8','gbk','交易成功');
							}else{
								$fieldValue = iconv('utf-8','gbk','未付款');
							}
							break;
						case 'ordername':
							$fieldValue = iconv('utf-8','gbk',$fieldValue);
							break;
			
						default:
							break;
					}
					
					if ($j<$fieldCount-1){
						echo $fieldValue."\t";
					}else {
						echo $fieldValue."\n";
					}
					$j++;
				}
				$i++;
			}
		
		}
		exit();
		
		
		
	}
	
	
//导出会员卡
	
	public function exportCard(){
		header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=card.xls");
	
		$id = $this->_get('id','intval');
		$token = $this->token;

		$arr = array(
			array('en'=>'number','cn'=>'卡号'),
			array('en'=>'truename','cn'=>'姓名'),
			array('en'=>'tel','cn'=>'手机号'),
			array('en'=>'total_score','cn'=>'积分'),
			array('en'=>'sex','cn'=>'性别 ( 男； 女； 其他）'),
			array('en'=>'bornyear','cn'=>'出生年'),
			array('en'=>'bornmonth','cn'=>'出生月'),
			array('en'=>'bornday','cn'=>'出生日'),
			array('en'=>'portrait','cn'=>'头像地址'),
			array('en'=>'qq','cn'=>'QQ号'),
			array('en'=>'getcardtime','cn'=>'领卡时间'),
			array('en'=>'expensetotal','cn'=>'消费总额'),
			array('en'=>'balance','cn'=>'余额 单位：元'),
			array('en'=>'wecha_id','cn'=>'微信OpenID')
		);

		//$i = 0;
		$fieldCount = count($arr);
		$s = 0;
		//thead
		foreach ($arr as $f){
			if ($s<$fieldCount-1){
				echo iconv('utf-8','gbk',$f['cn'])."\t";
			}else {
				echo iconv('utf-8','gbk',$f['cn'])."\n";
				
			}
			$s++;
		}
		$create = M('Member_card_create');
		$userinfo = M('Userinfo');
		
		$createInfo = $create->field('number,token,wecha_id')->where("token = '$token' AND wecha_id != '' AND cardid = $id")->select();
		
		if($createInfo){
			foreach($createInfo as $k=>$v){
				
				$where['token'] = $v['token'];
				$where['wecha_id'] = $v['wecha_id'];
				
				$info = $userinfo->where($where)->field('truename,wechaname,tel,total_score,sex,bornyear,bornmonth,bornday,portrait,qq,getcardtime,expensetotal,balance')->select();
				$i=0;
				foreach($info as $key=>$val){
					$val = array_merge($val,$v);
					$j=0;


					foreach ($arr as $field){
						$fieldValue = $val[$field['en']];
						
						switch($field['en']){
						
							case 'truename':
								$fieldValue = iconv('utf-8','gbk',$fieldValue);
								break;
							case 'wechaname':
								$fieldValue = iconv('utf-8','gbk',$fieldValue);
								break;
							case 'sex':
								if($fieldValue == 1){
									$fieldValue = iconv('utf-8','gbk','男');
								}elseif($fieldValue == 2){
									$fieldValue = iconv('utf-8','gbk','女');
								}else{
									$fieldValue = iconv('utf-8','gbk','其他');
								}
								break;
							case 'getcardtime':
								$fieldValue = date('Y-m-d',$fieldValue);
								break;

						}
						
						
						if ($j<$fieldCount-1){
							echo $fieldValue."\t";
						}else {
							echo $fieldValue."\n";
						}
						$j++;
						
					}
					//$i++;
				}
			
			}
		
		}
	
	}
	
//删除记录
	public function payRecord_del(){
		$id = $this->_get('pid');
		$token = $this->token;
		$pay_record = M('Member_card_pay_record');
		if($pay_record->where(array('token'=>$token,'id'=>(int)$id))->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}

	}
	
	public function focus(){
		$focusDb = M('Member_card_focus');
		$list = $focusDb->where(array('token'=>$this->token))->select();
		
		$this->assign('info',$list);
		$this->display();
	}
	
	public function focusEdit(){
	
		$where['id']=$this->_get('fid','intval');
		$where['token']=$this->token;
		
		$data = M('Member_card_focus')->where($where)->find();
		
		$this->assign('info',$data);
		$this->display();
	}
	
	public function upsave(){
		$where['id'] = (int)$_POST['fid'];
		$where['token'] = $this->token;
		if(M('Member_card_focus')->where($where)->save($_POST)){
			$this->success('保存成功');
		}else{
			$this->error('保存失败');
		}
	}
	
	public function focusDel(){
		$where['token'] = $this->token;
		$where['id'] = (int)$_GET['fid'];
		if(M('Member_card_focus')->where($where)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
		
	}
	
	public function focusAdd(){
		if(IS_POST){
			$_POST['token']=$this->token;						
			$this->all_insert('Member_card_focus','/focus');
			}else{
				$this->display();
		}
	}
	
	public function custom(){
		$conf = M('Member_card_custom')->where(array('token'=>$this->token))->find();
		$conf = array_map('intval',$conf);
		
		$this->assign('conf',$conf);
		$this->display();
	}
	
	

	public function center(){
		$where 		= array('token'=>$this->token,'wecha_id'=>array('neq',''));
		$searchkey 	='';
        if (IS_POST){
			if (isset($_POST['number'])&&trim($_POST['number'])){
                $searchkey='%'.trim($_POST['number']).'%';
			}
		}
        else
        {
            if (isset($_GET['number'])&&trim($_GET['number'])){
                $searchkey='%'.trim($_GET['number']).'%';
			}
        }
        if($searchkey){       
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= M('Userinfo')->where($where)->count();
		$Page       = new Page($count,15);
		$users 	= M('Userinfo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('users',$users);
		$this->assign('page',$Page->show());
		$this->display();
	}

    public function withdraw(){
		$where 		= array('token'=>$this->token,'wecha_id'=>array('neq',''));
		$searchkey 	='';
        if (IS_POST){
			if (isset($_POST['number'])&&trim($_POST['number'])){
                $searchkey='%'.trim($_POST['number']).'%';
			}
		}
        else
        {
            if (isset($_GET['number'])&&trim($_GET['number'])){
                $searchkey='%'.trim($_GET['number']).'%';
			}
        }
        if($searchkey){       
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= M('Yangchebao_withdraw')->where($where)->count();
		$Page       = new Page($count,15);
		$withdraws 	= M('Yangchebao_withdraw')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('withdraws',$withdraws);
		$this->assign('page',$Page->show());
		$this->display();
	}

}
?>