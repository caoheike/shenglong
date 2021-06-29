<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();
include_once "../Public/config.php";
include_once "../Public/db.class.php";
$db = new db(array('DB_HOST'=>$db['host'] ,'DB_USER'=>$db['user'],'DB_PWD'=>$db['pass'],'DB_NAME'=>$db['name']));

$jsondata = array();

if($_REQUEST['type'] == 'jsmt'){
    $typeid = 6;
}
elseif($_REQUEST['type'] == 'xyft'){
    $typeid = 2;
}
elseif($_REQUEST['type'] == 'azxy5'){
    $typeid = 15;
}
elseif($_REQUEST['type'] == 'jssc'){
    $typeid = 7;
}
elseif($_REQUEST['type'] == 'jsssc'){
    $typeid = 8;
}
elseif($_REQUEST['type'] == 'cqssc'){
    $typeid = 3;
}
$cur_term = $db->table('fn_open')->where("type=".$typeid)->order('term desc')->find();

$jsondata['open'][] = array('opencode'=>$cur_term['code'], 'expect' => $cur_term['term'],'opentime'=>$cur_term['time']);
$jsondata['next'][] = array('expect'=>$cur_term['next_term'],'opentime'=>$cur_term['next_time']);
    header('Content-type:text/json');
echo json_encode($jsondata);
?>