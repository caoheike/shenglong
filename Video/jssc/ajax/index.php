<?php	
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
date_default_timezone_set("Asia/Shanghai");
//$url = "http://api.woaizy.com/chatkj.php";
$json = file_get_contents("http://{$_SERVER['HTTP_HOST']}/lotteryAPI/api_animal.php?type=jssc");
$json = json_decode($json,true);

$code = $opencode =  $json['open'][0]['opencode'];
$code = explode(',',$code);
$codes = (int)$code[0].','.(int)$code[1].','.(int)$code[2].','.(int)$code[3].','.(int)$code[4].','.(int)$code[5].','.(int)$code[6].','.(int)$code[7].','.(int)$code[8].','.(int)$code[9];

$term = $json['open'][0]['expect'];
$nextterm = $json['next'][0]['expect'];
$count = strtotime($json['next'][0]['opentime']) - time();
$sumNum = (int)$code[0] + (int)$code[1];
$ds = $sumNum % 2 != 0 ? 0 : 1;
$dx = $sumNum > 11 ? 0 : 1;
if($code[0] > $code[9]){
	$lh1 = 0;
}else{
	$lh1 = 1;//'虎';
}
if($code[1] > $code[8]){
	$lh2 = 0;//'龙';
}else{ 
	$lh2 = 1;//'虎';
}
if($code[2] > $code[7]){
	$lh3 = 0;//'龙';
}else{
	$lh3 = 1;//'虎';
}
if($code[3] > $code[6]){
	$lh4 = 0;//'龙';
}else{
	$lh4 = 1;//'虎';
}
if($code[4] > $code[5]){
	$lh5 = 0;//'龙';
}else{
	$lh5 = 1;//'虎';
}
header("Content-type:text/html;charset=utf-8");
//header('Content-type:text/json');
echo '{
	"message": "操作成功",
	"result": {
		"businessCode": 0,
		"message": "操作成功",
		"data": {
			"preDrawCode": "'.$opencode.'",
			"drawIssue": '.$nextterm.',
			"drawTime": "'.$json['next'][0]['opentime'].'",
			"preDrawTime": "'.$json['open'][0]['opentime'].'",
			"preDrawDate": "'.date("Y-m-d",strtotime($json['open'][0]['opentime'])).'",
			"preDrawIssue": '.$term.',
			"drawCount": 695,
			"firstNum": '.intval($code[0]).',
			"secondNum": '.intval($code[1]).',
			"thirdNum": '.intval($code[2]).',
			"fourthNum": '.intval($code[3]).',
			"fifthNum": '.intval($code[4]).',
			"sixthNum": '.intval($code[5]).',
			"fifthDT": '.$lh5.',
			"firstDT": '.$lh1.',
			"fourthDT":'.$lh4.',
			"secondDT": '.$lh2.',
			"sumBigSamll": "'.$dx.'",
			"sumFS": '.$sumNum.',
			"sumSingleDouble": "'.$ds.'",
			"thirdDT": '.$lh3.',
			"eighthNum": '.intval($code[7]).',
			"ninthNum": '.intval($code[8]).',
			"seventhNum": '.intval($code[6]).',
			"tenthNum": '.intval($code[9]).',
			"serverTime": "'.date("Y-m-d H:i:s").'",
			"frequency": "",
			"totalCount": 1152,
			"iconUrl": "",
			"lotCode": 10037,
			"shelves": 1,
			"groupCode": 1,
			"lotName": "极速赛车",
			"index": 100
		}
	},
	"errorCode": 0
}';
exit;
echo json_encode(array('preDrawCode'=>$codes,'drawIssue'=>$term,'nexttime'=>$time,'count'=>$count,'sumNum'=>$sumNum,'hedx'=>$dx,'heds'=>$ds,'lh'=>$lh));
exit;

?>