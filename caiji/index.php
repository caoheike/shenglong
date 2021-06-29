<?php
set_time_limit(0);
$load = 5;
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

include_once "../Public/config.php";
include_once "../Public/Bjl.php";

include_once "../Public/reopen.funcion.php";

require "jiesuan.php";

if($_GET['t'] == 'test'){
    SSC_jiesuan();
    exit;
}
if (!isset($_GET['debug'])) {
	$bjl = new Bjl();
	$codes = $bjl->newCode(false);
	if (!$codes) {
		$codes = $bjl->newCode();
		$cur = $bjl->get_period_info($bjl->getTodayCur());
		BJL_jiesuan();
		select_query('fn_lottery9', '*', array('gameopen' => 'true'));
		while ($con = db_fetch_array()) {
			$cons[] = $con;
		}
		foreach ($cons as $con) {
			$Content = "第 {$cur['next_periodNumber']} 期已经开启,请开始下注!";
			$bjl = new Bjl();
			$cur = $bjl->get_period_info($bjl->getTodayCur());
			$myy_time = strtotime($cur['awardTime']) + 9;
			$headimg = get_query_val('fn_setting', 'setting_robotsimg', array('roomid' => $con['roomid']));
			insert_query("fn_chat", array("username" => "机器人", "headimg" => $headimg, 'content' => $Content, 'game' => 'bjl', 'addtime' => date('H:i:s', $myy_time), 'type' => 'S3', 'userid' => 'system', 'roomid' => $con['roomid']));
			echo "bjl喊话-" . $con['roomid'] . '..<br>';
		}
	}
	if (isset($_GET['from_bjl'])) {
		exit;
	}
}
restore(); 
$cjUrl = array(
	// '1' => array('url'=>'http://main.caipiaoapi.com:7373/hall/nodeService/api_request?uid=1626&time=1572202511&gamekey=bjpk10&api=apiGameList&md5=fc98246ae100dfc7062315cdff36dc49&site=api.jiekouapi.com','code'=>'pk10'),//pk10  北京赛车 
	// '2' => array('url'=>'http://main.caipiaoapi.com:7373/hall/nodeService/api_request?uid=1626&time=1572222473&gamekey=metpk10&api=apiGameList&md5=93a57d939874a7fbd95943f16b91ade2&site=api.jiekouapi.com','code'=>'xyft'), // xyft 幸运飞艇 
	'7' => array('url'=>'http://api.b6api.com/api?p=json&t=jisupk10&limit=5&token=EBCD5037AE185617','code'=>'jssc'), //jssc 急速赛车 
	// '3' => array('url'=>'http://main.caipiaoapi.com:7373/hall/nodeService/api_request?uid=1626&time=1572222500&gamekey=ssc&api=apiGameList&md5=4df890de9e58682fa5531fcbdb4fcca7&site=api.jiekouapi.com','code'=>'cqssc'), // cqssc重庆时时彩 
	// '8' => array('url'=>'http://main.caipiaoapi.com:7373/hall/nodeService/api_request?uid=1626&time=1572222513&gamekey=jsssc&api=apiGameList&md5=04d434e08e6531c09807565439b11be0&site=api.jiekouapi.com','code'=>'jsssc'), // jsssc急速时时彩 
	// '15' => array('url'=>'http://main.caipiaoapi.com:7373/hall/nodeService/api_request?uid=1626&time=1572222527&gamekey=azxy5&api=apiGameList&md5=1f74584bfc94d95c32adc6941297ccc2&site=api.jiekouapi.com','code'=>'azxy5'),//azxy5 澳洲幸运5
);
foreach($cjUrl as $k => $v){
	$code = $v['code'];
	$typeid = $k;
	$json = file_get_contents($v['url']);
	$jsondata = json_decode($json,true);
	$jsondata = $jsondata['data'];
	/*
	 "gid": "740479",
        "award": "02,10,01,03,04,05,07,08,06,09",
        "time": "2019-10-27 23:50:00",
        "date": "2019-10-27",
        "nextOpenIssue": "740480",
        "nextOpenTime": "2019-10-28 09:30:00",
        "secondOpenIssue": "740481",
        "secondOpenTime": "2019-10-28 09:50:00"
	
	*/
	$opencode = $jsondata[0]['opencode'];
	$qihao = $jsondata[0]['expect'];
	$opentime = $jsondata[0]['opentime'];
	$next_term = $jsondata[0]['nextexpect'];
	$nexttime = $jsondata[0]['nextopentime'];
	/*
	if($k == '1'){
		$time = strtotime($opentime) + 5 * 60 - 20;
		$nexttime = date('Y-m-d H:i',$time).':20';
	}elseif($k == '2'){
		$time = strtotime($opentime) + 5 * 60 - 15;
		$nexttime = date('Y-m-d H:i',$time).':25';
	}elseif($k == '7'){
		$time = strtotime($opentime) + 1 * 60;
		$nexttime = date('Y-m-d H:i:d',$time);
	}elseif($k == '3'){
		$time = strtotime($opentime) + 20 * 60 - 15;
		$nexttime = date('Y-m-d H:i',$time).':20';
	}elseif($k == '8'){
		$time = strtotime($opentime) + 1 * 60 ;
		$nexttime = date('Y-m-d H:i:d',$time);
	}elseif($k == '15'){
		$time = strtotime($opentime) + 5 * 60 - 20;
		$nexttime = date('Y-m-d H:i',$time).':20';
	}
	*/
	$topcode = db_query("select `term` from `fn_open` where `type`={$typeid} order by `term` desc limit 1");
	$topcode = db_fetch_array();
	/*if($code == 'azxy5'){
		$setcode = db_query("select `res_code` from `fn_res_open` where `term`={$qihao} order by `id` desc limit 1");
			$setcode = db_fetch_array();
			var_dump($setcode);exit;   
	}*/ 
	if(!empty($qihao)){
		
		if(empty($topcode[0]) || $topcode[0] != $qihao){
			//判断是不是有预设置的期号
			$setcode = db_query("select `res_code` from `fn_res_open` where `term`={$qihao} order by `id` desc limit 1");
			$setcode = db_fetch_array();
			if(!empty($setcode[0])){
				$opencode =  $setcode['res_code'];     
				$update_time = time();
				db_query("update  `fn_res_open` set status = 1,update_time =  {$update_time}  where `term`={$qihao} ");
			}
			insert_query('fn_open', array('term' => $qihao, 'code' => $opencode, 'time' => $opentime, 'type' => $typeid, 'next_term' => $next_term, 'next_time' => $nexttime));
			
			if($code == 'jsssc'){
				JSSSC_jiesuan();
			}
			if($code == 'cqssc'){
				SSC_jiesuan();
			}
			if($code == 'azxy5'){
				AZXY5_jiesuan();
			}
			if($code == 'jssc'){
				JSSC_jiesuan();
			}
			if($code == 'pk10' || $code == 'xyft'){
				jiesuan();
			}
			
			kaichat($code, $next_term);
			echo "更新 $code 成功！<br>";
		}else{
			echo "等待 $code 刷新<br>";
		}
	}else{
		echo "等待 $code 刷新2<br>";
	}
	
	sleep(1);
	 
}


//zepto 2017-12-31
echo "系统当前时间戳为 ";
echo "";
echo time() . ' ' . date('Y-m-d H:i:s');
echo "<script type=\"text/javascript\">";
echo "function fresh_page()";
echo "{";
echo "window.location.reload();";
echo "}";
echo "setTimeout('fresh_page()',5000);";
echo "</script>";

//echo "<br><br><<开盘采集>>--该页面每10秒自动刷新!!";

//<!--JS 页面自动刷新 -->
//echo ("<script type=\"text/javascript\">");
//echo ("function fresh_page()");    
//echo ("{");
//echo ("window.location.reload();");
//echo ("}"); 
//echo ("setTimeout('fresh_page()',10000);");      
//echo ("</script>");
?>