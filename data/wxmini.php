<?php
/**
 * @desc: 微信小程序支付demo
 *
 * 支付参考文档：https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_3&index=1
 * 小程序开发参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/index.html?t=2017621
 *      1、wx.request(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/network-request.html
 *      2、wx.requestPayment(OBJECT)参考文档: https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-pay.html#wxrequestpaymentobject
 *      3、获取openid参考文档：https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject
 *
 * @author: jason
 * @since:  2017-10-08 18:59
 */
$ret = array('resultCode' => 1);
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
try{
    switch($type){
        case 'openid':
            //小程序的appid和appsecret
            $appid = 'xxxx';
            $appsecret = 'yyyy';
            $code = isset($_POST['code']) ? trim($_POST['code']) : '';
            if(empty($code)){
                $ret['errMsg'] = '登录凭证code获取失败';
                exit(json_encode($ret));
            }
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";

            $json = json_decode(file_get_contents($url));
            if(isset($json->errcode) && $json->errcode){
                $ret['errMsg'] = $json->errcode.', '.$json->errmsg;
                exit(json_encode($ret));
            }
            $openid = $json->openid;

            $ret['resultCode'] = 0;
            $ret['openid'] = $openid;
            break;
        case 'pay':
            include_once('./WxPayPubHelper/WxPayPubHelper.php');

            $total = isset($_POST['total']) ? trim($_POST['total']) : 0;
            $openid = isset($_POST['openid']) ? trim($_POST['openid']) : '';
            if(empty($openid)){
                $ret['errMsg'] = '缺少参数openid';
                exit(json_encode($ret));
            }

            $order = new UnifiedOrder_pub();
            $order->setParameter("openid", $openid);//商品描述
            $order->setParameter('out_trade_no', 'phpdemo' . time());
            $order->setParameter('total_fee', (int)($total * 100));
            $order->setParameter('trade_type', 'JSAPI');
            $order->setParameter('body', 'PHP微信小程序支付测试');
            $order->setParameter('notify_url', WxPayConf_pub::JS_API_CALL_URL);

            $prepay_id = $order->getPrepayId();
            $jsApi = new JsApi_pub();
            $jsApi->setPrepayId($prepay_id);
            $jsApiParams = json_decode($jsApi->getParameters());

            $ret['resultCode'] = 0;
            $ret['params'] = array(
                'appid' => $jsApiParams->appId,
                'timestamp' => $jsApiParams->timeStamp,
                'nonce_str' => $jsApiParams->nonceStr,
                'sign_type' => $jsApiParams->signType,
                'package' => $jsApiParams->package,
                'pay_sign' => $jsApiParams->paySign,
            );
            break;
        case 'rank':
            //$openid = isset($_POST['openid']) ? trim($_POST['openid']) : '';
            $conn = @mysqli_connect('localhost', 'root', '07061108', 'reward');
            if(mysqli_connect_errno($conn)){
                $ret['errMsg'] = mysqli_connect_error();
                exit(json_encode($ret));
            }
            $sql = 'select pay_user.openid, nickname, sum(total_fee) as total_fee, avatar_url 
                    from pay_user 
                    inner join pay_detail on pay_user.openid = pay_detail.openid
                    group by pay_user.openid
                    order by total_fee desc';
            $result = mysqli_query($conn, $sql);
            //通过循环获得数组
            $rank = 1;
            while($row = mysqli_fetch_assoc($result)){
                //$row['self'] = $openid && $openid == $row['openid'] ? true : false;
                $row['total_fee'] = number_format($row['total_fee'] / 100, 2);
                $row['rank'] = $rank++;
                $list[] = $row;
            }
            //关闭连接
            mysqli_close($conn);

            $ret['resultCode'] = 0;
            $ret['list'] = $list;
            break;
        case 'notify':
            $xmlStr = $GLOBALS['HTTP_RAW_POST_DATA']; // 这里拿到微信返回的数据结果
            if(empty($xmlStr)){
                $xmlStr = file_get_contents("php://input");
            }
            $info = (array)simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            if($info['result_code'] == 'SUCCESS') {
                //TODO...
            }
            break;
        default :
            $ret['errMsg'] = 'No this type : ' . $type;
            break;
    }
}catch(Exception $e){
    $ret['errMsg'] = $e->getMessage();
}
exit(json_encode($ret));