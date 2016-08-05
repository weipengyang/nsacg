<?php
class RepastStaffAction extends BaseAction{
	public $_cid = 0;
	public $token = '';
	public function _initialize() {
		parent::_initialize();
        $this->_cid = isset($_GET['cid']) ? $this->_get('cid', 'trim,intval') : session('ycompany_id');
        $this->token = isset($_GET['token']) ? $this->_get('token', 'trim') : session('RepastStaff_token');
        $this->assign('token', $this->token);
        $this->assign('cid', $this->_cid);
    }

    /**
     * 店员登录
     */
    public function mtlogin() {
        if (IS_POST) {
            $staffdb = M('Company_staff');
            $username = htmlspecialchars($this->_post('username', 'trim'), ENT_QUOTES);
            $pwd = $this->_post('password', 'trim');
			echo $pwd;
			$pwd = !empty($pwd) ? md5($pwd) : '';
            if (!($this->_cid > 0) || empty($this->token)) {
                $this->error('URL参数出错！', U('RepastStaff/mtlogin', array('token' => $this->token, 'cid' => $this->_cid)));
            }
            $tmp = $staffdb->where(array('token' => $this->token, 'companyid' => $this->_cid, 'username' => $username, 'password' => $pwd))->find();
            if (!empty($tmp) && is_array($tmp)) {
                session('ycompany_id', $this->_cid);
                session('RepastStaff_token', $this->token);
				session('RepastStaff_name', $tmp['name']);
                Header("Location:" . U('RepastStaff/optindex', array('token' => $this->token, 'cid' => $this->_cid)));
                exit();
            }else{
			   $this->error('密码或账户错误！');
			}
        } else {
			$cid = session('ycompany_id');
			$token = session('RepastStaff_token');
			if (($cid>0) && !empty($token) && ($cid == $this->_cid) && ($token == $this->token)) {
			  Header("Location:" . U('RepastStaff/optindex', array('token' => $this->token, 'cid' => $this->_cid)));
			  exit;
			}
            $token = $this->_get('token', 'trim');
            $this->assign('cid', $this->cid);
            $this->display();
        }
    }

    /**
     * 工作人员扫描餐台列表
     */
    public function optindex() {
        $cid = session('ycompany_id');
        $token = session('RepastStaff_token');
        if (($cid>0) && !empty($token) && ($cid == $this->_cid) && ($token == $this->token)) {
            $Diningdb = M('Dining_table');
            $where = array('cid' => $this->_cid);
            $count = $Diningdb->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $Diningdb->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $nowtime = time();
            $tabledb = M('Dish_table');
            $jointable = C('DB_PREFIX') . 'dish_order';
            $tabledb->join('as d_t LEFT JOIN ' . $jointable . ' as d_o on d_t.orderid=d_o.id');
            $orderTable = $tabledb->where("d_t.cid=" . $this->_cid . " AND d_t.reservetime >" . $nowtime . " AND d_t.isuse !=2 AND d_o.takeaway=0")->group('d_t.tableid')->order("d_t.id DESC")->field('d_t.id,d_o.id as orid,d_t.tableid,d_t.reservetime,d_t.isuse,d_o.name,d_o.sex,d_o.tel,d_o.paid,d_o.price,d_o.takeaway')->select();

            $tmpdata = array();
            if ($orderTable) {
                foreach ($orderTable as $vv) {
                    if (array_key_exists($vv['tableid'], $tmpdata)) {
                        continue;
                    } else {
                        $isusestr = $vv['isuse'] == 1 ? '正在就餐...' : '未就餐';
                        $tmp = date('Y-m-d H:i', $vv['reservetime']) . " <span style='color:red'>" . $vv['name'] . " </span>预定了";
                        $tmpdata[$vv['tableid']] = array('id' => $vv['id'], 'tableid' => $vv['tableid'], 'reservestr' => $tmp, 'reservetime' => $vv['reservetime'], 'orid' => $vv['orid'], 'isuse' => $vv['isuse'], 'isusestr' => $isusestr);
                    }
                }
            }
			$this->assign('Staff_name', session('RepastStaff_name'));
            $this->assign('reserve', $tmpdata);
            $this->assign('page', $show);
            $this->assign('list', $list);
            $this->display();
        } else {
            Header("Location:" . U('RepastStaff/mtlogin', array('token' => $token, 'cid' => $cid)));
            exit();
        }
    }

    /*     * 切换餐桌状态* */

    public function toSwitchStatus() {
        $typ = $this->_post('typ') ? intval($this->_post('typ', "trim")) : 0;
        $tid = $this->_post('tid') ? intval($this->_post('tid', "trim")) : 0;
        $vv = $this->_post('vv') ? intval($this->_post('vv', "trim")) : 0;
        $dtid = $this->_post('dtid') ? intval($this->_post('dtid', "trim")) : 0;
        $Diningdb = M('Dining_table');
        switch ($typ) {
            case 1:
                if ($vv == 1) {
                    $Diningdb->where(array('id' => $tid, 'cid' => $this->_cid))->save(array('status' => 0));
                } else {
                    $Diningdb->where(array('id' => $tid, 'cid' => $this->_cid))->save(array('status' => 1));
                }
                break;
            case 2:
                if ($vv == 1) {
                    M('Dish_table')->where(array('id' => $dtid, 'tableid' => $tid, 'cid' => $this->_cid))->save(array('isuse' => 2));
                    $Diningdb->where(array('id' => $tid, 'cid' => $this->_cid))->save(array('status' => 0));
                } else {
                    M('Dish_table')->where(array('id' => $dtid, 'tableid' => $tid, 'cid' => $this->_cid))->save(array('isuse' => 1));
                    $Diningdb->where(array('id' => $tid, 'cid' => $this->_cid))->save(array('status' => 1));
                }
                break;
            default:
                break;
        }
        echo json_encode(array('error' => 0));
    }

}
?>