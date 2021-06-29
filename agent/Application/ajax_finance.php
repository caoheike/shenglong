<?php
include(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/config.php");
$type = $_GET['type'];
$id = $_POST['id'];
$money = $_POST['money'];
switch($type){
case 'update': 
	update_query('fn_finance', array('status' => '1','update_time'=>time()), array('id' => $id, 'status' => 0));
    echo json_encode(array("success" => true, "msg" => "操作成功"));
    break;

}

?>