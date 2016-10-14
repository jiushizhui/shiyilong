<?php  
  
// ??????????deviceToken???????????????  
//	$deviceToken = '27c6d113377fe1944ce0df78918c76f3ef50a25a732736cec6a13184a7d1e78c';  

$deviceToken = 'b59ec88deafc79503a57a1b42b68de9eee96e91343e3dff62aee6c9fc62a843e';

  
// Put your private key's passphrase here:  
$passphrase = '123456';  
  
// Put your alert message here:  
$message = 'My first push test!';  
  
////////////////////////////////////////////////////////////////////////////////  
  
$ctx = stream_context_create();  
stream_context_set_option($ctx, 'ssl', 'local_cert', '~ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);  

//stream_context_set_option($ctx, 'ssl', 'local_cert', 'qps_MiDou.pem'); 
  
  
$a = rand(0,1);
if($a){
	$urls = 'ssl://gateway.sandbox.push.apple.com:2195';
	$fp = stream_socket_client($urls, $err,  $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
}else{
	$urls = 'ssl://gateway.push.apple.com:2195';
	$fp = stream_socket_client($urls, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
}

$message .= 'qps_MiDou'.$urls;
// Open a connection to the APNS server  
//??????????  
 //$fp = stream_socket_client(?ssl://gateway.push.apple.com:2195?, $err, $errstr, 60, //STREAM_CLIENT_CONNECT, $ctx);  
//?????????????appstore??????  
//$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,  $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);  
  
  
if (!$fp)  
exit("Failed to connect: $err $errstr" . PHP_EOL);  
  
echo 'Connected to APNS' . PHP_EOL;  
  
// Create the payload body  
$body['aps'] = array(  
'alert' => $message,  
'sound' => 'default'  
);  
  
// Encode the payload as JSON  
$payload = json_encode($body);  
  
// Build the binary notification  
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;  
  
// Send it to the server  
$result = fwrite($fp, $msg, strlen($msg));  
  
if (!$result)  
echo $urls.'Message not delivered' . PHP_EOL;  
else  
echo $urls.'Message successfully delivered' . PHP_EOL;  
  
// Close the connection to the server  
fclose($fp);

?>