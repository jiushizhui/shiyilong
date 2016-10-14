<?php
header("Content-Type: text/html; charset=UTF-8");

if($_GET['sign'] != md5($_GET['OrderNo'].substr($_GET['OrderNo'],-3))){
	echo '非法';exit;
}
$OrderNo = $_GET['OrderNo'];

require_once ('../ebusclient/RefundRequest.php');
$con = mysql_connect("localhost","root","BJacms!@#$%^");
mysql_select_db("mysql_52mb", $con);
$sql = "select wsl_money,wsl_content from 52_wxshoplogs where wsl_orderid='".$OrderNo."' and wsl_state='-1' limit 1";
$query = mysql_query($sql);
$array = mysql_fetch_array($query);

//充值失败的信息时才有必须退款
if($array){
$money = $array['wsl_money']/100;

$OrderDate = date('Y/m/d');
$OrderTime = date('H:i:s');

	//1、生成退款请求对象
	$tRequest = new RefundRequest();
	$tRequest->request["OrderDate"] = $OrderDate; //订单日期（必要信息）
	$tRequest->request["OrderTime"] = $OrderTime; //订单时间（必要信息）
	// 以下为非必填项
	/* 
	$tRequest->request["MerRefundAccountNo"] = ($_POST['MerRefundAccountNo']); //商户退款账号
	$tRequest->request["MerRefundAccountName"] = ($_POST['MerRefundAccountName']); //商户退款名
	*/
	$tRequest->request["OrderNo"] = $OrderNo; //原交易编号（必要信息）
	$tRequest->request["NewOrderNo"] = $OrderNo.'_'.rand(10,99); //交易编号（必要信息）
	$tRequest->request["CurrencyCode"] = "156"; //交易币种（必要信息）
	$tRequest->request["TrxAmount"] = ($money); //退货金额（必要信息）
	$tRequest->request["MerchantRemarks"] = '流量充值失败-退款'; //附言

	//2、传送退款请求并取得退货结果
	$tResponse = $tRequest->postRequest();

	//3、支付请求提交成功，返回结果信息
	if ($tResponse->isSuccess()) {
		$sql = "update 52_wxshoplogs set wsl_state='-2' where wsl_orderid='".$OrderNo."' and wsl_state='-1' limit 1";
		$query = mysql_query($sql);
		$time = strtotime($OrderDate.' '.$OrderTime);
		$sql = "insert into 52_cash_money (cm_money,cm_time,cm_info,cm_userid,cm_biaoshi,cm_orderid,cm_re_info,cm_re_time,cm_re_userid,cm_type,cm_state,cm_card_type,cm_status) VALUES('".$array['wsl_money']."','".$time."','【退款】为订单号".$OrderNo."退款','33','buy_52mb','".$OrderNo."','buy_52mb申请的农行退款','".$time."','33','-2','1','3','1')";
		$query = mysql_query($sql);
		print ("<br>Success!!!" . "</br>");
		print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
		print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
		print ("OrderNo   = [" . $tResponse->GetValue("OrderNo") . "]<br/>");
		print ("NewOrderNo   = [" . $tResponse->GetValue("NewOrderNo") . "]<br/>");
		print ("TrxAmount = [" . $tResponse->GetValue("TrxAmount") . "]<br/>");
		print ("BatchNo   = [" . $tResponse->GetValue("BatchNo") . "]<br/>");
		print ("VoucherNo = [" . $tResponse->GetValue("VoucherNo") . "]<br/>");
		print ("HostDate  = [" . $tResponse->GetValue("HostDate") . "]<br/>");
		print ("HostTime  = [" . $tResponse->GetValue("HostTime") . "]<br/>");
		print ("iRspRef  = [" . $tResponse->GetValue("iRspRef") . "]<br/>");
	} else {
		print ("<br>Failed!!!" . "</br>");
		print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
		print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	}

}
?>