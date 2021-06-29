<?php

//decode by http://www.yunlu99.com/
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once '../Public/config.php';
include_once '../Public/db.class.php';
$db = new db(array($db['host'], 'DB_USER' => $db['user'], 'DB_PWD' => $db['pass'], 'DB_NAME' => $db['name']));
$result = $db->table('fn_open')->where(array('type' => $_GET['code']))->order('term Desc')->find();
header('content-type:application/json');
echo json_encode($result);