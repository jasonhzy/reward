<?php
/**
* 	配置账号信息
*   注意:BeeCloud 结合自身系统修改了微信配置
*/

class WxPayConf_pub {
	//=======【基本信息设置】=====================================
	//微信小程序APPID
	const APPID = 'xxx';
	//受理商ID，身份标识
	const MCHID = 'yyy';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'xxx';
	//微信小程序APPSECRET
	const APPSECRET = 'xxx';

	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    //官方渠道
	const JS_API_CALL_URL = '';
	const NOTIFY_URL = 'http://www.example.com/reward/data/wxmini.php?type=notify';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>