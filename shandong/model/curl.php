<?php
class curl{
	private $result;
	public function _curl($url,$data='',$method = 'GET'){
		$ch = curl_init();
		$header = array('Content-type: text/plain','Accept-Charset: utf-8');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$this->result = curl_exec($ch);
		// echo $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);	#服务端返回状态码
		curl_close($ch);
		unset($ch);
		return $this->result;
	}	
}