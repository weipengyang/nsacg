<?php

class RepastAction extends WapAction {

    public $token;
    public $wecha_id = '';
    //public $session_dish_user;
    public $_cid = 0;
    public $isMember = 0;
    public $orid = 0;

    public function _initialize() {
        parent::_initialize();
       /* $checkFunc = new checkFunc();
        if (!function_exists('fdsrejsie3qklwewerzdagf4ds')) {
            exit('error-4');
        }
        $checkFunc->cfdwdgfds3skgfds3szsd3idsj();*/
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $this->orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        $this->_cid = $_SESSION["session_shop_{$this->token}"];
        $this->_cid = $this->_cid > 0 ? $this->_cid : 0;
        $this->assign('cid', $this->_cid);
        $this->assign('orid', $this->orid);
        $this->isMember = $this->getCardInfo();
        $this->isMember = $this->isMember ? 1 : 0;
    }

    /**
     * 餐厅分布
     */
    public function index() {
        $company = M('Company')->where("token='{$this->token}' AND display=1")->select();
        $st = $this->_get('st') ? intval($this->_get('st', "trim")) : 1;
        if (count($company) == 1) {
            $this->redirect(U('Repast/ShopPage', array('token' => $this->token, 'cid' => $company['0']['id'], 'wecha_id' => $this->wecha_id)));
        } else {
            $nowlat = $this->_get('nowlat') ? floatval($this->_get('nowlat', "trim")) : 0;
            $nowlng = $this->_get('nowlng') ? floatval($this->_get('nowlng', "trim")) : 0;
            if (($nowlat > 0) && ($nowlng > 0)) {
                $tmpe = array();
                foreach ($company as $kk => $vv) {
                    $tmpd = $this->getDistance_map($nowlat, $nowlng, $vv['latitude'], $vv['longitude']);
                    $tmpdstr = $tmpd > 1000 ? (round(floatval($tmpd / 1000), 2) . ' km') : (intval($tmpd) . " m");
                    $vv['distance'] = $tmpd;
                    $vv['distancestr'] = $tmpdstr;
                    $company[$kk] = $vv;
                    $tmpe[$kk] = $tmpd;
                }
                asort($tmpe);
                $newCy = array();
                foreach ($tmpe as $tk => $tv) {
                    $newCy[] = $company[$tk];
                }
                $company = !empty($newCy) ? $newCy : $company;
                $this->assign('is_dwei', true);
            }
            $this->assign('select', $st);
            $this->assign('company', $company);
            $this->assign('metaTitle', '餐厅分布');
            $this->display('newindex');
        }
    }

    /*     * 计算两经纬度间的距离* */

    private function getDistance_map($lat_a, $lng_a, $lat_b, $lng_b) {
        //R是地球半径（米）
        $R = 6377830;
        $pk = doubleval(180 / 3.1415926);

        $a1 = doubleval($lat_a / $pk);
        $a2 = doubleval($lng_a / $pk);
        $b1 = doubleval($lat_b / $pk);
        $b2 = doubleval($lng_b / $pk);

        $t1 = doubleval(cos($a1) * cos($a2) * cos($b1) * cos($b2));
        $t2 = doubleval(cos($a1) * sin($a2) * cos($b1) * sin($b2));
        $t3 = doubleval(sin($a1) * sin($b1));
        $tt = doubleval(acos($t1 + $t2 + $t3));

        return round($R * $tt);
    }

    /** 获取公司信息* */
    private function getCompany($cid, $cache = true) {
        $company = $_SESSION["session_shop{$cid}_{$this->token}"];
        $company = !empty($company) ? unserialize($company) : false;
        if ($cache && !empty($company)) {
            return $company;
        } else {
            $company = M('Company')->where(array('token' => $this->token, 'id' => $cid))->find();
            if (empty($company) || !is_array($company)) {
                $this->redirect(U('Repast/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
            }
            if ($cache) {
                $_SESSION["session_shop{$cid}_{$this->token}"] = !empty($company) ? serialize($company) : '';
            }
            return $company;
        }
    }

    /** 获取餐厅配置信息* */
    private function getDishCompany($cid, $cache = true) {
        $DishC = $_SESSION["session_dish{$cid}_{$this->token}"];
        $DishC = !empty($DishC) ? unserialize($DishC) : false;
        if ($cache && !empty($DishC)) {
            return $DishC;
        } else {
            $DishC = M('Dish_company')->where(array('cid' => $cid))->find();
            if (!empty($DishC)) {
                $DishC['stimestr'] = $DishC['starttime'] > 0 ? date('H:i', $DishC['starttime']) : '00:00';
                $DishC['etimestr'] = $DishC['endtime'] > 0 ? date('H:i', $DishC['endtime']) : '23:59';
            } else {
                $DishC['stimestr'] = '00:00';
                $DishC['etimestr'] = '23:59';
            }
            if ($cache) {
                $_SESSION["session_dish{$cid}_{$this->token}"] = !empty($DishC) ? serialize($DishC) : '';
            } else {
                $_SESSION["session_dish{$cid}_{$this->token}"] = '';
            }
            return $DishC;
        }
    }

    /**获取dish_name表信息**/
	private function GetCanName($cid,$id=0,$cache = true){
		$NameC = $_SESSION["session_nameC{$cid}_{$this->token}"];
        $NameC = !empty($NameC) ? unserialize($NameC) : false;
        if ($cache && !empty($NameC)) {
			if(($id>0) && array_key_exists($id,$NameC)){
			   return  $NameC[$id];
			}elseif(($id>0) && !array_key_exists($id,$NameC)){
			   return  false;
			}
            return  $NameC;
        } else {
            $NameC = M('Dish_name')->where(array('cid' => $cid,'token'=>$this->token))->select();
			if(!empty($NameC)){
			   $tmparr=array();
			    foreach($NameC as $vv){
			       $tmparr[$vv['id']]=$vv;
			    }
			   $NameC=$tmparr;
			}
            if ($cache) {
                $_SESSION["session_nameC{$cid}_{$this->token}"] = !empty($NameC) ? serialize($NameC) : '';
            } else {
                $_SESSION["session_nameC{$cid}_{$this->token}"] = '';
            }
          	if(($id>0) && array_key_exists($id,$NameC)){
			   return  $NameC[$id];
			}elseif(($id>0) && !array_key_exists($id,$NameC)){
			   return  false;
			}
            return  $NameC;
        }
	}
    /*     * *门店页面** */

    public function ShopPage() {
        $cid = $this->_get('cid') ? intval($this->_get('cid', 'trim')) : 0;
        $dt = $this->_get('dt');
        $dt = !empty($dt) ? urldecode(trim($dt)) : '';
        $_SESSION["session_dt{$cid}_{$this->token}"] = $dt;
        $company = $this->getCompany($cid);
        $DishC = $this->getDishCompany($cid);
        $this->assign('DishC', $DishC);
        $this->assign('dt', $dt);
        $this->assign('company', $company);
        $this->assign('metaTitle', $company['name']);
        $this->assign('cid', $cid);
        $this->display();
    }

    /*     * *门店详细页面** */

    public function DetailShopPage() {
        $cid = $this->_get('cid') ? intval($this->_get('cid', 'trim')) : 0;
        $company = $this->getCompany($cid);
        $DishC = $this->getDishCompany($cid);
        $tmp = $DishC['stimestr'] . " ~ " . $DishC['etimestr'];
        $this->assign('company', $company);
        $this->assign('dt', $_SESSION["session_dt{$cid}_{$this->token}"]);
        $this->assign('DishC', $DishC);
        $this->assign('openstr', $tmp);
        $this->assign('metaTitle', $company['name']);
        $this->assign('cid', $cid);
        $this->display();
    }

    /** 地图* */
    public function companyMap() {
        if (C('baidu_map')) {
            $isamap = 0;
        } else {
            $isamap = 1;
        }
        $this->apikey = C('baidu_map_api');
        $this->assign('apikey', $this->apikey);
        $cid = $this->_get('cid') ? intval($this->_get('cid', "trim")) : 0;
        $company = $this->getCompany($cid, false);
        $this->assign('thisCompany', $company);
        if (!$isamap) {
            $this->display();
        } else {
            $this->amap = new amap();
            $link = $this->amap->getPointMapLink($company['longitude'], $company['latitude'], $company['name']);
            header('Location:' . $link);
        }
    }

    /**
     * 点菜页面
     */
    public function dishMenu() {
        $cid = $this->_get('cid') ? intval($this->_get('cid', "trim")) : 0;
        $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        $tid = $this->_get('tid') ? intval($this->_get('tid', "trim")) : 0;
        if ($tid > 0) {
            $_SESSION["session_tid{$cid}_{$this->token}"] = $tid;
        }
        if ($orid > 0) {
            $sessionoridK = "session_orid{$cid}_{$this->token}";
            $_SESSION[$sessionoridK] = $orid;
        }
        $outset = $this->getDishCompany($cid, false);
        $nows = strtotime(date('Y-m-d H:i'));
        if ($outset['starttime'] > 0) {
            $sf = date('H:i', $outset['starttime']);
            $setime = strtotime(date('Y-m-d ') . $sf);
            if ($nows < $setime) {
                $this->error('抱歉！尚未到营业时间！');
                exit();
            }
        }
        if ($outset['endtime'] > 0) {
            $ef = date('H:i', $outset['endtime']);
            $setime = strtotime(date('Y-m-d ') . $ef);
            if ($nows > $setime) {
                $this->error('抱歉！已经过了营业时间了！');
                exit();
            }
        }
        $company = $this->getCompany($cid);
        if ($company && is_array($company)) {
            $_SESSION["session_shop_{$this->token}"] = $cid;
        }

        /** 分类* */
        $dish_sort = M('Dish_sort')->where(array('cid' => $cid))->order("`sort` ASC")->select();
        /** 菜单* */
        $dish = M('Dish')->where(array('cid' => $cid, 'isopen' => 1))->order("`sort` ASC")->select();
        /*         * 统计这个月的销售情况,菜销售的份数**** */
        $starttime = strtotime(date('Y-m') . "-01 00:00:00"); /*         * 月开始时间* */
        $t = date('t');
        $endtime = strtotime(date('Y-m') . "-" . $t . " 23:59:59"); /*         * 月结束时间* */
        $Model = new Model();
        $sqlstr = "select cid,did,sum(nums) as tnums from " . C('DB_PREFIX') . "dishout_salelog where cid=" . $cid . " AND token='" . $this->token . "' AND addtime>=" . $starttime . " AND addtime<=" . $endtime . " group by did";
        $tmp = $Model->query($sqlstr);
        $newtmp = array();
        if (!empty($tmp)) {
            foreach ($tmp AS $vv) {
                $newtmp[$vv['did']] = $vv['tnums'];
            }
        }
        $fenleiarr = array();
        if (is_array($dish_sort)) {
            foreach ($dish_sort as $sk => $sv) {
                $fenleiarr[$sv['id']] = $sv['name'];
            }
        }
        /*         * 取喜欢的菜品* */
        $loves = D('Dish_like')->where(array('cid' => $cid, 'wecha_id' => $this->wecha_id))->field('id,did')->select();
        $lovesarr = array();
        if (!empty($loves) && is_array($loves)) {
            foreach ($loves as $lv) {
                $lovesarr[] = $lv['did'];
            }
        }

        $sessionK = "session_dishs{$cid}_{$this->token}";
        $isHave = $_SESSION[$sessionK];
        $isHave = $isHave && !empty($isHave) ? unserialize($isHave) : array();
        $disharr = $dztjtmp = array();
        if (is_array($dish)) {
            foreach ($dish as $dk => $dv) {
                $dv['sortname'] = $fenleiarr[$dv['sid']];
                $dv['sortname'] = $dv['sortname'] ? $dv['sortname'] : '无';
                if (($outset['discount'] > 0) && $this->isMember && $dv['isdiscount']) {
                    $dv['zkprice'] = $dv['price'] * $outset['discount'] / 10;
                }
                if (array_key_exists($dv['id'], $isHave)) {
                    $dv['ornum'] = $isHave[$dv['id']]['num'];
                }
                if (array_key_exists($dv['id'], $newtmp)) {
                    $dv['m_sale'] = $newtmp[$dv['id']];
                } else {
                    $dv['m_sale'] = 0;
                }
                /*                 * 我喜欢的菜* */
                $dv['mylove'] = in_array($dv['id'], $lovesarr) ? 1 : 0;
                if (array_key_exists($dv['sid'], $disharr)) {
                    $disharr[$dv['sid']][] = $dv;
                } else {
                    $disharr[$dv['sid']] = array();
                    $disharr[$dv['sid']][] = $dv;
                }
                /* if($dv['ishot']==1){
                  if (array_key_exists($dv['id'], $isHaveTj)) {
                  $dv['ornum'] = $isHaveTj[$dv['id']]['ornum'];
                  }else{
                  $dv['ornum'] = 0;
                  }
                  $dztjtmp[]=$dv;
                  $dztj=true;
                  } */
            }
        }
        /* $disharr['dztj']=!empty($dztjtmp) ? $dztjtmp :array(); */
        ksort($disharr); /*         * 对数组按照键名排序，保留键名到数据的关联* */
        //print_r($disharr);
        $this->assign('kconoff', $outset['kconoff']);
        $this->assign('isMember', $this->isMember);
        $this->assign('discount', $outset['discount']);
        $this->assign('cid', $cid);
        $this->assign('orid', $orid);
        $this->assign('fenleiarr', $fenleiarr);
        $this->assign('disharr', $disharr);
        $this->assign('company', $company);
        $this->assign('metaTitle', $company['name']);
        $this->display();
    }

    //json格式输出封装函数
    private function dexit($data = '') {
        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }
        exit();
    }

    /**
     * 对某个菜进行喜欢标记操作
     */
    public function doLike() {
        if (empty($this->wecha_id)) {
            $this->dexit(array('status' => 0));
        }
        $id = $this->_post('did') ? intval($this->_post('did', 'trim')) : 0;
        $islove = $this->_post('islove') ? intval($this->_post('islove', 'trim')) : 0;
        if ($id && $this->_cid) {
            $dishLike = D('Dish_like');
            $data = array('did' => $id, 'cid' => $this->_cid, 'wecha_id' => $this->wecha_id);
            if ($islove) {
                $dishLike->add($data);
            } else {
                $dishLike->where($data)->delete();
                $this->dexit(array('status' => 1));
            }
        }
        $this->dexit(array('status' => 0));
    }

    /**
     * 处理提交的订单信息
     */
    public function processOrder() {
        $dishtmp = $_POST['cart'];
        $tmpcid = intval($_POST['mycid']);
        $disharr = array();
        if (($tmpcid > 0) && ($tmpcid == $this->_cid)) {
            foreach ($dishtmp as $kk => $vv) {
                $count = $vv['count'] ? intval($vv['count']) : 0;
                if ($count > 0) {
                    $disharr[$vv['id']] = array('id' => $vv['id'], 'num' => $count);
                }
            }
            if (empty($disharr)) {
                $this->dexit(array('error' => 1, 'msg' => '您尚未点菜！'));
            }
            $sessionK = "session_dishs{$tmpcid}_{$this->token}";
            $_SESSION[$sessionK] = serialize($disharr);
            $_SESSION["session_shop_{$this->token}"] = $tmpcid;
            /* Header("Location:".$this->siteUrl . U('Repast/sureOrder', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $tmpcid))); */
            $this->dexit(array('error' => 0, 'msg' => ''));
        } else {
            $this->dexit(array('error' => 1, 'msg' => '提交信息出错了'));
        }
    }

    /**
     * 处理提交的订单信息
     */
    public function sureOrder() {
        $isclean = $this->_get('isclean', 'trim');
        $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        $isclean = $isclean ? intval($isclean) : 0;
        $sessionK = "session_dishs{$this->_cid}_{$this->token}";
        if ($isclean == 1) {
            $_SESSION[$sessionK] = '';
            $disharr = '';
        } else {
            $disharr = unserialize($_SESSION[$sessionK]);
        }
        $outset = $this->getDishCompany($this->_cid);
        if (!empty($disharr)) {
            $idarr = array_keys($disharr);
            sort($idarr);
            $idstr = implode(',', $idarr);
            $db_dish = M('Dish');
            $dish = $db_dish->where('id in(' . $idstr . ') and cid="' . $this->_cid . '" and isopen="1"')->order("`sort` ASC")->select();
            foreach ($dish as $val) {
                $index = $val['id'];
                if (($outset['discount'] > 0) && $this->isMember && $val['isdiscount']) {
                    $val['zkprice'] = $val['price'] * $outset['discount'] / 10;
                }
                $disharr[$index] = array_merge($disharr[$index], $val);
            }
        }
        unset($outset['bookingtime'], $outset['imgs']);
        $company = $this->getCompany($this->_cid);
        $allmark = $_SESSION["allmark" . $this->_cid . $this->token];
        $allmark = !empty($allmark) ? htmlspecialchars_decode($allmark, ENT_QUOTES) : '';
        $this->assign('kconoff', $outset['kconoff']);
        $this->assign('isMember', $this->isMember);
        $this->assign('discount', $outset['discount']);
        $this->assign('cid', $this->_cid);
        $this->assign('orid', $orid);
        $this->assign('ordishs', $disharr);
        $this->assign('allmark', $allmark);
        $this->assign('company', $company);
        $this->assign('metaTitle', $company['name']);
        $this->display();
    }

    /**
     * 点餐信息
     */
    private function getOrderdish($cid) {
        $sessionDK = "session_Ordishs{$cid}_{$this->token}";
        $Orderdish = $_SESSION[$sessionDK];
        $Orderdish = !empty($Orderdish) ? unserialize($Orderdish) : false;
        return $Orderdish;
    }

    /**
     * 根据Wechaid获取已有相关信息
     */
    private function getWechaidInfo($wecha_id, $cid) {
        $contact = false;
        $tmp = M('Dish_order')->where(array('token' => $this->token, 'cid' => $cid, 'wecha_id' => $wecha_id))->order("paid DESC,id DESC ")->find();
        if (!empty($tmp)) {
            $contact = array('youname' => $tmp['name'], 'yousex' => $tmp['sex'], 'youtel' => $tmp['tel'], 'youaddress' => $tmp['address']);
        } elseif (!empty($this->fans)) {
            $contact = array('youname' => $this->fans['truename'], 'yousex' => $this->fans['sex'] == 2 ? 0 : 1, 'youtel' => $this->fans['tel'], 'youaddress' => $this->fans['address']);
        }
        return $contact;
    }

    /*     * 预定页面**** */

    public function preMeal() {
        $cid = $this->_get('cid') ? intval($this->_get('cid', "trim")) : 0;
        $time = time();
        $Dish_table = M('Dish_table');
        /* $orderTable = $Dish_table->where("reservetime <" . ($time + 3 * 3600) . " AND reservetime >" . $time . " AND cid=" . $cid)->select(); */

        /* $tids = array();
          foreach ($orderTable as $row) {
          $tids[] = $row['tableid'];
          }
          if ($tids) {
          $tidsarr = array_unique($tids);
          $table = M('Dining_table')->where(array('id' => array('not in', $tidsarr), 'cid' => $cid, 'status' => '0'))->select();
          } else {
          $table = M('Dining_table')->where(array('cid' => $cid, 'status' => '0'))->select();
          } */
        $table = M('Dining_table')->where(array('cid' => $cid))->select();
        $DishC = $this->getDishCompany($cid);
        $company = $this->getCompany($cid);
        if (!empty($company) && is_array($company)) {
            $_SESSION["session_shop_{$this->token}"] = $cid;
        }
        $WechaidInfo = $this->getWechaidInfo($this->wecha_id, $cid);
        $dc_namearr=$this->GetCanName($cid);
		$this->assign('dcnamearr', $dc_namearr);
        $this->assign('cid', $cid);
        $this->assign('WechaidInfo', $WechaidInfo);
        $this->assign('company', $company);
        $this->assign('tid', 0);
        $this->assign('table', $table);
        $this->assign('DishC', $DishC);
        $this->assign('metaTitle', $company['name']);
        $this->assign('takeaway', 0);
        $this->display('orderBooking');
    }

    /*     * 预定信息处理**** */

    public function preMealInfo() {
        $data = $_POST;
        $takeaway = intval($data['takeaway']);
        $date = trim($data['date']);
        $time = trim($data['time']);
        $timetype = intval(trim($data['timetype']));/**1是商家设定的选择时间2是默认插件生成的时间**/
        $number = intval(trim($data['number']));
        $youremark = htmlspecialchars(trim($data['youremark']), ENT_QUOTES);
        $shopid = intval($data['mycid']);
        if (empty($date) || empty($time)) {
            $this->error('就餐日期时间没有填写完整！');
        }
        if (!($number > 0)) {
            $this->error('就餐人数填写有误！');
        }
        $youtel = htmlspecialchars(trim($data['youtel']), ENT_QUOTES);
        $youname = htmlspecialchars(trim($data['youname']), ENT_QUOTES);

        if (empty($youtel) || empty($youname)) {
            $this->error('手机号码或顾客姓名没有填写！');
        }
        $tableid = intval(trim($data['youtable']));
        if (!($tableid > 0)) {
            $this->error('请选择预定的餐桌！');
        }
        $nowtime = time();
        $reservetime = $timetype == 1 ? strtotime($date . ' 00:00:00' ) : strtotime($date . ' ' . $time);
        $thistable = M('Dining_table')->where(array('cid' => $shopid, 'id' => $tableid))->find();
        if ($thistable['isbox'] == 1) {
            $tablestr = "包厢：{$thistable['name']} ({$thistable['num']}座)";
        } else {
            $tablestr = "大厅：{$thistable['name']} ({$thistable['num']}座)";
        }
        $alreadytime = intval(trim($data['alreadytime']));

        if ($alreadytime > 0) {
            $tmp1 = $alreadytime - 3 * 3600;
            $tmp2 = $alreadytime + 3 * 3600;
            if (($reservetime >= $tmp1) && ($reservetime <= $tmp2)) {
                $this->error("餐桌：" . $thistable['name'] . " 已被预定了，在预定时间前后3小时内将不接受预定！");
            }
        }
		$Dtabledb = M('Dish_table');
		if($timetype == 1){
			$dnameid=intval($time);
			$datenum= strtotime($date." 00:00:00");
		    $tabletmp=$Dtabledb->where(array('cid' => $shopid, 'tableid' => $tableid, 'reservetime' => $datenum,'dn_id'=>$dnameid,'isuse' => array('neq', 2)))->find();
			if(!empty($tabletmp)){
			   $dnamearr=$this->GetCanName($shopid,$dnameid);
			   $this->error("餐桌：" . $thistable['name'] . " ".$date." ".$dnamearr['name']."已经被人预定！");
			}
		}

        $wecha_id = $this->wecha_id ? $this->wecha_id : 'Repastm_' . $youtel;
        $orderid = substr($wecha_id, -5) . date("YmdHis");
        if (($shopid > 0) && ($shopid == $this->_cid)) {
            $DishC = $this->getDishCompany($shopid, false);
            $price = $DishC['subscription'] > 0 ? $DishC['subscription'] : 0;
            $orderdish = array('table' => array('tableid' => $tableid, 'num' => 1, 'price' => $price));
            $Orderarr = array('cid' => $this->_cid, 'wecha_id' => $wecha_id, 'token' => $this->token, 'total' => 0,
                'price' => $price, 'nums' => $number, 'info' => serialize($orderdish), 'name' => $youname,
                'sex' => intval(trim($data['yousex'])), 'tel' => $youtel, 'tableid' => $tableid,
                'time' => $nowtime, 'reservetime' =>$reservetime, 'paid' => 0, 'takeaway' => 0,
                'isuse' => 0, 'orderid' => $orderid, 'des' => $youremark);
            $orid = D('Dish_order')->add($Orderarr);
            $company = $this->getCompany($shopid);
            if ($orid) {
                $sessionoridK = "session_orid{$shopid}_{$this->token}";
                $_SESSION[$sessionoridK] = $orid;
                $tabledata = array('cid' => $this->_cid, 'tableid' => $tableid, 'wecha_id' => $wecha_id, 'reservetime' => $reservetime, 'creattime' => $nowtime, 'orderid' => $orid, 'isuse' => 0);
				if(isset($dnameid)){
				   $tabledata['dn_id']=$dnameid;
				}
                $Dtabledb->add($tabledata);
                //TODO 短信提示
                Sms::sendSms($this->token, "顾客{$youname}预定一个餐位：{$tablestr}，订单号：{$orderid}，请您注意查看并处理", $company['mp']);
                $op = new orderPrint();
                $msg = array('companyname' => $company['name'], 'des' => $Orderarr['des'],
                    'companytel' => $company['tel'], 'truename' => $youname,
                    'tel' => $youtel, 'address' => '',
                    'buytime' => $Orderarr['time'], 'orderid' => $Orderarr['orderid'],
                    'price' => $DishC['subscription'],
                    'total' => 0, 'typename' => '预约点餐');
                $msg = ArrayToStr::array_to_str($msg, 0);
                $op->printit($this->token, $this->_cid, 'Repast', $msg, 0);
                if ($DishC['subscription'] > 0) {
                    $alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
                    if ($alipayConfig['open']) {
                        $this->success('需要支付 ' . $Orderarr['price'] . ' 元预定金<br/>正在提交中...', U('Alipay/pay', array('token' => $this->token, 'wecha_id' => $wecha_id, 'success' => 1, 'from' => 'Repast', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $Orderarr['price'])));
                    } /* elseif ($this->fans['balance']) {
                      $this->success('正在提交中...', U('CardPay/pay',array('token' => $this->token, 'wecha_id' => $wecha_id, 'success' => 1, 'from'=> 'Repast', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $Orderarr['price'])));
                      } */ else {
                        $this->error('商家尚未开启支付功能', $jumpurl);
                    }
                } else {

                    $this->assign('orid', $orid);
                    $this->assign('company', $company);
                    $this->assign('metaTitle', $company['name']);
                    $this->display('preMealTips');
                }
            }
        }
    }

    public function orderBooking() {
        /*         * $takeaway 0:在线预定  1：外卖 2：现场点餐**1已经没用了* */
        $disharr = $_POST['dish']; /*         * 菜品id 数组*id 份数 */
        $shopid = intval($_POST['mycid']);
        $totalmoney = floatval(trim($_POST['totalmoney']));
        $totalnum = intval(trim($_POST['totalnum']));
        $allmark = htmlspecialchars(trim($_POST['allmark']), ENT_QUOTES);
        $_SESSION["allmark" . $shopid . $this->token] = $allmark;
        $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        if ($shopid > 0) {
            $_SESSION["session_shop_{$this->token}"] = $shopid;
            $jumpurl = U('Repast/dishMenu', array('token' => $this->token, 'cid' => $shopid, 'wecha_id' => $this->wecha_id, 'orid' => $orid));
            if (empty($disharr) || !($totalmoney > 0) || !($totalnum > 0)) {
                $this->error('订单信息出错！', $jumpurl);
            }
            $tmparr = array();
            $tmpsubnum = 0;
            $tmpsubmoney = 0;
            foreach ($disharr as $dk => $dv) {
                if (!empty($dv)) {
                    $tmpnum = intval($dv['num']);
                    if ($tmpnum > 0) {
                        $discount = floatval($dv['discount']);
                        $tmpprice = $discount > 0 ? $discount * floatval($dv['price']) / 10 : floatval($dv['price']);
                        $tmparr[$dk] = array();
                        $tmparr[$dk]['did'] = $dk;
                        $tmparr[$dk]['num'] = $tmpnum;
                        $tmparr[$dk]['discount'] = $discount;
                        $tmparr[$dk]['price'] = $tmpprice;
                        $tmparr[$dk]['name'] = $dv['name'];
                        $tmparr[$dk]['omark'] = htmlspecialchars(trim($dv['omark']), ENT_QUOTES);
                        $tmpsubnum+=$tmpnum;
                        $tmpsubmoney+=($tmpprice * $tmpnum);
                    }
                }
            }
            if (empty($tmparr)) {
                $this->error('没有订单信息', $jumpurl);
            }
            if (($tmpsubnum != $totalnum) || bccomp($tmpsubmoney, $totalmoney, 2) != 0) {
                /*                 * bccomp**解决浮点数值比较大小不准确问题 */
                $this->error('订单的金额或点的菜的份数不对', $jumpurl);
            }
            $sessionDK = "session_Ordishs{$shopid}_{$this->token}";
            $tmpdata = array('orderdish' => $tmparr, 'totalnum' => $tmpsubnum, 'totalmoney' => $tmpsubmoney);
            $_SESSION[$sessionDK] = serialize($tmpdata);

            $sessionK = "session_dishs{$shopid}_{$this->token}";
            $_SESSION[$sessionK] = serialize($tmparr);

            $tmpdata = $_SESSION[$sessionDK];
            if (empty($tmpdata)) {
                $_SESSION[$sessionDK] = serialize($tmpdata);
                $_SESSION[$sessionK] = serialize($tmparr);
            }

            $sessionoridK = "session_orid{$shopid}_{$this->token}";
            $sessionorid = $_SESSION[$sessionoridK];
            if (($orid > 0) && ($orid == $sessionorid)) {
                Header("Location:" . U('Repast/saveOrderAndToPay', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $shopid, 'orid' => $orid)));
                exit();
            }
            /*
              $time = time();
              $orderTable = M('Dish_table')->where(array('reservetime' => array('elt', $time + 3 * 3600), 'cid' => $shopid, 'isuse' => 0))->select();
              $tids = array();
              foreach ($orderTable as $row) {
              $tids[] = $row['tableid'];
              }
              if ($tids) {
              $tidsarr=array_unique($tids);
              $table = M('Dining_table')->where(array('id' => array('not in', $tidsarr), 'cid' => $shopid, 'status' => '0'))->select();
              } else {
              $table = M('Dining_table')->where(array('cid' => $shopid, 'status' => '0'))->select();
              } */
            $table = M('Dining_table')->where(array('cid' => $shopid, 'status' => '0'))->select();
            $DishC = $this->getDishCompany($shopid);
            $company = $this->getCompany($shopid);
            $WechaidInfo = $this->getWechaidInfo($this->wecha_id, $shopid);
            $tid = $_SESSION["session_tid{$shopid}_{$this->token}"];
            $this->assign('WechaidInfo', $WechaidInfo);
            $this->assign('cid', $shopid);
            $this->assign('tid', $tid);
            $this->assign('company', $company);
            $this->assign('table', $table);
            $this->assign('DishC', $DishC);
            $this->assign('orid', $orid);
            $this->assign('takeaway', 2);
            $this->assign('metaTitle', $company['name']);
            $this->display();
        } else {
            $jumpurl = U('Repast/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id));
            $this->error('订单信息中店面信息出错', $jumpurl);
        }
    }

    /*     * 根据时间来获取餐桌的预定情况* */

    public function getTableinfo() {
        $takeaway = intval($_GET['takeaway']);
        $date = trim($_GET['datee']);
        $time = trim($_GET['time']);
        $timetype = intval(trim($_GET['timetype']));/**1是商家设定的选择时间2是默认插件生成的时间**/
        $shopid = intval($_GET['cid']);
       
        $tableid = intval($_GET['tid']);
        if (($takeaway != 2) && (empty($date) || empty($time))) {
            $this->dexit(array('error' => 1, 'msg' => '就餐日期时间没有填写完整！'));
        }
        $Dtabledb = M('Dish_table');
        $joinorder = C('DB_PREFIX') . 'dish_order';
        $Dtabledb->join('as d_t LEFT JOIN ' . $joinorder . ' as d_o on d_t.orderid=d_o.id');
        if ($timetype==1) {
			$dn_id=intval($time);
            $tmparr = $Dtabledb->where(array('d_t.cid' => $shopid, 'd_t.tableid' => $tableid, 'd_t.reservetime' =>strtotime($date . ' 00:00:00') ,'dn_id'=>$dn_id,'d_t.isuse' => array('neq', 2)))->field('d_t.*,d_o.name,d_o.sex,d_o.tel,d_o.time')->find();
            if (!empty($tmparr) && is_array($tmparr)) {
				$dnamearr=$this->GetCanName($shopid,$dn_id);
                $tmparr['reservetimestr'] = date('Y-m-d', $tmparr['reservetime'])." ".$dnamearr['name'];
                $this->dexit(array('error' => 0, 'msg' => 'OK', 'data' => $tmparr));
            }
            
        } else {
            $reservetime = $takeaway == 2 ? time() : strtotime($date . ' 00:00:00');
            $nowtime = time();
            if ($reservetime > $nowtime) {
                $tmp1 = $reservetime - 3 * 3600;
                $tmp2 = $reservetime + 3 * 3600;
                $tmparr = $Dtabledb->where('d_t.cid=' . $shopid . " AND d_t.tableid=" . $tableid . " AND d_t.isuse!=2 AND d_t.reservetime>" . $tmp1 . " AND d_t.reservetime<" . $tmp2)->field('d_t.*,d_o.name,d_o.sex,d_o.tel,d_o.time')->find();
                if (!empty($tmparr) && is_array($tmparr)) {
                    $tmparr['reservetimestr'] = date('Y-m-d H:i:s', $tmparr['reservetime']);
                    $this->dexit(array('error' => 0, 'msg' => 'OK', 'data' => $tmparr));
                }
            }
        }
        $this->dexit(array('error' => 2, 'msg' => ''));
    }

    public function saveOrderAndToPay() {
        $sessionDK = "session_Ordishs{$this->_cid}_{$this->token}";
        $tmpOrderdata = $_SESSION[$sessionDK];
        $tmpOrderdata = !empty($tmpOrderdata) ? unserialize($tmpOrderdata) : false;
        $DishC = $this->getDishCompany($this->_cid);
        if (is_array($tmpOrderdata)) {
            $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
            $sessionoridK = "session_orid{$this->_cid}_{$this->token}";
            $sessionorid = $_SESSION[$sessionoridK];
            $allmark = $_SESSION["allmark" . $this->_cid . $this->token];
            if (($orid > 0) && ($orid == $sessionorid)) {
                $takeaway = 2;
                $Dish_order = D('Dish_order');
                $myorder = $Dish_order->where(array('id' => $orid, 'token' => $this->token, 'cid' => $this->_cid))->find();
                if ($myorder) {
                    $orderdish = array();
                    $takeaway = $myorder['takeaway'];
                    $myorderinfo = !empty($myorder['info']) ? unserialize($myorder['info']) : false;
                    if ((empty($myorderinfo) || ((count($myorderinfo) == 1) && isset($myorderinfo['table']))) && ($myorder['total'] == 0)) {

                        $orderdish = $tmpOrderdata['orderdish'];
                        $orderdish['table'] = array('tableid' => $myorder['tableid'], 'num' => 1, 'price' => $myorder['price']);
                    } else {
                        $myorderinfo = unserialize($myorder['info']);
                        $mc = count($myorderinfo);
                        $mc = $mc > 0 ? $mc : 1;
                        foreach ($tmpOrderdata['orderdish'] as $key => $val) {
                            $val['j_c'] = 1;
                            $val['flag'] = $mc;
                            $myorderinfo[$mc . 'jc' . $key] = $val;
                        }
                        $orderdish = $myorderinfo;
                    }
                    $tmpOrderarr = array('total' => $tmpOrderdata['totalnum'] + $myorder['total'],
                        'price' => $tmpOrderdata['totalmoney'] + $myorder['price'],
                        'info' => serialize($orderdish), 'paid' => 0, 'allmark' => $allmark);
                    if ($myorder['paid'] == 1) {
                        $tmpOrderarr['havepaid'] = $myorder['price'] + $myorder['havepaid'];
                        $tmpOrderarr['paid'] = 0;
                    }
                    $Dish_order->where(array('id' => $orid, 'token' => $this->token, 'cid' => $this->_cid))->save($tmpOrderarr);

                    $Orderarr = array('nums' => $myorder['nums'], 'time' => time(),
                        'allmark' => $allmark, 'orderid' => $myorder['orderid'],
                        'name' => $myorder['name'], 'tel' => $myorder['tel'],
                        'wecha_id' => $myorder['wecha_id'], 'tableid' => $myorder['tableid'],
                        'des' => '', 'sex' => $myorder['sex']);
                    unset($myorder, $orderdish);
                    $_SESSION[$sessionoridK] = '';
                } else {
                    $jumpurl = U('Repast/dishMenu', array('token' => $this->token, 'cid' => $this->_cid, 'wecha_id' => $this->wecha_id, 'orid' => $orid));
                    $this->error('订单信息出错了', $jumpurl);
                }
            } else {
                $data = $_POST;
                $takeaway = intval($data['takeaway']);
                $youtel = htmlspecialchars(trim($data['youtel']), ENT_QUOTES);
                $youname = htmlspecialchars(trim($data['youname']), ENT_QUOTES);
                $youremark = htmlspecialchars(trim($data['youremark']), ENT_QUOTES);
                $youtableid = intval(trim($data['youtable']));
                $isallpay = isset($_POST['isallpay']) ? intval($_POST['isallpay']) : 1; /*                 * 是否要立刻支付0不是1是* */
                if (empty($youtel) || empty($youname)) {
                    $this->error('手机号码或顾客姓名没有填写！');
                }
                if (!($youtableid > 0)) {
                    $this->error('请选择一个餐桌！');
                }
                $wecha_id = $this->wecha_id ? $this->wecha_id : 'Repastm_' . $youtel;
                $orderid = substr($wecha_id, -5) . date("YmdHis");
                $Orderarr = array('cid' => $this->_cid, 'wecha_id' => $wecha_id, 'token' => $this->token, 'total' => $tmpOrderdata['totalnum'], 'price' => $tmpOrderdata['totalmoney']);
                if ($takeaway == 0) {
                    /* $date=trim($data['date']);
                      $time=trim($data['time']);
                      $number=intval(trim($data['number']));
                      if(empty($date) || empty($time)){
                      $this->error('就餐时间没有填写完整！');
                      }
                      if(!($number>0)){
                      $this->error('就餐人数填写有误！');
                      } */
                    $Orderarr['nums'] = $number;
                    $Orderarr['reservetime'] = strtotime($date . ' ' . $time);
                } else {
                    $Orderarr['nums'] = 1;
                    $Orderarr['reservetime'] = time();
                }
                $Orderarr['info'] = serialize($tmpOrderdata['orderdish']);
                $Orderarr['name'] = $youname;
                $Orderarr['sex'] = intval(trim($data['yousex']));
                $Orderarr['tel'] = $youtel;
                $Orderarr['address'] = '';
                $Orderarr['tableid'] = $youtableid;
                $Orderarr['time'] = time();
                $Orderarr['stype'] = 0;
                $Orderarr['paid'] = 0;
                $Orderarr['isuse'] = 0;
                $Orderarr['orderid'] = $orderid;
                $Orderarr['printed'] = 0;
                $Orderarr['des'] = $youremark;
                $Orderarr['allmark'] = $allmark;
                $Orderarr['takeaway'] = $takeaway;
                $Orderarr['advancepay'] = !($isallpay > 0) ? $DishC['advancepay'] : 0;
                $Orderarr['isover'] = !($isallpay > 0) ? 1 : 0; /*                 * 订单支付是否结束1进行2结束* */
                $orid = M('Dish_order')->add($Orderarr);
            }
            if ($orid) {
                $_SESSION[$sessionDK] = '';
                $sessionK = "session_dishs{$this->_cid}_{$this->token}";
                $_SESSION[$sessionK] = '';
                /*                 * 更新餐桌状态* */
                if ($takeaway == 2) {
                    M('Dining_table')->where(array('cid' => $this->_cid, 'id' => $Orderarr['tableid']))->save(array('status' => 1));
                }
                //TODO 短信提示
                $company = $this->getCompany($this->_cid);
                Sms::sendSms($this->token, "顾客{$youname}他刚刚点了一份餐，订单号：{$orderid}，请您注意查看并处理", $company['mp']);
                $op = new orderPrint();
                $msg = array('companyname' => $company['name'], 'des' => $Orderarr['allmark'] ? $Orderarr['allmark'] : $Orderarr['des'],
                    'companytel' => $company['tel'], 'truename' => $Orderarr['name'],
                    'tel' => $Orderarr['tel'], 'address' => '',
                    'buytime' => $Orderarr['time'], 'orderid' => $Orderarr['orderid'],
                    'price' => $tmpOrderdata['totalmoney'],
                    'total' => $tmpOrderdata['totalnum'], 'typename' => $takeaway == 2 ? '现场点餐' : '预约点餐',
                    'list' => $tmpOrderdata['orderdish']);
                if (isset($isallpay) && ($isallpay == 0)) {
                    $advancepay = $msg['advancepay'] = $DishC['advancepay'];
                }
                $msg = ArrayToStr::array_to_str($msg, 0);
                $op->printit($this->token, $this->_cid, 'Repast', $msg, 0);
                $alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();

                if ($alipayConfig['open']) {
                    $msgstr = isset($advancepay) ? '需要支付 ' . $advancepay . ' 元就餐预定金<br/>正在提交中...' : '正在提交中...';
                    $paydata = array('token' => $this->token, 'wecha_id' => $Orderarr['wecha_id'], 'success' => 1, 'from' => 'Repast', 'orderName' => $Orderarr['orderid'], 'single_orderid' => $Orderarr['orderid'], 'price' => $tmpOrderdata['totalmoney']);
                    if (isset($advancepay) && ($advancepay > 0)) {
                        $paydata['price'] = $advancepay;
                        $paydata['advancepay'] = 1;
                    }
                    $this->success($msgstr, U('Alipay/pay', $paydata));
                } /* elseif ($this->fans['balance']) {
                  $this->success('正在提交中...', U('CardPay/pay',array('token' => $this->token, 'wecha_id' => $wecha_id, 'success' => 1, 'from'=> 'Repast', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $tmpOrderdata['totalmoney'])));
                  } */ else {
                    $this->error('商家尚未开启支付功能', $jumpurl);
                }
            } else {
                $this->error('订单录入系统出错，抱歉给您的带来了不便。请重新下单吧', $jumpurl);
            }
            if (!empty($this->wecha_id)) {
                /* 保存个人信息 */
                $userinfo_model = M('Userinfo');
                $thisUser = $userinfo_model->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
                if (empty($thisUser)) {
                    $userRow = array('tel' => $Orderarr['tel'], 'truename' => $Orderarr['name'], 'address' => '');
                    $userRow['token'] = $this->token;
                    $userRow['wecha_id'] = $this->wecha_id;
                    $userRow['wechaname'] = '';
                    $userRow['qq'] = 0;
                    $userRow['sex'] = $Orderarr['sex'] == 1 ? 1 : 2;
                    $userRow['age'] = 0;
                    $userRow['birthday'] = '';
                    $userRow['info'] = '';

                    $userRow['total_score'] = 0;
                    $userRow['sign_score'] = 0;
                    $userRow['expend_score'] = 0;
                    $userRow['continuous'] = 0;
                    $userRow['add_expend'] = 0;
                    $userRow['add_expend_time'] = 0;
                    $userRow['live_time'] = 0;
                    $userinfo_model->add($userRow);
                }
            }
        } else {
            $jumpurl = U('Repast/index', array('token' => $this->token, 'wecha_id' => $this->wecha_id));
            $this->error('没有点菜', $jumpurl);
        }
    }

    /**
     * 订单支付
     */
    public function OrderToPay() {
        $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        $cid = $this->_get('cid') ? intval($this->_get('cid', "trim")) : 0;
        if ($orid > 0 && ($cid > 0)) {
            $Dish_order = M('Dish_order');
            $myorder = $Dish_order->where(array('id' => $orid, 'token' => $this->token, 'cid' => $cid))->find();
            if ($myorder) {
                $updatas = array('advancepay' => 0);
                $price = $myorder['price'] - $myorder['havepaid'];
                $alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
                if ($alipayConfig['open']) {
                    if (($myorder['takeaway'] == 2) && ($myorder['isover'] == 1)) {
                        $updatas['isover'] = 2;
                    }
                    $Dish_order->where(array('id' => $myorder['id'], 'cid' => $myorder['cid']))->save($updatas);
                    $this->success('正在提交中...', U('Alipay/pay', array('token' => $this->token, 'wecha_id' => $myorder['wecha_id'], 'success' => 1, 'from' => 'Repast', 'orderName' => $myorder['orderid'], 'single_orderid' => $myorder['orderid'], 'price' => $price)));
                    exit();
                } else {
                    $this->error('商家尚未开启支付功能');
                }
            }
        }
        $this->error('订单信息出错！');
    }

    /**
     * 支付成功后的回调函数
     */
    public function payReturn() {
        $orderid = trim($_GET['orderid']);
		if(isset($_GET['nohandle'])){
		   //已经异步处理过了
		}else {
			ThirdPayRepast::index($orderid);
		}
		/**原来的代码 先注释 防止有问题 可以随时改回来
        $dish_order_db = M('dish_order');
        if ($order = $dish_order_db->where(array('orderid' => $orderid, 'token' => $this->token))->find()) {
            //TODO 发货的短信提醒
            $this->_cid = $order['cid'];
            $company = $this->getCompany($this->_cid);
            $temp = !empty($order['info']) ? unserialize($order['info']) : array();
            $temp = isset($temp['list']) ? $temp['list'] : $temp;
            //$order['paid'] = 1;
            if ($order['paid']) {
                if (!empty($temp) && is_array($temp)) {
                    $log_db = M('Dishout_salelog');
                    $tmparr = array('token' => $this->token, 'cid' => $order['cid'], 'order_id' => $order['id'], 'paytype' => $order['paytype']);
                    foreach ($temp as $kk => $vv) {
                        $did = isset($vv['did']) ? $vv['did'] : $kk;
                        if ($did > 0) {
                            $flag = isset($vv['flag']) ? $vv['flag'] : '';
                            $newk = $flag . 'jc' . $did;
                            if (!($order['paycount'] > 0) || ($kk == $newk)) {
                                M('Dish')->where(array('id' => $did, 'cid' => $order['cid']))->setDec('instock', $vv['num']);
                                $logarr = array(
                                    'did' => $did, 'nums' => $vv['num'],
                                    'unitprice' => $vv['price'], 'money' => $vv['num'] * $vv['price'], 'dname' => $vv['name'],
                                    'addtime' => $order['time'], 'addtimestr' => date('Y-m-d H:i:s', $order['time']), 'comefrom' => 1
                                );
                                $savelogarr = array_merge($tmparr, $logarr);
                                $log_db->add($savelogarr);
                            }
                        }
                    }
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->setInc('paycount', 1);
                }
                if (($order['takeaway'] == 2) && ($order['isover'] == 2)) {
                    M('Dining_table')->where(array('id' => $order['tableid'], 'cid' => $order['cid']))->save(array('status' => 0));
                } elseif (($order['takeaway'] == 2) && ($order['isover'] == 1)) {
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->save(array('paid' => 0));
                }
                if ((empty($temp) || ((count($temp) == 1) && isset($temp['table']))) && ($temp['total'] == 0)) {
                    $temp = false;
                    $order['total'] = 1;
                    $bookTable = $order['price'];
                } elseif (($order['takeaway'] == 2) && ($order['advancepay'] > 0)) {
                    $bookTable = false;
                    if ($order['paycount'] == 1) {
                        $realpay = $myorder['price'] - $myorder['havepaid'];
                    } elseif ($order['paycount'] == 0) {
                        $advancepay = $order['advancepay'];
                    }
                    $dish_order_db->where(array('id' => $order['id'], 'cid' => $order['cid']))->save(array('havepaid' => $order['advancepay'], 'advancepay' => 0));
                } else {
                    $bookTable = false;
                    if (isset($temp['table']) && !empty($temp['table'])) {
                        $bookTable = $temp['table']['price'];
                        unset($temp['table']);
                    }
                    $realpay = $myorder['price'] - $myorder['havepaid'];
                }
                $op = new orderPrint();
                $msg = array('companyname' => $company['name'], 'des' => $order['des'], 'companytel' => $company['tel'], 'truename' => $order['name'], 'tel' => $order['tel'], 'address' => $order['address'], 'buytime' => $order['time'], 'orderid' => $order['orderid'], 'bookTable' => $bookTable, 'price' => $order['price'], 'total' => $order['total'], 'list' => $temp, 'advancepay' => isset($advancepay) ? $advancepay : false, 'realpay' => isset($realpay) ? $realpay : false);
				$msg['typename'] = $order['takeaway'] == 2 ? '现在点餐' : '预约点餐';
				if($order['takeaway'] == 1){
				  $msg['sendtime']=$order['reservetime'];
				  $msg['typename']='外卖';
				}
                if ($order['tableid']) {
                    $t_table = M("Dining_table")->where(array('id' => $order['tableid']))->find();
                    $msg['tablename'] = isset($t_table['name']) ? $t_table['name'] : '';
                }

                $msg = ArrayToStr::array_to_str($msg, 1);
                $op->printit($this->token, $this->_cid, 'Repast', $msg, 1);

                Sms::sendSms($this->token . "_" . $this->_cid, "顾客{$order['name']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理");
                $model = new templateNews();
                $model->sendTempMsg('TM00820', array('href' => U('Repast/myOrders', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid)), 'wecha_id' => $this->wecha_id, 'first' => '订餐交易提醒', 'keynote1' => '订单已支付', 'keynote2' => date("Y年m月d日H时i分s秒"), 'remark' => '支付成功，感谢您的光临，欢迎下次再次光临！'));
            }
            if (empty($order['info']) && ($order['total'] == 0)) {
                $sessionoridK = "session_orid{$this->_cid}_{$this->token}";
                $_SESSION[$sessionoridK] = $order['id'];
                $this->assign('orid', $order['id']);
                $this->assign('company', $company);
                $this->assign('metaTitle', $company['name']);
                $this->display('preMealTips');
            } else {
                $this->redirect(U('Repast/myOrders', array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $this->_cid)));
            }
        } else {
            exit('订单不存在');
        }*/
    }

    /**
     * 我的订单记录
     */
    public function myOrders() {

        $where = array('wecha_id' => $this->wecha_id, 'token' => $this->token, 'comefrom' => 'dish');
        $dish_order = M('Dish_order')->where($where)->order('id DESC')->limit(30)->select(); //只查询最近30条记录
        $companys = M('Company')->where("token='{$this->token}' AND display=1")->order('id ASC')->select();
        $company = $companys['0'];
        $newcompanys = array();
        foreach ($companys as $crow) {
            $newcompanys[$crow['id']] = $crow['name'];
        }
        unset($companys);
        $list = array();
        $tmp = array();
        $weekarr = array('0' => '周末', '1' => '周一', '2' => '周二', '3' => '周三', '4' => '周四', '5' => '周五', '6' => '周六');
        $nowtime = time();
        $tt1 = $nowtime - 3 * 3600;
        foreach ($dish_order as $row) {
            $tmp['oid'] = $row['id'];
            $tmp['cid'] = $row['cid'];
            $tmp['cyname'] = $newcompanys[$row['cid']];
            $tmp['wecha_id'] = $row['wecha_id'];
            $tmp['token'] = $row['token'];
            $tmp['otime'] = $row['time'];
            $tmp['takeaway'] = $row['takeaway'];
            $tmp['reservetime'] = $row['reservetime'];
            $tmp['paid'] = $row['paid'];
            $tmp['orderid'] = $row['orderid'];
            $tmp['paytype'] = $row['paytype'];
            $datestr = date('Y-m-d', $row['time']);
            $wk = date('w', $row['time']);
            $timestr = date('H:i', $row['time']);
            $tmp['otimestr'] = $datestr . "&nbsp;&nbsp;" . $weekarr[$wk] . "&nbsp;&nbsp;" . $timestr;
            $tmp['jiaxcai'] = false;
            if (($row['takeaway'] == 2) && ($row['time'] > $tt1)) {
                $tmp['jiaxcai'] = true;
            }
            if ($row['takeaway'] == 0) {
                $reserveinfo = M('Dish_table')->where(array('orderid' => $row['id'], 'cid' => $row['cid']))->find();
				$tmptime=$row['reservetime'];
                if (!empty($reserveinfo) && ($reserveinfo['dn_id'] > 0)) {
					$tmptime=$reserveinfo['reservetime']+23*3600;
                    $tmp['reservetime'] = $tmptime;
                }
				
                if ($tmptime > $tt1) {
                    $tmp['jiaxcai'] = true;
                }
            }
            $list[] = $tmp;
        }
        $this->assign('orderList', $list);
        $this->assign('today', strtotime(date('Y-m-d 00:00:00')));
        $this->assign('company', $company);
        $this->assign('metaTitle', '微餐饮');
        $this->display();
    }

    /*     * 订单详情页*** */

    public function myOrderDetail() {
        $orid = $this->_get('orid') ? intval($this->_get('orid', "trim")) : 0;
        $cid = $this->_get('cid') ? intval($this->_get('cid', "trim")) : 0;
        $weekarr = array('0' => '周末', '1' => '周一', '2' => '周二', '3' => '周三', '4' => '周四', '5' => '周五', '6' => '周六');
        $paystrarr = array('alipay' => '支付宝', 'weixin' => '微信支付', 'tenpay' => '财付通[wap手机]', 'tenpaycomputer' => '财付通[即时到帐]', 'yeepay' => '易宝支付', 'allinpay' => '通联支付', 'daofu' => '货到付款', 'dianfu' => '到店付款', 'chinabank' => '网银在线');
        if (($cid > 0) && ($orid > 0)) {
            $tt1 = time() - 3 * 3600;
            $myorder = M('Dish_order')->where(array('id' => $orid, 'cid' => $cid, 'token' => $this->token))->find();
            if (!empty($myorder)) {
                if (!empty($myorder['info'])) {
                    $myorder['info'] = unserialize($myorder['info']);
                }
                $datestr = date('Y-m-d', $myorder['time']);
                $wk = date('w', $myorder['time']);
                $timestr = date('H:i', $myorder['time']);
                $myorder['otimestr'] = $datestr . "&nbsp;&nbsp;" . $weekarr[$wk] . "&nbsp;&nbsp;" . $timestr;
                $myorder['paytypestr'] = array_key_exists($myorder['paytype'], $paystrarr) ? $paystrarr[$myorder['paytype']] : '其他';
                $myorder['paidstr'] = $myorder['paid'] == 1 ? '已支付' : '未支付';
                $table = M('Dining_table')->where(array('id' => $myorder['tableid'], 'cid' => $cid))->find();
                if (!empty($table)) {
                    $myorder['tablestr'] = $table['isbox'] == 1 ? '包厢：' : '大厅：';
                    $myorder['tablestr'] = $myorder['tablestr'] . $table['name'] . " &nbsp;(" . $table['num'] . "座)";
                } else {
                    $myorder['tablestr'] = '无';
                }
                $myorder['jiaxcai'] = false;
                if (($myorder['takeaway'] == 2) && ($myorder['time'] > $tt1)) {
                    $myorder['jiaxcai'] = true;
                }
                $reserveinfo = M('Dish_table')->where(array('orderid' => $myorder['id'], 'cid' => $cid))->find();
                if (!empty($reserveinfo)) {
                    if ($reserveinfo['dn_id']>0) {
						$dnamearr=$this->GetCanName($myorder['cid'],$reserveinfo['dn_id']);
                        $myorder['reservetimestr'] = date('Y-m-d', $reserveinfo['reservetime'])." ".$dnamearr['name'];
                        $myorder['reservetime'] = $reserveinfo['reservetime']+23*3600;
                        if (($myorder['takeaway'] == 0) && ($myorder['reservetime'] > $tt1)) {
                            $myorder['jiaxcai'] = true;
                        }
                    } else {
                        $myorder['reservetimestr'] = date('Y-m-d H:i:s', $reserveinfo['reservetime']);
                    }
                }
                if (($myorder['takeaway'] == 0) && ($myorder['reservetime'] > $tt1)) {
                    $myorder['jiaxcai'] = true;
                }
            } else {
                $myorder = array();
            }
            $company = $this->getCompany($cid, false);
            $this->assign('orderList', $myorder);
            $this->assign('company', $company);
            $this->assign('today', strtotime(date('Y-m-d') . ' 00:00:00'));
            $this->assign('metaTitle', '我的订单');
            $this->display();
        }
    }

}

?>