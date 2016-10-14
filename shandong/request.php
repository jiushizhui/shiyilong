<?php
$appsecret = 'f57a3d4fdbff47509cd6dce3582e948b';
// $appsecret = '9af1dde3d6e140ef9920fd709bcad53d'; #测试用
$EnterpriseBossId = '5348029542847';
// $EnterpriseBossId = '1111'; #测试用
$MB = '10'; #购买的流量
$tel = '13805331100'; #购买流量的手机号

$product = '{"2048":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed870\u5143\u5305","ProductCode":"108707","Size":"2048"},"1024":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed850\u5143\u5305","ProductCode":"108706","Size":"1024"},"500":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed830\u5143\u5305","ProductCode":"108705","Size":"500"},"11264":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed8280\u5143\u5305","ProductCode":"108711","Size":"11264"},"6144":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed8180\u5143\u5305","ProductCode":"108710","Size":"6144"},"4096":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed8130\u5143\u5305","ProductCode":"108709","Size":"4096"},"3072":{"UserID":"5368047306282","ProductName":"\u6d41\u91cf\u7edf\u4ed8100\u5143\u5305","ProductCode":"108708","Size":"3072"}}';
$product = json_decode($product,true);
print_r($product);
list($s1, $s2) = explode(' ', microtime()); 
$mictime = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 

$body = '<BusiData><CreateTime>'.$mictime.'</CreateTime><ChargePhoneNum>'.$tel.'</ChargePhoneNum><UserID>'.$product[$MB]['UserID'].'</UserID><ProductCode>'.$product[$MB]['ProductCode'].'</ProductCode><ChargeNum>1</ChargeNum></BusiData>';
$VerifyCode = md5($body.$appsecret);
$xml = '<?xml version="1.0" encoding="utf-8"?><AdvPay><PubInfo><Version>1</Version><EnterpriseBossId>'.$EnterpriseBossId.'</EnterpriseBossId><VerifyCode>'.$VerifyCode.'</VerifyCode></PubInfo>'.$body.'</AdvPay>';
echo $xml;
include 'model/curl.php';
$curl =  new curl();
$url = 'http://shandong.4ggogo.com/sd-web/open/ChargeFlow'; //正式地址
// $testurl = 'http://shandongtest.4ggogo.com/sd-web/open/ChargeFlow'; //测试地址
$data= $xml;
// echo $curl->_curl($testurl,$data,'POST');
echo $curl->_curl($url,$data,'POST');