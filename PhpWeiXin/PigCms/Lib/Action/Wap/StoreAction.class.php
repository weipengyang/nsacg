<?php
class StoreAction extends WapAction{
	//public $token;
	//public $wecha_id = '';
	public $product_model;
	public $product_cat_model;
	public $session_cart_name;
	public $_cid = 0;
	public $_set;
	public $_isgroup = 0;
	
	public $mainCompany = null;
	
	public $_twid = '';
	
	public $mytwid = '';
	
	private $randstr = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
    #region 初始化
	public function _initialize() 
	{
		parent::_initialize();
		
		$tpl = $this->wxuser;
		
		$tpl['color_id'] = intval($tpl['color_id']);
		
		$this->tpl = $tpl;
        if(!in_array(ACTION_NAME, array('register', 'login','userinfo'))){
        if(!$this->wecha_id){
            $this->redirect(U('Store/register', array('token' => $this->token)));
           
        }
        else{
                $user = M('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
                if(empty($user)||empty($user['carno'])){
                    $this->redirect(U('Store/userinfo', array('token' => $this->token)));

                }
        }
        }else{
            $user = M('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
            if($user&&$user['carno']){
                $this->redirect(U('Store/cats', array('token' => $this->token,'wecha_id'=>$this->wecha_id)));
            }   
           
        }
		
		$this->_cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : session("session_company_{$this->token}");
		
		$this->product_model = M('Product');
		$this->product_cat_model = M('Product_cat');
		$this->mainCompany = M('Company')->where("`token`='{$this->token}' AND `isbranch`=0")->find();
		
		if (C('zhongshuai')) {
			$cid = $this->mainCompany['id'];
			$set = M("Product_setting")->where(array('token' => $this->token, 'cid' => $this->mainCompany['id']))->find();
			$this->_isgroup = isset($set['isgroup']) ? intval($set['isgroup']) : 0;
		}
		$this->_cid || $this->_cid = $this->mainCompany['id'];
		$this->assign('cid', $this->_cid);
		$this->session_cart_name = "session_cart_products_{$this->token}_{$this->_cid}";//'session_cart_products_' . $this->token;
// 		$twitter_set = null;
		if ($this->_cid) {
			$this->_set = M("Product_setting")->where(array('token' => $this->token, 'cid' => $this->_cid))->find();
			$this->assign('productSet', $this->_set);
			$cid = $this->_isgroup ? $this->mainCompany['id'] : $this->_cid;
			$cats = $this->product_cat_model->where(array('token' => $this->token, 'cid' => $cid, 'parentid' => 0))->order("sort ASC, id DESC")->select();
			$this->assign('cats', $cats);
			
		}
		$this->_twid = isset($_REQUEST['twid']) ? $_REQUEST['twid'] : '';//来自推广人的推广标示
		$this->mytwid = session('twid');//我自己的推广标示
		$login = session("login");
// 		$twitter_set = M("Twitter_set")->where(array('token' => $this->token, 'cid' => $this->_cid))->find();
		if (empty($this->wecha_id) && empty($this->mytwid) && empty($login) && !in_array(ACTION_NAME, array('register', 'login'))) {
			$callbackurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
			session('callbackurl', $callbackurl);
			session("login", 1);
			$this->redirect(U('Store/login', array('token' => $this->token, 'cid' => $this->_cid, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
		}
		
		$istwittersave = session('twitter_save');
		if (empty($istwittersave) && $this->_cid) {
			$this->savelog(1, $this->_twid, $this->token, $this->_cid);
			session('twitter_save', 1);
		}
		
		if (empty($this->wecha_id) && $this->mytwid) {
			$fansInfo = M('Userinfo')->where(array('token' => $this->token, 'twid' => $this->mytwid))->find();
			$this->fans = $fansInfo;
			$this->assign('fans', $fansInfo);
		}
		 
		if ($this->fans && empty($this->fans['twid'])) {
			$twid = $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->fans['id'];
			D('Userinfo')->where(array('id' => $this->fans['id']))->save(array('twid' => $twid));
			$this->fans['twid'] = $twid;
			$this->assign('fans', $fansInfo);
		} elseif (empty($this->fans) && $this->wecha_id) { //TODO 没有用户信息时候的处理
			$tmpuid = D('Userinfo')->add(array('token' => $this->token, 'wecha_id' => $this->wecha_id));
			if ($tmpuid) {
				$twid = $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $tmpuid;
				D('Userinfo')->where(array('id' => $tmpuid))->save(array('twid' => $twid));
			}  
			$this->fans = array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $twid);
		}
		$this->mytwid = $this->fans['twid'];
		
		$this->wecha_id || $this->wecha_id = $this->mytwid;
		
		$this->assign('staticFilePath', str_replace('./', '/', THEME_PATH . 'common/css/store'));
		//购物车
		$calCartInfo = $this->calCartInfo();
		$this->assign('totalProductCount', $calCartInfo[0]);
		$this->assign('totalProductFee', $calCartInfo[1]);
		$this->assign('mytwid', $this->mytwid);
		$this->assign('twid', $this->_twid);
	}
    #endregion

    #region 用户注册
	/**
	 * 用户注册
	 */
	public function userinfo(){
        if (IS_POST) {
   		    $tel= isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
            $carno=isset($_POST['carno']) ? htmlspecialchars(strtoupper($_POST['carno'])) : '';
            //如果原来有用户就迁移数据
            $user = M('Userinfo','','acg')->where(array('token'=>'jifbjv1429543374','tel'=>$tel))->find();
            $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
			$password2 = isset($_POST['password2']) ? htmlspecialchars($_POST['password2']) : '';
			$user['truename'] = isset($_POST['truename']) ? htmlspecialchars($_POST['truename']) : '';
			$user['sex'] = isset($_POST['sex']) ? htmlspecialchars($_POST['sex']) : '';
            $user['tel']=$tel;
            $user['carno']=str_replace('粵','粤',$carno);
            $user['carno']=str_replace(' ','',$user['carno']);
            M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->delete();
            $count=M('member_card_car')->where(array('token' => $this->token,'carno'=>$user['carno']))->count();
            if($count>=2)
            {
                echo $user['carno'].'车牌已经绑定两个微信，每个车牌最多绑定两个微信';exit;
            }
			if (empty($user['tel'])) {
				echo "电话号码不能为空!"; exit;
			}
			if (empty($password)) {
				echo "密码不能为空!";exit;
			}
			if ($password != $password2) {
				$this->error("密码不一致");
			}
            //查找是否是爱车港平台会员
            $oldcard = M('Member_card_create','','acg')->where(array('token'=>'jifbjv1429543374','wecha_id'=>$user['wecha_id']))->find();
            if($user&&$oldcard)//从爱车港转卡过爱养车
            {
                if($user['carno']){
                    $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno']))->find();
                    if(empty($carinfo))
                    {
                        M('member_card_car')->add(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno']));
                    }
                }
                if($user['carno1']){
                    $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno1']))->find();
                    if(empty($carinfo))
                    {
                        M('member_card_car')->add(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno1']));
                    }
                }
                if($user['carno2']){
                    $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno2']))->find();
                    if(empty($carinfo))
                    {
                        M('member_card_car')->add(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno2']));
                    }
                }
                //会员优惠券转换
                $coupon_record=M('member_card_coupon_record','','acg')->where(array('token'=>'jifbjv1429543374','wecha_id'=>$user['wecha_id']))->select();
                foreach($coupon_record as $key=>$r)
                {   
                    $coupon_record[$key]['id']=null;
                    unset($coupon_record[$key]['id']);
                    $coupon_record[$key]['token']=$this->token;
                    $coupon_record[$key]['wecha_id']=$this->wecha_id;
                }
                M('member_card_coupon_record')->addAll($coupon_record);
                //会员支付记录转换
                $pay_record=M('member_card_pay_record','','acg')->where(array('token'=>'jifbjv1429543374','wecha_id'=>$user['wecha_id']))->select();
                foreach($pay_record as $key=>$r)
                {
                    $pay_record[$key]['id']=null;
                    unset($pay_record[$key]['id']);
                    $pay_record[$key]['token']=$this->token;
                    $pay_record[$key]['wecha_id']=$this->wecha_id;

                }
                M('member_card_pay_record')->addall($pay_record);
                //会员卡使用记录转换
                $use_record=M('member_card_use_record','','acg')->where(array('token'=>'jifbjv1429543374','wecha_id'=>$user['wecha_id']))->select();
                foreach($use_record as $key=>$r)
                {
                    $use_record[$key]['id']=null;
                    unset($use_record[$key]['id']);
                    $use_record[$key]['token']=$this->token;
                    $use_record[$key]['wecha_id']=$this->wecha_id;
                }
                M('member_card_use_record')->addall($use_record);
                //积分签到转换
                $sign_record=M('member_card_sign','','acg')->where(array('token'=>'jifbjv1429543374','wecha_id'=>$user['wecha_id']))->select();
                foreach($sign_record as $key=>$r)
                {
                    $sign_record[$key]['id']=null;
                    unset($sign_record[$key]['id']);
                    $sign_record[$key]['token']=$this->token;
                    $sign_record[$key]['wecha_id']=$this->wecha_id;

                }
                M('member_card_sign')->addall($sign_record);
                //获取新卡
                $card=M('Member_card_create','','ayc')->field('cardid,id,number')->where("token='".$this->_get('token')."' and cardid=".$oldcard['cardid']." and wecha_id = ''")->order('id ASC')->find();

            }else{
                //新用户获取新卡，取积分卡
                $card=M('Member_card_create','','ayc')->field('cardid,id,number')->where("token='".$this->_get('token')."' and cardid=5 and wecha_id = ''")->order('id ASC')->find();
            }
            M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$this->wecha_id));

            if(empty($user['id'])){
                //新会员绑定车辆
                $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno']))->find();
                if(empty($carinfo))
                {
                    M('member_card_car')->add(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$user['carno']));
                }
            }else{
                unset($user['id']);
            }
            if(!$user['getcardtime'])
                $user['getcardtime']=time();
			
            $user['paypass']=md5($password);
            $user['token']=$this->token;
            $user['wecha_id']=$this->wecha_id;
            $twid = $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)}.$this->randstr{rand(0, 51)}.mt_rand(1000,9999);
			$this->savelog(2, $this->_twid, $this->token, $this->_cid);
			session('twid', $twid);
            $user['twid']=$twid;
            $userInfo = M('Userinfo','','ayc')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
             if($userInfo){
                 M('Userinfo','','ayc')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->save($user);
             }
             else{
                  $fansInfo=S('fans_'.$this->token.'_'.$this->wecha_id);
                  $user['wechaname']=$fansInfo['wechaname'];
                  $user['portrait']=$fansInfo['portrait'];
                  M("Userinfo",'','ayc')->add($user);
             }
            //修改笛佛系统中的会员资料
            $car=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$user['carno']))->find();
            $lb='4星客户';
            $czinfo['等级']='★★★★';
            if($card['cardid']==5){
                $lb='1星客户';
                $czinfo['等级']='★';
            }
            if(!empty($car)){
                $item['车主']=$card['number'];
                $item['联系人']=$user['truename'];
                $item['联系电话']=$user['tel'];
                $item['手机号码']=$user['tel'];
                $item['客户类别']=$lb;
                M('车辆档案','dbo.','difo')->where(array('客户ID'=>$car['客户ID']))->save($item);
                M('维修','dbo.','difo')->where(array('客户ID'=>$car['客户ID']))->save($item);
                $czinfo['名称']=$card['number'];
                $czinfo['会员']=1;
                $czinfo['会员编号']=$card['number'];
                $czinfo['入会日期']=date('Y-m-d',time());
                $czinfo['联系人']=$user['truename'];
                $czinfo['联系电话']=$user['tel'];
                $czinfo['手机号码']=$user['tel'];
                $czinfo['类别']=$lb;
                M('往来单位','dbo.','difo')->where(array('ID'=>$car['客户ID']))->save($czinfo);
            }
            else//新用户
            {
                $czinfo['名称']=$card['number'];
                $czinfo['客户']=1;
                $czinfo['ID']=$this->getcode(18,0,0);
                $czinfo['会员']=1;
                $czinfo['会员编号']=$card['number'];
                $czinfo['入会日期']=date('Y-m-d',time());
                $czinfo['联系人']=$user['truename'];
                $czinfo['联系电话']=$user['tel'];
                $czinfo['手机号码']=$user['tel'];
                $czinfo['类别']=$lb;
                M('往来单位','dbo.','difo')->add($czinfo);
                $czinfo=M('往来单位','dbo.','difo')->where(array('名称'=>$card['number']))->find();
                $item['车主']=$card['number'];
                $item['车牌号码']=$user['carno'];
                $item['客户ID']=$czinfo['ID'];
                $item['手机号码']=$user['tel'];
                $item['联系人']=$user['truename'];
                $item['联系电话']=$user['tel'];
                $item['客户类别']=$lb;
                M('车辆档案','dbo.','difo')->add($item);
    
            }
            if(!$oldcard){
                $uinfo  = M('Userinfo','','ayc')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
                $now 	= time();
                $gwhere = array('token'=>$this->token,'cardid'=>$card['cardid'],'is_open'=>'1','start'=>array('lt',$now),'end'=>array('gt',$now));
                $gifts 	= M('Member_card_gifts')->where($gwhere)->select();
                foreach($gifts as $key=>$value){
                    if($value['type'] == "1"){
                        //赠积分
                        $arr=array();
                        $arr['itemid']	= 0;
                        $arr['token']	= $this->token;
                        $arr['wecha_id']= $this->wecha_id;
                        $arr['expense']	= 0;
                        $arr['time']	= $now;
                        $arr['cat']		= 3;
                        $arr['staffid']	= 0;
                        $arr['notes']	= '开卡赠送积分';
                        $arr['score']	= $value['item_value'];
                        M('Member_card_use_record')->add($arr);
                        M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$uinfo['wecha_id']))->setInc('total_score',$arr['score']);
                    }else{
                        $data['token']		= $this->token;
                        $data['wecha_id']	= $uinfo['wecha_id'];
                        $data['coupon_id']	= $value['item_value'];
                        $data['is_use']		= '0';
                        $data['cardid']		= $card['cardid'];
                        $data['add_time']	= $now;   
                        $data['over_time']=strtotime(date('Y-m-d',time())."+30 day");
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
            }
            echo '注册成功'; exit;
			
		} else {
			$this->assign('metaTitle', '会员注册');
			$this->display(); 
		}

    }
    #endregion
    
    #region 修改密码
    function modifypass(){
        if(IS_POST){
            $update['wecha_id']=$this->wecha_id;
            $update['token']=$this->_get('token');
            $userinfo=M('Userinfo')->where($update)->find();
            if($userinfo['paypass']!=md5($this->_post('password'))){
                echo 2;exit ;
            }
            if($this->_post('password1') != ''){
				$data['paypass'] = md5($this->_post('password1'));
			}
            if(M('Userinfo')->where($update)->save($data)){
                echo 1;exit;
            }else{
                echo 0;exit;
            }
        }
		$this->display();
	}
    #endregion

    #region 生成随机字符串方法
    /**
     * 生成随机字符串方法
     * @param mixed $randLength 字符串长度
     * @param mixed $attatime  是否包含时间头
     * @param mixed $includenumber 是否包含数字
     * @return string
     */
    private function getcode($randLength=6,$attatime=1,$includenumber=0){
	if ($includenumber){
		$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
	}else {
		$chars='abcdefghijklmnopqrstuvwxyz';
	}
	$len=strlen($chars);
	$randStr='';
	for ($i=0;$i<$randLength;$i++){
		$randStr.=$chars[rand(0,$len-1)];
	}
	$tokenvalue=$randStr;
	if ($attatime){
		$tokenvalue=$randStr.time();
	}
	return $tokenvalue;
}
#endregion


private function sellbill($price,$name){
      $card=M('member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
      $user=M('往来单位','dbo.','difo')->where(array('名称'=>$card['number']))->find();
      $data['ID']=$this->getcode(18,0,1);;
      $data['单据编号']=$this->getcodenum('SS');
      $data['制单日期']=date('Y-m-d',time());
      $data['制单人']='系统自动';
      $data['客户名称']=$user['名称'];
      $data['客户ID']=$user['客户ID'];
      $data['发票类别']='';
      $data['发票号码']='';
      $data['运费']=0;
      $data['结算方式']='会员卡支付';
      $data['货运方式']='';
      $data['业务员']='系统';
      $data['整单折扣']=1;
      $data['收款期限']='';
      //$data['送货地址']=;
      $data['备注']=$name;
      $data['当前状态']='待审核';
      //$data['审核人']=;
      //$data['审核日期']=;
      $data['合计数量']=1;
      $data['实际货款']=$price;
      $data['实际税额']=0;
      $data['虚增货款']=0;
      $data['虚增税额']=0;
      $data['价税合计']=$price;
      $data['总金额']=$price;
      $data['已结算金额']=$price;
      $data['未结算金额']=0;
      //$data['引用ID']=;
      //$data['毛利']=;
      $data['单据类别']='销售出库';
      $data['应结金额']=$price;
      $data['现结金额']=$price;
      $data['挂账金额']=0;
      $data['优惠金额']=0;
      $data['取用预存']=0;
      $data['会员编号']=$user['名称'];
       $data['使用积分']=0;
      //$data['兑现备注']=;
      //$data['会员卡号']=;
      //$data['代币券结算']=;

      M('销售单','dbo.','difo')->add($data);


}

private function washcar($price,$carno,$type='AYC0002',$wxlb='蜡水洗车'){
    //if($this->wecha_id=='ohD3dviFloHSvcl9ieoXFibqPFJM')
    {
        $data=M('维修','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
        $data['流水号']=null;
        unset( $data['流水号']);
        unset($data['ROW_NUMBER']);

        $code=M('编号单','dbo.','difo')->where(array('类别'=>'WX','日期'=>date('Y-m-d', time())))->max('队列');
        $bianhao='WX-'.date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
        M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>'WX','日期'=>date('Y-m-d', time())));
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
        $data['车牌号码']=$carno;
        $data['送修人']=$carinfo['手机号码'];
        foreach($data as $key=>$value){
            $data[$key]=$carinfo[$key];
        }
        $data['接车人']='系统自动';
        $data['ID']=$this->getcode(10,0,1);
        $data['制单日期']=date('Y-m-d',time());
        $data['制单人']='系统录单';
        $data['保修类别']='保外';
        $data['单据类别']='快修单';
        $data['当前主修人']='';
        $data['结算客户']=$carinfo['车主'];;
        $data['结算客户ID']=$carinfo['客户ID'];
        $data['当前状态']='结算';
        $data['维修状态']='结算';
        $data['进厂时间']=date('Y-m-d',time());
        $data['结算日期']=date('Y-m-d',time());
        $data['下次保养']=null;
        $data['维修类别']=$wxlb;
        $data['报价金额']=$price;
        $data['应收金额']=$price;
        $data['业务编号']=$bianhao;
        M('维修','dbo.','difo')->add($data);
        $row=array();
        $row['ID']=$data['ID'];
        if($price==0)
        {
            $row['项目编号']='AYC0001';
            $row['项目名称']='会员券消费';
            
        }else{
            $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>$type))->find();
            $row['项目编号']=$xm['项目编号'];
            $row['项目名称']=$xm['项目名称'];
        }
        $row['维修工艺']='';
        $row['结算方式']='客付';
        $row['工时']=1;
        $row['单价']=$price;
        $row['金额']=$price;
        $row['折扣']=1;
        $row['提成工时']=1;
        $row['提成金额']=0;
        $row['开工时间']=date('Y-m-d H:i',time());
        $row['完工时间']=date('Y-m-d H:i',time());
        $row['是否同意']=1;
        $row['已维修']='0小时'; 
        M('维修项目','dbo.','difo')->add($row);
    }
} 
public function check(){
        $card=M('member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        $user=M('往来单位','dbo.','difo')->where(array('名称'=>$card['number']))->find();
        $wxlist=M('维修','dbo.','difo')->where(array('制单人'=>array('neq','系统录单'),'客户ID'=>$user['ID'],'当前状态'=>'结算','维修类别'=>array('neq','蜡水洗车')))->order('流水号 desc')->select();
        foreach($wxlist as $key=>$value)
        {
            $items=M('维修项目','dbo.','difo')->where(array('ID'=>$value['ID']))->select();
            $peijian=M('维修配件','dbo.','difo')->where(array('ID'=>$value['ID'],'仅内部核算成本'=>0))->select();
            $wxlist[$key]['items']=$items;
            $wxlist[$key]['peijian']=$peijian;
        }
        $xslist=M('销售单','dbo.','difo')->where(array('制单人'=>array('neq','系统录单'),'客户ID'=>$user['ID'],'当前状态'=>'待审核'))->order('流水号 desc')->select();
        foreach($xslist as $key=>$value)
        {
            $xsmx=M('销售明细','dbo.','difo')->where(array('ID'=>$value['ID']))->select();
            $xslist[$key]['xsmx']=$xsmx;
        }
        $bxlist=M('车辆保险','dbo.','difo')->where(array('客户ID'=>$user['ID'],'当前状态'=>'审核'))->order('流水号 desc')->select();
        $dblist=M('车辆代办','dbo.','difo')->where(array('客户ID'=>$user['ID'],'当前状态'=>'审核'))->order('流水号 desc')->select();

        $this->assign('wxlist',$wxlist);
        $this->assign('xslist',$xslist);
        $this->assign('bxlist',$bxlist);
        $this->assign('dblist',$dblist);
        $this->display();
    }
  public function weixiu()
    {
       if(IS_POST)
       {
           $id=$_POST['id'];
           M('维修','dbo.','difo')->where(array('流水号'=>$id))->save(array('确认维修'=>'是'));
           echo '确认成功';
           exit();
        }
        $card=M('member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        $user=M('往来单位','dbo.','difo')->where(array('名称'=>$card['number']))->find();
        $wxlist=M('维修','dbo.','difo')->where(array('客户ID'=>$user['ID'],'维修类别'=>array('neq','蜡水洗车'),'当前状态'=>array('not in',array('结束','取消'))))->order('流水号 desc')->select();
        foreach($wxlist as $key=>$value)
        {
            $items=M('维修项目','dbo.','difo')->where(array('ID'=>$value['ID']))->select();
            $peijian=M('维修配件','dbo.','difo')->where(array('ID'=>$value['ID'],'仅内部核算成本'=>0))->select();
            $wxlist[$key]['items']=$items;
            $wxlist[$key]['peijian']=$peijian;
        }
        
        $carlist=M('member_card_car')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
        $this->assign('carlist',$carlist);
        $this->assign('wxlist',$wxlist);
        $this->display();
    }

   public function pay(){
     if(IS_POST){
         $itemid=$_POST['id'];
         $price=doubleval($_POST['price']);
         $type=intval($_POST['type']);
         $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
         if (md5($this->_post('password'))!=$userinfo['paypass'])
         {
             echo  '支付密码不正确';
             exit;
         }
         if ($price>$userinfo['balance'])
         {
             echo  '会员卡余额不足，请先充值';
             exit;
         }
         if($type==1)
         {
            $wx=M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->find();
            $this->genbill($price,$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);
            M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save(array('当前状态'=>'结束'));
            $this->consumerecord($price,'汽车维修支付',$userinfo);
            $data['出厂时间']=date('Y-m-d H:i',time());
            $data['实际完工']=date('Y-m-d H:i',time());
            $data['结算日期']=date('Y-m-d',time());
            $data['结束日期']=date('Y-m-d',time());
            $data['挂账金额']=0;
            $data['现收金额']=$price;
            $data['标志']='已结算';
            M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
            echo '结算成功';
            exit;

         }elseif($type==2){
             $xs=M('销售单','dbo.','difo')->where(array('流水号'=>$itemid))->find();

             $this->genbill($price,$xs['客户名称'],'销售出库收款('.$xs['单据编号'].')',$xs['客户ID']);
             M('销售单','dbo.','difo')->where(array('流水号'=>$itemid))->save(array('当前状态'=>'已审核'));
             $this->consumerecord($price,'汽车商品支付',$userinfo);
             echo '结算成功';
             exit;
         }
         elseif($type==3){
             $bx=M('车辆保险','dbo.','difo')->where(array('流水号'=>$itemid))->find();
             $this->genbill($price,$bx['车主'],'保险收款('.$bx['业务编号'].')',$bx['客户ID']);
             $data['挂账金额']=0;
             $data['现收金额']=$price;
             $data['结束日期']=date('Y-m-d',time());
             $data['审核日期']=date('Y-m-d',time());
             $data['缴费日期']=date('Y-m-d',time());
             $data['审核人']='系统自动';
             $data['当前状态']='结束';
             M('车辆保险','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
             $this->consumerecord($price,'汽车保险支付',$userinfo);
             echo '结算成功';
             exit;
         }
         else{
             $db=M('车辆代办','dbo.','difo')->where(array('流水号'=>$itemid))->find();
             $this->genbill($price,$db['车主'],'代办收款('.$db['业务编号'].')',$db['客户ID']);
             //$this->genbill($price,$db['车主'],'代办收款('.$db['业务编号'].')',$db['客户ID']);
             $data['挂账金额']=0;
             $data['现收金额']=$price;
             $data['结束日期']=date('Y-m-d',time());
             $data['审核日期']=date('Y-m-d',time());
             $data['审核人']='系统自动';
             $data['当前状态']='结束';

             M('车辆代办','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
             $this->consumerecord($price,"代办".$db['代办类别']."支付",$userinfo);
             echo '结算成功';
             exit;
         }

     }
     $this->display();
   }

   private function genbill($price,$chezhu,$zhaiyao,$daiwen){

       $bianhao=$this->getcodenum("BI");
       $data['单据编号']=$bianhao;
       $data['ID']=$this->getcode(10,0,1);
       $data['制单日期']=date('Y-m-d',time());
       $data['制单人']='系统自动';
       $data['单位名称']=$chezhu;
       $data['账款类别']='收款单';
       $data['开户银行']='';
       $data['银行账号']='';
       $data['本次应付']=0;
       $data['本次应收']=0;
       $data['整单折扣']=1;
       $data['实付金额']=0;
       $data['实收金额']=$price;
       $data['折扣金额']=0;
       $data['结算方式']='会员卡支付';
       $data['结算账户']='会员账户';
       $data['支票号']=0;
       $data['凭证号']=0;
       $data['摘要']=$zhaiyao;
       $data['收支项目']='维修收款';
       $data['当前状态']='待审核';
       $data['发票类别']='';
       $data['发票号']='';
       $data['单位编号']=$daiwen;
       $data['取用预付']=0;
       $data['取用预收']=0;
       $data['本次冲账']=$price;
       $data['单据类别']='应收款';
       $data['取用预存']=0;
       M('日常收支','dbo.','difo')->add($data);
   }
   private function getcodenum($type)
   {
       $code=M('编号单','dbo.','difo')->where(array('类别'=>"$type",'日期'=>date('Y-m-d', time())))->max('队列');
       $bianhao="$type-".date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
       M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>"$type",'日期'=>date('Y-m-d', time())));
       return $bianhao;
   }
   private function consumerecord($balance,$ordername,$userinfo){
   
       $single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
       $record['orderid'] = $single_orderid;
       $record['ordername'] = $ordername;
       $record['paytype'] = 'CardPay';
       $record['createtime'] = time();
       $record['paytime'] = time();
       $record['paid'] = 1; 
       $record['price'] =$balance;
       $record['token'] = $this->token;
       $record['wecha_id'] = $this->wecha_id;
       $record['type'] = 0;
       $record['notes'] =$ordername.'获取积分';
       M('Member_card_pay_record')->add($record);// 插入会员卡线下消费
       $create = M('Member_card_create');
       $cardid = $create->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->getField('cardid');
       $cardid = (int)$cardid;
       $member_card_create_db = M("Member_card_create");
       $userCard = $member_card_create_db->where(array("token" => $this->token, "wecha_id" => $this->wecha_id))->find();
       if ($userCard) {
           $member_card_set_db = M("Member_card_set");
           $thisCard = $member_card_set_db->where(array("id" => intval($userCard["cardid"])))->find();
           if ($thisCard) {
               $set_exchange = M("Member_card_exchange")->where(array("cardid" => intval($thisCard["id"])))->find();
               $arr["token"] = $this->token;
               $arr["wecha_id"] = $this->wecha_id;
               $arr["expense"] =$balance;
               $arr["time"] = time();
               $arr["cat"] = 99;
               $arr["staffid"] = 0;
               if(strpos($ordername, '保险') === 0){
                   $arr["score"] = 0;

               }else{
                   $arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
               }
               $thisUser = M('Userinfo')->where(array("token" => $thisCard["token"], "wecha_id" => $arr["wecha_id"]))->find();
               $userArr = array();
               $userArr["total_score"] = $thisUser["total_score"] + $arr["score"];//积分
               $userArr["expensetotal"] = $thisUser["expensetotal"] + $arr["expense"];//消费总额
               $userArr['balance'] = $userinfo['balance'] - $balance;//余额
               M('Userinfo')->where(array("token" => $this->token, "wecha_id" => $arr["wecha_id"]))->save($userArr);//更新用户信息
               M("Member_card_use_record")->add($arr);//插入会员卡使用表
           }
       }
   
   }
    public function consume(){
		$now 	= time();
		$config = M('Alipay_config')->where(array('token'=>$this->token))->find();
		$cardid = $this->_get('cardid','intval');
		$now=time();
		
		$thisCard=M('Member_card_set')->where(array('token'=>$this->token,'id'=>$cardid))->find();
		
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$cardid,'coupon_type'=>'2','is_use'=>'0','over_time'=>array('gt',$now));
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_id,coupon_type,add_time,is_use')->select();
		
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'cardid'=>$value['cardid'],'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_coupon')->where($cwhere)->field('info,pic,statdate,enddate,title,price,useinfo')->find();
			$cinfo['info'] 	= html_entity_decode($cinfo['info']);
			if($cinfo['enddate']>$now && $cinfo['statdate']<$now){
				$data[$key] = array_merge($value,$cinfo);
			}else{
				unset($data[$key]);
			}
		}

		if(IS_POST){	
			$rwhere 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'2','id'=>$this->_post('consume_id','intval'),'is_use'=>'0');
			$r_record 	= M('Member_card_coupon_record')->where($rwhere)->find();
			if (!$r_record){
				$r_record['coupon_id'] = 0;
			}
            $consumetype=array('discount1'=>'代办服务','discount2'=>'汽车美容','discount3'=>'汽车维修','discount4'=>'汽车商品','discount5'=>'汽车轮胎');
			$itemid		= $r_record['coupon_id'];
			$pay_type 	= intval($_POST['pay_type']);
			$consume_type 	= $this->_post('consume_type');
			$price =doubleval($_POST['price'])*$thisCard["$consume_type"];
            $price=round($price,2);
            $single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
            $notes=$consumetype["$consume_type"].'支付';
            if(doubleval($price)<0)
            {
                $this->error('请输入有效金额!');
				exit;
            }
            Log::write(json_encode($_POST));
			if(empty($_POST['price']) || empty($_POST['pay_type'])){
				$this->error('请填写完整的信息');
				exit;
			}

			if($pay_type == 1){
                $arr['itemid']		= $itemid;
                $arr['wecha_id']	= $this->wecha_id;
                $arr['expense']		= $price;
                $arr['time']		= $now;
                $arr['token']		= $this->token;
                $arr['cat']			= 1;
                $arr['staffid']		= 0;
                $arr['usecount']	= 1;
                
                $set_exchange = M('Member_card_exchange')->where(array('cardid'=>$cardid))->find();
                $arr['score']=intval($set_exchange['reward'])*$arr['expense'];
                
                $record['orderid'] 		= $single_orderid;
                $record['ordername'] 	=$notes;
                $record['paytype'] 		= 'CardPay';
                $record['createtime'] 	= time();
                $record['paid'] 		= 0;
                $record['price'] 		= $price;
                $record['token'] 		= $this->token;
                $record['wecha_id'] 	= $this->wecha_id;
                $record['type'] 		= 0;
                $record['discount'] 		= $thisCard["$consume_type"];
                M('Member_card_coupon')->where(array('id'=>$itemid))->setInc('usetime',1);
                M('Member_card_pay_record')->add($record);	
                M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1'));
                $this->redirect(U('CardPay/pay',array('from'=>'consume','token'=>$this->token,'wecha_id'=>$this->wecha_id,'discount'=>$thisCard["$consume_type"]*10,'price'=>$price,'single_orderid'=>$single_orderid,'orderName'=>$notes,'redirect'=>'Card/payReturn|itemid:'.$itemid.',usecount:'.$arr['usecount'].',score:'.$arr['score'].',type:coupon,cardid:'.$cardid)));
                exit;
			}elseif($pay_type == 3)
            {
                $_POST['wecha_id'] =$this->wecha_id;
		        $_POST['token'] = $this->token;
		        $_POST['createtime'] =$now;
		        $_POST['orderid'] =$single_orderid;
		        $_POST['ordername'] = $notes;
                $_POST['price']=$price;
                $_POST['paytype'] = 'WxPay';
                $_POST['type'] = 0;
                $_POST['discount']= $thisCard["$consume_type"];
		        if(M('Member_card_pay_record')->create($_POST)){
			        if(M('Member_card_pay_record')->add($_POST)){
				        $this->success('提交成功，正在跳转支付页面..',U('Alipay/pay',array('from'=>'consume','discount'=>$thisCard["$consume_type"]*10,'orderName'=>$_POST['ordername'],'single_orderid'=>$_POST['orderid'],'token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],'price'=>$price)));
                        exit;
			        }
		        }else{
			        $this->error('系统错误');
                    exit;
		        }
            } 
            else{				

				$staff_db	= M('Company_staff');
				$thisStaff	= $staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();

	    		if(empty($thisStaff)){
	    			$this->error('商家名称不存在');
					exit;
	    		}	
	    		
    			if (md5($this->_post('password')) == $thisStaff['password']){
					
    				$arr=array();
					$arr['itemid']		= $itemid;
					$arr['wecha_id']	= $this->wecha_id;
					$arr['expense']		= $price;
					$arr['time']		= $now;
					$arr['token']		= $this->token;
					$arr['cat']			= 0;
					$arr['notes']		= $notes;
					$arr['staffid']		= $thisStaff['id'];
					$arr['usecount']	= 1;
                    
					$set_exchange = M('Member_card_exchange')->where(array('cardid'=>$cardid))->find();
					$arr['score'] = intval($set_exchange['reward'])*$arr['expense'];
					
					$userArr=array();
					$userArr['total_score']		= $this->fans['total_score']+$arr['score'];
					$userArr['expensetotal']	= $this->fans['expensetotal']+$arr['expense'];

					M('Member_card_use_record')->add($arr);
					M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->save($userArr);
					M('Member_card_coupon')->where(array('id'=>$itemid))->setInc('usetime',1);					
					M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1'));
					$this->success('支付成功');
					exit;
				}else{
					$this->error('商家密码错误!');
					exit;
				}
			}
			
			
			
		}else{
			$card = M('Member_card_create')->field('number')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
			$this->assign('card',$card);
			$this->assign('consume_info',$data);
			$this->assign('thisCard',$thisCard);
			$this->display();		
		}	

	}

	public function select()
	{
		//session("session_company_{$this->token}", null);
		
		$company = M('Company')->where("`token`='{$this->token}' AND ((`isbranch`=1 AND `display`=1) OR `isbranch`=0)")->select();
		if (count($company) == 1) {
			$this->redirect(U('Store/cats',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $company[0]['id'], 'twid' => $this->_twid)));
		}
		
		$this->assign('company', $company);
		$this->assign('metaTitle', '商城分布');
		$this->display();
		
	}
	
	public function baoxian(){
        $this->display();
    }
    public function daiban(){
        $this->display();
    }
	/**
	 * 商城首页
	 */
	public function index() 
	{
		$this->redirect(U('Store/cats',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
	}
	
	/**
	 * 商城首页
	 */
	public function cats() 
	{
		$company = M('Company')->where("`token`='{$this->token}' AND `isbranch`=0")->find();
		D("Product_cat")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Attribute")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Product")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Product_cart")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Product_cart_list")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Product_comment")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		D("Product_setting")->where(array('token' => $this->token, 'cid' => 0))->save(array('cid' => $company['id']));
		
		$cid = $this->_cid = isset($_GET['cid']) ? intval($_GET['cid']) : $company['id'];
		if ($this->_isgroup) {
			$cid = $company['id'];
			$relation = M("Product_relation")->where(array('token' => $this->token, 'cid' => $this->_cid))->select();
			if (empty($relation) && $this->_cid != $cid) {
				$this->error("该店铺暂时没有商品可卖，先逛逛别的", U('Store/select',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
			}
		}
		session("session_company_{$this->token}", $this->_cid);
		$this->assign('cid', $this->_cid);
		
		$parentid = isset($_GET['parentid']) ? intval($_GET['parentid']) : 0;
		$cats = $this->product_cat_model->where(array('token' => $this->token, 'cid' => $cid))->order("sort ASC, id DESC")->select();
		$info = array();
		$sub = array();
		foreach ($cats as &$row) {
			$row['info'] = $row['des'];
			$row['img'] = $row['logourl'];
			if ($row['isfinal'] == 1) {
				$row['url'] = U('Store/products', array('token' => $this->token, 'catid' => $row['id'], 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid));
			} else {
				
                $row['url'] = U('Store/products', array('token' => $this->token, 'catid' => $row['id'], 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid));
                //$row['sub'] = array();
				//$row['url'] = U('Store/cats', array('token' => $this->token, 'cid' => $this->_cid, 'parentid' => $row['id'], 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid));
			}
			$info[$row['id']] = $row;
			
			$row['parentid'] && $sub[$row['parentid']][] = $row;
		}
		foreach ($sub as $k => $r) {
			if (isset($info[$k]) && $info[$k]) {
				$info[$k]['sub'] = $r;
			}
		}
		$result = array();
		foreach ($info as $kk => $ii) {
			if ($ii['parentid'] == $parentid) {
				$result[$kk] = $ii;
			}
		}
		$this->assign('info', $result);
		
		$this->assign('metaTitle', '商品分类');
		
		include('./PigCms/Lib/ORG/index.Tpl.php');
		include('./PigCms/Lib/ORG/cont.Tpl.php');
		$catemenu[0] = array('id' => 0, 'name' => '所有商品', 'picurl' => '/tpl/static/store/m-act-cat.png', 'k' => 0, 'vo' => array(), 'url' => U('Store/cats', array('token'=> $this->token,'wecha_id'=> $this->wecha_id,'cid' => $this->_cid)));
		$catemenu[1] = array('id' => 1, 'name' => '购物车', 'picurl' => '/tpl/static/store/m-act-cart.png', 'k' => 1, 'vo' => array(), 'url' => U('Store/cart', array('token'=> $this->token,'wecha_id'=> $this->wecha_id,'cid' => $this->_cid)));
		$catemenu[2] = array('id' => 2, 'name' => '查物流', 'picurl' => '/tpl/static/store/m-act-wuliu.png', 'k' => 2, 'vo' => array(), 'url' => U('Store/my', array('token'=> $this->token,'wecha_id'=> $this->wecha_id,'cid' => $this->_cid)));
		$catemenu[3] = array('id' => 3, 'name' => '用户中心', 'picurl' => '/tpl/static/store/user2.png', 'k' => 3, 'vo' => array(), 'url' => U('Store/my', array('token'=> $this->token,'wecha_id'=> $this->wecha_id,'cid' => $this->_cid)));
		$this->assign('catemenu', $catemenu);
		$set = M("Product_setting")->where(array('token' => $this->token, 'cid' => $this->_cid))->find();
		if (isset($tpl[$set['tpid'] - 1]['tpltypename'])) {
			$t = $tpl[$set['tpid'] - 1]['tpltypename'];
			
			$cateMenuFileName = "tpl/Wap/default/Index_menuStyle{$set['footerid']}.html";
			$this->assign('cateMenuFileName', $cateMenuFileName);
			
			$allflash=M('Store_flash')->where(array('token' => $this->token, 'cid' => $this->_cid))->select();
			
			foreach ($allflash as &$f) {
				if ($f['url']) {
					$url = $f['url'];
					$link=str_replace(array('{wechat_id}','{siteUrl}','&amp;'),array($this->wecha_id,$this->siteUrl,'&'),$url);
					if (!!(strpos($url,'tel')===false)&&$url!='javascript:void(0)'&&!strpos($url,'wecha_id=')){
						if (strpos($url,'?')){
							$link=$link.'&wecha_id='.$this->wecha_id . '&twid=' . $this->_twid;
						}else {
							$link=$link.'?wecha_id='.$this->wecha_id . '&twid=' . $this->_twid;
						}
					}
					$f['url'] = $link;
				}
			}
		
			$flash = array();
			$flashbg = array();
			foreach ($allflash as $af){
				if ($af['url']=='') {
					$af['url']='javascript:void(0)';
				}
				if ($af['type'] == 1) {
					array_push($flash,$af);
				} elseif ($af['type'] == 0) {
					array_push($flashbg,$af);
				}
			}
			
			//$allflash=$this->convertLinks($allflash);
		    $userinfo = M("Userinfo")->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
            $card=M('member_card_create')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
            $cars=M('member_card_car')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->select();
            $cardinfo=M('member_card_set')->where(array('token' => $this->token,'id'=>$card['cardid']))->find();
            $user=M('往来单位','dbo.','difo')->where(array('名称'=>$card['number']))->find();
            $wxcount=M('维修','dbo.','difo')->where(array('车主'=>$card['number'],'维修类别'=>array('neq','蜡水洗车'),'当前状态'=>array('not in',array('结束','取消'))))->count();
            $this->assign('card',$card);
            $this->assign('user',$user);
            $this->assign('wxcount',$wxcount);
            $this->assign('cardinfo',$cardinfo);
            $this->assign('userinfo',$userinfo);
			$count = count($flash);
			$this->assign('flash', $flash);
			$this->assign('tpl', $this->tpl);
			$this->assign('num', $count);
			$this->assign('flashbg', $flashbg);
			$this->assign('flashbgcount', count($flashbg));
		
			$this->display("Index:{$t}");
		} else {
			$this->assign('cats', $result);
			$this->display();
		}
	}
	
	public function products() 
	{
		//if (isset($_G['cid']))
		$where = array('token' => $this->token, 'cid' => $this->_cid, 'groupon' => 0, 'dining' => 0, 'status' => 0);
		if ($this->_isgroup) {
			$relation = M("Product_relation")->where(array('token' => $this->token, 'cid' => $this->_cid))->select();
			$gids = array();
			foreach ($relation as $r) {
				$gids[] = $r['gid'];
			}
			if ($gids) $where['gid'] = array('in', $gids);
			$where['cid'] = $this->mainCompany['id'];
		}
		
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		if ($catid) {
			$where['catid'] = $catid;
			$thisCat = $this->product_cat_model->where(array('id'=>$catid))->find();
			$where['cid'] = $thisCat['cid'];
			if (empty($this->_cid) || $this->_cid != $thisCat['cid']) {
				$this->_cid = $thisCat['cid'];
				session("session_company_{$this->token}", $this->_cid);
			}
			$this->assign('thisCat', $thisCat);
		}
		if (IS_POST){
			$key = $this->_post('search_name');
            $this->redirect('/index.php?g=Wap&m=Store&a=products&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&keyword=' . $key . '&twid=' . $this->_twid . '&cid=' . $this->_cid);
		}
		if (isset($_GET['keyword'])){
            $where['name|intro|keyword'] = array('like', "%".$_GET['keyword']."%");
            $this->assign('isSearch', 1);
		}
		$count = $this->product_model->where($where)->count();
		$this->assign('count', $count); 
		//排序方式
		$method = isset($_GET['method']) && ($_GET['method']=='DESC' || $_GET['method']=='ASC') ? $_GET['method'] : 'DESC';
		$orders = array('time', 'discount', 'price', 'salecount');
		$order = isset($_GET['order']) && in_array($_GET['order'], $orders) ? $_GET['order'] : 'time';
		$this->assign('order', $order);
		$this->assign('method', $method);
        	
		$products = $this->product_model->where($where)->order("sort ASC, " . $order.' '.$method)->limit('0, 8')->select();
		$this->assign('products', $products);
		$name = isset($thisCat['name']) ? $thisCat['name'] . '列表' : "商品列表";
		$this->assign('metaTitle', $name);
        $calCartInfo = $this->calCartInfo();
		$this->assign('productcount', $calCartInfo[0]);
		$this->display();
	}
	
	public function ajaxProducts()
	{
		$where = array('token' => $this->token, 'cid' => $this->_cid, 'groupon' => 0, 'dining' => 0, 'status' => 0);
		if ($this->_isgroup) {
			$relation = M("Product_relation")->where(array('token' => $this->token, 'cid' => $this->_cid))->select();
			$gids = array();
			foreach ($relation as $r) {
				$gids[] = $r['gid'];
			}
			if ($gids) $where['gid'] = array('in', $gids);
			$where['cid'] = $this->mainCompany['id'];
		}
		//$where = array('token' => $this->token, 'cid' => $this->_cid);
		if (isset($_GET['catid'])) {
			$catid = intval($_GET['catid']);
			$where['catid'] = $catid;
		}
		$page = isset($_GET['page']) && intval($_GET['page']) > 1 ? intval($_GET['page']) : 2;
		$pageSize = isset($_GET['pagesize']) && intval($_GET['pagesize']) > 1 ? intval($_GET['pagesize']) : 8;
		
		$method = isset($_GET['method']) && ($_GET['method']=='DESC' || $_GET['method']=='ASC') ? $_GET['method'] : 'DESC';
		$orders = array('time', 'discount', 'price', 'salecount');
		$order = isset($_GET['order']) && in_array($_GET['order'], $orders) ? $_GET['order'] : 'time';
		$start = ($page-1) * $pageSize;
		$products = $this->product_model->where($where)->order("sort ASC, " . $order.' '.$method)->limit($start . ',' . $pageSize)->select();
		exit(json_encode(array('products' => $products)));
	}
	
	public function product() 
	{
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where = array('token' => $this->token, 'id' => $id);
		$product = $this->product_model->where($where)->find();
		if (empty($product)) {
			$this->redirect(U('Store/products',array('token' => $this->token,'wecha_id' => $this->wecha_id,'cid' => $this->_cid, 'twid' => $this->_twid)));
		}
		
		$cid = $this->_isgroup ? $this->mainCompany['id'] : $this->_cid;
		
		$product['intro'] = isset($product['intro']) ? htmlspecialchars_decode($product['intro']) : '';
		$this->assign('product', $product);
		if ($product['endtime']){
			$leftSeconds = intval($product['endtime'] - time());
			$this->assign('leftSeconds', $leftSeconds);
		}
        $normsData = M("Norms")->where(array('catid' => $product['catid']))->select();
        foreach ($normsData as $row) {
        	$normsList[$row['id']] = $row['value'];
        }
        if($productCatData = M('Product_cat')->where(array('id' => $product['catid'], 'token' => $this->token, 'cid' => $cid))->find()) {
        	$this->assign('catData', $productCatData);
        }
		$colorDetail = $normsDeatail = $productDetail = array();
		$attributeData = M("Product_attribute")->where(array('pid' => $product['id']))->select();
		
		$productDetailData = M("Product_detail")->where(array('pid' => $product['id']))->select();
		foreach ($productDetailData as $p) {
			$p['formatName'] = $normsList[$p['format']];
			$p['colorName'] = $normsList[$p['color']];
			
			$formatData[$p['format']] = $colorData[$p['color']] = $productDetail[] = $p;
			
			$colorDetail[$p['color']][] = $p;
			$normsDetail[$p['format']][] = $p;
		}
		$productimage = M("Product_image")->where(array('pid' => $product['id']))->select();
		
		$this->assign('imageList', $productimage);
		$this->assign('productDetail', $productDetail);
		$this->assign('attributeData', $attributeData);
		$this->assign('normsDetail', $normsDetail);
		$this->assign('colorDetail', $colorDetail);
		$this->assign('formatData', $formatData);
		$this->assign('colorData', $colorData);
		$this->assign('metaTitle', $product['name']);
		$calCartInfo = $this->calCartInfo();
		$this->assign('count', $calCartInfo[0]);
		$where = array('token' => $this->token, 'cid' => $cid, 'pid' => $id, 'isdelete' => 0);
		$product_model = M("Product_comment");
		$score      = $product_model->where($where)->sum('score');
		$count      = $product_model->where($where)->count();
		$comment = $product_model->where($where)->order('id desc')->limit("0, 10")->select();
		foreach ($comment as &$com) {
			$com['wecha_id'] = $com['truename'];
		}
		
		$percent = "100%";
		if ($count) {
			$score = number_format($score / $count, 1);
			$percent =  number_format($score / 5, 2) * 100 . "%";
		}
		$totalPage = ceil($count / 10);
		$page = $totalPage > 1 ? 2 : 0;
		
		$this->assign('score', $score);
		$this->assign('num', $count);
		$this->assign('page', $page);
		$this->assign('comment', $comment);
		$this->assign('percent', $percent);
		$this->display();
	}
	
	public function getcomment()
	{
		$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
		$start = ($page - 1) * $offset;
		$offset = 10;
		$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
		$where = array('token' => $this->token, 'pid' => $pid, 'isdelete' => 0);
		$product_model = M("Product_comment");
		$count = $product_model->where($where)->count();
		
		$comment = $product_model->where($where)->order('id desc')->limit($start, $offset)->select();
		foreach ($comment as &$com) {
			$com['wecha_id'] = $com['truename'];
			$com['dateline'] = date("Y-m-d H:i", $com['dateline']);//substr($com['wecha_id'], 0, 7) . "****";
		}
		$totalPage = ceil($count / $offset);
		$page = $totalPage > $page ? intval($page + 1) : 0;
		exit(json_encode(array('error_code' => false, 'data' => $comment, 'page' => $page)));
	}
	
	/**
	 * 添加购物车
	 */
	public function addProductToCart()
	{
		$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		$carts = $this->_getCart();
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;//商品的详细id,即颜色与尺寸
		if (isset($carts[$id])) {
			if ($did) {
				if (isset($carts[$id][$did])) {
					$carts[$id][$did]['count'] += $count;
				} else {
					$carts[$id][$did]['count'] = $count;
				}
			} else {
				$carts[$id] += $count;
			}
		} else {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$calCartInfo = $this->calCartInfo();
		echo $calCartInfo[0].'|'.$calCartInfo[1];
	}
	
	private function calCartInfo($carts='')
	{
		$totalCount = $totalFee = 0;
		if (!$carts) {
			$carts = $this->_getCart();
		}
		$data = $this->getCat($carts);
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}
		
		return array($totalCount, $totalFee, $data[2]);
	}
	
	private function _getCart()
	{
		if (!isset($_SESSION[$this->session_cart_name])||!strlen($_SESSION[$this->session_cart_name])){
			$carts = array();
		} else {
			$carts=unserialize($_SESSION[$this->session_cart_name]);
		}
		return $carts;
	}
	
	/**
	 * 购物车列表
	 */
	public function cart()
	{
// 		if (empty($this->wecha_id)) {
// 			unset($_SESSION[$this->session_cart_name]);
// 		}
		$totalCount = $totalFee = 0;
		$data = $this->getCat($this->_getCart());
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}
		$list = $data[0];
		
		$this->assign('products', $list);
		$this->assign('totalFee', $totalFee);
		$this->assign('totalCount', $totalCount);
		$this->assign('metaTitle','购物车');
		$this->display();
	}
	
	
	
	/**
	 * 计算一次购物的总的价格与数量
	 * @param array $carts
	 */
	public function getCat($carts = '')
	{
		$carts = empty($carts) ? $this->_getCart() : $carts;
		//邮费
		$mailPrice = 0;
		//商品的IDS
		$pids = array_keys($carts);
		
		//商品分类IDS
		$productList = $cartIds = array();
		if (empty($pids)) {
			return array(array(), array(), array());
		}
		
		//获取分类ID
		$productdata = $this->product_model->where(array('id'=> array('in', $pids)))->select();
		foreach ($productdata as $p) {
			if (!in_array($p['catid'], $cartIds)) {
				$cartIds[] = $p['catid'];
			}
			$mailPrice = max($mailPrice, $p['mailprice']);
			$productList[$p['id']] = $p;
		}
		
		//商品规格参数值
		$catlist = $norms = array();
		if ($cartIds) {
			//产品规格列表
			$normsdata = M('norms')->where(array('catid' => array('in', $cartIds)))->select();
			foreach ($normsdata as $r) {
				$norms[$r['id']] = $r['value'];
			}
			//商品分类
			$catdata = M('Product_cat')-> where(array('id' => array('in', $cartIds)))->select();
			foreach ($catdata as $cat) {
				$catlist[$cat['id']] = $cat;
			}
		}
		$dids = array();
		foreach ($carts as $pid => $rowset) {
			if (is_array($rowset)) {
				$dids = array_merge($dids, array_keys($rowset));
			}
		}
        $card=M('member_card_create')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
        $cardinfo=M('member_card_set')->where(array('token' => $this->token,'id'=>$card['cardid']))->find();
        $grade="discount".$cardinfo['grade'];
		//商品的详细
		$totalprice = 0;
		$data = array();
		if ($dids) {
			$dids = array_unique($dids);
			$detail = M('Product_detail')->where(array('id'=> array('in', $dids)))->select();
			foreach ($detail as $row) {
				$row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
				$row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
				$row['count'] = isset($carts[$row['pid']][$row['id']]['count']) ? $carts[$row['pid']][$row['id']]['count'] : 0;
				if ($this->fans['getcardtime'] > 0) {
					$row['price'] = $row['vprice'] ? $row['vprice'] : $row['price'];
				}
				$productList[$row['pid']]['detail'][] = $row;
				$data[$row['pid']]['total'] = isset($data[$row['pid']]['total']) ? intval($data[$row['pid']]['total'] + $row['count']) : $row['count'];
				$data[$row['pid']]['totalPrice'] = isset($data[$row['pid']]['totalPrice']) ? intval($data[$row['pid']]['totalPrice'] + $row['count'] * $row['price']) : $row['count'] * $row['price'];//array('total' => $totalCount, 'totalPrice' => $totalFee);
				$totalprice += $data[$row['pid']]['totalPrice'];
			}
		}
		//商品的详细列表
		$list = array();
		foreach ($productList as $pid => $row) {
			if (!isset($data[$pid]['total'])) {
				$count = $price = 0;
				if (isset($carts[$pid]) && is_array($carts[$pid])) {
					$a = explode("|", $carts[$pid]['count']);
					$count = isset($a[0]) ? $a[0] : 0;
					$price = isset($a[1]) ? $a[1] : 0;
				} else {
					$a = explode("|", $carts[$pid]);
					$count = isset($a[0]) ? $a[0] : 0;
					$price = isset($a[1]) ? $a[1] : 0;
				}
				$data[$pid] = array();
               
				$row['price'] = $price ? $price : ($this->fans['getcardtime'] > 0 && $row["$grade"] ? $row["$grade"] : $row['price']);
				$row['count'] = $data[$pid]['total'] = $count;
				if (empty($count) && empty($price)) {
					$row['count'] = $data[$pid]['total'] = isset($carts[$pid]['count']) ? $carts[$pid]['count'] : (isset($carts[$pid]) && is_int($carts[$pid]) ? $carts[$pid] : 0);
					if ($this->fans['getcardtime'] > 0) {
						$row['price'] = $row["$grade"] ? $row["$grade"] : $row['price'];
					}
				}
				
				$data[$pid]['totalPrice'] = $data[$pid]['total'] * $row['price'];
				$totalprice += $data[$pid]['totalPrice'];
			}
			$row['formatTitle'] =  isset($catlist[$row['catid']]['norms']) ? $catlist[$row['catid']]['norms'] : '';
			$row['colorTitle'] =  isset($catlist[$row['catid']]['color']) ? $catlist[$row['catid']]['color'] : '';
			$list[] = $row;
		}
		if ($obj = M('Product_setting')->where(array('token' => $this->token, 'cid' => $this->_cid))->find()) {
			if ($totalprice >= $obj['price']) $mailPrice = 0;
		}
		return array($list, $data, $mailPrice);
	}
	
	public function deleteCart()
	{
		$products=array();
		$ids=array();
		$carts=$this->_getCart();
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($did) {
			unset($carts[$id][$did]);
			if (empty($carts[$id])) {
				unset($carts[$id]);
			}
		} else {
			unset($carts[$id]);
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$this->redirect(U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $this->_twid, 'cid' => $this->_cid)));
	}
	
	public function ajaxUpdateCart(){
		$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		$carts = $this->_getCart();
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;
		if (isset($carts[$id])) {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		} else {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$calCartInfo = $this->calCartInfo();
		echo $calCartInfo[0].'|'.$calCartInfo[1];
	}
	
	
	public function ordersave()
	{
        $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        if (md5($_GET['password'])!=$userinfo['paypass'])
        {
            $this->error('支付密码不正确');
            exit;
        }
        $totalprice=$_GET['price'];
        if ($totalprice>$userinfo['balance'])
        {
            $this->error('会员卡余额不足，请先充值');
            exit;
        }
        $carno=$_GET['carno'];
		$row = array();
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		$row['token'] = $this->token;
		$row['wecha_id'] = $wecha_id;
		$info = array();
		$row['cid'] = $this->_isgroup ? $this->mainCompany['id'] : $this->_cid;
		//积分
		$orid = isset($_GET['orid']) ? intval($_GET['orid']) : 0;
		$product_cart_model = D('Product_cart');
		if ($cartObj = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'id' => $orid))->find()) {
			$carts = unserialize($cartObj['info']);
            $saveprice = $totalprice =$cartObj['totalprice'];
            $orderid = $cartObj['orderid'];
		} 
        else{
			$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
            $id = intval($_GET['id']);
            $did = isset($_GET['did']) ? intval($_GET['did']) : 0;//商品的详细id,即颜色与尺寸
            if ($did) {
                $carts[$id][$did]['count'] = $count;
            } else {
                $carts[$id] = $count;
            }
			$calCartInfo = $this->calCartInfo($carts);
            $saveprice = $totalprice = $calCartInfo[1] + $calCartInfo[2];

			foreach ($carts as $pid => $rowset) {
				$total = 0;
				$tmp = M('product')->where(array('id' => $pid))->find();
                $ordername=$row['ordername']='购买'.$tmp['name'];
				if (is_array($rowset)) {
					foreach ($rowset as $did => $ro) {
						$temp = M('Product_detail')->where(array('id' => $did, 'pid' => $pid))->find();
						if ($temp['num'] < $ro['count'] && empty($cartObj)) {
							$this->error('购买的量超过了库存');
                            exit;
						}
						$total += $ro['count'];
						$price =$totalprice;
						$info[$pid][$did] = array('count' => $ro['count'], 'price' => $price);
					}
				}else {
                    $total = $rowset;
                    $price = $totalprice;
                    $info[$pid] = $rowset. "|" . $price;
                    
				}
				if ($tmp['num'] <intval($total) && empty($cartObj)) {
					$this->error('购买的量超过了库存');
                    exit;
				}
			}
          
            $row['total'] = $calCartInfo[0];
            $row['price'] = $totalprice;
            $row['diningtype'] = 0;
            $row['buytime'] = '';
            $row['tableid'] = 0;
            $row['info'] = serialize($info);
            $row['groupon']=0;
            $row['dining'] = 0;
            $row['score'] = 0;
            $row['sent'] =0;
            $row['twid'] = $this->_twid;
            $row['totalprice'] = $saveprice;
            $row['time'] = time();
      
			$product_model = M('product');
			$product_cart_list_model = M('product_cart_list');
            $row['orderid'] = $orderid = date("YmdHis") . rand(100000, 999999);
            $cartid = $product_cart_model->add($row);
			$crow = array();
			$tdata = $this->getCat($carts);
			foreach ($carts as $k => $c){
				$crow['cartid'] = $cartid;
				$crow['productid'] = $k;
				$crow['price'] = $tdata[1][$k]['totalPrice'];
				$crow['total'] = $tdata[1][$k]['total'];
				$crow['wecha_id'] = $row['wecha_id'];
				$crow['token'] = $row['token'];
				$crow['cid'] = $row['cid'];
				$crow['time'] = time();
				$product_cart_list_model->add($crow);
					
			}
			//删除库存
			foreach ($carts as $pid => $rowset){
				$total = 0;
				if (is_array($rowset)) {
					foreach ($rowset as $did => $ro) {
						M('Product_detail')->where(array('id' => $did, 'pid' => $pid))->setDec('num', $ro['count']);
						$total += $ro['count'];
					}
				} else {
					if (strstr($rowset, '|')) {
						$a = explode("|", $rowset);
						$total = $a[0];
					} else {
						$total = $rowset;
					}
				}
				$product_model->where(array('id' => $pid))->setDec('num', $total);
			}
        }
        
        $this->gotopay($orderid,$totalprice,'Store',$ordername,$carno);
       
	}
    private function gotopay($orderid,$price,$from,$orderName,$carno,$redirect=NULL){
        
        $userinfo = M('Userinfo');
        $payrecord = M('Member_card_pay_record');
        $create = M('Member_card_create');
        $exchange = M('Member_card_exchange');

        $cardid = $create->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->getField('cardid');
        $cardid = (int)$cardid;
        $reward = $exchange->where(array('token'=>$this->token,'cardid'=>$cardid))->getField('reward');
        $reward = (int)$reward;
        $uinfo = $userinfo->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->field('id,balance,expensetotal,total_score')->find();

        if(!$orderid)$this->error('请传入订单号');
        if($uinfo['balance'] < $price){
            $this->error('余额不足');
        }
        
        if($payrecord->where("orderid = '$orderid'")->getField('id')){
            $flag1 = true;
        }else{
            $record['orderid'] = $orderid;
            $record['ordername'] = $orderName;
            $record['paytype'] = 'CardPay';
            $record['createtime'] = time();
            $record['paid'] = 0;
            $record['price'] = $price;
            $record['note'] =$_GET['shop'];
            $record['shop'] =$_GET['shop'];
            $record['usecar'] =$carno;
            $record['token'] = $this->token;
            $record['wecha_id'] = $this->wecha_id;
            $record['type'] = 0;
            $flag1 = $payrecord->add($record);
        }
        if($from=='consume')$from='Card';
        $udata['balance'] = $uinfo['balance'] - $price;
        $flag2 = $userinfo->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->save($udata);
        $payHande=new payHandle($this->token,$from,'CardPay');
        $payHande->afterPay($orderid,null,null);
        if($flag1 && $flag2){
            
            $payrecord->where(array('orderid'=>$orderid,'token'=>$this->token))->setField('paid',1);
            if (isset($redirect)){
                $urlinfo=explode('|',$_GET['redirect']);
                $parmArr=explode(',',$urlinfo[1]);
                $parms=array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'paytype'=>'CardPay','orderid'=>$orderid);
                if ($parmArr){
                    foreach ($parmArr as $pa){
                        $pas=explode(':',$pa);
                        $parms[$pas[0]]=$pas[1];
                    }
                }
                $this->redirect(U($urlinfo[0],$parms));
                
            }else{
                $this->redirect(U("$from/payReturn",array('orderid'=>$orderid,'carno'=>$carno,'token'=>$this->token,'wecha_id'=>$this->wecha_id,'paytype'=>'CardPay')));
            }
            
        }else{
            
            $this->error('支付失败');
        }
		
    }

	public function getcoupon()
    {
        $jssdk = new JSSDK($this->wxuser['appid'],$this->wxuser['appsecret']);
        $signPackage = $jssdk->GetSignPackage();
        if(IS_POST)
        {
            $couponnum=$_POST['couponid'];
            $lottery=M('member_lottery')->where(array('coupon_num'=>$couponnum))->find();
            if(!$lottery)
            {
                echo '抱歉找不到该券码';
                exit;
            }
            elseif($lottery['overtime']<time())
            {
                 echo '券码已过期';
                 exit;
          
            }elseif($lottery['usetime']>0)
            {
                echo '该券码已经兑换过';
                exit;
                
            }
            $list=M('member_lottery_detail')->where(array('coupon_num'=>$couponnum,'token'=>$this->token))->select();
            $card=M('member_card_create')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
            if($list)
            {
                foreach($list as $c){
                    $mycoupon=M("member_card_coupon")->where(array('id'=>$c['coupon_id']))->find();
                    $data['token']		= $this->token;
                    $data['wecha_id']	= $this->wecha_id;
                    $data['coupon_id']	= $c['coupon_id'];
                    $data['is_use']		= '0';
                    $data['coupon_type']= '1';
                    $data['cardid']		= $card['cardid'];
                    $data['add_time']	= time(); 
                    $days=$mycoupon['days'];
                    $data['over_time']=strtotime(date('Y-m-d',time())."+$days day");
                    if(intval($c['num'])>0){
                        for($i=0;$i<intval($c['num']);$i++){
                            $data['coupon_num']=date('YmdHis',time()).mt_rand(1000,9999);
                            M('Member_card_coupon_record')->add($data);
                        }
                    }
                    
                }
            }
            $row['usetime']=time();
            $row['wecha_id']=$this->wecha_id;
            M('member_lottery')->where(array('token'=>$this->token,'coupon_num'=>$couponnum))->save($row);
            echo '兑换成功';
            exit;
        }
        $this->assign('signPackage', $signPackage);
        $this->display();

    }
	public function orderCart()
	{
        $count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;//商品的详细id,即颜色与尺寸
		if ($did) {
			$carts[$id][$did]['count'] = $count;
		} else {
			$carts[$id] = $count;
		}
		$orid = isset($_GET['orid']) ? intval($_GET['orid']) : 0;
		$totalCount = $totalFee = 0;
		if ($orid && ($cartObj = M('product_cart')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'id' => $orid))->find())) {
			$products = unserialize($cartObj['info']);
			$data = $this->getCat($products);
            $totalCount= $cartObj['total'];
		    $totalFee= $cartObj['price'];
		} else {
			$data = $this->getCat($carts);
            foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}

		}
        $product=M('Product')->where(array('id' => $id))->find();
        $carlist=M('member_card_car')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
        $this->assign('carlist',$carlist);
		if (empty($data[0])) {
			$this->redirect(U('Store/cart', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)));
		}
		if (empty($totalCount)) {
			$this->error('没有购买商品!', U('Store/cart', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)));
		}
		$this->assign('product', $product);
		$this->assign('orid', $orid);
		$this->assign('totalFee', $totalFee);
		$this->assign('totalCount', $totalCount);
		$this->display();
	}
	
	public function my()
	{
        //$offset = 5;
        //$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
        //$start = ($page - 1) * $offset;
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		$product_cart_model = M('product_cart');
		//$orders = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'groupon' => 0, 'dining' => 0))->limit($start, $offset)->order('time DESC')->select();
		$orders = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'groupon' => 0, 'dining' => 0))->order('time DESC')->select();
		$count = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'groupon' => 0, 'dining' => 0))->count();
        $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->select();
		$list = array();
        $isconfirm=0;
		if ($orders){
			foreach ($orders as $o){
				$products = unserialize($o['info']);
				$pids = array_keys($products);
				$o['productInfo'] = array();
                $o['isconfirm']=0;
				if ($pids) {
                    foreach ($pids as $id)
                    {
                        $item=M('product')->where(array('id' =>$id))->find();
                        if($item['iscoupon']==1)$o['isconfirm']=1;
					    $item['count']=explode('|',$products["$id"])[0];
					    $item['money']=explode('|',$products["$id"])[1];
                        $o['productInfo'][] =$item;
                    }
                }
				$list[] = $o;
			}
		}
        if(IS_POST){
            $arr=array();
            $arr['itemid']  	= $this->_post('record_id','trim');
            $arr['wecha_id']	= $this->wecha_id;
            $arr['expense']		= 0;
            $arr['time']		= time();
            $arr['token']		= $this->token;
            $arr['cat']			= 1;
            $arr['shop']		= $this->_post('address');
            $arr['carno']		= $this->_post('carno');
            $arr['usecount']	= 1;
            $arr['notes']		= $this->_post('notes','trim').'_确认收货';
            $arr['score'] 		=0;
            M('Product_cart')->where(array('id' => $arr['itemid'], 'token' => $this->token))->save(array('handled'=>1));
            M('Member_card_use_record')->add($arr);	//添加消费券使用记录	
            echo '操作成功';
            exit;
        }
		//$totalpage = ceil($count / $offset);
		//$this->assign('totalpage', $totalpage);
		//$this->assign('page', $page);
		$this->assign('orders', $list);
		$this->assign('isconfirm', $isconfirm);
		$this->assign('carinfo', $carinfo);
		$this->assign('ordersCount', $count);
		$this->assign('metaTitle', '我的订单');
		
		//是否要支付
		//$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
		//$this->assign('alipayConfig',$alipayConfig);
		$this->display();
	}
	
	public function myDetail()
	{
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		
		$cartid = isset($_GET['cartid']) && intval($_GET['cartid'])? intval($_GET['cartid']) : 0;
		$product_cart_model = M('product_cart');

		$list = array();
		if ($cartObj = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id, 'id' => $cartid))->find()){
			$products = unserialize($cartObj['info']);
			$data = $this->getCat($products);
			$pids = array_keys($products);
			$cartObj['productInfo'] = array();
			if ($pids) {
				$cartObj['productInfo'] = M('product')->where(array('id' => array('in', $pids)))->select();
			}
			
			$totalCount = $totalFee = 0;
			if (isset($data[1])) {
				foreach ($data[1] as $pid => $row) {
					$totalCount += $row['total'];
					$totalFee += $row['totalPrice'];
				}
			}
			$list = $data[0];
			$commentList = array();
			//if ($cartObj['paid']) {
				$comment = M("Product_comment")->where(array('token' => $this->token, 'cartid' => $cartid, 'wecha_id' => $wecha_id))->select();
				foreach ($comment as $row) {
					$commentList[$row['pid']][$row['detailid']] = $row;
				}
			//}
			$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
			foreach ($list as &$row) {
				if ($row['detail']) {
					foreach ($row['detail'] as &$r) {
						if (isset($commentList[$row['id']][$r['id']])) {
							$r['comment'] = 0;
						} else {
							$r['comment'] = $alipayConfig['open'] ? ($cartObj['paid'] ? 1 : 0) : 1;
						}
					}
				} else {
					if (isset($commentList[$row['id']][0])) {
						$row['comment'] = 0;
					} else {
						$row['comment'] = $cartObj['paid'] ? 1 : 0;
					}
				}
			}
			$this->assign('commentList', $commentList);
			$this->assign('products', $list);
			$this->assign('totalFee', $totalFee);
			$this->assign('totalCount', $totalCount);
			$this->assign('mailprice', $data[2]);
			$this->assign('cartData', $cartObj);
			$this->assign('cartid', $cartid);
		}
		$this->assign('metaTitle', '我的订单');
		$this->display();
	}
	
	public function cancelCart()
	{
        if (IS_POST){
            $cartid = isset($_POST['cartid']) && intval($_POST['cartid'])? intval($_POST['cartid']) : 0;
            $product_model=M('product');
            $product_cart_model = M('product_cart');
            $product_cart_list_model = M('product_cart_list');
            $thisOrder = $product_cart_model->where(array('id'=> $cartid))->find();
            if (empty($thisOrder)) {
                echo "没有此订单";
                exit;
            }
            $this->wecha_id ? $this->wecha_id : session('twid');
            if (empty($thisOrder['paid'])) {
                //删除订单和订单列表
                $product_cart_model->where(array('id' => $cartid))->delete();
                $product_cart_list_model->where(array('cartid' => $cartid))->delete();
                //还原积分
                //$userinfo_model = M('Userinfo');
                //$thisUser = $userinfo_model->where(array('token' => $this->token, 'wecha_id' => $wecha_id))->find();
                //$userinfo_model->where(array('id' => $thisUser['id']))->setInc('total_score', $thisOrder['score']);
                F('fans_token_wechaid', NULL);
                //商品销量做相应的减少
                $carts = unserialize($thisOrder['info']);
                //还原库存
                foreach ($carts as $pid => $rowset) {
                    $total = 0;
                    if (is_array($rowset)) {
                        foreach ($rowset as $did => $row) {
                            M('product_detail')->where(array('id' => $did, 'pid' => $pid))->setInc('num', $row['count']);
                            $total += $row['count'];
                        }
                    } else {
                        if (strstr($rowset, '|')) {
                            $a = explode("|", $rowset);
                            $total = $a[0];
                        } else {
                            $total = $rowset;
                        }
                    }
                    $product_model->where(array('id' => $pid))->setInc('num', $total);
                    //$product_model->where(array('id' => $pid))->setDec('salecount', $total);
                }
                echo '订单取消成功';
                exit;
            }
            echo '购买成功的订单不能取消';
            exit;
        }
	}
	
	public function updateOrder()
	{
		$product_cart_model = M('product_cart');
		$thisOrder = $product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		if ($thisOrder['wecha_id']!=$this->wecha_id){
			exit();
		}
		$this->assign('thisOrder',$thisOrder);
		$carts = unserialize($thisOrder['info']);
		$totalCount = $totalFee = 0;
		$listNum = array();
		$data = $this->getCat($carts);
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
				$listNum[$pid] = $row['total'];
			}
		}
		$list = $data[0];
		$this->assign('products', $list);
		$this->assign('totalFee', $totalFee);
		$this->assign('listNum', $listNum);
		$this->assign('metaTitle','修改订单');
		//是否要支付
		$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
		$this->assign('alipayConfig', $alipayConfig);
		$this->display();
	}
	
	/**
	 * 评论
	 */
	public function comment()
	{
		$cartid = isset($_GET['orid']) && intval($_GET['orid'])? intval($_GET['orid']) : 0;
        $cartObj = M("product_cart")->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'id' => $cartid))->find();
        $products = unserialize($cartObj['info']);
        $ids=array();
        foreach($products as $key=>$value){
            $ids[]=$key;
        }
        $p=M("Product")->where(array('id' =>array('in',$ids), 'token' => $this->token))->select();
        $this->assign('products',$p);
		$this->assign('cartid', $cartid);
		$this->display();
	}
	public function commentSave()
	{
		$cartid = isset($_POST['cartid']) && intval($_POST['cartid'])? intval($_POST['cartid']) : 0;
        $userinfo=M('userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
        $products=$_POST['products'];		
		$comment = D("Product_comment");
        foreach($products as $product){
            $data['cartid'] = $cartid;
            $data['pid'] = $product['id'];
            $data['score'] = $product['score'];
            $data['content'] = htmlspecialchars($product['content']);
            $data['token'] = $this->token;
            $data['wecha_id'] = $this->wecha_id;
            $data['truename'] = $userinfo['truename'];
            $data['tel'] = $userinfo['tel'];
            $data['dateline'] = time();
            $comment->add($data);
        }
	    echo U('Store/my',array('token' => $this->token,'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'cid' => $this->_cid,'cartid' => $cartid, 'twid' => $this->_twid));
        exit;
		
	}
	public function wxcomment()
	{
		$id = isset($_GET['orid']) && intval($_GET['orid'])? intval($_GET['orid']) : 0;
        $wx=M("维修",'dbo.','difo')->where(array('流水号' =>$id))->find();
        $this->assign('wxinfo',$wx);
		$this->display();
	}
	public function wxcommentSave()
	{
		$id = isset($_POST['orid']) && intval($_POST['orid'])? intval($_POST['orid']):0;
        $commnet=$_POST['comment'];		
        $data['是否评论'] = '是';
        $data['服务态度'] = $commnet['fwtd'];
        $data['服务质量'] = $commnet['fwzl'];
        $data['评论内容'] = htmlspecialchars($commnet['content']);
        $wx=M("维修",'dbo.','difo')->where(array('流水号' =>$id))->find();
        M("维修",'dbo.','difo')->where(array('流水号' =>$id))->save($data);
	    echo U('Store/carinfo',array('carno' =>$wx['车牌号码'] ));
        exit;
		
	}
    public function modifyinfo(){
        $useinfo=M('userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        $this->assign('userinfo',$useinfo);
        $this->display();
    }
	public function deleteOrder()
	{
		$product_model = M('product');
		$product_cart_model = M('product_cart');
		$product_cart_list_model = M('product_cart_list');
		$thisOrder = $product_cart_model->where(array('id' => intval($_GET['id'])))->find();
		//检查权限
		$id = $thisOrder['id'];
		$wecha_id = $this->wecha_id ? $this->wecha_id : session('twid');
		
		if ($thisOrder['wecha_id'] != $wecha_id || $thisOrder['handled'] == 1) {
			exit();
		}
		//删除订单和订单列表
		$product_cart_model->where(array('id' => $id))->delete();
		$product_cart_list_model->where(array('cartid' => $id))->delete();
		//商品销量做相应的减少
		$carts = unserialize($thisOrder['info']);
		foreach ($carts as $k=>$c) {
			if (is_array($c)) {
				$productid = $k;
				$price = $c['price'];
				$count = $c['count'];
				//$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
			}
		}
		$this->redirect(U('Store/my', array('token' => $_GET['token'], 'wecha_id' => $_GET['wecha_id'], 'cid' => $this->_cid, 'twid' => $this->_twid)));
	}
	
	/**
	 * 支付成功后的回调函数
	 */
	public function payReturn() {
	   $orderid = $_GET['orderid'];
       $carno=$_GET['carno'];
	   if ($order = M('Product_cart')->where(array('orderid' => $orderid, 'token' => $this->token))->find()) {
           if (intval($order['paid'])==1) {
               if($order['score']>0)
               {
                   M('userinfo')->where(array('token' => $_GET['token'], 'wecha_id' => $_GET['wecha_id']))->setDec('total_score',$order['score']);
               }
               $carts = unserialize($order['info']);

               $tdata = $this->getCat($carts);
               $list = array();
               foreach ($tdata[0] as $va) {
                   $t = array();
                   $salecount = 0;
                   if (!empty($va['detail'])) {
                       foreach ($va['detail'] as $v) {
                           $t = array('num' => $v['count'], 'colorName' => $v['colorName'], 'formatName' => $v['formatName'], 'price' => $v['price'], 'name' => $va['name']);
                           $list[] = $t;
                           $salecount += $v['count'];
                       }
                   } else {
                       $t = array('num' => $va['count'], 'price' => $va['price'], 'name' => $va['name']);
                       $list[] = $t;
                       $salecount = $va['count'];
                   }
                   $coupon=M("Product_coupon")->where(array('token' => $_GET['token'],'pid'=>$va['id']))->select();
                   $card=M('member_card_create')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
                   $product=M('Product')->where(array('id' => $va['id']))->find();
                   if($product['autosend']==1){
                       M('Product_cart')->where(array('orderid' => $orderid, 'token' => $this->token))->save(array('sent'=>1));
                   }
                   if($coupon&&$product['iscoupon']){
                      foreach($coupon as $c){
                          $mycoupon=M("member_card_coupon")->where(array('id'=>$c['cid']))->find();
                          $data['token']		= $this->token;
                          $data['wecha_id']	= $this->wecha_id;
                          $data['coupon_id']	= $c['cid'];
                          $data['is_use']		= '0';
                          $data['coupon_type']		= '1';
                          $data['cardid']		= $card['cardid'];
                          $data['add_time']	= time(); 
                          $days=$mycoupon['days'];
                          $data['over_time']=strtotime(date('Y-m-d',time())."+$days day");
                          if($c['num']>0){
                              for($i=0;$i<intval($c['num']);$i++){
                                  $data['coupon_num']=date('YmdHis',time()).mt_rand(1000,9999);
                                  M('Member_card_coupon_record')->add($data);
                              }
                          }
                          
                      }
                      $this->sellbill($va['price'],$product['name']);
                   }
                   else{
                       if(strpos($product['name'], '打蜡') === 0){
                            $this->washcar($va['price'],$carno,$product['project'],'汽车美容');
                       }
                       else{
                            $this->washcar($va['price'],$carno,$product['project'],'蜡水洗车');
                       }
                   }
                   M('Product')->where(array('id' => $va['id']))->setInc('salecount', $salecount);
               }
               $model  = new templateNews();
               $dataKey    = 'OPENTM201444641';
               $dataArr    = array(
                   'first'         => '您的订单支付成功。',
                   'keyword1'      => $orderid,
                   'keyword2'      => $order['price'].'元',
                   'keyword3'      => $order['paytype']=='CardPay'?'会员卡支付':'微信支付',
                   'wecha_id'      => $this->wecha_id,
                   'remark'        => '如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                   'url'      => U('Store/myinfo',array('token'=>$this->token,'wecha_id'=>$this->wecha_id),true,false,true),
               );
               $model->sendTempMsg($dataKey,$dataArr);
               if ($order['twid']) {
                   $this->savelog(3, $order['twid'], $this->token, $order['cid'], $order['totalprice']);
               }
               /*$company = D('Company')->where(array('token' =>$this->token, 'id' => $order['cid']))->find();
               $op = new orderPrint();
               $msg = array('companyname' => $company['name'], 'companytel' => $company['tel'], 'truename' => $order['truename'], 'tel' => $order['tel'], 'address' => $order['address'], 'buytime' => $order['time'], 'orderid' => $order['orderid'], 'sendtime' => '', 'price' => $order['price'], 'total' => $order['total'], 'list' => $list);
               $msg = ArrayToStr::array_to_str($msg, 1);
               $op->printit($this->token, $this->_cid, 'Store', $msg, 1);
               $userInfo = D('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
               Sms::sendSms($this->token, "您的顾客{$userInfo['truename']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理");
               $model = new templateNews();
               $model->sendTempMsg('TM00820', array('href' => U('Store/my',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)), 'wecha_id' => $this->wecha_id, 'first' => '购买商品提醒', 'keynote1' => '订单已支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '购买成功，感谢您的光临，欢迎下次再次光临！'));
                */
           }
			$this->redirect(U('Store/myinfo',array('token' => $this->token,'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)));
	   }else{
	      exit('订单不存在');
	    }
	}
    public function _thisCard(){
    	$member_card_set_db=M('Member_card_set');
        $cardinfo=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($cardinfo['cardid'])))->find();
    	return $thisCard;
    }
    //优惠劵
    public function coupon(){
    	$this->assign('infoType','coupon');
    	$type 		= $this->_get('type','intval');
    	$now=time();
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$type);
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_num,coupon_id,coupon_type,add_time,is_use,over_time,use_time')->select();	
        $overlist=array();
        $list=array();
        $uselist=array();
        $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->select();
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_coupon')->where($cwhere)->field('useinfo,info,pic,statdate,enddate,title,price')->find();
			$cinfo['info'] 	= html_entity_decode($cinfo['info']);
            if($value['is_use']==0){
                if($value['over_time']-$now>=0){
                    $cinfo['isovertime']=0;
                    $list[$key]=array_merge($value,$cinfo);
                }else{ 
                    $cinfo['isovertime']=1;
                    $overlist[$key]=array_merge($value,$cinfo);
                }

            }
            else
            {
                $uselist[$key]=array_merge($value,$cinfo);
        
            }
		}
    	$this->assign('firstItemID',$data[0]['id']);
    	$this->assign('count1',count($list));
        //$this->assign('count2',count($uselist));
   	   // $this->assign('count3',count($overlist));
   	    $this->assign('carinfo',$carinfo);
        $this->assign('list',$list);
    	$this->assign('overlist',$overlist);
    	$this->assign('uselist',$uselist);
    	$this->assign('type',$type);
    	$this->display();
    }
    function action_useCoupon(){
    	$now=time();
    	if (IS_POST){
            $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    		$rwhere 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$this->_post('coupon_type','intval'),'id'=>$this->_post('record_id','intval'),'is_use'=>'0');
    		$r_record 	= M('Member_card_coupon_record')->where($rwhere)->find();
    		if (!$r_record){
    			echo'没有找到卷类';
    			exit();
    		}
    		$itemid		= $r_record['coupon_id'];  
    		$useTime	= 1;
            //$condition	= array('coupon_id'=>$itemid,'token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$this->_post('coupon_type','intval'),'is_use'=>'1','use_time'=>array('egt',strtotime(date('Y-m-d'))));
            //$count= M('Member_card_coupon_record')->where($condition)->count();
            //if($count>0)
            //{
            //    echo '{"success":-1,"msg":"每人每天限消费一张优惠券"}';
            //    exit;
            //}
            //$staff_db=M('Company_staff');
            //$thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$this->token))->find();
            //if(empty($thisStaff)){
            //    echo'{"success":-7,"msg":"用户名不存在"}';
            //    exit();
            //}	
            if (md5($this->_post('password'))!=$userinfo['paypass'])
            {
                echo  '支付密码不正确';
                exit;
            }
            else {
                $couponname=M('Member_card_coupon')->where(array('id'=>$itemid))->field('title,cardid')->find();
                $arr=array();
                $arr['itemid']  	= $itemid;
                $arr['wecha_id']	= $this->wecha_id;
                $arr['expense']		= 0;
                $arr['time']		= $now;
                $arr['token']		= $this->token;
                $arr['cat']			= 1;
                $arr['shop']		= $this->_post('address');
                $arr['carno']		= $this->_post('carno');
                $arr['usecount']	= $useTime;
                $arr['notes']		= $this->_post('notes','trim').'线下消费'.$couponname['title'].'一张';
                $arr['score'] 		=0;

                M('Member_card_use_record')->add($arr);	//添加消费券使用记录					
                //优惠劵使用记录
                M('Member_card_coupon')->where(array('id'=>$itemid))->setInc('usetime',1);//优惠券使用次数加1
                $couponnum=M('Member_card_coupon_record')->where($rwhere)->find();
                M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1','carno'=>$arr['carno'],'shop'=>$arr['shop']));//会员优惠券记录修改为已使用
                $model  = new templateNews();
                $dataKey    = 'TM151214';
                $dataArr    = array(
                    'productType'      =>'券名称',
                    'name'      =>$couponname['title'],
                    'certificateNumber'      =>$couponnum["coupon_num"],
                    'number'      =>'1张',
                    'wecha_id'      => $this->wecha_id,
                    'remark'        => '注意：此消息作为您本次消费凭证，请妥善保存，如有疑问，请致电020-39053199联系我们,或发消息到微信平台上进行咨询。',
                    'url'      => U('Store/myinfo',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$couponname['cardid']),true,false,true),
                );
                //Log::write(json_encode($r),Log::DEBUG);
                $model->sendTempMsg($dataKey,$dataArr);
                if(strpos($couponname['title'], '打蜡') === 0){
                 $this->washcar(0,$arr['carno'],'AYC0001','汽车美容');
                }else{
                 $this->washcar(0,$arr['carno'],'AYC0001','蜡水洗车');
               } 
                echo "线下消费成功";	
                exit;
			}                 
        }
	}
    public function integral(){
    	$this->assign('infoType','integral');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	$is_use 	= $this->_get('is_use','intval')?$this->_get('is_use','intval'):'0';
    	$now=time();
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'3');
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,coupon_num,cardid,coupon_id,coupon_type,add_time,is_use,over_time,use_time')->select();
		$overlist=array();
        $list=array();
        $uselist=array();
        $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->select();
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_integral')->where($cwhere)->field('info,pic,statdate,enddate,title,integral,useinfo')->find();
			$cinfo['info'] 	= html_entity_decode($cinfo['info']);
			if($value['is_use']==0){
                if($value['over_time']-$now>=0){
                    $cinfo['isovertime']=0;
                    $list[$key]=array_merge($value,$cinfo);
                }else{
                    $cinfo['isovertime']=1;
                    $overlist[$key]=array_merge($value,$cinfo);
                }

            }
            else
            {
                $uselist[$key]=array_merge($value,$cinfo);
                
            }
		}

    	$this->assign('firstItemID',$data[0]['id']);
        $this->assign('count1',count($list));
        $this->assign('carinfo',$carinfo);
        //$this->assign('count2',count($uselist));
        //$this->assign('count3',count($overlist));
        $this->assign('list',$list);
    	$this->assign('overlist',$overlist);
    	$this->assign('uselist',$uselist);
    	$this->assign('is_use',$is_use);
    	$this->display();
    }
    function action_useIntergral(){
    	$now=time();
    	if (IS_POST){  
            $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    		$rwhere 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$this->_post('coupon_type','intval'),'id'=>$this->_post('record_id','intval'),'is_use'=>'0');
    		$r_record 	= M('Member_card_coupon_record')->where($rwhere)->find();
    		if (!$r_record){
    			echo "没有找到卷类";
    			exit();
    		} 
    		$itemid		= $r_record['coupon_id'];
    		$db 		= M('Member_card_integral');
    		$couponname	= $db->where('id='.$itemid.' AND statdate<'.$now.' AND enddate>'.$now)->find();
    		
    		if (!$couponname){ 
    			echo '不存在指定信息';
    			exit();
    		}
            //$condition	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$this->_post('coupon_type','intval'),'is_use'=>'1','use_time'=>array('egt',strtotime(date('Y-m-d'))));
            //$count= M('Member_card_coupon_record')->where($condition)->count();
            //if($count>0)
            //{
            //    echo'{"success":-9,"msg":"每人每天限消费一张礼品券"}';
            //    exit();
            //}

    		$member_card_set_db=M('Member_card_set');
    		$thisCard=$member_card_set_db->where(array('id'=>intval($couponname['cardid'])))->find();
    		if (!$thisCard){
    			echo'会员卡不存在';
    			exit();
    		}
            if (md5($this->_post('password'))!=$userinfo['paypass'])
            {
                echo  '支付密码不正确';
                exit;
            }else {			
                $arr=array();
                $arr['itemid']  	= $itemid;
                $arr['wecha_id']	= $this->wecha_id;
                $arr['expense']		= 0;
                $arr['time']		= $now;
                $arr['token']		= $this->token;
                $arr['cat']			= 1;
                $arr['shop']		= $this->_post('address');
                $arr['carno']		= $this->_post('carno');
                $arr['usecount']	= 1;
                $arr['notes']		= $this->_post('notes','trim').'线下消费'.$couponname['title'].'一张';
                $arr['score'] 		=0;
                //更新记录
                M('Member_card_use_record')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'itemid'=>$r_record['id']))->save($arr);
                $db->where(array('id'=>$itemid))->setInc('usetime',1);
                //优惠劵使用记录
                M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1'));
                if(strpos($couponname['title'], '打蜡') === 0){
                    $this->washcar(0,$arr['carno'],'AYC0001','汽车美容');
                }
                elseif(strpos($couponname['title'], '救援') === 0){
                     $this->washcar(0,$arr['carno'],'AYC0001','普通快修');
                 }
                else{
                    $this->washcar(0,$arr['carno'],'AYC0001','蜡水洗车');
                } 
                echo '兑换成功';
            }
    	}
    }
	public function register()
	{
		if (IS_POST) {
			$tel = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
			$password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
			$password2 = isset($_POST['password2']) ? htmlspecialchars($_POST['password2']) : '';
			$truename = isset($_POST['truename']) ? htmlspecialchars($_POST['truename']) : '';
			$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
			$wechaname = isset($_POST['wechaname']) ? htmlspecialchars($_POST['wechaname']) : '';
			$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
			//$wecha_id = md5($tel . time());
			$userInfo = M('Userinfo')->where(array('username' => $username))->find();
			if (empty($username)) {
				$this->error("此账号已存在!");
			}
			if ($userInfo) {
				$this->error("此账号已存在!");
			}
			if (empty($tel)) {
				$this->error("电话号码不能为空!");
			}
			if (empty($password)) {
				$this->error("密码不能为空!");
			}
			if ($password != $password2) {
				$this->error("密码不正确");
			}
			$uid = D("Userinfo")->add(array('truename' => $truename,'wechaname'=>$wechaname,'token' => $this->token,wecha_id=>$this->wecha_id, 'address' => $address, 'password' => md5($password), 'tel' => $tel, 'username' => $username));
			if ($uid) {
				$twid = $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $this->randstr{rand(0, 51)} . $uid;
				D('Userinfo')->where(array('id' => $uid))->save(array('twid' => $twid));
				$this->savelog(2, $this->_twid, $this->token, $this->_cid);
				session('twid', $twid);
				$callbackurl = session('callbackurl');
				$this->success('注册成功', $callbackurl);
			} else {
				$this->error(D("Userinfo")->error());
			}
		} else {
			$this->assign('metaTitle', '商城会员注册');
			$this->display();
		}
	}
	public function login()
	{
		if (IS_POST) {
			$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
			$password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
			$userInfo = M('Userinfo')->where(array('username' => $username))->find();
			if (empty($userInfo)) {
				$this->error("用户不存在");
			} elseif ($userInfo['password'] != md5($password)) {
				$this->error("密码不正确");
			} else {
				session('twid', $userInfo['twid']);
				$callbackurl = session('callbackurl');
				if ($callbackurl) {
					session('callbackurl', null);
					$this->success('登录成功', $callbackurl);
				} else {
					$this->success('登录成功', U('Store/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid, 'twid' => $this->_twid)));
				}
			}
		} else {
			$this->assign('metaTitle', '商城会员登录');
			$this->display();
		}
	}
	
	/**
	 * 分佣记录
	 */
	private function savelog($type, $twid, $token, $cid, $param = 1)
	{
		if ($twid && $token && $cid) {
			$set = M("Twitter_set")->where(array('token' => $token, 'cid' => $cid))->find();
			if (empty($set)) return false;
			$db = D("Twitter_log");
			$price = 0;
			// 1.点击， 2.注册会员， 3.购买商品
			if ($type == 3) {//购买商品
				$price = $set['percent'] * 0.01 * $param;
				$db->add(array('token' => $token, 'cid' => $cid, 'twid' => $twid, 'type' => 3, 'dateline' => time(), 'param' => $param, 'price' => $price));
			} elseif ($type == 2) {//注册会员
				$price = $set['registerprice'];
				$db->add(array('token' => $token, 'cid' => $cid, 'twid' => $twid, 'type' => 2, 'dateline' => time(), 'param' => $param, 'price' => $set['registerprice']));
			} else {//点击
				$stime = strtotime(date("Y-m-d") . " 00:00:01");
				$etime = strtotime(date("Y-m-d") . " 23:59:59");
				$count = $db->where("`dateline`>={$stime} AND `dateline`<={$etime} AND `token`='{$token}' AND `twid`='{$twid}'")->count();
				if ($count < $set['clickmax']) {
					$price = $set['clickprice'];
					$db->add(array('token' => $token, 'cid' => $cid, 'twid' => $twid, 'type' => 1, 'dateline' => time(), 'param' => $param, 'price' => $set['clickprice']));
				}
			}
			//统计总收入
			if ($price) {
				if ($count = M("Twitter_count")->where(array('token' => $token, 'cid' => $cid, 'twid' => $twid))->find()) {
					D("Twitter_count")->where(array('id' => $count['id']))->setInc('total', $price);
				} else {
					D("Twitter_count")->add(array('twid' => $twid, 'token' => $token, 'cid' => $cid, 'total' => $price, 'remove' => 0));
				}
			}
		}
	}

	public function mysetting()
    {
        $this->display();
    }
    public function  carinfo()
    {
        $list=M('维修','dbo.','difo')->where(array('车牌号码'=>$_GET['carno'],'当前状态'=>'结束'))->order('制单日期 desc')->select();
        foreach($list as $key=>$value)
        {
           $items=M('维修项目','dbo.','difo')->where(array('ID'=>$value['ID']))->select();
           $peijian=M('维修配件','dbo.','difo')->where(array('ID'=>$value['ID'],'仅内部核算成本'=>0))->select();
           $list[$key]['items']=$items;
           $list[$key]['peijian']=$peijian;
       }
        $this->assign('list',$list);
        $this->display();
    }
    public function mycar()
	{
        $userinfo = M("Userinfo")->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->find();
        $carlist=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->select();
        foreach($carlist as $key=>$car)
        {
               $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$car['carno']))->find();
               $carlist[$key]['carinfo']=$carinfo;
        }
        //$card=M('member_card_create')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->find();
        //$couponCount1=M("member_card_coupon_record")->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'is_use'=>'0','coupon_type'=>'1'))->count();
        //$couponCount2=M("member_card_coupon_record")->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'is_use'=>'0','coupon_type'=>'3'))->count();
        //$product_cart_model = M('product_cart');
        //$orderscount = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'groupon' => 0, 'dining' => 0))->count();
        //$this->assign('orderscount',$orderscount);
        //$this->assign('couponCount1',$couponCount1);
        //$this->assign('couponCount2',$couponCount2);
        $this->assign('carinfo',$carinfo);
        $this->assign('carlist',$carlist);
        $this->assign('userinfo',$userinfo);
        $this->assign('metaTitle', '我的车辆');
        $this->display();
        if($_POST)
        {
            $carno = isset($_POST['carno']) ? htmlspecialchars($_POST['carno']) : '';
            if($_POST['opt']=='delete'){
               M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$carno))->delete(); 
            }
            else if($_POST['opt']=='modify')
            {
                $where=array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$_POST['oldcarno']);
                M('member_card_car')->where($where)->save(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$carno));
                echo '修改成功';
                exit;
            }
            else
            {
                
                $carno=strtoupper($carno);
                $carinfo=M('member_card_car')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$carno))->find();
                if(empty($carinfo))
                {
                    M('member_card_car')->add(array('token' => $this->token,wecha_id=>$this->wecha_id,'carno'=>$carno));
                    echo '添加成功';
                    exit();
                }
                else
                {
                    echo '车牌号码已存在';
                    exit();
                }
            }

        }
	}
    public function getQRCode()
    {
        include './PigCms/Lib/ORG/phpqrcode.php'; 
        $value = 'https://www.jiandaoyun.com/f/57519d0cfc57121c4be57255'; //二维码内容   
        $errorCorrectionLevel = 'L';//容错级别   
        $matrixPointSize = 9;//生成图片大小   
        //生成二维码图片   
        //$logo = 'getheadimg.png';//准备好的logo图片   
        //$QR = 'qrcode.png';//已经生成的原始二维码图   
        //if ($logo !== FALSE) {   
        //    $QR = imagecreatefromstring(file_get_contents($QR));   
        //    $logo = imagecreatefromstring(file_get_contents($logo));   
        //    $QR_width = imagesx($QR);//二维码图片宽度   
        //    $QR_height = imagesy($QR);//二维码图片高度   
        //    $logo_width = imagesx($logo);//logo图片宽度   
        //    $logo_height = imagesy($logo);//logo图片高度   
        //    $logo_qr_width = $QR_width / 5;   
        //    $scale = $logo_width/$logo_qr_width;   
        //    $logo_qr_height = $logo_height/$scale;   
        //    $from_width = ($QR_width - $logo_qr_width) / 2;   
        //    //重新组合图片并调整大小   
        //    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
        //    $logo_qr_height, $logo_width, $logo_height);   
        //}   
        //输出图片 
        header("Content-type: image/png"); 
        QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize,2);   
       
        //imagepng($QR,null,0);   
        //imagedestroy($QR); 
    }
	/**
	 * 我的个人信息
	 */
	public function myinfo()
	{
			$userinfo = M("Userinfo")->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
            $card=M('member_card_create')->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id))->find();
            $cardinfo=M('member_card_set')->where(array('token' => $this->token,'id'=>$card['cardid']))->find();
            $couponCount1=M("member_card_coupon_record")->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id,'is_use'=>'0','coupon_type'=>'1','over_time'=>array('egt',strtotime(date('Y-m-d',time())))))->count();
            $couponCount2=M("member_card_coupon_record")->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id,'is_use'=>'0','coupon_type'=>'3','over_time'=>array('egt',strtotime(date('Y-m-d',time())))))->count();
            $withrawcount=M("yangchebao_withdraw")->where(array('token' => $this->token,'wecha_id'=>$this->wecha_id,'state'=>array('lt',3)))->count();
            $product_cart_model = M('product_cart');
            $orderscount = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'paid'=>0,'groupon' => 0, 'dining' => 0))->count();
            $this->assign('card',$card);
            $this->assign('orderscount',$orderscount);
            $this->assign('withrawcount',$withrawcount);
            $this->assign('couponCount1',$couponCount1);
            $this->assign('couponCount2',$couponCount2);
            $this->assign('cardinfo',$cardinfo);
            $this->assign('userinfo',$userinfo);
			$this->assign('metaTitle', '个人中心');
		    $this->display();
	}
    public function withdrawrecord(){
      $records= M('yangchebao_withdraw')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
      $this->assign('records',$records);
     $this->display();
    }
	public function withdraw(){
        $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        if (md5($this->_post('password'))!=$userinfo['paypass'])
        {
            echo  '支付密码不正确';
            exit;
        }
        else{
            $money=doubleval($this->_post('money'));
            if($money>$userinfo['balance'])
            {
                echo '会员账户余额不足';
                exit();
            }
            $userinfo['balance']=$userinfo['balance']-$money;
            if(M('Userinfo')->save($userinfo)){
                $data['token']=$this->token;
                $data['wecha_id']=$this->wecha_id;
                $data['wecha_name']=$userinfo['wechaname'];
                $data['money']=$money;
                $data['withdraw_time']=time();
                $data['account_type']=$this->_post('type');
                $data['account_user']=$this->_post('username');
                $data['account']=$this->_post('account');
                $data['state']=1;
                M('yangchebao_withdraw')->add($data);
                echo '申请已提交成功';
            }
        }
    
    }
    public function my_coupon(){
    	$this->assign('infoType','coupon');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	$type=3;
    	$now	= time();
    	$data 	= array();
        $now 		= time();
    	$where 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
    	$data	= M('Member_card_integral')->where($where)->order('create_time desc')->select();
        foreach ($data as $k=>$n){
    		$data[$k]['info']	= html_entity_decode($n['info']);
    		$data[$k]['count'] 	= 1;
    		$cwhere = array('token'=>$this->token,'cardid'=>$thisCard['id'],'coupon_type'=>$type,'coupon_id'=>$n['id']);
    		$count 	= M('Member_card_coupon_record')->where($cwhere)->count();
            $data[$k]['get_count'] 	= $n['people']-$count;//剩余多少张
    		$data[$k]['count'] 	= $n['people'];//总共多少张
    	}
    	$this->assign('firstItemID',$data[0]['id']);
    	$this->assign('list',$data);
    	$this->assign('type',$type);
    	$this->display();
    } 
    public function action_myCoupon(){
    	$data['use_time'] 		= '';
    	$data['add_time'] 		= time();
    	$data['coupon_id'] 		= $this->_post('coupon_id','intval');
    	$data['cardid'] 		= $this->_post('cardid','intval');
    	$data['token'] 			= $this->token;
    	$data['wecha_id'] 		= $this->wecha_id;
    	$data['coupon_type'] 	= $this->_post('type','intval');
    	$now 	= time();
    	$integral 	= M('Member_card_integral')->where(array('token'=>$this->token,'cardid'=>$data['cardid'],'id'=>$data['coupon_id'],'ispublic'=>'1'))->find();
        $count1=M('Member_card_coupon_record')->where(array('token'=>$this->token,'coupon_id'=>$integral['id'],'coupon_type'=>$data['coupon_type'],'cardid'=>$data['cardid']))->count();
        if($this->thisUser['total_score']<$integral['integral']){
    		echo  '你的积分不足'.$integral['integral'];
    		exit;
    	}
        if($count1>=$integral['people'])
        {
    		echo  '礼品券已经兑换完了';
    		exit;
        }
        $count=M('Member_card_coupon_record')->where(array('token'=>$this->token,'coupon_id'=>$integral['id'],'wecha_id'=>$data['wecha_id'],'coupon_type'=>$data['coupon_type'],'cardid'=>$data['cardid']))->count();
        $total=$integral['total'];
        if($count>=$total)
        {
    		echo  '该礼品券每人最多能兑换'.$total.'张，你已超出兑换数量限制';
    		exit;
        }
        $days=$integral['days'];
        $data['over_time']=strtotime(date('Y-m-d',time)."+$days day");
        $data['coupon_num']=date('YmdHis',time()).mt_rand(1000,9999);
        $rid 	= M('Member_card_coupon_record')->add($data);//会员优惠券表中增加一条记录

        $arr			= array();
        $arr['itemid']	= $rid; //暂取记录id
        $arr['wecha_id']= $this->wecha_id;
        $arr['expense']	= 0;
        $arr['time']	= $now;
        $arr['token']	= $this->token;
        $arr['cat']		= 2;
        $arr['score']	= 0-intval($integral['integral']);
        M('Member_card_use_record')->add($arr);//积分记录中增加一条记录
        M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->setDec('total_score',$integral['integral']);// 修改用户信息表中积分数据

        echo  '兑换成功';
        exit;
    		
    	
    }


    public function turnin()
    {
        $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        if (md5($this->_post('password'))!=$userinfo['paypass'])
        {
            echo  '支付密码不正确';
            exit;
        }
        else{

            $money=doubleval($this->_post('money'));
            if($money>$userinfo['balance'])
            {
                echo '会员账户余额不足，请先充值';
                exit();
            }
            $userinfo['balance']=$userinfo['balance']-$money;
            $userinfo['yc_balance']=$userinfo['yc_balance']+$money;
            if(M('Userinfo')->save($userinfo)){
                $data['token']=$this->token;
                $data['wecha_id']=$this->wecha_id;
                $data['money']=$money;
                $data['opdate']=time();
                $data['type']=1;
                M('yangchebao_inout')->add($data);
                echo '转入成功';
                exit;
            }
        }
    }
    public function turnout()
    {
        $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
        if (md5($this->_post('password'))!=$userinfo['paypass'])
        {
            echo  '支付密码不正确';
            exit;
        }
        else{
            $money=doubleval($this->_post('money'));
            if($money>$userinfo['yc_balance'])
            {
                echo '养车宝账户余额不足';
                exit();
            }
            $userinfo['balance']=$userinfo['balance']+$money;
            $userinfo['yc_balance']=$userinfo['yc_balance']-$money;
            if(M('Userinfo')->save($userinfo)){
                $data['token']=$this->token;
                $data['wecha_id']=$this->wecha_id;
                $data['money']=$money;
                $data['opdate']=time();
                $data['type']=2;
                M('yangchebao_inout')->add($data);
                echo '转出成功';
            }
        }
      
    }
    public function revenue(){
        $revenuelist=M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'revenue_time'=>array('gt',date("y-m-d",strtotime('-30 day')))))->order('revenue_time desc')->select();
        $this->assign('revenuelist',$revenuelist);
        $sumrevenue=round(M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->sum('revenue'),2);
        $this->assign('sumrevenue',$sumrevenue);
        $this->display();
    }
    public function revenueunit(){
        $revenuelist=M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'revenue_time'=>array('gt',date("y-m-d",strtotime('-30 day')))))->order('revenue_time desc')->select();
        $this->assign('revenuelist',$revenuelist);
        $avgrevenue=round(M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'revenue_time'=>array('gt',date("y-m-d",strtotime('-30 day')))))->avg('revenueunit'),2);
        $this->assign('avgrevenue',$avgrevenue);
        
        $this->display();
    }
  public function yangchebao()
	{
			$userinfo = M("Userinfo")->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->find();
            $card=M('member_card_create')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->find();
            $revenue=M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id,'revenue_time'=>array('gt',date("y-m-d",strtotime('-7 day')))))->order('revenue_time')->select();
            $revenuetime=array();
            $revenuerate=array();
            $myrevenue=0.0;
            $sumrevenue=round(M('yangchebao_revenue')->where(array('token' => $this->token,wecha_id=>$this->wecha_id))->sum('revenue'),2);
            foreach($revenue as $item){
                $revenuetime[]=date("m-d",strtotime($item['revenue_time']));
                $revenuerate[]=doubleval($item['revenue_rate']);
                if(date("m-d",strtotime($item['revenue_time']))==date("m-d",strtotime('-1 day')))
                {
                    $myrevenue=$item['revenue'];
                }
                $revenueunit=round($item['revenueunit'],4);
            }
            
            $this->assign('card',$card);
            $this->assign('sumrevenue',$sumrevenue);
            $this->assign('myrevenue',$myrevenue);
            $this->assign('revenueunit',$revenueunit);
            $this->assign('revenuetime',json_encode($revenuetime));
            $this->assign('revenuerate',json_encode($revenuerate));
            $this->assign('userinfo',$userinfo);
			$this->assign('metaTitle', '养车宝');
		    $this->display();
	}
	/**
	 * 佣金的获取记录
	 */
	public function detail()
	{
// 		echo $this->mytwid;die;
		if ($this->mytwid) {
			$offset = 5;
			$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
			$start = ($page - 1) * $offset;
			$log = M("Twitter_log")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid))->limit($start, $offset)->order('id DESC')->select();
			$count = M("Twitter_log")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid))->count();
			$totalpage = ceil($count / $offset);
			$this->assign('orders', $log);
			$this->assign('ordersCount', $count);
			$this->assign('totalpage', $totalpage);
			$this->assign('page', $page);
		}
		$this->assign('metaTitle', '佣金获取记录');
		$this->display();
	}
	
	/**
	 * 提现记录
	 */
	public function remove()
	{
		if ($this->mytwid) {
			$offset = 5;
			$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
			$start = ($page - 1) * $offset;
			$log = M("Twitter_remove")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid))->limit($start, $offset)->order('id DESC')->select();
			$count = M("Twitter_remove")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid))->count();
			$totalpage = ceil($count / $offset);
			$this->assign('orders', $log);
			$this->assign('ordersCount', $count);
			$this->assign('totalpage', $totalpage);
			$this->assign('page', $page);
		}
		$this->assign('metaTitle', '我的个人信息');
		$this->display();
	}
	
	public function logout()
	{
		session('twid', null);
		session('login', null);
		session('twitter_save', null);
		$this->redirect(U('Store/cats',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'twid' => $this->_twid)));
	}
	
	/**
	 * 提现请求
	 */
	public function setremove()
	{
		if ($this->mytwid) {
			$remove = M("Twitter_remove")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid, 'status' => 0))->find();
			$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
			$tel = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
			$number = isset($_POST['number']) ? htmlspecialchars($_POST['number']) : '';
			$bank = isset($_POST['bank']) ? htmlspecialchars($_POST['bank']) : '';
			$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
			$price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : 0;
			$data = array('token' => $this->token, 'cid' => $this->_cid, 'twid' => $this->mytwid, 'name' => $name, 'tel' => $tel, 'number' => $number, 'bank' => $bank, 'address' => $address, 'price' => $price);
			if (IS_POST) {
				$count = M("Twitter_count")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid))->find();
				$total = $count['total'] - $count['remove'];
				if ($total < $price) $this->error("请不要贪心，您现在还没有{$price}元钱供你提现");
				if (empty($name)) $this->error("提款人姓名不能为空");
				if (empty($number)) $this->error("提款人账号不能为空");
				if (empty($bank)) $this->error("提款银行名称不能为空");
				if ($remove) {
					D('Twitter_remove')->where(array('id' => $remove['id']))->save($data);
				} else {
					$data['dateline'] = time();
					$data['status'] = 0;
					D('Twitter_remove')->add($data);
				}
				$this->success('提现提交成功');
				die;
			} else {
				if (empty($remove)) {
					$remove = M("Twitter_remove")->where(array('twid' => $this->mytwid, 'token' => $this->token, 'cid' => $this->_cid, 'status' => 1))->order('id DESC')->limit('0, 1')->find();
					$remove['price'] = 0;
				}
				$this->assign('remove', $remove);
			}
		}
		$this->assign('metaTitle', '填写提现信息');
		$this->display();
	}
}

?>