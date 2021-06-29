<?php

//decode by http://www.yunlu99.com/
include "sq.php";
?>
	<?php 
$load = 5;
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once "../Public/config.php";
include_once "../Public/Bjl.php";
include_once "../Public/reopen.funcion.php";
require "jiesuan.php";
if ($_GET['t'] == 'test') {
	SSC_jiesuan();
	exit;
}
$bjl = new Bjl();
$codes = $bjl->newCode(false);
$cur = $bjl->get_period_info($bjl->getTodayCur());
$diff = strtotime($cur['next_awardTime']) - time();
if (!$codes) {
	echo "{$cur['periodNumber']}--kj---start</br>";
	$codes = $bjl->newCode();
	BJL_jiesuan();
	echo "{$cur['periodNumber']}--kj---end</br>";
}
$diff2 = $diff;
if ($diff > 20) {
	$diff = 10;
}
header("refresh:$diff;url=index_bjl.php");
echo $diff2;