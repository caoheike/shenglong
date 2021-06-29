<?php  
//$url = 'http://chatapi.o4s.cn/pk10.php?type=json';
//$aa = get($url);
//date_default_timezone_set('PRC');
//$json = json_decode($aa, true);
//$awardTime = strtotime($json['next']['awardTime']);
//if( time() >= ($awardTime-15) && time() < ($awardTime+20) ){
//	if ( $json['next']['awardTimeInterval'] == 60000 ){
//		$json['next']['awardTimeInterval'] = 0;
//	}
//}
$url = 'http://'.$_SERVER['SERVER_NAME'].'/lotteryAPI/api.php?code=1';
$aa = get($url);
date_default_timezone_set('PRC');
$json = json_decode($aa, true);

$awardTime = strtotime($json['time']);

$_json['next']['awardTimeInterval'] = (strtotime($json['next_time']) - time()- 20) * 1000;

$_json['time'] = time();
$_json['current']['periodNumber'] = $json['term'];
$_json['current']['awardTime'] = $json['time'];
$_json['current']['awardNumbers'] = $json['code'];

$_json['next']['periodNumber'] = $json['next_term'];
$_json['next']['awardTime'] = $json['next_time'];
$_json['next']['delayTimeInterval'] = 3;

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