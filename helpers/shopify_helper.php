<?php

//data_string : 
//method : Get or POST
//url : API URL
//access_token : ACCESS TOKEN

function shopify_curl($data_string, $method, $url, $access_token) {

	if (empty($access_token)) {
	
		// For Private App 
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string)
		));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;

	} else {
		 
		// For Public App
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'X-Shopify-Access-Token: '.$access_token.'',
			'Content-Length: ' . strlen($data_string)
		));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;

	}

}

function getShopify_curl($method, $url) {
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json'
	));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;

}

?>