<?php
class CardAction extends WapAction{
	public $wecha_id;
	public $thisUser;
	public $isamap;
	public function __construct(){
		parent::_initialize();
		if (!defined('RES')){
			define('RES',THEME_PATH.'common');
		}
		$this->assign('wecha_id',$this->wecha_id);
		//
		$this->token=$this->_get('token');
		$this->thisUser = M('Userinfo')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->wecha_id))->find();
		if (!$this->wecha_id && ACTION_NAME != 'companyMap'){
			$this->error('您没有权限使用会员卡，如需使用请关注微信“'.$this->wxuser['wxname'].'”并回复会员卡',U('Index/index',array('token'=>$this->token)));
		}
        if(!in_array(ACTION_NAME, array('scanpay'))){

            $user = M('Userinfo')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
            if(empty($user)||(empty($user['carno'])&&empty($user['carno1'])&&empty($user['carno2']))){
                $this->redirect(U('Store/userinfo', array('token' => $this->token)));

            }
        }
		if (C('baidu_map')){
			$this->isamap=0;
		}else {
			$this->isamap=1;
			$this->amap=new amap();
		}
	} 
	public function index(){
     
		$cards=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
		$cardsByID=array();
		if ($cards){
			foreach ($cards as $c){
				$cardsByID[$c['cardid']]=$c;
			}
		}
		$cardsCount=count($cards);
		$this->assign('cards',$cards);
		$this->assign('memberCard',$cards[0]);
		if ($cardsCount){
			echo '<script>location.href="/index.php?g=Wap&m=Card&a=card&wecha_id='.$this->wecha_id.'&token='.$this->token.'&cardid='.$cards[0]['cardid'].'";</script>';
		}
		$this->assign('cardsCount',$cardsCount);
		//
		$userinfo_db=M('Userinfo');
		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
		$userScore=0;
		if ($userInfos){
			$userScore=intval($userInfos[0]['total_score']);
		}
		$this->assign('userScore',$userScore);
		//
		$member_card_set_db=M('Member_card_set');
		$allCards=$member_card_set_db->where(array('token'=>$this->token))->order('miniscore ASC')->select();
		if ($allCards){
			$i=0;
			foreach ($allCards as $c){
				$allCards[$i]['applied']=$cardsByID[$c['id']]?1:0;
				if (isset($_GET['mycard'])&&!$allCards[$i]['applied']){
					unset($allCards[$i]);
				}else{
                    $owhere         = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'is_open'=>'1','cardid'=>$allCards[$i]['id'],'start'=>array('lt',time()),'end'=>array('gt',time()));    
                    $allCards[$i]['gifts'] = M('Member_card_gifts')->where($owhere)->count();
                }
				$i++;
			}
		}
		$allCardsCount=count($allCards);
		$this->assign('allCards',$allCards);
		$this->assign('allCardsCount',$allCardsCount);
		//
		$thisCompany=M('Company')->where(array('token'=>$this->token,'isbranch'=>0,'display'=>1))->find();
		$this->assign('thisCompany',$thisCompany);
		//
		$infoType='memberCardHome';
		if (isset($_GET['mycard'])){
			$infoType='myCard';
		}
		
		$focus = M('Member_card_focus')->where(array('token'=>$this->_get('token')))->select();
		
		if($focus == NULL){
			$focus = array(
				array(
					"info" => "广告位描述",
					"img" => "/tpl/static/attachment/focus/tour/4.jpg",
					"url" => ""
				),
				array(
					"info" => "广告位描述",
					"img" => "/tpl/static/attachment/focus/tour/3.jpg",
					"url" => ""
				)
			);
		}
        
        //redirect("/index.php?g=Wap&m=Card&a=card&token=$token&wecha_id=$wecha_id&cardid=$c.id}");
		$focus = $this->convertLinks($focus);
		$this->assign('flash',$focus);
		$this->assign('infoType',$infoType);
		//
		$this->display();
    }
    public function getLink($url){
		$url=$url?$url:'javascript:void(0)';
		
		$link=str_replace(array('{wechat_id}','{siteUrl}','&amp;'),array($this->wecha_id,$this->siteUrl,'&'),$url);
        if (!!(strpos($url,'tel')===false)&&$url!='javascript:void(0)'&&!strpos($url,'wecha_id=')){
            if (strpos($url,'?')){
                $link=$link.'&wecha_id='.$this->wecha_id;
            }else {
                $link=$link.'?wecha_id='.$this->wecha_id;
            }
        }
		return $link;
	}
	function modifypass(){
        if(IS_POST){
            $update['wecha_id']=$this->wecha_id;
            $update['token']=$this->_get('token');
            $sql=D('Userinfo');
            $userinfo=$sql->where($update)->find();
            if($userinfo['paypass']!=md5($this->_post('oldpassword'))){
                echo 2;exit ;
            }
            if($this->_post('newpassword') != ''){
				$data['paypass'] = md5($this->_post('newpassword'));
			}
            //$this->_post('carno')? $data['carno'] = $this->_post('carno') : $data['carno'] = '';	
            if(M('Userinfo')->where($update)->save($data)){
                S('fans_'.$this->token.'_'.$this->wecha_id,NULL);
                echo 1;exit;
            }else{
                echo 0;exit;
            }
        }
        //$data['wecha_id']=$this->wecha_id;
        //$data['token']=$this->_get('token');
        //$sql=D('Userinfo');
        //$userinfo=$sql->where($data)->find();
        //$this->assign('info',$userinfo);
		$this->display();
	}
	public function convertLinks($arr){
		$i=0;
		foreach ($arr as $a){
			if ($a['url']){
				$arr[$i]['url']=$this->getLink($a['url']);
			}
			$i++;
		}
		return $arr;
	}
    public function companyMap(){
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
    	$this->apikey=C('baidu_map_api');
		$this->assign('apikey',$this->apikey);
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		
		
		if (!$this->isamap){
			$this->display();
		}else {			
			$link=$this->amap->getPointMapLink($thisCompany['longitude'],$thisCompany['latitude'],$thisCompany['name']);
			header('Location:'.$link);
		}
		
		
		
    }
    public function companyDetail(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token))->order('id ASC')->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
		$company_model=M('Company');
		$where=array('token'=>$this->token,'display'=>1);
		$companies=$company_model->where($where)->order('taxis ASC')->select();
		$this->assign('companies',$companies);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		$this->display();
    }
    public function companyIntro(){
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		$this->display();
    }
    function card(){
        $cardid     = $this->_get('cardid','intval');
    	$this->assign('infoType','card');
    	$member_card_set_db=M('Member_card_set');
        if(!$cardid){
            $card=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
            $cardid=$card['cardid'];
        }
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>$cardid))->find();
        if(empty($cardid) || empty($thisCard)){
            $this->error('您还没有领取会员卡');
        }
    	$this->assign('thisCard',$thisCard);
    	$this->assign('card',$thisCard);

        $member_card_notice_db=M('Member_card_notice');
    	$now=time();
    	//
    	$notices=$member_card_notice_db->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->select();
    	if ($notices){
    		$i=0;
    		foreach ($notices as $n){
    			$notices[$i]['content']=html_entity_decode($n['content']);
    			$i++;
    		}
    	}
    	$this->assign('notices',$notices);

    	$error=0;
    	if ($thisCard){
    		$userinfo_db=M('Userinfo');
    		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    		$userScore=0;
    		if ($userInfos){
    			$userScore=intval($userInfos[0]['total_score']);
    			$userInfo=$userInfos[0];
    		}
    		$this->assign('userScore',$userScore);
    		//
    		$member_card_create_db=M('Member_card_create');
    		$thisMember=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>intval($cardid)))->find();
    		$hasApplied=count($thisMember);
    		//
    		if (!$hasApplied){
    			//
    			$card=M('Member_card_create')->field('id,number')->where("token='".$this->token."' and cardid=".$thisCard['id']." and wecha_id = ''")->find();
    			if(!$card){
    				$error=-1;
    			}else {
    				//
                    if (intval($thisCard['miniscore'])==0 || $userScore>intval($thisCard['miniscore'])){
                        $error=-4;
                        header('Location:/index.php?g=Wap&m=Userinfo&a=index&token='.$this->token.'&wecha_id='.$this->wecha_id.'&cardid='.$thisCard['id']);
                    }else if($userScore<intval($thisCard['miniscore'])){
                        $error=-3;
                    }
    			}
    			//
    		}else{
    			$this->assign('thisMember',$thisMember);
    			//
    			$now=time();
    			//
    			$noticeCount=M('Member_card_notice')->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->count();
    			$this->assign('noticeCount',$noticeCount);
    			//
                //$member_card_vip_db=M('Member_card_vip');
                //$previlegeCount=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->count();
                //$this->assign('previlegeCount',$previlegeCount);
    			//
                //$iwhere 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'3','is_use'=>'0','cardid'=>$thisCard['id']);
                //$integralCount 	= M('Member_card_coupon_record')->where($iwhere)->count();

                //$cwhere1 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'1','is_use'=>'0','cardid'=>$thisCard['id']);
                //$couponCount1 	= M('Member_card_coupon_record')->where($cwhere1)->count();

                //$cwhere2 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'2','is_use'=>'0','cardid'=>$thisCard['id']);
                //$couponCount2 	= M('Member_card_coupon_record')->where($cwhere2)->count();
                //$recordcount 	= $integralCount+$couponCount1+$couponCount2;
                //$now 		= time();
                //$where1 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'attr'=>'0','statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
                //$coupon 	= M('Member_card_coupon')->where($where1)->count(); 
    			
                //$where1 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'statdate'=>array('lt',$now),'enddate'=>array('gt',$now));
                //$integral 	= M('Member_card_integral')->where($where1)->count();

                //$couponCount 	= $coupon+$integral;
                //$this->assign('couponCount',$couponCount);
    			
                /*    			$owhere 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'is_open'=>'1','cardid'=>$thisCard['id'],'start'=>array('lt',time()),'end'=>array('gt',time()));
    			
    			$openCount 		= M('Member_card_gifts')->where($owhere)->count();
                
    			
    			$this->assign('openCount',$openCount);*/
    			
    			//$todaySigned=$this->_todaySigned();
    			//$this->assign('todaySigned',$todaySigned);
    			//
    			$this->assign('userInfo',$userInfo);
    		}
    	}else {
    		$error=-2;
    	}
    	$this->assign('error',$error);
    	$this->display();
    }
    
    function gifts(){
    	$cardid = $this->_get('cardid','intval');
    	$now 	= time();
    	$where	= array('token'=>$this->token,'cardid'=>$cardid,'is_open'=>'1','start'=>array('lt',$now),'end'=>array('gt',$now));
    	
    	$list 	= M('Member_card_gifts')->where($where)->select();

		$this->assign('list',$list);
    	$this->display();
    }
    
    function cards(){
    	$this->assign('infoType','card');
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	$this->assign('card',$thisCard);
    	$error=0;
    	if ($thisCard){
    		$userinfo_db=M('Userinfo');
    		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    		$userScore=0;
    		if ($userInfos){
    			$userScore=intval($userInfos[0]['total_score']);
    			$userInfo=$userInfos[0];
    		}
    		$this->assign('userScore',$userScore);
    		//
    		$member_card_create_db=M('Member_card_create');
    		$thisMember=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>intval($_GET['cardid'])))->find();
    		$hasApplied=count($thisMember);
    		//
    		if (!$hasApplied){
    			//
    			$card=M('Member_card_create')->field('id,number')->where("token='".$this->token."' and cardid=".$thisCard['id']." and wecha_id = ''")->find();
    			if(!$card){
    				$error=-1;
    			}else {
    				//
    			    if (intval($thisCard['miniscore'])==0){
    					$error=-4;
    					header('Location:/index.php?g=Wap&m=Userinfo&a=index&token='.$this->token.'&wecha_id='.$this->wecha_id.'&cardid='.$thisCard['id']);
    				}else if($userScore <= intval($thisCard['miniscore'])){
    					$error=-3;
    				}
    			}
    			//
    		}else{
                $model  = new Model();

    			$this->assign('thisMember',$thisMember);
    			//
    			$now=time();
    			//
    			$noticeCount=M('Member_card_notice')->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->count();
    			$this->assign('noticeCount',$noticeCount);
    			//
    			$member_card_vip_db=M('Member_card_vip');
    			$previlegeCount=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->count();
    			$this->assign('previlegeCount',$previlegeCount);
    			//
    			/*$iwhere 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'3','cardid'=>$thisCard['id'],'is_use'=>'0');
    			$integralCount 	= M('member_card_coupon_record')->where($iwhere)->count();*/
                $integralCount  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_integral'=>'integral'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=integral.id AND record.coupon_type="3" AND integral.enddate >"'.$now.'"')->count();

    			$this->assign('integralCount',$integralCount);
    			

    			/*$cwhere1 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'1','cardid'=>$thisCard['id'],'is_use'=>'0');
    			$couponCount1 	= M('member_card_coupon_record')->where($cwhere1)->count();*/
                $couponCount1  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_coupon'=>'coupon'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=coupon.id AND record.coupon_type="1" ')->count();
                $this->assign('couponCount1',$couponCount1);
    			//
    			/*$cwhere2 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'2','cardid'=>$thisCard['id'],'is_use'=>'0');
    			$couponCount2 	= M('member_card_coupon_record')->where($cwhere2)->count();*/

                $couponCount2  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_coupon'=>'coupon'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=coupon.id AND record.coupon_type="2" AND coupon.enddate >"'.$now.'"')->count();

                $this->assign('couponCount2',$couponCount2);
    			//
    			$todaySigned=$this->_todaySigned();
    			$this->assign('todaySigned',$todaySigned);
    			//
    			$this->assign('userInfo',$userInfo);
    		}
    	}else {
    		$error=-2;
    	}
    	$this->assign('error',$error);
    	$this->display();
    }

    function wallet(){
    	$this->assign('infoType','card');
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	$this->assign('card',$thisCard);
    	$error=0;
    	if ($thisCard){
    		$userinfo_db=M('Userinfo');
    		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    		$userScore=0;
            $userInfo=array();
    		if ($userInfos){
    			$userScore=intval($userInfos[0]['total_score']);
    			$userInfo=$userInfos[0];
    		}
    		$this->assign('userScore',$userScore);
    		//
    		$member_card_create_db=M('Member_card_create');
    		$thisMember=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>intval($_GET['cardid'])))->find();
            $model  = new Model();
    		$this->assign('thisMember',$thisMember);
    		$now=time();
    		$noticeCount=M('Member_card_notice')->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->count();
    		$this->assign('noticeCount',$noticeCount);
    		$member_card_vip_db=M('Member_card_vip');
    		$previlegeCount=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->count();
    		$this->assign('previlegeCount',$previlegeCount);
    	    $integralCount  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_integral'=>'integral'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=integral.id AND record.coupon_type="3" AND integral.enddate >"'.$now.'"')->count();
    		$this->assign('integralCount',$integralCount);
    			
    		/*$cwhere1 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'1','cardid'=>$thisCard['id'],'is_use'=>'0');
    		$couponCount1 	= M('member_card_coupon_record')->where($cwhere1)->count();*/
            $couponCount1  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_coupon'=>'coupon'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=coupon.id AND record.coupon_type="1" ')->count();
            $this->assign('couponCount1',$couponCount1);
    		//
    		/*$cwhere2 		= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>'2','cardid'=>$thisCard['id'],'is_use'=>'0');
    		$couponCount2 	= M('member_card_coupon_record')->where($cwhere2)->count();*/

            $couponCount2  = $model->table(array(C('DB_PREFIX').'member_card_coupon_record'=>'record',C('DB_PREFIX').'member_card_coupon'=>'coupon'))->where('record.token="'.$this->token.'" AND record.wecha_id="'.$this->wecha_id.'" AND record.is_use="0" AND record.cardid='.$thisCard['id'].' AND record.coupon_id=coupon.id AND record.coupon_type="2" AND coupon.enddate >"'.$now.'"')->count();

            $this->assign('couponCount2',$couponCount2);
    		//
    		$todaySigned=$this->_todaySigned();
    		$this->assign('todaySigned',$todaySigned);
    		//
    		$this->assign('userInfo',$userInfo);
    		
    	}else {
    		$error=-2;
    	}
    	$this->assign('error',$error);
    	$this->display();
    }

    public function cardIntro(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	$data=M('Member_card_exchange')->where(array('token'=>$this->token,'cardid'=>$_GET['cardid']))->find();
    	$this->assign('data',$data);
    	//
    	$company_model=M('Company');
		$where=array('token'=>$this->token);
		$thisCompany=$company_model->where($where)->order('isbranch ASC')->find();
		$thisCompany['intro']=str_replace(array('&lt;','&gt;','&quot;','&amp;nbsp;'),array('<','>','"',' '),$thisCompany['intro']);
		$this->assign('thisCompany',$thisCompany);
    	//
    	$this->display();
    }

    public function signscore(){
    	$userinfo_db=M('Userinfo');
    	$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    	$userScore=0;
    	if ($userInfos){
    		$userScore=intval($userInfos[0]['total_score']);
    		$userInfo=$userInfos[0];
    	}
    	$this->assign('userInfo',$userInfo);
    	$this->assign('userScore',$userScore);
    	//
    	$cardinfo=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($cardinfo['cardid'])))->find();

    	$this->assign('thisCard',$thisCard);
    	//
    	$todaySigned=$this->_todaySigned();
    	$this->assign('todaySigned',$todaySigned);
    	//
    	
    	$cardsign_db   = M('Member_card_sign');
    	$now=time();
    	$day=date('d',$now);
    	$year=date('Y',$now);
    	$month=date('m',$now);
    	if (isset($_GET['month'])){
    		$month=intval($_GET['month']);
    	}
    	$firstday = date('Y-m-01', strtotime($now));
    	$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    	$firstSecondOfMonth=mktime(0,0,0,$month,1,$year);
    	$lastSecondOfMonth=mktime(23,59,59,$month,$lastday,$year);
    	$signRecords=$cardsign_db->where('token=\''.$this->token.'\' AND score_type=1 AND wecha_id=\''.$this->wecha_id.'\' AND sign_time>'.$firstSecondOfMonth.' AND sign_time<'.$lastSecondOfMonth)->order('sign_time DESC')->select();
    	$this->assign('signRecords',$signRecords);
    	//
    	$this->display();
    }

    public function addSign(){
    	$signed=$this->_todaySigned();
    	if ($signed){
    		echo'{"success":1,"msg":"您今天已经签到了"}';
    		exit();
    	}
        $cardinfo=M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    	$cardsign_db   = M('Member_card_sign');
    	$where    = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'score_type'=>1);
    	$sign = $cardsign_db->where($where)->order('sign_time desc')->find();
    	//
    	if($sign == null){
    		$cardsign_db->add($where);
    		$sign = $cardsign_db->where($where)->order('id desc')->find();
    	}
    	$get_card=M('member_card_create')->where(array('wecha_id'=>$this->wecha_id,'cardid'=>intval($cardinfo['cardid'])))->find();
    	//
    	if(empty($get_card)){
    		Header("Location: ".C('site_url').'/'.U('Wap/Card/card',array('token'=>$this->token,'wecha_id'=>$this->wecha_id)));
    		exit('领卡后才可以签到.');
    	}
    	//
    	$set_exchange = M('Member_card_exchange')->where(array('token'=>$this->token,'cardid'=>intval($cardinfo['cardid'])))->find();
        $this->assign('set_exchange',$set_exchange);
        //
        $userinfo = M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    	//
    	$data['sign_time']  = time();
    	$data['is_sign']    = 1;
    	$data['score_type'] = 1;
    	$data['token']      = $this->token;
    	$data['wecha_id']   = $this->wecha_id;
    	$data['expense']    = intval($set_exchange['everyday']);
    	$rt=$cardsign_db->where($where)->add($data);
    	//
    	if ($rt){
    		$da['total_score'] = $userinfo['total_score'] +  $data['expense'];
    		$da['sign_score']  = $userinfo['sign_score'] + $data['expense'];
    		$da['continuous']  =  1;
    		//
    		M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->save($da);
    		echo'{"success":1,"msg":"签到成功，成功获取了'.$set_exchange['everyday'].'个积分"}';
    	}else {
    		echo'{"success":1,"msg":"暂时无法签到"}';
    	}
    }

    function _todaySigned(){
    	$signined=0;
    	$now=time();
    	$member_card_sign_db   = M('Member_card_sign');
    	$where    = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'score_type'=>1);
    	$sign = $member_card_sign_db->where($where)->order('sign_time desc')->find();
    	$today = date('Y-m-d',$now);
    	$itoday = date('Y-m-d',intval($sign['sign_time']));
    	if($sign&&$itoday == $today){
    		$signined = 1;
    	}
    	return $signined;
    }
    
    public function _thisCard(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	return $thisCard;
    }
    public function notice(){
    	$this->assign('infoType','notice');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$member_card_notice_db=M('Member_card_notice');
    	$now=time();
    	//
    	$notices=$member_card_notice_db->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->select();
    	if ($notices){
    		$i=0;
    		foreach ($notices as $n){
    			$notices[$i]['content']=html_entity_decode($n['content']);
    			$i++;
    		}
    	}
    	$this->assign('notices',$notices);
    	$this->assign('firstItemID',$notices[0]['id']);
    	$this->display();
    }
    
    public function previlege(){
    	$this->assign('infoType','privelege');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$now=time();
    	//
    	$member_card_vip_db=M('Member_card_vip');
    	$list=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->order('create_time desc')->select();
    	if ($list){
    		$i=0;
    		foreach ($list as $n){
    			$list[$i]['info']=html_entity_decode($n['info']);
    			$i++;
    		}
    	}
    	$this->assign('firstItemID',$list[0]['id']);
    	$this->assign('list',$list);
    	//
    	$this->display();
    }
   
    public function action_usePrivelege(){
    	if (IS_POST){	
			$paytype = intval($_POST['paytype']);
    		$itemid=intval($_POST['itemid']);
    		$db=M('Member_card_vip');
    		$thisItem=$db->where(array('id'=>$itemid))->find();
    		if (!$thisItem){
    			echo'{"success":-2,"msg":"不存在指定特权"}';
    			exit();
    		}
    		//
    		$member_card_set_db=M('Member_card_set');
    		$thisCard=$member_card_set_db->where(array('id'=>intval($thisItem['cardid'])))->find();
    		$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
    		if (!$thisCard){
    			echo'{"success":-3,"msg":"会员卡不存在"}';
    			exit();
    		}
    		//
			$userinfo_db=M('Userinfo');
    		$thisUser = $this->thisUser;
			
            if($paytype == 0){

				$staff_db=M('Company_staff');
				$thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();
	    		
	    		if(empty($thisStaff)){
	    			echo'{"success":-7,"msg":"用户名不存在"}';
	    			exit();
	    		}
                
    			if (md5($this->_post('password'))!=$thisStaff['password']){
    				echo'{"success":-4,"msg":"商家密码错误"}';
    				exit();
    			}else {

                    $now=time();
                    //score
                    $arr=array();
                    $arr['itemid']	= $this->_post('itemid');
                    $arr['token']	= $this->token;
                    $arr['wecha_id']= $this->wecha_id;
                    $arr['expense']	= $this->_post('money');
                    $arr['time']	= $now;
                    $arr['cat']		= 4;
                    $arr['staffid']	= $thisStaff['id'];
                    $arr['notes']	= $this->_post('notes','trim');
                    $arr['score']	= intval($set_exchange['reward'])*$arr['expense'];
                    
                    M('Member_card_use_record')->add($arr);
                    $userinfo_db=M('Userinfo');
                    $thisUser = $this->thisUser;
                    $userArr=array();
                    $userArr['total_score']=$thisUser['total_score']+$arr['score'];
                    $userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
                    $userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
                    
                    $useCount=intval($thisItem['usetime'])+1;
                    $db->where(array('id'=>$itemid))->save(array('usetime'=>$useCount));
                    echo'{"success":1,"msg":"数据提交成功"}';
				}
    			
    		}else{   			   			
                $arr['itemid']	= $this->_post('itemid');
                $arr['wecha_id']= $this->wecha_id;
                $arr['expense']	= $_POST['money'];
                $arr['time']	= time();
                $arr['token']	= $this->token;
                $arr['cat']		= 4;
                $arr['staffid']	= 0;
                $arr['usecount']= 1;
                $set_exchange 	= M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
                $arr['score']	= intval($set_exchange['reward'])*$arr['expense'];
                if($arr['expense'] <= 0){
                    $this->error('请输入有效的金额');
                }
                
                $single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
                
                $record['orderid'] 	= $single_orderid;
                $record['ordername']= '支付除特权外多余款项';
                $record['paytype'] 	= 'CardPay';
                $record['createtime'] = time();
                $record['paid'] 	= 0;
                $record['price'] 	= $arr['expense'];
                $record['token'] 	= $this->token;
                $record['wecha_id'] = $this->wecha_id;
                $record['type'] 	= 0;
                $result = M('Member_card_pay_record')->add($record);
                if(!$result){
                    $this->error('提交记录失败');
                }
                $this->redirect(U('CardPay/pay',array('from'=>'Card','token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id'),'price'=>$arr['expense'],'single_orderid'=>$single_orderid,'orderName'=>'支付除特权外多余款项','redirect'=>'Card/payReturn|itemid:'.$arr['itemid'].',usecount:'.$arr['usecount'].',score:'.$arr['score'].',type:privelege,act=cards,cardid:'.$thisCard['id'])));
			}
    		//
    	}else {
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
    }
    
    public function integral(){
    	$this->assign('infoType','integral');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	$is_use 	= $this->_get('is_use','intval')?$this->_get('is_use','intval'):'0';
    	$now=time();
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$thisCard['id'],'coupon_type'=>'3','is_use'=>"$is_use");
        
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_id,coupon_type,add_time,is_use,over_time')->select();
		
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'cardid'=>$value['cardid'],'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_integral')->where($cwhere)->field('info,pic,statdate,enddate,title,integral,useinfo')->find();
			$cinfo['info'] 	= html_entity_decode($cinfo['info']);
			if($value['over_time']-$now>=0){
                $cinfo['isovertime']=0;
			}else{
                $cinfo['isovertime']=1;
			}
            $data[$key] = array_merge($value,$cinfo);
		}

    	$this->assign('firstItemID',$data[0]['id']);
    	$this->assign('list',$data);
    	$this->assign('is_use',$is_use);
    	$this->display();
    }
    
    function action_useIntergral(){
    	$now=time();
    	if (IS_POST){  		
    		$rwhere 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_type'=>$this->_post('coupon_type','intval'),'id'=>$this->_post('record_id','intval'),'is_use'=>'0');
    		$r_record 	= M('Member_card_coupon_record')->where($rwhere)->find();
    		if (!$r_record){
    			echo'{"success":-8,"msg":"没有找到卷类"}';
    			exit();
    		} 
    		$itemid		= $r_record['coupon_id'];
    		$db 		= M('Member_card_integral');
    		
    		$thisItem	= $db->where('id='.$itemid.' AND statdate<'.$now.' AND enddate>'.$now)->find();
    		
    		if (!$thisItem){ 
    			echo'{"success":-2,"msg":"不存在指定信息"}';
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
    		$thisCard=$member_card_set_db->where(array('id'=>intval($thisItem['cardid'])))->find();
    		if (!$thisCard){
    			echo'{"success":-3,"msg":"会员卡不存在"}';
    			exit();
    		}

            $staff_db=M('Company_staff');
            $thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();
            
            if(empty($thisStaff)){
                echo'{"success":-7,"msg":"用户名不存在"}';
                exit();
            }
            
            if (md5($this->_post('password'))!=$thisStaff['password']){
                echo'{"success":-4,"msg":"商家密码错误"}';
                exit();
            }else {			
                $arr['notes']	= $this->_post('notes','trim');
                $arr['staffid']	= $thisStaff['id'];
                $arr['itemid']	= $itemid;
                //更新记录
                M('Member_card_use_record')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'itemid'=>$r_record['id']))->save($arr);
                $db->where(array('id'=>$itemid))->setInc('usetime',1);
                //优惠劵使用记录
                M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1'));
                echo'{"success":1,"msg":"兑换成功"}';
            }
    	}else {
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
    }
    public function my_coupon(){
    	$this->assign('infoType','coupon');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	
    	$type 	= $this->_get('type','intval')?$this->_get('type','intval'):1;
    	$now	= time();
    	$data 	= array();
        $now 		= time();
        $where1 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'attr'=>'0','type'=>1,'statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
        $couponcount 	= M('Member_card_coupon')->where($where1)->count(); 
        $where1 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'attr'=>'0','type'=>0,'statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
        $cashcount 	= M('Member_card_coupon')->where($where1)->count(); 
        $where1 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
        $integralcount 	= M('Member_card_integral')->where($where1)->count();
        $this->assign('couponCount',$couponcount);
        $this->assign('cashcount',$cashcount);
        $this->assign('integralcount',$integralcount);
    	if($type  == 3){
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
    	}else{
    		$where 	= array('token'=>$this->token,'cardid'=>$thisCard['id'],'attr'=>'0','statdate'=>array('lt',$now),'enddate'=>array('gt',$now),'ispublic'=>'1');
    		if($type == 1){
    			$where['type'] = 1;
    		}else if($type == 2){
    			$where['type'] = 0;
    		}	
           	$data	= M('Member_card_coupon')->where($where)->order('create_time desc')->select();
    		foreach ($data as $k=>$n){
    			$data[$k]['info']	 	= html_entity_decode($n['info']);
    			$cwhere = array('token'=>$this->token,'cardid'=>$thisCard['id'],'coupon_type'=>$type,'coupon_id'=>$n['id']);
    			$count 	= M('Member_card_coupon_record')->where($cwhere)->count();
                $count1=M('Member_card_coupon_record')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'coupon_id'=>$n['id'],'coupon_type'=>$type,'cardid'=>$thisCard['id'],'add_time'=>array('egt',strtotime(date('Y-m-01')))))->count();
                if($data[$k]['ischeap']==1&&$count1>0){
                    $data[$k]['money']=$data[$k]['money']*$data[$k]['discount'];
                }
                $data[$k]['get_count'] 	= $n['people']-$count;//剩余多少张
    			$data[$k]['count'] 	= $n['people'];//总共多少张
    		}
    		
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
    	if($data['coupon_type'] == 3){//礼品券  		
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
            $sign=array();
            $sign['token'] = $this->token;
            $sign['wecha_id'] = $this->wecha_id;
            $sign['sign_time'] = time();
            $sign['is_sign'] = 0;
            $sign['score_type'] = 6;
            $sign['expense'] =intval($integral['integral']);
            M('Member_card_sign')->add($sign);
            M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->setDec('total_score',$integral['integral']);// 修改用户信息表中积分数据

            echo  '兑换成功';
            exit;
    		
    	}else{
            
    		$where 	= array('token'=>$this->token,'cardid'=>$data['cardid'],'id'=>$data['coupon_id']);
    		if($data['coupon_type'] == 1){//优惠券
    			$where['type'] = 1;
    		}else if($data['coupon_type'] == 2){//代金券
    			$where['type'] = 0;
    		}
            $buynum= $this->_post('buynum','intval');
    		$coupon	= M('Member_card_coupon')->where($where)->order('create_time desc')->find();
    		$cwhere = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$data['cardid'],'coupon_type'=>$data['coupon_type'],'coupon_id'=>$data['coupon_id']);
    		$count 	= M('Member_card_coupon_record')->where($cwhere)->count();
            $userinfo=M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
            $days=$coupon['days'];
            $data['over_time']=strtotime(date('Y-m-d',time)."+$days day");
            $balance=$coupon['money']*$buynum;
            if($coupon['ischeap']==1){
                $count1=M('Member_card_coupon_record')->where(array('token'=>$this->token,'wecha_id'=>$data['wecha_id'],'coupon_id'=>$coupon['id'],'coupon_type'=>$data['coupon_type'],'cardid'=>$data['cardid'],'add_time'=>array('egt',strtotime(date('Y-m-01')))))->count();
                if($count1>0){
                    $balance=$coupon['money']*$coupon['discount']*$buynum;
                }
            } 
             if($coupon['id']==30||$coupon['id']==31)
            {
                $count1=M('Member_card_coupon_record')->where(array('token'=>$this->token,'wecha_id'=>$data['wecha_id'],'coupon_id'=>$coupon['id'],'coupon_type'=>$data['coupon_type'],'cardid'=>$data['cardid'],'add_time'=>array('egt',strtotime(date('Y-m-01')))))->count();
                if($count1>0||$buynum>1){    
                    echo  '该券每月最多能购买1张';
                    exit;
                }
            }
            if($userinfo['balance']<$coupon['balance'])
            {
    			echo '会员卡内余额必须大于'.$coupon['balance'].'元才能领取该优惠劵';
    			exit;
            }
            if($userinfo['balance']<$balance)
            {
    			echo '会员卡内余额不足'.$balance.'，请先充值';
                exit;
            }
            //if($coupon['people']-$count <= 0){
            //    echo  '优惠劵已经被领光了';
            //    exit;
            //}
            if (md5($this->_post('password'))!=$userinfo['paypass'])
            {
                echo  '支付密码不正确';
                exit;
            }
            $single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
            $record['orderid'] = $single_orderid;
            $record['ordername'] = '购买优惠券';
            $record['paytype'] = 'CardPay';
            $record['createtime'] = time();
            $record['paytime'] = time();
            $record['paid'] = 1; 
            $record['price'] =$balance;
            $record['token'] = $this->token;
            $record['wecha_id'] = $this->wecha_id;
            $record['type'] = 0;
            $record['notes'] ='购买优惠券获取积分';
            M('Member_card_pay_record')->add($record);// 插入会员卡线下消费
            $create = M('Member_card_create');
            $exchange = M('Member_card_exchange');
            $cardid = $create->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->getField('cardid');
            $cardid = (int)$cardid;
            $reward = $exchange->where(array('token'=>$this->token,'cardid'=>$cardid))->getField('reward');
            $reward = (int)$reward;
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
                    $arr["score"] = intval($set_exchange["reward"]) * $arr["expense"];
                    $thisUser = M('Userinfo')->where(array("token" => $thisCard["token"], "wecha_id" => $arr["wecha_id"]))->find();
                    $userArr = array();
                    $userArr["total_score"] = $thisUser["total_score"] + $arr["score"];//积分
                    $userArr["expensetotal"] = $thisUser["expensetotal"] + $arr["expense"];//消费总额
                    $userArr['balance'] = $userinfo['balance'] - $balance;//余额
                    M('Userinfo')->where(array("token" => $this->token, "wecha_id" => $arr["wecha_id"]))->save($userArr);//更新用户信息
                    M("Member_card_use_record")->add($arr);//插入会员卡使用表
				}
			}
            for($i=0;$i<$buynum;$i++)
            {
                $data['coupon_num']=date('YmdHis',time()).mt_rand(1000,9999);
                M('Member_card_coupon_record')->add($data);//会员优惠券表中增加一条记录
            }
            if($data['coupon_type'] == 2){
                 $juan   = '代金卷';
             }else{
                 $juan   = '优惠劵';
             }
            //else{
            
            //    $result['err'] 	= -1;
            //    $result['info'] = '领取优惠券失败';
            //    echo json_encode($result);
            //    exit;
            //}
            /*模板消息*/
            $model  = new templateNews();
            $dataKey    = 'TM151203';
            $dataArr    = array(
                'first'         => '恭喜您成功购买了'.$buynum.'张'.$juan,
                'keyword1'      => $coupon['title'],
                'keyword2'      => $balance.'元',
                'keyword3'      => date("Y-m-d",time()),
                'wecha_id'      => $this->wecha_id,
                'remark'        => '券的有效期至'.date("Y-m-d",$data['over_time']).',请您在有效期内到南沙爱车港门店体验，如有疑问，请致电020-39053199联系我们,或发消息到微信平台上进行咨询。',
                'url'      => U('Card/wallet',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$userCard['cardid']),true,false,true),
            );
            //Log::write(json_encode($r),Log::DEBUG);
            $model->sendTempMsg($dataKey,$dataArr);

            echo  '您成功购买了'.$buynum.'张'.$juan;
            exit;
    	}        
      
    	

    }

    //会员中心-优惠劵
    public function coupon(){
    	$this->assign('infoType','coupon');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	$type 		= $this->_get('type','intval');
    	$is_use 	= $this->_get('is_use','intval')?$this->_get('is_use','intval'):'0';
    	$now=time();
		$where 	= array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$thisCard['id'],'coupon_type'=>$type,'is_use'=>"$is_use");
        
		$data 	= M('Member_card_coupon_record')->where($where)->field('id,cardid,coupon_id,coupon_type,add_time,is_use,over_time')->select();
		
		foreach($data as $key=>$value){
			$cwhere 		= array('token'=>$this->token,'cardid'=>$value['cardid'],'id'=>$value['coupon_id']);
			$cinfo			= M('Member_card_coupon')->where($cwhere)->field('useinfo,info,pic,statdate,enddate,title,price')->find();
			$cinfo['info'] 	= html_entity_decode($cinfo['info']);
			if($value['over_time']-$now>=0){
                $cinfo['isovertime']=0; 
			}else{
                $cinfo['isovertime']=1;
			}
		    $data[$key] = array_merge($value,$cinfo);
		}
		
    	$this->assign('firstItemID',$data[0]['id']);
    	$this->assign('list',$data);
    	$this->assign('is_use',$is_use);
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
    			echo'{"success":-8,"msg":"没有找到卷类"}';
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
            //if (md5($this->_post('password'))!=$thisStaff['password']){
            //    echo'{"success":-4,"msg":"商家密码错误"}';
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
                $arr['usecount']	= $useTime;
                $arr['notes']		= $this->_post('notes','trim').'线下消费'.$couponname['title'].'一张';
                $arr['score'] 		=0;

                M('Member_card_use_record')->add($arr);	//添加消费券使用记录					
                //优惠劵使用记录
                M('Member_card_coupon')->where(array('id'=>$itemid))->setInc('usetime',1);//优惠券使用次数加1
                $couponnum=M('Member_card_coupon_record')->where($rwhere)->find();
                M('Member_card_coupon_record')->where($rwhere)->save(array('use_time'=>time(),'is_use'=>'1'));//会员优惠券记录修改为已使用
               
                $model  = new templateNews();
                $dataKey    = 'TM151214';
                $dataArr    = array(
                    //'first'         => '您消费'.$couponname['title'].'['.$couponnum["coupon_num"].']'.'一张',
                    'productType'      =>'券名称',
                    'name'      =>$couponname['title'],
                    'certificateNumber'      =>$couponnum["coupon_num"],
                    'number'      =>'1张',
                    'wecha_id'      => $this->wecha_id,
                    'remark'        => '注意：此消息作为您本次消费凭证，请妥善保存，如有疑问，请致电020-39053199联系我们,或发消息到微信平台上进行咨询。',
                    'url'      => U('Card/wallet',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>$couponname['cardid']),true,false,true),
                );
                //Log::write(json_encode($r),Log::DEBUG);
                $model->sendTempMsg($dataKey,$dataArr);
                echo '{"success":1,"msg":"线下消费成功"}';	
                exit;
			}                 
        }else {		
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
	}
    
    public function expense(){
    	$userinfo_db=M('Userinfo');
    	$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    	$userScore=0;
    	if ($userInfos){
    		$userScore=intval($userInfos[0]['total_score']);
    		$userInfo=$userInfos[0];
    	}  
    	$this->assign('userInfo',$userInfo);
    	$this->assign('userScore',$userScore);
    	//
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	
    	$db   = M('Member_card_use_record');
    	$now=time();
    	//$day=date('d',$now);
    	//$year=date('Y',$now);
    	$month=date('m',$now);
    	if (isset($_GET['month'])){
    		$month=intval($_GET['month']);
    	}
        $nowY = date('Y');
        $start = strtotime($nowY."-".$month."-01");
        $last = strtotime(date('Y-m-d',$start)." +1 month ");
    	$records=$db->where('token=\''.$this->token.'\' AND wecha_id=\''.$this->wecha_id.'\' AND time>'.$start.' AND time<'.$last)->order('time DESC')->select();
    	$this->assign('records',$records);
    	//
    	$this->display();
    }
     
    ////////////////////////////////////////////////////////////////////////////////////////////////
	public function request(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//卡号
			$card=M('member_card_create')->where(array('token'=>$token))->order('id asc')->find();
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);			
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function get_card(){
		$token=$this->_get('token');
		$wecha_id=$this->_get('wecha_id');
		$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->find();
		if($card){
			header('Location:'.rtrim(C('site_url'),'/').U('Wap/Card/vip',array('token'=>$token,'wecha_id'=>$wecha_id)));
		}			
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		
		$get_card=M('member_card_create')->where(array('wecha_id'=>$wecha_id))->find();

		if($get_card!=false){
			Header("Location: ".C('site_url').'/'.U('Wap/Card/vip',array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))); 
		}
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//联系方式
            
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$contact=M('company')->where(array('token'=>$token,'branch'=>0))->find();
			$this->assign('contact',$contact);
			$this->assign('info',$info);
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function info(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			$info['description']=nl2br($info['description']);
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			//我的卡号
			$mycard=M('member_card_create')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))->find();
			$this->assign('mycard',$mycard);
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function vip(){
		$token=$this->_get('token');
		$wecha_id=$this->_get('wecha_id');
		$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->find();
		if($card==false){
			header('Location:'.rtrim(C('site_url'),'/').U('Wap/Card/get_card',array('token'=>$token,'wecha_id'=>$wecha_id)));
		}
		//
        $agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}   
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//卡号
			$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$this->_get('wecha_id')))->find();
			//var_dump($card);exit;
			//dump(array('token'=>$token,'wecha_id'=>$this->get('wecha_id')));
			//联系方式
			$contact=M('company')->where(array('token'=>$token,'branch'=>0))->find();
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);			
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//dump($data);
			$this->assign('card',$data);
			//特权服务
			$vip=M('member_card_vip')->where(array('token'=>$token))->order('id desc')->find();
			//dump($vip);
			$this->assign('vip',$vip);
			//优惠卷
			$coupon=M('member_card_coupon')->where(array('token'=>$token))->find();
			$this->assign('coupon',$coupon);
			//兑换
			$integral=M('member_card_integral')->where(array('token'=>$token))->find();
			$this->assign('integral',$integral);
		}else{
			$this->error('无此信息');
		}
        
		$this->display();
        
	}
	public function addr(){
        $agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
        
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			//$addr=M('member_card_contact')->where(array('token'=>$token))->select();
			//if (!$addr){
			$addr=M('Company')->where(array('token'=>$token))->order('isbranch ASC')->select();
			if ($addr){
				$i=0;
				foreach ($addr as $a){
					$addr[$i]['info']=$a['address'];
					$addr[$i]['tel']=$a['tel'];
					$i++;
				}
			}
			//}
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			//我的卡号
			$mycard=M('member_card_create')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))->find();
			$this->assign('mycard',$mycard);
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('addr',$addr);
		}else{
			$this->error('无此信息');
		}
		$this->display();
        
	}
	//充值页面
	public function topay(){
		$config = M('Alipay_config')->where(array('token'=>$this->token))->find();
		
		$info['cardid'] = $this->_get('cardid','intval');
		$info['token'] = $this->_get('token');
		$info['wecha_id'] = $this->_get('wecha_id');
		$member_card_set_db=M('Member_card_set');
		$member_card_create_db=M('Member_card_create');
		$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
		$card = $member_card_create_db->field('number')->where(array('token'=>$info['token'],'wecha_id'=>$info['wecha_id']))->find();
		$company_model=M('Company');
		
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
		$token = $this->token;
		$thisCompany=$company_model->where("token = '$token'")->find();
		
		
		$this->assign('thisCompany',$thisCompany);
		$this->assign('info',$info);
		$this->assign('card',$card);
		$this->assign('thisCard',$thisCard);
		$this->display();
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

    // 消费返回
	public function consumeReturn(){
		$orderid = $_GET['orderid'];
		$token = $_GET['token'];
		$wecha_id = $_GET['wecha_id'];
		$record = M('member_card_pay_record');
		$order = $record->where("orderid = '$orderid' AND token = '$token' AND wecha_id = '$wecha_id'")->find();
        $cardinfo   = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();

		if($order){
			if($order['paid'] == 1){
				$record->where("orderid = '$orderid'")->setField('paytime',time());
                //$lastid = M('Member_card_use_record')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->order('id DESC')->getField('id');
                //if($this->_get('type') == 'coupon'){
                //    M('Member_card_coupon')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime',(int)$this->_get('usecount'));
                //    M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField(array('usecount'=>(int)$this->_get('usecount'),'cat'=>1));
                //}elseif($this->_get('type') == 'privelege'){
                //    M('Member_card_vip')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime');
                //    M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField('cat',4);
                //}
                
                $cardinfo   = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
                //模板消息
                $model  = new templateNews();
                $dataKey    = 'OPENTM201444641';
                $dataArr    = array(
                    'first'         => '您好，您的'.$order['ordername'].'购买已成功。',
                    'keyword1'      => $orderid,
                    'keyword2'      => $order['price'].'元',
                    'keyword3'      => $order['paytype']=='CardPay'?'会员卡支付':'微信支付',
                    'wecha_id'      => $wecha_id,
                    'remark'        => '如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                    'url'      => U('Store/myinfo',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$cardinfo['cardid']),true,false,true),
                );
                $model->sendTempMsg($dataKey,$dataArr);
				$this->success('支付成功',U('Store/myinfo',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$cardinfo['cardid'])));
                /*'OPENTM201444641' =>
                array(
				'name'=>'会员支付提醒',
				'vars'=>array('first','keyword1','keyword2','keyword3','remark'),
				'content'=>'{{first.DATA}}
                订单号：{{keyword1.DATA}}
                消费金额：{{keyword2.DATA}}
                付款方式：{{keyword3.DATA}}
                {{remark.DATA}}' 
               */

			}else{
				exit('支付失败');
			}
            
		}else{
			exit('订单不存在');
		}
        
	}
	
    public function scanpay(){
        if(IS_POST){
            $price 		= $_POST['price'];
            $orderid 	= $this->_get('orderid');
            $record 	= M('Member_card_pay_record');
            if($orderid == '' && $price <= 0){
                $this->error('请填写正确的充值金额');
            }

            $token = $this->_get('token');
            $wecha_id = $this->_get('wecha_id');
            $_POST['wecha_id'] = $wecha_id;
            $_POST['token'] = $token;
            $_POST['createtime'] = time();
            $_POST['orderid'] = date('YmdHis',time()).mt_rand(1000,9999);
            $_POST['ordername'] ='临时客户支付';
            
            if($record->create($_POST)){
                if($record->add($_POST)){
                    header("Location:/wxpay/index.php?g=Wap&m=Weixin&a=pay&price=" . $price . "&orderName=" . $_POST['ordername'] . "&single_orderid=" . $_POST['orderid'] . "&showwxpaytitle=1&from=Card"  . "&token=" . $this->token . "&wecha_id=" . $this->wecha_id);
                }
            }else{
                $this->error('系统错误');
            }
        }
        else{
           $this->display();
        }
		
	}

	//充值处理
	public function payAction(){
		$price 		= $_POST['price'];
		$orderid 	= $this->_get('orderid');
		$record 	= M('Member_card_pay_record');
		if($orderid == '' && $price <= 0){
			$this->error('请填写正确的充值金额');
		}

		$token = $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
		if($orderid != ''){
			$res = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND orderid = $orderid AND paid = 0")->find();
            
			if($res){
				$this->success('提交成功，正在跳转支付页面..',U('Alipay/pay',array('from'=>'Card','orderName'=>$res['ordername'],'single_orderid'=>$res['orderid'],'token'=>$res['token'],'wecha_id'=>$res['wecha_id'],'price'=>$res['price'])));
			}else{
				$this->error('无此订单');
			}
		}
        
		
		$_POST['wecha_id'] = $wecha_id;
		$_POST['token'] = $token;
		$_POST['shop'] = $this->getshopname();
		$_POST['createtime'] = time();
		$_POST['orderid'] = date('YmdHis',time()).mt_rand(1000,9999);
		$_POST['ordername'] = $_POST['number'].' 充值';
		
		if($record->create($_POST)){
			if($record->add($_POST)){
				$this->success('提交成功，正在跳转支付页面..',U('Alipay/pay',array('from'=>'Card','orderName'=>$_POST['ordername'],'single_orderid'=>$_POST['orderid'],'token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],'price'=>$price)));
			}
		}else{
			$this->error('系统错误');
		}
		
	}
    private function getshopname(){
        $arr=array();
        $shops=M('company')->where(array('token'=>$this->token))->select();
        $user=M('userinfo')->where(array('wecha_id'=>$this->wecha_id,'token'=>$this->token))->find();
        foreach($shops as $shop){
            $key=$shop['shortname'];
            $arr["$key"]=$this->distance($user['location_y'],$user['location_x'],$shop['latitude'],$shop['longitude']);
        }
        return  array_search(min($arr),$arr);
    }
    private function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
    {
        $rad = floatval(M_PI / 180.0);

        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;

        $theta = $lon2 - $lon1;

        $dist = acos(sin($lat1) * sin($lat2) +
                    cos($lat1) * cos($lat2) * cos($theta)
                );

        if ($dist < 0 ) {
            $dist += M_PI;
        }

        return $dist = $dist * $radius;
    }

    private function changecarinfo($user,$number)
    {
        $car=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$user['carno']))->find();
        $lb='1星客户';
        if(!empty($car)){
            $item['车主']=$number;
            $item['联系人']=$user['truename'];
            $item['联系电话']=$user['tel'];
            $item['客户类别']=$lb;
            M('车辆档案','dbo.','difo')->where(array('客户ID'=>$car['客户ID']))->save($item);
            M('维修','dbo.','difo')->where(array('客户ID'=>$car['客户ID']))->save($item);
            $czinfo['名称']=$number;
            $czinfo['会员']=1;
            $czinfo['等级']='★';
            $czinfo['会员编号']=$number;
            $czinfo['入会日期']=date('Y-m-d',time());
            $czinfo['联系人']=$user['truename'];
            $czinfo['联系电话']=$user['tel'];
            $czinfo['手机号码']=$user['tel'];
            $czinfo['类别']=$lb;
            M('往来单位','dbo.','difo')->where(array('ID'=>$car['客户ID']))->save($czinfo);
        }        
    }

    private function change()
    {   
        $wecha_id=$this->wecha_id;
        $member_card_set_db=M('Member_card_create'); 
        $thisCard=$member_card_set_db->join('tp_member_card_set on tp_member_card_create.cardid=tp_member_card_set.id')->where(array('wecha_id'=>$wecha_id))->find(); 
        if($thisCard['grade']==1){
            $newcard=M('member_card_set')->where(array('grade'=>2))->find();
            $card=M('Member_card_create')->field('id,number')->where("cardid=".$newcard['id']." and wecha_id = ''")->order('id ASC')->find();
            $now=time();
            if($card)
            {
                $gwhere = array('token'=>$this->token,'cardid'=>$newcard['id'],'is_open'=>'1','start'=>array('lt',$now),'end'=>array('gt',$now));
                $gifts 	= M('Member_card_gifts')->where($gwhere)->select();
                foreach($gifts as $key=>$value){
                    if($value['type'] == "1"){
                        //赠积分
                        $arr=array();
                        $arr['itemid']	= 0;
                        $arr['token']	= $this->token;
                        $arr['wecha_id']= $wecha_id;
                        $arr['expense']	= 0;
                        $arr['time']	= $now;
                        $arr['cat']		= 3;
                        $arr['staffid']	= 0;
                        $arr['notes']	= '开卡赠送积分';
                        $arr['score']	= $value['item_value'];
                        
                        M('Member_card_use_record')->add($arr);
                        M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->setInc('total_score',$arr['score']);
                    }else{
                        $data['token']		= $this->token;
                        $data['wecha_id']	= $wecha_id;
                        $data['coupon_id']	= $value['item_value'];
                        $data['is_use']		= '0';
                        $data['cardid']		= $card['id'];
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
                M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->save(array('wecha_id'=>''));
                M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$wecha_id));
                return $card['number'];
            }else{
                return 0;
            }
        }
        return 0;
        
    }

	//充值返回
	public function payReturn(){
		$orderid = $_GET['orderid'];
		$token = $_GET['token'];
		$wecha_id = $_GET['wecha_id'];
		$record = M('member_card_pay_record');
		$order = $record->where("orderid = '$orderid' AND token = '$token' AND wecha_id = '$wecha_id'")->find();
        $cardinfo   = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
		
		if($order){
			if($order['paid'] == 1){
				$record->where("orderid = '$orderid'")->setField('paytime',time());
				if($order['type'] == 1){
                    if($order['price']>=500||$wecha_id=='ohD3dviFloHSvcl9ieoXFibqPFJM')
                    {   
                        $cardnumber=$this->change();
                        if($cardnumber!='0'){
                            $uinfo=M('Userinfo')->where("wecha_id = '$wecha_id' AND token = '$token'")->find();
                            $this->changecarinfo($uinfo,$cardnumber);
                        }

                    }
                    /*模板消息*/
                    $model  = new templateNews();
                    $dataKey    = 'TM151125';
                    $dataArr    = array(
                        'first'         => '您好，您已成功进行会员卡充值。',
                        'accountType'      => '会员卡号',
                        'account'      => $cardinfo['number'],
                        'amount'      => $order['price'],
                        'result'      => '充值成功',
                        'wecha_id'      => $wecha_id,
                        'remark'        => '会员充值如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                        'url'      => U('Store/myinfo',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$cardinfo['cardid']),true,false,true),
                    );
                    $model->sendTempMsg($dataKey,$dataArr);
				}else{// 消费
					$lastid = M('Member_card_use_record')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->order('id DESC')->getField('id');
					if($this->_get('type') == 'coupon'){
						M('Member_card_coupon')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime',(int)$this->_get('usecount'));
						M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField(array('usecount'=>(int)$this->_get('usecount'),'cat'=>1));
					}elseif($this->_get('type') == 'privelege'){
						M('Member_card_vip')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime');
						M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField('cat',4);
					}
                    $model  = new templateNews();
                    $dataKey    = 'OPENTM201444641';
                    $dataArr    = array(
                        'first'         => '您好，您的'.$order['ordername'].'已成功。',
                        'keyword1'      => $orderid,
                        'keyword2'      => $order['price'].'元',
                        'keyword3'      => $order['paytype']=='CardPay'?'会员卡支付':'微信支付',
                        'wecha_id'      => $wecha_id,
                        'remark'        => '如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                        'url'      => U('Store/myinfo',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$cardinfo['cardid']),true,false,true),
                    );
                    $model->sendTempMsg($dataKey,$dataArr);
					
				}
					$this->success('支付成功',U('Store/myinfo',array('token'=>$token,'wecha_id'=>$wecha_id,'cardid'=>$cardinfo['cardid'])));

			}else{
				exit('支付失败');
			}
            
		}else{
			exit('订单不存在');
		}
        
	}
 
	//充值消费记录
	public function payRecord(){

		
		$token = $this->token;
		$wecha_id = $this->wecha_id;

		$record = M('Member_card_pay_record');

    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$token,'id'=>intval($_GET['cardid'])))->find();

		$m = $this->_get('month','intval');
		
		if($m != ''){
			$nowY = date('Y');
			$start = strtotime($nowY."-".$m."-01");
			$last = strtotime(date('Y-m-d',$start)." +1 month");
			$list = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND createtime < $last AND createtime > $start")->order('createtime DESC')->select();
		}else{
			$list = $record->where("token = '$token' AND wecha_id = '$wecha_id'")->order('createtime DESC')->select();
		}

		
		$balance = M('Userinfo')->field('balance')->where("token = '$token' AND wecha_id = '$wecha_id'")->find();
		

		
    	$member_card_create_db=M('Member_card_create');
		$company_model=M('Company');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$thisCompany=$company_model->where("token = '$token'")->find();
		
		$this->assign('thisCompany',$thisCompany);
		
		$this->assign('cardsCount',$cardsCount);
		
		$this->assign('balance',$balance['balance']);
		$this->assign('thisCard',$thisCard);
		$this->assign('list',$list);
		$this->assign('cardid',$this->_get('cardid','intval'));
		$this->display();
	}
}
?>