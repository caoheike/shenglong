<?php
include_once(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/config.php");
include_once(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/Bjl.php");
$game = $_COOKIE['game'];
//北京赛车 1 飞艇 2  急速赛车7  重庆时时彩3  急速时时彩 8 
    if ($game == 'pk10') {
        $type = '1';
    } elseif ($game == 'xyft') {
        $type = '2';
    } elseif ($game == 'cqssc') {
        $type = '3';
    } elseif ($game == 'xy28') {
        $type = '4';
    } elseif ($game == 'jnd28') {
        $type = '5';
    } elseif ($game == 'jsmt') {
        $type = '6';
    } elseif ($game == 'jssc') {
        $type = '7';
    } elseif ($game == 'jsssc') {
        $type = '8';
    } elseif ($game == 'bjl') {
        $type = '9';
    }elseif ($game == 'azxy5') {
        $type = '15';
    } else {
        echo '<br><br><strong style="font-size:40px;color:red">该房间已经封盘啦！';
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta http-equiv="Cache-Control" content="no-siteapp">
	<link rel="stylesheet" type="text/css" href="/Style/New/css/H-ui.min.css">
	<link rel="stylesheet" type="text/css" href="/Style/Old/css/common.css" />
	<style>
		.pk_1,
		.pk_2,
		.pk_3,
		.pk_4,
		.pk_5,
		.pk_6,
		.pk_7,
		.pk_8,
		.pk_9,
		.pk_10 {
			width: 35px;
			height: 35px;
			float: left;
			line-height: 35px;
			color: #000;
			font-weight: normal;
			font-size: 20px;
			text-align: center;
			font-weight: bold;
			color: #fff;
			border-radius: 5px;
			margin: 0 10px 2px 0;
			text-align: center;
			text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			-webkit-text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			-moz-text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			*filter: Glow(color=#000, strength=1);
		}
		.pcnum,
		.pcres {
			width: 35px;
			height: 35px;
			float: left;
			line-height: 35px;
			color: #000;
			font-weight: normal;
			font-size: 20px;
			text-align: center;
			font-weight: bold;
			color: #fff;
			border-radius: 23px;
			margin: 0 10px 2px 0;
			text-align: center;
			text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			-webkit-text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			-moz-text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
			*filter: Glow(color=#000, strength=1);
		}
		.pcnum {
			background: #0099FF;
		}
		.pcres {
			background: #FF0000;
		}

		.pk_1 {
			background: #FFFF00;
		}

		.pk_2 {
			background: #0099FF;
		}

		.pk_3 {
			background: #666666;
		}

		.pk_4 {
			background: #FF9900;
		}

		.pk_5 {
			background: #66CCCC;
		}

		.pk_6 {
			background: #3300FF;
		}

		.pk_7 {
			background: #CCCCCC;
		}

		.pk_8 {
			background: #FF0000;
		}

		.pk_9 {
			background: #780B00;
		}

		.pk_10 {
			background: #009900;
		}
	</style>

</head>
<body>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover">

			<thead>
				<tr class="text-c">
					<th style="font-size:34px;" width="25%">期号</th>
					<th style="font-size:34px;" width="50%">开奖号码</th>
					<th style="font-size:34px;" width="50%">开奖时间</th>
				</tr>
			</thead>
			<tbody>
			<?php if($game == 'pk10' || $game == 'xyft' || $game == 'jsmt' || $game == 'jssc'){
    select_query('fn_open', '*', "type = $type order by term desc limit 50");
    while($con = db_fetch_array()){
        $num = explode(',', $con['code']);
        ?>
				<tr class="text-c">
					<td style="font-size:30px;"><?php echo $con['term'];
        ?></td>
					<td>
						<div style="margin:0 auto;width:450px;">
							<span class="pk_<?php echo (int)$num[0];
        ?>"><?php echo (int)$num[0];
        ?></span>
							<span class="pk_<?php echo (int)$num[1];
        ?>"><?php echo (int)$num[1];
        ?></span>
							<span class="pk_<?php echo (int)$num[2];
        ?>"><?php echo (int)$num[2];
        ?></span>
							<span class="pk_<?php echo (int)$num[3];
        ?>"><?php echo (int)$num[3];
        ?></span>
							<span class="pk_<?php echo (int)$num[4];
        ?>"><?php echo (int)$num[4];
        ?></span>
							<span class="pk_<?php echo (int)$num[5];
        ?>"><?php echo (int)$num[5];
        ?></span>
							<span class="pk_<?php echo (int)$num[6];
        ?>"><?php echo (int)$num[6];
        ?></span>
							<span class="pk_<?php echo (int)$num[7];
        ?>"><?php echo (int)$num[7];
        ?></span>
							<span class="pk_<?php echo (int)$num[8];
        ?>"><?php echo (int)$num[8];
        ?></span>
							<span class="pk_<?php echo (int)$num[9];
        ?>"><?php echo (int)$num[9];
        ?></span>
						</div>
					</td>
					<td style="font-size:20px;"><?php echo $con['time'];
        ?></td>
				</tr>
			<?php }
}elseif($game == 'cqssc' || $game == 'jsssc' || $game == 'azxy5'){
    select_query('fn_open', '*', "type = $type order by term desc limit 50");
    while($con = db_fetch_array()){
        $cons[] = $con;
    }
	//var_dump($cons);exit;
    $ay = true;
    foreach($cons as $con){
        if($ay){
            $line_row = "";
            $ay = false;
        }else{
            $line_row = "line_row";
            $ay = true;
        }
        $ys['小'] = 'blue';
        $ys['大'] = 'gray';
        $ys['单'] = 'blue';
        $ys['双'] = 'gray';
        $ys['龙'] = 'gray';
        $ys['虎'] = 'blue';
        $codes = explode(",", $con['code']);
        if(count($codes) != 5){
            continue;
        }else{       
          if($type == '3'){
                $number1 =  (int)$codes[0];
				$number2 =  (int)$codes[1];
            	$number3 =  (int)$codes[2];
            	$number4 =  (int)$codes[3];
            	$number5 =  (int)$codes[4];
                
            }elseif($type == '8'){
              	$number1 =  (int)$codes[0];
               	$number2 =  (int)$codes[1];
            	$number3 =  (int)$codes[2];
            	$number4 =  (int)$codes[3];
            	$number5 =  (int)$codes[4];
            }elseif($type == '15'){
              	$number1 =  (int)$codes[0];
               	$number2 =  (int)$codes[1];
            	$number3 =  (int)$codes[2];
            	$number4 =  (int)$codes[3];
            	$number5 =  (int)$codes[4];
            }
			
            	$number1 = substr($number1,-1);
            	$number2 = substr($number2,-1);
          		$number3 = substr($number3,-1);
          		$number4 = substr($number4,-1);
          		$number5 = substr($number5,-1);
                
           		$hz = (int)$number1 + (int)$number2 + (int)$number3 + (int)$number4 + (int)$number5;
     
        }
       	$ds = $hz % 2 != 0 ? '单' : '双';
		$dx = $hz > 22 ? '大':'小';
        ?>
				<tr class="text-c">
					<td style="font-size:30px;"><?php echo $con['term'];
        ?></td>
					<td>
						<div style="margin:0 auto;width:285px;">
							<span class="ball_s_ ball_s_blue ball_lenght5"><?php echo (int)$number1;
        ?></span>&nbsp;
							<span class="ball_s_ ball_s_blue ball_lenght5"><?php echo (int)$number2;
        ?></span>
							<span class="ball_s_ ball_s_blue ball_lenght5"><?php echo (int)$number3;
        ?></span>
							<span class="ball_s_ ball_s_blue ball_lenght5"><?php echo (int)$number4;
        ?></span>
							<span class="ball_s_ ball_s_blue ball_lenght5"><?php echo (int)$number5;
        ?></span>
							
						</div>
					</td>
					<td style="font-size:20px;"><?php echo $con['time'];
        ?></td>
				</tr>
			<?php }
}elseif($game == 'xy28' || $game == 'jnd28'){
    select_query('fn_open', '*', "type = $type order by term desc limit 50");
    while($con = db_fetch_array()){
        $codes = explode(",", $con['code']);
        if(count($codes) != 3 && count($codes) != 20){ //加拿大28只采集到3个号码,所以改动了该代码,原代码为:if(count($codes) != 20){
            continue;
        }else{
            if($type == '4'){
                $number1 = (int)$codes[0] + (int)$codes[1] + (int)$codes[2] + (int)$codes[3] + (int)$codes[4] + (int)$codes[5];
                $number2 = (int)$codes[6] + (int)$codes[7] + (int)$codes[8] + (int)$codes[9] + (int)$codes[10] + (int)$codes[11];
                $number3 = (int)$codes[12] + (int)$codes[13] + (int)$codes[14] + (int)$codes[15] + (int)$codes[16] + (int)$codes[17];
            }elseif($type == '5'){
                $number1 = (int)$codes[1] + (int)$codes[4] + (int)$codes[7] + (int)$codes[10] + (int)$codes[13] + (int)$codes[16];
                $number2 = (int)$codes[2] + (int)$codes[5] + (int)$codes[8] + (int)$codes[11] + (int)$codes[14] + (int)$codes[17];
                $number3 = (int)$codes[3] + (int)$codes[6] + (int)$codes[9] + (int)$codes[12] + (int)$codes[15] + (int)$codes[18];
            }
            $number1 = substr($number1, -1);
            $number2 = substr($number2, -1);
            $number3 = substr($number3, -1);
            $hz = (int)$number1 + (int)$number2 + (int)$number3;
        }
        ?>
				<tr class="text-c">
					<td style="font-size:30px;"><?php echo $con['term'];
        ?></td>
					<td>
						<div style="margin:0 auto;width:185px;">
							<span class="pcnum"><?php echo (int)$number1;
        ?></span>&nbsp;
							<span class="pcnum"><?php echo (int)$number2;
        ?></span>
							<span class="pcnum"><?php echo (int)$number3;
        ?></span>
							<span class="pcres"><?php echo (int)$hz;
        ?></span>
							
						</div>
					</td>
					<td style="font-size:20px;"><?php echo $con['time'];
        ?></td>
				</tr>
			<?php }
}elseif($game == 'bjl'){
    select_query('fn_open', '*', "type = $type order by term desc limit 50");
    while ($con = db_fetch_array()) {
        $cons[] = $con;
    }
    $ay = true;
    $bjl=new Bjl();
    foreach ($cons as $con) {
        if ($ay) {
            $line_row = "";
            $ay = false;
        } else {
            $line_row = "line_row";
            $ay = true;
        }
        $ys['庄'] = 'blue';
        $ys['闲'] = 'gray';
        $ys['和'] = 'blue';
        $ys['庄对'] = 'gray';
        $ys['闲对'] = 'gray';
        $ys['任意对'] = 'blue';
        $pb=$bjl->getPB($con['code']);
        $player=  implode(" ", $bjl->HtmlCard($pb['p']));
        $bank=  implode(" ", $bjl->HtmlCard($pb['b']));
        $result=$bjl->Result($pb['p'], $pb['b']);
        ?>
				<tr class="text-c">
					<td style="font-size:30px;"><?php echo $con['term'];
        ?></td>
					<td>
						<div style="margin:0 auto;width:350px;">
		<?php
            $zhi = $pb['b'][0] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk st" style="font-size:20px;">庄:</span>
            <span class="myyk" style="font-size:20px;"><?php echo $zhi; ?></span>
            <?php
            $zhi = $pb['b'][1] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk" style="font-size:20px;">+</span>
            <span class="myyk" style="font-size:20px;"><?php echo $zhi; ?></span>
            <?php
            $zhi = $pb['b'][2] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk" style="font-size:20px;"><?php echo isset($pb['b'][2])?"+":""; ?></span>
            <span class="myyk" style="font-size:20px;"><?php echo isset($pb['b'][2])?$zhi:""; ?></span>
		<?php
            $zhi = $pb['p'][0] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk st" style="font-size:20px;">==</span>
            <span class="myyk st" style="font-size:20px;">闲:</span>
            <span class="myyk" style="font-size:20px;"><?php echo $zhi; ?></span>
            <?php
            $zhi = $pb['p'][1] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk" style="font-size:20px;">+</span>
            <span class="myyk" style="font-size:20px;"><?php echo $zhi; ?></span>
        <?php
            $zhi = $pb['p'][2] % 13 + 1;
            if ($zhi == 11) {
                $zhi = 'J';
            }
            if ($zhi == 12) {
                $zhi = 'Q';
            }
            if ($zhi == 13) {
                $zhi = 'K';
            }
            ?>
            <span class="myyk" style="font-size:20px;"><?php echo isset($pb['p'][2])?"+":""; ?></span>
            <span class="myyk" style="font-size:20px;"><?php echo isset($pb['p'][2])?$zhi:""; ?></span>
			<span class="myyk" style="font-size:20px;">==</span>
			<mark class="gray" style="font-size:20px;"><?php if(in_array('BankerWin', $result)){
                echo '庄赢';
            }
            ?></mark>
            <mark class="blue" style="font-size:20px;"><?php if(in_array('PlayerWin', $result)){
                echo '闲赢';
            }
            ?></mark>
            <mark class="blue" style="font-size:20px;"><?php if(in_array('Draw', $result)){
                echo '和';
            }
            ?></mark>
						</div>
					</td>
					<td style="font-size:20px;"><?php echo $con['time'];
        ?></td>
				</tr>
			<?php }
}
?>
			</tbody>
		</table>
	</div>
</body>
</html>