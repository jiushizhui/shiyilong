<?php
$order = $_GET['order'];
$sign = md5($order.substr($order,-3));
$url = 'http://abchina.52mb.com/nh/demo/MerchantRefund.php?OrderNo='.$order.'&sign='.$sign;

echo $url;





?>