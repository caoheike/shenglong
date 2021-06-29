<?php

//decode by http://www.yunlu99.com/
$load = 5;
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once '../Public/config.php';
include_once '../Public/Bjl.php';
$bjl = new Bjl();
$cur = $bjl->get_period_info($bjl->getTodayCur());
if (time() - strtotime($cur['awardTime']) >= 10 && time() - strtotime($cur['awardTime']) <= 20) {
	$diff = strtotime($cur['next_awardTime']) + 10 - time();
	select_query('fn_lottery9', '*', array('gameopen' => 'true'));
	while ($con = db_fetch_array()) {
		$cons[] = $con;
	}
	foreach ($cons as $con) {
		$Content = '第 ' . $cur['next_periodNumber'] . ' 期已经开启,请开始下注!';
		$bjl = new Bjl();
		$cur = $bjl->get_period_info($bjl->getTodayCur());
		$headimg = get_query_val('fn_setting', 'setting_robotsimg', array('roomid' => $con['roomid']));
		$myy_time = strtotime($cur['awardTime']) + 10;
		db_query('select * from fn_chat where username=\'机器人\' and game=\'bjl\' and addtime=\'' . date('Y-m-d H:i:s', $myy_time) . '\'');
		$arr = db_fetch_array();
		if (empty($arr)) {
			insert_query('fn_chat', array('username' => '机器人', 'headimg' => $headimg, 'content' => $Content, 'game' => 'bjl', 'addtime' => date('Y-m-d H:i:s', $myy_time), 'type' => 'S3', 'userid' => 'system', 'roomid' => $con['roomid']));
		}
		echo 'bjl喊话-' . $con['roomid'] . '..<br>';
	}
} else {
	if (time() - strtotime($cur['awardTime']) < 10) {
		$diff = 10 - (time() - strtotime($cur['awardTime']));
	} else {
		if (time() - strtotime($cur['awardTime']) > 10) {
			$diff = strtotime($cur['next_awardTime']) + 10 - time();
		}
	}
}
$diff2 = $diff;
if ($diff > 50) {
	$diff = 30;
}
header('refresh:' . $diff . ';url=index_bjl_kai.php');
echo $diff2;