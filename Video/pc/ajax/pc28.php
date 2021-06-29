<?php

function getTimestamp($digits = false)
{
    $digits = $digits > 10 ? $digits : 10;
    $digits = $digits - 10;
    if ((!$digits) || ($digits == 10)) {
        return time();
    } else {
        return number_format(microtime(true), $digits, '', '');
    }
}

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
date_default_timezone_set("Asia/Shanghai");
if ($_COOKIE['game'] == 'xy28') {
    $gametype = 'pc28a';
    $url = "http://api.api68.com/LuckTwenty/getBaseLuckTewnty.do?issue=&lotCode=10014";
    $json = json_decode(file_get_contents($url), 1);
    $data = $json['result']['data'];

    $term = $data['preDrawIssue'];
    $time = $data['preDrawTime'];
    $code = substr($data['preDrawCode'], 0, -3);
    $codes = explode(',', $code);


    $number1 = (int)$codes[0] + (int)$codes[1] + (int)$codes[2] + (int)$codes[3] + (int)$codes[4] + (int)$codes[5];
    $number2 = (int)$codes[6] + (int)$codes[7] + (int)$codes[8] + (int)$codes[9] + (int)$codes[10] + (int)$codes[11];
    $number3 = (int)$codes[12] + (int)$codes[13] + (int)$codes[14] + (int)$codes[15] + (int)$codes[16] + (int)$codes[17];

    $number1 = substr($number1,-1);
    $number2 = substr($number2,-1);
    $number3 = substr($number3,-1);
    $hz = (int)$number1 + (int)$number2 + (int)$number3;


    $next_term = $data['drawIssue'];
    $next_time = $data['drawTime'];
    $next_ss = (int)strtotime($next_time) . '000' - getTimestamp(13);
    $next_game = $gametype;

    echo "
{
	\"time\":" . getTimestamp(13) . ",
	\"current\": {
		\"game\":\"" . $gametype . "\",
		\"periodNumber\":\"" . $term . "\",
		\"awardTime\":\"" . $time . "\",
		\"awardNumbers\":\"" . $number1 . ',' . $number2 . ',' . $number3 . "\"
	},
	\"next\":{
		\"game\":\"" . $next_game . "\",
		\"periodNumber\":\"" . $next_term . "\",
		\"awardTime\":\"" . $next_time . "\",
		\"awardTimeInterval\":" . $next_ss . ",
		\"delayTimeInterval\": 0 
	}
}

";
} else if ($_COOKIE['game'] == 'jnd28') {
    $gametype = 'pc28b';
    $url = "https://www.1399klc.com/lottery/ajax?lotterycode=canada";
    $data = json_decode(file_get_contents($url), 1);

    $term = $data['period'];
    $time = $data['time'];
    $code = $data['result'];
    $codes = explode(',', $code);



    $number1 = (int)$codes[1] + (int)$codes[4] + (int)$codes[7] + (int)$codes[10] + (int)$codes[13] + (int)$codes[16];
    $number2 = (int)$codes[2] + (int)$codes[5] + (int)$codes[8] + (int)$codes[11] + (int)$codes[14] + (int)$codes[17];
    $number3 = (int)$codes[3] + (int)$codes[6] + (int)$codes[9] + (int)$codes[12] + (int)$codes[15] + (int)$codes[18];

    $number1 = substr($number1,-1);
    $number2 = substr($number2,-1);
    $number3 = substr($number3,-1);
    $hz = (int)$number1 + (int)$number2 + (int)$number3;

    $next_term = $data['next_period'];
    $next_time = date('Y-m-d H:i:s',strtotime($data['next_awardTime']));
    $next_ss = (int)strtotime($next_time) . '000' - getTimestamp(13);
    $next_game = $gametype;


    echo "
{
	\"time\":".getTimestamp(13).",
	\"current\": {
		\"game\":\"".$gametype."\",
		\"periodNumber\":\"".$term."\",
		\"awardTime\":\"".$time."\",
		\"awardNumbers\":\"".$number1.','.$number2.','.$number3."\"
	},
	\"next\":{
		\"game\":\"".$next_game."\",
		\"periodNumber\":\"".$next_term."\",
		\"awardTime\":\"".$next_time."\",
		\"awardTimeInterval\":".$next_ss.",
		\"delayTimeInterval\": 0 
	}
}

";
}


?>