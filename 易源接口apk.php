<?php
class QQValue
{
    private $showapi_appid  = 'xxx';  //替换此值,在官网的"我的应用"中找到相关值;
    private $showapi_secret = 'xxxxxx';  //替换此值
    private $paramArr       = array();  // 参数

    // 获取参数
    function __construct($_params)
    {
        $this->paramArr = $_params;
        $this->paramArr['showapi_appid'] = $this->showapi_appid;
    }

    //创建参数(包括签名的处理)
    private function createParam () {
        $paraStr = "";
        $signStr = "";
        ksort($this->paramArr);
        foreach ($this->paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key.$val;
                $paraStr .= $key.'='.urlencode($val).'&';
            }
        }
        $signStr .= $this->showapi_secret;//排好序的参数加上secret,进行md5
        $sign = strtolower(md5($signStr));
        $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
        return $paraStr;
    }

    public function get_data()
    {
        $param = $this->createParam();
        $url = 'http://route.showapi.com/1321-1?'.$param;
        $result = file_get_contents($url);
        return $result;
    }
}



// 调用
header("Content-Type:text/html;charset=UTF-8");
date_default_timezone_set("PRC");
$paramArr = array(
    'QQ'=> "784255790"
);
$_qq  = new QQValue($paramArr);
$_result = $_qq->get_data();
$_result = json_decode($_result,true);
if($_result['showapi_res_code'] == '0')
{
    echo '<img src="'.$_result['showapi_res_body']['data'].'">';
}
else
{
    echo '接口调用失败:'.$_result['showapi_res_error'];
}