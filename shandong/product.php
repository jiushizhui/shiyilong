<?php
$product = file_get_contents('http://shandong.4ggogo.com/sd-web/open/productList?EnterpriseBossId=5368010349389');

$product = json_decode(json_encode(simplexml_load_string($product)), true);

// var_dump($product);
$list = array();
foreach ($product['Product'] as $key => $value) {
	# code...
	$list[$value['Size']]=$value;
}
echo json_encode($list);


die;