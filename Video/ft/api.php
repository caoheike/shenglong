<?php  
//include('key.php');
//exit;
//$url = 'key.php';
$url = 'http://'.$_SERVER['HTTP_HOST'].'/lotteryAPI/api.php?code=2';
$aa = file_get_contents($url);
date_default_timezone_set('PRC');
$json = json_decode($aa, true);

$awardTime = strtotime($json['time']);
if(substr($json['term'],-3) == '180'){
	$next_periodNumber = '001';
	$next_awardTime = date('Ymd') . ' 13:30:00' ;
}else{
	$next_periodNumber = substr($json['next_term'],-3);
	$next_awardTime = $json['next_time'];
}

$_json['time'] = time();
$_json['current']['periodNumber'] = substr($json['term'],-3);
$_json['current']['periodDate'] = date('Ymd');
$_json['current']['awardTime'] = $json['time'];
$_json['current']['awardNumbers'] = $json['code'];

$_json['next']['periodNumber'] = $next_periodNumber;
$_json['next']['periodDate'] = date('Ymd');
$_json['next']['awardTime'] = $next_awardTime;
$_json['next']['awardTimeInterval'] =  6000;
$_json['next']['delayTimeInterval'] = strtotime($next_awardTime)-time();


echo json_encode($_json);

function get($durl, $data=array()) {
$cookiejar = realpath('cookie.txt');
$t = parse_url($durl);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$durl);
curl_setopt($ch, CURLOPT_TIMEOUT,5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_REFERER, "http://$t[host]/");
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_ENCODING, 1); //gzip 解码
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
if($data) {
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
}
$r = curl_exec($ch);
curl_close($ch);
return $r;
}
?>  