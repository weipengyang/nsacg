<?php
/**
 *项目公共配置
 *@package pigcms
 *@author pigcms
 **/
return array(
	'LOAD_EXT_CONFIG' 		=> 'db,info,email,safe,upfile,cache,route,app,alipay,sms,rippleos_key,platform',		
	'APP_AUTOLOAD_PATH'     =>'@.ORG',
	'OUTPUT_ENCODE'         =>  true, 			//页面压缩输出
	'PAGE_NUM'				=> 15,
	/*Cookie配置*/
	'COOKIE_PATH'           => '/',     		// Cookie路径
    'COOKIE_PREFIX'         => '',      		// Cookie前缀 避免冲突
	/*定义模版标签*/
	'TMPL_L_DELIM'   		=>'{pigcms:',			//模板引擎普通标签开始标记
	'TMPL_R_DELIM'			=>'}',				//模板引擎普通标签结束标记
    'CORPID'                =>'ding36884f8a82159575',//企业CORPID
    'CORPSECRET'            =>'tBwwroDNnnRDkC3k5Cqf3CHtVBdPAt0xvnBLqCz0FidKYDWT-Xdz9kPwHySB4Zx_',
    'SSOsecret'               =>'iP0pnIqnEpo1l0v3CuTXU1fD4NapwA-oH3UB8Opryd1-fZmF-hXDQ0t6SgUt3o2i',//微应用id
    'AGENTID'               =>'29443806',//微应用id
);
?>