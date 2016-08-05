<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>
    <?php if($zd['status'] == 1): echo ($zd['code']); endif; ?>
    <title><?php echo ($tpl["wxname"]); ?></title>
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css" />
    <script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css" />
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
    <link href="http://at.alicdn.com/t/font_1452909907_4403944.css" rel="stylesheet" type="text/css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .icon1 {
            font-size: 26px;
            line-height: 10px;
            margin: 10px 0;
            color: white;
            -webkit-transition: font-size 0.25s ease-out 0s;
            -moz-transition: font-size 0.25s ease-out 0s;
            transition: font-size 0.25s ease-out 0s;
        }

            .icon1:hover {
                color: red;
            }

        .device {
            margin: 0px;
        }

        .index-nav {
            padding: 0.88652rem 0 0 0;
            font-size: 0;
            background-color: #FFF;
            border-bottom: #DBDBDB 1px solid;
            height: 100%;
        }
        .index-nav-item {
            display: inline-block;
            width: 25%;
            line-height: 1.5em;
            margin-bottom: 0.88652rem;
            vertical-align: middle;
            font-size: 0.8rem;
            text-align: center;
        }
         .index-nav-item-link {
            color: #929292;
        }

            .index-nav-item-link:visited {
                color: #929292;
            }

            .index-nav-item-link:focus {
                color: #929292;
            }

            .index-nav-item-link:hover {
                color: #929292;
            }

            .index-nav-item-link:active {
                color: #929292;
            }

            .index-nav-item-link i {
                display: block;
                width: 2rem;
                height: 2rem;
                line-height: 2rem;
                margin: 0 auto 0.22163rem;
                border-radius: 100%;
            }
           .index-nav-item-link i:after {
                    font-size: 2.21631rem;
                    height: 3.98936rem;
                    line-height: 3.98936rem;
                }

        body .index-nav > li:nth-child(1) i {
            background-color: rgba(210,170,84,0.9);
        }

        body .index-nav > li:nth-child(2) i {
            background-color: rgba(108,160,182,0.9);
        }

        body .index-nav > li:nth-child(3) i {
            background-color: rgba(129,165,93,0.9);
        }

        body .index-nav > li:nth-child(4) i {
            background-color: rgba(209,109,82,0.9);
        }

        body .index-nav > li:nth-child(5) i {
            background-color: rgba(109,108,168,0.9);
        }

        body .index-nav > li:nth-child(6) i {
            background-color: rgba(196,126,184,0.9);
        }

        body .index-nav > li:nth-child(7) i {
            background-color: rgba(120,180,135,0.9);
        } 

        body .index-nav > li:nth-child(8) i {
            background-color: rgba(210,84,145,0.9);
        }

        body .index-nav > li:nth-child(9) i {
            background-color: rgba(160,113,99,0.9);
        }

        body .index-nav > li:nth-child(10) i {i
            background-color: rgba(160,158,99,0.9);
        }

        body .index-nav > li:nth-child(11) i {
            background-color: rgba(160,180,64,32);
        }

        body .index-nav > li:nth-child(12) i {
            background-color: rgba(160,158,109,29);
        }
    </style>
</head>
<body>
    <div class="page" style="margin:0px">
        <nav class="bar bar-tab">
            <a class="tab-item active" data-no-cache="true" href="<?php echo U('Store/cats',array('token' => $_GET['token'], 'catid' => $hostlist['id'], 'wecha_id' => $wecha_id, 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-home"></span>
                <span class="tab-label">首页</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Card/index',array('token' => $_GET['token'], 'wecha_id' => $wecha_id));?>">
                <span class="icon icon-friends"></span>
                <span class="tab-label">会员中心</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="#">
                <span class="icon icon-message" ></span>
                <span class="tab-label">消息</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-cart"></span>
                <?php if($productcount != 0): ?><span class="badge" id='badge'><?php echo ($productcount); ?></span>
                    <?php else: ?>
                    <span class="badge" id='badge' style="display:none"><?php echo ($productcount); ?></span><?php endif; ?>
                <span class="tab-label">购物车</span>
            </a>
            <a class="tab-item" data-no-cache="true" href="<?php echo U('Store/myinfo',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'], 'cid' => $cid, 'twid' => $twid, 'cid' => $cid));?>">
                <span class="icon icon-me"></span>
                <span class="tab-label">我</span> 
            </a>
        </nav>
        <div class="content">
            <div class="card" style="margin:0px">
                <div class="card-content">
                    <div class="swiper-container" data-space-between='10'>
                        <div class="swiper-wrapper">
                            <?php if(is_array($flash)): $i = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$so): $mod = ($i % 2 );++$i;?><div class="swiper-slide"><a href="<?php echo ($so["url"]); ?>"><img src="<?php echo ($so["img"]); ?>" style="width:100%;height:100%" /></a></div><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
            <div class="card" style="margin:0px">
                <div class="device">
                    <ul class="index-nav">
                        <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="index-nav-item">
                                <a class="index-nav-item-link" href="<?php if($vo['url'] == ''): echo U('Wap/Index/lists',array('classid'=>$vo['id'],'token'=>$vo['token'])); else: echo (htmlspecialchars_decode($vo["url"])); endif; ?>">
                                    <i class="icon1 iconfont <?php echo ($vo["img"]); ?>"></i>
                                    <span class="tab-label" style="font-size:0.625rem"><?php echo ($vo["name"]); ?></span>
                                </a>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        <li class="index-nav-item">
                            <a class="index-nav-item-link" href="<?php echo U('Wap/Index/lists',array('classid'=>$vo['id'],'token'=>$vo['token']));?>">
                                <i class="icon1 iconfont icon-baoyang0"></i>
                                <span class="tab-label" style="font-size:0.625rem">保险</span>
                            </a>
                        </li>
                        <li class="index-nav-item">
                            <a class="index-nav-item-link" href="<?php echo U('Wap/Index/lists',array('classid'=>$vo['id'],'token'=>$vo['token']));?>">
                                <i class="icon1 iconfont icon-jiaotongfakuandaiban"></i>
                                <span class="tab-label" style="font-size:0.625rem">违章处理</span>
                            </a>
                        </li>
                         <li class="index-nav-item">
                            <a class="index-nav-item-link" href="/index.php?g=Wap&m=Card&a=companyDetail&token=<?php echo ($token); ?>&wecha_id=<?php echo ($wecha_id); ?>">
                                <i class="icon1 iconfont icon-shangjia"></i>
                                <span class="tab-label" style="font-size:0.625rem">商家</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(function () {
            'use strict';
            //对话框
            $.init();
        });
    </script>

    <!-- share-->
    
	<?php if(ACTION_NAME == 'index'): ?><script type="text/javascript">
			window.shareData = {  
					"moduleName":"Index",
					"moduleID": "0",
					"imgUrl": "<?php echo ($homeInfo["picurl"]); ?>", 
					"timeLineLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"sendFriendLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"weiboLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token']));?>",
					"tTitle": "<?php echo ($homeInfo["title"]); ?>",
					"tContent": "<?php echo ($homeInfo["info"]); ?>"
				};
		</script>
	<?php else: ?>
		<script type="text/javascript">
			window.shareData = {  
				"moduleName":"NewsList",
				"moduleID": "<?php echo (intval($_GET['classid'])); ?>",
				"imgUrl": "<?php echo ($thisClassInfo["img"]); ?>", 
				"timeLineLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"sendFriendLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"weiboLink": "<?php echo C('site_url'); echo U(MODULE_NAME/ACTION_NAME,array('token'=>$_GET['token'],'classid'=>$_GET['classid']));?>",
				"tTitle": "<?php echo ($thisClassInfo["name"]); ?>",
				"tContent": "<?php echo ($thisClassInfo["info"]); ?>"
			};
		</script><?php endif; ?>
	
<?php echo ($shareScript); ?>
</body>
</html>