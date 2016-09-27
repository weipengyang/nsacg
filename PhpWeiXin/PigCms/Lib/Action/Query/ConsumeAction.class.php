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
    public function _initialize(){
        $this->token='rlydsv1453614397';
        if(!in_array(ACTION_NAME, array('register', 'login'))){
            if(!cookie('username')){
                if(ACTION_NAME=='products' and $_GET['key']=='39099139'){
                
                }
                else{
                     $this->redirect(U('Consume/login', array('token' => $this->token)));
                }
            }
            else{
                $username=cookie('username');
                cookie('username',$username,3600);
                $this->assign('username',$username);
            }
        }
    }

    public function addproject(){
        if(IS_POST){
            $result=M('','dbo','difo')->query("SELECT 类别 [text],'true' isexpand FROM 项目分类");
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
    public function stat()
    {
        $jy=M('车辆档案','dbo.','difo')->query("select  机油格,count(1) 数量 from  车辆档案 where 机油格 is not null group by 机油格 ");
        $keys=array_column($jy,'机油格');
        $values=array_column($jy,'数量');;
        $this->assign('keys',json_encode($keys));
        $this->assign('values',json_encode($values));
        $this->display();
    }
    public function getstatdata()
    {
        $type=$_GET['type'];
        $jy=M('车辆档案','dbo.','difo')->query("select  $type,count(1) 数量 from  车辆档案 where $type is not null and $type !='' group by $type order by  $type ");
        $data['keys']=array_column($jy,"$type");
        $data['values']=array_column($jy,'数量');;
        echo json_encode($data);
    }
    public function addproduct(){
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
    private function calprice($id){
        $projectprice=0;
        $totalproject=0;
        $projects=M('维修项目','dbo.','difo')->where(array('ID'=>$id))->select();
        if($projects){
           foreach($projects as $project){
               $projectprice+=$project['金额'];
               $totalproject+=$project['金额'];
           }
        }
        $productprice=0;
        $totalproduct=0;
        $products=M('维修配件','dbo.','difo')->where(array('ID'=>$id,'仅内部核算成本'=>'0'))->select();
        if($products){
            foreach($products as $product){
                $productprice+=$product['金额'];
                $totalproduct+=$product['金额'];
            }
        }
        $appendprice=M('附加费用','dbo.','difo')->where(array('ID'=>$id))->sum('金额');
        $totalprice=$projectprice+$productprice+$appendprice;
        $data['报价金额']=$totalprice;
        $data['报价人']=cookie('username');
        $data['材料费']=$productprice;
        $data['工时费']=$projectprice;
        $data['附加费']=$appendprice;
        $data['款项总额']=$totalprice;
        $data['客付金额']=$totalproduct+$totalproject+$appendprice;
        $data['应收金额']=$totalproduct+$totalproject+$appendprice;
        //$data['材料成本']=;
        $data['人工成本']=0;

        M('维修','dbo.','difo')->where(array('ID'=>$id))->save($data);
    }
    public function changestate(){
       if(IS_POST){
           $id=$_POST['id'];
           $zt=$_POST['zt'];
           M('维修','dbo.','difo')->where(array('流水号'=>$id))->save(array('当前状态'=>$zt));
           
       }
    }
    public  function getscoreinfo(){
    
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        $searchkey=$_POST['searchkey'];
        $searchkey='%'.trim($searchkey).'%';
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
    public  function getuserinfo(){
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
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
    public function getstacks()
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
            $searchwhere['编号']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        if($_GET['key']=='39099139')
        {
            $where['类别']=array('like','%'.trim('轮胎%'));
        }
        if($_POST['flag'])
        {
            $where['_string']='isnull(库存,0)<isnull(警戒下限,0)';
        }
        $count=M('配件库存','dbo.','difo')->where($where)->count();
        $yelist=M('配件库存','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public function getcrklist()
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
            $where['单据类别']=$_POST['lb'];
            
        }
        if($_POST['code']&&trim($_POST['code'])!='')
        {
            $where['编号']=$_POST['code'];
            
        }
        if($searchkey){       
            $searchwhere['编号']=array('like',$searchkey);
            $searchwhere['名称']=array('like',$searchkey);
            $searchwhere['规格']=array('like',$searchkey);
            $searchwhere['备注']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
        $count=M('出入库明细','dbo.','difo')->join('left join 出入库单 on 出入库明细.ID=出入库单.ID')->where($where)->count();
        $yelist=M('出入库明细','dbo.','difo')
            ->join('left join 出入库单 on 出入库明细.ID=出入库单.ID')->where($where)
            ->field('出入库明细.*,出入库单.单据编号,出入库单.原因,出入库单.引用单号,出入库单.引用类别,出入库单.制单日期,出入库单.审核日期')
            ->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }
    public function getcarsinfo()
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
            $where['客户类别']=$_POST['khlb'];
        }
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
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
        $yelist=M('车辆档案','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order("$sortname  $sortorder")->select();
        $data['Rows']=$yelist;
        $data['Total']=$count;
        echo json_encode($data);
        
    }

    public  function getwxinfo(){
        $type=$_POST['type'];
        $page=$_POST['page'];
        $pagesize=$_POST['pagesize'];
        $where['1']=1;
        if (isset($_POST['searchkey'])&&trim($_POST['searchkey'])!=''){
            $searchkey='%'.trim($_POST['searchkey']).'%';
        }
        if($_POST['lb']&&trim($_POST['lb'])!='')
        {
            $where['维修类别']=trim($_POST['lb']);
            
        }
        if($_POST['zt']&&trim($_POST['zt'])!='')
        {
            $where['当前状态']=trim($_POST['zt']);
            
        }
        if($_POST['shop']&&trim($_POST['shop'])!='')
        {
            $where['门店']=trim($_POST['shop']);
            
        }
        if($_POST['carno']&&trim($_POST['carno'])!='')
        {
            $where['车牌号码']=trim($_POST['carno']);
            
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
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['接车人']=array('like',$searchkey);
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['门店']=array('like',$searchkey);
            $searchwhere['送修人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['当前状态']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

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
        $wxinfo=M('维修','dbo.','difo')->where($where)->limit(($page-1)*$pagesize,$pagesize)->order($order)->select();
        $count=M('维修','dbo.','difo')->where($where)->count();
        $data['Rows']=$wxinfo;
        $data['Total']=$count;
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
    public function savestock()
    {
        if(IS_POST){
            $stockinfo=$_POST['stockinfo'];
            M('配件目录','dbo.','difo')->where(array('编号'=>$stockinfo['编号']))->save($stockinfo);
            echo '保存成功';
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
        $where['1']=1;
        $shop=$_GET['shop'];
        $cangku='塘坑门店仓库';
        if($shop=='区府店')
            $cangku='区府门店仓库';
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
            ->join('left join 配件仓位 on 配件目录.编号=配件仓位.编号  and  配件仓位.仓库=\''.$cangku.'\'')
            ->field('配件目录.*,配件仓位.仓库,配件仓位.库存 分库存')
            ->where($where)->limit(($page-1)*$pagesize,$pagesize)->select();
        $count=M('配件目录','dbo.','difo')
            ->join('left join 配件仓位 on 配件目录.编号=配件仓位.编号  and  配件仓位.仓库=\''.$cangku.'\'')
            ->where($where)->count();
        $data['Rows']=$product;
        $data['Total']=$count;
        echo json_encode($data);
    
    }
    public function getzhuxiu()
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
    public  function getcarinfo(){
        
        $carno=$_GET['carno'];
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();

        echo json_encode($carinfo);
        
    }
    public  function getpinpai(){
         
        $pinpai=M('常见品牌','dbo.','difo')->where(array('品牌'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public  function getfwgw(){
         
        $zxlist=M('员工目录','dbo.','difo')->where(array('接车员'=>'1','姓名'=>array('like','%'.$_POST['key'].'%')))->select();
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
    public  function getwxlb(){
         
        $wxlb=M('维修类别','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($wxlb);
    
} 
    public  function getkhlb(){
         
        $pinpai=M('客商分类','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    
   }
    public function getcllb(){
        $pinpai=M('车辆类别','dbo.','difo')->where(array('类别'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    public function getdiscount(){
        $discount=M('会员详细信息','dbo.','difo')->where(array('ID'=>$_GET['id']))->find();
        echo json_encode($discount);
    }
    public function waiguan(){
        $pinpai=M('车辆外观','dbo.','difo')->where(array('外观'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    public function yanse(){
        $pinpai=M('车辆颜色','dbo.','difo')->where(array('颜色'=>array('like','%'.$_POST['key'].'%')))->select();
        echo json_encode($pinpai);
    }
    public function getmrproject(){
        $meirong=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目编号'=>array('notlike','%AYC00%'),'项目名称'=>array('like','%'.$_POST['key'].'%')))->order('项目名称')->select();
        echo json_encode($meirong);
    }
    public function getkxproject(){
        $meirong=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目编号'=>array('notlike','%AYC00%'),'项目名称'=>array('like','%'.$_POST['key'].'%')))->order('项目名称')->select();
        echo json_encode($meirong);
    }
   public function deletefile(){
       
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
   public function deletewxfile(){
       
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
   #region 上传文件
   public function fileupload(){
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
   public function wxfileupload(){
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
       $upload->saveRule=null;
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
       $upload->saveRule=null;
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
       $carinfo=M('系统日志','dbo.','difo')->where(array('单据编号'=>$id))->select();
       $data['Rows']=$carinfo;
       $data['Total']=count($carinfo);
       echo json_encode($data);
   }
   public function deleteproject(){
       $projects=$_POST['projects'];
       foreach($projects as $project)
       {
              $num=$project['流水号'];
              M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->delete();
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
       foreach($projects as $project)
       {
          if($project['__status']=='add')
          {
            unset($project['__status']);
            unset($project['流水号']);
            if(isset($project['主修人'])&&$project['主修人']!=''){
                $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$project['主修人']))->field('班组')->find();
                $project['班组']=$yg['班组'];
            }
            M('维修项目','dbo.','difo')->add($project);
            M('维修','dbo.','difo')->where(array('ID'=>$project['ID'],'单据类别'=>'普通单'))->save(array('当前状态'=>'派工'));

          }
          elseif($project['__status']=='update'){
              unset($project['__status']);
              unset($project['ROW_NUMBER']);
              $num=$project['流水号'];
              unset($project['流水号']);
              M('维修项目','dbo.','difo')->where(array('流水号'=>$num))->save($project);
          }

       }
       $this->calprice($projects[0]['ID']);
       echo '保存成功';
   }
  public function saveproduct(){
       $products=$_POST['products'];
       foreach($products as $product)
       {
           if($product['__status']=='add')
          {
              unset($product['__status']);
              unset($product['流水号']);
              M('维修配件','dbo.','difo')->add($product);
              M('维修','dbo.','difo')->where(array('ID'=>$product['ID'],'单据类别'=>'普通单'))->save(array('当前状态'=>'领料'));

          }
          elseif($product['__status']=='update'){
              unset($product['__status']);
              unset($product['ROW_NUMBER']);
              $num=$product['流水号'];
              unset($product['流水号']);
              M('维修配件','dbo.','difo')->where(array('流水号'=>$num))->save($product);
          }
           $this->calprice($products[0]['ID']);
       }
       echo '保存成功';
   }
  public function savewxinfo(){
      $wxinfo=$_POST;
      $xh=$_POST['流水号'];
      unset($wxinfo['流水号']);
      M('维修','dbo.','difo')->where(array('流水号'=>$xh))->save($wxinfo);
      echo '保存成功';
  }

   public function modifyproject()
   {
       $id=$_GET['id'];
       $projectinfo=M('维修项目','dbo.','difo')->where(array('流水号'=>$id))->find();
       $this->assign('projectinfo',json_encode($projectinfo));
       $this->display();
   }
   public function getproject(){
       $id=$_GET['ID'];
       $carinfo=M('维修项目','dbo.','difo')->where(array('ID'=>$id))->select();
       $data['Rows']=$carinfo;
       $data['Total']=count($carinfo);
       echo json_encode($data);
   }
   public function getproduct(){
       $id=$_GET['id'];
       $carinfo=M('维修配件','dbo.','difo')->where(array('ID'=>$id))->select();
       $data['Rows']=$carinfo;
       $data['Total']=count($carinfo);
       echo json_encode($data);
   }
   public function carinfo()
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
        $carno=$_GET['carno'];
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
        $this->assign('carinfo',json_encode($carinfo));
        $this->display();
    }
    public function carsinfo()
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
       $lb='3星客户';
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
           $czinfo['等级']='★★★';
           $czinfo['会员编号']=$number;
           $czinfo['入会日期']=date('Y-m-d',time());
           $czinfo['联系人']=$user['truename'];
           $czinfo['联系电话']=$user['tel'];
           $czinfo['手机号码']=$user['tel']; 
           $czinfo['类别']=$lb;
           M('往来单位','dbo.','difo')->where(array('ID'=>$car['客户ID']))->save($czinfo);
       }
   }
    public function recharge(){

		if(IS_POST){
		    $db = M('Userinfo');
			$wecha_id = $_POST['wecha_id'];
		    $uinfo = $db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
            $mycard = M('Member_card_create')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
			if($db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->setInc('balance',$_POST['price'])){
				$orderid = date('YmdHis',time()).mt_rand(1000,9999);
				M('Member_card_pay_record')->add(array('orderid' => $orderid , 'ordername' => '前台手动充值' ,'note'=>'操作人'.cookie('username'), 'createtime' => time() , 'token' => $this->token , 'wecha_id' => $uinfo['wecha_id'] , 'price' => $_POST['price'] , 'type' => 1 , 'paid' => 1 , 'module' => 'qiantai' , 'paytime' => time() , 'paytype' => 'recharge'));
                if(intval($_POST['price'])>=200){
                    $cardnumber=$this->change($mycard['cardid'],$uinfo['wecha_id']);
                    if($cardnumber!='0'){
                       $this->changecarinfo($uinfo,$cardnumber);
                    }
                }
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
            $user=M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->find();
            if($user['carno1']==""){
                M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno1'=>$carno));
            }elseif($user['carno2']==""){
                M('userinfo')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->save(array('carno2'=>$carno));
            }else{
                echo '最多绑定三辆车';
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
            M('车辆档案','dbo.','difo')->add($item);
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
        M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->delete();
        echo '删除成功';
        exit();
       

    }
   public function getcarno(){
    
        $wecha_id = isset($_GET['wecha_id']) ? htmlspecialchars($_GET['wecha_id']) : '';
        $carinfo=M('member_card_car')->where(array('token' => $this->token,'wecha_id'=>$wecha_id))->select();
        echo json_encode($carinfo);
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
            $searchwhere['carno']=array('like',$searchkey);
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
    public function getgrade(){
        
        if(IS_POST){
            $carno=$_POST['carno'];
            $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
            echo json_encode($carinfo);
            exit;
        }
    }
   public function setprice()
    {
        $this->display();
    }
    private function getprice(){
      
    }
    public function login()
    {
        if(IS_POST)
        {
            $username=$_POST['username'];
            $password=$_POST['password'];
            $user=M('用户管理','dbo.','difo')->where(array('姓名'=>$username))->find();
            if($user&&$user['密码']==$password)
            {
                cookie('username',$username,3600);
                $this->redirect(U('Consume/newindex', array('token' => $this->token)));
                exit();
            }
            else{
                $this->error('用户名密码不正确');
                 exit();
            }
            
        }
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
    private function getcodenum($type)
    {
        $code=M('编号单','dbo.','difo')->where(array('类别'=>"$type",'日期'=>date('Y-m-d', time())))->max('队列');
        $bianhao="$type-".date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
        M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>"$type",'日期'=>date('Y-m-d', time())));
        return $bianhao;
    }

    private function genbill($price,$chezhu,$zhaiyao,$danwei){

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
        $data['单位编号']=$danwei;
        $data['取用预付']=0;
        $data['取用预收']=0;
        $data['本次冲账']=$price;
        $data['单据类别']='应收款';
        $data['取用预存']=0;
        M('日常收支','dbo.','difo')->add($data);
    }

   public function wxcx()
    {  
       if(IS_POST){
           $zhuxiu=$_POST['zhuxiu'];
           $itemid=$_POST['itemid'];
           $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$zhuxiu))->find();

           $wx=M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->find();

           M('维修项目','dbo.','difo')->where(array('ID'=>$wx['ID']))->save(array('主修人'=>$zhuxiu,'班组'=>$yg['班组']));

           $this->genbill($wx['应收金额'],$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);

           $data['当前主修人']=$zhuxiu;
           $data['主修人']=$zhuxiu;
           $data['当前状态']='结束';
           $data['门店']=$yg['部门'];
           $data['出厂时间']=date('Y-m-d H:i',time());
           $data['实际完工']=date('Y-m-d H:i',time());
           $data['结算日期']=date('Y-m-d',time());
           $data['结束日期']=date('Y-m-d',time());
           $data['挂账金额']=0;
           $data['现收金额']=0;
           $data['标志']='已结算';
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
           echo '订单完成';
           exit;
       }
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
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['接车人']=array('like',$searchkey);
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['送修人']=array('like',$searchkey);
            $searchwhere['当前状态']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
         $count=M('车辆资料','dbo.','difo')->where($where)->count();
         
         $pjlist=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目名称'=>array('notlike','%洗车%')))->order('项目名称')->select();
         $Page = new Page($count,15,$parms);
         $show = $Page->show();
         $list=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%美容%')))->select();
         $wxlist=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%维修%')))->select();
         $fwlist=M('员工目录','dbo.','difo')->where(array('接车员'=>'1'))->select();
         $this->assign('list',$list);
         $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
         $this->assign('lblist',$lblist);

         $yelist=M('车辆资料','dbo.','difo')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('序号 desc')->select();
         //$lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
         //$this->assign('lblist',$lblist);
         $this->assign('page',$show);
         $this->assign('pjlist',$pjlist);
         $this->assign('fwlist',$fwlist);
         $this->assign('wxlist',$wxlist);
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
           $item['开工时间']=date('Y-m-d H:i',time());;
           M('维修项目','dbo.','difo')->where(array('ID'=>$itemid))->save($item);
           $data['当前主修人']=$zhuxiu;
           $data['主修人']=$zhuxiu;
           //$data['当前状态']='结束'; 
           $data['门店']=$yg['部门'];
           //$data['出厂时间']=date('Y-m-d H:i',time());
           //$data['实际完工']=date('Y-m-d H:i',time());
           //$data['结算日期']=date('Y-m-d',time());
           //$data['结束日期']=date('Y-m-d',time());
           //$data['挂账金额']=0;
           //$data['现收金额']=0;
           //$data['标志']='已结算';
           M('维修','dbo.','difo')->where(array('ID'=>$itemid))->save($data);
           //$this->genbill($wx['应收金额'],$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);
           echo '派工完成';
           exit;
       }
   }
   public function maintenance()
    {  
       if(IS_POST){
           $zhuxiu=$_POST['zhuxiu'];
           $itemid=$_POST['itemid'];
           $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$zhuxiu))->find();

           $wx=M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->find();

           M('维修项目','dbo.','difo')->where(array('ID'=>$wx['ID']))->save(array('主修人'=>$zhuxiu,'班组'=>$yg['班组']));

           $this->genbill($wx['应收金额'],$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);

           $data['当前主修人']=$zhuxiu;
           $data['当前状态']='结束'; 
           $data['门店']=$yg['部门'];
           $data['出厂时间']=date('Y-m-d H:i',time());
           $data['实际完工']=date('Y-m-d H:i',time());
           $data['结算日期']=date('Y-m-d',time());
           $data['结束日期']=date('Y-m-d',time());
           $data['挂账金额']=0;
           $data['现收金额']=0;
           $data['标志']='已结算';
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
           echo '订单完成';
           exit;
       }
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
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['接车人']=array('like',$searchkey);
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['送修人']=array('like',$searchkey);
            $searchwhere['当前状态']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
         $pjlist=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目名称'=>array('notlike','%洗车%')))->order('项目名称')->select();
         $list=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%美容%')))->select();
         $wxlist=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%维修%')))->select();
         $fwlist=M('员工目录','dbo.','difo')->where(array('接车员'=>'1'))->select();
         $this->assign('list',$list);
         $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
         $this->assign('lblist',$lblist);
         $this->assign('pjlist',$pjlist);
         $this->assign('fwlist',$fwlist);
         $this->assign('wxlist',$wxlist);
         $this->display();
    }
   public function maintenancerecord()
    {  
       if(IS_POST){
           $zhuxiu=$_POST['zhuxiu'];
           $itemid=$_POST['itemid'];
           $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$zhuxiu))->find();

           $wx=M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->find();

           M('维修项目','dbo.','difo')->where(array('ID'=>$wx['ID']))->save(array('主修人'=>$zhuxiu,'班组'=>$yg['班组']));

           $this->genbill($wx['应收金额'],$wx['车主'],'维修收款('.$wx['业务编号'].')',$wx['客户ID']);

           $data['当前主修人']=$zhuxiu;
           $data['当前状态']='结束'; 
           $data['门店']=$yg['部门'];
           $data['出厂时间']=date('Y-m-d H:i',time());
           $data['实际完工']=date('Y-m-d H:i',time());
           $data['结算日期']=date('Y-m-d',time());
           $data['结束日期']=date('Y-m-d',time());
           $data['挂账金额']=0;
           $data['现收金额']=0;
           $data['标志']='已结算';
           M('维修','dbo.','difo')->where(array('流水号'=>$itemid))->save($data);
           echo '订单完成';
           exit;
       }
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
        if($searchkey){       
            $searchwhere['制单人']=array('like',$searchkey);
            $searchwhere['接车人']=array('like',$searchkey);
            $searchwhere['维修类别']=array('like',$searchkey);
            $searchwhere['车主']=array('like',$searchkey);
            $searchwhere['车牌号码']=array('like',$searchkey);
            $searchwhere['客户类别']=array('like',$searchkey);
            $searchwhere['联系人']=array('like',$searchkey);
            $searchwhere['联系电话']=array('like',$searchkey);
            $searchwhere['送修人']=array('like',$searchkey);
            $searchwhere['当前状态']=array('like',$searchkey);
            $searchwhere['_logic']='OR';
            $where['_complex']=$searchwhere;

        }
         $pjlist=M('项目目录','dbo.','difo')->where(array('类别'=>'洗车美容','项目名称'=>array('notlike','%洗车%')))->order('项目名称')->select();
         $list=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%美容%')))->select();
         $wxlist=M('员工目录','dbo.','difo')->where(array('职务'=>array('like','%维修%')))->select();
         $fwlist=M('员工目录','dbo.','difo')->where(array('接车员'=>'1'))->select();
         $this->assign('list',$list);
         $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
         $this->assign('lblist',$lblist);
         $this->assign('pjlist',$pjlist);
         $this->assign('fwlist',$fwlist);
         $this->assign('wxlist',$wxlist);
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
         $parms=$_GET;
         if (isset($_GET['searchkey'])&&trim($_GET['searchkey'])){
             $searchkey='%'.trim($_GET['searchkey']).'%';
         }
         if($_GET['lb']&&trim($_GET['lb'])!='')
         {
             $where['维修类别']=trim($_GET['lb']);
             
         }
         if($_GET['bm']&&trim($_GET['bm'])!='')
         {
             $where['班组']=array('like','%'.trim($_GET['bm'].'%'));
             
         }
         if($_GET['startDate']&&trim($_GET['startDate'])!='')
         {
             $where['时间']=array('egt',trim($_GET['startDate']));
             
         }
         if($_GET['endDate']&&trim($_GET['endDate'])!='')
         {
             $where['时间']=array('elt',trim($_GET['endDate']));
             
         }
         if(trim($_GET['startDate'])!=''&&trim($_GET['endDate'])!='')
         {
             $where['时间']=array('BETWEEN',array(trim($_GET['startDate']),trim($_GET['endDate'])));
             
         }
         if($_GET['zhuxiu']&&trim($_GET['zhuxiu'])!='')
         {
             $where['主修人']=trim($_GET['zhuxiu']);
             
         }
         if($searchkey){       
             $searchwhere['维修类别']=array('like',$searchkey);
             $searchwhere['主修人']=array('like',$searchkey);
             $searchwhere['_logic']='OR';
             $where['_complex']=$searchwhere;

         }
         $count=M('个人业绩表','dbo.','difo')->join('员工目录 on 个人业绩表.主修人=员工目录.姓名')->where($where)->count();
         $Page = new Page($count,15,$parms);
         $show = $Page->show();
         $zxlist=M('员工目录','dbo.','difo')->where(array('技术员'=>'1'))->order('姓名')->select();
         $yelist=M('个人业绩表','dbo.','difo')->join('员工目录 on 个人业绩表.主修人=员工目录.姓名')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('时间 desc,服务车辆数 desc')->select();
         $this->assign('page',$show);
         $this->assign('count',$count);
         $this->assign('yelist',$yelist);
         $this->assign('zxlist',$zxlist);
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
               $where['类别']=array('like','%'.trim($_GET['lb'].'%'));
               
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
   public function mrrecord()
   {
       $carno=$_POST['carno'];
       $person=$_POST['person'];
       $fwgw=$_POST['fwgw'];
       $type=$_POST['project'];
       $wxinfo=$_POST['wxinfo'];
       if(isset($wxinfo))
       {
           $result=$this->genwxrecord($wxinfo['车牌号码'],$wxinfo['维修类别'],'汽车美容',$wxinfo['主修人'],
               $wxinfo['接车人'],$wxinfo['里程表'],$wxinfo['油位表'],$wxinfo['联系人'],$wxinfo['联系电话'],$wxinfo['轮胎规格']);
       }
       else{
           $result=$this->genwxrecord($carno,$type,'汽车美容',$person,$fwgw);
       }

       echo $result;
   }
   public function wxrecord()
   {
       if(IS_POST){
           $carno=$_POST['carno'];
           $person=$_POST['person'];
           $fwgw=$_POST['fwgw'];
           $wxtype=$_POST['wxtype'];
           $licheng=$_POST['licheng'];
           $youwei=$_POST['youwei'];
           $luntai=$_POST['luntai'];
            $wxinfo=$_POST['wxinfo'];
            if(isset($wxinfo))
            {
                $result=$this->genwxrecord($wxinfo['车牌号码'],null,$wxinfo['维修类别'],$wxinfo['主修人'],
              $wxinfo['接车人'],$wxinfo['里程表'],$wxinfo['油位表'],$wxinfo['联系人'],$wxinfo['联系电话'],$wxinfo['轮胎规格'],$wxinfo['故障描述']);
            }
            else{
                $result=$this->genwxrecord($carno,'',$wxtype,$person,$fwgw,$licheng,$youwei,$_POST['lxr'],$_POST['phone'],$luntai,$_POST['fault']);
            }
            echo $result;
            exit;
          
       }
       else{
           $this->display();
       }
   }
   public function haswxrecord()
   {    
       $carno=$_POST['carno'];
       $wxtype=$_POST['wxtype'];
       $wxrecord=M('维修','dbo.','difo')->where(array('车牌号码'=>$carno,'维修类别'=>$wxtype,'_string'=>"当前状态 not in ('结束','取消')"))->find();
       if(isset($wxrecord)){
          
           echo 1;
           exit;
       }
       echo 0;
       exit;
   }
   public function getviewnotice()
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
   public function getnotices(){
    
       $notices= M('member_card_notice')->query('SELECT *,(SELECT count(1) from tp_userinfo where   carno<>\'\') total from tp_member_card_notice a left join (
SELECT noticeid,count(1) num from tp_member_card_noticedetail GROUP BY noticeid
) b on a.id=b.noticeid order by time desc');
       $data['Rows']=$notices;
       $data['Total']=count($notices);
       echo json_encode($data);

   }
   public function notices(){
       $this->display();
   }
   private function genwxrecord($carno,$type='AYC0002',$wxlb='蜡水洗车',$person,$fwgw,$licheng=null,$youwei=null,$lxr=null,$phone=null,$luntai=null,$fault=null){
      
        $data=M('维修','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
        $data['流水号']=null;
        unset($data['流水号']);
        unset($data['ROW_NUMBER']);

        $code=M('编号单','dbo.','difo')->where(array('类别'=>'WX','日期'=>date('Y-m-d', time())))->max('队列');
        $bianhao='WX-'.date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
        M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>'WX','日期'=>date('Y-m-d', time())));
        $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
        if($type!=''){
            $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>$type))->find();
        }
        $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$fwgw))->find();
              
        if($carinfo)
        {
            $carinfo['最近维修']=date('Y-m-d',time());
            $carinfo['服务顾问']=$fwgw;
            $carinfo['维修次数']=intval($carinfo['维修次数'])+1;
            $carinfo['轮胎规格']=$luntai;
            if(isset($licheng)){
                $carinfo['里程']=$licheng;
            }
            if($wxlb=='常规保养'){
                $carinfo['最近保养']=date("Y-m-d",time());
                $carinfo['下次保养']=date("Y-m-d",strtotime("+182 day"));
                if(isset($licheng)){
                    $carinfo['保养里程']=$licheng;
                    $carinfo['下次保养里程']=intval($licheng)+5000;
                }
            }
            unset($carinfo['流水号']);
            unset($carinfo['ROW_NUMBER']);
            M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->save($carinfo);
            foreach($data as $key=>$value){
                $data[$key]=$carinfo[$key];
            }
        }
        else{ 
            if($wxlb!='普通快修'){
                $czinfo['名称']=$carno;
                $czinfo['客户']=1;
                $czinfo['类别']='1星客户';
                $czinfo['手机号码']=$phone;
                $czinfo['联系电话']=$phone;
                $czinfo['联系人']=$lxr;
                $czinfo['ID']=$this->getcode(18,0,0);
                M('往来单位','dbo.','difo')->add($czinfo);
                $carinfo['车主']=$carno;
                $carinfo['车牌号码']=$carno;
                $carinfo['轮胎规格']=$_POST['luntai'];
                $carinfo['客户ID']=$czinfo['ID'];
                $carinfo['客户类别']='1星客户';
                $carinfo['最近维修']=date('Y-m-d',time());
                $carinfo['服务顾问']=$fwgw;
                $carinfo['维修次数']=1;
                $carinfo['手机号码']=$phone;
                $carinfo['联系电话']=$phone;
                $carinfo['联系人']=$lxr;
                if(isset($licheng)){
                    $carinfo['里程']=$licheng;
                }
                if($wxlb=='常规保养'){
                    $carinfo['常规保养数']=5000;
                    $carinfo['最近保养']=date("Y-m-d",time());
                    $carinfo['下次保养']=date("Y-m-d",strtotime("+182 day"));
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
        $data['ID']=$this->getcode(10,0,1);
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
        $data['当前状态']='结算';
        $data['维修状态']='结算';
        if(!in_array($wxlb,array('蜡水洗车','汽车美容','普通快修'))){
            $data['单据类别']='普通单';
            $data['当前状态']='报价';
            $data['维修状态']='报价';
        }
        $data['当前主修人']=$person;
        $data['主修人']=$person;
        $data['门店']=$yg['部门'];
        $data['结算客户']=$carinfo['车主'];;
        $data['结算客户ID']=$carinfo['客户ID'];
        unset($data['出厂时间']);
        unset($data['实际完工']);
        unset($data['购买日期']);
        unset($data['结束日期']);
        unset($data['预计完工']);
        $data['进厂时间']=date('Y-m-d',time());
        $data['结算日期']=date('Y-m-d',time());
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
        if(in_array($wxlb,array('蜡水洗车','汽车美容'))){
            $row=array();
            $row['ID']=$data['ID'];
            $row['项目编号']=$xm['项目编号'];
            $row['项目名称']=$xm['项目名称'];
            $row['维修工艺']=$xm['维修工艺'];
            $row['结算方式']='客付';
            $row['工时']=$xm['标准工时'];
            $row['单价']=$xm['标准金额'];
            $row['金额']=$xm['标准金额'];
            $row['虚增类别']='';
            $row['折扣']=1;
            $row['提成工时']=1;
            $row['提成金额']=1;
            $row['主修人']=$person;
            $row['班组']=$yg['班组'];
            $row['开工时间']=date('Y-m-d H:i',time());
            //$row['完工时间']=date('Y-m-d H:i',time());
            $row['是否同意']=1;
            $row['已维修']='0小时'; 

            M('维修项目','dbo.','difo')->add($row);
        }
        $this->writeLog($data['ID'],$bianhao,$wxlb,'维修录入');
        return '提交成功';

       
   } 
   private function writeLog($id,$ywbh,$type,$content)
   {
       $username=cookie('username');
       $sql="exec [dbo].[Proc_WriteLog] '$id','$ywbh','$type','$content','$username'";
       M('维修项目','dbo.','difo')->execute($sql);
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
         
         $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$wxinfo['主修人']))->find();
         $data['接车人']=$wxinfo['接车人'];         
         $data['ID']=$this->getcode(18,0,1);
         $data['制单日期']=date('Y-m-d',time());
         $data['制单人']=cookie('username');
         $data['维修类别']='蜡水洗车';
         $data['保修类别']='保外';
         $data['单据类别']='快修单';
         $data['当前主修人']=$wxinfo['主修人'];
         $data['主修人']=$wxinfo['主修人'];
         $data['结算客户']=$carinfo['车主'];;
         $data['结算客户ID']=$carinfo['客户ID'];
         //$data['送修人电话']=$carinfo['手机号码'];
         $data['当前状态']='结算';
         $data['维修状态']='结算';
         $data['预计完工']=date('Y-m-d',time());
         $data['进厂时间']=date('Y-m-d',time());
         unset($data['出厂时间']);
         unset($data['下次保养']);
         unset($data['实际完工']);
         unset($data['购买日期']);
         unset($data['结束日期']);
         $data['结算日期']=date('Y-m-d',time());
         $data['报价金额']=0;
         $data['应收金额']=0; 
         if(strpos($data['车主'], 'AYC') === 0){
             $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0001'))->find();
         }elseif($carinfo['客户类别']=='VIP客户'){
             $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0004'))->find();
         }
         elseif(strpos($carinfo['客户类别'],'定点签约')===0){
             $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0003'))->find();
             $xm['标准金额']=$money;
             $data['报价金额']=$money;
             $data['应收金额']=$money;
         }
         elseif($carinfo['客户类别']=='三方合作'){
             $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0007'))->find();
         }
         else{
             $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0002'))->find();
             $data['报价金额']=35;
             $data['应收金额']=35;
         }
         $data['业务编号']=$bianhao;
         M('维修','dbo.','difo')->add($data);
         $this->writeLog($data['ID'],$bianhao,'维修','前台接车');
         $row=array();
         $row['ID']=$data['ID'];
         $row['项目编号']=$xm['项目编号'];
         $row['项目名称']=$xm['项目名称'];
         $row['维修工艺']=$xm['维修工艺'];
         $row['结算方式']='客付';
         $row['工时']=$xm['标准工时'];
         $row['单价']=$xm['标准金额'];
         $row['金额']=$xm['标准金额'];
         $row['折扣']=1;
         $row['提成工时']=1;
         $row['提成金额']=1;
         $row['主修人']=$wxinfo['主修人'];
         $row['班组']=$yg['班组'];
         $row['开工时间']=date('Y-m-d H:i',time());
         //$row['完工时间']=date('Y-m-d H:i',time());
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
    public function record()
    {
        $list=M('员工目录','dbo.','difo')->where(array('部门'=>array('like','%美容部')))->select();
        $this->assign('list',$list);
        $lblist=M('车辆档案','dbo.','difo')->Distinct(true)->field('客户类别')->order('客户类别')->select();
        $this->assign('lblist',$lblist);
        if(IS_POST)
        {
            $carno=$_POST['carno'];
            $person=$_POST['person'];
            $money=$_POST['money'];
            //M('维修','','difo')->where();
            $data=M('维修','dbo.','difo')->where(array('车牌号码'=>'0000'))->find();
            $data['流水号']=null;
            unset( $data['流水号']);
            unset($data['ROW_NUMBER']);
            $code=M('编号单','dbo.','difo')->where(array('类别'=>'WX','日期'=>date('Y-m-d', time())))->max('队列');
            $bianhao='WX-'.date('ymd', time()).'-'.str_pad(($code+1),3,'0',STR_PAD_LEFT);
            M('编号单','dbo.','difo')->add(array('单据编号'=>$bianhao,'队列'=>($code+1),'类别'=>'WX','日期'=>date('Y-m-d', time())));
            $carinfo=M('车辆档案','dbo.','difo')->where(array('车牌号码'=>$carno))->find();
            if($carinfo)
            {
               $data['车牌号码']=$carno;
               $data['送修人']=$carinfo['手机号码'];
               foreach($data as $key=>$value){
                   $data[$key]=$carinfo[$key];
               }
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
           
            $yg=M('员工目录','dbo.','difo')->where(array('姓名'=>$person))->find();
            $data['接车人']='刘述庆';
            if($yg&&strpos($yg['班组'], '塘坑') === 0){
                $data['接车人']='廖吉洪';
            }
            $data['ID']=$this->getcode(10,0,1);
            $data['制单日期']=date('Y-m-d',time());
            $data['制单人']=cookie('username');
            $data['维修类别']='蜡水洗车';
            $data['保修类别']='保外';
            $data['单据类别']='快修单';
            $data['当前主修人']=$person;
            $data['主修人']=$person;
            $data['结算客户']=$carinfo['车主'];;
            $data['结算客户ID']=$carinfo['客户ID'];
            $data['当前状态']='结算';
            $data['维修状态']='结算';
            $data['进厂时间']=date('Y-m-d',time());
            $data['结算日期']=date('Y-m-d',time());
            unset($data['出厂时间']);
            unset($data['下次保养']);
            unset($data['实际完工']);
            unset($data['购买日期']);
            unset($data['结束日期']);
            $data['报价金额']=0;
            $data['应收金额']=0; 
            if(strpos($data['车主'], 'ACG') === 0||(strpos($data['车主'], 'AYC') === 0)){
                $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0001'))->find();
            }elseif($carinfo['客户类别']=='VIP客户'){
                 $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0004'))->find();
            }
            elseif(strpos($carinfo['客户类别'],'定点签约')===0){
                $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0003'))->find();
                $xm['标准金额']=$money;
                $data['报价金额']=$money;
                $data['应收金额']=$money;
            }
            elseif($carinfo['客户类别']=='三方合作'){
                 $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0007'))->find();
            }
            else{
                 $xm=M('项目目录','dbo.','difo')->where(array('项目编号'=>'AYC0002'))->find();
                 $data['报价金额']=35;
                 $data['应收金额']=35;
            }
            $data['业务编号']=$bianhao;
            M('维修','dbo.','difo')->add($data);
            $this->writeLog($data['ID'],$bianhao,'维修','前台接车');
            $row=array();
            $row['ID']=$data['ID'];
            $row['项目编号']=$xm['项目编号'];
            $row['项目名称']=$xm['项目名称'];
            $row['维修工艺']=$xm['维修工艺'];
            $row['结算方式']='客付';
            $row['工时']=$xm['标准工时'];
            $row['单价']=$xm['标准金额'];
            $row['金额']=$xm['标准金额'];
            $row['折扣']=1;
            $row['提成工时']=1;
            $row['提成金额']=1;
            $row['主修人']=$person;
            $row['班组']=$yg['班组'];
            $row['开工时间']=date('Y-m-d H:i',time());
            $row['是否同意']=0;
            $row['已维修']='0小时'; 
            M('维修项目','dbo.','difo')->add($row);
            echo '提交成功';
            exit;
        }
       
        $this->display();
      

    }
}

