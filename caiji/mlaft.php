<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once "../Public/config.php";
include_once "../Public/db.class.php";
$db = new db(array($db['host'] ,'DB_USER'=>$db['user'],'DB_PWD'=>$db['pass'],'DB_NAME'=>$db['name']));

require "jiesuan.php";
if($_GET['t'] == 'test'){
    SSC_jiesuan();
    exit;
}
//$url = "http://api.woaizy.com/chatkj.php";
$url = 'http://'.$_SERVER['SERVER_NAME'].'/lotteryAPI/mlaft.php';
$json = file_get_contents($url);
$jsondata = json_decode($json,true);
if($jsondata['row'] > 0){
	foreach($jsondata['data'] as $k=>$v){
		foreach ($v as $data){	
			if($data['action'] == 'add'){
				jiesuan();
				SSC_jiesuan();
				kaichat($data['alias'], $data['next_time']);
				echo "更新 {$data['alias']} {$data['term']} 成功 操作  {$data['action']} ！<br>";	
			}else{
				echo "{$data['alias']} {$data['term']} 操作  {$data['action']} ！<br>";	
			}
		}
	}
}



//zepto 2017-10-13
echo "系统当前时间为 ";
echo date('Y-m-d H:i:s',time()).'<br>'.PHP_EOL;
echo time().PHP_EOL;
//<!--JS 页面自动刷新 -->
echo ("<script type=\"text/javascript\">");
echo ("function fresh_page()");    
echo ("{");
echo ("window.location.reload();");
echo ("}"); 
echo ("setTimeout('fresh_page()',15000);");      
echo ("</script>");
?>