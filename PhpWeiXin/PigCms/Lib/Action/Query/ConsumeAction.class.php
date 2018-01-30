<?php

/**
 * ConsumeAction short summary.
 *
 * ConsumeAction description.
 *
 * @version 1.0
 * @author Administrator
 */
class ConsumeAction extends Action{
    public $token;
    public $weixin;
    public $wxuser;
   public  function _initialize(){
        $this->token='rlydsv1453614397';
        $this->wxuser=D('Wxuser')->where(array('token'=>$this->token))->find();
        $this->weixin=new JSSDK($this->wxuser['appid'],$this->wxuser['appsecret']);

        if(!in_array(ACTION_NAME, array('register', 'login','ordering'))){
            if(!cookie('username')){
                if(ACTION_NAME=='products' and $_GET['key']=='39099139'){
                
                }
                else{
                    // $this->redirect(U('Consume/login', array('token' => $this->token)));
                     echo "<script>top.location.href='/index.php?&g=Query&m=Consume&a=login'</script>";
                     //$this->redirect(U('Consume/main'));
                     exit();
                }
            }
            else{
                $username=cookie('username');
                cookie('username',$username,3600*2);
                 $user=M('用户管理','dbo.','difo')->where(array('姓名'=>$username))->find();
                 if($user)
                 { 
                     cookie('department',$user['门店权限'],3600*2);
                     cookie('role',$user['角色权限'],3600*2);
                 }
                if($this->isAjax()){
                    //Log::write('url1:'. $_SERVER['PHP_SELF']);
                    //Log::write('url2:'. '/index.php?g=Query&m=Consume&a='.ACTION_NAME);
                
                //if(!in_array(ACTION_NAME, array('main', 'getmainmenu','getmenus'))){
                //    //$url=$_SERVER['PHP_SELF'];
                //    $url='/index.php?g=Query&m=Consume&a='.ACTION_NAME;
                //    $right=M('sys_app','dbo.','difo')
                //    ->query("select * from Sys_Menu where Menu_id in(
                //    select menuid from  Sys_authority where roleID=(
                //    select RoleID from Sys_role where Sys_role.RoleName =(select 角色权限 from 用户管理 where 姓名='$username')  
                //    )
                //    ) and Menu_url='$url'");
                //    if(!$right){
                //        echo '你无权访问该页面，请联系管理员';
                //        exit;
                //    }
                //}
                }
                $this->assign('username',$username);
            }
        }
    }
    public  function login()
    {
        if(IS_POST)
        { 
            $username=$_POST['username'];
            $password=$_POST['password'];
            $user=M('用户管理','dbo.','difo')->where(array('姓名'=>$username))->find();
            if($user&&$user['密码']==$password)
            {
                cookie('username',$username,3600*2);
                cookie('department',$user['门店权限'],3600*2);
                cookie('role',$user['角色权限'],3600*2);
                echo "<script>top.location.href='/index.php?&g=Query&m=Consume&a=main'</script>";
                //$this->redirect(U('Consume/main'));
                exit();
            }
            else{
                $this->error('用户名密码不正确');
                exit();
            }
            
        }
        $this->display();

    }
    public  function addproject(){
        if(IS_POST){
            $result=M('','dbo.','difo')->query("SELECT 类别 [text],'true' isexpand FROM 项目分类");
            $project['text']='全部';
            $project['isexpand']='true';
            $project['children']=$result;
            $projects=array();
            array_push($projects,$project);
            echo json_encode($projects);
            exit;
        }
        else{
            $this->assign('ID',$_GET['ID']);
            $this->assign('itemID',$_GET['itemID']);
            $this->display();
        }
    }
    public function getroles(){
     
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if($searchkey){       
            $searchwhere['RoleName']=array('like',$searchkey);
            $searchwhere['RoleDscrip']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='RoleID desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
        $ds=M('sys_role','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('sys_role','dbo.','difo')->where($where)->count();
        $data['Rows']=$ds;
        $data['Total']=$count;
        echo json_encode($data);

    }
    public function getusers(){
     
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if($searchkey){       
            $searchwhere['姓名']=array('like',$searchkey);
            $searchwhere['门店权限']=array('like',$searchkey);
            $searchwhere['部门权限']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='姓名 desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
        $ds=M('用户管理','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('用户管理','dbo.','difo')->where($where)->count();
        $data['Rows']=$ds;
        $data['Total']=$count;
        echo json_encode($data);

    }
    public function getemployees(){
     
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if($searchkey){       
            $searchwhere['姓名']=array('like',$searchkey);
            $searchwhere['单位']=array('like',$searchkey);
            $searchwhere['部门']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='姓名 desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
        $ds=M('员工目录','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('员工目录','dbo.','difo')->where($where)->count();
        $data['Rows']=$ds;
        $data['Total']=$count;
        echo json_encode($data);

    }
    public  function getsysapplist()
    {
            $ds=M('sys_app','dbo.','difo')->order('App_order')->select();
            echo json_encode($ds);
            exit;
     }
    public function savemenu(){
      if(IS_POST){
          $role=$_POST['row'];
          M('Sys_authority','dbo.','difo')->where(array('roleID'=>$role['RoleID']))->delete();
          $roleID=$role['RoleID'];
          if($_POST['menudata']!=''){
              $menus=$_POST['menudata'];
              foreach($menus as $menuid){
                  if($menuid!='0'){
                      $item['roleID']=$roleID;
                      $item['menuid']=$menuid;
                      $item['menutype']=2;
                      M('Sys_authority','dbo.','difo')->add($item);
                  }

              }
          }
          echo '保存成功';
      }
    }
    public  function getmainmenu()
    {
        $username=cookie('username');
        $ds=M('sys_app','dbo.','difo')
            ->query("
                select * from sys_app where id in(
                select app_id from Sys_Menu where Menu_id in(
                select menuid from  Sys_authority where roleID=(
                select RoleID from Sys_role where Sys_role.RoleName =(select 角色权限 from 用户管理 where 姓名='$username') and parentid=0
                )
                ) ) order by app_order");

            $toolbarscript = "{Items:[";

           foreach($ds as $row)
           {
                $toolbarscript .= "{";
                $toolbarscript .= "type: 'button',";
                $toolbarscript .= "text: '". $row["App_name"] . "',";
                $toolbarscript .= "icon: '" .$row["App_icon"] ."',";
                if (true)
                {
                    $toolbarscript .= "disable: false,";
                }
                else
                {
                    $toolbarscript .="disable:false," ;
                }
                $toolbarscript .= "click: function () {";
                $toolbarscript .= "f_according(" . $row["id"] .")";
                $toolbarscript .= "}";
                $toolbarscript .= "},";
           }
            $toolbarscript .= "]}";
            echo $toolbarscript;
     }
    public  function getmenus()
    {
        $username=cookie('username');
        $appid=$_GET['appid'];
        $ds=M('sys_menu','dbo.','difo')->where(array('app_id'=>$_GET['appid'],'parentid'=>0))->order('menu_order')->select();
        foreach($ds as $key=>$item){
            $children=M('sys_app','dbo.','difo')
            ->query("select * from Sys_Menu where Menu_id in(
                select menuid from  Sys_authority where roleID=(
                select RoleID from Sys_role where Sys_role.RoleName =(select 角色权限 from 用户管理 where 姓名='$username') and app_id=$appid and parentid!=0 
                )
                )  order by menu_order");
           $ds[$key]['children']=$children;
        }
        echo json_encode($ds);
     }
    public  function getrolemenus()
    {
        $ds=M('Sys_authority','dbo.','difo')->where(array('roleID'=>$_GET['roleid']))->select();
        echo json_encode($ds);
     }
    public  function getallmenus()
    {
        //$ds=M('sys_menu','dbo.','difo')->order('app_id')->select();
        $treenodes=array();
        $treedata['App_id']=0;
        $treedata['Menu_id']=0;
        $treedata['Menu_name']='爱养车';
        $treedata['parentid']='无';

        $ds=M('sys_menu','dbo.','difo')->where(array('parentid'=>0))->order('app_id')->select();
        foreach($ds as $key=>$item){
           $children=M('sys_menu','dbo.','difo')->where(array('parentid'=>$item['Menu_id']))->order('menu_order')->select();
           $ds[$key]['children']=$children;
        }
        $treedata['children']=$ds;
        $treenodes[0]=$treedata;
        echo json_encode($treenodes);
     }
   public  function stat()
    {
        $jy=M('车辆档案','dbo.','difo')->query("select  机油格,count(1) 数量 from  车辆档案 where 机油格 is not null group by 机油格 ");
        $keys=array_column($jy,'机油格');
        $values=array_column($jy,'数量');;
        $this->assign('keys',json_encode($keys));
        $this->assign('values',json_encode($values));
        $this->display();
    }
    public  function getstatdata()
    {
        $type=$_GET['type'];
        $jy=M('车辆档案','dbo.','difo')->query("select  $type,count(1) 数量 from  车辆档案 where $type is not null and $type !='' group by $type order by  $type ");
        $data['keys']=array_column($jy,"$type");
        $data['values']=array_column($jy,'数量');;
        echo json_encode($data);
    }
    public  function addproduct(){
        if(IS_POST){
            $projects=M('配件分类','dbo.','difo')->query("SELECT 名称 [text],'false' isexpand, 有无子节点 haschildren FROM 配件分类 where 父项=''");
            foreach($projects as $key=>$c)
            {    
                if($c['haschildren']=='1')
                {
                   $children=M('配件分类','dbo.','difo')->query("SELECT 名称 [text],'false' isexpand, 有无子节点 haschildren FROM 配件分类 where 父项='".$c['text']."'");
                   foreach($children as $k=>$s)
                    {
                        if($s['haschildren']=='1'){
                            $sun=M('配件分类','dbo.','difo')->query("SELECT 名称 [text],'false' isexpand, 有无子节点 haschildren FROM 配件分类 where 父项='".$s['text']."'");
                            $s['children']=$sun;
                            $children[$k]['children']=$sun;
                        }
                    }
                   $projects[$key]['children']=$children;
                   
                }
            }
            //$project['text']='全部';
            //$project['isexpand']='true';
            //$project['children']=$result;
            //$projects=array();
            //array_push($projects,$project);
            echo json_encode($projects);
            exit;
        }
        else{
            $this->assign('ID',$_GET['ID']);
            $this->assign('itemID',$_GET['itemID']);
            $this->display();
        }
    }
    public  function changestate(){
       if(IS_POST){
           $id=$_POST['id'];
           $zt=$_POST['zt'];
           $oldzt=$_POST['oldzt'];
           $wx=M('维修','dbo.','difo')->where(array('流水号'=>$id))->find();
           $data['当前状态']=$zt;
           if($wx){
               if($zt=='出厂'){
                   if($wx['标志']!='已结算')
                   {
                       echo '请先结算后再出厂';
                       exit;
                   }
               }
               if ($oldzt == '出厂' && $zt == '结束') {
                   $data['出厂时间']=date('Y-m-d H:i',time());
                   $data['结束日期']=date('Y-m-d',time());
               }
                //if ($oldzt == '领料' && $zt == '审核') {
                //    //$data['实际完工']=date('Y-m-d H:i');
                //    $products=M('维修配件','dbo.','difo')->where(array('ID'=>$wx['ID']))->select();
                //    if (count($products)> 0) {
                //        for ($i = 0; $i < count($products); $i++) {
                //            if ($products[$i]['虚增类别'] == null||$products[$i]['虚增类别'] == '') {
                //                $count = $products[$i]['数量'] - $products[$i]['已领料数量'];
                //                if ($count > 0) {
                //                    echo $products[$i]['名称']."未领料，不能转完工";
                //                    exit;
                //                }
                //            }
                //        }
                //    }
                //}
                if ($oldzt == '派工' && $zt == '领料') {
                     $projects=M('维修项目','dbo.','difo')->where(array('ID'=>$wx['ID']))->select();
                    if (count($projects)> 0) {
                        for ($i = 0; $i < count($projects); $i++) {
                            if ($projects[$i]['虚增类别'] == null || $projects[$i]['虚增类别'] == '') {
                                if ($projects[$i]['主修人'] == null || $projects[$i]['主修人'] == '') {
                                    echo $projects[$i]['项目名称']."没有派工";
                                    exit;
                                }
                                if ($projects[$i]['开工时间'] == null || $projects[$i]['开工时间'] == '' || date('Y-m-d', strtotime($projects[$i]['开工时间'])) == '1900-01-01')
                                {
                                    echo $projects[i]['项目名称']."开工时间为空";
                                    exit;
                                }
                               
                            }
                        }
                    }
                }
               M('维修','dbo.','difo')->where(array('流水号'=>$id))->save($data);
               $this->writeLog($wx['ID'],$wx['业务编号'],$wx['维修类别'],'转入'.$zt);
               echo '1';
           }

       }
    }
    public  function outfactory(){
       if(IS_POST){
           $id=$_POST['id'];
            $wx=M('维修','dbo.','difo')->where(array('流水号'=>$id))->find();
            if($wx['标志']!='已结算')
            {
                echo '请先结算后再出厂';
                return;
            }
            M('维修','dbo.','difo')->where(array('流水号'=>$id))->save(array('当前状态'=>'结束','出厂时间'=>date('Y-m-d H:i',time()),'结束日期'=>date('Y-m-d H:i',time())));
            $this->writeLog($wx['ID'],$wx['业务编号'],$wx['维修类别'],'结束');
            echo '更新状态成功';
       }
    }
    public  function getscoreinfo(){
    
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if($searchkey){       
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['score_type']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='sign_time desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',sign_time desc';
        }
        $scoreinfo=M('member_card_sign')->join('join tp_userinfo on tp_member_card_sign.wecha_id=tp_userinfo.wecha_id')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('member_card_sign')->join('join tp_userinfo on tp_member_card_sign.wecha_id=tp_userinfo.wecha_id')->where($where)->count();
        $data['Rows']=$scoreinfo;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public  function gettireinfo(){
    
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        if($_GET[type]){
            $where['type']=array('eq','3');        
        }else{
            $where['type']=array('neq','3');
        }
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if (isset($_POST['startdate'])&&trim($_POST['startdate'])){
           $startdate=strtotime($_POST['startdate']);
           $where['optime']=array('gt',strtotime($_POST['startdate']));
        }else{
            $startdate=strtotime('2016-01-01');
        }
        if($searchkey){       
            $searchwhere['nickname']=array('like',$searchkey);
            $searchwhere['code']=array('like',$searchkey);
            $searchwhere['name']=array('like',$searchkey);
            $searchwhere['type']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='optime desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',optime desc';
        }
        if(!$_GET[type]){
            $scoreinfo=M('tire_query')
                ->join("join ( select count(1) num,code  from tp_tire_query  where type!=3 and optime>$startdate  group by code) b on tp_tire_query.code=b.code")
                ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
            $count=M('tire_query')
                ->join("join ( select count(1) num,code  from tp_tire_query  where type!=3 and optime>$startdate group by code) b on tp_tire_query.code=b.code")
                ->where($where)->count();
        }
        else{
            $scoreinfo=M('tire_query')
                   ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
            $count=M('tire_query')->where($where)->count();
        }
        $data['Rows']=$scoreinfo;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public  function getpresentcoupons(){
    
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        $where['1']=1;
        if($searchkey){       
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['optuser']=array('like',$searchkey);
            $searchwhere['proposer']=array('like',$searchkey);
            $searchwhere['comment']=array('like',$searchkey);
            $searchwhere['coupon_name']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='addtime desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',addtime desc';
        }
        $scoreinfo=M('member_present')
            ->join('left join tp_member_card_create on tp_member_present.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('member_present')
            ->join('left join tp_member_card_create on tp_member_present.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->count();
        $data['Rows']=$scoreinfo;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public  function getuserinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        if($_GET['type']){
            $where['供应商']=1;
        }
        $searchkey=$_POST['searchkey'];
        $searchkey='%'.trim($searchkey).'%';
        if($searchkey){       
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['会员编号']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['手机号码']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $userinfo=M('往来单位','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
        $count=M('往来单位','dbo.','difo')->where($where)->count();
        $data['Rows']=$userinfo;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public function getfwgwwork()
    {
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }
        else{
               $where['门店']=array('in',explode(',',cookie('department')));
        
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($_POST['fwgw']&&trim($_POST['fwgw'])!='')
        {
            $where['接车人']=trim($_POST['fwgw']);
            
        }
        if($searchkey){       
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['接车人']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if(!isset($sortname)){
            $sortname='日期';
            $sortorder='desc';
        }
        $count=M('服务顾问业绩表','dbo.','difo')->join('员工目录 on 服务顾问业绩表.接车人=员工目录.姓名')->where($where)->count();
        $yelist=M('服务顾问业绩表','dbo.','difo')->join('员工目录 on 服务顾问业绩表.接车人=员工目录.姓名')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $total=M('服务顾问业绩表','dbo.','difo')->join('员工目录 on 服务顾问业绩表.接车人=员工目录.姓名')
            ->where($where)
            ->field("sum(服务车辆数) 服务车辆数,sum(工时费) 工时,sum(产值) 产值,sum(毛利) 毛利")
            ->find();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$total;
        echo json_encode($data);
    }
    public function getpurchasework()
    {
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($_POST['fwgw']&&trim($_POST['fwgw'])!='')
        {
            $where['制单人']=trim($_POST['fwgw']);
            
        }
        if($searchkey){       
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if(!isset($sortname)){
            $sortname='日期';
            $sortorder='desc';
        }
        $count=M('采购录单业绩表','dbo.','difo')->where($where)->count();
        $yelist=M('采购录单业绩表','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $total=M('采购录单业绩表','dbo.','difo')
            ->where($where)
            ->field("sum(服务车辆数) 服务车辆数,sum(工时费) 工时,sum(产值) 产值,sum(毛利) 毛利")
            ->find();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$total;
        echo json_encode($data);
    }

    public  function getpersonwork()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
         if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
             $searchkey='%'.trim($_POST['searchkey']).'%';
         }
         if($_POST['lb']&&trim($_POST['lb'])!='')
         {
             $where['维修类别']=trim($_POST['lb']);
             
         }
         if($_POST['bm']&&trim($_POST['bm'])!='')
         {
             $where['门店']=array('like','%'.trim($_POST['bm'].'%'));
             
         }else{
             $where['门店']=array('in',explode(',',cookie('department')));

         }
         if($_POST['startdate']&&trim($_POST['startdate'])!='')
         {
             $where['结算日期']=array('egt',trim($_POST['startdate']));
             
         }
         if($_POST['enddate']&&trim($_POST['enddate'])!='')
         {
             $where['结算日期']=array('elt',trim($_POST['enddate']));
             
         }
         if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
         {
             $where['结算日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
             
         }
         if($_POST['zhuxiu']&&trim($_POST['zhuxiu'])!='')
         {
             $where['主修人']=trim($_POST['zhuxiu']);
             
         }
        if(!isset($sortname)){
            $sortname='结算日期';
            $sortorder='desc';
        }
        if($searchkey){       
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['主修人']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $count=M('个人业绩表','dbo.','difo')->where($where)->count();
        $yelist=M('个人业绩表','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $total=M('个人业绩表','dbo.','difo')
            ->where($where)
            ->field("sum(服务车辆数) 服务车辆,sum(工时费) 工时,sum(工时数) 工时数")
            ->find();
        $where['当前状态']=array('neq','取消');
        $total1=M('维修','dbo.','difo')
            ->where($where)
            ->field("sum(材料费) 配件费,sum(应收金额) 产值")
            ->find();
        $total['配件费']=$total1['配件费'];
        $total['产值']=$total1['产值'];
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$total;
        echo json_encode($data);
        
    }
   public  function getpersonstat()
    {   
        $where['1']=1;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['主修人']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $where['主修人']='陈康';
        $yelist=M('个人业绩表','dbo.','difo')->query("SELECT sum([工时费]) 工时
      ,[时间]
      ,[主修人]
      ,sum([服务车辆数]) 台次
      ,sum([产值]) 产值
  FROM [qxqpt2009].[dbo].[个人业绩表] group by [主修人],[时间]");
        foreach($$yelist as $item){
            
        }
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public function crquery()
    {
        if(IS_POST)
        {
            $code=$_POST['code'];
            $pjlist=null;
            if($code!='')
                $pjlist=M('配件分类','dbo.','difo')->where(array('父项'=>$code))->select();
            echo json_encode($pjlist);
            exit;
        }
        else{
           
            $pjlist=M('配件分类','dbo.','difo')->where(array('级别'=>'0'))->select();
            $this->assign('pjlist',$pjlist);
            $this->display();
        }
    }

    public  function getstacks()
    {   

        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['类别']=array('like','%'.trim($_POST['lb'].'%'));
            
        }
        if($searchkey){                  
            if($_POST['shop']&&$_POST['shop']!='all'){
                $searchwhere['配件目录.编号']=array('like',$searchkey);
             }else{
                 $searchwhere['编号']=array('like',$searchkey);
            }
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if($_POST['flag'])
        {
            if($_POST['shop']&&$_POST['shop']!='all'){
                $where['_string']='isnull(出入库统计.库存,0)<isnull(出入库统计.警戒下限,0)';
            }else{
                $where['_string']='isnull(配件库存.库存,0)<isnull(配件库存.警戒下限,0)';
            }
        }
        if($_POST['shop']&&$_POST['shop']!='all'){
            $cangku=$_POST['shop'];
            $where['配件目录.停用']=0;
            $yelist=M('配件目录','dbo.','difo')
                   ->join(' left join  出入库统计  on 配件目录.编号=出入库统计.编号  and  出入库统计.仓库=\''.$cangku.'\' left join 信息分类统计  on 配件目录.编码=信息分类统计.类型')
                   ->field('配件目录.*,出入库统计.仓库,isnull(出入库统计.库存,0) 分库存,isnull(出入库统计.警戒下限,0) 警戒线,出入库统计.出库数量,出入库统计.入库数量,信息分类统计.数量')
                   ->where($where)->limit(($page-1)*$pagesize,$pagesize)
                   ->order("$sortname  $sortorder")
                   ->select();
            $count=M('配件目录','dbo.','difo')->where($where)
                   ->join(' left join 出入库统计  on 配件目录.编号=出入库统计.编号  and  出入库统计.仓库=\''.$cangku.'\' left join 信息分类统计  on 配件目录.编码=信息分类统计.类型')
                ->count();
            $StockTotal=M('配件目录','dbo.','difo')
                  ->join(' left join 出入库统计  on 配件目录.编号=出入库统计.编号  and  出入库统计.仓库=\''.$cangku.'\' left join 信息分类统计  on 配件目录.编码=信息分类统计.类型')
                  ->field('sum(出入库统计.库存) 分库存,sum(配件目录.库存) 总库存,sum(参考进价*配件目录.库存) 总进货价,sum(参考售价*配件目录.库存) 总价值,sum(出入库统计.出库数量) 总出库数,sum(出入库统计.入库数量) 总入库数')
                  ->where($where)->find();

        }
        else{
            $count=M('配件库存','dbo.','difo')->where($where)->count();
            $yelist=M('配件库存','dbo.','difo')->where($where)
                ->limit(($page-1)*$pagesize,$pagesize)
                ->field('配件库存.*,配件库存.警戒下限 警戒线')
                ->order("$sortname  $sortorder")->select();
            $StockTotal=M('配件库存','dbo.','difo')->where($where)
            ->field('0 分库存,sum(库存) 总库存,sum(参考进价*库存) 总进货价,sum(参考售价*库存) 总价值,sum(出库数量) 总出库数,sum(入库数量) 总入库数')
            ->find();
        }
       
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['StockTotal']=$StockTotal;
        echo json_encode($data);
        
    }
    public function ordering(){
        if(IS_POST){
            $mc=$_POST['mc'];
            $gg=$_POST['gg'];
            $pp=$_POST['pp'];
            $num=$_POST['num'];
            $wechaname=$_POST['wechaname'];
            $product=M('配件目录','dbo.','difo')->where(array('名称'=>$mc,'规格'=>$gg,'品牌'=>$pp))->find();

            $user=M('往来单位','dbo.','difo')->where(array('名称'=>array('like',split(' ',$_POST['user'])[0])))->find();
            $data['ID']=$this->getcode(18,0,1);;
            $data['单据编号']=$this->getcodenum('SS');
            $data['制单日期']=date('Y-m-d',time());
            $data['制单人']='系统自动';
            $data['客户名称']=$user['名称'];
            $data['客户ID']=$user['ID'];
            $data['门店']='塘坑店';
            $data['销售类别']='汽车轮胎';
            $data['发票类别']='';
            $data['发票号码']='';
            $data['运费']=0;
            $data['结算方式']='欠款';
            $data['货运方式']='';
            $data['业务员']='系统';
            $data['整单折扣']=1;
            $data['收款期限']='';
            //$data['送货地址']=;
            $data['备注']='客户自助下单';
            $data['当前状态']='待审核';
            $data['合计数量']=$num+1;
            $data['实际货款']=$product['一级批发价'];
            $data['实际税额']=0;
            $data['虚增货款']=0;
            $data['虚增税额']=0;
            $data['价税合计']=$product['一级批发价']*$num;
            $data['总金额']=$product['一级批发价']*$num;
            $data['已结算金额']=0;
            $data['未结算金额']=$product['一级批发价']*$num;
            $data['单据类别']='销售出库';
            $data['应结金额']=$product['一级批发价']*$num;
            $data['挂账金额']=$product['一级批发价']*$num;
            $data['优惠金额']=0;
            $data['取用预存']=0;
            $data['会员编号']=$user['名称'];
            $data['使用积分']=0;
            M('销售单','dbo.','difo')->add($data);
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['默认仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=$num;
            $crk['单价']=$product['一级批发价'];
            $crk['成本价']=$product['参考进价'];
            $crk['金额']=$product['一级批发价']*$num;
            $crk['折扣']=1;
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            M('销售明细','dbo.','difo')->add($crk);
            $product=M('配件目录','dbo.','difo')->where(array('名称'=>'送货费'))->find();
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['默认仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=1;
            $crk['单价']=$product['一级批发价'];
            $crk['成本价']=$product['参考进价'];
            $crk['金额']=$product['一级批发价'];
            $crk['折扣']=1;
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            M('销售明细','dbo.','difo')->add($crk);

            $model=new templateNews();
            $booturl='https://oapi.dingtalk.com/robot/send?access_token=2477f2bc29e472747c2e75e01bb1ab2b405221c2ce152dc13307b4dda5fa28d7';
            $tangkeng='https://oapi.dingtalk.com/robot/send?access_token=9e3c1b9e17029774dc6f2749a82eb01d61555daa57c9cfbacc5b129d141d55e8';
            $qufu='https://oapi.dingtalk.com/robot/send?access_token=ca22bf1b681e1b7d842c8ee8741dd4ff392934b1ffd0ae35530f9d69a61bfed1';
            $content=date('Y-m-d H:i',time()).','.$user['名称'].$wechaname.'下单'.$pp.$mc.$gg.'的轮胎'.$num.'条,电话:'.end(explode(' ',$_POST['user']));
            $content.=',请马上安排送货，超过30分钟将免送货费。'; 
            $id=$data['ID'];
            $msgdata='{
            "title": "下单信息", 
            "actionCard": {
                "title": "客户下单信息", 
                "text": "'.$content.'", 
                "hideAvatar": "0", 
                "btnOrientation": "1", 
                "btns": [
                        {
                            "title": "开始送货", 
                            "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=changeordertime&lb=1&id='.$id.'"
                        },
                        {
                            "title": "货物送达", 
                            "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=changeordertime&lb=2&id='.$id.'"  
                        }
                        ]
            }, 
        "msgtype": "actionCard",
        }';
            $model->postMessage($booturl,$msgdata);
            $model->postMessage($tangkeng,$msgdata);
            echo '下单成功';

        }
        else{
            $this->display();
        }
        
    }

    public  function getstacksbyshop()
    {   

        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['类别']=array('like','%'.trim($_POST['lb'].'%'));
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='all')
        {
            $where['仓库']=array('like','%'.trim($_POST['shop'].'%'));
            
        }
        $where['门店']=array('in',explode(',',cookie('department')));
        if($searchkey){                  
            $searchwhere['编号']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if($_POST['flag'])
        {
          $where['_string']='isnull(库存,0)<isnull(警戒下限,0)';
        }
       
            $yelist=M('分仓配件库存','dbo.','difo')
                   ->where($where)->limit(($page-1)*$pagesize,$pagesize)
                   ->order("$sortname  $sortorder")
                   ->select();
            $count=M('分仓配件库存','dbo.','difo')->where($where)
                ->count();
            $StockTotal=M('分仓配件库存','dbo.','difo')
                  ->field('sum(分仓配件库存.库存) 总库存,sum(参考进价*库存) 总进货价,sum(参考售价*库存) 总价值,sum(出库数量) 总出库数,sum(入库数量) 总入库数')
                  ->where($where)->find();
        
       
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['StockTotal']=$StockTotal;
        echo json_encode($data);
        
    }
    public  function getproducts()
    {   

        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $lb=trim($_POST['lb']);
        $startdate=$_POST['startdate'];
        $enddate=$_POST['enddate'];
        $searchkey=trim($_POST['searchkey']);
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        if($startdate==''){
            $startdate=date('Y-m-01',time());
        }
        if($enddate==''){
            $enddate=date('Y-m-d',time());
        }
        $cangku='';
        if($_POST['shop']&&$_POST['shop']!='all'){
            $cangku=$_POST['shop'];
        }          
        $yelist=M('出入库统计数量','dbo.','difo')->query("exec 统计出入库数 '$startdate','$enddate','$cangku','$lb',$page,$pagesize,'$searchkey'");
        $StockTotal=M('出入库统计数量','dbo.','difo')->query("exec 配件出库库统计数 '$startdate','$enddate','$cangku','$lb','$searchkey'");
        $count=$StockTotal[0]['数量'];        
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['StockTotal']=$StockTotal;
        echo json_encode($data);
        
    }
    public  function exportproductlist(){
        $filename=date('Y_m_d',time());
		header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=进货数据$filename.xls");
		$arr = array(
			array('en'=>'仓库','cn'=>'仓库'),
			array('en'=>'编号','cn'=>'编号'),
			array('en'=>'名称','cn'=>'名称'),
			array('en'=>'规格','cn'=>'规格'),
			array('en'=>'品牌','cn'=>'品牌'),
			array('en'=>'库存','cn'=>'总库存'),
			array('en'=>'分库存','cn'=>'分库存'),
            array('en'=>'警戒线','cn'=>'警戒线'),
            array('en'=>'进货数量','cn'=>'进货数量'),
			array('en'=>'数量','cn'=>'使用车辆数'),
			array('en'=>'入库数量','cn'=>'入库数量'),
			array('en'=>'出库数量','cn'=>'出库数量'),
			array('en'=>'最新进价','cn'=>'最新进价'),
			array('en'=>'参考进价','cn'=>'参考进价'),
			array('en'=>'参考售价','cn'=>'参考售价'),
			array('en'=>'一级批发价','cn'=>'批发价'),
			array('en'=>'备注','cn'=>'适用车型'),
		);
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['lb']&&trim($_GET['lb'])!='')
        {
            $where['类别']=array('like','%'.trim($_GET['lb'].'%'));
            
        }
        if($searchkey){                  
            if($_GET['shop']&&$_GET['shop']!='all'){
                $searchwhere['配件目录.编号']=array('like',$searchkey);
            }else{
                $searchwhere['编号']=array('like',$searchkey);
            }
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if($_GET['flag'])
        {
            if($_GET['shop']&&$_GET['shop']!='all'){
                $where['_string']='isnull(出入库统计.库存,0)<isnull(出入库统计.警戒下限,0)';
            }else{
                $where['_string']='isnull(配件库存.库存,0)<isnull(配件库存.警戒下限,0)';
            }
        }
        if($_GET['shop']&&$_GET['shop']!='all'){
            $cangku=$_GET['shop'];
            $products=M('配件目录','dbo.','difo')
                   ->join(' join 出入库统计  on 配件目录.编号=出入库统计.编号  and  出入库统计.仓库=\''.$cangku.'\' left join 信息分类统计  on 配件目录.编码=信息分类统计.类型')
                   ->field('配件目录.*,出入库统计.仓库,isnull(出入库统计.库存,0) 分库存,isnull(出入库统计.警戒下限,0) 警戒线,出入库统计.出库数量,出入库统计.入库数量,信息分类统计.数量,出入库统计.警戒下限-出入库统计.库存 进货数量')
                   ->where($where)->order("$sortname  $sortorder")
                   ->select();

        }
        else{
            $products=M('配件库存','dbo.','difo')->where($where)
                ->field('配件库存.*,配件库存.警戒下限 警戒线,配件库存.警戒下限-配件库存.库存 进货数量')
                ->order("$sortname  $sortorder")->select();
        }
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
       
		if($products){
			foreach ($products as $product){
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $product[$field['en']];
                    //switch($field['en']){		
                    
                    //}
					
					if ($j<$fieldCount-1){
						echo iconv('utf-8','gbk',$fieldValue)."\t";
					}else {
						echo iconv('utf-8','gbk',$fieldValue)."\n";
					}
					$j++;
				}
				$i++;
			}
            
		}
		exit();
        
	}

    public  function getcrklist()
    {   

        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if(!isset($sortname)){
            $sortname='单据编号';
            $sortorder='desc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['出入库单.单据类别']=$_POST['lb'];
            
        }
        if($_POST['code']&&trim($_POST['code'])!='')
        {
            $where['编号']=$_POST['code'];
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='all')
        {   
            $cangku=$_POST['shop'];
            $where['仓库']=$cangku;
            
        }
        if($searchkey){       
            $searchwhere['编号']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if($_POST['lb']=='入库'){
            $count=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID left join 采购单 on 采购单.单据编号=出入库单.引用单号')->where($where)->count();
            $yelist=M('出入库明细','dbo.','difo')
                ->join('left join 出入库单 on 出入库明细.ID=出入库单.ID left join 采购单 on 采购单.单据编号=出入库单.引用单号')->where($where)
                ->field('出入库明细.*,出入库单.单据编号,出入库单.车牌号码,出入库单.原因,出入库单.引用单号,出入库单.引用类别,出入库单.制单日期,出入库单.审核日期,采购单.供应商 客户名称')
                ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        }else{
            $count=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID left join 销售单 on 销售单.单据编号=出入库单.引用单号')->where($where)->count();
            $yelist=M('出入库明细','dbo.','difo')
                ->join('left join 出入库单 on 出入库明细.ID=出入库单.ID left join 销售单 on 销售单.单据编号=出入库单.引用单号')->where($where)
                ->field('出入库明细.*,出入库单.单据编号,出入库单.车牌号码,出入库单.原因,出入库单.引用单号,出入库单.引用类别,出入库单.制单日期,出入库单.审核日期,销售单.客户名称')
                ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        }
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getcarsinfo()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['khlb'])&&trim($_POST['khlb'])!=''){
            $where['客户类别']=array('in',explode(';',$_POST['khlb']));
        }
        if (isset($_POST['sfzy'])&&trim($_POST['sfzy'])!=''){
            $where['是否在用']=$_POST['sfzy'];
        } 
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        $where['车牌号码']=array('neq','0000');
        if($_POST['where']!=''){
            $cond=json_decode($_POST['where']);
            $conditions=$cond->rules;
            $opt=$cond->op;
            $maps['equal']='eq';
            $maps['notequal']='neq';
            $maps['like']='like';
            if($opt=='and'){
                foreach($conditions as $condition){
                    if($condition->op=='equal'||$condition->op=='notequal')
                        $value=array($maps[$condition->op],$condition->value);
                    else
                        $value=array('like','%'.$condition->value.'%');
                    $where[$condition->field]=$value;
                }
            }
            else{
                
            }
        }
        if($searchkey){       
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['轮胎规格']=array('like',$searchkey);
            $searchwhere['车型']=array('like',$searchkey);
            $searchwhere['电池']=array('like',$searchkey);
            $searchwhere['车架号']=array('like',$searchkey);
            $searchwhere['机油格']=array('like',$searchkey);
            $searchwhere['空气格']=array('like',$searchkey);
            $searchwhere['冷气格']=array('like',$searchkey);
            $searchwhere['汽油格']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('车辆资料','dbo.','difo')
            ->join('left join 会员消费分析 on 车辆资料.车牌号码=会员消费分析.车牌')
            ->where($where)->count();
        $yelist=M('车辆资料','dbo.','difo')
            ->join('left join 会员消费分析 on 车辆资料.车牌号码=会员消费分析.车牌')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }

    public  function getbalance()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['szxm'])&&trim($_POST['szxm'])!=''){
            $where['收支项目']=$_POST['szxm'];
        }
        $where['当前状态']='待审核';

        if (isset($_POST['jsfs'])&&trim($_POST['jsfs'])!=''){
            $where['结算方式']=$_POST['jsfs'];
        } 
        if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['发票类别']=$_POST['shop'];
        } 
        else{
            $where['发票类别']=array('in',explode(',',cookie('department')));

        } 
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['收支项目']=array('like',$searchkey);
            $searchwhere['账款类别']=array('like',$searchkey);
            $searchwhere['结算方式']=array('like',$searchkey);
            $searchwhere['发票号']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('日常收支','dbo.','difo')
            ->where($where)->count();
        $sumdata=M('日常收支','dbo.','difo')
           ->where($where)->field('sum(实付金额) 实付金额 ,sum(实收金额) 实收金额')->find();;
        $yelist=M('日常收支','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }
    public  function getbalancesum()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }

        if (isset($_POST['szxm'])&&trim($_POST['szxm'])!=''){
            $where['收支项目']=$_POST['szxm'];
        }
        if (isset($_POST['jsfs'])&&trim($_POST['jsfs'])!=''){
            $where['结算方式']=$_POST['jsfs'];
        } 
        if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['门店']=$_POST['shop'];
        } 
        else{
            $where['门店']=array('in',explode(',',cookie('department')));

        } 
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['对账摘要']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['_string']=' 应收款<>0 or 应付款<>0 or 往来余款<>0';
        $count=M('往来单位','dbo.','difo')
            ->where($where)->count();
        $sumdata=M('往来单位','dbo.','difo')
           ->where($where)->field('sum(应收款) 应收款 ,sum(应付款) 应付款,sum(往来余款) 往来余款')->find();;
        $yelist=M('往来单位','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }
    public  function getbalancequery()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['szxm'])&&trim($_POST['szxm'])!=''){
            $where['收支项目']=$_POST['szxm'];
        }
        $where['当前状态']='已审核';
        if (isset($_POST['jsfs'])&&trim($_POST['jsfs'])!=''){
            $where['结算方式']=$_POST['jsfs'];
        } 
        if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['发票类别']=$_POST['shop'];
        } 
        else{
            $where['发票类别']=array('in',explode(',',cookie('department')));

        } 
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        else{
            $where['制单日期']=array('egt',date('Y-m-01 00:00',time()));
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['收支项目']=array('like',$searchkey);
            $searchwhere['账款类别']=array('like',$searchkey);
            $searchwhere['结算方式']=array('like',$searchkey);
            $searchwhere['发票号']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('日常收支','dbo.','difo')
            ->where($where)->count();
        $sumdata=M('日常收支','dbo.','difo')
           ->where($where)->field('sum(实付金额) 实付金额 ,sum(实收金额) 实收金额')->find();      
        $yelist=M('日常收支','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }
    public function  balancecheck(){      
        if(IS_POST){
            $ids=$_POST['ids'];
            $crkitem['当前状态']='已审核';
            $crkitem['审核人']=cookie('username');
            $crkitem['审核日期']=date('Y-m-d',time());
            M('日常收支','dbo.','difo')->where(array('ID'=>array('in',$ids)))->save($crkitem);
            //$this->writeLog($crk['ID'],$crk['单据编号'],'出库审核',$crk['单据类别'].'出库审核');
            echo '审核通过';
            exit;
        }

    }
    public function  balanceuncheck(){      
        if(IS_POST){
            $record=$_POST['record'];
            $crkitem['当前状态']='待审核';
            $crkitem['审核人']=cookie('username');
            $crkitem['审核日期']=date('Y-m-d',time());
            M('日常收支','dbo.','difo')->where(array('ID'=>$record['ID']))->save($crkitem);
            //$this->writeLog($crk['ID'],$crk['单据编号'],'出库审核',$crk['单据类别'].'出库审核');
            echo '反审核成功';
            exit;
        }

    }
    public function cancelbill()
    {  
        if(IS_POST){
            $record=$_POST['record'];
            $data['当前状态']='已作废'; 
            $data['审核人']=cookie('username');
            $data['审核日期']=date('Y-m-d',time());
            M('日常收支','dbo.','difo')->where(array('流水号'=>$record['流水号']))->save($data);
            echo '单据已作废';
            exit;
        }
    }

   public function receivedpaycheck(){      
        if(IS_POST){
            $record=$_POST['record'];
            $crkitem['当前状态']='已审核';
            $crkitem['审核人']=cookie('username');
            $crkitem['审核日期']=date('Y-m-d',time());
            M('应收应付单','dbo.','difo')->where(array('ID'=>$record['ID']))->save($crkitem);
            $dwid=$record['单位编号'];
            $je=$record['总金额'];
            if($record['账款类别']=='应收款'){
                M('应收应付单','dbo.','difo')->execute("UPDATE 往来单位 SET 应收款=应收款+$je,往来余款=往来余款+$je WHERE ID='$dwid'");
            }else{
                M('应收应付单','dbo.','difo')->execute("UPDATE 往来单位 SET 应付款=应付款+$je,往来余款=往来余款-$je WHERE ID='$dwid'");
               
            }
            //$this->writeLog($crk['ID'],$crk['单据编号'],'出库审核',$crk['单据类别'].'出库审核');
            echo '审核通过';
            exit;
        }

    }
   public  function balancecheckall()
    {   
        if (isset($_POST['szxm'])&&trim($_POST['szxm'])!=''){
            $where['收支项目']=$_POST['szxm'];
        }
        $where['当前状态']='待审核';
        if (isset($_POST['jsfs'])&&trim($_POST['jsfs'])!=''){
            $where['结算方式']=$_POST['jsfs'];
        } 
        if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['发票类别']=$_POST['shop'];
        }
        else{
            $where['发票类别']=array('in',explode(',',cookie('department')));

        } 
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['收支项目']=array('like',$searchkey);
            $searchwhere['账款类别']=array('like',$searchkey);
            $searchwhere['结算方式']=array('like',$searchkey);
            $searchwhere['发票号']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $crkitem['当前状态']='已审核';
        $crkitem['审核人']=cookie('username');
        $crkitem['审核日期']=date('Y-m-d',time());
        M('日常收支','dbo.','difo')->where($where)->save($crkitem);
        //$this->writeLog($crk['ID'],$crk['单据编号'],'出库审核',$crk['单据类别'].'出库审核');
        echo '审核通过';
        exit;;
        
    }
   public  function getrecevieandpay()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if($_GET['khID']){
            $where['当前状态']='已审核';
            $where['单位编号']=$_GET['khID'];
            $where['未结算金额']=array('gt',0);

        }else{
            $where['当前状态']='待审核';
        }
       if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['门店']=$_POST['shop'];
        }else{
             $where['门店']=array('in',explode(',',cookie('department')));

         } 
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['账款类别']=array('like',$searchkey);
            //$searchwhere['门店']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('应收应付单','dbo.','difo')
            ->where($where)->count();
        $sumdata=M('应收应付单','dbo.','difo')
           ->where($where)->field('sum(总金额) 总金额')->find();
           $yelist=M('应收应付单','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }
   public  function getrecevieandpayquery()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if($_GET['khID']){
            $where['当前状态']='已审核';
            $where['单位编号']=$_GET['khID'];
            $where['未结算金额']=array('gt',0);

        }else{
            $where['当前状态']=array('neq','待审核');
        }
       if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['门店']=$_POST['shop'];
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['账款类别']=array('like',$searchkey);
            //$searchwhere['门店']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('应收应付单','dbo.','difo')
            ->where($where)->count();
        $sumdata=M('应收应付单','dbo.','difo')
           ->where($where)->field('sum(总金额) 总金额')->find();
           $yelist=M('应收应付单','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }

    public  function getallrecevie()
    {   
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if($_GET['khID']){
            $where['当前状态']='已审核';
            $where['单位编号']=$_GET['khID'];
            $where['未结算金额']=array('gt',0);

        }
       if (isset($_POST['zklb'])&&trim($_POST['zklb'])!=''){
            $where['账款类别']=$_POST['zklb'];
        } 
        if (isset($_POST['shop'])&&trim($_POST['shop'])!=''){
            $where['门店']=$_POST['shop'];
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['账款类别']=array('like',$searchkey);
            //$searchwhere['门店']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['单位名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['摘要']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $sumdata=M('应收应付单','dbo.','difo')
           ->where($where)->field('sum(总金额) 总金额')->find();
           $yelist=M('应收应付单','dbo.','difo')->where($where)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=count($yelist);
        $data['sumdata']=$sumdata;
        echo json_encode($data);
        
    }
   public  function gettracemessage()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='跟踪时间';
            $sortorder='desc';
        }
        $where['跟踪类型']='到店消费';
        if (isset($_POST['sffk'])&&trim($_POST['sffk'])!=''){
            $where['是否反馈']=$_POST['sffk'];
        }
        if (isset($_POST['shop'])&&trim($_POST['shop'])!='all'){
            $where['门店']=$_POST['shop'];
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if (isset($_POST['gzr'])&&trim($_POST['gzr'])!=''){
            $where['跟踪人']=array('like','%'.trim($_POST['gzr']).'%');
        }
        if (isset($_POST['lb'])&&trim($_POST['lb'])!=''){
            $where['类别']=$_POST['lb'];
        }
        if (isset($_POST['sfcj'])&&trim($_POST['sfcj'])!=''){
            $where['是否成交']=$_POST['sfcj'];
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['跟踪时间']=array('egt',trim($_POST['startDate']));
            
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['跟踪时间']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['跟踪时间']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        if($searchkey){       
            $searchwhere['内容']=array('like',$searchkey);
            //$searchwhere['门店']=array('like',$searchkey);
            $searchwhere['跟踪人']=array('like',$searchkey);
            $searchwhere['登记人']=array('like',$searchkey);
            $searchwhere['反馈内容']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('客户跟踪','dbo.','difo')
            ->where($where)->count();
        $where1=$where;
        $where1['是否反馈']='是';
        $fkcount=M('客户跟踪','dbo.','difo')
            ->where($where1)->count();
        $where1=$where;
        $where1['是否成交']='是';
        $cjcount=M('客户跟踪','dbo.','difo')
            ->where($where1)->count();
        $yelist=M('客户跟踪','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['fkcount']=$fkcount;
        $data['cjcount']=$cjcount;
        echo json_encode($data);
        
    }
    public  function getcarsAnnual()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='年检日期';
            $sortorder='asc';
        }
        if (isset($_POST['khlb'])&&trim($_POST['khlb'])!=''){
            $where['客户类别']=array('in',explode(';',$_POST['khlb']));
        }
        $where['是否在用']='是';

        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        $where['车牌号码']=array('neq','0000');
        if($_POST['sfmj']&&trim($_POST['sfmj'])!='')
        {
            $where['是否免检']=trim($_POST['sfmj']);
            
        }        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['年检日期']=array('egt',trim($_POST['startDate']));
            
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['年检日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['年检日期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        $where['_string']="年检日期 is not null and 年检日期<>'1900-01-01'";
        if($searchkey){       
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['轮胎规格']=array('like',$searchkey);
            $searchwhere['车型']=array('like',$searchkey);
            $searchwhere['运输证号']=array('like',$searchkey);
            $searchwhere['车架号']=array('like',$searchkey);
            $searchwhere['机油格']=array('like',$searchkey);
            $searchwhere['空气格']=array('like',$searchkey);
            $searchwhere['冷气格']=array('like',$searchkey);
            $searchwhere['汽油格']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->count();
        $yelist=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getinsurances()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='交保到期';
            $sortorder='asc';
        }
        if (isset($_POST['khlb'])&&trim($_POST['khlb'])!=''){
            $where['客户类别']=array('in',explode(';',$_POST['khlb']));
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        $where['车牌号码']=array('neq','0000');
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['交保到期']=array('egt',trim($_POST['startDate']));
            
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['交保到期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['交保到期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        $where['_string']="交保到期 is not null and 交保到期<>'1900-01-01'";
        if($searchkey){       
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['轮胎规格']=array('like',$searchkey);
            $searchwhere['车型']=array('like',$searchkey);
            $searchwhere['运输证号']=array('like',$searchkey);
            $searchwhere['车架号']=array('like',$searchkey);
            $searchwhere['机油格']=array('like',$searchkey);
            $searchwhere['空气格']=array('like',$searchkey);
            $searchwhere['冷气格']=array('like',$searchkey);
            $searchwhere['汽油格']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['是否在用']='是';
        $count=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->count();
        $yelist=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }

    public  function getoilchange()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='下次保养';
            $sortorder='asc';
        }
        if (isset($_POST['khlb'])&&trim($_POST['khlb'])!=''){
            $where['客户类别']=array('in',explode(';',$_POST['khlb']));
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        $where['车牌号码']=array('neq','0000');
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['下次保养']=array('egt',trim($_POST['startDate']));
            
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['下次保养']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['下次保养']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        $where['_string']=" (下次保养 is not null and 下次保养<>'1900-01-01' and 下次保养>=getdate()) or ( isnull(保养里程,0)>0 and isnull(里程,0)>isnull(下次保养里程,0)) ";
        if($searchkey){       
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['轮胎规格']=array('like',$searchkey);
            $searchwhere['车型']=array('like',$searchkey);
            $searchwhere['运输证号']=array('like',$searchkey);
            $searchwhere['车架号']=array('like',$searchkey);
            $searchwhere['机油格']=array('like',$searchkey);
            $searchwhere['空气格']=array('like',$searchkey);
            $searchwhere['冷气格']=array('like',$searchkey);
            $searchwhere['汽油格']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['是否在用']='是';
        $count=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->count();
        $yelist=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getuserconsume()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $fwgw=$_POST['fwgw'];
        $khlb=$_POST['khlb'];
        $startdate=$_POST['startdate'];
        $enddate=$_POST['enddate'];
        $unconsume=$_POST['unconsume'];
        $searchkey=trim($_POST['searchkey']);
        if($startdate==''){
            $startdate=date('Y-01-01',time());
        }
        if($enddate==''){
            $enddate=date('Y-m-d',time());
        }
        $flag=100000;
        if($unconsume!='')
        {
            $startdate=date('Y-m-d',strtotime("-$unconsume month"));
            $enddate=date('Y-m-d',time());
            $flag=0;

        }
        if($searchkey){       
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $yelist=M('车辆消费分析','dbo.','difo')->query("exec 车辆消费分析 '$startdate','$enddate','$fwgw','$khlb',$page,$pagesize,'$searchkey',$flag");
        $StockTotal=M('出入库统计数量','dbo.','difo')->query("exec 车辆消费统计数据 '$startdate','$enddate','$fwgw','$khlb','$searchkey',$flag");
        $count=$StockTotal[0]['数量'];        
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['StockTotal']=$StockTotal;
        echo json_encode($data);
        
    }
    public  function exportUseRecord(){
        header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=用户分析数据_".date('Ymd',time()).".xls");
		$arr = array(
			array('en'=>'车牌号码','cn'=>'车牌号码'),
			array('en'=>'车主','cn'=>'车主'),
			array('en'=>'联系人','cn'=>'联系人'),
			array('en'=>'联系电话','cn'=>'联系电话'),
			array('en'=>'客户类别','cn'=>'客户类别'),
			array('en'=>'会员等级','cn'=>'会员等级'),
			array('en'=>'保险客户','cn'=>'保险客户'),
			array('en'=>'服务顾问','cn'=>'服务顾问'),
			array('en'=>'洗车次数','cn'=>'洗车次数'),
			array('en'=>'洗车金额','cn'=>'洗车金额'),
            array('en'=>'美容次数','cn'=>'美容次数'),
			array('en'=>'美容金额','cn'=>'美容金额'),
			array('en'=>'维修数量','cn'=>'维修次数'),
			array('en'=>'维修金额','cn'=>'维修金额'),
			array('en'=>'保险次数','cn'=>'保险次数'),
			array('en'=>'保险金额','cn'=>'保险金额'),
			array('en'=>'代办次数','cn'=>'代办次数'),
			array('en'=>'代办金额','cn'=>'代办金额')
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
        $where['车牌号码']=array('neq','0000');
		//data
        if (isset($_GET['khlb'])&&trim($_GET['khlb'])!=''){
            $where['客户类别']=$_GET['khlb'];
        }
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])!=''){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['会员等级']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['保险客户']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $users=M('车辆资料','dbo.','difo')
           ->join('left join 会员消费分析 on 车辆资料.车牌号码=会员消费分析.车牌')
           ->where($where)->select();

		if($users){
			foreach ($users as $user){
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $user[$field['en']];
                    //switch($field['en']){		
						
                    //}
					
					if ($j<$fieldCount-1){
						echo iconv('utf-8','gbk',$fieldValue)."\t";
					}else {
						echo iconv('utf-8','gbk',$fieldValue)."\n";
					}
					$j++;
				}
				$i++;
			}
            
		}
		exit();
        
	}
    public  function exporttirequery(){
        header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=轮胎查询数据_".date('Ymd',time()).".xls");
		$arr = array(
			array('en'=>'optime','cn'=>'查询时间'),
			array('en'=>'name','cn'=>'微信名'),
			array('en'=>'nickname','cn'=>'备注名称'),
			array('en'=>'code','cn'=>'轮胎型号'),
			array('en'=>'num','cn'=>'查询次数'),
			array('en'=>'type','cn'=>'客户类别')
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
        $where['type']=array('neq','3');
        $searchkey=$_POST['searchkey'];
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])){
            $searchkey='%'.trim($searchkey).'%';
        }
        if (isset($_POST['startdate'])&&trim($_POST['startdate'])){
            $startdate=strtotime($_POST['startdate']);
            $where['optime']=array('gt',strtotime($_POST['startdate']));
        }else{
            $startdate=strtotime('2016-01-01');
        }
        if($searchkey){       
            $searchwhere['nickname']=array('like',$searchkey);
            $searchwhere['code']=array('like',$searchkey);
            $searchwhere['name']=array('like',$searchkey);
            $searchwhere['type']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='optime desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',optime desc';
        }
        $scoreinfo=M('tire_query')
                ->join("join ( select count(1) num,code  from tp_tire_query  where type!=3 and optime>$startdate  group by code) b on tp_tire_query.code=b.code")
                ->where($where)->order($order)->select();
        if($scoreinfo){
			foreach ($scoreinfo as $person){
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $person[$field['en']];
                    switch($field['en']){		
                        case 'optime':
                            $fieldValue =date('Y-m-d',$person[$field['en']]);
                            break;
                        case 'type':{
                             if($person[$field['en']]=='1'){
                                $fieldValue='外部修理厂';
                             }else{
                                $fieldValue='内部员工';
                               
                             }
                          break;
                        }
                    }
					
					if ($j<$fieldCount-1){
						echo iconv('utf-8','gbk',$fieldValue)."\t";
					}else {
						echo iconv('utf-8','gbk',$fieldValue)."\n";
					}
					$j++;
				}
				$i++;
			}
            
		}
		exit();    
        
    }

    public  function exportpersonwork()
    {   
        header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=个人业绩数据_".date('Ymd',time()).".xls");
		$arr = array(
			array('en'=>'业务编号','cn'=>'业务编号'),
			array('en'=>'制单日期','cn'=>'制单日期'),
			array('en'=>'接车人','cn'=>'接车人'),
			array('en'=>'维修类别','cn'=>'维修类别'),
			array('en'=>'车牌号码','cn'=>'车牌号码'),
            array('en'=>'车牌号码','cn'=>'车牌号码'),
            array('en'=>'车主','cn'=>'车主'),
            array('en'=>'客户类别','cn'=>'客户类别'),
            array('en'=>'款项总额','cn'=>'款项总额'),
            array('en'=>'工时费','cn'=>'工时费'),
            //array('en'=>'工时','cn'=>'工时数'),
            array('en'=>'结算日期','cn'=>'结算日期'),
            array('en'=>'主修人','cn'=>'主修人'),
            array('en'=>'门店','cn'=>'门店')
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
        $where['1']=1;
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['lb']&&trim($_GET['lb'])!='')
        {
            $where['维修类别']=trim($_GET['lb']);
            
        }
        if($_GET['bm']&&trim($_GET['bm'])!='')
        {
            $where['门店']=array('like','%'.trim($_GET['bm'].'%'));
            
        }
        if($_GET['startdate']&&trim($_GET['startdate'])!='')
        {
            $where['结算日期']=array('egt',trim($_GET['startdate']));
            
        }
        if($_GET['endDate']&&trim($_GET['enddate'])!='')
        {
            $where['结算日期']=array('elt',trim($_GET['endDate']));
            
        }
        if(trim($_GET['startdate'])!=''&&trim($_GET['enddate'])!='')
        {
            $where['结算日期']=array('BETWEEN',array(trim($_GET['startdate']),trim($_GET['enddate'])));
            
        }
        if($_GET['zhuxiu']&&trim($_GET['zhuxiu'])!='')
        {
            $where['主修人']=trim($_GET['zhuxiu']);
            
        }
        if(!isset($sortname)){
            $sortname='结算日期';
            $sortorder='desc';
        }
        if($searchkey){       
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['主修人']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $personinfo=M('维修','dbo.','difo')
            ->join('维修项目 on 维修项目.ID=维修.ID')
            ->where($where)->field('[业务编号],[制单日期] ,[接车人],[维修类别],[车牌号码],[车主],[客户类别],[款项总额],[工时费],[结算日期],[主修人],[门店]')
            ->order("$sortname  $sortorder")->select();
        if($personinfo){
			foreach ($personinfo as $person){
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $person[$field['en']];
                    switch($field['en']){		
                        case '结算日期':
                            $fieldValue =date('Y-m-d',strtotime($person[$field['en']]));
                            break;
                        case '制单日期':
                            $fieldValue =date('Y-m-d',strtotime($person[$field['en']]));
                            break;
                    }
					
					if ($j<$fieldCount-1){
						echo iconv('utf-8','gbk',$fieldValue)."\t";
					}else {
						echo iconv('utf-8','gbk',$fieldValue)."\n";
					}
					$j++;
				}
				$i++;
			}
            
		}
		exit();
    }
public  function exportpurchasedata(){
        header("Content-Type: text/html; charset=utf-8");
        header("Content-type:application/vnd.ms-execl");
        header("Content-Disposition:filename=采购数据_".date('Ymd',time()).".xls");
        $arr = array(
            array('en'=>'单据类别','cn'=>'单据类别'),
            array('en'=>'单据编号','cn'=>'单据编号'),
            array('en'=>'制单日期','cn'=>'制单日期'),
            array('en'=>'制单人','cn'=>'制单人'),
            array('en'=>'业务员','cn'=>'业务员'),
            array('en'=>'供应商','cn'=>'供应商'),
            array('en'=>'发票类别','cn'=>'发票类别'),
            array('en'=>'发票号码','cn'=>'发票号码'),
            array('en'=>'结算方式','cn'=>'结算方式'),
            array('en'=>'货运方式','cn'=>'货运方式'),
            array('en'=>'付款期限','cn'=>'付款期限'),
            array('en'=>'付款日期','cn'=>'付款日期'),
            array('en'=>'送货地址','cn'=>'送货地址'),
            array('en'=>'整单折扣','cn'=>'整单折扣'),
            array('en'=>'合计数量','cn'=>'合计数量'),
            array('en'=>'合计货款','cn'=>'合计货款'),
            array('en'=>'合计税额','cn'=>'合计税额'),
            array('en'=>'价税合计','cn'=>'价税合计'),
            array('en'=>'运费','cn'=>'运费'),
            array('en'=>'总金额','cn'=>'总金额'),
            array('en'=>'审核日期','cn'=>'审核日期'),
            array('en'=>'审核人','cn'=>'审核人'),
            array('en'=>'备注','cn'=>'备注')
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

    $sortname='流水号';
    $sortorder='desc';
    if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])!=''){
        $searchkey='%'.trim($_GET['searchkey']).'%';
    }
    $where['当前状态']='已审核';
    if(isset($_GET['shop'])&&$_GET['shop']!='all'){
        $where['门店']=$_GET['shop'];

    }else{
        $where['门店']=array('in',explode(',',cookie('department')));

    }
    if(isset($_GET['zdr'])){
        $where['制单人']=$_GET['zdr'];

    }
    if($_GET['startdate']&&trim($_GET['startdate'])!='')
    {
        $where['制单日期']=array('egt',trim($_GET['startdate']));

    }
    if($_GET['enddate']&&trim($_GET['enddate'])!='')
    {
        $where['制单日期']=array('elt',trim($_GET['enddate']));

    }
    if(trim($_GET['startdate'])!=''&&trim($_GET['enddate'])!='')
    {
        $where['制单日期']=array('BETWEEN',array(trim($_GET['startdate']),trim($_GET['enddate'])));

    }
    if($searchkey){
        $searchwhere['制单人']=array('like',$searchkey);
        $searchwhere['业务员']=array('like',$searchkey);
        $searchwhere['单据编号']=array('like',$searchkey);
        $searchwhere['发票号码']=array('like',$searchkey);
        $searchwhere['发票类别']=array('like',$searchkey);
        $searchwhere['供应商']=array('like',$searchkey);
        $searchwhere['车牌号码']=array('like',$searchkey);
        $searchwhere['备注']=array('like',$searchkey);
        $searchwhere['_logic']='OR';
        $where['_complex']=$searchwhere;

    }
    $yelist=M('采购单','dbo.','difo')->where($where)->order("$sortname  $sortorder")->select();
    if($yelist){
            foreach ($yelist as $item){
                $j = 0;
                foreach ($arr as $field){
                    $fieldValue = $item[$field['en']];
                    switch($field['en']){
                        case '制单日期':
                        case '付款日期':
                        case '审核日期':
                        $fieldValue =date('Y-m-d',strtotime($item[$field['en']]));
                            break;
                    }
                    if ($j<$fieldCount-1){
                        echo iconv('utf-8','gbk',$fieldValue)."\t";
                    }else {
                        echo iconv('utf-8','gbk',$fieldValue)."\n";
                    }
                    $j++;
                }
                $i++;
            }

        }
        exit();

    }
    public  function exportcarsinfo(){
		header("Content-Type: text/html; charset=utf-8");
		header("Content-type:application/vnd.ms-execl");
		header("Content-Disposition:filename=车辆数据_".date('Ymd',time()).".xls");
		$arr = array(
			array('en'=>'车牌号码','cn'=>'车牌号码'),
			array('en'=>'车主','cn'=>'车主'),
			array('en'=>'联系人','cn'=>'联系人'),
			array('en'=>'联系电话','cn'=>'联系电话'),
			array('en'=>'客户类别','cn'=>'客户类别'),
			array('en'=>'会员等级','cn'=>'会员等级'),
			array('en'=>'保险客户','cn'=>'保险客户'),
			array('en'=>'服务顾问','cn'=>'服务顾问'),
			array('en'=>'交保到期','cn'=>'保险到期'),
			array('en'=>'保险公司','cn'=>'保险公司'),
			array('en'=>'年检日期','cn'=>'年检到期'),
			array('en'=>'下次保养','cn'=>'下次保养'),
			array('en'=>'最近保养','cn'=>'最近保养'),
            array('en'=>'年份','cn'=>'年份'),
            array('en'=>'品牌','cn'=>'品牌'),
			array('en'=>'车型','cn'=>'车型'),
			array('en'=>'排量','cn'=>'年份排量'),
			array('en'=>'车架号','cn'=>'车架号'),
			array('en'=>'机油格','cn'=>'机油格'),
			array('en'=>'空气格','cn'=>'空气格'),
			array('en'=>'冷气格','cn'=>'冷气格'),
			array('en'=>'进厂次数','cn'=>'进厂次数'),
			array('en'=>'消费金额','cn'=>'消费金额')
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
        $where['车牌号码']=array('neq','0000');

        if($_GET['type']=='1')
            $sortname='交保到期';
        elseif($_GET['type']=='3')
            $sortname='下次保养';
        else
            $sortname='年检日期';
        $sortorder='asc';
        if (isset($_GET['khlb'])&&trim($_GET['khlb'])!=''){
            $where['客户类别']=$_GET['khlb'];
        }
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])!=''){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['startDate']&&trim($_GET['startDate'])!='')
        {
            $where["$sortname"]=array('egt',trim($_GET['startDate']));
            
        }
        if($_GET['endDate']&&trim($_GET['endDate'])!='')
        {
            $where["$sortname"]=array('elt',trim($_GET['endDate']));
            
        }
        if(trim($_GET['startDate'])!=''&&trim($_GET['endDate'])!='')
        {
            $where["$sortname"]=array('BETWEEN',array(trim($_GET['startDate']),trim($_GET['endDate'])));
            
        }
        $where['_string']="$sortname is not null and $sortname<>'1900-01-01'";
        if($searchkey){       
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['轮胎规格']=array('like',$searchkey);
            $searchwhere['车型']=array('like',$searchkey);
            $searchwhere['运输证号']=array('like',$searchkey);
            $searchwhere['车架号']=array('like',$searchkey);
            $searchwhere['机油格']=array('like',$searchkey);
            $searchwhere['空气格']=array('like',$searchkey);
            $searchwhere['冷气格']=array('like',$searchkey);
            $searchwhere['汽油格']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['服务顾问']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['发动机号']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $carsinfo=M('车辆资料','dbo.','difo')
            ->join('left join 维修统计 on 车辆资料.车牌号码=维修统计.车牌')
            ->where($where)->order("$sortname  $sortorder")->select();
		if($carsinfo){
			foreach ($carsinfo as $car){
				$j = 0;
				foreach ($arr as $field){			
					$fieldValue = $car[$field['en']];
                    switch($field['en']){		
                        case '年检日期':
                        case '交保到期':
                        case '下次保养':
                        case '最近保养':
                            $fieldValue =date('Y-m-d',strtotime($car[$field['en']]));
                            break;
                    }
					if ($j<$fieldCount-1){
						echo iconv('utf-8','gbk',$fieldValue)."\t";
					}else {
						echo iconv('utf-8','gbk',$fieldValue)."\n";
					}
					$j++;
				}
				$i++;
			}
            
		}
		exit();
        
	}

    public function getrecharge()
    {
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        $where['1']=1;
        if($_GET['khID'])
        {
            $where['tp_member_card_create.number']=trim($_GET['khID']);
        }
        if($_GET['type'])
        {
            $where['_string']="ordername like '%充值%' and paid=1";
        }  
        if(!isset($sortname)){
            $order='tp_member_card_pay_record.createtime desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
        if($_POST['bm']&&trim($_POST['bm'])!='')
        {
            $where['shop']=trim($_POST['bm']);
            
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['createtime']=array('egt',strtotime(trim($_POST['startdate'])));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['createtime']=array('elt',strtotime(trim($_POST['enddate'])));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['createtime']=array('BETWEEN',array(strtotime(trim($_POST['startdate'])),strtotime(trim($_POST['enddate']))+3600*24));
            
        }
        $count= M('Member_card_pay_record')
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
             ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
           ->where($where)->count();
		$rmb = M('Member_card_pay_record')
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();  
	    $records = M('Member_card_pay_record')
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->select(); 
       $weixin=0;
       $qiantai=0;
       if($records){
         foreach($records as $record){
             if($record['paytype']=='weixin'){
                 $weixin+=doubleval($record['price']);
             }else{
                 $qiantai+=doubleval($record['price']);

             }
         }
       }
        $data['Rows']=$rmb;
        $data['Total']=$count;
        $data['weixin']=$weixin;
        $data['qiantai']=$qiantai;
        $data['totalmoeny']=$qiantai+$weixin;
        echo json_encode($data);
		
    }

    public  function getcouponinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_GET['khID'])
        {
            $where['tp_member_card_create.number']=trim($_GET['khID']);
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='tp_member_card_coupon_record.id desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
       
        $count=M('member_card_coupon_record')
           ->join('join tp_member_card_create on tp_member_card_create.wecha_id=tp_member_card_coupon_record.wecha_id')
           ->where($where)->count();
        $coupons=M('member_card_coupon_record')
            ->join('join tp_member_card_create on tp_member_card_create.wecha_id=tp_member_card_coupon_record.wecha_id')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();        
        $data['Rows']=$coupons;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getbxinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_GET['khID'])
        {
            $where['客户ID']=trim($_GET['khID']);
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        if(isset($type)&&$type=='1')
        {
            $where['_string']="当前状态 not in ('结束','取消')";

        }
        $wxinfo=M('车辆保险','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('车辆保险','dbo.','difo')->where($where)->count();
        $data['Rows']=$wxinfo;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getdbinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_GET['khID'])
        {
            $where['客户ID']=trim($_GET['khID']);
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        if(isset($type)&&$type=='1')
        {
            $where['_string']="当前状态 not in ('结束','取消')";

        }
        $wxinfo=M('车辆代办','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('车辆代办','dbo.','difo')->where($where)->count();
        $data['Rows']=$wxinfo;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getxsinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_GET['khID'])
        {
            $where['客户ID']=trim($_GET['khID']);
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        if(isset($type)&&$type=='1')
        {
            $where['_string']="当前状态 not in ('结束','取消')";

        }
        $wxinfo=M('销售单','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('销售单','dbo.','difo')->where($where)->count();
        $data['Rows']=$wxinfo;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public function getwxinfobyid(){
        $carinfo=M('维修','dbo.','difo')->where(array('流水号'=>$_GET['code']))->find();
        echo json_encode($carinfo);

    }
    public function getmembers(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
		$card_create_db=M('Member_card_create');
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['tp_userinfo.shop']=$_POST['shop'];
            
        }
        else{
            //$where['tp_userinfo.shop']=array('in',explode(',',cookie('department')));

        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['tp_userinfo.getcardtime']=array('egt',strtotime($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['tp_userinfo.getcardtime']=array('elt',strtotime($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&strtotime($_POST['enddate'])!='')
        {
            $where['tp_userinfo.getcardtime']=array('BETWEEN',array(strtotime($_POST['startdate']),strtotime($_POST['enddate'])));
            
        }
		$where['tp_member_card_create.wecha_id']=array('neq','');
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
                $searchkey='%'.trim($_POST['searchkey']).'%';
		}       
        if($searchkey){       
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['shop']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='getcardtime desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
		$sumdata= $card_create_db->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)
            ->field('avg(expensetotal) avgexpence,sum(balance) balance,sum(total_score) total_score,count(1) count')->find(); 
		$list= $card_create_db
            ->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
		$data['Rows']=$list;
        $data['Total']=$sumdata['count'];
        $data['sumdata']=$sumdata;
        echo json_encode($data);
	}
    public function getcoupons(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_POST['coupontype']&&trim($_POST['coupontype'])!='')
        {
            $where['coupon_id']=$_POST['coupontype'];
            
        }
        if(trim($_POST['sfgq'])!='')
        {
            if($_POST['sfgq']=='1'){
             $where['over_time']=array('lt',time());
            }else{
              $where['over_time']=array('egt',time());
           }
            
        }
        if(trim($_POST['sfsy'])!='')
        {
            $where['is_use']=$_POST['sfsy'];
            
        }
        if($_POST['type']&&trim($_POST['type'])!='')
        {
            $where['coupon_type']=$_POST['type'];
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
                $searchkey='%'.trim($_POST['searchkey']).'%';
		}       
        if($searchkey){       
            $searchwhere['coupon_name']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='id desc';
        }
        else{
            $order=$sortname.' '.$sortorder;
        }
		$sumdata=  M('member_card_coupon_record')->where($where)
            ->count(); 
		$list= M('member_card_coupon_record')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
		$data['Rows']=$list;
        $data['Total']=$sumdata;
        echo json_encode($data);
	}

    public  function getcommentinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['khlb']&&trim($_POST['khlb'])!='')
        {
            $where['客户类别']=trim($_POST['khlb']);
            
        }
        if($_POST['zt']&&trim($_POST['zt'])!='')
        {
            $where['当前状态']=trim($_POST['zt']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startDate']));
            
        }
        else{
            $where['制单日期']=array('egt',date('Y-m-d 00:00',time()));
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        if($_POST['zhuxiu']){
            $where['主修人']=$_POST['zhuxiu'];

        }
        if($_POST['fwgw']){
            $where['接车人']=$_POST['fwgw'];

        }
        if($_POST['searchkey']){
            $where['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like','%'.$_POST['searchkey'].'%');

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        $wxCount=M('维修','dbo.','difo')->where($where)->count();
        $where['车主']=array('like','%AYC%');
        $hycount=M('维修','dbo.','difo')->where($where)->count();
        $where['是否评论']='是';
        $wxinfo=M('维修','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('维修','dbo.','difo')->where($where)->count();
        $TotalData=M('维修','dbo.','difo')->where($where)->field('AVG(convert(float,服务质量)) 服务质量,AVG(convert(float,服务态度)) 服务态度,AVG(convert(float,前台接待)) 前台接待')->find();
        $data['Rows']=$wxinfo;
        $data['Total']=$count;
        $data['hyCount']=$hycount;
        $data['TotalData']=$TotalData;
        $data['wxCount']=$wxCount;
        echo json_encode($data);
        
    }
    public  function gethistoryinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['khID'])
        {
            $where['客户ID']=trim($_POST['khID']);
        }
        if($_POST['khlb']&&trim($_POST['khlb'])!='')
        {
            $where['客户类别']=trim($_POST['khlb']);
            
        }
        if($_POST['carno']&&trim($_POST['carno'])!='')
        {
            $where['车牌号码']=trim($_POST['carno']);
            
        }
        if($_POST['zt']&&trim($_POST['zt'])!='')
        {
            $where['当前状态']=trim($_POST['zt']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startDate']));
            
        }
        
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        } 
        if($searchkey){       
            $where['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like',$searchkey);

        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        $wxCount=M('维修','dbo.','difo')->where($where)->count();
        $wxinfo=M('维修','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $data['Rows']=$wxinfo;
        $data['Total']=$wxCount;
        echo json_encode($data);
        
    }
    public  function getwashcarinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['维修类别']=array('in',array('蜡水洗车','汽车美容'));
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['khlb']&&trim($_POST['khlb'])!='')
        {
            $where['客户类别']=trim($_POST['khlb']);
            
        }
        if($_POST['zt']&&trim($_POST['zt'])!='')
        {
            $where['当前状态']=trim($_POST['zt']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['zhuxiu']&&trim($_POST['zhuxiu'])!='')
        {
            $where['主修人']=trim($_POST['zhuxiu']);
            
        }
        if($_POST['carno']&&trim($_POST['carno'])!='')
        {
            $where['车牌号码']=trim($_POST['carno']);
            
        }
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where1['制单日期']=array('egt',trim($_POST['startDate']));
            
        }
        else{
            if($_GET['type']!='4'&&$_GET['type']!='1'){
                $where1['制单日期']=array('egt',date('Y-m-d 00:00',time()));
            }
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where1['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where1['制单日期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        if($_POST['zt']==''){
            $where1['_string']="当前状态 not in ('结束','取消')";
            $where1['_logic']='OR';  
            $map['_complex']=$where1;
            $map['_logic']='and';
            if($searchkey){       
                $map['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like',$searchkey);

            }
            $where['_complex']=$map;

        }
        else{
            if($where1['制单日期'])
                $where['制单日期']=$where1['制单日期'];
            if($searchkey){ 
                $where['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like',$searchkey);
            }
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        $wxinfo=M('维修档案','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('维修','dbo.','difo')->where($where)->count();
        $where1=$where;
        $where1['_string']=" 当前状态 !='取消' and isnull(已处理,0)>10 and isnull(已处理,0)<100";
        $dealtime=M('维修','dbo.','difo')->where($where1)->field('sum(convert(int,isnull(已处理,0))) 已处理')->find();
        $where['车主']=array('like','%AYC%');
        $hycount=M('维修','dbo.','difo')->where($where)->count();
        $where['是否评论']='是';
        $TotalData=M('维修','dbo.','difo')->where($where)->field('count(1) commentcount,AVG(convert(float,服务质量)) 服务质量,AVG(convert(float,服务态度)) 服务态度,AVG(convert(float,前台接待)) 前台接待')->find();
        $data['Rows']=$wxinfo;
        $data['dealtime']=$dealtime['已处理'];
        $data['Total']=$count;
        $data['hyCount']=$hycount;
        $data['TotalData']=$TotalData;
      echo json_encode($data);
        
    }

    public  function getwxinfo(){
        $type=$_GET['type'];
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if($_GET['lb'])
            exit;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($type=='1'){
            $where['维修类别']=array('not in',array('蜡水洗车','汽车美容'));
        }else{
           
        }
       
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['overtime']=='1'){

           $where['_string']="实际完工>预计完工";
        }
        if($_POST['khlb']&&trim($_POST['khlb'])!='')
        {
            $where['客户类别']=trim($_POST['khlb']);
            
        }
        if($_POST['zt']&&trim($_POST['zt'])!='')
        {
            $where['当前状态']=trim($_POST['zt']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['zhuxiu']&&trim($_POST['zhuxiu'])!='')
        {
            $where['主修人']=trim($_POST['zhuxiu']);
            
        }
        if($_POST['carno']&&trim($_POST['carno'])!='')
        {
            $where['车牌号码']=trim($_POST['carno']);
            
        }
        if($_POST['startDate']&&trim($_POST['startDate'])!='')
        {
            $where1['制单日期']=array('egt',trim($_POST['startDate']));
            
        }
        else{
            if($_GET['type']!='4'&&$_GET['type']!='1'){
                $where1['制单日期']=array('egt',date('Y-m-d 00:00',time()));
            }
        }
        if($_POST['endDate']&&trim($_POST['endDate'])!='')
        {
            $where1['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startDate'])!=''&&trim($_POST['endDate'])!='')
        {
            $where1['制单日期']=array('BETWEEN',array(trim($_POST['startDate']),trim($_POST['endDate'])));
            
        }
        if($type!=4&&$_POST['zt']==''){
          $where1['_string']="当前状态 not in ('结束','取消')";
          $where1['_logic']='OR';  
          $map['_complex']=$where1;
          $map['_logic']='and';
          if($searchkey){       
              $map['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like',$searchkey);

          }
          $where['_complex']=$map;

        }
        else{
            if($where1['制单日期'])
               $where['制单日期']=$where1['制单日期'];
            if($searchkey){ 
                $where['主修人|制单人|接车人|业务编号|车主|车牌号码|联系人|联系电话']=array('like',$searchkey);
            }
        }
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $order='流水号 desc';
        }
        else{
            $order=$sortname.' '.$sortorder.',流水号 desc';
        }
        $wxinfo=M('维修档案','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('维修','dbo.','difo')->where($where)->count();
        $where1=$where;
        if($_POST['lb']=='蜡水洗车'){
            $where1['_string']=" 当前状态 !='取消' and isnull(已处理,0)>10 and isnull(已处理,0)<100";
        }else{
            $where1['_string']=" 当前状态 !='取消'";
        }
        $dealtime=M('维修','dbo.','difo')->where($where1)->field('sum(convert(int,isnull(已处理,0))) 已处理')->find();
        $where['车主']=array('like','%AYC%');
        $hycount=M('维修','dbo.','difo')->where($where)->count();
        $where['是否评论']='是';
        $TotalData=M('维修','dbo.','difo')->where($where)->field('count(1) commentcount,AVG(convert(float,服务质量)) 服务质量,AVG(convert(float,服务态度)) 服务态度,AVG(convert(float,前台接待)) 前台接待')->find();
        $data['Rows']=$wxinfo;
        $data['dealtime']=$dealtime['已处理'];
        $data['Total']=$count;
        $data['hyCount']=$hycount;
        $data['TotalData']=$TotalData;
      echo json_encode($data);
        
    }
    public  function getstockbycode(){
       
        $userinfo=M('配件仓位','dbo.','difo')->where(array('编号'=>$_POST['code']))->select();
        $data['Rows']=$userinfo;
        $data['Total']=count($userinfo);
        echo json_encode($data);
        
    }
    public  function getstock(){
        
        $data=M('配件目录','dbo.','difo')->where(array('编号'=>$_GET['code']))->find();
        echo json_encode($data);
        
    }
    public  function editprojectlist()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $projectlist=$_POST['project'];
            if($type&&$type=='add'){
                M('项目目录','dbo.','difo')->add($projectlist);
            }else{
                unset($projectlist['流水号']);
                M('项目目录','dbo.','difo')->where(array('项目编号'=>$projectlist['项目编号']))->save($projectlist);
            }
            echo '保存成功';
        }
    }
    public function savepayrecord(){
        $record=$_POST['record'];
        if($record['账款类别']=='收款单'){
            $bianhao=$this->getcodenum("BI");
            $data['本次应付']=0;
            $data['本次应收']=$record['金额'];
            $data['实付金额']=0;
            $data['实收金额']=$record['金额'];
            $data['单据类别']='应收款';

        }
        else{
            $bianhao=$this->getcodenum("BE");
            $data['本次应付']=$record['金额'];
            $data['本次应收']=0;
            $data['实付金额']=$record['金额'];
            $data['实收金额']=0;
            $data['单据类别']='应付款';

        }
        $data['单据编号']=$bianhao;
        $data['ID']=$this->getcode(20,1,1);
        $data['制单日期']=date('Y-m-d',time());
        $data['制单人']=cookie('username');
        $data['单位名称']=$record['客户名称'];
        $data['单位编号']=$record['userID'];
        $data['账款类别']=$record['账款类别'];
        $data['开户银行']='';
        $data['银行账号']='';
        $data['整单折扣']=1; 
        $data['折扣金额']=0;
        $data['结算方式']=$record['结算方式'];
        $data['结算账户']=$record['结算账户'];
        $data['支票号']=0;
        $data['凭证号']=0;
        $data['摘要']=$record['备注'];
        $data['收支项目']=$record['收支项目'];
        $data['当前状态']='待审核';
        $data['发票类别']=$record['门店'];
        $data['发票号']='';
        $data['取用预付']=0;
        $data['取用预收']=0;
        $data['取用预存']=0;
        $data['本次冲账']=$record['金额'];
        M('日常收支','dbo.','difo')->add($data);
    
    }
    public function savereceivebill(){
        $rows=$_POST['rows'];
        foreach($rows as $row){
            $balance['已结算金额']=$row['总金额'];
            $balance['未结算金额']=0;
            $balance['本次结算']=$row['总金额'];
            M('应收应付单','dbo.','difo')->where(array('流水号'=>$row['流水号']))->save($balance);
        }
        $record=$_POST['balance'];
        $je=$record['金额'];
        $dwid=$record['userID'];
        $bianhao=$this->getcodenum("BI");
        $data['本次应付']=0;
        $data['本次应收']=$record['金额'];
        $data['实付金额']=0;
        $data['实收金额']=$record['金额'];
        $data['单据类别']='应收款';
        $data['单据编号']=$bianhao;
        $data['ID']=$this->getcode(20,1,1);
        $data['制单日期']=date('Y-m-d',time());
        $data['制单人']=cookie('username');
        $data['单位名称']=$record['客户名称'];
        $data['单位编号']=$record['userID'];
        $data['账款类别']=$record['账款类别'];
        $data['开户银行']='';
        $data['银行账号']='';
        $data['整单折扣']=1; 
        $data['折扣金额']=0;
        $data['结算方式']=$record['结算方式'];
        $data['结算账户']=$record['结算账户'];
        $data['支票号']=0;
        $data['凭证号']=0;
        $data['摘要']=$record['备注'];
        $data['收支项目']=$record['收支类型'];
        $data['当前状态']='待审核';
        $data['发票类别']=$record['门店'];
        $data['发票号']='';
        $data['取用预付']=0;
        $data['取用预收']=0;
        $data['取用预存']=0;
        $data['本次冲账']=$record['金额'];
        M('日常收支','dbo.','difo')->add($data);
        M('应收应付单','dbo.','difo')->execute("UPDATE 往来单位 SET 应收款=应收款-$je,往来余款=往来余款-$je WHERE ID='$dwid'");

        echo '操作成功';
    
    }
    public function savepaybill(){
        $rows=$_POST['rows'];
        foreach($rows as $row){
            $balance['已结算金额']=$row['总金额'];
            $balance['未结算金额']=0;
            $balance['本次结算']=$row['总金额'];
            M('应收应付单','dbo.','difo')->where(array('流水号'=>$row['流水号']))->save($balance);
        }
        $record=$_POST['balance'];
        $je=$record['金额'];
        $dwid=$record['userID'];
        $bianhao=$this->getcodenum("BE");
        $data['本次应付']=$record['金额'];
        $data['本次应收']=0;
        $data['实付金额']=$record['金额'];
        $data['实收金额']=0;
        $data['单据类别']='应付款';
        $data['单据编号']=$bianhao;
        $data['ID']=$this->getcode(20,1,1);
        $data['制单日期']=date('Y-m-d',time());
        $data['制单人']=cookie('username');
        $data['单位名称']=$record['客户名称'];
        $data['单位编号']=$record['ID'];
        $data['账款类别']=$record['账款类别'];
        $data['开户银行']='';
        $data['银行账号']='';
        $data['整单折扣']=1; 
        $data['折扣金额']=0;
        $data['结算方式']=$record['结算方式'];
        $data['结算账户']=$record['结算账户'];
        $data['支票号']=0;
        $data['凭证号']=0;
        $data['摘要']=$record['备注'];
        $data['收支项目']=$record['收支类型'];
        $data['当前状态']='待审核';
        $data['发票类别']=$record['门店'];
        $data['发票号']='';
        $data['取用预付']=0;
        $data['取用预收']=0;
        $data['取用预存']=0;
        $data['本次冲账']=$record['金额'];
        M('日常收支','dbo.','difo')->add($data);
        M('应收应付单','dbo.','difo')->execute("UPDATE 往来单位 SET 应付款=应付款-$je,往来余款=往来余款+$je WHERE ID='$dwid'");

        echo '操作成功';
    
    }
    public  function savestock()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $stockinfo=$_POST['stockinfo'];
            if($type&&$type=='add'){
               $stockinfo['号码']=$this->genproductnum($stockinfo['类别']);
               M('配件目录','dbo.','difo')->add($stockinfo);
               $cklist=M('仓库目录','dbo.','difo')->select();
               foreach($cklist as $ck){
                   M('配件仓位','dbo.','difo')->add(array('仓库'=>$ck['名称'],'编号'=>$stockinfo['编号']));
               }

            }else{
                M('配件目录','dbo.','difo')->where(array('编号'=>$stockinfo['编号']))->save($stockinfo);
            }
            echo '保存成功';
        }
    }
    public  function savedbinfo()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $dbinfo=$_POST['dbinfo'];
            if($type&&$type=='add'){
                $dbinfo['ID']=$this->getcode(18,0,1);;
                $dbinfo['业务编号']=$this->getcodenum('QT');
                $dbinfo['制单人']=cookie('username');
                $dbinfo['制单日期']=date('Y-m-d',time());
                $dbinfo['当前状态']='审核';
                unset($dbinfo['流水号']);
                M('车辆代办','dbo.','difo')->add($dbinfo);
                $carinfo['年检日期']=$dbinfo['截至日期'];
                M('车辆档案')->where(array('车牌号码'=>$dbinfo['车牌号码']))->save($carinfo);

            }else{
                $code=$dbinfo['流水号'];
                unset($dbinfo['流水号']);
                $carinfo['年检日期']=$dbinfo['年检日期'];
                M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$dbinfo['车牌号码']))->save($carinfo);
                M('车辆代办','dbo.','difo')->where(array('流水号'=>$code))->save($dbinfo);
            }
            echo '保存成功';
        }
    }
    public  function savebxinfo()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $bxinfo=$_POST['bxinfo'];
            if($type&&$type=='add'){
                unset($bxinfo['流水号']);
                $bxinfo['ID']=$this->getcode(18,0,1);;
                $bxinfo['业务编号']=$this->getcodenum('BX');
                $bxinfo['制单人']=cookie('username');
                $bxinfo['费用折扣']=1;
                $bxinfo['制单日期']=date('Y-m-d',time());
                $bxinfo['当前状态']='审核';
                M('车辆保险','dbo.','difo')->add($bxinfo);
                $carinfo['交保到期']=$bxinfo['交强截至'];
                $carinfo['商保到期']=$bxinfo['商业截至'];
                M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$bxinfo['车牌号码']))->save($carinfo);
            }else{
                $code=$bxinfo['流水号'];
                unset($bxinfo['流水号']);
                $carinfo['交保到期']=$bxinfo['交强截至'];
                $carinfo['商保到期']=$bxinfo['商业截至'];
                M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$bxinfo['车牌号码']))->save($carinfo);
                M('车辆保险','dbo.','difo')->where(array('流水号'=>$code))->save($bxinfo);

            }
            echo '保存成功';
        }
    }
    public  function savetipreply()
    {
        if(IS_POST){
            $tracedata=$_POST['tracedata'];
            $tipdata=$_POST['tipdata'];
            $tracedata['跟踪时间']=date('Y-m-d H:i',time());
            $tracedata['类别']=$tipdata['类别'];
            $tracedata['年份']=$tipdata['年份'];
            $tracedata['跟踪类型']='现场交流';
            $tracedata['登记人']=cookie('username');
            M('客户跟踪','dbo.','difo')->add($tracedata);
            M('客户跟踪','dbo.','difo')->where(array('流水号'=>$tipdata['流水号']))->save(
                array('跟踪人'=>$tracedata['跟踪人'],
                '是否反馈'=>'是',
                '反馈时间'=>date('Y-m-d H:i',time()),
                '是否成交'=>$tracedata['是否成交'],
                '反馈内容'=>$tracedata['内容']
                ));
            echo '保存成功';
        }
    }
    public  function savetrace()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $tracedata=$_POST['tracedata'];
            if($type&&$type=='add'){
                unset($tracedata['年审到期']);
                $tracedata['跟踪时间']=date('Y-m-d H:i',time());
                $tracedata['类别']='年审';
                $tracedata['登记人']=cookie('username');
                M('客户跟踪','dbo.','difo')->add($tracedata);
            }else{
                unset($tracedata['年审到期']);
                $code=$tracedata['流水号'];
                unset($tracedata['流水号']);
                M('客户跟踪','dbo.','difo')->where(array('流水号'=>$code))->save($tracedata);
            }
            echo '保存成功';
        }
    }
    public  function savewxtrace()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $tracedata=$_POST['tracedata'];
            if($type&&$type=='add'){
                $tracedata['跟踪时间']=date('Y-m-d H:i',time());
                $tracedata['类别']='维修';
                $tracedata['登记人']=cookie('username');
                M('客户跟踪','dbo.','difo')->add($tracedata);
            }else{
                $code=$tracedata['流水号'];
                unset($tracedata['流水号']);
                M('客户跟踪','dbo.','difo')->where(array('流水号'=>$code))->save($tracedata);
            }
            echo '保存成功';
        }
    }
   public  function saveinsurancetrace()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $tracedata=$_POST['tracedata'];
            if($type&&$type=='add'){
                unset($tracedata['保险到期']);
                $tracedata['跟踪时间']=date('Y-m-d H:i',time());
                $tracedata['类别']='保险';
                $tracedata['登记人']=cookie('username');
                M('客户跟踪','dbo.','difo')->add($tracedata);
            }else{
                unset($tracedata['保险到期']);
                $code=$tracedata['流水号'];
                unset($tracedata['流水号']);
                M('客户跟踪','dbo.','difo')->where(array('流水号'=>$code))->save($tracedata);
            }
            echo '保存成功';
        }
    }
   public  function deleteinsurancetrace()
    {
        if(IS_POST){
            $code=$_POST['code'];
            M('客户跟踪','dbo.','difo')->where(array('流水号'=>$code))->delete();
            echo '操作成功';
        }
    }
  public  function saveoilchangetrace()
    {
        if(IS_POST){
            $type=$_POST['type'];
            $tracedata=$_POST['tracedata'];
            if($type&&$type=='add'){
                unset($tracedata['下次保养']);
                $tracedata['跟踪时间']=date('Y-m-d H:i',time());
                $tracedata['登记人']=cookie('username');
                $tracedata['类别']='保养';
                M('客户跟踪','dbo.','difo')->add($tracedata);
            }else{
                M('客户跟踪','dbo.','difo')->where(array('流水号'=>$tracedata['流水号']))->save($tracedata);
            }
            echo '保存成功';
        }
    }
   public function gettraceinfo(){
       $traceinfo=M('客户跟踪','dbo.','difo')->where(array('车牌号码'=>$_POST['code'],'类别'=>$_POST['type']))->order('跟踪时间 desc')->select();
       $data['Rows']=$traceinfo;
       $data['Total']=count($traceinfo);
       echo json_encode($data);
    }
   public function saverole(){
       if(IS_POST){
           $data=$_POST['row'];
           $type=$_POST['type'];
           if($type=='add'){
               unset($data['RoleID']);
               M('Sys_role','dbo.','difo')->add($data);
               echo '添加成功';
           }elseif($type=='del'){
               $code=$data['RoleID'];
               M('Sys_role','dbo.','difo')->where(array('RoleID'=>$code))->delete();
               echo '删除成功';
           }
           else{
               $code=$data['RoleID'];
               unset($data['RoleID']);
               M('Sys_role','dbo.','difo')->where(array('RoleID'=>$code))->save($data);
                echo '修改成功';
             
           }
       }
   }
   public function saveuser(){
       if(IS_POST){
           $data=$_POST['row'];
           $type=$_POST['type'];
           if($type=='add'){
               unset($data['流水号']);
               M('用户管理','dbo.','difo')->add($data);
               echo '添加成功';
           }elseif($type=='del'){
               $code=$data['流水号'];
               M('用户管理','dbo.','difo')->where(array('流水号'=>$code))->delete();
               echo '删除成功';
           }
           else{
               $code=$data['流水号'];
               unset($data['流水号']);
               M('用户管理','dbo.','difo')->where(array('流水号'=>$code))->save($data);
                echo '修改成功';
             
           }
       }
   }
   public  function getprojectbyname(){
        $page=$_POST['page']; 
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){
            $searchwhere['类别']=array('like',$searchkey);
            $searchwhere['项目编号']=array('like',$searchkey);
            $searchwhere['项目名称']=array('like',$searchkey);
            $searchwhere['助记码']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $userinfo=M('项目目录','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
        $count=M('项目目录','dbo.','difo')->where($where)->count();
        $data['Rows']=$userinfo;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public  function getproductbyname(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        $where['停用']=0;
        $shop=$_GET['shop'];
        $cangku='塘坑门店仓库';
        if($shop=='区府店')
            $cangku='区府门店仓库';
        elseif($shop=='主仓库')
            $cangku='主仓库';
        elseif($shop=='爱养车')
            $cangku='主仓库';
        elseif($shop=='时代长岛店')
            $cangku='时代长岛店仓库';
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){
            $searchwhere['配件目录.品牌']=array('like',$searchkey);
            $searchwhere['配件目录.名称']=array('like',$searchkey);
            $searchwhere['配件目录.类别']=array('like',$searchkey);
            $searchwhere['配件目录.编号']=array('like',$searchkey);
            $searchwhere['配件目录.原厂编号']=array('like',$searchkey);
            $searchwhere['助记码']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }

        $product=M('配件目录','dbo.','difo')
            ->join('left join 配件仓位 on 配件目录.编号=配件仓位.编号  and  配件仓位.仓库=\''.$cangku.'\'')
            ->field('配件目录.*,配件仓位.仓库,配件仓位.库存 分库存,配件仓位.参考售价 售价,配件仓位.参考进价 进价,配件仓位.成本价 成本')
            ->order("$sortname  $sortorder")
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
        $count=M('配件目录','dbo.','difo')
            ->join('left join 配件仓位 on 配件目录.编号=配件仓位.编号  and  配件仓位.仓库=\''.$cangku.'\'')
            ->where($where)->count();
        $data['Rows']=$product;
        $data['Total']=$count;
        echo json_encode($data);
    
    } 
    public  function getproductlist(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='编号';
            $sortorder='asc';
        }
        $where['1']=1;
        if (isset($_POST['type'])&&trim($_POST['type'])!=''){
            $where['类别']=array('like',$_POST['type'].'%');
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){
            $searchwhere['品牌']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['类别']=array('like',$searchkey);
            $searchwhere['配件目录.编号']=array('like',$searchkey);
            $searchwhere['原厂编号']=array('like',$searchkey);
            $searchwhere['助记码']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $product=M('配件目录','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)
            ->order("$sortname $sortorder")
            ->select();
        $count=M('配件目录','dbo.','difo')
            ->where($where)->count();
        $data['Rows']=$product;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public  function getmrworker(){
        $list=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%美容%')))->select();
        echo json_encode($list);
    }

    public  function getzhuxiu()
    {
        $shop=$_POST['shop'];
        $wxlx=$_POST['wxlx'];
        $zhiwu='维修技工';
        if($wxlx&&in_array($wxlx,array('蜡水洗车','汽车美容'))){
            $zhiwu='美容技工';
        }
        if($shop){
            $zhuxiu=M('员工目录','dbo.','difo')->where(array('部门'=>$shop,'职务'=>$zhiwu))->select();
        }else{
            $zhuxiu=M('员工目录','dbo.','difo')->where(array('职务'=>$zhiwu))->select();       
        }        
        echo json_encode($zhuxiu);
    }
    public  function getpersonbydepart()
    {
        $shop=$_POST['bumen'];
        if($shop){
            $zhuxiu=M('员工目录','dbo.','difo')->where(array('部门'=>$shop,'技术员'=>'1'))->select();
        }else{
            $zhuxiu=M('员工目录','dbo.','difo')->select(array('技术员'=>'1'));       
        }        
        echo json_encode($zhuxiu);
    }
    public  function getcarinfo(){
        
        $carno=$_GET['carno'];
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();

        echo json_encode($carinfo);
        
    }
    public  function getpinpai(){
         
        $pinpai=M('常见品牌','dbo.','difo')->where(array('品牌'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public  function getinsurancetrace(){
         
        $pinpai=M('客户跟踪','dbo.','difo')->where(array('流水号'=>$_POST['code']))->find();
        echo json_encode($pinpai);
    
   }
   public  function getfwgw(){
         
        $zxlist=M('员工目录','dbo.','difo')->where(array('接车员'=>'1','姓名'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($zxlist);
    
   }
    public  function getgw(){
        
        $zxlist=M('员工目录','dbo.','difo')->where(array('职务'=>'服务顾问','姓名'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($zxlist);
        
    }
    public  function getlly(){
        
        $zxlist=M('员工目录','dbo.','difo')->where(array('领料员'=>'1','姓名'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($zxlist);
        
    }
    public  function getzdr(){
        
        $zxlist=M('用户管理','dbo.','difo')->where(array('姓名'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($zxlist);
        
    }
    public  function getywy(){
        
        $zxlist=M('员工目录','dbo.','difo')->where(array('业务员'=>'1','姓名'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($zxlist);
        
    }
    public  function getchexing(){
         
        $pinpai=M('常见车型','dbo.','difo')->where(array('车型'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public  function baoxian(){
         
        $pinpai=M('往来单位','dbo.','difo')->where(array('保险公司'=>'1','名称'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public  function daiban(){
        
        $pinpai=M('往来单位','dbo.','difo')->where(array('车管单位'=>'1','名称'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
        
    }

    public  function getwxlb(){
        
        if($_GET['flag']=='1'){
         $wxlb=M('维修类别','dbo.','difo')
             ->where(array('类别'=>array('like','%'.$_POST['key'].'%'),'类别'=>array('not in',array('蜡水洗车','汽车美容'))))
             ->select();
       }
        else
        $wxlb=M('维修类别','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
    public  function getgzlb(){
        
        $wxlb=M('回访方式','dbo.','difo')->where(array('方式'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
        
    } 
    public  function getfplb(){
         
        $wxlb=M('发票类别','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
     public  function getzones(){
         
         $wxlb=M('区域列表','dbo.','difo')->where(array('区域'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
   public  function getjsfs(){
         
        $wxlb=M('结算方式','dbo.','difo')->where(array('名称'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
   public  function getdepartments(){
       $data=M('部门列表','dbo.','difo')->where(array('部门'=>array('like','%'.$_POST['key'].'%')))->select();
       echo json_encode($data);
   }
   public  function getdgrouplist(){
       $data=M('班组目录','dbo.','difo')->where(array('名称'=>array('like','%'.$_POST['key'].'%')))->select();
       echo json_encode($data);
   }
   public  function getjszh(){
         
        $wxlb=M('收支账户','dbo.','difo')->where(array('名称'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
   public  function getszxm(){
         
        $wxlb=M('收支项目','dbo.','difo')->where(array('名称'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
   public  function getkhgrade(){
        $wxlb=M('客户等级','dbo.','difo')->select();
        echo json_encode($wxlb);
    
} 
    public  function getallshoplist(){
        $wxlb=M('门店目录','dbo.','difo')->select();
        echo json_encode($wxlb);
    
}
    public  function getshoplist(){
        $where['名称']=array('in',explode(',',cookie('department')));
        $wxlb=M('门店目录','dbo.','difo')->where($where)->select();
        echo json_encode($wxlb);
    
}
     public  function getrolelist(){
        $wxlb=M('sys_role','dbo.','difo')->select();
        echo json_encode($wxlb);
    
} 
   public  function getstorelist(){
        $where['门店']=array('in',explode(',',cookie('department')));
        $wxlb=M('仓库目录','dbo.','difo')->where($where)->select();
        echo json_encode($wxlb);
    
} 
    public  function getkhtype(){
        $wxlb=M('客商分类','dbo.','difo')->select();
        echo json_encode($wxlb);
    
} 
    public  function getrecordbyid(){
        $id=$_GET['id'];
        $result=M('维修','dbo.','difo')->where(array('id'=>$id))->find();
        echo json_encode($result);
    
} 
    public  function gethyfs(){
         
        $wxlb=M('货运方式','dbo.','difo')->where(array('方式'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
    public  function getcouponlist(){
         
        $pinpai=M('member_card_coupon')->where(array('title'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public  function getkhlb(){
        
        $pinpai=M('客商分类','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
        
    }
    public  function getcllb(){
        $pinpai=M('车辆类别','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    public  function getsaletype(){
        $pinpai=M('syscode','dbo.','difo')->where(array('parentid'=>10))->select();
        echo json_encode($pinpai);
    }
    public  function getdblb(){
        $pinpai=M('syscode','dbo.','difo')->where(array('parentid'=>9))->select();
        echo json_encode($pinpai);
    }
    public  function getdiscount(){
        $discount=M('会员详细信息','dbo.','difo')->where(array('ID'=>$_GET['id']))->find();
        echo json_encode($discount);
    }
    public  function getproject(){
        $id=$_GET['ID'];
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        $count=M('维修项目','dbo.','difo')->where(array('ID'=>$id))->count();
        $carinfo=M('维修项目','dbo.','difo')->where(array('ID'=>$id))
            ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select(); 
       $data['Rows']=$carinfo;
       $data['Total']=$count;
       echo json_encode($data);
    }
    public  function  getproduct(){
        $id=$_GET['id'];
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        $count=M('维修配件','dbo.','difo')->where(array('ID'=>$id))->count();
        $carinfo=M('维修配件','dbo.','difo')->where(array('ID'=>$id))
            ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$carinfo;
        $data['Total']=$count;
        echo json_encode($data);
    }
    public  function getpick()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if(isset($_POST['zt'])){
            if($_POST['zt']!='all'){
                $where['当前状态']=$_POST['zt'];
            }
        }else
        {
            $where['当前状态']='待审核';
        }
        if(isset($_GET['type'])&&$_GET['type']=='1'){
            $where['单据类别']='入库';

        }else{
            $where['单据类别']='出库';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['领料员']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['原因']=array('like',$searchkey);
            $searchwhere['引用单号']=array('like',$searchkey);
            $searchwhere['引用类别']=array('like',$searchkey);
            $searchwhere['单据备注']=array('like',$searchkey);
            $searchwhere['单据类别']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['领料员']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('出入库单','dbo.','difo')->where($where)->count();
        $yelist=M('出入库单','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getselfpick()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='名称';
            $sortorder='desc';
        }
        $where['单据类别']='出库';
        $where['当前状态']=array('neq','取消');
        $where['引用类别']='自用出库';
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['出入库单.门店']=$_POST['shop'];
            
        }else{
            $where['出入库单.门店']=array('in',explode(',',cookie('department')));

        }
        if($_POST['lly']&&trim($_POST['lly'])!='')
        {
            $where['领料员']=$_POST['lly'];
            
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['领料员']=array('like',$searchkey); 
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['原因']=array('like',$searchkey);
            $searchwhere['引用单号']=array('like',$searchkey);
            $searchwhere['引用类别']=array('like',$searchkey);
            $searchwhere['单据备注']=array('like',$searchkey);
            $searchwhere['单据类别']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID')->where($where)->count();
        $yelist=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->field(
            '出入库单.*,出入库明细.单价,出入库明细.名称,出入库明细.数量,出入库明细.金额'
            )->order("$sortname  $sortorder")->select();
        $statdata=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID')
            ->where($where)->field('sum(出入库明细.数量) 数量,sum(出入库明细.金额) 金额'
            )->find();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $data['statdata']=$statdata;
        echo json_encode($data);
        
    }

    public  function getpurchasequery()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        $where['当前状态']='已审核';
        if(isset($_POST['shop'])&&$_POST['shop']!='all'){
            $where['门店']=$_POST['shop'];

        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if(isset($_POST['fwgw'])){
            $where['制单人']=$_POST['fwgw'];

        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['enddate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['enddate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['发票号码']=array('like',$searchkey);
            $searchwhere['发票类别']=array('like',$searchkey);
            $searchwhere['供应商']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('采购单','dbo.','difo')->where($where)->count();
        $yelist=M('采购单','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getpurchase()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if(isset($_POST['zt'])){
            if($_POST['zt']!='all'){
                $where['当前状态']=$_POST['zt'];
            }
        }else
        {
          $where['当前状态']='待审核';
        }
        if(isset($_POST['shop'])&&$_POST['shop']!='all'){
            $where['门店']=$_POST['shop'];

        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if(isset($_POST['lb'])&&$_POST['lb']!='all'){
            $where['单据类别']=$_POST['lb'];

        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['发票号码']=array('like',$searchkey);
            $searchwhere['发票类别']=array('like',$searchkey);
            $searchwhere['供应商']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('采购单','dbo.','difo')->where($where)->count();
        $yelist=M('采购单','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getpurchasedetail()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        $where['ID']=$_POST['id'];
        $count=M('采购明细','dbo.','difo')->where($where)->count();
        $yelist=M('采购明细','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getsalebill()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if(isset($_POST['zt'])){
            if($_POST['zt']!='all'){
                $where['当前状态']=$_POST['zt'];
            }
        }else
        {
            $where['当前状态']='待审核';
        }
        if(isset($_POST['shop'])&&$_POST['shop']!='all'){
            $where['门店']=$_POST['shop'];

        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if(isset($_POST['xslb'])&&$_POST['xslb']!=''){
            $where['销售类别']=$_POST['xslb'];
        }
        if(isset($_POST['zdr'])&&$_POST['zdr']!=''){
            $where['业务员']=$_POST['zdr'];
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['客户名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('销售单','dbo.','difo')->where($where)->count();
        $yelist=M('销售单','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getsaleautobill()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if(isset($_POST['zt'])){
            if($_POST['zt']!='all'){
                $where['当前状态']=$_POST['zt'];
            }
        }else
        {
            $where['当前状态']='待审核';
        }
        if(isset($_POST['shop'])&&$_POST['shop']!='all'){
            $where['门店']=$_POST['shop'];

        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if(isset($_POST['xslb'])&&$_POST['xslb']!=''){
            $where['销售类别']=$_POST['xslb'];
        }
        if(isset($_POST['zdr'])&&$_POST['zdr']!=''){
            $where['业务员']=$_POST['zdr'];
        }
        $where['制单人']='系统自动';
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['客户名称']=array('like',$searchkey);
            $searchwhere['单据编号']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $count=M('销售单','dbo.','difo')->join('left join 销售单跟踪 on 销售单.ID=销售单跟踪.销售单ID')->where($where)->count();
        $yelist=M('销售单','dbo.','difo')->join('left join 销售单跟踪 on 销售单.ID=销售单跟踪.销售单ID')->where($where)
            ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")
            ->field('销售单.*,销售单跟踪.开始时间,销售单跟踪.送达时间,销售单跟踪.送货人')
            ->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getsaledata()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if(isset($_POST['shop'])&&$_POST['shop']!='all'){
            $where['门店']=$_POST['shop'];

        }else{
            $where['门店']=array('in',explode(',',cookie('department')));

        }
        if(isset($_POST['xslb'])&&$_POST['xslb']!=''){
            $where['销售类别']=$_POST['xslb'];
        }
       if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['endDate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($_POST['zdr']&&trim($_POST['zdr'])!='')
        {
            $where['业务员']=trim($_POST['zdr']);
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['客户名称']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['_string']="名称 not like '%充值%' and 当前状态='已审核' and 单据类别='销售出库'";
        $count=M('销售单','dbo.','difo')
            ->join('left join 销售明细 on 销售单.ID=销售明细.ID')
            ->where($where)->count();
        $yelist=M('销售单','dbo.','difo')
            ->join('left join 销售明细 on 销售单.ID=销售明细.ID')
            ->field('销售明细.*,销售单.制单人,销售单.销售类别,销售单.业务员,销售单.客户名称,销售单.制单日期,销售单.单据类别,销售单.当前状态,销售单.单据编号,销售单.收款日期,销售单.结算方式')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)
            ->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $TotalData=M('销售单','dbo.','difo')
             ->join('left join 销售明细 on 销售单.ID=销售明细.ID')
            ->where($where)
            ->field('sum(数量*折扣*单价) 总金额')->find();
        $data['TotalData']=$TotalData;
        echo json_encode($data);
        
    }
    public  function getbxdata()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['endDate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($_POST['ywy']&&trim($_POST['ywy'])!='')
        {
            $where['业务员']=trim($_POST['ywy']);
            
        }
        if($_POST['bxgs']&&trim($_POST['bxgs'])!='')
        {
            $where['保险公司']=trim($_POST['bxgs']);
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['保险公司']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['_string']=" 当前状态!='取消'";
        $count=M('车辆保险','dbo.','difo')
            ->where($where)->count();
        $yelist=M('车辆保险','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)
            ->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $TotalData=M('车辆保险','dbo.','difo')
            ->where($where)
            ->field('sum(总金额) 总金额,sum(手续费) 手续费,sum(商业保费) 商业保费,sum(交强保费) 交强保费')->find();
        $data['TotalData']=$TotalData;
        echo json_encode($data);
        
    }
    public  function getdbdata()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        if($_POST['startdate']&&trim($_POST['startdate'])!='')
        {
            $where['制单日期']=array('egt',trim($_POST['startdate']));
            
        }
        if($_POST['endDate']&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('elt',trim($_POST['endDate']));
            
        }
        if(trim($_POST['startdate'])!=''&&trim($_POST['enddate'])!='')
        {
            $where['制单日期']=array('BETWEEN',array(trim($_POST['startdate']),trim($_POST['enddate'])));
            
        }
        if($_POST['cgdw']&&trim($_POST['cgdw'])!='')
        {
            $where['车管单位']=trim($_POST['cgdw']);
            
        }
        if($_POST['dblb']&&trim($_POST['dblb'])!='')
        {
            $where['代办类别']=trim($_POST['dblb']);
            
        }
       if($_POST['ywy']&&trim($_POST['ywy'])!='')
        {
            $where['业务员']=trim($_POST['ywy']);
            
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['业务员']=array('like',$searchkey);
            $searchwhere['代办类别']=array('like',$searchkey);
            $searchwhere['车管单位']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;
            
        }
        $where['_string']="当前状态!='取消' ";
        $count=M('车辆代办','dbo.','difo')
            ->where($where)->count();
        $yelist=M('车辆代办','dbo.','difo')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)
            ->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        $TotalData=M('车辆代办','dbo.','difo')
            ->where($where)
            ->field('sum(总金额) 总金额,sum(手续费) 手续费,sum(代办费用) 代办费')->find();
        $data['TotalData']=$TotalData;
        echo json_encode($data);
        
    }
   public  function getsaledetail()
    {   
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $sortname=$_POST['sortname'];
        $sortorder=$_POST['sortorder'];
        if(!isset($sortname)){
            $sortname='流水号';
            $sortorder='desc';
        }
        $where['ID']=$_POST['id'];
        $count=M('销售明细','dbo.','difo')->where($where)->count();
        $yelist=M('销售明细','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public  function getmrproject(){
        $meirong=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目编号'=>array('notlike','%AYC00%'),'项目名称'=>array('like','%'.$_POST['key'].'%')))->order('项目名称')->select();
        echo json_encode($meirong);
    }
    public  function getcarnobypersonid(){
        $id=$_POST['id'];
        $carinfo=M('车辆档案','dbo.','difo')->where(array('客户ID'=>$id))->select();
        echo json_encode($carinfo);
    }
    public  function getkxproject(){
        $meirong=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目编号'=>array('notlike','%AYC00%'),'项目名称'=>array('like','%'.$_POST['key'].'%')))->order('项目名称')->select();
        echo json_encode($meirong);
    }
    public  function getcarno(){
        
        $wecha_id = isset($_GET['wecha_id']) ? htmlspecialchars($_GET['wecha_id']) : '';
        $carinfo=M('member_card_car')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->select();
        echo json_encode($carinfo);
        exit();
    }
    public  function getgrade(){
        
        if(IS_POST){
            $carno=$_POST['carno'];
            $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
            $count=M('维修','dbo.','difo')->where(array('车牌号码'=>$carno))->count();
            $carinfo['进厂次数']=$count;
            echo json_encode($carinfo);
            exit;
        }
    }
    public  function haswxrecord()
    {    
        $wxinfo=$_POST['wxinfo'];
        $wxrecord=M('维修','dbo.','difo')->where(array('车牌号码'=>$wxinfo['车牌号码'],'维修类别'=>$wxinfo['维修类别'],'_string'=>"当前状态 not in ('结束','取消')"))->find();
        if(isset($wxrecord)){
            
            echo 1;
            exit;
        }
        echo 0;
        exit;
    }
    public  function getviewnotice()
    {
        $id=$_GET['id'];
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $searchkey=$_POST['searchkey'];
        $searchkey='%'.trim($searchkey).'%';
        if($searchkey){       
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey); 
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $flag=$_GET['flag'];
        if(isset($flag)&&$flag=='1'){
            $where['_string']="wecha_id not in (SELECT wecha_id from tp_member_card_noticedetail  where noticeid=$id) and carno<>''";
            $userinfo=M('userinfo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
            $count=M('userinfo')->where($where)->count();

        }
        else{
            $where['_string']="tp_member_card_noticedetail.noticeid=$id";
            $userinfo=M('userinfo')->join('tp_member_card_noticedetail on tp_userinfo.wecha_id=tp_member_card_noticedetail.wecha_id')->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
            $count=M('userinfo')->join('tp_member_card_noticedetail on tp_userinfo.wecha_id=tp_member_card_noticedetail.wecha_id')->where($where)->count();
        }
        $data['Rows']=$userinfo;
        $data['Total']=$count;
        echo json_encode($data);

        
    }
    public  function getnotices(){
        
        $notices= M('member_card_notice')->query('SELECT *,(SELECT count(1) from tp_userinfo where   carno<>\'\') total from tp_member_card_notice a left join (
SELECT noticeid,count(1) num from tp_member_card_noticedetail GROUP BY noticeid
) b on a.id=b.noticeid order by time desc');
        $data['Rows']=$notices;
        $data['Total']=count($notices);
        echo json_encode($data);

    }
    public  function waiguan(){
        $pinpai=M('车辆外观','dbo.','difo')->where(array('外观'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    
    public  function yanse(){
        $pinpai=M('车辆颜色','dbo.','difo')->where(array('颜色'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    #region 维修跟踪附件
    public  function deletetracefile(){
        
        $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        if($user['权限']=='超级用户'||$user['权限']=='录单采购'){
            $key=$_POST['key'];
            $file='./uploads/rlydsv1453614397/tracefiles/'.$key;
            if(unlink($file)){
                echo 1;
                exit;
            } 
            echo 0;
            exit;
        }
    }
    function uploadtrcefile($filetypes=''){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize  = intval(C('up_size'))*1024 ;
        if (!$filetypes){
            $upload->allowExts  = explode(',',C('up_exts'));
        }else {
            $upload->allowExts  = $filetypes;
        }
        $wxno=$_GET['carno'];
        $upload->autoSub=0;
        $upload->saveRule=$this->genfilename();
        $upload->thumbRemoveOrigin=true;
        
        $upload->savePath =  './uploads/'.$this->token.'/tracefiles/'.$wxno.'/';// 设置附件上传目录
        //
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads')||!is_dir($_SERVER['DOCUMENT_ROOT'].'/uploads')){
            mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads',0777);
        }
        $firstLetterDir=$_SERVER['DOCUMENT_ROOT'].'/uploads/'.$this->token;
        if (!file_exists($firstLetterDir)||!is_dir($firstLetterDir)){
            mkdir($firstLetterDir,0777);
        }
        if (!file_exists($firstLetterDir.'/tracefiles')||!is_dir($firstLetterDir.'/tracefiles')){
            mkdir($firstLetterDir.'/wxfiles',0777);
        }
        if (!file_exists($firstLetterDir.'/tracefiles/'.$wxno)||!is_dir($firstLetterDir.'/tracefiles/'.$wxno)){
            mkdir($firstLetterDir.'/tracefiles/'.$wxno,0777);
        }
        //
        
        $upload->hashLevel=4;
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg=$upload->getErrorMsg();
            echo $msg;
            exit;

        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            $this->siteUrl=$this->siteUrl?$this->siteUrl:C('site_url');
            $msg=$this->siteUrl.substr($upload->savePath,1).$info[0]['savename'];
            echo json_encode($info);
            exit;

        }
        
    }
    public  function appendupload(){
        $wxno=$_GET['carno'];
        $path='./uploads/'.$this->token.'/tracefiles/'.$wxno.'/';
        $files=scandir($path);
        $attrs=array();
        foreach($files as $key=>$value){
            if(!in_array($files[$key],array('.','..'))){
                $files[$key]='http://www.nsayc.com/uploads/rlydsv1453614397/tracefiles/'.$wxno.'/'.$value;
                $attr['key']=$wxno.'/'.$value;
                $attr['showDelete']=true;
                $attr['showZoom']=true;
                $attr['width']='120px';
                array_push($attrs,$attr);
                
            }
            else
                unset($files[$key]);
        }
        if($files){
            $this->assign('files',json_encode(array_values($files)));
        }else{
            $this->assign('files','[]');
        }
        $this->assign('attrs',json_encode(array_values($attrs)));
        $this->assign('carno',$wxno);
        $this->display();
    }
    #endregion
    #region 维修附件管理
    public  function deletewxfile(){
       
       $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
       if($user['权限']=='超级用户'){
           $key=$_POST['key'];
           $file='./uploads/rlydsv1453614397/wxfiles/'.$key;
           if(unlink($file)){
               echo 1;
               exit;
           } 
           echo 0;
           exit;
       }
   }
    function uploadwxfile($filetypes=''){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize  = intval(C('up_size'))*1024 ;
        if (!$filetypes){
            $upload->allowExts  = explode(',',C('up_exts'));
        }else {
            $upload->allowExts  = $filetypes;
        }
        $wxno=$_GET['wxno'];
        $upload->autoSub=0;
        $upload->saveRule=$this->genfilename();
        $upload->thumbRemoveOrigin=true;
        
        $upload->savePath =  './uploads/'.$this->token.'/wxfiles/'.$wxno.'/';// 设置附件上传目录
        //
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads')||!is_dir($_SERVER['DOCUMENT_ROOT'].'/uploads')){
            mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads',0777);
        }
        $firstLetterDir=$_SERVER['DOCUMENT_ROOT'].'/uploads/'.$this->token;
        if (!file_exists($firstLetterDir)||!is_dir($firstLetterDir)){
            mkdir($firstLetterDir,0777);
        }
        if (!file_exists($firstLetterDir.'/wxfiles')||!is_dir($firstLetterDir.'/wxfiles')){
            mkdir($firstLetterDir.'/wxfiles',0777);
        }
        if (!file_exists($firstLetterDir.'/wxfiles/'.$wxno)||!is_dir($firstLetterDir.'/wxfiles/'.$wxno)){
            mkdir($firstLetterDir.'/wxfiles/'.$wxno,0777);
        }
        //
        
        $upload->hashLevel=4;
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg=$upload->getErrorMsg();
            echo $msg;
            exit;

        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            $this->siteUrl=$this->siteUrl?$this->siteUrl:C('site_url');
            $msg=$this->siteUrl.substr($upload->savePath,1).$info[0]['savename'];
            echo json_encode($info);
            exit;

        }
        
    }
    public  function wxfileupload(){
        $wxno=$_GET['wxno'];
        $path='./uploads/'.$this->token.'/wxfiles/'.$wxno.'/';
        $files=scandir($path);
        $attrs=array();
        foreach($files as $key=>$value){
            if(!in_array($files[$key],array('.','..'))){
                $files[$key]='http://www.nsayc.com/uploads/rlydsv1453614397/wxfiles/'.$wxno.'/'.$value;
                $attr['key']=$wxno.'/'.$value;
                $attr['showDelete']=true;
                $attr['showZoom']=true;
                $attr['width']='120px';
                array_push($attrs,$attr);
                
            }
            else
                unset($files[$key]);
        }
        if($files){
            $this->assign('files',json_encode(array_values($files)));
        }else{
            $this->assign('files','[]');
        }
        $this->assign('attrs',json_encode(array_values($attrs)));
        $this->assign('wxno',$wxno);
        $this->display();
    }
    #endregion
    #region 配件产品附件
    public  function deleteproductfile (){
       
       $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
       if($user['权限']=='超级用户'){
           $key=$_POST['key'];
           $file='./uploads/rlydsv1453614397/products/'.$key;
           if(unlink($file)){
               echo 1;
               exit;
           } 
           echo 0;
           exit;
       }
   }
    public  function productupload(){
        $code=$_GET['code'];
        $path='./uploads/'.$this->token.'/products/'.$code.'/';
        $files=scandir($path);
        $attrs=array();
        foreach($files as $key=>$value){
            if(!in_array($files[$key],array('.','..'))){
                $files[$key]='http://www.nsayc.com/uploads/rlydsv1453614397/products/'.$code.'/'.$value;
                $attr['key']=$code.'/'.$value;
                $attr['showDelete']=true;
                $attr['showZoom']=true;
                $attr['width']='120px';
                array_push($attrs,$attr);
                
            }
            else
                unset($files[$key]);
        }
        if($files){
            $this->assign('files',json_encode(array_values($files)));
        }else{
            $this->assign('files','[]');
        }
        $this->assign('attrs',json_encode(array_values($attrs)));
        $this->assign('code',$code);
        $this->display();
    }
    private function genfilename(){
        return date('YmdHis',time()).mt_rand(1000,9999);
    }
    function uploadproduct($filetypes=''){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize  = intval(C('up_size'))*1024 ;
        if (!$filetypes){
            $upload->allowExts  = explode(',',C('up_exts'));
        }else {
            $upload->allowExts  = $filetypes;
        }
        $code=$_GET['code'];
        $upload->autoSub=0;
        $upload->saveRule=$this->genfilename();
        $upload->thumbRemoveOrigin=true;
        
        $upload->savePath =  './uploads/'.$this->token.'/products/'.$code.'/';// 设置附件上传目录
        //
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads')||!is_dir($_SERVER['DOCUMENT_ROOT'].'/uploads')){
            mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads',0777);
        }
        $firstLetterDir=$_SERVER['DOCUMENT_ROOT'].'/uploads/'.$this->token;
        if (!file_exists($firstLetterDir)||!is_dir($firstLetterDir)){
            mkdir($firstLetterDir,0777);
        }
        if (!file_exists($firstLetterDir.'/products')||!is_dir($firstLetterDir.'/products')){
            mkdir($firstLetterDir.'/products',0777);
        }
        if (!file_exists($firstLetterDir.'/products/'.$code)||!is_dir($firstLetterDir.'/products/'.$code)){
            mkdir($firstLetterDir.'/products/'.$code,0777);
        }
        //
        
        $upload->hashLevel=4;
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg=$upload->getErrorMsg();
            echo $msg;
            exit;

        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            $this->siteUrl=$this->siteUrl?$this->siteUrl:C('site_url');
            $msg=$this->siteUrl.substr($upload->savePath,1).$info[0]['savename'];
            echo json_encode($info);
            exit;

        }
        
    }
    #endregion
    #region 车辆资料附件
    public  function deletefile(){
        
        $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        if($user['权限']=='超级用户'){
            $key=$_POST['key'];
            $file='./uploads/rlydsv1453614397/cars/'.$key;
            if(unlink($file)){
                echo 1;
                exit;
            } 
            echo 0;
            exit;
        }
    }
    public  function fileupload(){
        $carno=$_GET['carno'];
        $path='./uploads/'.$this->token.'/cars/'.$carno.'/';
        $files=scandir($path);
        $attrs=array();
        foreach($files as $key=>$value){
            if(!in_array($files[$key],array('.','..'))){
                $files[$key]='http://www.nsayc.com/uploads/rlydsv1453614397/cars/'.$carno.'/'.$value;
                $attr['key']=$carno.'/'.$value;
                $attr['showDelete']=true;
                $attr['showZoom']=true;
                $attr['width']='120px';
                array_push($attrs,$attr);
                
            }
            else
                unset($files[$key]);
          }
        if($files){
         $this->assign('files',json_encode(array_values($files)));
       }else{
           $this->assign('files','[]');
        }
        $this->assign('attrs',json_encode(array_values($attrs)));
        $this->assign('carno',$carno);
        $this->display();
    }
    function upload($filetypes=''){
       import('ORG.Net.UploadFile');
       $upload = new UploadFile();
       $upload->maxSize  = intval(C('up_size'))*1024 ;
       if (!$filetypes){
           $upload->allowExts  = explode(',',C('up_exts'));
       }else {
           $upload->allowExts  = $filetypes;
       }
       $carno=$_GET['carno'];
       $upload->autoSub=0;
       $upload->saveRule=$this->genfilename();
       $upload->thumbRemoveOrigin=true;
       
       $upload->savePath =  './uploads/'.$this->token.'/cars/'.$carno.'/';// 设置附件上传目录
       //
       if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads')||!is_dir($_SERVER['DOCUMENT_ROOT'].'/uploads')){
           mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads',0777);
       }
       $firstLetterDir=$_SERVER['DOCUMENT_ROOT'].'/uploads/'.$this->token;
       if (!file_exists($firstLetterDir)||!is_dir($firstLetterDir)){
           mkdir($firstLetterDir,0777);
       }
       if (!file_exists($firstLetterDir.'/cars')||!is_dir($firstLetterDir.'/cars')){
           mkdir($firstLetterDir.'/cars',0777);
       }
       if (!file_exists($firstLetterDir.'/cars/'.$carno)||!is_dir($firstLetterDir.'/cars/'.$carno)){
           mkdir($firstLetterDir.'/cars/'.$carno,0777);
       }
       //
       
       $upload->hashLevel=4;
       if(!$upload->upload()) {// 上传错误提示错误信息
           $msg=$upload->getErrorMsg();
           echo $msg;
           exit;

       }else{// 上传成功 获取上传文件信息
           $info =  $upload->getUploadFileInfo();
           $this->siteUrl=$this->siteUrl?$this->siteUrl:C('site_url');
           $msg=$this->siteUrl.substr($upload->savePath,1).$info[0]['savename'];
           echo json_encode($info);
           exit;

       }
      
   }
   #endregion

   public function wxinfo()
    {
        if(IS_POST){
            $carinfo=$_POST;
            unset($carinfo['流水号']);
            unset($carinfo['车辆图片']);
            foreach($carinfo as $key=>$value){
                if($carinfo[$key]==null||$carinfo[$key]=='null')
                    unset($carinfo[$key]);
            }
            if(M('车辆档案','dbo.','difo')->where(array('流水号'=>$_POST['流水号']))->save($carinfo)){
                echo '保存成功';
            }
            else{
                  echo '保存失败';
            
            }
            exit();

        }
        $xh=$_GET['xh'];
        $carinfo=M('维修','dbo.','difo')->where(array('流水号'=>$xh))->find();
        $shop=$carinfo['门店'];
        $zhiwu='维修技工';
        if(in_array($carinfo['维修类别'],array('蜡水洗车','汽车美容'))){
            $zhiwu='美容技工'; 
        }
        if($shop&&$shop!=''&&$shop!='NULL'){
            $zhuxiu=M('员工目录','dbo.','difo')->where(array('部门'=>$shop,'职务'=>$zhiwu))->select();
           }else{
               $zhuxiu=M('员工目录','dbo.','difo')->where(array('职务'=>$zhiwu))->select();       
           }    
        $discount=M('会员详细信息','dbo.','difo')->where(array('ID'=>$carinfo['客户ID']))->find();

        $this->assign('list',$zhuxiu);
        $this->assign('discount',$discount);
        $this->assign('carinfo',json_encode($carinfo));
        $this->display();
    }
   public function getlog(){
       $id=$_GET['id'];
       $page=$_POST['page'];
       $pagesize=$_POST['pagesize'];
       $count=M('系统日志','dbo.','difo')->where(array('单据编号'=>$id))->count();
       $carinfo=M('系统日志','dbo.','difo')->where(array('单据编号'=>$id))->limit(($page-1)*$pagesize,$pagesize)->select();
       $data['Rows']=$carinfo;
       $data['Total']=$count;
       echo json_encode($data);
   }
   public function deleteproject(){
       $projects=$_POST['projects'];
       $wxrecord=$_POST['record'];
       foreach($projects as $project)
       {
              $num=$project['流水号'];
              M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->delete();
              $this->writeLog($project['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'增加项目—'.$project['项目名称']);

       }
       $this->calprice($projects[0]['ID']);

       echo '删除成功';
   }
   public function deleteproduct(){
        $products=$_POST['products'];
        foreach($products as $product)
       {
           $num=$product['流水号'];
              M('维修配件','dbo.','difo')->where(array('流水号'=>$num))->delete();
       }
        $this->calprice($products[0]['ID']);

       echo '删除成功';
   }
   public function saveproject(){
       $projects=$_POST['projects'];
       $wxrecord=$_POST['record'];
       foreach($projects as $project)
       {
          if($project['__status']=='add')
          {
            unset($project['__status']);
            unset($project['流水号']);
            unset($project['开工时间']);
            unset($project['完工时间']);

            if(isset($project['主修人'])&&$project['主修人']!=''){
                $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$project['主修人']))->field('班组')->find();
                $project['班组']=$yg['班组'];
            }
            M('维修项目','dbo.','difo')->add($project);
            //M('维修','dbo.','difo')->where(array('ID'=>$project['ID'],'单据类别'=>'普通单'))->save(array('当前状态'=>'派工'));
            $this->writeLog($project['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'增加项目—'.$project['项目名称']);

          }
          elseif($project['__status']=='update'){
              unset($project['__status']);
              unset($project['ROW_NUMBER']);
              unset($project['开工时间']);
              unset($project['完工时间']);
              $num=$project['流水号'];
              unset($project['流水号']);
              M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->save($project);
              $this->writeLog($project['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'修改项目—'.$project['项目名称']);

          }else{
              $num=$project['流水号'];
              M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->delete();
              $this->writeLog($project['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'删除项目—'.$project['项目名称']);
          
          }

       }
       $this->calprice($projects[0]['ID']);
       echo '保存成功';
   }
   public function saveprojectbyid(){
       $project=$_POST['project'];
       $wxrecord=$_POST['record'];
        $num=$project['流水号'];
        unset($project['流水号']);
        M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->save($project);
        $this->writeLog($project['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'修改项目—'.$project['项目名称']);
       $this->calprice($project['ID']);
       echo '保存成功';
   }
   public function saveproductbyid(){
       $product=$_POST['product'];
       $wxrecord=$_POST['record'];
       $num=$product['流水号'];
       unset($product['流水号']);
       M('维修配件','dbo.','difo')->where(array('流水号'=>$num))->save($product);
       $this->writeLog($product['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'修改配件—'.$product['项目名称']);
       $this->calprice($product['ID']);
       echo '保存成功';
   }
   public function saveproduct(){
       $products=$_POST['products'];
       $wxrecord=$_POST['record'];
       foreach($products as $product)
       {
           if($product['__status']=='add')
          {
              unset($product['__status']);
              unset($product['流水号']);
              M('维修配件','dbo.','difo')->add($product);
              //M('维修','dbo.','difo')->where(array('ID'=>$product['ID'],'单据类别'=>'普通单'))->save(array('当前状态'=>'领料'));
              $this->writeLog($product['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'增加配件—'.$product['名称']);

          }
          elseif($product['__status']=='update'){
              unset($product['__status']);
              unset($product['ROW_NUMBER']);
              $num=$product['流水号'];
              unset($product['流水号']);
              M('维修配件','dbo.','difo')->where(array('流水号'=>$num))->save($product);
              $this->writeLog($product['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'修改配件—'.$product['名称']);

          }else{
              M('维修配件','dbo.','difo')->where(array('流水号'=>$product['流水号']))->delete();
              $this->writeLog($product['ID'],$wxrecord['业务编号'],$wxrecord['维修类别'],'删除配件—'.$product['名称']);


           }
           $this->calprice($products[0]['ID']);
       }
       echo '保存成功';
   }
   public function savewxinfo(){
      $wxinfo=$_POST;
      $xh=$_POST['流水号'];
      unset($wxinfo['流水号']);
      unset($wxinfo['进厂时间']);
      //unset($wxinfo['预计完工']);
      unset($wxinfo['上交钥匙']);
      unset($wxinfo['开工时间']);
      unset($wxinfo['实际完工']);
      unset($wxinfo['出厂时间']);
      unset($wxinfo['结束日期']);
      unset($wxinfo['结算日期']);
      unset($wxinfo['评价时间']);
      unset($wxinfo['购买日期']);
      M('维修','dbo.','difo')->where(array('流水号'=>$xh))->save($wxinfo);
      $this->calprice($wxinfo['ID']);
      $this->writeLog($wxinfo['ID'],$wxinfo['业务编号'],$wxinfo['维修类别'],'更新维修资料');
      echo '保存成功';
  }
   public function pay(){
    if(IS_POST){
        $wx=$_POST['record']; 
        $this->genwxbill($wx);
        $data['当前状态']='出厂'; 
        //$data['出厂时间']=date('Y-m-d H:i',time());
        //$data['实际完工']=date('Y-m-d H:i',time());
        $data['结算日期']=date('Y-m-d',time());
        $data['现收金额']=$wx['现收金额'];
        $data['收款人']=cookie('username');
        $data['挂账金额']=$wx['挂账金额'];
        $data['优惠金额']=$wx['优惠金额'];
        $data['结算方式']=$wx['结算方式'];
        $data['标志']='已结算';
        $code=$wx['流水号'];
        M('维修','dbo.','difo')->where(array('流水号'=>$code))->save($data);
        $this->writeLog($wx['ID'],$wx['业务编号'],$wx['维修类别'],'维修结算');

    }else{
        $this->display();
    }
  }
   public function modifyproject()
   {
       $id=$_GET['id'];
       $projectinfo=M('维修项目','dbo.','difo')->where(array('流水号'=>$id))->find();
       $this->assign('projectinfo',json_encode($projectinfo));
       $this->display();
   }
   private function gendbbill($price,$chezhu,$zhaiyao,$daiwen,$bianhao,$type,$yyid,$carno){
       $paybill['ID']=$this->getcode(18,1,1);
       $paybill['单位编号']=$daiwen;
       $paybill['单位名称']=$chezhu;
       $paybill['单据类别']=$type;
       $paybill['引用ID']=$yyid;
       $paybill['单据编号']=$bianhao;
       $paybill['制单日期']=date('Y-m-d',time());
       $paybill['制单人']=cookie('username');
       $paybill['总金额']=$price;
       $paybill['已结算金额']=0;
       $paybill['未结算金额']=$price;
       $paybill['本次结算']=0;
       $paybill['提醒日期']=date('Y-m-d',time());
       $paybill['账款类别']='应付款';
       $paybill['当前状态']='待审核';
       //$paybill['审核人']=cookie('username'); 
       //$paybill['审核日期']=date('Y-m-d',time());
       $paybill['摘要']=$zhaiyao;
       $paybill['虚增价税']=0;
       $paybill['挂账金额']=$price; 
       $paybill['车牌号码']=$carno;
       $paybill['门店']='爱养车';
       M('应收应付单','dbo.','difo')->add($paybill);
   }
   public function dbcheck(){
       if(IS_POST){
           $dbinfo=$_POST['dbinfo'];
           $purchase['当前状态']='结束';
           $purchase['审核人']=cookie('username');
           $purchase['审核日期']=date('Y-m-d',time());
           $purchase['应收金额']=$dbinfo['应收金额'];
           $purchase['现收金额']=$dbinfo['现收金额'];
           $purchase['挂账金额']=$dbinfo['挂账金额'];
           $purchase['优惠金额']=$dbinfo['优惠金额'];
           M('车辆代办','dbo.','difo')->where(array('流水号'=>$dbinfo['流水号']))->save($purchase);
           if($dbinfo['代办费用']>0){
               $wldw=M('往来单位','dbo.','difo')->where(array('名称'=>$dbinfo['车管单位']))->find();
               $this->gendbbill($dbinfo['代办费用'],$dbinfo['车管单位'],'代办'.$dbinfo['代办类型'].'付款('.$dbinfo['业务编号'].')',$wldw['ID'],$dbinfo['业务编号'],'其它代办',$dbinfo['ID'],$dbinfo['车牌号码']);
           }
           $paybill['ID']=$this->getcode(18,1,1);
           $paybill['单位编号']=$dbinfo['客户ID'];
           $paybill['单位名称']=$dbinfo['车主'];
           $paybill['单据类别']=$dbinfo['代办类别'];
           $paybill['单据编号']=$dbinfo['业务编号'];
           $paybill['引用ID']=$dbinfo['ID'];
           $paybill['制单日期']=date('Y-m-d',time());
           $paybill['制单人']=cookie('username');
           $paybill['总金额']=$dbinfo['总金额'];
           $paybill['已结算金额']=$dbinfo['现收金额'];
           $paybill['未结算金额']=$dbinfo['挂账金额'];
           $paybill['本次结算']=$dbinfo['现收金额'];
           $paybill['提醒日期']=date('Y-m-d',time());
           $paybill['账款类别']='应收款';
           $paybill['当前状态']='待审核';
           $paybill['车牌号码']=$dbinfo['车牌号码'];
           $paybill['摘要']=$dbinfo['代办类别'].'欠款';

           if($dbinfo['挂账金额']==0){
               $paybill['当前状态']='已审核';
               $paybill['摘要']=$dbinfo['代办类别'];
               $paybill['审核人']=cookie('username');
               $paybill['审核日期']=date('Y-m-d',time());
           }
           $paybill['虚增价税']=0;
           $paybill['挂账金额']=$dbinfo['挂账金额'];
           $paybill['车牌号码']=$dbinfo['车牌号码'];
           $paybill['门店']='爱养车';
           M('应收应付单','dbo.','difo')->add($paybill);
           if($dbinfo['挂账金额']==0){
               $inout['单据编号']=$this->getcodenum('BI');
               $inout['制单日期']=date('Y-m-d',time());
               $inout['制单人']=cookie('username');
               $inout['单位名称']=$dbinfo['车主'];
               $inout['账款类别']='收款单';
               $inout['实收金额']=$dbinfo['现收金额'];
               $inout['折扣金额']=0;
               $inout['结算方式']=$dbinfo['结算方式'];
               $inout['结算账户']='爱养车';
               $inout['摘要']='车辆代办收款('.$dbinfo['业务编号'].')';
               $inout['收支项目']=$dbinfo['代办类别'];
               $inout['当前状态']='待审核';
               $inout['发票类别']='爱养车';
               $inout['发票号']=$dbinfo['车牌号码'];
               $inout['单位编号']=$dbinfo['客户ID'];
               $inout['ID']=$this->getcode(18,1,1);
               $inout['单位编号']=$dbinfo['供应商ID'];
               $inout['本次冲账']=$dbinfo['现收金额'];
               $inout['单据类别']='应收款';
               $inout['取用预存']=0;
               M('日常收支','dbo.','difo')->add($inout);
               $dj['挂账ID']=$paybill['ID'];
               $dj['收支ID']=$inout['ID'];
               $dj['金额']=$dbinfo['现收金额'];
               M('引用单据','dbo.','difo')->add($dj);
           }
           echo '审核通过';
           exit;
           

       }
       else{
           $this->display();
       }

   }
   public function bxcheck(){
       if(IS_POST){
           $bxinfo=$_POST['bxinfo'];
           $purchase['当前状态']='结束';
           $purchase['审核人']=cookie('username');
           $purchase['审核日期']=date('Y-m-d',time());
           $purchase['应收金额']=$bxinfo['应收金额'];
           $purchase['现收金额']=$bxinfo['现收金额'];
           $purchase['挂账金额']=$bxinfo['挂账金额'];
           $purchase['优惠金额']=$bxinfo['优惠金额'];
           M('车辆保险','dbo.','difo')->where(array('流水号'=>$bxinfo['流水号']))->save($purchase);
           if($bxinfo['总金额']>0){
               $wldw=M('往来单位','dbo.','difo')->where(array('名称'=>$bxinfo['保险公司']))->find();
               $this->gendbbill($bxinfo['总金额'],$bxinfo['保险公司'],'代办保险付款('.$bxinfo['业务编号'].')',$wldw['ID'],$bxinfo['业务编号'],'保险代办',$bxinfo['ID'],$bxinfo['车牌号码']);
           }
           $paybill['ID']=$this->getcode(18,1,1);
           $paybill['单位编号']=$bxinfo['客户ID'];
           $paybill['单位名称']=$bxinfo['车主'];
           $paybill['单据类别']='保险';
           $paybill['单据编号']=$bxinfo['业务编号'];
           $paybill['引用ID']=$bxinfo['ID'];
           $paybill['制单日期']=date('Y-m-d',time());
           $paybill['制单人']=cookie('username');
           $paybill['总金额']=$bxinfo['挂账金额'];
           $paybill['已结算金额']=$bxinfo['现收金额'];
           $paybill['未结算金额']=$bxinfo['挂账金额'];
           $paybill['本次结算']=$bxinfo['现收金额'];
           $paybill['提醒日期']=date('Y-m-d',time());
           $paybill['账款类别']='应收款';
           $paybill['当前状态']='待审核';
           $paybill['车牌号码']=$bxinfo['车牌号码'];
           $paybill['摘要']='保险代办欠款';
           if($bxinfo['挂账金额']==0){
               $paybill['当前状态']='已审核';
               $paybill['摘要']='保险代办';
               $paybill['审核人']=cookie('username');
               $paybill['审核日期']=date('Y-m-d',time());
           }
           $paybill['虚增价税']=0;
           $paybill['挂账金额']=$bxinfo['挂账金额'];
           $paybill['车牌号码']=$bxinfo['车牌号码'];
           $paybill['门店']='爱养车';
           M('应收应付单','dbo.','difo')->add($paybill);
           if($bxinfo['挂账金额']==0){
               $inout['单据编号']=$this->getcodenum('BI');
               $inout['制单日期']=date('Y-m-d',time());
               $inout['制单人']=cookie('username');
               $inout['单位名称']=$bxinfo['车主'];
               $inout['账款类别']='收款单';
               $inout['实收金额']=$bxinfo['现收金额'];
               $inout['折扣金额']=0;
               $inout['结算方式']=$bxinfo['结算方式'];
               $inout['结算账户']='爱养车';
               $inout['摘要']='车辆代办收款('.$bxinfo['业务编号'].')';
               $inout['收支项目']='车辆保险';
               $inout['当前状态']='待审核';
               $inout['发票类别']='爱养车';
               $inout['发票号']=$bxinfo['车牌号码'];
               $inout['单位编号']=$bxinfo['客户ID'];
               $inout['ID']=$this->getcode(18,1,1);
               $inout['单位编号']=$bxinfo['供应商ID'];
               $inout['本次冲账']=$bxinfo['现收金额'];
               $inout['单据类别']='应收款';
               $inout['取用预存']=0;
               M('日常收支','dbo.','difo')->add($inout);
               $dj['挂账ID']=$paybill['ID'];
               $dj['收支ID']=$inout['ID'];
               $dj['金额']=$bxinfo['现收金额'];
               M('引用单据','dbo.','difo')->add($dj);
           }
           echo '审核通过';
           exit;
           

       }
       else{
           $this->display();
       }

   }
   public function purchasecheck(){
       if(IS_POST){
           $cgd=$_POST['cgd'];
           $record=M('采购单','dbo.','difo')->where(array('流水号'=>$cgd['流水号']))->find();
           if($record['当前状态']=='已审核'){
               echo '该单已经审核';
               exit;
           }
           $cgmx=M('采购明细','dbo.','difo')->where(array('ID'=>$cgd['ID']))->select();
           if($cgd['单据类别']=='采购进货'){
                $purchase['当前状态']='已审核';
                $purchase['审核人']=cookie('username');
                $purchase['审核日期']=date('Y-m-d',time());
                $purchase['应结金额']=$cgd['应结金额'];
                $purchase['现结金额']=$cgd['现结金额'];
                $purchase['挂账金额']=$cgd['挂账金额'];
                $purchase['优惠金额']=$cgd['优惠金额'];
                M('采购单','dbo.','difo')->where(array('流水号'=>$cgd['流水号']))->save($purchase);

                $paybill['ID']=$this->getcode(18,1,1);
                $paybill['单位编号']=$cgd['供应商ID'];
                $paybill['单位名称']=$cgd['供应商'];
                $paybill['单据类别']='采购进货';
                $paybill['单据编号']=$cgd['单据编号'];
                $paybill['引用ID']=$cgd['ID'];
                $paybill['制单日期']=date('Y-m-d',time());
                $paybill['制单人']=cookie('username');
                $paybill['总金额']=$cgd['总金额'];
                $paybill['已结算金额']=$cgd['现结金额'];
                $paybill['未结算金额']=$cgd['挂账金额'];
                $paybill['本次结算']=$cgd['现结金额'];
                $paybill['提醒日期']=date('Y-m-d',time());
                $paybill['账款类别']='应付款';
                $paybill['当前状态']='待审核';
                if($cgd['挂账金额']==0){
                    $paybill['当前状态']='已审核';
                    $paybill['审核人']=cookie('username');
                    $paybill['审核日期']=date('Y-m-d',time());
                }
                $paybill['摘要']='采购进货';
                $paybill['虚增价税']=0;
                $paybill['挂账金额']=$cgd['挂账金额'];
                $paybill['车牌号码']=$cgd['车牌号码'];
                $paybill['门店']=$cgd['门店'];
                M('应收应付单','dbo.','difo')->add($paybill);
                if($cgd['挂账金额']==0){
                    $inout['单据编号']=$this->getcodenum('BE');
                    $inout['制单日期']=date('Y-m-d',time());
                    $inout['制单人']=cookie('username');
                    $inout['单位名称']=$cgd['供应商'];
                    $inout['账款类别']='付款单';
                    $inout['实付金额']=$cgd['现结金额'];
                    $inout['折扣金额']=0;
                    $inout['结算方式']='支出';
                    $inout['结算账户']=$cgd['门店'];
                    $inout['摘要']='采购进货付款('.$cgd['单据编号'].')';
                    $inout['收支项目']='采购进货';
                    $inout['当前状态']='待审核';
                    $inout['发票类别']=$cgd['门店'];
                    $inout['发票号']=$cgd['车牌号码'];
                    $inout['ID']=$this->getcode(18,1,1);
                    $inout['单位编号']=$cgd['供应商ID'];
                    $inout['本次冲账']=$cgd['现结金额'];
                    $inout['单据类别']='应付款';
                    $inout['取用预存']=0;
                    M('日常收支','dbo.','difo')->add($inout);
                    $dj['挂账ID']=$paybill['ID'];
                    $dj['收支ID']=$inout['ID'];
                    $dj['金额']=$cgd['现收金额'];
                    M('引用单据','dbo.','difo')->add($dj);
                }
                $crkitem['ID']=$this->getcode(20,1,1);
                $crkitem['引用单号']=$cgd['单据编号'];
                $crkitem['引用ID']=$cgd['ID'];
                $crkitem['引用类别']='采购进货';
                $crkitem['单据编号']=$this->getcodenum('RK');
                $crkitem['制单日期']=date('Y-m-d',time());;
                $crkitem['制单人']=cookie('username');
                $crkitem['车牌号码']=$cgd['车牌号码'];
                $crkitem['门店']=$cgd['门店'];
                $crkitem['当前状态']='待审核';
                $crkitem['原因']='采购进货';
                $crkitem['领料员']=cookie('username');
                $crkitem['单据类别']='入库';
                $crkitem['单据备注']='采购进货';
                M('出入库单','dbo.','difo')->add($crkitem);
                foreach($cgmx as $product){
                    $crk['ID']=$crkitem['ID'];
                    $crk['仓库']=$product['仓库'];
                    $crk['编号']=$product['编号'];
                    $crk['名称']=$product['名称'];
                    $crk['规格']=$product['规格'];
                    $crk['单位']=$product['单位'];
                    $crk['门店']=$cgd['门店'];
                    $crk['数量']=$product['数量'];
                    $crk['单价']=$product['单价'];
                    $crk['金额']=$product['金额'];
                    $crk['成本价']=$product['单价'];
                    $crk['适用车型']=$product['适用车型'];
                    $crk['产地']=$product['产地'];
                    $crk['备注']=$product['备注'];
                    M('出入库明细','dbo.','difo')->add($crk);
                }
                $this->writeLog($cgd['引用ID'],$cgd['引用单号'],'采购审核','采购审核');

                echo '审核通过';
                exit;
           }
           else{
           
           }

       }
       else{
           $this->display();
       }

   }
   public function unpurchasecheck(){
       if(IS_POST){
           $cgd=$_POST['cgd']; 
           $record=M('采购单','dbo.','difo')->where(array('流水号'=>$cgd['流水号']))->find();
           if($record['当前状态']=='待审核'){
               echo '该单已经反审核';
               exit;
           }
           if($cgd['单据类别']=='采购进货'){
                $crk=M('出入库单','dbo.','difo')->where(array('引用单号'=>$cgd['单据编号']))->find();
                if($crk&&$crk['当前状态']=='已审核'){
                    echo '请先将单号为'.$crk['单据编号'].'出入库单反审核';
                    exit;
                }
                $purchase['当前状态']='待审核';
                M('采购单','dbo.','difo')->where(array('流水号'=>$cgd['流水号']))->save($purchase);
                $ys=M('应收应付单','dbo.','difo')->where(array('引用ID'=>$cgd['ID']))->find();
                $dj=M('引用单据','dbo.','difo')->where(array('挂账ID'=>$ys['ID']))->find();
                M('应收应付单','dbo.','difo')->where(array('ID'=>$ys['ID']))->delete();
                M('日常收支','dbo.','difo')->where(array('ID'=>$dj['收支ID']))->delete();
                M('应收应付单','dbo.','difo')->where(array('引用ID'=>$cgd['ID']))->delete();
                $this->writeLog($cgd['引用ID'],$cgd['引用单号'],'采购反审核','采购反审核');
                echo '反审核通过';
                exit;
           }
       }
      

   }
   public function deletepurchase(){
       if(IS_POST){
           $cgd=$_POST['cgd'];
           if($cgd['引用ID']!=''){
               $cgmx=M('采购明细','dbo.','difo')->where(array('ID'=>$cgd['ID']))->select();
               foreach($cgmx as $item){
                   $data['是否采购']=0;
                   M('维修配件','dbo.','difo')->where(array('ID'=>$cgd['引用ID'],'编号'=>$item['编号']))->save($data);
               }
               //$this->writeLog($crk['ID'],$crk['单据编号'],'出入库审核','删除维修领料出库单—'.$crk['单据编号']);
           }
           M('采购明细','dbo.','difo')->where(array('ID'=>$cgd['ID']))->delete();
           M('采购单','dbo.','difo')->where(array('流水号'=>$cgd['流水号']))->delete();
           echo '删除成功';
           exit;
       }
       
   }
   public function changelowerbound(){
     
       if(IS_POST){
           $id=$_POST['流水号'];
           $num=$_POST['警戒下限'];
           M('配件仓位','dbo.','difo')->where(array('流水号'=>$id))->save(array('警戒下限'=>$num));
           echo '保存成功';
       }
   }
   public function carinfo()
    {
        if(IS_POST){
            $carinfo=$_POST['carinfo'];
            $type=$_POST['type'];
            if($type&&$type=='add'){
                if(M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carinfo['车牌号码']))->find())
                {
                    echo '系统中已存在该车牌号码';
                    exit();  
                }
                if(!isset($carinfo['客户ID'])||$carinfo['客户ID']==''){
                    $czinfo['名称']=$carinfo['车牌号码'];
                    $carinfo['车主']=$carinfo['车牌号码'];
                    $czinfo['客户']=1;
                    $czinfo['ID']=$this->getcode(18,0,0);
                    $carinfo['客户ID']=$czinfo['ID'];
                    $czinfo['会员']=0;
                    $czinfo['联系人']=$carinfo['联系人'];
                    $czinfo['联系电话']=$carinfo['联系电话'];
                    $czinfo['手机号码']=$carinfo['联系电话'];
                    $czinfo['类别']='临时客户';
                    M('往来单位','dbo.','difo')->add($czinfo);
                }
                unset($carinfo['流水号']);
                unset($carinfo['车辆图片']);
                foreach($carinfo as $key=>$value){
                    if($carinfo[$key]==null||$carinfo[$key]=='null')
                        unset($carinfo[$key]);
                }
                M('车辆档案','dbo.','difo')->add($carinfo);
                echo '新增成功';
                exit();               
            }else{
                $code=$carinfo['流水号'];
                unset($carinfo['流水号']);
                unset($carinfo['车辆图片']);
                foreach($carinfo as $key=>$value){
                    if($carinfo[$key]==null||$carinfo[$key]=='null')
                        unset($carinfo[$key]);
                }
                if(M('车辆档案','dbo.','difo')->where(array('流水号'=>$code))->save($carinfo)){
                    echo '保存成功';
                    exit();
                }else{
                    echo '保存失败';
                    exit();
                    
                } 
            }
            

        }
        $carno=$_GET['carno'];
        $carinfo=M('车辆资料','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
        $this->assign('carinfo',json_encode($carinfo));
        $this->display();
    }
   public function memberinfo()
    {
        if(IS_POST){
            $memberinfo=$_POST;
            $number=$memberinfo['流水号'];
            unset($memberinfo['流水号']);
            unset($memberinfo['balance']);
            unset($memberinfo['expensetotal']);
            unset($memberinfo['total_score']);
            if(M('往来单位','dbo.','difo')->where(array('流水号'=>$number))->save($memberinfo)){
                M('车辆档案','dbo.','difo')->where(array('客户ID'=>$_POST['ID']))->save(array('客户类别'=>$_POST['类别']));
                echo '保存成功';
            }
            else{
                  echo '保存失败';
            
            }
            exit();

        }
        $carno=$_GET['carno'];
        $memberinfo=M('往来单位','dbo.','difo')->where(array('名称'=>$carno))->find();
        $wxmemberinfo=M('userinfo')
            ->join('join tp_member_card_create on tp_userinfo.wecha_id=tp_member_card_create.wecha_id')
            ->where(array('tp_member_card_create.number'=>$carno))->find();
        $this->assign('memberinfo',json_encode($memberinfo));
        $this->assign('wxmemberinfo',json_encode($wxmemberinfo));
        $this->display();
    }
   public function carsinfo()
    {
        $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
        $this->assign('lblist',$lblist);
        $this->display();
    }
   public function Annual()
    {
        $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
        $this->assign('lblist',$lblist);
        $this->display();
    }
   public function insurance()
   {
       $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
       $this->assign('lblist',$lblist);
       $this->display();
   }
   public function oilchange()
   {
       $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
       $this->assign('lblist',$lblist);
       $this->display();
   }
   public function userAnalyze()
    {
        $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
        $this->assign('lblist',$lblist);
        $this->display();
    }
   public function member_del(){
		$card_create_db=M('Member_card_create');
		$thisUser=M('Userinfo')->where(array('id'=>intval($_GET['id'])))->find();
		$where=array('wecha_id'=>$thisUser['wecha_id'],'token'=>$this->token);
		M('Member_card_sign')->where($where)->delete();
		M('member_card_car')->where($where)->delete();
		M('member_card_coupon_record')->where($where)->delete();
		M('Member_card_use_record')->where($where)->delete();
		M('Product_cart')->where($where)->delete();
		M('Product_cart_list')->where($where)->delete();
		M('Userinfo')->where($where)->delete();
		$card_create_db->where($where)->save(array('wecha_id'=>''));
		echo('操作成功');
	}
   public function acgmember_del(){
		$card_create_db=M('Member_card_create','','acg');
		$thisUser=M('Userinfo','','acg')->where(array('id'=>intval($_GET['id'])))->find();
		$where=array('wecha_id'=>$thisUser['wecha_id'],'token'=>$thisUser['token']);
		M('Member_card_sign','','acg')->where($where)->delete();
		M('member_card_coupon_record','','acg')->where($where)->delete();
		M('Member_card_use_record','','acg')->where($where)->delete();
		M('Product_cart','','acg')->where($where)->delete();
		M('Product_cart_list','','acg')->where($where)->delete();
		M('Userinfo','','acg')->where($where)->delete();
		$card_create_db->where($where)->save(array('wecha_id'=>''));
		echo('操作成功');
	}
   public function presentcoupon(){
        $db = M('member_card_coupon');
		//$uid = (int)$_GET['uid'];
		$cardid = $this->_get('cardid','intval');
        $days=$_GET['days'];
		$list= $db->where(array('token'=>$this->token,'ispublic'=>'1'))->field('id,title,type,days')->select();
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
                            if(!$days){
                                $days=$item['days'];      
                            }
                            $data['over_time']=strtotime(date('Y-m-d',time())."+$days day");
                            $data['token'] 			= $this->token;
                            $data['is_use'] 			= 0;
                            $data['wecha_id'] 		= $_GET['wecha_id'];
                            $data['coupon_type'] 	= 2;
                            $data['coupon_name']=$item['title'];
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
                                'url'      => U('Wap/Store/cats',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                            );
                            $model->sendTempMsg($dataKey,$dataArr);
                        }
                        $coupon['wecha_id']=$_GET['wecha_id'];
                        $coupon['token']=$this->token;
                        $coupon['coupon_id']=$item['id'];
                        $coupon['coupon_name']=$item['title'];
                        $coupon['comment']=$_POST['comment'];
                        $coupon['num']=$this->_post($item['id'],'intval');
                        $coupon['addtime']=time();
                        $coupon['optuser']=cookie('username');
                        $coupon['proposer']=$_POST['proposer'];
                        M('member_present')->add($coupon);
                    }
                }
            }
            echo '操作成功';
        }
        else{
            $userinfo=M('userinfo')->where(array('wecha_id'=>$_GET['wecha_id']))->find();
            $this->assign('list',$list);
            $this->assign('truename',$userinfo['truename']);
            $this->assign('carno',$userinfo['carno']);
            $this->display();
        }

    }
   public function present(){
        
        $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        
        $this->assign('right',$user['权限']);

		$card_create_db=M('Member_card_create');
		$where=array();
		$where['tp_member_card_create.wecha_id']=array('neq','');
        $parms=$_GET;
		if (IS_POST){
			if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
                $searchkey='%'.trim($_POST['searchkey']).'%';
			}
		}
        else
        {
            if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])!=''){
                $searchkey='%'.trim($_GET['searchkey']).'%';
			}
        }
        if($searchkey){       
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= $card_create_db->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->count(); 
		$Page       = new Page($count,15,$parms);
		$show       = $Page->show();
		$list 		= $card_create_db->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->order('tp_userinfo.getcardtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$members	= $list;
		if ($members){		
			$this->assign('members',$members);
			$this->assign('page',$show);
		}
		$this->display();
	}
   public function membercard(){
        
        $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        
        $this->assign('right',$user['权限']);

		$card_create_db=M('Member_card_create');
		$where=array();
		$where['tp_member_card_create.wecha_id']=array('neq','');
        $parms=$_GET;
		if (IS_POST){
			if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
                $searchkey='%'.trim($_POST['searchkey']).'%';
			}
		}
        else
        {
            if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])!=''){
                $searchkey='%'.trim($_GET['searchkey']).'%';
			}
        }
        if($searchkey){       
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= $card_create_db->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->count(); 
		$Page       = new Page($count,15,$parms);
		$show       = $Page->show();
		$list 		= $card_create_db->join('join tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->order('tp_userinfo.getcardtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$members	= $list;
		if ($members){		
			$this->assign('members',$members);
			$this->assign('page',$show);
		}
		$this->display();
	}
  public function acgmembercard(){
        
        $user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        $this->assign('right',$user['权限']);
		$card_create_db=M('Member_card_create','','acg');
		$where=array();
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
            $searchwhere['tel']=array('like',$searchkey);
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
		$count		= $card_create_db->join('tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->count();
		$Page       = new Page($count,15,$parms);
		$show       = $Page->show();
		$list 		= $card_create_db->join('tp_userinfo on tp_member_card_create.wecha_id=tp_userinfo.wecha_id')->where($where)->order('tp_userinfo.getcardtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$members	= $list;
		if ($members){		
			$this->assign('members',$members);
			$this->assign('page',$show);
		}
		$this->display();
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
   public function quitsystem()
    {
        cookie('username',null);
        echo'{"success":1,"msg":"退出成功"}';	
    }
   public function recharge(){

		if(IS_POST){
		    $db = M('Userinfo');
			$wecha_id = $_POST['wecha_id'];
		    $uinfo = $db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
            $mycard = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
			if($db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->setInc('balance',$_POST['price'])){
				$orderid = date('YmdHis',time()).mt_rand(1000,9999);
                $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
				M('Member_card_pay_record')->add(array('shop'=>$yg['部门'],'orderid' => $orderid , 'ordername' => '前台手动充值' ,'note'=>'操作人'.cookie('username'), 'createtime' => time() ,
                    'token' => $this->token , 'wecha_id' => $uinfo['wecha_id'] , 'price' => $_POST['price'] , 'type' => 1 , 'paid' => 1 , 'module' => 'qiantai' , 'paytime' => time() , 'paytype' =>$_POST['paytype']));
                if(intval($_POST['price'])>=500){
                    $cardnumber=$this->change($mycard['cardid'],$uinfo['wecha_id']);
                    if($cardnumber!='0'){
                       $mycard['number']=$cardnumber;
                       $this->changecarinfo($uinfo,$cardnumber);
                    }
                }
           $paybill['ID']=$this->getcode(18,1,1);
           //$paybill['单位编号']=$wx['客户ID'];
           $paybill['单位名称']=$mycard['number'];
           $paybill['单据类别']='会员充值';
           $paybill['单据编号']=$orderid;
           $paybill['制单日期']=date('Y-m-d',time());
           $paybill['制单人']=cookie('username');
           $paybill['总金额']=$_POST['price'];
           //$paybill['引用ID']=$wx['ID'];
           $paybill['已结算金额']=$_POST['price'];
           $paybill['未结算金额']=0;
           $paybill['本次结算']=$_POST['price'];
           $paybill['提醒日期']=date('Y-m-d',time());
           $paybill['账款类别']='应收款';
           $paybill['当前状态']='待审核';
           $paybill['摘要']=$mycard['number'].'会员充值';
           $paybill['当前状态']='已审核';
           $paybill['审核人']=cookie('username');
           $paybill['审核日期']=date('Y-m-d',time());
           $paybill['虚增价税']=0;
           $paybill['挂账金额']=0;
           //$paybill['车牌号码']=$wx['车牌号码'];
           $paybill['门店']=$yg['部门'];
           M('应收应付单','dbo.','difo')->add($paybill);
      
           $bianhao=$this->getcodenum("BI");
           $data['单据编号']=$bianhao;
           $data['ID']=$this->getcode(20,1,1);
           $data['制单日期']=date('Y-m-d',time());
           $data['制单人']=cookie('username');
           $data['单位名称']=$mycard['number'];
           $data['账款类别']='收款单';
           $data['开户银行']='';
           $data['银行账号']='';
           $data['本次应付']=0;
           $data['本次应收']==$_POST['price'];
           $data['整单折扣']=1;
           $data['实付金额']=0;
           $data['实收金额']=$_POST['price'];
           $data['折扣金额']=0;
           $data['结算方式']=$_POST['paytype'];
           $data['结算账户']=$yg['部门'];
           $data['支票号']=0;
           $data['凭证号']=0;
           $data['摘要']=$mycard['number'].'会员充值';
           $data['收支项目']='会员充值';
           $data['当前状态']='待审核';
           $data['发票类别']=$yg['部门'];
           //$data['发票号']=$wx['车牌号码'];
           //$data['单位编号']=$wx['客户ID'];
           $data['取用预付']=0;
           $data['取用预收']=0;
           $data['本次冲账']=$_POST['price'];
           $data['单据类别']='应收款';
           $data['取用预存']=0;
           M('日常收支','dbo.','difo')->add($data);

           $dj['挂账ID']=$paybill['ID'];
           $dj['收支ID']=$data['ID'];
           $dj['金额']=$_POST['price'];
           M('引用单据','dbo.','difo')->add($dj);
                /*模板消息*/
                $model  = new templateNews();
                $dataKey    = 'TM151125';
                $dataArr    = array(
                    'first'         => '您好，前台会员卡充值成功。',
                    'accountType'      => '会员卡号',
                    'account'      => $mycard['number'],
                    'amount'      => $_POST['price'].'元',
                    'result'      => '充值成功',
                    'wecha_id'      => $uinfo['wecha_id'],
                    'remark'        => '会员充值如有疑问，请致电020-39099139联系我们。',
                    'url'      => U('Wap/Store/myinfo',array('token'=>$this->token,'wecha_id'=>$uinfo['wecha_id'],'cardid'=>$mycard['cardid']),true,false,true),
                );
                $model->sendTempMsg($dataKey,$dataArr);
				echo '充值成功';
				//$this->success('充值成功');

			}else{
				echo '充值失败，请稍后再试';
			}

		}
	}

   public function bindcar(){
    
        $carno = isset($_POST['carno']) ? htmlspecialchars($_POST['carno']) : '';
        $wecha_id = isset($_POST['wecha_id']) ? htmlspecialchars($_POST['wecha_id']) : '';
        $carno=strtoupper($carno);
        $cardno=$_POST['cardno'];
        $carinfo=M('member_card_car')->where(array('token' => $this->token,'carno'=>$carno))->find();
        if(empty($carinfo))
        {   
            $count=M('member_card_car')->where(array('token' =>$this->token,'wecha_id'=>$wecha_id))->count();
            if($count<2||cookie('username')=='阳伟鹏'){
                $user=M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->find();
                if($user['carno']==""){
                    M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno'=>$carno));
                }elseif($user['carno1']==""){
                    M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno1'=>$carno));
                }else{
                    M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno2'=>$carno));
                }
            }else{
                echo '一个微信最多绑定两辆车';
                exit();
            }
            M('member_card_car')->add(array('token' => $this->token,'wecha_id'=>$wecha_id,'carno'=>$carno,'optuser'=>cookie('username'),'bindtime'=>time()));
            $czinfo=M('往来单位','dbo.','difo')->where(array('名称'=>$cardno))->find();
            $item['车主']=$cardno;
            $item['车牌号码']=$carno;
            $item['客户ID']=$czinfo['ID'];
            $item['手机号码']=$user['tel'];
            $item['联系人']=$user['truename'];
            $item['联系电话']=$user['tel']; 
            $item['客户类别']=$czinfo['类别'];
            if(M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find()){
                M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->save($item);
            }else{
             M('车辆档案','dbo.','difo')->add($item);
           }
            echo '添加成功';
            exit();
        }
        else
        {
           //M('member_card_car')->where(array('token' => $this->token,wecha_id=>$wecha_id,'carno'=>$carno))->delete();
           echo '车牌号码已存在';
            exit();
        }

    }
   public function modifycar(){
        $carno = isset($_POST['carno']) ? htmlspecialchars($_POST['carno']) : '';
        $oldcarno = isset($_POST['oldcarno']) ? htmlspecialchars($_POST['oldcarno']) : '';
        $wecha_id = isset($_POST['wecha_id']) ? htmlspecialchars($_POST['wecha_id']) : '';
        $carno=strtoupper($carno);
        $carinfo=M('member_card_car')->where(array('token' => $this->token,'wecha_id'=>$wecha_id,'carno'=>$carno))->find();
        if(empty($carinfo)){
            M('member_card_car')->where(array('wecha_id'=>$wecha_id,'carno'=>$oldcarno))->save(array('carno'=>$carno));
            $user=M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->find();
            if($user['carno1']==$oldcarno){
                M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno1'=>$carno));
            }
            if($user['carno2']==$oldcarno){
                M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno2'=>$carno));
            }
            if($user['carno']==$oldcarno){
                M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno'=>$carno));
            }
            $item['车牌号码']=$carno;
            M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$oldcarno))->save($item);
            echo '保存成功';
            exit();
        }
        else
        {
            echo '车牌号码已存在';
            exit();
        }
        

    }
   public function delcar(){
        $carno = isset($_POST['carno']) ? htmlspecialchars($_POST['carno']) : '';
        $wecha_id = isset($_POST['wecha_id']) ? htmlspecialchars($_POST['wecha_id']) : '';
        $carno=strtoupper($carno);
        $user=M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->find();
        if($user['carno1']==$carno){
            M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno1'=>''));
        }
        if($user['carno2']==$carno){
            M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno2'=>''));
        }
        if($user['carno']==$carno){
            M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno'=>''));
        }
        M('member_card_car')->where(array('wecha_id'=>$wecha_id,'carno'=>$carno))->delete();
        //M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->delete();
        echo '删除成功';
        exit();
       

    }
   public function left()
    {
        //$user=M('用户管理','dbo.','difo')->where(array('姓名'=>cookie('username')))->find();
        //$this->assign('user',$user);
        //$this->display();
    }
   public function index()
    {
        $where=array('1'=>'1');
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['shop']&&trim($_GET['shop'])!='')
        {
          $where=array('shop'=>trim($_GET['shop']));

        }
        else{
           $where['shop']=array('neq','');
        }
        if($searchkey){       
            $searchwhere['carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $parms=$_GET;
        $count= M('Member_card_use_record','','acg')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->count();
		$Page = new Page($count,15,$parms);
		$show = $Page->show();
		$record = M('Member_card_use_record','','acg')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->order('tp_member_card_use_record.time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('record',$record);
        $pay_record=M('Member_card_pay_record','','acg');
        $count2      = $pay_record
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->count();
		$Page2       = new Page($count2,15,$parms);
		$rmb = $pay_record
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->limit($Page2->firstRow.','.$Page2->listRows)->order('createtime DESC')->select();
		$show2       = $Page2->show();
		$this->assign('rmb',$rmb);
		$this->assign('page2',$show2);
        $this->display();
    }
   public function members()
    {
        $where=array('1'=>'1');
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['shop']&&trim($_GET['shop'])!='')
        {
            $where=array('shop'=>trim($_GET['shop']));

        }
        else{
            $where['shop']=array('neq','');
        }
        if($searchkey){       
            $searchwhere['tp_userinfo.carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['orderid']=array('like',$searchkey);
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['ordername']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $parms=$_GET;
        $count= M('Member_card_use_record')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->count();
		$Page = new Page($count,15,$parms);
		$show = $Page->show();
		$record = M('Member_card_use_record')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->order('tp_member_card_use_record.time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('record',$record);
        $pay_record=M('Member_card_pay_record');
        unset($where['shop']);
        $count2      = $pay_record
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
             ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
           ->where($where)->count();
		$Page2       = new Page($count2,15,$parms);
		$rmb = $pay_record
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->limit($Page2->firstRow.','.$Page2->listRows)->order('createtime DESC')->select();
		$show2       = $Page2->show();
		$this->assign('rmb',$rmb);
		$this->assign('page2',$show2);
        $this->display();
		
    }
   public function consume()
    {
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['shop']&&trim($_GET['shop'])!='')
        {
            $where=array('shop'=>trim($_GET['shop']));

        }
        $where['_string']="ordername not like '%充值%' and paid=1";
        if($searchkey){       
            $searchwhere['tp_userinfo.carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['orderid']=array('like',$searchkey);
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['ordername']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $parms=$_GET;
        $count= M('Member_card_pay_record')
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
             ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
           ->where($where)->count();
		$Page = new Page($count,15,$parms);
		$rmb = M('Member_card_pay_record')
            ->join('join tp_userinfo on tp_member_card_pay_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_pay_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('createtime DESC')->select();
		$this->assign('rmb',$rmb);
		$this->assign('page',$Page->show());
        $this->display();
		
    }
   public function usecoupons()
    {
        $where=array('1'=>'1');
        if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
            $searchkey='%'.trim($_GET['searchkey']).'%';
        }
        if($_GET['shop']&&trim($_GET['shop'])!='')
        {
            $where=array('shop'=>trim($_GET['shop']));

        }
        else{
            $where['shop']=array('neq','');
        }
        if($searchkey){       
            $searchwhere['tp_userinfo.carno']=array('like',$searchkey);
            $searchwhere['carno1']=array('like',$searchkey);
            $searchwhere['carno2']=array('like',$searchkey);
            $searchwhere['number']=array('like',$searchkey);
            $searchwhere['notes']=array('like',$searchkey);
            $searchwhere['truename']=array('like',$searchkey);
            $searchwhere['wechaname']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $parms=$_GET;
        $count= M('Member_card_use_record')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->count();
		$Page = new Page($count,15,$parms);
		$show = $Page->show();
		$record = M('Member_card_use_record')
            ->join('join tp_userinfo on tp_member_card_use_record.wecha_id=tp_userinfo.wecha_id')
            ->join('join tp_member_card_create on tp_member_card_use_record.wecha_id=tp_member_card_create.wecha_id')
            ->where($where)->field('truename,number,tp_member_card_use_record.carno,shop,time,notes')
            ->order('tp_member_card_use_record.time desc')->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('page',$show);
        $this->assign('record',$record);
        $this->display();
		
    }
   public function setprice()
    {
        $this->display();
    }
   public function days()
   {
       $parms=$_GET;
       $count=M('维修日营业表','dbo.','difo')->count();
       $Page = new Page($count,15,$parms);
       $show = $Page->show();
       $yelist=M('维修日营业表','dbo.','difo')->limit($Page->firstRow.','.$Page->listRows)->order('日期 desc')->select();
       $this->assign('page',$show);
       $this->assign('count',$count);
       $this->assign('yelist',$yelist);
       $this->display();
   }
   public function assigntask(){
       if(IS_POST){
           $zhuxiu=$_POST['zhuxiu'];
           $itemid=$_POST['itemid'];
           $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$zhuxiu))->find();
           $item['主修人']=$zhuxiu;
           $item['班组']=$yg['班组'];
           $wxinfo=M('维修项目','dbo.','difo')->where(array('ID'=>$itemid))->find();
           if(!isset($wxinfo['开工时间'])){
               $item['开工时间']=date('Y-m-d H:i',time());
           }
           if(isset($_POST['code'])){
               M('维修项目','dbo.','difo')->where(array('流水号'=>$_POST['code']))->save($item);

           }else{
             M('维修项目','dbo.','difo')->where(array('ID'=>$itemid))->save($item);
             $data['当前主修人']=$zhuxiu;
             $data['主修人']=$zhuxiu;
             $wx=M('维修','dbo.','difo')->where(array('ID'=>$itemid))->find();
             if(!isset($wx['开工时间'])||date('Y-m-d',strtotime($wx['开工时间']))=='1900-01-01'){
                 $data['当前状态']='派工';
                 $data['开工时间']=date('Y-m-d H:i',time());
             }
             M('维修','dbo.','difo')->where(array('ID'=>$itemid))->save($data);
          }
           //$data['当前状态']='结束'; 
           //$data['出厂时间']=date('Y-m-d H:i',time());
           //$data['实际完工']=date('Y-m-d H:i',time());
           //$data['结算日期']=date('Y-m-d',time());
           //$data['结束日期']=date('Y-m-d',time());
           //$data['挂账金额']=0;
           //$data['现收金额']=0;
           //$data['标志']='已结算';
           //$this->genbill($wx['应收金额'],$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);
           echo '派工完成';
           exit;
       }
       else{
           $this->display();
       }
   }
   public function selfpick(){
     if(IS_POST){
         $form=$_POST['data'];
         $products=$_POST['products'];
         $data['ID']=$this->getcode(20,1,1);
         $data['引用类别']='自用出库';
         $data['单据编号']=$this->getcodenum('CK');
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['当前状态']='待审核';
         $data['原因']=$form['原因'];
         $data['门店']=$form['门店'];
         $data['领料员']=$form['领料员'];
         $data['单据类别']='出库';
         $data['单据备注']=$form['备注'];
        if($form['备注']==''){ 
            $data['单据备注']='自用';
        }
        if($form['原因']==''){ 
            $data['原因']='自用出库';
        }
        M('出入库单','dbo.','difo')->add($data);
        foreach($products as $product){
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=$product['数量'];
            $crk['单价']=$product['单价'];
            $crk['金额']=$product['金额'];
            $crk['门店']=$form['门店'];
            $crk['成本价']=$product['成本价'];
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            M('出入库明细','dbo.','difo')->add($crk);
           
        }
        echo '自用出库成功';
    }
     else{ 

         $this->display();
     }
   }
   public function havepick(){
       $products=$_POST['products'];
       foreach($products as $product){
            $pj=M('维修配件','dbo.','difo')->where(array('流水号'=>$product['流水号']))->find();
            if ($pj['数量'] - $pj['已领料数量'] - $pj['待审核数量'] <= 0){
                echo $pj['名称']."已经领料";
                exit;
           }
          $kc=M('配件仓位','dbo.','difo')->where(array('编号'=>$pj['编号'],'仓库'=>$pj['仓库']))->find();
          if($kc['库存']+$pj['已领料数量']-$pj['数量']-$pj['待审核数量']<0){
              echo $pj['名称']."库存不够";
              exit;
          }
           
       }
       echo 'success';
   }
   public function picking(){
     if(IS_POST){
         $wxinfo=$_POST['wxinfo'];
         $form=$_POST['data'];
         $wxID=$wxinfo['ID'];
         $products=$_POST['products'];
         $data['ID']=$this->getcode(20,1,1);
         $data['引用单号']=$wxinfo['业务编号'];
         $data['引用ID']=$wxID;
         $data['引用类别']='维修领料';
         $data['单据编号']=$this->getcodenum('CK');
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['车牌号码']=$wxinfo['车牌号码'];
         $data['门店']=$wxinfo['门店'];
         $data['当前状态']='待审核';
         $data['原因']='维修领料';
         $data['领料员']=$form['领料员'];
         $data['单据类别']='出库';
         $data['单据备注']=$form['备注'];
        if($form['备注']==''){
            $data['单据备注']='维修领料';
        }
        M('出入库单','dbo.','difo')->add($data);
        foreach($products as $product){
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['门店']=$wxinfo['门店'];
            $crk['数量']=$product['本次领料'];
            $crk['单价']=$product['单价'];
            $crk['金额']=$product['金额'];
            $crk['成本价']=$product['成本价'];
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            $num=$product['本次领料'];
            $code=$product['编号'];
            M('出入库明细','dbo.','difo')->add($crk);
            M('配件目录','dbo.','difo')->execute("UPDATE 配件目录 SET 维修领用=维修领用+$num WHERE 编号='$code'");
            M('维修配件','dbo.','difo')->execute("UPDATE 维修配件 SET 待审核数量=isnull(待审核数量,0)+$num WHERE ID='$wxID'and 编号='$code'");
           
        }
        echo '领料成功';
    }
     else{ 

         $this->display();
     }
   }
   public function pickcheck(){      
       if(IS_POST){
           $crk=$_POST['crk'];
           $record=M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->find();
           if($record['当前状态']=='已审核'){
               echo '此单已审核';
               exit;
           }
           $crkmx=M('出入库明细','dbo.','difo')->where(array('ID'=>$crk['ID']))->select();
           if($crk['单据类别']=='出库'){
               if(count($crkmx)>0){
                   foreach($crkmx as $item){
                       if($item['编号']!=''){
                           $num=$item['数量'];
                           $code=$item['编号'];
                           $ck=$item['仓库'];
                           if($crk['引用类别']=='维修领料'){
                               $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                               $num=$pj['待审核数量'];
                               $data['待审核数量']=0;
                               $data['已领料数量']=$pj['已领料数量']+ $num;
                               M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                           }
                           M('配件目录','dbo.','difo')->execute("update 配件目录 set 库存=库存-$num where 编号='$code'");
                           M('配件仓位','dbo.','difo')->execute("update 配件仓位 set 库存=库存-$num where 编号='$code' and 仓库='$ck'");
                       }
                   }
               }
               $crkitem['当前状态']='已审核';
               $crkitem['审核人']=cookie('username');
               $crkitem['审核日期']=date('Y-m-d',time());
               M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->save($crkitem);
               $this->writeLog($crk['ID'],$crk['单据编号'],'出库审核',$crk['单据类别'].'出库审核');
               echo '审核通过';
               exit;
           }
           else{
               foreach($crkmx as $item){
                   $num=$item['数量'];
                   $code=$item['编号'];
                   $ck=$item['仓库'];
                   $price=$item['单价'];
                   if($crk['引用类别']=='维修退料'){
                       $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                       $num=$pj['已退料数量'];
                       $data['已领料数量']=$pj['已领料数量']-$num;
                       M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                   }
                   $pjinfo=M('配件仓位','dbo.','difo')->where(array('_string'=>"编号='$code' and 仓库='$ck'"))->find();
                   $pj=M('配件目录','dbo.','difo')->where(array('_string'=>"编号='$code' "))->find();
                   $cbprice=round(($pjinfo['库存']*$pjinfo['成本价']+$num*$price)/($pjinfo['库存']+$num),2);
                   $lsj=$price+doubleval($pj['零售利润']);
                   $pfj=$price+doubleval($pj['批发利润']);
                   M('配件目录','dbo.','difo')->execute("update 配件目录 set 库存=库存+$num,最新进价=$price,参考售价=$lsj,一级批发价=$pfj  where 编号='$code'");
                   M('配件仓位','dbo.','difo')->execute("update 配件仓位 set 库存=库存+$num,最新进价=$price,成本价=$cbprice,参考售价=$lsj,一级批发价=$pfj where 编号='$code' and 仓库='$ck'");
               }
               $crkitem['当前状态']='已审核';
               $crkitem['审核人']=cookie('username');
               $crkitem['审核日期']=date('Y-m-d',time());
               M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->save($crkitem);
               $this->writeLog($crk['ID'],$crk['单据编号'],'入库审核','采购入库审核');
               echo '审核通过';
               exit;
           }

       }
       else{
           $this->display();
       }

   }
   public function unpickcheck(){
       if(IS_POST){
           $crk=$_POST['crk'];
           $crkmx=M('出入库明细','dbo.','difo')->where(array('ID'=>$crk['ID']))->select();
           $record=M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->find();
           if($record['当前状态']=='待审核'){
               echo '此单已反审核';
               exit;
           }
           if($crk['单据类别']=='出库'){
               if($crkmx){
                   foreach($crkmx as $item){
                       $num=$item['数量'];
                       $code=$item['编号'];
                       $ck=$item['仓库'];
                       if($crk['引用类别']=='维修领料'){
                           $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                           $data['待审核数量']=$num;
                           $data['已领料数量']=$pj['已领料数量']-$num;
                           M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                       }
                       M('配件目录','dbo.','difo')->execute("update 配件目录 set 库存=库存+$num where 编号='$code'");
                       M('配件仓位','dbo.','difo')->execute("update 配件仓位 set 库存=库存+$num where 编号='$code' and 仓库='$ck'");
                   }
               }
               $crkitem['当前状态']='待审核';
               $crkitem['审核人']=cookie('username');
               $crkitem['审核日期']=date('Y-m-d',time());
               M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->save($crkitem);
               $this->writeLog($crk['ID'],$crk['单据编号'],'出库反审核',$crk['单据类别'].'出库反审核');
               echo '反审核通过';
               exit;
           }
           else{
               foreach($crkmx as $item){
                   $num=$item['数量'];
                   $code=$item['编号'];
                   $ck=$item['仓库'];
                   $price=$item['单价'];
                   if($crk['引用类别']=='维修退料'){
                       $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                       $data['已退料数量']=$pj['已退料数量']-$num;
                       $data['已领料数量']=$pj['已领料数量']+$num;
                       M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                   }
                   M('配件目录','dbo.','difo')->execute("update 配件目录 set 库存=库存-$num,最新进价=$price where 编号='$code'");
                   M('配件仓位','dbo.','difo')->execute("update 配件仓位 set 库存=库存-$num where 编号='$code' and 仓库='$ck'");
               }
               $crkitem['当前状态']='待审核';
               $crkitem['审核人']=cookie('username');
               $crkitem['审核日期']=date('Y-m-d',time());
               M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->save($crkitem);
               $this->writeLog($crk['ID'],$crk['单据编号'],'入库反审核','采购入库反审核');
               echo '反审核通过';
               exit;
           }

       }
       else{
           $this->display();
       }

   }
   public function deletepick(){
       if(IS_POST){
           $crk=$_POST['crk'];
           if($crk['单据类别']=='出库'){
               if($crk['引用类别']=='维修领料'){
                   $crkmx=M('出入库明细','dbo.','difo')->where(array('ID'=>$crk['ID']))->select();
                   foreach($crkmx as $item){
                       $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                       $data['待审核数量']=$pj['待审核数量']-$item['数量'];
                       M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                   }
               }
           }else{
               if($crk['引用类别']=='维修退料'){
                   $crkmx=M('出入库明细','dbo.','difo')->where(array('ID'=>$crk['ID']))->select();
                   foreach($crkmx as $item){
                       $pj=M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->find();
                       $data['已退料数量']=$pj['已退料数量']-$item['数量'];
                       M('维修配件','dbo.','difo')->where(array('ID'=>$crk['引用ID'],'编号'=>$item['编号']))->save($data);
                   }
               }
           }
           M('出入库单','dbo.','difo')->where(array('流水号'=>$crk['流水号']))->delete(); 
           M('出入库明细','dbo.','difo')->where(array('ID'=>$crk['ID']))->delete();
           echo '删除成功';
           exit;

       }
       else{
           $this->display(); 
       }

   }
   public function getpickdetail(){
       $id=$_POST['id'];
       $carinfo=M('出入库明细','dbo.','difo')->where(array('ID'=>$id))->select();
       $data['Rows']=$carinfo;
       $data['Total']=count($carinfo);
       echo json_encode($data);
   }
   public function backpick(){
     if(IS_POST){
         $wxinfo=$_POST['wxinfo'];
         $form=$_POST['data'];
         $wxID=$wxinfo['ID'];
         $products=$_POST['products'];
         $data['ID']=$this->getcode(20,1,1);
         $data['引用单号']=$wxinfo['业务编号'];
         $data['引用ID']=$wxID;
         $data['引用类别']='维修退料';
         $data['单据编号']=$this->getcodenum('RK');
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['车牌号码']=$wxinfo['车牌号码'];
         $data['当前状态']='待审核';
         $data['原因']='维修退料';
         $data['领料员']=$form['退料员'];
         $data['单据类别']='入库';
         $data['单据备注']=$form['备注'];
        if($form['备注']==''){
            $data['单据备注']='维修退料';
        }
        M('出入库单','dbo.','difo')->add($data);
        foreach($products as $product){
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=$product['本次退料'];
            $crk['单价']=$product['单价'];
            $crk['金额']=$product['金额'];
            $crk['成本价']=$product['成本价'];
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            $num=$product['本次退料'];
            $code=$product['编号'];
            M('出入库明细','dbo.','difo')->add($crk);
            M('维修配件','dbo.','difo')->execute("UPDATE 维修配件 SET 已退料数量=isnull(已退料数量,0)+$num WHERE ID='$wxID'and 编号='$code'");
           
        }
        echo '退料成功';
    }
     else{ 

         $this->display();
     }
   }
   private function sumpurchaseprice($id){
       $products=M('采购明细','dbo.','difo')->where(array('ID'=>$id))->select();
       foreach($products as $product){
           $data['合计货款']+=$product['金额'];
           $data['合计数量']+=$product['数量'];
           $data['合计税额']+=$product['税额'];
           $data['价税合计']=$product['金额']+=$product['税额'];
           $data['总金额']+=$data['价税合计'];
           $data['应结金额']+=$data['价税合计'];
           $data['挂账金额']+=$data['价税合计'];
       }
       M('采购单','dbo.','difo')->where(array('ID'=>$id))->save($data);
   }
   private function sumsaleprice($id){
       $products=M('销售明细','dbo.','difo')->where(array('ID'=>$id))->select();
       if($products){
           foreach($products as $product){
               $data['实际货款']+=$product['金额'];
               $data['合计数量']+=$product['数量'];
               $data['实际税额']+=$product['税额'];
               $data['价税合计']+=$product['金额']+$product['税额'];
               $data['总金额']+=$product['金额']+$product['税额'];
               $data['应结金额']+=$product['价税合计'];
               $data['挂账金额']=$data['总金额'];
           }
       }else{
           $data['实际货款']=0;
           $data['合计数量']=0;
           $data['实际税额']=0;
           $data['价税合计']=0;
           $data['总金额']=0;
           $data['应结金额']=0;
           $data['挂账金额']=0;
       }
       M('销售单','dbo.','difo')->where(array('ID'=>$id))->save($data);

   }
   public function deletesaleproduct(){
       if(IS_POST){
           $xsd=$_POST['xsd'];
           M('销售明细','dbo.','difo')->where(array('ID'=>$xsd['ID']))->delete();
           M('销售单','dbo.','difo')->where(array('流水号'=>$xsd['流水号']))->delete();
           echo '删除成功';
           exit;
       }
       
   }
   public function deletepurchaseitem(){
       $ID=$_POST['ID'];
       $code=$_POST['code'];
       M('采购明细','dbo.','difo')->where(array('流水号'=>$code))->delete();
       $this->sumpurchaseprice($ID);
       echo '操作成功';
   }
   public function deletesaleitem(){
       $ID=$_POST['ID'];
       $code=$_POST['code'];
       M('销售明细','dbo.','difo')->where(array('流水号'=>$code))->delete();
       $this->sumsaleprice($ID);
       echo '操作成功';
   }
   public function savesaleproduct(){
       $products=$_POST['products'];
       $ID=$_POST['ID'];
       foreach($products as $product){
           $crk['ID']=$ID;
           $crk['仓库']=$product['仓库'];
           $crk['编号']=$product['编号'];
           $crk['名称']=$product['名称'];
           $crk['规格']=$product['规格'];
           $crk['单位']=$product['单位'];
           $crk['数量']=$product['数量'];
           $crk['单价']=$product['单价'];
           $crk['成本价']=$product['成本价'];
           $crk['金额']=$product['金额'];
           $crk['折扣']=$product['折扣'];
           $crk['税率']=$product['税率'];
           $crk['税额']=$product['税额'];
           $crk['虚增类别']=$product['虚增类别'];
           $crk['虚增金额']=$product['虚增金额'];
           $crk['适用车型']=$product['适用车型'];
           $crk['产地']=$product['产地'];
           $crk['备注']=$product['备注'];
           M('销售明细','dbo.','difo')->add($crk);
       }
       $this->sumsaleprice($ID);
       echo '操作成功';
   }
   public function savepurchase(){
       $products=$_POST['products'];
       $ID=$_POST['ID'];
       $cgd=M('采购单','dbo.','difo')->where(array('ID'=>$ID))->find();
       foreach($products as $product){
           $crk['ID']=$ID;
           $crk['仓库']=$product['仓库'];
           $crk['编号']=$product['编号'];
           $crk['名称']=$product['名称'];
           $crk['规格']=$product['规格'];
           $crk['单位']=$product['单位'];
           $crk['数量']=$product['数量'];
           $crk['单价']=$product['单价'];
           $crk['金额']=$product['金额'];
           $crk['折扣']=$product['折扣'];
           $crk['税率']=$product['税率'];
           $crk['税额']=$product['税额'];
           $crk['含税价']=$product['含税价'];
           $crk['价税合计']=$product['价税合计'];
           $crk['适用车型']=$product['适用车型'];
           $crk['产地']=$product['产地'];
           $crk['备注']=$product['备注'];
           M('采购明细','dbo.','difo')->add($crk);
           if($cgd['引用单号']!='')
           {
               $code=$product['编号'];
               $price=$product['单价'];
               $wxID=$cgd['引用ID'];
               M('维修配件','dbo.','difo')->execute("UPDATE 维修配件 SET 成本价=$price WHERE  编号='$code' and ID='$wxID'");
           }
       }
       $this->sumpurchaseprice($ID);
       echo '操作成功';
   }
   public function  purchasing(){
     if(IS_POST){
         $wxinfo=$_POST['wxinfo'];
         $form=$_POST['data'];
         $products=$_POST['products'];

         $data['门店']=$form['门店'];
         $data['发票号码']=$form['发票号码'];
         if($wxinfo){
             $data['引用ID']=$wxinfo['ID'];
             $data['引用单号']=$wxinfo['业务编号'];
             $data['车牌号码']=$wxinfo['车牌号码'];
             $data['发票号码']=$wxinfo['车牌号码'];
             $data['门店']=$wxinfo['门店'];
         }
         $data['ID']=$this->getcode(20,1,1);
         $data['单据编号']=$this->getcodenum('PK');
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['供应商']=$form['供应商'];
         $data['供应商ID']=$form['供应商ID'];
         $data['发票类别']=$form['发票类别'];
         $data['运费']=$form['运费'];
         $data['结算方式']=$form['结算方式'];
         $data['货运方式']=$form['货运方式'];
         $data['业务员']=$form['业务员'];
         $data['整单折扣']=1;
         $data['送货地址']=$form['送货地址'];
         $data['付款日期']=$form['付款日期'];
         $data['当前状态']='待审核';
         if(!isset($data['车牌号码'])){
             $data['车牌号码']=$form['发票号码'];
         }
         $data['合计货款']=$form['合计货款'];
         $data['合计数量']=$form['合计数量'];
         $data['合计税额']=$form['合计税额'];
         $data['价税合计']=$form['价税合计'];
         $data['总金额']=$form['总金额'];
         $data['单据类别']='采购进货';
         //$data['引用类别']='维修领料';
         //$data['急件']=$form['急件'];
         $data['应结金额']=$form['总金额'];
         $data['现结金额']=0;
         $data['挂账金额']=$form['总金额'];
         $data['备注']=$form['备注'];
        if($form['备注']==''){
            $data['备注']='维修采购';
        }
        M('采购单','dbo.','difo')->add($data);
        foreach($products as $product){
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=$product['数量'];
            $crk['单价']=$product['单价'];
            $crk['金额']=$product['金额'];
            $crk['折扣']=$product['折扣'];
            $crk['税率']=$product['税率'];
            $crk['税额']=$product['税额'];
            $crk['含税价']=$product['含税价'];
            $crk['价税合计']=$product['价税合计'];
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            if($wxinfo){
                $crk['车牌号码']=$wxinfo['车牌号码'];
                $crk['车型']=$wxinfo['车型'];
                $crk['品牌']=$wxinfo['品牌'];
                $crk['排量']=$wxinfo['排量'];
                $crk['年份']=$wxinfo['年份'];
                $code=$crk['编号'];
                $wxID=$wxinfo['ID'];
                $price=$product['金额'];
                M('维修配件','dbo.','difo')->execute("UPDATE 维修配件 SET 是否采购=1,成本价=$price WHERE  编号='$code' and ID='$wxID'");
            }
            M('采购明细','dbo.','difo')->add($crk);
            //M('配件目录','dbo.','difo')->execute("UPDATE 配件目录 SET 维修领用=维修领用+$num WHERE 编号='$code'");
           
        }
        echo '开单成功，转入采购审核中';
    }
     else{ 

         $this->display();
     }
   }
   public function modifysaling(){
      if(IS_POST){
       $form=$_POST['data'];
       $id=$_POST['ID'];
       M('销售单','dbo.','difo')->where(array('ID'=>$id))->save($form);
       echo '保存成功';
      }else{
         $this->display();
      }
   }
   public function saling(){
     if(IS_POST){
         //$wxinfo=$_POST['wxinfo'];
         $form=$_POST['data'];
         $products=$_POST['products'];
         $data['ID']=$this->getcode(20,1,1);
         $data['单据编号']=$this->getcodenum('SS');
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['客户名称']=$form['客户名称'];
         $data['销售类别']=$form['销售类别'];
         $data['客户ID']=$form['客户ID'];
         $data['发票类别']=$form['发票类别'];
         $data['发票号码']=$form['发票号码'];
         $data['运费']=$form['运费'];
         $data['结算方式']=$form['结算方式'];
         $data['货运方式']=$form['货运方式'];
         $data['业务员']=$form['业务员'];
         $data['整单折扣']=1;
         $data['送货地址']=$form['送货地址'];
         $data['收款日期']=$form['收款日期'];
         $data['当前状态']='待审核';
         $data['实际货款']=$form['实际货款'];
         $data['合计数量']=$form['合计数量'];
         $data['实际税额']=$form['实际税额'];
         $data['价税合计']=$form['价税合计'];
         $data['总金额']=$form['总金额'];
         $data['单据类别']='销售出库';
         $data['门店']=$form['门店'];
         $data['车牌号码']=$form['车牌号码'];
         //$data['引用ID']=$wxID;
         //$data['引用类别']='维修领料';
         //$data['引用单号']=$wxinfo['业务编号'];
         //$data['急件']=$form['急件'];
         $data['应结金额']=$form['总金额'];
         $data['现结金额']=0;
         $data['挂账金额']=0;
         //$data['车牌号码']=$wxinfo['车牌号码'];
         //$data['原因']='维修领料';
         $data['备注']=$form['备注'];
        if($form['备注']==''){
            $data['备注']='销售开单';
        }
        M('销售单','dbo.','difo')->add($data);
        foreach($products as $product){
            $crk['ID']=$data['ID'];
            $crk['仓库']=$product['仓库'];
            $crk['编号']=$product['编号'];
            $crk['名称']=$product['名称'];
            $crk['规格']=$product['规格'];
            $crk['单位']=$product['单位'];
            $crk['数量']=$product['数量'];
            $crk['单价']=$product['单价'];
            $crk['成本价']=$product['成本价'];
            $crk['金额']=$product['金额'];
            $crk['折扣']=$product['折扣'];
            $crk['税率']=$product['税率'];
            $crk['税额']=$product['税额'];
            $crk['虚增类别']=$product['虚增类别'];
            $crk['虚增金额']=$product['虚增金额'];
            $crk['适用车型']=$product['适用车型'];
            $crk['产地']=$product['产地'];
            $crk['备注']=$product['备注'];
            M('销售明细','dbo.','difo')->add($crk);
           
           
        }
        echo '开单成功';
    }
     else{ 

         $this->display();
     }
   }
   public function salebillcheck(){
       if(IS_POST){
           $xsd=$_POST['xsd'];
           $record=M('销售单','dbo.','difo')->where(array('流水号'=>$xsd['流水号']))->find();
           if($record['当前状态']=='已审核'){
               echo '此单据已经审核';
               exit;
           }
           $xsmx=$_POST['xsdmx'];
           if($xsd['单据类别']=='销售出库'){
               $sellbill['当前状态']='已审核';
               $sellbill['审核人']=cookie('username');
               $sellbill['审核日期']=date('Y-m-d',time());
               $sellbill['应结金额']=$xsd['应结金额'];
               $sellbill['现结金额']=$xsd['现结金额'];
               $sellbill['挂账金额']=$xsd['挂账金额'];
               $sellbill['优惠金额']=$xsd['优惠金额'];
               M('销售单','dbo.','difo')->where(array('流水号'=>$xsd['流水号']))->save($sellbill);

               $paybill['ID']=$this->getcode(18,1,1);
               $paybill['单位编号']=$xsd['客户ID'];
               $paybill['单位名称']=$xsd['客户名称'];
               $paybill['单据类别']='销售出库';
               $paybill['单据编号']=$xsd['单据编号'];
               $paybill['引用ID']=$xsd['ID'];
               $paybill['制单日期']=date('Y-m-d',time());
               $paybill['制单人']=cookie('username');
               $paybill['总金额']=$xsd['总金额'];
               $paybill['已结算金额']=$xsd['现结金额'];
               $paybill['未结算金额']=$xsd['挂账金额'];
               $paybill['本次结算']=$xsd['现结金额'];
               $paybill['提醒日期']=date('Y-m-d',time());
               $paybill['账款类别']='应收款';
               $paybill['当前状态']='待审核';
               if($xsd['挂账金额']==0){
                   $paybill['当前状态']='已审核';
               }
               $paybill['审核人']=cookie('username');
               $paybill['审核日期']=date('Y-m-d',time());
               $paybill['摘要']='销售出库';
               $paybill['虚增价税']=0;
               $paybill['挂账金额']=$xsd['挂账金额'];
               $paybill['车牌号码']=$xsd['车牌号码'];
               $paybill['门店']=$xsd['门店'];
               M('应收应付单','dbo.','difo')->add($paybill);

               if(doubleval($xsd['现结金额'])>0||$xsd['结算方式']=='会员卡支付'){
                   $inout['单据编号']=$this->getcodenum('BI');
                   $inout['制单日期']=date('Y-m-d',time());
                   $inout['制单人']=cookie('username');
                   $inout['单位名称']=$xsd['客户名称'];
                   $inout['账款类别']='收款单';
                   $inout['实收金额']=$xsd['现结金额'];
                   $inout['折扣金额']=0;
                   $inout['结算方式']=$xsd['结算方式'];
                   $inout['结算账户']=$xsd['门店'];
                   $inout['摘要']='销售出库收款('.$xsd['单据编号'].')';
                   $inout['收支项目']='销售出库';
                   $inout['当前状态']='待审核';
                   $inout['发票类别']=$xsd['门店'];
                   $inout['发票号']=$xsd['车牌号码'];
                   $inout['ID']=$this->getcode(18,1,1);
                   $inout['单位编号']=$xsd['客户ID'];
                   $inout['本次冲账']=$xsd['现结金额'];
                   $inout['单据类别']='应收款';
                   $inout['取用预存']=0;
                   M('日常收支','dbo.','difo')->add($inout);
                   
                   $dj['挂账ID']=$paybill['ID'];
                   $dj['收支ID']=$inout['ID'];
                   $dj['金额']=$xsd['现结金额'];
                   M('引用单据','dbo.','difo')->add($dj);
               }
               $crkitem['ID']=$this->getcode(20,1,1);
               $crkitem['引用单号']=$xsd['单据编号'];
               $crkitem['引用ID']=$xsd['ID'];
               $crkitem['引用类别']='销售出库';
               $crkitem['单据编号']=$this->getcodenum('CK');
               $crkitem['制单日期']=date('Y-m-d',time());
               $crkitem['门店']=$xsd['门店'];
               $crkitem['制单人']=cookie('username');
               $crkitem['车牌号码']=$xsd['车牌号码'];
               $crkitem['当前状态']='待审核';
               $crkitem['原因']='销售出库';
               $crkitem['领料员']=cookie('username');
               $crkitem['单据类别']='出库';
               $crkitem['单据备注']='销售出库';
               M('出入库单','dbo.','difo')->add($crkitem);
               foreach($xsmx as $product){
                   $crk['ID']=$crkitem['ID'];
                   $crk['仓库']=$product['仓库'];
                   $crk['编号']=$product['编号'];
                   $crk['名称']=$product['名称'];
                   $crk['规格']=$product['规格'];
                   $crk['单位']=$product['单位'];
                   $crk['数量']=$product['数量'];
                   $crk['门店']=$xsd['门店'];
                   $crk['单价']=$product['单价'];
                   $crk['金额']=$product['金额'];
                   $crk['成本价']=$product['成本价'];
                   $crk['适用车型']=$product['适用车型'];
                   $crk['产地']=$product['产地'];
                   $crk['备注']=$product['备注'];
                   M('出入库明细','dbo.','difo')->add($crk);
               }
               $this->writeLog($xsd['ID'],$xsd['单据编号'],'销售审核','销售审核');

               echo '审核通过';
               exit;
           }
           else{
               
           }

       }else{
           $this->display();
       }
   }
   public function unsalebillcheck(){
       if(IS_POST){
           $xsd=$_POST['xsd'];
           $record=M('销售单','dbo.','difo')->where(array('流水号'=>$xsd['流水号']))->find();
           if($record['当前状态']=='待审核'){
               echo '此单据已经反审核';
               exit;
           }
           if($xsd['单据类别']=='销售出库'){
               $crk=M('出入库单','dbo.','difo')->where(array('引用单号'=>$xsd['单据编号']))->find();
               if($crk&&$crk['当前状态']=='已审核'){
                   echo '请先将单号为'.$crk['单据编号'].'出入库单反审核';
                   exit;
               }
               $data['当前状态']='待审核';
               M('销售单','dbo.','difo')->where(array('流水号'=>$xsd['流水号']))->save($data);
               $ys=M('应收应付单','dbo.','difo')->where(array('单据编号'=>$xsd['单据编号']))->find();
               $dj=M('引用单据','dbo.','difo')->where(array('挂账ID'=>$ys['ID']))->find();
               M('应收应付单','dbo.','difo')->where(array('ID'=>$ys['ID']))->delete();
               M('日常收支','dbo.','difo')->where(array('ID'=>$dj['收支ID']))->delete();
               M('应收应付单','dbo.','difo')->where(array('单据编号'=>$xsd['单据编号']))->delete();
               $this->writeLog($xsd['引用ID'],$xsd['引用单号'],'销售反审核','销售反审核');
               echo '反审核通过';
               exit;
           }
        
       }
   }
   public function maintenance()
   {  
       $this->display();
   }

   public function washcarmanage()
   {  
       $list=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%美容%')))->select();
       $this->assign('list',$list);
       $this->display();
   }
   public function completework()
    {  
       if(IS_POST){
           $itemid=$_POST['itemid'];
           $type=$_POST['type'];
           $wxinfo=M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->find();
           M('维修项目','dbo.','difo')->where(array('ID'=>$wxinfo['ID']))->save(array('完工时间'=>date('Y-m-d H:i',time())));
           $wxdata['当前状态']='结算'; 
           $wxdata['实际完工']=date('Y-m-d H:i',time());
           $wxdata['已处理']=intval((time()-strtotime($wxinfo['开工时间']))/60);
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($wxdata);
           if($type=='baoyang'){
               $wxID=$wxinfo['ID'];
               $data['ID']=$this->getcode(20,1,1);
               $data['引用单号']=$wxinfo['业务编号'];
               $data['引用ID']=$wxID;
               $data['引用类别']='维修领料';
               $data['单据编号']=$this->getcodenum('CK');
               $data['制单日期']=date('Y-m-d',time());
               $data['制单人']=cookie('username');
               $data['车牌号码']=$wxinfo['车牌号码'];
               $data['当前状态']='已审核';
               $data['门店']=$wxinfo['门店'];
               $data['审核人']='系统自动';
               $data['审核日期']=date('Y-m-d',time());
               $data['原因']='维修领料';
               $data['领料员']=$wxinfo['接车人'];
               $data['单据类别']='出库';
               $data['单据备注']='保养自动出库';
               M('出入库单','dbo.','difo')->add($data);
               $products=M('维修配件','dbo.','difo')->where(array('ID'=>$wxID))->select();
               foreach($products as $product){
                   $crk['ID']=$data['ID'];
                   $crk['仓库']=$product['仓库'];
                   $crk['编号']=$product['编号'];
                   $crk['名称']=$product['名称'];
                   $crk['规格']=$product['规格'];
                   $crk['单位']=$product['单位'];
                   $crk['门店']=$wxinfo['门店'];
                   $crk['数量']=$product['数量'];
                   $crk['单价']=$product['单价'];
                   $crk['金额']=$product['金额'];
                   $crk['成本价']=$product['成本价'];
                   $crk['适用车型']=$product['适用车型'];
                   $crk['产地']=$product['产地'];
                   $crk['备注']=$product['备注'];
                   $num=$product['数量'];
                   $code=$product['编号'];
                   $ck=$product['仓库'];
                   M('出入库明细','dbo.','difo')->add($crk);
                   M('维修配件','dbo.','difo')->execute("UPDATE 维修配件 SET 已领料数量=$num WHERE ID='$wxID'and 编号='$code'");
                   M('配件目录','dbo.','difo')->execute("update 配件目录 set 库存=库存-$num where 编号='$code'");
                   M('配件仓位','dbo.','difo')->execute("update 配件仓位 set 库存=库存-$num where 编号='$code' and 仓库='$ck'");
                   
               }
           }
           $user=M('member_card_car')->where(array('carno'=>$wxinfo['车牌号码']))->find();
           $model  = new templateNews();
           $dataKey    = 'TM151213';
           $dataArr    = array(
               'first'         => '尊敬的车主，您的爱车已经维修完毕。', 
               'keyword1'      =>$wxinfo['车牌号码'],//车牌号
               'keyword2'      => date('Y-m-d H:i',time()),//完工时间
               'keyword3'      => $wxinfo['接车人'],//接车人与联系电话
               'keyword4'      => $wxinfo['应收金额'].'元',//维修费用
               'wecha_id'      => $user['wecha_id'],
               'remark'        => '请您安排时间到店取车，结算请点击详情。',
               'url'           => U('Wap/Store/newcheck',array('token'=>$this->token,'wecha_id'=>$user['wecha_id']),true,false,true),
           );
           $model->sendTempMsg($dataKey,$dataArr);
           echo '更新完成';
           exit;
       }
    }
   public function submitcarkey()
    {  
       if(IS_POST){
           $itemid=$_POST['itemid'];
           $lb=$_POST['lb'];
           $data['钥匙编号']=$_POST['keynum']; 
           $data['上交钥匙']=date('Y-m-d H:i',time());
           if($lb=='蜡水洗车'){
             $data['预计完工']=date('Y-m-d H:i',time()+60*60);
           }elseif($lb=='汽车美容'){
             $data['预计完工']=date('Y-m-d H:i',time()+3*60*60);
           }elseif($lb=='普通快修'){
             $data['预计完工']=date('Y-m-d H:i',time()+2*60*60);
           }
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
           echo '更新完成';
           exit;
       }
    }
   public function cancelwash()
    {  
       if(IS_POST){
           $itemid=$_POST['itemid'];
           $data['当前状态']='取消'; 
           $data['维修状态']='取消';
           $data['取消原因']='超过规定时间未交钥匙自动取消';
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
           echo '更新完成';
           exit;
       }
    }
   public function maintenancerecord()
    {  
         $this->display();
    }
   public function clxx()
    {   
            $parms=$_GET;
            if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
                $searchkey='%'.trim($_GET['searchkey']).'%';
            }
            $where['车牌号码']=array('neq','0000');
            if($searchkey){       
                $searchwhere['品牌']=array('like',$searchkey);
                $searchwhere['轮胎规格']=array('like',$searchkey);
                $searchwhere['车型']=array('like',$searchkey);
                $searchwhere['运输证号']=array('like',$searchkey);
                $searchwhere['车架号']=array('like',$searchkey);
                $searchwhere['机油格']=array('like',$searchkey);
                $searchwhere['空气格']=array('like',$searchkey);
                $searchwhere['冷气格']=array('like',$searchkey);
                $searchwhere['汽油格']=array('like',$searchkey);
                $searchwhere['车主']=array('like',$searchkey);
                $searchwhere['车牌号码']=array('like',$searchkey);
                $searchwhere['客户类别']=array('like',$searchkey);
                $searchwhere['联系人']=array('like',$searchkey);
                $searchwhere['联系电话']=array('like',$searchkey);
                $searchwhere['保险公司']=array('like',$searchkey);
                $searchwhere['发动机号']=array('like',$searchkey);
                $searchwhere['_logic']='OR';
                $where['_complex']=$searchwhere;
                
            }
            $count=M('车辆档案','dbo.','difo')->where($where)->count();
            $Page = new Page($count,15,$parms);
            $show = $Page->show();
            $yelist=M('车辆档案','dbo.','difo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('流水号 desc')->select();
            $this->assign('page',$show);
            $this->assign('count',$count);
            $this->assign('yelist',$yelist);
            $this->display();
        
    }
   public function comment()
    {   
            $parms=$_GET;
            $where['是否评论']='是';
            if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
                $searchkey='%'.trim($_GET['searchkey']).'%';
            }
            if($_GET['lb']&&trim($_GET['lb'])!='')
            {
                $where['维修类别']=trim($_GET['lb']);
                
            }
            if($_GET['startDate']&&trim($_GET['startDate'])!='')
            {
                $where['制单日期']=array('egt',trim($_GET['startDate']));
                
            }
            if($_GET['endDate']&&trim($_GET['endDate'])!='')
            {
                $where['制单日期']=array('elt',trim($_GET['endDate']));
                
            }
            if(trim($_GET['startDate'])!=''&&trim($_GET['endDate'])!='')
            {
                $where['制单日期']=array('BETWEEN',array(trim($_GET['startDate']),trim($_GET['endDate'])));
                
            }
            $where['车牌号码']=array('neq','0000');
            if($searchkey){       
                $searchwhere['品牌']=array('like',$searchkey);
                $searchwhere['运输证号']=array('like',$searchkey);
                $searchwhere['车架号']=array('like',$searchkey);
                $searchwhere['车主']=array('like',$searchkey);
                $searchwhere['车牌号码']=array('like',$searchkey);
                $searchwhere['客户类别']=array('like',$searchkey);
                $searchwhere['联系人']=array('like',$searchkey);
                $searchwhere['联系电话']=array('like',$searchkey);
                $searchwhere['保险公司']=array('like',$searchkey);
                $searchwhere['发动机号']=array('like',$searchkey);
                $searchwhere['_logic']='OR';
                $where['_complex']=$searchwhere;
                
            }
            $count=M('客户评价','dbo.','difo')->where($where)->count();
            $Page = new Page($count,15,$parms);
            $show = $Page->show();
            $yelist=M('客户评价','dbo.','difo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('流水号 desc')->select();
            $this->assign('page',$show);
            $this->assign('count',$count);
            $this->assign('yelist',$yelist);
            $this->display();
        
    }

   public function printbill(){
       $id=$_GET['ID'];
        $wxrecord=M('维修','dbo.','difo')->where(array('ID'=>$id))->find(); 
        $items=M('维修项目','dbo.','difo')->where(array('ID'=>$id))->select();
        $peijian=M('维修配件','dbo.','difo')->where(array('ID'=>$id,'仅内部核算成本'=>0))->select();
        $this->assign('wxrecord',$wxrecord);
        $this->assign('items',$items);
        $this->assign('peijian',$peijian);
        $this->display();
   }
   public function printsellbill(){
       $id=$_GET['ID'];
        $wxrecord=M('销售单','dbo.','difo')->where(array('ID'=>$id))->find();
        $userinfo=M('往来单位','dbo.','difo')->where(array('ID'=>$wxrecord['客户ID']))->find();
        $items=M('销售明细','dbo.','difo')->where(array('ID'=>$id))->select();
        $this->assign('xsrecord',$wxrecord);
        $this->assign('items',$items);
        $this->assign('userinfo',$userinfo);
        $this->display();
   }
    public function printbalancebill(){
       $id=$_GET['ID'];
        $items=M('日常收支','dbo.','difo')->where(array('ID'=>$id))->find();
        $userinfo=M('往来单位','dbo.','difo')->where(array('ID'=>$items['单位编号']))->find();
        $this->assign('record',$items);
        $this->assign('userinfo',$userinfo);
        $this->display();
   }
  public function printdbbill(){
       $id=$_GET['ID'];
        $wxrecord=M('车辆代办','dbo.','difo')->where(array('ID'=>$id))->find();
        $userinfo=M('往来单位','dbo.','difo')->where(array('ID'=>$wxrecord['客户ID']))->find();
        $this->assign('xsrecord',$wxrecord);
        $this->assign('userinfo',$userinfo);
        $this->display();
   }
   public function printbxbill(){
       $id=$_GET['ID'];
        $wxrecord=M('车辆保险','dbo.','difo')->where(array('ID'=>$id))->find();
        $userinfo=M('往来单位','dbo.','difo')->where(array('ID'=>$wxrecord['客户ID']))->find();
        $this->assign('xsrecord',$wxrecord);
        $this->assign('userinfo',$userinfo);
        $this->display();
   }
   public function printpurchasebill(){
       $id=$_GET['ID'];
        $wxrecord=M('采购单','dbo.','difo')->where(array('ID'=>$id))->find();
        $userinfo=M('往来单位','dbo.','difo')->where(array('ID'=>$wxrecord['供应商ID']))->find();
        $items=M('采购明细','dbo.','difo')->where(array('ID'=>$id))->select();
        $this->assign('cgrecord',$wxrecord);
        $this->assign('userinfo',$userinfo);
        $this->assign('items',$items);
        $this->display();
   }
   public function purchase()
    {
         $parms=$_GET;
         if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
             $searchkey='%'.trim($_GET['searchkey']).'%';
         }
         if($_GET['lb']&&trim($_GET['lb'])!='')
         {
             $where['维修类别']=trim($_GET['lb']);
             
         }
         if($_GET['startDate']&&trim($_GET['startDate'])!='')
         {
             $where['日期']=array('egt',trim($_GET['startDate']));
             
         }
         if($_GET['endDate']&&trim($_GET['endDate'])!='')
         {
             $where['日期']=array('elt',trim($_GET['endDate']));
             
         }
         if(trim($_GET['startDate'])!=''&&trim($_GET['endDate'])!='')
         {
             $where['日期']=array('BETWEEN',array(trim($_GET['startDate']),trim($_GET['endDate'])));
             
         }
         if($_GET['zhidan']&&trim($_GET['zhidan'])!='')
         {
             $where['制单人']=trim($_GET['zhidan']);
             
         }
         if($searchkey){       
             $searchwhere['制单人']=array('like',$searchkey);
             $searchwhere['维修类别']=array('like',$searchkey);
             $searchwhere['_logic']='OR';
             $where['_complex']=$searchwhere;

         }
         $zdlist=M('员工目录','dbo.','difo')->where(array('班组'=>array('in',array('录单采购','区府店前台收银','塘坑店前台收银'))))->select();

         $count=M('采购录单业绩表','dbo.','difo')->where($where)->count();
         $Page = new Page($count,15,$parms);
         $show = $Page->show();
         $yelist=M('采购录单业绩表','dbo.','difo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('日期 desc')->select();
         $this->assign('page',$show);
         $this->assign('count',$count);
         $this->assign('zdlist',$zdlist);
         $this->assign('yelist',$yelist);
         $this->display();
    }
   public function month()
    {
         $parms=$_GET;
         $count=M('维修月营业表','dbo.','difo')->count();
         $Page = new Page($count,15,$parms);
         $show = $Page->show();
         $yelist=M('维修月营业表','dbo.','difo')->limit($Page->firstRow.','.$Page->listRows)->order('日期 desc')->select();
         $this->assign('page',$show);
         $this->assign('count',$count);
         $this->assign('yelist',$yelist);
         $this->display();
    }
   public function persons()
    {
         $this->display();
    }
   public function fwgw()
    {
         $parms=$_GET;
         if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
             $searchkey='%'.trim($_GET['searchkey']).'%';
         }
         if($_GET['lb']&&trim($_GET['lb'])!='')
         {
             $where['维修类别']=trim($_GET['lb']);
             
         }
         if($_GET['startDate']&&trim($_GET['startDate'])!='')
         {
             $where['日期']=array('egt',trim($_GET['startDate']));
             
         }
         if($_GET['bm']&&trim($_GET['bm'])!='')
         {
             $where['班组']=array('like',trim($_GET['bm']).'%');
             
         }
         if($_GET['endDate']&&trim($_GET['endDate'])!='')
         {
             $where['日期']=array('elt',trim($_GET['endDate']));
             
         }
         if(trim($_GET['startDate'])!=''&&trim($_GET['endDate'])!='')
         {
             $where['日期']=array('BETWEEN',array(trim($_GET['startDate']),trim($_GET['endDate'])));
             
         }
         if($_GET['zhuxiu']&&trim($_GET['zhuxiu'])!='')
         {
             $where['接车人']=trim($_GET['zhuxiu']);
             
         }
         if($searchkey){       
             $searchwhere['维修类别']=array('like',$searchkey);
             $searchwhere['接车人']=array('like',$searchkey);
             $searchwhere['_logic']='OR';
             $where['_complex']=$searchwhere;

         }
         $count=M('服务顾问业绩表','dbo.','difo')->join('员工目录 on 服务顾问业绩表.接车人=员工目录.姓名')->where($where)->count();
         $Page = new Page($count,15,$parms);
         $show = $Page->show();
         
         $zxlist=M('员工目录','dbo.','difo')->where(array('部门'=>array('in',array('业务部','门店行政'))))->order('姓名')->select();
         $yelist=M('服务顾问业绩表','dbo.','difo')->join('员工目录 on 服务顾问业绩表.接车人=员工目录.姓名')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('日期 desc')->select();
         $this->assign('page',$show);
         $this->assign('count',$count);
         $this->assign('yelist',$yelist);
         $this->assign('zxlist',$zxlist);
         $this->display();
    }
   public function washcar(){
       if(IS_POST)
       {
           $wxinfo=$_POST['wxinfo'];
           $carno=$wxinfo['车牌号码'];
           $money=$wxinfo['money'];
           $data=M('维修','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
           $data['流水号']=null;
           unset( $data['流水号']);
           unset($data['ROW_NUMBER']);
           $bianhao=$this->getcodenum('WX');
           $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
           if($carinfo)
           {
               $data['车牌号码']=$carno;
               $data['送修人']=$carinfo['手机号码'];
               foreach($data as $key=>$value){
                   $data[$key]=$carinfo[$key];
               }
               $this->MessageTip($carinfo,$wxinfo['门店'],'蜡水洗车');              
           }
           else{
               $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
               foreach($data as $key=>$value){
                   $data[$key]=$carinfo[$key];
               }
               $data['车牌号码']='0000';
               $data['客户类别']='临时客户';
               $data['联系人']=$carno;
               $data['送修人']=$carno;
               $data['联系电话']='';
           }
           
           //$yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$wxinfo['主修人']))->find();
           $data['接车人']=$wxinfo['接车人'];         
           $data['ID']=$this->getcode(18,0,1);
           $data['制单日期']=date('Y-m-d',time());
           $data['制单人']=cookie('username');
           $data['维修类别']='蜡水洗车';
           $data['保修类别']='保外';
           $data['单据类别']='快修单';
           //$data['当前主修人']=$wxinfo['主修人'];
           //$data['主修人']=$wxinfo['主修人'];
           $data['门店']=$wxinfo['门店'];
           $data['结算客户']=$carinfo['车主'];;
           $data['结算客户ID']=$carinfo['客户ID'];
           //$data['送修人电话']=$carinfo['手机号码'];
           $data['当前状态']='报价';
           $data['维修状态']='报价';
           $data['预计完工']=date('Y-m-d',time());
           $data['进厂时间']=date('Y-m-d H:i',time());
           unset($data['出厂时间']);
           unset($data['开工时间']);
           unset($data['实际完工']);
           unset($data['购买日期']);
           unset($data['结束日期']);
           unset($data['预计完工']);
           unset($data['结算日期']);
           unset($data['上交钥匙']);
           unset($data['完工时间']);
           unset($data['评价时间']);
           $data['报价金额']=0;
           $data['应收金额']=0; 
           if(strpos($carinfo['客户类别'],'定点签约')===0){
               $xm=M('项目目录','dbo.','difo')->where(array('项目名称'=>array('like','%定点洗车%')))->find();
               $xm['标准金额']=$money;
               $data['报价金额']=$money;
               $data['应收金额']=$money;
               $data['客付金额']=$money;
               $data['挂账金额']=$money;
           }
           elseif($carinfo['客户类别']=='三方合作'){
               $xm=M('项目目录','dbo.','difo')->where(array('项目名称'=>array('like','%第三方平台%')))->find();
           }
           else{
               $price=35;
               $seatnum=intval($carinfo['座位数']);
               if($carinfo['客户类别']=='VIP客户'){
                   $price=0;
               }
               if($seatnum<7){
                   $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC10009'))->find();
               }elseif($seatnum>=7&&$seatnum<11){
                   $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC10003'))->find();
               }else{
                   $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC10004'))->find();
               }
               $price=$xm['标准金额'];
               $data['报价金额']=$price;
               $data['应收金额']=$price;
               $data['客付金额']=$price;
               $data['挂账金额']=$price;

           }
           $data['业务编号']=$bianhao;
           M('维修','dbo.','difo')->add($data);
           $this->writeLog($data['ID'],$bianhao,'蜡水洗车','洗车录入');
           $row=array();
           $row['ID']=$data['ID'];
           $row['项目编号']=$xm['项目编号'];
           $row['项目名称']=$xm['项目名称'];
           $row['维修工艺']=$xm['维修工艺'];
           $row['结算方式']='客付';
           $row['券编码']=$xm['券编码'];
           $row['工时']=$xm['标准工时'];
           $row['单价']=$xm['标准金额'];
           $row['金额']=$xm['标准金额'];
           $row['折扣']=1;
           $row['提成工时']=1;
           $row['提成金额']=1;
           $row['是否同意']=0;
           $row['已维修']='0小时'; 
           M('维修项目','dbo.','difo')->add($row);
           echo '提交成功';
           exit;
       }
       else{
           $this->display();
       }

   }
   public function products()
   {
       if(IS_POST)
       {
           $code=$_POST['code'];
           $pjlist=null;
           if($code!='')
                $pjlist=M('配件分类','dbo.','difo')->where(array('父项'=>$code))->select();
           echo json_encode($pjlist);
           exit;
       }
       else{
           if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
               $searchkey='%'.trim($_GET['searchkey']).'%';
           }
           if($_GET['lb']&&trim($_GET['lb'])!='')
           {
               $where['类别']=array('like',trim($_GET['lb'].'%'));
               
           }
           if($searchkey){       
               $searchwhere['编号']=array('like',$searchkey);
               $searchwhere['名称']=array('like',$searchkey);
               $searchwhere['规格']=array('like',$searchkey);
               $searchwhere['_logic']='OR';
               $where['_complex']=$searchwhere;

           }
           if($_GET['key']=='39099139')
           {
                 $where['类别']=array('like','%'.trim('轮胎%'));
           }
           $parms=$_GET;
           $pjlist=M('配件分类','dbo.','difo')->where(array('级别'=>'0'))->select();
           $count=M('配件库存','dbo.','difo')->where($where)->count();
           $Page = new Page($count,15,$parms);
           $show = $Page->show();
           $list=M('配件库存','dbo.','difo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
           $this->assign('page',$show);
           $this->assign('count',$count);
           $this->assign('pjlist',$pjlist);
           $this->assign('list',$list);
           $this->display();
       }
   }
   public function productsbyshop()
   {
       if(IS_POST)
       {
           $code=$_POST['code'];
           $pjlist=null;
           if($code!='')
                $pjlist=M('配件分类','dbo.','difo')->where(array('父项'=>$code))->select();
           echo json_encode($pjlist);
           exit;
       }
       else{
           if(cookie('username')=='老王'){
               $pjlist=M('配件分类','dbo.','difo')->where(array('级别'=>'0','名称'=>'轮胎'))->select();
           }else{
               $pjlist=M('配件分类','dbo.','difo')->where(array('级别'=>'0'))->select();
           }

           $this->assign('pjlist',$pjlist);
           $this->display();
       }
   }
   public function updatedate(){
       if(IS_POST){
           $sortname='年检日期';
           if($_GET['type']=='1')
               $sortname='交保到期';
           elseif($_GET['type']=='3')
               $sortname='下次保养';
           $r=M('车辆资料','dbo.','difo')->execute("update 车辆资料 set $sortname=REPLACE(CONVERT(nvarchar(10),$sortname,120),CONVERT(nvarchar(4),$sortname,120),year(getdate())+1) 
                     where $sortname is not null and $sortname<>'1900-01-01' and $sortname<GETDATE()");
           if($r)
               echo '更新成功';
           else
               echo '更新失败';
       }
   
   }
   public function sendbatchmessage(){
      if(IS_POST){
             $sortname='年检日期';
          if($_GET['type']=='1')
              $sortname='交保到期';
          elseif($_GET['type']=='3')
              $sortname='下次保养';
          $sortorder='asc';
          $where['车牌号码']=array('neq','0000');
          $where["$sortname"]=array('BETWEEN',array(date('Y-m-d',time()),date('Y-m-d',time()+3600*24*90)));
          $where['_string']="$sortname is not null and $sortname<>'1900-01-01' and 车主 like 'AYC%'";
          $carsinfo=M('车辆资料','dbo.','difo')->where($where)->order("$sortname  $sortorder")->select();
          $model  = new templateNews();
          $dataKey='';
          $dataArr=array();
          foreach($carsinfo as $carinfo){
              $lb='年审';
              if($_GET['type']=='1')
                  $lb='保险';
              if($_GET['type']=='3')
                  $lb='保养';
              $trace=M('客户跟踪','dbo.','difo')->where(array('车牌号码'=>$carinfo['车牌号码'],'年份'=>date('Y'),'跟踪类型'=>'模板信息','类别'=>"$lb"))->find();
              if(!$trace){
                  $card=M('Member_card_create')->where(array('token'=>$this->token,'number'=>$carinfo['车主']))->find();
                  $user=M('userinfo')->where(array('token'=>$this->token,'wecha_id'=>$card['wecha_id']))->find();
                  if($lb=='年审'){
                      $dataKey    = 'OPENTM206161737';
                      $dataArr    = array(
                          'first'         => '尊敬的['.$carinfo['车牌号码'].']车主您好，您的车辆年检即将到期。',
                          'keyword1'      =>$carinfo['车牌号码'],
                          'keyword2'      =>date('Y-m-d',strtotime($carinfo['年检日期'])),
                          'wecha_id'      => $card['wecha_id'],
                          'remark'        => '回复字母Y可预约,或致电020-39099139进行电话预约。',
                          'url'      => U('Wap/Store/daiban',array('token'=>$this->token,'carno'=>$carinfo['车牌号码'],'type'=>'年审','number'=>$carinfo['车主']),true,false,true),
                      );
                      $model->sendTempMsg($dataKey,$dataArr);
                      $data['车主']=$carinfo['车主'];
                      $data['车牌号码']=$carinfo['车牌号码'];
                      $data['跟踪时间']=date('Y-m-d H:i',time());
                      $data['跟踪人']='系统';
                      $data['跟踪类型']='模板信息';
                      $data['类别']='年审';
                      $data['年份']=date('Y');
                      $data['内容']='系统发送年审到期模板信息';
                      M('客户跟踪','dbo.','difo')->add($data);
                  }
                  elseif($lb=='保养'){
                   $dataKey    = 'TM151215';
                   $dataArr    = array(
                       'first'         => '尊敬的车主您好，您车牌为'.$carinfo['车牌号码'].'的汽车保养已到期。',
                       'keynote1'      =>date('Y-m-d',strtotime($carinfo['下次保养'])),//保养到期时间
                       'keynote2'      =>date('Y-m-d',strtotime($carinfo['最近保养'])),//上次保养时间
                       'keynote3'      => $carinfo['保养里程'].'公里',//上次保养里程
                       'wecha_id'      => $_GET['wecha_id'],
                       'remark'        => '回复字母N可预约,或致电020-39099139进行电话预约。',
                       'url'      => U('Wap/Store/cats',array('token'=>$this->token,'wecha_id'=>$user['wecha_id']),true,false,true),
                   );
                   $model->sendTempMsg($dataKey,$dataArr);
                   $data['车主']=$carinfo['车主'];
                   $data['车牌号码']=$carinfo['车牌号码'];
                   $data['跟踪时间']=date('Y-m-d H:i',time());
                   $data['跟踪人']='系统';
                   $data['跟踪类型']='模板信息';
                   $data['类别']='保养';
                   $data['年份']=date('Y');
                   $data['内容']='系统发送保养到期模板信息';
                   M('客户跟踪','dbo.','difo')->add($data);
                  }
                  else{
                      $dataKey    = 'TM151126';
                      $dataArr    = array(
                          'first'  => '尊敬的['.$carinfo['车牌号码'].']车主您好，您的爱车保险即将到期',
                          'keyword1'  => $user['truename'],
                          'keyword2'  =>$carinfo['车牌号码'],
                          'keyword3'  =>$carinfo['车型'],
                          'keyword4'  =>date('Y-m-d',strtotime($carinfo['交保到期'])),
                          'wecha_id'  =>$card['wecha_id'],
                          'remark'    =>'回复字母B可保险报价,或致电020-39099139进行电话预约。',
                          'url'      => U('Wap/Store/baoxian',array('token'=>$this->token,'carno'=>$carinfo['车牌号码'],'type'=>'保险','number'=>$carinfo['车主']),true,false,true),
                      );
                      $model->sendTempMsg($dataKey,$dataArr);
                      $data['车主']=$carinfo['车主'];
                      $data['车牌号码']=$carinfo['车牌号码'];
                      $data['跟踪时间']=date('Y-m-d H:i',time());
                      $data['跟踪人']='系统';
                      $data['跟踪类型']='模板信息';
                      $data['类别']='保险';
                      $data['年份']=date('Y');
                      $data['内容']='系统发送保险到期模板信息';
                      M('客户跟踪','dbo.','difo')->add($data);
                  }
              }
          }
          echo "发送成功";
      }
   }
   public function sendmessage()
   {
       if(IS_POST){
           $model  = new templateNews();
           $dataKey='';
           $dataArr=array();
           $carno=$_POST['carno'];
           $number=$_POST['number'];
           $card=M('Member_card_create')->where(array('token'=>$this->token,'number'=>$number))->find();
           $cardid=$card['cardid'];
           $user=M('userinfo')->where(array('token'=>$this->token,'wecha_id'=>$card['wecha_id']))->find();
           $carinfo =M('车辆资料','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
           switch($_GET['type']){
               case '4'://维修服务完成
                   $dataKey    = 'TM151213';
                   $wxinfo=M('维修','dbo.','difo')->where(array('车牌号码'=>$carno))->order('流水号 desc')->find();
                   $dataArr    = array(
                       'first'         => '尊敬的车主，您的爱车已经维修完毕。',
                       'keyword1'      =>$user['carno'],//车牌号
                       'keyword2'      => date("Y-m-d",time()),//完工时间
                       'keyword3'      => $wxinfo['接车人'].'（电话:'.$wxinfo['接车人电话'].')',//接车人与联系电话
                       'keyword4'      => $wxinfo['应收金额'].'元',//维修费用
                       'wecha_id'      => $user['wecha_id'],
                       'remark'        => '请您安排时间到店取车，如有疑问，请致电020-39099139联系我们,或发消息到微信平台上进行咨询。',
                       'url'           => U('Wap/Store/cats',array('token'=>$this->token,'wecha_id'=>$_GET['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               case '1'://保险到期通知
                   $dataKey    = 'TM151126';
                   $dataArr    = array(
                       'first'         => '尊敬的['.$carno.']车主您好，您的爱车保险即将到期',
                       'keyword1'      => $user['truename'],
                       'keyword2'      =>$carno,
                       'keyword3'      =>$carinfo['车型'],
                       'keyword4'      =>date('Y-m-d',strtotime($carinfo['交保到期'])),
                       'wecha_id'      => $user['wecha_id'],
                       'remark'        =>'回复字母B可保险报价,或致电020-39099139进行电话预约。',
                       'url'           => U('Wap/Store/baoxian',array('token'=>$this->token,'carno'=>$carinfo['车牌号码'],'type'=>'保险','number'=>$carinfo['车主']),true,false,true)
                   );
                   break;
               case '2'://年检通知
                   $dataKey    = 'OPENTM206161737';
                   $dataArr    = array(
                       'first'         => '尊敬的['.$carno.']车主您好，您的车辆年检即将到期。',
                       'keyword1'      =>$carno,
                       'keyword2'      =>date('Y-m-d',strtotime($carinfo['年检日期'])),
                       'wecha_id'      => $user['wecha_id'],
                       'remark'        => '回复字母Y可预约,或致电020-39099139进行电话预约。',
                       'url'           => U('Wap/Store/daiban',array('token'=>$this->token,'carno'=>$carinfo['车牌号码'],'type'=>'年审','number'=>$carinfo['车主']),true,false,true)
                   );
                   break;
               case '3':
                   $dataKey    = 'TM151215';
                   $dataArr    = array(
                       'first'         => '尊敬的车主您好，您车牌为'.$carno.'的汽车保养已到期。',
                       'keynote1'      =>date('Y-m-d',strtotime($carinfo['下次保养'])),//保养到期时间
                       'keynote2'      =>date('Y-m-d',strtotime($carinfo['最近保养'])),//上次保养时间
                       'keynote3'      => $carinfo['保养里程'].'公里',//上次保养里程
                       'wecha_id'      => $user['wecha_id'],
                       'remark'        => '回复字母N可预约,或致电020-39099139进行电话预约。',
                       'url'      => U('Wap/Store/cats',array('token'=>$this->token,'wecha_id'=>$user['wecha_id'],'cardid'=>$cardid),true,false,true),
                   );
                   break;
               case '5':
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
                   break; 

           }
           $model->sendTempMsg($dataKey,$dataArr);
           $data['车主']=$carinfo['车主'];
           $data['车牌号码']=$carinfo['车牌号码'];
           $data['跟踪时间']=date('Y-m-d H:i',time());
           $data['跟踪人']=cookie('username');
           $data['跟踪类型']='模板信息';
           $data['年份']=date('Y');
           $data['类别']='年审';
           $data['内容']='系统发送年审到期模板信息';
           if($_GET['type']=='1'){
               $data['类别']='保险';
               $data['内容']='系统发送保险到期模板信息';
           }elseif($_GET['type']=='3'){
               $data['类别']='保养';
               $data['内容']='系统发送保养到期模板信息';
           }
           M('客户跟踪','dbo.','difo')->add($data);                   
           echo "发送成功";
           
       }
       else{
           $this->assign('truename',$_GET['truename']);
           $this->assign('carno',$_GET['carno']);
           $this->display();
       }
   }

   public function mrrecord()
   {
       $wxinfo=$_POST['wxinfo'];
       $result=$this->genwxrecord($wxinfo['车牌号码'],$wxinfo['维修类别'],'汽车美容',$wxinfo['主修人'],
           $wxinfo['接车人'],$wxinfo['里程表'],$wxinfo['油位表'],$wxinfo['联系人'],$wxinfo['联系电话'],$wxinfo['轮胎规格'],null,null,$wxinfo['门店']);
     

       echo $result;
   }
   public function wxrecord()
   {
       if(IS_POST){
           $wxinfo=$_POST['wxinfo'];
           $pretime=$wxinfo['预计完工'];
           if($pretime&&$pretime!='')
           {
               if(count(explode('-', $pretime))==0){
                  $pretime=date('Y-m-d H:i',strtotime("+$pretime minute"));
               }
           }
           $result=$this->genwxrecord($wxinfo['车牌号码'],null,$wxinfo['维修类别'],$wxinfo['主修人'],
           $wxinfo['接车人'],$wxinfo['里程表'],$wxinfo['油位表'],$wxinfo['联系人'],$wxinfo['联系电话'],
           $wxinfo['轮胎规格'],$wxinfo['故障描述'],$pretime,$wxinfo['门店']);
            echo $result;
            exit;
          
       }
       else{
           $this->display();
       }
   }
   public function byrecord()
   {
       if(IS_POST){
           $wxinfo=$_POST['wxinfo'];
           if(isset($wxinfo))
           {
               $result=$this->genwxrecord($wxinfo['车牌号码'],null,$wxinfo['维修类别'],$wxinfo['主修人'],
               $wxinfo['接车人'],$wxinfo['里程表'],$wxinfo['油位表'],$wxinfo['联系人'],$wxinfo['联系电话'],$wxinfo['轮胎规格'],$wxinfo['故障描述'],$wxinfo['预计完工']);
               echo $result;
               exit;
           }
          
       }
       else{
           $this->display();
       }
   }
   public function notices(){
       $this->display();
   }
   private function weixinmessage($content,$fwgw){
       $this->weixin->send($content,'ohD3dvqz0EpNooXm4MgE4Xth8UVM');//刘伟
       $this->weixin->send($content,'ohD3dvtYWyjpTMAlWyLF2UPZKSv8');//刘飞
       $this->weixin->send($content,'ohD3dvkNqFLmM83oLxTK2hcKqoRM');//周四红
       $this->weixin->send($content,'ohD3dviFloHSvcl9ieoXFibqPFJM');//欧阳伟鹏
       //$this->weixin->send($content,'ohD3dvvygQTJUa8U2mTCRCr4YC3g');//公司手机号
       //$this->weixin->send($content,'ohD3dvux1Tdb71ZJZIH5bpKLuXNo');//张超群
       //$this->weixin->send($content,'ohD3dvmBt8Y82WjV5VceLc96-jO8');//王前进
       //$this->weixin->send($content,'ohD3dvpJALNBXqpu5Kz70kz0caGo');//欧建银
       //$this->weixin->send($content,'ohD3dviLfAhv63_C5tCbNrO2mfqU');//姜*英
       //$this->weixin->send($content,'ohD3dvh3B7LCBTlCT63ijG-blt6U');//侯碧婷
       if($fwgw&$fwgw!=''){
           if($fwgw=='刘亮'){
               $this->weixin->send($content,'ohD3dvlwa5PgS7n6z3s1tAK2NnTY');//刘亮
               $this->weixin->send($content,'ohD3dvnoP57_LF0vXtTIbN1L4PZo');//刘亮
           }else{
               $this->weixin->send($content,'ohD3dvhb9V5DXEoCZO5yMfg6clgc');//刘叔

           }
       }
   }
   private function MessageTip($carinfo,$mendian,$wxlb){
       $data['车主']=$carinfo['车主'];
       $data['车牌号码']=$carinfo['车牌号码'];
       $data['跟踪时间']=date('Y-m-d H:i',time());
       $data['登记人']='系统';
       $data['是否反馈']='否';
       $data['是否成交']='否';
       $data['门店']=$mendian;
       $data['跟踪类型']='到店消费';
       $data['年份']=date('Y');
       $model=new templateNews();
       if(date('Y-m-d',strtotime($carinfo['交保到期']))!='1900-01-01'&&date('Y-m-d',strtotime($carinfo['交保到期']))!='1970-01-01'){
           if(strtotime($carinfo['交保到期'])-(time()+90*24*3600)<0){
               $content=$carinfo['客户类别'].$carinfo['联系人'].'的'.$carinfo['车牌号码'].'车辆保险于';
               $content.=date('Y-m-d',strtotime($carinfo['交保到期'])).'日到期,现车辆已到'.$mendian.$wxlb;
               $content.=',请做好跟踪服务（服务顾问:'.$carinfo['服务顾问'].'）'; 
               //$this->weixinmessage($content,$carinfo['服务顾问']);
               
               $data['类别']='保险';
               $data['内容']=$content;
               $id=M('客户跟踪','dbo.','difo')->add($data);
               $msgdata='{
                "title": "保险跟踪信息", 
                "actionCard": {
                    "title": "保险跟踪信息", 
                    "text": "'.$content.'", 
                    "hideAvatar": "0", 
                    "btnOrientation": "1", 
                    "btns": [
                            {
                                "title": "反馈信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=record&lb=1&id='.$id.'&number='.$carinfo['车主'].'" 
                            },
                           {
                                "title": "历史信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=history&lb=1&carno='.$carinfo['车牌号码'].'&number='.$carinfo['车主'].'"  
                            }
                           ]
                }, 
               "msgtype": "actionCard",
                }';
               $model->postMessage($this->getbooturl('总经办'),$msgdata);
               $model->postMessage($this->getbooturl($mendian),$msgdata);
               $projects=M('客户跟踪','dbo.','difo')->where(array('车牌号码'=>$carinfo['车牌号码'],'年份'=>date('Y',time()),'类别'=>'保险','跟踪类型'=>'推广方案'))->select();
               if(count($projects)>0){
                   $membercar=M('member_card_car')->where(array('carno'=>$carinfo['车牌号码']))->find();
                   foreach($projects as $project){
                       if($membercar){
                           $this->weixin->send($project['内容'],$membercar['wecha_id']);
                       }
                       $msgdata='{
                        "msgtype": "text", 
                        "text": {
                            "content": "'.$project['内容'].'"
                        }, 
                        "at": {
                            "isAtAll": true
                        }
                        }';
                       $model->postMessage($this->getbooturl('总经办'),$msgdata);
                       $model->postMessage($this->getbooturl($mendian),$msgdata);
                       //$this->weixinmessage($project['内容'],$carinfo['服务顾问']);
                       //$data['类别']='推广信息';
                       //$data['内容']=$project['内容'];
                       //M('客户跟踪','dbo.','difo')->add($data);
                   }
               }
           }
       }
       if(date('Y-m-d',strtotime($carinfo['年检日期']))!='1900-01-01'&&date('Y-m-d',strtotime($carinfo['年检日期']))!='1970-01-01'){
           if(strtotime($carinfo['年检日期'])-(time()+90*24*3600)<0){
               $content=$carinfo['客户类别'].$carinfo['联系人'].'的'.$carinfo['车牌号码'].'车辆年检于';
               $content.=date('Y-m-d',strtotime($carinfo['年检日期'])).'日到期,现车辆已进厂'.$mendian.$wxlb;
               $content.=',请做好跟踪服务（服务顾问:'.$carinfo['服务顾问'].'）'; 
               //$this->weixinmessage($content,$carinfo['服务顾问']);
              
               $data['类别']='年审';
               $data['内容']=$content;
               $id=M('客户跟踪','dbo.','difo')->add($data);
               $msgdata='{
                "actionCard": {
                    "title": "年审跟踪信息", 
                    "text": "'.$content.'", 
                    "hideAvatar": "0", 
                    "btnOrientation": "1", 
                    "btns": [
                            {
                                "title": "反馈信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=record&lb=2&id='.$id.'&number='.$carinfo['车主'].'" 
                            },
                           {
                                "title": "历史信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=history&lb=2&carno='.$carinfo['车牌号码'].'&number='.$carinfo['车主'].'"  
                            }
                           ]
                }, 
               "msgtype": "actionCard",
                }';
               $model->postMessage($this->getbooturl('总经办'),$msgdata);
               $model->postMessage($this->getbooturl($mendian),$msgdata);
               $projects=M('客户跟踪','dbo.','difo')->where(array('车牌号码'=>$carinfo['车牌号码'],'年份'=>date('Y',time()),'类别'=>'年审','跟踪类型'=>'推广方案'))->select();
               if(count($projects)>0){
                   $membercar=M('member_card_car')->where(array('carno'=>$carinfo['车牌号码']))->find();
                   foreach($projects as $project){
                       if($membercar){
                           $this->weixin->send($project['内容'],$membercar['wecha_id']);
                       }
                       //$this->weixinmessage($project['内容'],$carinfo['服务顾问']);
                       $msgdata='{
                        "msgtype": "text", 
                        "text": {
                            "content": "'.$project['内容'].'"
                        }, 
                        "at": {
                            "isAtAll": true
                        }
                        }';
                       //$model->postMessage($booturl,$msgdata);
                       //$data['类别']='推广信息';
                       //$data['内容']=$project['内容'];
                       //M('客户跟踪','dbo.','difo')->add($data);
                   }
               }
           }
       }
       if(date('Y-m-d',strtotime($carinfo['下次保养']))!='1900-01-01'&&date('Y-m-d',strtotime($carinfo['下次保养']))!='1970-01-01'){
           if(strtotime($carinfo['下次保养'])<=time()&&strtotime($carinfo['下次保养'])+60*24*3600>=time()){
               $content=$carinfo['客户类别'].$carinfo['联系人'].'的'.$carinfo['车牌号码'].'车辆保养于';
               $content.=date('Y-m-d',strtotime($carinfo['下次保养'])).'日到期,现车辆已进厂'.$mendian.$wxlb;
               $content.=',请做好跟踪服务（服务顾问:'.$carinfo['服务顾问'].'）'; 
               //$this->weixinmessage($content,$carinfo['服务顾问']);
               $data['类别']='保养';
               $data['内容']=$content;
               $id=M('客户跟踪','dbo.','difo')->add($data);
               $msgdata='{
                "actionCard": {
                    "title": "保养跟踪信息", 
                    "text": "'.$content.'", 
                    "hideAvatar": "0", 
                    "btnOrientation": "1", 
                    "btns": [
                            {
                                "title": "反馈信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=record&lb=3&id='.$id.'&number='.$carinfo['车主'].'" 
                            },
                           {
                                "title": "历史信息", 
                                "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=history&lb=3&carno='.$carinfo['车牌号码'].'&number='.$carinfo['车主'].'"  
                            }
                           ]
                }, 
               "msgtype": "actionCard",
                }';
               $model->postMessage($this->getbooturl('总经办'),$msgdata);
               $model->postMessage($this->getbooturl($mendian),$msgdata);

           }
       }
   }
   private function getbooturl($shop){
       $boots=array( '塘坑店'=>'https://oapi.dingtalk.com/robot/send?access_token=9e3c1b9e17029774dc6f2749a82eb01d61555daa57c9cfbacc5b129d141d55e8',
       '区府店'=>'https://oapi.dingtalk.com/robot/send?access_token=ca22bf1b681e1b7d842c8ee8741dd4ff392934b1ffd0ae35530f9d69a61bfed1',
       '时代长岛店'=>'https://oapi.dingtalk.com/robot/send?access_token=bd9a4610ae31bd9826814791517bb6a4e67573368b0407358fe1468fe1288b1a',
           '总经办'=>'https://oapi.dingtalk.com/robot/send?access_token=2477f2bc29e472747c2e75e01bb1ab2b405221c2ce152dc13307b4dda5fa28d7'
   );
       return $boots[$shop];
   }
   private function genwxrecord($carno,$type='',$wxlb='',$person,$fwgw,$licheng=null,$youwei=null,$lxr=null,$phone=null,$luntai=null,$fault=null,$yjwg=null,$shop=null){
      
        $data=M('维修','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
        unset($data['流水号']);
        unset($data['ROW_NUMBER']);
        $bianhao=$this->getcodenum('WX');
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
        if($type!=''){
            $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>$type))->find();
        }
        if($wxlb=='常规保养'){
            $xm=M('项目目录','dbo.','difo')->where(array('项目名称'=>'机油更换'))->find();
        }
        $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$fwgw))->find();
        if($carinfo)
        {
            $carinfo['最近维修']=date('Y-m-d',time());
            $carinfo['维修次数']=intval($carinfo['维修次数'])+1;
            $carinfo['轮胎规格']=$luntai;
            if(isset($licheng)){
                if($wxlb!='常规保养'){
                    $baoyang=$carinfo['常规保养数'];
                    $bylc=$carinfo['保养里程'];
                    if($bylc>0&&intval($licheng)-intval($carinfo['保养里程'])>intval($carinfo['常规保养数'])){
                        $content=$carinfo['客户类别'].$carinfo['联系人'].'的'.$carinfo['车牌号码']."车辆已超过$baoyang 公里未进行保养，本次里程$licheng 公里，上次保养里程$bylc 公里";
                        $content.='现车辆已进厂'.$shop.$wxlb;
                        $content.=',请做好跟踪服务（服务顾问:'.$carinfo['服务顾问'].'）'; 
                        //$this->weixinmessage($content,$shop);
                        $tracedata['车主']=$carinfo['车主'];
                        $tracedata['车牌号码']=$carinfo['车牌号码'];
                        $tracedata['跟踪时间']=date('Y-m-d H:i',time());
                        $tracedata['登记人']='系统';
                        $tracedata['是否反馈']='否';
                        $tracedata['是否成交']='否';
                        $tracedata['门店']=$shop;
                        $tracedata['跟踪类型']='到店消费';
                        $tracedata['年份']=date('Y');
                        $tracedata['类别']='保养';
                        $tracedata['内容']=$content;
                        $id=M('客户跟踪','dbo.','difo')->add($tracedata);
                        $msgdata='{
                            "title": "保养跟踪信息", 
                            "actionCard": {
                                "title": "保养跟踪信息", 
                                "text": "'.$content.'", 
                                "hideAvatar": "0", 
                                "btnOrientation": "1", 
                                "btns": [
                                        {
                                            "title": "反馈信息", 
                                            "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=record&lb=3&id='.$id.'&number='.$carinfo['车主'].'" 
                                        },
                                       {
                                            "title": "历史信息", 
                                            "actionURL": "http://www.nsayc.com/index.php?g=Wap&m=Dingding&a=history&lb=3&carno='.$carinfo['车牌号码'].'&number='.$carinfo['车主'].'"  
                                        }
                                       ]
                            }, 
                           "msgtype": "actionCard",
                            }';
                        $model=new templateNews();
                        $model->postMessage($this->getbooturl('总经办'),$msgdata);
                        $model->postMessage($this->getbooturl($shop),$msgdata);
                    }
                }
                $carinfo['里程']=$licheng;
            }
            if($wxlb=='常规保养'){

                $carinfo['最近保养']=date("Y-m-d",time()); 
                $months=$carinfo['保养周期'];
                $carinfo['下次保养']=date("Y-m-d",strtotime("+$months month"));
                if(isset($licheng)){
                    $carinfo['保养里程']=$licheng;
                    $carinfo['下次保养里程']=intval($licheng)+intval($carinfo['常规保养数']);
                }
            }
            unset($carinfo['流水号']);
            unset($carinfo['ROW_NUMBER']);
            M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->save($carinfo);
            foreach($data as $key=>$value){
                $data[$key]=$carinfo[$key];
            }
            $this->MessageTip($carinfo,$shop,$wxlb);
             
             $content=$carinfo['联系人'].'的'.$carinfo['车牌号码'].'车辆进厂'.$shop.$wxlb;
             $content.=",客户要求在$yjwg 时候交车，请做好派工安排";
             $this->weixin->send($content,'ohD3dvpJALNBXqpu5Kz70kz0caGo');//欧建银
        }
        else{ 
            if($wxlb!='普通快修'){
                $czinfo['名称']=$carno;
                $czinfo['客户']=1;
                $czinfo['类别']='临时客户';
                //$czinfo['等级']='★';
                $czinfo['手机号码']=$phone;
                $czinfo['联系电话']=$phone;
                $czinfo['门店']=$shop;
                $czinfo['联系人']=$lxr;
                $czinfo['ID']=$this->getcode(18,0,1);
                M('往来单位','dbo.','difo')->add($czinfo);
                $carinfo['车主']=$carno;
                $carinfo['车牌号码']=$carno;
                $carinfo['轮胎规格']=$_POST['luntai'];
                $carinfo['客户ID']=$czinfo['ID'];
                $carinfo['客户类别']='临时客户';
                $carinfo['最近维修']=date('Y-m-d',time());
                $carinfo['维修次数']=1;
                $carinfo['是否在用']='是';
                $carinfo['手机号码']=$phone;
                $carinfo['门店']=$shop;
                $carinfo['联系电话']=$phone;
                $carinfo['联系人']=$lxr;
                $carinfo['常规保养数']=5000;
                $carinfo['保养周期']=5;
                if(isset($licheng)){
                    $carinfo['里程']=$licheng;
                }
                if($wxlb=='常规保养'){
                    $carinfo['最近保养']=date("Y-m-d",time());
                    $carinfo['下次保养']=date("Y-m-d",strtotime("+5 month"));
                    if(isset($licheng)){
                        $carinfo['保养里程']=$licheng;
                        $carinfo['下次保养里程']=intval($licheng)+5000;
                    }
                }
                else{
                    unset($data['下次保养']);
                }
                M('车辆档案','dbo.','difo')->add($carinfo);
                foreach($data as $key=>$value){
                    $data[$key]=$carinfo[$key];
                }
                $data['手机号码']=$phone;
                $data['联系电话']=$phone;
                $data['联系人']=$lxr;
                $data['轮胎规格']=$luntai;


            }else{
                $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
                foreach($data as $key=>$value){
                    $data[$key]=$carinfo[$key];
                }
                $data['车牌号码']='0000';
                $data['客户类别']='临时客户';
                $data['手机号码']='';
                $data['联系电话']='';
                $data['联系人']=$carno;
                $data['送修人']=$carno;
            }
                  

        }
        $data['接车人']=$fwgw;
        $data['ID']=$this->getcode(20,1,1);
        $data['制单日期']=date('Y-m-d',time());
        $data['制单人']=cookie('username');
        $data['保修类别']='保外';
        if($wxlb=='保险理赔'){
        $data['保修类别']='事故';
        }
        if(isset($youwei)){
            $data['油表数']=$youwei;
        }
        if(isset($licheng)){
            $data['进厂里程']=$licheng;
        }
        $data['单据类别']='快修单';
        $data['当前状态']='报价';
        $data['维修状态']='报价';
        if(!in_array($wxlb,array('蜡水洗车','汽车美容','普通快修'))){
            $data['单据类别']='普通单';
        }
        $data['当前主修人']=$person;
        $data['主修人']=$person;
        $data['门店']=$shop;
        $data['结算客户']=$carinfo['车主'];;
        $data['结算客户ID']=$carinfo['客户ID'];
        unset($data['出厂时间']);
        unset($data['实际完工']);
        unset($data['购买日期']);
        unset($data['结束日期']);
        if($yjwg==null){
            unset($data['预计完工']);
        }else{
            $data['预计完工']=$yjwg;
        }
        $data['整单服务折扣']=1;
        $data['整单销售折扣']=1;
        if($wxlb!='保险理赔'){
            $memberinfo=M('会员详细信息','dbo.','difo')->where(array('ID'=>$carinfo['客户ID']))->find();         
            $data['整单服务折扣']=$memberinfo['服务折扣1'];
            $data['整单销售折扣']=$memberinfo['销售折扣1'];;
        }
        unset($data['结算日期']);
        unset($data['开工时间']);
        unset($data['上交钥匙']);
        unset($data['评价时间']);
        $data['进厂时间']=date('Y-m-d H:i',time());
        $data['维修类别']=$wxlb;
        $data['报称故障']=$fault;
        if(isset($xm))
            $price=$xm['标准金额'];
        else
            $price=null;
        $data['报价金额']=$price;
        $data['应收金额']=$price;
        $data['业务编号']=$bianhao;
        M('维修','dbo.','difo')->add($data);
        if(in_array($wxlb,array('蜡水洗车','汽车美容','常规保养'))){
            $row=array();
            $row['ID']=$data['ID'];
            $row['项目编号']=$xm['项目编号'];
            $row['项目名称']=$xm['项目名称'];
            $row['维修工艺']=$xm['维修工艺'];
            $row['结算方式']='客付';
            $row['券编码']=$xm['券编码'];
            $row['工时']=$xm['标准工时'];
            $row['单价']=$xm['标准金额'];
            $row['金额']=$xm['标准金额'];
            $row['虚增类别']='';
            $row['折扣']=$data['整单服务折扣'];
            $row['提成工时']=1;
            $row['提成金额']=1;
            //$row['主修人']=$person;
            //$row['班组']=$yg['班组'];
            $row['是否同意']=1;
            M('维修项目','dbo.','difo')->add($row);
        }
        if($wxlb=='常规保养'){
            if(isset($carinfo['机油格'])&&$carinfo['机油格']!='')
            {
                $project=M('配件目录','dbo.','difo')->where(array('类别'=>array('like','%机油格'),'编码'=>$carinfo['机油格']))->find();
                if($project){
                    $this->addbyproduct($project,$data['ID'],$shop,$data['整单销售折扣']);
                }
            }
            if(isset($carinfo['空气格'])&&$carinfo['空气格']!='')
            {
                $project=M('配件目录','dbo.','difo')->where(array('类别'=>array('like','%空气格'),'编码'=>$carinfo['空气格']))->find();
                if($project){
                    $this->addbyproduct($project,$data['ID'],$shop,$data['整单销售折扣']);
                }
            }
            if(isset($carinfo['冷气格'])&&$carinfo['冷气格']!='')
            {
                $project=M('配件目录','dbo.','difo')->where(array('类别'=>array('like','%冷气格'),'编码'=>$carinfo['冷气格']))->find();
                if($project){
                    $this->addbyproduct($project,$data['ID'],$shop,$data['整单销售折扣']);
                }
            }
            $project=M('配件目录','dbo.','difo')->where(array('名称'=>array('like','%磁护/4L')))->find();
            if($project){
                $this->addbyproduct($project,$data['ID'],$shop,$data['整单销售折扣']);
            }
        }
        $this->writeLog($data['ID'],$bianhao,$wxlb,'维修录入');
        $this->calprice($data['ID']);
        return '提交成功';

       
   } 
   private function addbyproduct($project,$wxid,$shop,$discount=1){
       $row['ID'] = $wxid;
       $row['编号'] = $project['编号'];
       $row['仓库'] = '区府门店仓库';
       if ($shop=='塘坑店')
           $row['仓库'] = '塘坑门店仓库';
       else if($shop=='时代长岛店')
           $row['仓库'] = '时代长岛店仓库';
       $row['名称'] = $project['名称'];
       $row['券编码'] = $project['券编码'];
       $row['规格'] = $project['规格'];
       $row['结算方式'] = '客付';
       $row['单位'] = $project['库存单位'];
       $row['虚增类别'] ='';
       $row['单价'] = $project['参考售价'];
       $row['折扣'] = $discount;
       $row['数量'] = 1;
       $row['金额'] = $row['数量'] * $row['单价'];
       //row['金额'] = row['数量'] * row['单价'] * row['折扣'];
       $row['税率'] = 0;
       $row['成本价'] = $project['成本价'];
       $row['税额'] = $row['金额'] * $row['税率'];
       $row['备注'] = $project['备注'];
       M('维修配件','dbo.','difo')->add($row);
   }
   private function writeLog($id,$ywbh,$type,$content)
   {
       $username=cookie('username');
       $sql="exec [dbo].[Proc_WriteLog] '$id','$ywbh','$type','$content','$username'";
       M('维修项目','dbo.','difo')->execute($sql);
   }
   private function getprice(){
       
   }
   private function getcodenum($type)
   {
       $code=M('编号单','dbo.','difo')->where(array('类别'=>"$type",'日期'=>date('Y-m-d', time())))->max('队列');
       $bianhao="$type-".date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
       M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>"$type",'日期'=>date('Y-m-d', time())));
       return $bianhao;
   }
   private function genproductnum($type)
   {
       $code=M('配件目录','dbo.','difo')->where(array('类别'=>"$type"))->max('号码');
       if(!$code){
           $code=M('配件目录','dbo.','difo')->max('号码');
       }
       $right=substr($code,-4);
       $right=intval($right)+1;
       $right=str_pad($right,4,'0',STR_PAD_LEFT);
       $bianhao=substr_replace($code,$right,-4);
       return $bianhao;
   }
   public function genproductcode()
   {
       $type=$_GET['type'];
       $code=M('配件目录','dbo.','difo')->where(array('类别'=>"$type"))->max('编号');
       if(!$code){
           $code=M('配件目录','dbo.','difo')->max('编号');
       }
       $right=substr($code,-4);
       $right=intval($right)+1;
       $right=str_pad($right,4,'0',STR_PAD_LEFT);
       $bianhao=substr_replace($code,$right,-4);
       echo $bianhao;
   }

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
   private function genbill($price,$chezhu,$zhaiyao,$danwei){

       $bianhao=$this->getcodenum("BI");
       $data['单据编号']=$bianhao;
       $data['ID']=$this->getcode(20,1,1);
       $data['制单日期']=date('Y-m-d',time());
       $data['制单人']=cookie('username');
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
       $data['单位编号']=$danwei;
       $data['取用预付']=0;
       $data['取用预收']=0;
       $data['本次冲账']=$price;
       $data['单据类别']='应收款';
       $data['取用预存']=0;
       M('日常收支','dbo.','difo')->add($data);
   }
   private function genwxbill($wx){

       $paybill['ID']=$this->getcode(18,1,1);
       $paybill['单位编号']=$wx['客户ID'];
       $paybill['单位名称']=$wx['车主'];
       //$paybill['单据类别']=$wx['维修类别'];
       $paybill['单据类别']='维修';
       $paybill['单据编号']=$wx['业务编号'];
       $paybill['制单日期']=date('Y-m-d',time());
       $paybill['制单人']=cookie('username');
       $paybill['总金额']=$wx['应收金额'];
       $paybill['引用ID']=$wx['ID'];
       $paybill['已结算金额']=$wx['现收金额'];
       $paybill['未结算金额']=$wx['挂账金额'];
       $paybill['本次结算']=$wx['现收金额'];
       $paybill['提醒日期']=date('Y-m-d',time());
       $paybill['账款类别']='应收款';
       $paybill['当前状态']='待审核';
       $paybill['摘要']='['.$wx['门店'].']'.$wx['维修类别'].'欠款';
       if($wx['挂账金额']==0){
           $paybill['当前状态']='已审核';
           $paybill['摘要']=$wx['维修类别'];
       }
       $paybill['审核人']=cookie('username');
       $paybill['审核日期']=date('Y-m-d',time());
       $paybill['虚增价税']=0;
       $paybill['挂账金额']=$wx['挂账金额'];
       $paybill['车牌号码']=$wx['车牌号码'];
       $paybill['门店']=$wx['门店'];
       M('应收应付单','dbo.','difo')->add($paybill);
       if(doubleval($wx['现收金额'])>0||$wx['结算方式']=='会员卡支付'){
           $bianhao=$this->getcodenum("BI");
           $data['单据编号']=$bianhao;
           $data['ID']=$this->getcode(20,1,1);
           $data['制单日期']=date('Y-m-d',time());
           $data['制单人']=cookie('username');
           $data['单位名称']=$wx['车主'];
           $data['账款类别']='收款单';
           $data['开户银行']='';
           $data['银行账号']='';
           $data['本次应付']=0;
           $data['本次应收']==$wx['现收金额'];
           $data['整单折扣']=1;
           $data['实付金额']=0;
           $data['实收金额']=$wx['现收金额'];
           $data['折扣金额']=$wx['优惠金额'];
           $data['结算方式']=$wx['结算方式'];
           $data['结算账户']=$wx['结算账户'];
           $data['支票号']=0;
           $data['凭证号']=0;
           $data['摘要']='维修收款('.$wx['业务编号'].')';
           $data['收支项目']='维修收款';
           $data['当前状态']='待审核';
           $data['发票类别']=$wx['门店'];
           $data['发票号']=$wx['车牌号码'];
           $data['单位编号']=$wx['客户ID'];
           $data['取用预付']=0;
           $data['取用预收']=0;
           $data['本次冲账']=$wx['现收金额'];
           $data['单据类别']='应收款';
           $data['取用预存']=0;
           M('日常收支','dbo.','difo')->add($data);

           $dj['挂账ID']=$paybill['ID'];
           $dj['收支ID']=$data['ID'];
           $dj['金额']=$wx['现收金额'];
           M('引用单据','dbo.','difo')->add($dj);
       }
   }
   private function calprice($id){
       $projectprice=0;
       $totalproject=0;
       $projects=M('维修项目','dbo.','difo')->where(array('ID'=>$id))->select();
       if($projects){
           foreach($projects as $project){
               if(doubleval($project['虚增金额'])>0){
                   $projectprice+=$project['虚增金额']*$project['折扣']+$project['税额'];
                   $totalproject+=$project['虚增金额']+$project['税额'];
               }else{
                   $projectprice+=$project['金额']*$project['折扣']+$project['税额'];
                   $totalproject+=$project['金额']+$project['税额'];
               }
               
           }
       }
       $productprice=0;
       $totalproduct=0;
       $sumcost=0;
       $products=M('维修配件','dbo.','difo')->where(array('ID'=>$id,'仅内部核算成本'=>'0'))->select();
       if($products){
           foreach($products as $product){
               if(doubleval($product['虚增金额'])>0){
                   $productprice+=$product['虚增金额']*$product['折扣']+$product['税额'];
                   $totalproduct+=$product['虚增金额']+$product['税额'];
               }
               else{
                   $productprice+=$product['金额']*$product['折扣']+$product['税额'];
                   $totalproduct+=$product['金额']+$product['税额'];
                   $sumcost+=$product['成本价']*$product['数量'];
               }
               
           }
       }
       $appendprice=M('附加费用','dbo.','difo')->where(array('ID'=>$id))->sum('金额');
       $totalprice=$projectprice+$productprice+$appendprice;
       $data['报价金额']=$totalproject+$totalproduct+$appendprice;
       $data['报价人']=cookie('username');
       $data['材料费']=$productprice;
       $data['材料成本']=$sumcost;
       $data['工时费']=$projectprice;
       $data['附加费']=$appendprice;
       $data['款项总额']=$totalprice;
       $data['客付金额']=$totalprice;
       $data['应收金额']=$totalprice;
       $data['人工成本']=0;

       M('维修','dbo.','difo')->where(array('ID'=>$id))->save($data);
   }
   private function change($cardid,$wecha_id)
   {
       $member_card_set_db=M('Member_card_set'); 
       $thisCard=$member_card_set_db->where(array('id'=>$cardid))->find(); 
       if($thisCard['grade']==1){
           $newcard=$member_card_set_db->where(array('grade'=>2))->find();
           $card=M('Member_card_create')->field('id,number')->where(" cardid=".intval($newcard['id'])." and wecha_id = ''")->order('id ASC')->find();
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
               M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->save(array('wecha_id'=>''));
               M('Member_card_create')->where(array('id'=>$card['id']))->save(array('wecha_id'=>$wecha_id));
               return $card['number'];
           }else{
               return 0;
           }
       }
       return 0;
       
   }
   private function changecarinfo($user,$number)
   {
       $car=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$user['carno']))->find();
       $lb='1星客户';
       if(!empty($car)){
           $item['车主']=$number;
           $item['联系人']=$user['truename'];
           $item['联系电话']=$user['tel'];
           $item['手机号码']=$user['tel'];
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
}

