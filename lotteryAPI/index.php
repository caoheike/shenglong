<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once "../Public/config.php";
include_once "lib/Http.php";
$file_path = 'temp';
$t = microtime(true);
$gateway = 'https://www.pk103.com/api/newest?code={code}&t='.time();
$headers = array(
    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
);
//bjpk10-pk10
$url = str_replace('{code}','pk10',$gateway);
$_result['bjpk10'] = $info = Http::curlGet($url,5,$headers );
//mlaft-xyft
$url = str_replace('{code}','xyft',$gateway);
$_result['mlaft'] = $info = Http::curlGet($url,5,$headers );
//cqssc-cq_ssc
$url = str_replace('{code}','cq_ssc',$gateway);
$_result['cqssc'] = $info = Http::curlGet($url,5,$headers );
//bjkl8-xy28
$url = str_replace('{code}','xy28',$gateway);
$_result['bjkl8'] = $info = Http::curlGet($url,5,$headers );
//cakeno-jnd28
$url = str_replace('{code}','jnd28',$gateway);
$_result['cakeno'] = $info = Http::curlGet($url,5,$headers );
//jsmt
//jssc-speed10
$url = str_replace('{code}','speed10',$gateway);
$_result['jssc'] = $info = Http::curlGet($url,5,$headers );
//jsssc-speed5
$url = str_replace('{code}','speed5',$gateway);
$_result['jsssc'] = $info = Http::curlGet($url,5,$headers );
//kuai3-js_k3
$url = str_replace('{code}','js_k3',$gateway);
$_result['js_k3'] = $info = Http::curlGet($url,5,$headers );



//组装数据
$data = $temp = array();
$data['rows'] = count($_result);
$data['now'] = date('Y-m-d H:i:s',time());


foreach ($_result as $k => $v) {
	$_temp = array();$_v = '';
	$_v = json_decode($v,TRUE);
	$_v = $_v['data'];
	$_temp['code'] = $k;
	$_temp['open_phase'] = $_v['newest']['issue'];
	$_temp['open_index'] = $_v['newest']['term'];
	$_temp['open_result'] = $_v['newest']['code'];
	$_temp['open_time'] = $_v['newest']['time'];
	$_temp['load_time'] = date('Y-m-d H:i:s',(strtotime($_temp['open_time'])+$_v['interval']));
	$_temp['next_phase'] = $_v['current'];
	$_temp['next_index'] = $_v['currentNo'];
	$_temp['next_time'] = date('Y-m-d H:i:s',(strtotime($_temp['open_time'])+$_v['interval']));
	$_temp['now']= date('Y-m-d H:i:s',time());
	
	array_push($temp,$_temp);

}
$e = microtime(true);

$data['data'] = $temp;
$data['runtime'] = ($e-$t);
header('Content-type:text/json');
echo json_encode($data);