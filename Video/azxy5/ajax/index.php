<?php	
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
date_default_timezone_set("Asia/Shanghai");
//$url = "http://api.woaizy.com/chatkj.php";
$json = file_get_contents("http://{$_SERVER['HTTP_HOST']}/lotteryAPI/api_animal.php?type=azxy5");
$json = json_decode($json,true);

$code =  $json['open'][0]['opencode'];
$code11 = explode(',',$code);
$codes =$code;

$term = $json['open'][0]['expect'];
$time = date('Y-m-d H:i:s',strtotime($json['open'][0]['opentime']));

$count = strtotime($json['open'][0]['next_time']) - time();
$sumNum = (int)$code11[0] + (int)$code11[1] + (int)$code11[2] + (int)$code11[3] + (int)$code11[4];
$ds = $sumNum % 2 != 0 ? 0 : 1;
$dx = $sumNum > 22 ? 0:1;
if($code11[0] > $code11[4]){
	$lh = 0;
}elseif($code11[0] < $code11[4]){
	$lh = 1;
}elseif($code11[0] == $code11[4]){
	$lh = 2;
}
header("Content-type:text/html;charset=utf-8");

echo '{
  "errorCode": 0,
  "message": "操作成功",
  "result": {
    "businessCode": 0,
    "data": {
      "preDrawCode":"'.$code.'",
      "drawIssue": "'.$json['next'][0]['expect'].'",
      "drawTime": "'.$json['next'][0]['opentime'].'",
      "preDrawTime": "'.$time.'",
      "preDrawDate": "'.date("Y-m-d").'",
      "preDrawIssue": "'.$term.'",
      "drawCount": "'.$term.'",
      "firstNum": '.$code11[0].',
      "secondNum": '.$code11[1].',
      "thirdNum": '.$code11[2].',
      "fourthNum": '.$code11[3].',
      "fifthNum": '.$code11[4].',
      "sumNum": '.$sumNum.',
      "sumSingleDouble":'.$ds.',
      "sumBigSmall": '.$dx.',
      "behindThree": 1,
      "sdrawCount": "",
      "betweenThree": 2,
      "dragonTiger": '.$lh.',
      "fifthBigSmall": 0,
      "fifthSingleDouble": 0,
      "firstBigSmall": 0,
      "firstSingleDouble": 0,
      "fourthBigSmall": 1,
      "fourthSingleDouble": 1,
      "lastThree": 1,
      "secondBigSmall": 1,
      "secondSingleDouble": 1,
      "thirdBigSmall": 1,
      "thirdSingleDouble": 0,
      "status": 0,
      "id": 969923,
      "frequency": "",
      "totalCount": 1152,
      "iconUrl": "",
      "lotCode": 10036,
      "shelves": 1,
      "groupCode": 2,
      "lotName": "极速时时彩",
      "serverTime": "'.date("Y-m-d H:i:s").'",
      "index": 100
    },
    "message": "操作成功"
  }
}';
exit;
echo json_encode(array('preDrawCode'=>$codes,'drawIssue'=>$term,'nexttime'=>$time,'count'=>$count,'sumNum'=>$sumNum,'hedx'=>$dx,'heds'=>$ds,'lh'=>$lh));
exit;

?>