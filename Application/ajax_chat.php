<?php
include_once("../Public/config.php");
include_once("../Public/Bjl.php");
session_start();
$type = $_GET['type'];
$BetGame = $_COOKIE['game'];
switch($type){
case 'first': $arr = array();
   select_query("fn_chat", '*', "roomid = {$_SESSION['roomid']} and game = '{$BetGame}' order by id desc limit 0,50");

    while($x = db_fetch_array()){
        if($x['userid'] == $_SESSION['userid']){
            $type = 'U2';
        }else{
            $type = $x['type'];
        }
        $arr[] = array('nickname' => $x['username'], 'headimg' => $x['headimg'], 'content' => $x['content']."...", 'addtime' => $x['addtime'], 'type' => $type, 'game' => $BetGame, 'id' => $x['id']);
    }
    echo json_encode($arr);
    break;
case "update": $arr = array();
    $chatid = $_GET['id'];
    select_query("fn_chat", '*', "roomid = {$_SESSION['roomid']} and game = '{$BetGame}' and id>$chatid order by id asc");
    while($x = db_fetch_array()){
        if($x['userid'] == $_SESSION['userid'])continue;
        $arr[] = array('nickname' => $x['username'], 'headimg' => $x['headimg'], 'content' => $x['content'], 'addtime' => $x['addtime'], 'type' => $x['type'], 'game' => $BetGame, 'id' => $x['id']);
    }
    echo json_encode($arr);
    break;
case "send": $nickname = $_SESSION['username'];
    $content = $_POST['content'];
    $headimg = $_SESSION['headimg'];
    if($BetGame == 'pk10'){
        if(get_query_val('fn_lottery1', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'xyft'){
        if(get_query_val('fn_lottery2', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'cqssc'){
        if(get_query_val('fn_lottery3', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'azxy5'){
        if(get_query_val('fn_lottery15', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'xy28'){
        if(get_query_val('fn_lottery4', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'jnd28'){
        if(get_query_val('fn_lottery5', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'jsmt'){
        if(get_query_val('fn_lottery6', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'jssc'){
        if(get_query_val('fn_lottery7', 'gameopen', array('roomid' =>   $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'jsssc'){
        if(get_query_val('fn_lottery8', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }elseif($BetGame == 'bjl'){
        if(get_query_val('fn_lottery9', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
    }
    if($BetGame == 'pk10'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 1 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery1', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 1 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'xyft'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 2 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery2', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 2 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'cqssc'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 3 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery3', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 3 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'azxy5'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 15 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery15', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 15 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'xy28'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 4 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery4', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 4 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'jnd28'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 5 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery5', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 5 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'jsmt'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 6 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery6', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 6 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'jssc'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 7 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery7', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 7 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'jsssc'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 8 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery8', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 8 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'bjl'){
        $BetTerm = get_query_val('fn_open', 'next_term', "type = 9 order by term desc limit 1");
        $time = (int)get_query_val('fn_lottery9', 'fengtime', array('roomid' => $_SESSION['roomid']));
        $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 9 order by term desc limit 1')) - time();
        if($djs < $time){
            $fengpan = true;
        }else{
            $fengpan = false;
        }
    }elseif($BetGame == 'feng'){
        $fengpan = true;
    }
    if(substr($content, 0, 1) == '@'){
        $type = "U1";
    }else{
        $type = "U3";
    }
    if(get_query_val("fn_ban", "id", array("roomid" => $_SESSION['roomid'], 'userid' => $_SESSION['userid'])) != ""){
        echo json_encode(array('success' => false, 'msg' => '???????????????????????????????????????!'));
        break;
    }elseif(!wordkeys($content)){
        echo json_encode(array('success' => false, 'msg' => '????????????????????????????????????!'));
        break;
    }
    if($type == 'U3'){
		
        if(substr($content, 0, 6) == '??????'){
            $fenshuchange = true;
            $sfmoney = substr($content, 6);
            if((int)$sfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $sfmoney);
        }elseif(substr($content, 0, 3) == '???'){
            $fenshuchange = true;
            $sfmoney = substr($content, 3);
            if((int)$sfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $sfmoney);
        }elseif(substr($content, 0, 3) == '???'){
            $fenshuchange = true;
            $sfmoney = substr($content, 3);
            if((int)$sfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $sfmoney);
        }elseif(substr($content, 0, 6) == '??????'){
            $fenshuchange = true;
            $xfmoney = substr($content, 6);
            if((int)$xfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $xfmoney);
        }elseif(substr($content, 0, 3) == '???'){
            $fenshuchange = true;
            $xfmoney = substr($content, 3);
            if((int)$xfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $xfmoney);
        }elseif(substr($content, 0, 3) == '???'){
            $fenshuchange = true;
            $xfmoney = substr($content, 3);
            if((int)$xfmoney > 0)????????????($_SESSION['username'], $_SESSION['userid'], $xfmoney);
        }else{
            $fenshuchange = false;
        }
        if($content == "??????"){
            CancelBet($_SESSION['userid'], $BetTerm, $BetGame, $fengpan);
            echo json_encode(array("success" => true, "content" => $content));
            insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => $type, 'userid' => $_SESSION['userid'], 'roomid' => $_SESSION['roomid']));
            break;
        }
    }
    if($type == 'U3' && $fenshuchange == false && ($BetGame == 'xy28' || $BetGame == 'jnd28')){
        $co = addPCBet($_SESSION['userid'], $_SESSION['username'], $_SESSION['headimg'], $content, $BetTerm, $fengpan);
    }elseif($type == 'U3' && $fenshuchange == false && ($BetGame == 'cqssc' || $BetGame == 'jsssc' ||$BetGame == 'azxy5' )){
        $co = addSSCBet($_SESSION['userid'], $_SESSION['username'], $_SESSION['headimg'], $content, $BetTerm, $fengpan);
    } elseif ($type == 'U3' && $fenshuchange == false && ($BetGame == 'bjl')) {
        //myy ?????????????????????
        //??????????????????
        $bjl = new Bjl();
        $cur = $bjl->get_period_info($bjl->getTodayCur());
        $myy_time = strtotime($cur['awardTime']);
        if (time() - $myy_time < 10) {
            ???????????????("@" . $_SESSION['username'] . " ,[$BetTerm]????????????????????????");exit;
        } else {
            $co = addBJLBet($_SESSION['userid'], $_SESSION['username'], $_SESSION['headimg'], $content, $BetTerm, $fengpan);
        }
    }elseif($type == 'U3' && $fenshuchange == false){
        $co = addBet($_SESSION['userid'], $_SESSION['username'], $_SESSION['headimg'], $content, $BetTerm, $fengpan);
    }
	var_dump($co);
	var_dump($fenshuchange);
    if(get_query_val("fn_setting", "setting_ischat", array("roomid" => $_SESSION['roomid'])) == 'open' && !$co && !$fenshuchange){
        echo json_encode(array('success' => false, 'msg' => "??????????????????????????????????????????"));
        break;
    }else{
        echo json_encode(array("success" => true, "content" => $content));
        insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => $type, 'userid' => $_SESSION['userid'], 'roomid' => $_SESSION['roomid']));
    }
    break;
}
function sum_betmoney($table, $mc, $cont, $user, $term){
$re = get_query_val($table, 'sum(`money`)', array('userid' => $user, 'term' => $term, 'mingci' => $mc, 'content' => $cont));
return (int)$re;
}
function str_replace_once($needle, $replace, $haystack){
$pos = strpos($haystack, $needle);
if ($pos === false){
    return $haystack;
}
return substr_replace($haystack, $replace, $pos, strlen($needle));
}
function runrobot($BetGame){
$open = get_query_val('fn_setting', 'setting_runrobot', array('roomid' => $_SESSION['roomid']));
if($open != 'true'){
    return;
}
if($BetGame == 'pk10'){
    if(get_query_val('fn_lottery1', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}elseif($BetGame == 'xyft'){
    if(get_query_val('fn_lottery2', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}elseif($BetGame == 'cqssc'){
    if(get_query_val('fn_lottery3', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}elseif($BetGame == 'xy28'){
    if(get_query_val('fn_lottery4', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}elseif($BetGame == 'jnd28'){
    if(get_query_val('fn_lottery5', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}elseif($BetGame == 'jsmt'){
    if(get_query_val('fn_lottery6', 'gameopen', array('roomid' => $_SESSION['roomid'])) == 'false')$BetGame = 'feng';
}
if($BetGame == 'pk10'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 1 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery1', 'fengtime', array('roomid' => $_SESSION['roomid']));
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 1 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'xyft'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 2 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery2', 'fengtime', array('roomid' => $_SESSION['roomid']));
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 2 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'cqssc'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 3 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery3', 'fengtime', array('roomid' => $_SESSION['roomid']));
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 3 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'xy28'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 4 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery4', 'fengtime');
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 4 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'jnd28'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 5 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery5', 'fengtime');
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 5 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'jsmt'){
    $BetTerm = get_query_val('fn_open', 'next_term', "type = 6 order by term desc limit 1");
    $time = (int)get_query_val('fn_lottery6', 'fengtime');
    $djs = strtotime(get_query_val('fn_open', 'next_time', 'type = 6 order by term desc limit 1')) - time();
    if($djs < $time){
        $fengpan = true;
    }else{
        $fengpan = false;
    }
}elseif($BetGame == 'feng'){
    $fengpan = true;
}
if(!$fengpan){
    $robots = get_query_vals('fn_robots', '*', "roomid = {$_SESSION['roomid']} and game = '{$BetGame}' order by rand() desc limit 1");
    $headimg = $robots['headimg'];
    $name = $robots['name'];
    $plan = $robots['plan'];
    $plan = explode('|', $plan);
    if($headimg == '' || $name == '' || $plan == '')return;
    $use = rand(0, count($plan)-1);
    $plan = get_query_val('fn_robotplan', 'content', array('id' => $plan[$use]));
    if(preg_match("/{????????????}/", $plan)){
        $i2 = substr_count($plan, '{????????????}');
        for($i = 0;$i < $i2;$i++){
            $plan = str_replace_once("{????????????}", rand(0, 9), $plan);
        }
    }
    if(preg_match("/{????????????}/", $plan)){
        $i2 = substr_count($plan, '{????????????}');
        for($i = 0;$i < $i2;$i++){
            $plan = str_replace_once("{????????????}", rand(0, 9), $plan);
        }
    }
    if(preg_match("/{????????????}/", $plan)){
        $val = rand(1, 4);
        if($val == 1){
            $val = '???';
        }elseif($val == 2){
            $val = '???';
        }elseif($val == 3){
            $val = '???';
        }elseif($val == 4){
            $val = '???';
        }
        $plan = str_replace('{????????????}', $val, $plan);
    }
    if(preg_match("/{????????????}/", $plan)){
        $val = rand(1, 2);
        if($val == 1){
            $val = '???';
        }elseif($val == 2){
            $val = '???';
        }
        $plan = str_replace('{????????????}', $val, $plan);
    }
    if(preg_match("/{????????????}/", $plan)){
        $val = rand(1, 2);
        if($val == 1){
            $val = '??????';
        }elseif($val == 2){
            $val = '??????';
        }
        $plan = str_replace('{????????????}', $val, $plan);
    }
    if(preg_match("/{????????????1}/", $plan)){
        $val = rand(1, 2);
        if($val == 1){
            $val = '??????';
        }elseif($val == 2){
            $val = '??????';
        }
        $plan = str_replace('{????????????1}', $val, $plan);
    }
    if(preg_match("/{????????????2}/", $plan)){
        $val = rand(1, 2);
        if($val == 1){
            $val = '??????';
        }elseif($val == 2){
            $val = '??????';
        }
        $plan = str_replace('{????????????2}', $val, $plan);
    }
    if(preg_match("/{????????????}/", $plan)){
        $i2 = substr_count($plan, '{????????????}');
        for($i = 0;$i < $i2;$i++){
            $plan = str_replace_once("{????????????}", rand(0, 27), $plan);
        }
    }
    if(preg_match("/{????????????}/", $plan)){
        $i2 = substr_count($plan, '{????????????}');
        for($i = 0;$i < $i2;$i++){
            $plan = str_replace_once("{????????????}", rand(3, 19), $plan);
        }
    }
    if(preg_match("/{????????????}/", $plan)){
        $val = rand(1, 3);
        if($val == 1){
            $val = '??????';
        }elseif($val == 2){
            $val = '??????';
        }elseif($val == 3){
            $val = '??????';
        }
        $plan = str_replace('{????????????}', $val, $plan);
    }
    if(preg_match("/{????????????1}/", $plan)){
        $plan = str_replace('{????????????1}', rand(20, 300), $plan);
    }
    if(preg_match("/{????????????2}/", $plan)){
        $plan = str_replace('{????????????2}', rand(300, 1000), $plan);
    }
    if(preg_match("/{????????????3}/", $plan)){
        $plan = str_replace('{????????????3}', rand(1000, 3000), $plan);
    }
    insert_query("fn_chat", array("userid" => "robot", "username" => $name, 'headimg' => $headimg, 'content' => $plan, 'addtime' => date('H:i:s'), 'game' => $BetGame, 'roomid' => $_SESSION['roomid'], 'type' => 'U3'));
    if(get_query_val("fn_setting", "setting_tishi", array("roomid" => $_SESSION['roomid'])) == 'open'){
        ???????????????("@$name,???????????????????????????????????????????????????");
    }
}
}
function wordkeys($content){
$keys = get_query_val('fn_setting', 'setting_wordkeys', array('roomid' => $_SESSION['roomid']));
$arr = explode("|", $keys);
foreach($arr as $con){
    if($con == ""){
        continue;
    }
    if(preg_match("/$con/", $content)){
        return false;
    }
}
return true;
}
function ??????_????????????($str, $split_len = 1){
if (!preg_match('/^[0-9]+$/', $split_len) || $split_len < 1)return FALSE;
$len = mb_strlen($str, 'UTF-8');
if ($len <= $split_len)return array($str);
preg_match_all("/.{" . $split_len . '}|[^x00]{1,' . $split_len . '}$/us', $str, $ar);
return $ar[0];
}
function ???????????????($str){
$arr = ??????_????????????($str);
$new = array();
foreach($arr as $ii){
    if($ii == "???"){
        $new[] = "??????";
        continue;
    }
    if($ii == "???"){
        $new[] = "??????";
        continue;
    }
    if($ii == "???"){
        $new[] = "??????";
        continue;
    }
    continue;
}
return $new;
}
function ????????????($str){
$arr = ??????_????????????($str);
$new = array();
$ii_1_b = true;
$ii_1 = '';
foreach($arr as $ii){
    if(!$ii_1_b && $ii_1 == "1")$ii = "1" . $ii;
    $ii_1 = $ii;
    if($ii_1_b)$ii_1_b = false;
    if($ii == "1")continue;
    array_push($new, $ii);
}
return $new;
}
function ??????????????????($Userid){
return (int)get_query_val('fn_user', 'money', array('userid' => $Userid, 'roomid' => $_SESSION['roomid']));
}
function ??????_??????($Userid, $Money){
update_query('fn_user', array('money' => '-=' . $Money), array('userid' => $Userid, 'roomid' => $_SESSION['roomid']));
insert_query("fn_marklog", array("userid" => $Userid, 'type' => '??????', 'content' => '????????????', 'money' => $Money, 'roomid' => $_SESSION['roomid'], 'addtime' => 'now()'));
}
function ??????_??????($Userid, $Money){
update_query('fn_user', array('money' => '+=' . $Money), array('userid' => $Userid, 'roomid' => $_SESSION['roomid']));
insert_query("fn_marklog", array("userid" => $Userid, 'type' => '??????', 'content' => '??????????????????', 'money' => $Money, 'roomid' => $_SESSION['roomid'], 'addtime' => 'now()'));
}
function ???????????????($Content){
$headimg = get_query_val('fn_setting', 'setting_robotsimg', array('roomid' => $_SESSION['roomid']));
insert_query("fn_chat", array("userid" => "system", "username" => "?????????", "game" => $_COOKIE['game'], 'headimg' => $headimg, 'content' => $Content, 'addtime' => date('H:i:s'), 'type' => 'S3', 'roomid' => $_SESSION['roomid']));
}
function ????????????($username, $userid, $money){
$jia = get_query_val('fn_user', 'jia', array('userid' => $userid));
insert_query("fn_upmark", array("userid" => $userid, 'headimg' => $_SESSION['headimg'], 'username' => $username, 'type' => '??????', 'money' => $money, 'status' => '?????????', 'time' => 'now()', 'game' => $_COOKIE['game'], 'roomid' => $_SESSION['roomid'], 'jia' => $jia));
}
function ????????????($username, $userid, $money){
$m = (int)get_query_val('fn_user', 'money', array('roomid' => $_SESSION['roomid'], 'userid' => $userid));
if(($m - (int)$money) < 0){
    ???????????????("@$username,????????????????????????????????????", $game);
    return;
}
$jia = get_query_val('fn_user', 'jia', array('userid' => $userid));
insert_query("fn_upmark", array("userid" => $userid, 'headimg' => $_SESSION['headimg'], 'username' => $username, 'type' => '??????', 'money' => $money, 'status' => '?????????', 'time' => 'now()', 'game' => $_COOKIE['game'], 'roomid' => $_SESSION['roomid'], 'jia' => $jia));
if(get_query_val("fn_setting", "setting_downmark", array("roomid" => $_SESSION['roomid'])) == 'true'){
    update_query('fn_user', array('money' => '-=' . $money), array('userid' => $userid, 'roomid' => $_SESSION['roomid']));
    insert_query("fn_marklog", array("roomid" => $_SESSION['roomid'], 'userid' => $userid, 'type' => '??????', 'content' => '????????????????????????' . $money, 'money' => $money, 'addtime' => 'now()'));
    $headimg = get_query_val('fn_setting', 'setting_sysimg', array('roomid' => $_SESSION['roomid']));
    insert_query("fn_chat", array("userid" => "system", "username" => "?????????", "game" => $_COOKIE['game'], 'headimg' => $headimg, 'content' => "@{$username}, ????????????????????????????????????????????????", 'addtime' => date('H:i:s'), 'type' => 'S1', 'roomid' => $_SESSION['roomid']));
}
}
function CancelBet($userid, $term, $game, $fengpan){
$chedan = get_query_val('fn_setting', 'setting_cancelbet', array('roomid' => $_SESSION['roomid'])) == 'open' ? true : false;
if($chedan){
    return;
}else{
    if($fengpan){
        ??????????????? ("@" . $_SESSION['username'] . " ,[$term]???????????????????????????????????????");
        return false;
    }
    switch($game){
    case 'xy28': $table = "fn_pcorder";
        break;
    case "jnd28": $table = "fn_pcorder";
        break;
	case "bjl": $table = "fn_bjlorder";
        break;
    case "jsmt": $table = "fn_jsmtorder";
        break;
    case "jssc": $table = "fn_jsscorder";
        break;
    case "jsssc": $table = "fn_jssscorder";
        break;
    case "cqssc": $table = "fn_sscorder";
        break;
    case "pk10": $table = "fn_order";
        break;
    case "xyft": $table = "fn_order";
        break;
    }
    $all = (int)get_query_val($table, 'sum(`money`)', "userid = '$userid' and term = '$term' and status = '?????????' and roomid = {$_SESSION['roomid']}");
    update_query($table, array('status' => '?????????'), "userid = '$userid' and term = '$term' and roomid = {$_SESSION['roomid']}");
    ??????_??????($userid, $all);
    ???????????????("@{$_SESSION['username']} ,[$term]????????????????????????");
}
}
function addBet($userid, $nickname, $headimg, $content, $addQihao, $fengpan){
if($fengpan){
    ??????????????? ("@" . $nickname . " ,[$addQihao]???????????????????????????????????????");
    return false;
}
$content = str_replace("?????????", "???", $content);
$content = str_replace("??????", "???", $content);
$content = str_replace("??????", "1/", $content);
$content = str_replace("??????", "2/", $content);
$content = str_replace("???", "1/", $content);
$content = str_replace("???", "2/", $content);
$content = str_replace("???", "1/", $content);
$content = str_replace("???", "2/", $content);
$content = str_replace("???", "3/", $content);
$content = str_replace("???", "4/", $content);
$content = str_replace("???", "5/", $content);
$content = str_replace("???", "6/", $content);
$content = str_replace("???", "7/", $content);
$content = str_replace("???", "8/", $content);
$content = str_replace("???", "9/", $content);
$content = str_replace("???", "0/", $content);
$content = str_replace(".", "/", $content);
$content = preg_replace("/[?????????-]/u", "/", $content);
$content = preg_replace("/(???|???|H|h)\//u", "$1", $content);
$content = preg_replace("/[??????Hh]/u", "???/", $content);
$content = preg_replace("/(??????|??????|??????|??????|???|???|???|???|???|???)\//u", "$1", $content);
$content = preg_replace("/\/(??????|??????|??????|??????|???|???|???|???|???|???)/u", "$1", $content);
$content = preg_replace("/(??????|??????|??????|??????|???|???|???|???|???|???)/u", "/$1/", $content);
if($_COOKIE['game'] == 'pk10'){
    $table = 'fn_lottery1';
}elseif($_COOKIE['game'] == 'xyft'){
}elseif($_COOKIE['game'] == 'jsmt'){
}elseif($_COOKIE['game'] == 'jssc'){
}
switch($_COOKIE['game']){
case 'pk10': $table = 'fn_lottery1';
    $ordertable = "fn_order";
    break;
case "xyft": $table = 'fn_lottery2';
    $ordertable = "fn_order";
    break;
case "jsmt": $table = 'fn_lottery6';
    $ordertable = "fn_mtorder";
    break;
case "jssc": $table = 'fn_lottery7';
    $ordertable = "fn_jsscorder";
    break;
}
$dx_min = get_query_val($table, 'daxiao_min', array('roomid' => $_SESSION['roomid']));
$dx_max = get_query_val($table, 'daxiao_max', array('roomid' => $_SESSION['roomid']));
$ds_min = get_query_val($table, 'danshuang_min', array('roomid' => $_SESSION['roomid']));
$ds_max = get_query_val($table, 'danshuang_max', array('roomid' => $_SESSION['roomid']));
$lh_min = get_query_val($table, 'longhu_min', array('roomid' => $_SESSION['roomid']));
$lh_max = get_query_val($table, 'longhu_max', array('roomid' => $_SESSION['roomid']));
$tm_min = get_query_val($table, 'tema_min', array('roomid' => $_SESSION['roomid']));
$tm_max = get_query_val($table, 'tema_max', array('roomid' => $_SESSION['roomid']));
$hz_min = get_query_val($table, 'he_min', array('roomid' => $_SESSION['roomid']));
$hz_max = get_query_val($table, 'he_max', array('roomid' => $_SESSION['roomid']));
$zh_min = get_query_val($table, 'zuhe_min', array('roomid' => $_SESSION['roomid']));
$zh_max = get_query_val($table, 'zuhe_max', array('roomid' => $_SESSION['roomid']));
$zym_8 = get_query_val('fn_user', 'jia', array('userid' => $userid, 'roomid' => $_SESSION['roomid'])) == 'true' ? 'true' : 'false';
$touzhu = false;
$A = explode(" ", $content);
$zym_2 = "";
foreach($A as $ai){
$ai = str_replace(" ", "", $ai);
if(empty($ai))continue;
if(substr($ai, 0, 1) == '/')$ai = '1' . $ai;
$b = explode("/", $ai);
if(count($b) == 2){
$ai = '1/' . $ai;
$b = explode("/", $ai);
}
if(count($b) != 3)continue;
if($b[0] == "" || $b[1] == "" || (int)$b[2] < 1)continue;
$zym_9 = ?????????????????? ($userid);
$zym_10 = $b[0];
$zym_6 = $b[1];
$zym_5 = (int)$b[2];
if($zym_6 == '???'){
??????????????? ("@" . $nickname . " ,??????????????????????????????????????????????????????3/100");
continue;
}
if($zym_10 == '???'){
if($zym_6 == "??????" || $zym_6 == "??????" || $zym_6 == "??????" || $zym_6 == "??????"){
    ??????????????? ("@" . $nickname . " ,??????????????????????????????????????????????????????");
    continue;
}
if($zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???"){
    if((int)$zym_9 < (int)$zym_5){
        $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        continue;
    }elseif($zym_5 < $hz_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $hz_max){
        $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }
    ??????_??????($userid, $zym_5);
    insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
    $touzhu = true;
    continue;
}
$zym_6_?????? = ???????????? ($zym_6);
foreach($zym_6_?????? as $ii){
    if($ii < 3 || $ii > 19){
        ??????????????? ("@" . $nickname . " ,????????????????????????????????????3 - 19??????????????????");
        break;
    }
    if(!is_numeric($ii)){
        continue;
    }elseif((int)$zym_9 < count($zym_6_??????) * (int)$zym_5){
        $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        break;
    }elseif($zym_5 < $hz_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $hz_max){
        $zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }
    $touzhu = true;
    ??????_?????? ($userid, $zym_5);
    insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $ii, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
    continue;
}
continue;
}
if($zym_6 == "??????" || $zym_6 == "??????" || $zym_6 == "??????" || $zym_6 == "??????"){
$zym_10_?????? = ??????_???????????? ($zym_10);
foreach($zym_10_?????? as $ii){
    if(!is_numeric($ii)){
        continue;
    }elseif($zym_9 < count($zym_10_??????) * (int)$zym_5){
        $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        break;
    }elseif($zym_5 < $zh_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $zh_max){
        $zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }
    $touzhu = true;
    ??????_??????($userid, $zym_5);
    insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}
continue;
}
if($zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???"){
$zym_10_?????? = ??????_???????????? ($zym_10);
foreach ($zym_10_?????? as $ii){
    if(!is_numeric($ii)){
        continue;
    }elseif($zym_9 < count($zym_10_??????) * (int)$zym_5){
        $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        break;
    }elseif($zym_5 < $dx_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }elseif($zym_5 < $dx_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }elseif($zym_5 < $ds_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }elseif($zym_5 < $ds_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }elseif($zym_5 < $lh_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $lh_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }elseif($zym_5 < $lh_min || sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $lh_max && $zym_6 == "???"){
        $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }
    if((int)$ii > 5 && $zym_6 == '???' || (int)$ii > 5 && $zym_6 == '???'){
        ???????????????("@{$nickname},??????????????????1~5??????");
        continue;
    }
    $touzhu = true;
    ??????_??????($userid, $zym_5);
    insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}
continue;
}
$zym_6_?????? = ??????_???????????? ($zym_6);
$zym_10_?????? = ??????_???????????? ($zym_10);
foreach ($zym_10_?????? as $ii){
if($zym_9 < count($zym_10_??????) * count($zym_6_??????) * (int)$zym_5){
    $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
    break;
}else if(!is_numeric($ii)){
    continue;
}
foreach ($zym_6_?????? as $iii){
    if(!is_numeric($iii)){
        continue;
    }else if($zym_5 < $tm_min || sum_betmoney($ordertable, $ii, $iii, $userid, $addQihao) + $zym_5 > $tm_max){
        $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
        $chaozhu = true;
        continue;
    }
    $touzhu = true;
    ??????_??????($userid, $zym_5);
    insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $iii, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}
}
}
if($zym_2 != ""){
if($chaozhu){
???????????????("@{$nickname},??????:{$zym_2}??????<br>??????????????????????????????<br>???????????????????????????<br>????????????{$dx_min}???,??????{$dx_max}<br>????????????{$ds_min}???,??????{$ds_max}<br>????????????{$lh_min}???,??????{$lh_max}<br>????????????{$tm_min}???,??????{$tm_max}<br>????????????{$hz_min}???,??????{$hz_max}<br>------------<br>????????????????????????????????????");
return true;
}else{
???????????????("@{$nickname},??????:{$zym_2}????????????????????????" . ??????????????????($userid));
return true;
}
}elseif(get_query_val("fn_setting", "setting_tishi", array("roomid" => $_SESSION['roomid'])) == 'open' && $touzhu == true){
???????????????("@$nickname,???????????????????????????????????????????????????");
return true;
}elseif($touzhu){
return true;
}
return false;
}
function addPCBet($userid, $nickname, $headimg, $content, $addQihao, $fengpan){
if($fengpan){
??????????????? ("@" . $nickname . " ,[$addQihao]???????????????????????????????????????");
return false;
}
$content = str_replace(".", "/", $content);
$content = preg_replace("/[????????????,-]/u", "/", $content);
$content = preg_replace("/(??????|??????|??????|??????|??????|??????|??????|??????|??????|???|???|???|???|???)\//u", "$1", $content);
$content = preg_replace("/(??????|??????|??????|??????|??????|??????|??????|??????|??????|???|???|???|???|???)/u", "$1/", $content);
switch($_COOKIE['game']){
case 'xy28': $table = 'fn_lottery4';
$ordertable = 'fn_pcorder';
break;
case "jnd28": $table = 'fn_lottery5';
$ordertable = 'fn_pcorder';
break;
}
$zym_17_min = (int)get_query_val($table, 'danzhu_min', array('roomid' => $_SESSION['roomid']));
$zym_16_max = (int)get_query_val($table, 'shuzi_max', array('roomid' => $_SESSION['roomid']));
$zym_15_max = (int)get_query_val($table, 'dxds_max', array('roomid' => $_SESSION['roomid']));
$zym_19_max = (int)get_query_val($table, 'zuhe_max', array('roomid' => $_SESSION['roomid']));
$zym_11_max = (int)get_query_val($table, 'jidx_max', array('roomid' => $_SESSION['roomid']));
$zym_20_max = (int)get_query_val($table, 'baozi_max', array('roomid' => $_SESSION['roomid']));
$zym_18_max = (int)get_query_val($table, 'duizi_max', array('roomid' => $_SESSION['roomid']));
$zym_13_max = (int)get_query_val($table, 'shunzi_max', array('roomid' => $_SESSION['roomid']));
$zym_12_max = (int)get_query_val($table, 'zongzhu_max', array('roomid' => $_SESSION['roomid']));
$zym_4 = get_query_val($table, 'setting_shazuhe', array('roomid' => $_SESSION['roomid']));
$zym_3 = get_query_val($table, 'setting_fanxiangzuhe', array('roomid' => $_SESSION['roomid']));
$zym_1 = get_query_val($table, 'setting_tongxiangzuhe', array('roomid' => $_SESSION['roomid']));
$zym_14_?????? = (int)get_query_val($table, 'setting_liwai', array('roomid' => $_SESSION['roomid']));
$touzhu = false;
$chaozhu = false;
$jinzhi = false;
$A = explode(' ', $content);
$zym_8 = get_query_val('fn_user', 'jia', array('userid' => $userid, 'roomid' => $_SESSION['roomid'])) == 'true' ? 'true' : 'false';
$zym_2 = "";
foreach($A as $i){
$i = str_replace(" ", "", $i);
if(empty($i))continue;
$b = explode('/', $i);
$zym_9 = ?????????????????? ($userid);
if(count($b) == 3 && $b[0] == '???'){
unset($b[2]);
$b[0] = $b[1];
$b[1] = $zym_9;
}
if(count($b) != 2)continue;
if($b[0] == "" || (int)$b[1] < 1)continue;
$zym_6 = $b[0];
$zym_5 = (int)$b[1];
if($zym_5 < $zym_17_min){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
$zym_7 = (int)get_query_val('fn_pcorder', 'sum(`money`)', "`userid` = '{$userid}' and `status` = '?????????' and `term` = '$addQihao'");
if($zym_7 + $zym_5 > $zym_12_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
if($zym_6 == '???' || $zym_6 == '???' || $zym_6 == '???' || $zym_6 == '???'){
if($zym_5 > $zym_15_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}else if($zym_9 < $zym_5){
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
if($zym_4 == 'true'){
switch($zym_6){
case '???': $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
break;
case "???": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
break;
case "???": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
break;
case "???": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '??????'));
break;
}
if($betting1 != "" || $betting2 != ""){
if($zym_14_?????? > 0 && $zym_7 + $zym_5 > $zym_14_??????){
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}else{
$jinzhi = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
}
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
continue;
}elseif($zym_6 == '??????' || $zym_6 == '??????' || $zym_6 == '??????' || $zym_6 == '??????'){
if($zym_5 > $zym_19_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}else if($zym_9 < $zym_5){
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
if($zym_4 == 'true'){
switch($zym_6){
case '??????': $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
break;
case "??????": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
break;
case "??????": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
break;
case "??????": $betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
$betting2 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => '???'));
break;
}
if($betting1 != "" || $betting2 != ""){
if($zym_14_?????? > 0 && $zym_7 + $zym_5 > $zym_14_??????){
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}else{
$jinzhi = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
}
if($zym_1 == 'true'){
switch($zym_6){
case '??????':$sql = '??????';
break;
case "??????":$sql = '??????';
break;
case "??????":$sql = '??????';
break;
case "??????":$sql = '??????';
break;
}
$betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => $sql));
if($betting1 != ""){
if($zym_14_?????? > 0 && $zym_7 + $zym_5 > $zym_14_??????){
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}else{
$jinzhi = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
}
if($zym_3 == 'true'){
switch($zym_6){
case '??????':$sql = '??????';
break;
case "??????":$sql = '??????';
break;
case "??????":$sql = '??????';
break;
case "??????":$sql = '??????';
break;
}
$betting1 = get_query_val('fn_pcorder', 'content', array('userid' => $userid, 'term' => $addQihao, 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'content' => $sql));
if($betting1 != ""){
if($zym_14_?????? > 0 && $zym_7 + $zym_5 > $zym_14_??????){
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
}else{
$jinzhi = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
}
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
continue;
}elseif($zym_6 == '??????' || $zym_6 == '??????'){
if($zym_5 > $zym_11_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}else if($zym_9 < $zym_5){
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
continue;
}elseif($zym_6 == '??????' || $zym_6 == '??????' || $zym_6 == '??????'){
switch($zym_6){
case '??????': if($zym_20_max == 0){
continue;
}else{
if($zym_5 > $zym_20_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
break;
case "??????": if($zym_18_max == 0){
continue;
}else{
if($zym_5 > $zym_18_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
break;
case "??????": if($zym_13_max == 0){
continue;
}else{
if($zym_5 > $zym_13_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
}
break;
}
if($zym_9 < $zym_5){
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
continue;
}else{
if($zym_5 > $zym_16_max){
$chaozhu = true;
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}else if($zym_9 < $zym_5){
$zym_2 = $zym_6 . '/' . $zym_5;
continue;
}else if(!is_numeric($zym_6)){
continue;
}
$touzhu = true;
??????_?????? ($userid, $zym_5);
insert_query("fn_pcorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
continue;
}
}
if($zym_2 != ""){
if($chaozhu){
???????????????("@$nickname,????????????????????????<br>???????????????????????????<br>??????????????????{$zym_17_min},??????{$zym_16_max}<br>??????????????????{$zym_17_min},??????{$zym_15_max}<br>????????????{$zym_17_min},??????{$zym_19_max}<br>??????????????????{$zym_17_min},??????{$zym_11_max}<br>????????????{$zym_17_min},??????{$zym_20_max}<br>????????????{$zym_17_min},??????{$zym_18_max}<br>????????????{$zym_17_min},??????{$zym_13_max}<br>-----------<br>?????????????????????{$zym_12_max},???????????????{$zym_7}");
return true;
}elseif($jinzhi){
$nr = "";
if($zym_4 == 'true'){
$nr .= '[???????????????]';
}
if($zym_1 == 'true'){
$nr .= '[??????????????????]';
}
if($zym_3 == 'true'){
$nr .= '[??????????????????]';
}
if($zym_14_?????? != 0){
$nr2 = '<br>?????????????????????' . $zym_14_?????? . '???,????????????????????????!';
}else{
$nr2 = "";
}
???????????????("@{$nickname},?????????{$zym_2}??????<br>??????$nr" . $nr2);
return true;
}else{
???????????????("@{$nickname},?????????{$zym_2}????????????????????????" . ??????????????????($userid));
return true;
}
}elseif(get_query_val("fn_setting", "setting_tishi", array("roomid" => $_SESSION['roomid'])) == 'open' && $touzhu == true){
???????????????("@{$nickname},???????????????????????????????????????????????????");
return true;
}elseif($touzhu){
return true;
}
return false;
}
function addBJLBet($userid, $nickname, $headimg, $content, $addQihao, $fengpan) {

if ($fengpan) {
    ???????????????("@" . $nickname . " ,[$addQihao]???????????????????????????????????????");
    return false;
}
$content = str_replace(".", "/", $content);
$content = preg_replace("/[????????????,-]/u", "/", $content);
$content = preg_replace("/??????/u", "zd", $content);
$content = preg_replace("/??????/u", "xd", $content);
$content = preg_replace("/(???|???|???|zd|xd|?????????|???)\//u", "$1", $content);
$content = preg_replace("/(???|???|???|zd|xd|?????????|???)/u", "$1/", $content);
$content = preg_replace("/zd/u", "??????", $content);
$content = preg_replace("/xd/u", "??????", $content);
switch ($_COOKIE['game']) {
    case 'bjl': $table = 'fn_lottery9';
        $ordertable = 'fn_bjlorder';
        break;
}
$touzhu = false;
$chaozhu = false;
$jinzhi = false;
$A = explode(' ', $content);
$zym_8 = get_query_val('fn_user', 'jia', array('userid' => $userid, 'roomid' => $_SESSION['roomid'])) == 'true' ? 'true' : 'false';
$zym_2 = "";
foreach ($A as $i) {
    $i = str_replace(" ", "", $i);
    if (empty($i))
        continue;
    $b = explode('/', $i);
    $zym_9 = ??????????????????($userid);
    if (count($b) == 3 && $b[0] == '???') {
        unset($b[2]);
        $b[0] = $b[1];
        $b[1] = $zym_9;
    }
    if (count($b) != 2)
        continue;
    if ($b[0] == "" || (int) $b[1] < 1)
        continue;
    $zym_6 = $b[0];
    $zym_5 = (int) $b[1];
if ((int) $zym_9 < (int) $zym_5) {
                $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                continue;
            }
    $zhuang_max = (int) get_query_val($table, 'zhuang_max', array('roomid' => $_SESSION['roomid']));
    $zhuang_min = (int) get_query_val($table, 'zhuang_min', array('roomid' => $_SESSION['roomid']));
    $xian_max = (int) get_query_val($table, 'xian_max', array('roomid' => $_SESSION['roomid']));
    $xian_min = (int) get_query_val($table, 'xian_min', array('roomid' => $_SESSION['roomid']));
    $he_max = (int) get_query_val($table, 'he_max', array('roomid' => $_SESSION['roomid']));
    $he_min = (int) get_query_val($table, 'he_min', array('roomid' => $_SESSION['roomid']));
    $zhuangdui_max = (int) get_query_val($table, 'zhuangdui_max', array('roomid' => $_SESSION['roomid']));
    $zhuangdui_min = (int) get_query_val($table, 'zhuangdui_min', array('roomid' => $_SESSION['roomid']));
    $xiandui_max = (int) get_query_val($table, 'xiandui_max', array('roomid' => $_SESSION['roomid']));
    $xiandui_min = (int) get_query_val($table, 'xiandui_min', array('roomid' => $_SESSION['roomid']));
    $anydui_max = (int) get_query_val($table, 'anydui_max', array('roomid' => $_SESSION['roomid']));
    $anydui_min = (int) get_query_val($table, 'anydui_min', array('roomid' => $_SESSION['roomid']));
    switch ($zym_6) {
        case '???':
            $max = $zhuang_max = (int) get_query_val($table, 'zhuang_max', array('roomid' => $_SESSION['roomid']));
            $min = $zhuang_min = (int) get_query_val($table, 'zhuang_min', array('roomid' => $_SESSION['roomid']));
            break;
        case '???':
            $max = $xian_max = (int) get_query_val($table, 'xian_max', array('roomid' => $_SESSION['roomid']));
            $min = $xian_min = (int) get_query_val($table, 'xian_min', array('roomid' => $_SESSION['roomid']));
            break;
        case '???':
            $max = $he_max = (int) get_query_val($table, 'he_max', array('roomid' => $_SESSION['roomid']));
            $min = $he_min = (int) get_query_val($table, 'he_min', array('roomid' => $_SESSION['roomid']));
            break;
        case '??????':
            $max = $zhuangdui_max = (int) get_query_val($table, 'zhuangdui_max', array('roomid' => $_SESSION['roomid']));
            $min = $zhuangdui_min = (int) get_query_val($table, 'zhuangdui_min', array('roomid' => $_SESSION['roomid']));
            break;
        case '??????':
            $max = $xiandui_max = (int) get_query_val($table, 'xiandui_max', array('roomid' => $_SESSION['roomid']));
            $min = $xiandui_min = (int) get_query_val($table, 'xiandui_min', array('roomid' => $_SESSION['roomid']));
            break;
        case '?????????':
            $max = $anydui_max = (int) get_query_val($table, 'anydui_max', array('roomid' => $_SESSION['roomid']));
            $min = $anydui_min = (int) get_query_val($table, 'anydui_min', array('roomid' => $_SESSION['roomid']));
            break;
        default:
            break;
    }
    if ($zym_6 == '???' || $zym_6 == '???' || $zym_6 == '???' || $zym_6 == '??????' || $zym_6 == '??????' || $zym_6 == '?????????') {
        if ($zym_5 > $max) {
            $chaozhu = true;
            $zym_2 = $zym_6 . '/' . $zym_5;
            continue;
        } else if ($min > $zym_5) {
            $zym_2 = $zym_6 . '/' . $zym_5;
            continue;
        }
        $touzhu = true;
        ??????_??????($userid, $zym_5);
        insert_query("fn_bjlorder", array("term" => $addQihao, 'userid' => $userid, 'username' => $_SESSION['username'], 'headimg' => $_SESSION['headimg'], 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
        continue;
    }
}
if ($zym_2 != "") {
    if ($chaozhu) {
        ???????????????("@$nickname,????????????????????????<br>???????????????????????????<br>?????????{$zhuang_min},??????{$zhuang_max}<br>?????????{$xian_min },??????{$xian_max }<br>?????????{$he_min},??????{$he_max}<br>????????????{$zhuangdui_min},??????{$zhuangdui_max}<br>????????????{$xiandui_min},??????{$xiandui_max}<br>???????????????{$anydui_min},??????{$anydui_max}<br>");
        return true;
    } elseif ($jinzhi) {
        $nr = "";
        if ($zym_4 == 'true') {
            $nr .= '[???????????????]';
        }
        if ($zym_1 == 'true') {
            $nr .= '[??????????????????]';
        }
        if ($zym_3 == 'true') {
            $nr .= '[??????????????????]';
        }
        if ($zym_14_?????? != 0) {
            $nr2 = '<br>?????????????????????' . $zym_14_?????? . '???,????????????????????????!';
        } else {
            $nr2 = "";
        }
        ???????????????("@{$nickname},?????????{$zym_2}??????<br>??????$nr" . $nr2);
        return true;
    } else {
        ???????????????("@{$nickname},?????????{$zym_2}????????????????????????" . ??????????????????($userid));
        return true;
    }
} elseif (get_query_val("fn_setting", "setting_tishi", array("roomid" => $_SESSION['roomid'])) == 'open' && $touzhu == true) {
    ???????????????("@{$nickname},???????????????????????????????????????????????????");
    return true;
} elseif ($touzhu) {
    return true;
}
    return false;
}
function addSSCBet($userid, $nickname, $headimg, $content, $addQihao, $fengpan){
if($fengpan){
??????????????? ("@" . $nickname . " ,[$addQihao]???????????????????????????????????????");
return false;
}
$content = str_replace(".", "/", $content);
$content = str_replace(",", "/", $content);
$content = str_replace("???", "/", $content);
$content = preg_replace("/[???????????????-]/u", "/", $content);
$content = str_replace("??????", "???", $content);
$content = str_replace("???", "???", $content);
$content = str_replace("??????", "???", $content);
$content = str_replace("??????", "???", $content);
$content = str_replace("??????", "???", $content);
$content = str_replace("???/", "???", $content);
$content = preg_replace("/(???|???)\//u", "$1", $content);
$content = preg_replace("/(???|???)\//u", "$1", $content);
$content = preg_replace("/(???|???)\//u", "$1", $content);
$content = preg_replace("/(???|???)\//u", "$1", $content);
$content = preg_replace("/(???|???)\//u", "$1", $content);
$content = preg_replace("/(???|???)/u", "1/", $content);
$content = preg_replace("/(???|???)/u", "2/", $content);
$content = preg_replace("/(???|???)/u", "3/", $content);
$content = preg_replace("/(???|???)/u", "4/", $content);
$content = preg_replace("/(???|???)/u", "5/", $content);
$content = preg_replace("/(???|???|???)\//u", "$1", $content);
$content = preg_replace("/\/(???|???|???)/u", "$1", $content);
$content = preg_replace("/(???|???|???)/u", "???/$1/", $content);
$content = preg_replace("/[???Qq]/u", "??????/", $content);
$content = preg_replace("/[???Zz]/u", "??????/", $content);
$content = preg_replace("/[???Hh]/u", "??????/", $content);
$content = preg_replace("/[???]/u", "???/", $content);
$content = preg_replace("/(???|???|???|???|??????|??????|??????|??????|??????|??????|??????|??????)\//u", "$1", $content);
$content = preg_replace("/\/(???|???|???|???|??????|??????|??????|??????|??????)/u", "$1", $content);
$content = preg_replace("/(???|???|???|???|??????|??????|??????|??????|??????)/u", "/$1/", $content);
switch($_COOKIE['game']){ 
case 'cqssc': $table = 'fn_lottery3';
$ordertable = 'fn_sscorder';
break;
case 'azxy5': $table = 'fn_lottery15';
$ordertable = 'fn_azxy5order';
break;
case "jsssc": $table = 'fn_lottery8';
$ordertable = 'fn_jssscorder';
break;
}
$dx_min = get_query_val($table, 'dx_min', array('roomid' => $_SESSION['roomid']));
$dx_max = get_query_val($table, 'dx_max', array('roomid' => $_SESSION['roomid']));
$ds_min = get_query_val($table, 'ds_min', array('roomid' => $_SESSION['roomid']));
$ds_max = get_query_val($table, 'ds_max', array('roomid' => $_SESSION['roomid']));
$lh_min = get_query_val($table, 'lh_min', array('roomid' => $_SESSION['roomid']));
$lh_max = get_query_val($table, 'lh_max', array('roomid' => $_SESSION['roomid']));
$tm_min = get_query_val($table, 'tm_min', array('roomid' => $_SESSION['roomid']));
$tm_max = get_query_val($table, 'tm_max', array('roomid' => $_SESSION['roomid']));
$zh_min = get_query_val($table, 'zh_min', array('roomid' => $_SESSION['roomid']));
$zh_max = get_query_val($table, 'zh_max', array('roomid' => $_SESSION['roomid']));
$bz_min = get_query_val($table, 'bz_min', array('roomid' => $_SESSION['roomid']));
$bz_max = get_query_val($table, 'bz_max', array('roomid' => $_SESSION['roomid']));
$dz_min = get_query_val($table, 'dz_min', array('roomid' => $_SESSION['roomid']));
$dz_max = get_query_val($table, 'dz_max', array('roomid' => $_SESSION['roomid']));
$sz_min = get_query_val($table, 'sz_min', array('roomid' => $_SESSION['roomid']));
$sz_max = get_query_val($table, 'sz_max', array('roomid' => $_SESSION['roomid']));
$bs_min = get_query_val($table, 'bs_min', array('roomid' => $_SESSION['roomid']));
$bs_max = get_query_val($table, 'bs_max', array('roomid' => $_SESSION['roomid']));
$zl_min = get_query_val($table, 'zl_min', array('roomid' => $_SESSION['roomid']));
$zl_max = get_query_val($table, 'zl_max', array('roomid' => $_SESSION['roomid']));
$zym_8 = get_query_val('fn_user', 'jia', array('userid' => $userid, 'roomid' => $_SESSION['roomid'])) == 'true' ? 'true' : 'false';
$touzhu = false;
$A = explode(" ", $content);
$zym_2 = "";
//var_dump($A);exit;
foreach($A as $ai){
	$ai = str_replace(" ", "", $ai);
	if(empty($ai))continue;
	if(substr($ai, 0, 1) == '/')$ai = '1' . $ai;
	$b = explode("/", $ai);
	if(count($b) == 2){
		if(preg_match("/(???|???|???|???)/u", $ai)){
			$ai = '1/' . $ai;
		}else{
			$ai = '???/' . $ai;
		}
		$b = explode("/", $ai);
	}
	if(count($b) != 3)continue;
	if($b[0] == "" || $b[1] == "" || (int)$b[2] < 1)continue;
	$zym_9 = ?????????????????? ($userid);
	$zym_10 = $b[0];
	$zym_6 = $b[1];
	$zym_5 = (int)$b[2];
	if($zym_10 == '???'){
		if($zym_6 == '???' || $zym_6 == '???'){
			if((int)$zym_9 < (int)$zym_5){
				$zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				continue;
			}elseif($zym_5 < $zh_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $zh_max){
				$zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
			$touzhu = true;
			continue;
		}elseif($zym_6 == '???' || $zym_6 == '???'){
			if((int)$zym_9 < (int)$zym_5){
				$zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				continue;
			}elseif($zym_5 < $zh_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $zh_max){
				$zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
			$touzhu = true;
			continue;
		}elseif($zym_6 == '???' || $zym_6 == '???' || $zym_6 == '???'){
			if((int)$zym_9 < (int)$zym_5){
				$zym_2 .= $zym_6 . "/" . $zym_5 . " ";
				continue;
			}elseif($zym_5 < $lh_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $lh_max){
				$zym_2 .= $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
			$touzhu = true;
			continue;
		}
		continue;
	}
	if($zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???" || $zym_6 == "???"){
		$zym_10_?????? = ??????_???????????? ($zym_10);
		foreach ($zym_10_?????? as $ii){
			if((int)$ii > 5){
				???????????????("???????????????5???????????????????????????????????????????????????");
				break;
			}
			if($zym_9 < count($zym_10_??????) * (int)$zym_5){
				$zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				break;
			}elseif($zym_5 < $dx_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "???"){
				$zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}elseif($zym_5 < $dx_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "???"){
				$zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}elseif($zym_5 < $ds_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "???"){
				$zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}elseif($zym_5 < $ds_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "???"){
				$zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			$touzhu = true;
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
			continue;
		}
		continue;
	}
	if($zym_10 == '??????' || $zym_10 == '??????' || $zym_10 == '??????' || preg_match("/(??????|??????|??????)/u", $zym_10)){
		$arr = ???????????????($zym_10);
		foreach($arr as $i){
			if($zym_9 < (int)$zym_5){
				$zym_2 = $zym_2 . $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				break;
			}elseif($zym_5 < $bz_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $bz_max && $zym_6 == "??????"){
				$zym_2 .= $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				break;
			}elseif($zym_5 < $dz_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $dz_max && $zym_6 == "??????"){
				$zym_2 .= $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				break;
			}elseif($zym_5 < $sz_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $sz_max && $zym_6 == "??????"){
				$zym_2 .= $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				break;
			}elseif($zym_5 < $bs_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $bs_max && $zym_6 == "??????"){
				$zym_2 .= $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				break;
			}elseif($zym_5 < $zl_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $zl_max && $zym_6 == "??????"){
				$zym_2 .= $i . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				break;
			}
			$touzhu = true;
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $i, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
			continue;
		}
		continue;
	}
	if($zym_10 == "???"){
		$zym_6_?????? = ??????_???????????? ($zym_6);
		foreach($zym_6_?????? as $ii){
			if(!is_numeric($ii)){
				continue;
			}elseif((int)$zym_9 < (int)$zym_5 * 5 * count($zym_6_??????)){
				$zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
				continue;
			}elseif($zym_5 < $tm_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $tm_max){
				$zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			??????_??????($userid, $zym_5 * 5);
			db_query("INSERT INTO {$ordertable}(userid, username, headimg, term, mingci, content, `money`, addtime, `status`, jia, roomid) VALUES('$userid', '$nickname', '$headimg', '$addQihao', '1', '{$ii}', '{$zym_5}', now(), '?????????', '{$zym_8}', '{$_SESSION['roomid']}'), ('$userid', '$nickname', '$headimg', '$addQihao', '2', '{$ii}', '{$zym_5}', now(), '?????????', '{$zym_8}', '{$_SESSION['roomid']}'), ('$userid', '$nickname', '$headimg', '$addQihao', '3', '{$ii}', '{$zym_5}', now(), '?????????', '{$zym_8}', '{$_SESSION['roomid']}'), ('$userid', '$nickname', '$headimg', '$addQihao', '4', '{$ii}', '{$zym_5}', now(), '?????????', '{$zym_8}', '{$_SESSION['roomid']}'), ('$userid', '$nickname', '$headimg', '$addQihao', '5', '{$ii}', '{$zym_5}', now(), '?????????', '{$zym_8}', '{$_SESSION['roomid']}')");
			$touzhu = true;
			continue;
		}
		continue;
	}
	$zym_6_?????? = ??????_???????????? ($zym_6);
	$zym_10_?????? = ??????_???????????? ($zym_10);
	foreach ($zym_10_?????? as $ii){
		if($zym_9 < count($zym_10_??????) * count($zym_6_??????) * (int)$zym_5){
			$zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
			break;
		}
		if((int)$ii > 5){
			???????????????("???????????????5???????????????????????????????????????????????????");
			break;
		}
		foreach ($zym_6_?????? as $iii){
			if(!is_numeric($iii)){
				continue;
			}
			if($zym_5 < $tm_min || sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $tm_max){
				$zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
				$chaozhu = true;
				continue;
			}
			$touzhu = true;
			??????_??????($userid, $zym_5);
			insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $iii, 'money' => $zym_5, 'addtime' => 'now()', 'roomid' => $_SESSION['roomid'], 'status' => '?????????', 'jia' => $zym_8));
		}
	}
	continue;
}
//var_dump($touzhu);exit; 
if($zym_2 != ""){
	if($chaozhu){
		???????????????("@{$nickname},?????????{$zym_2}??????<br>??????????????????????????????<br>????????????????????????:<br>????????????{$dx_min}???,??????{$dx_max}<br>????????????{$ds_min}???,??????{$ds_max}<br>????????????{$lh_min}???,??????{$lh_max}<br>????????????{$tm_min}???,??????{$tm_max}<br>????????????{$bz_min}???,??????{$bz_max}<br>????????????{$dz_min}???,??????{$dz_max}<br>????????????{$sz_min}???,??????{$sz_max}<br>????????????{$bs_min}???,??????{$bs_max}<br>????????????{$zl_min}???,??????{$zl_max}<br>????????????????????????{$zh_min}???,??????{$zh_max}");
		return true;
	}else{
		???????????????("@{$nickname},?????????{$zym_2}????????????????????????" . ??????????????????($userid));
		return true;
	}
}elseif(get_query_val("fn_setting", "setting_tishi", array("roomid" => $_SESSION['roomid'])) == 'open' && $touzhu == true){
	???????????????("@$nickname,???????????????????????????????????????????????????");
	return true;
}elseif($touzhu){
	return true;
}
return false;
}
?>