<?php
 function ESC_getcode($url){
    $name = $_SESSION['agent_room'] . time() . '.png';
    $code = dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . '/flyorder/' . $name;
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/yzm.php');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $result = curl_exec($ch);
    curl_close($ch);
    list($header, $body) = explode("\r\n\r\n", $result);
    preg_match("/^Set-Cookie: (.*?);/m", $header, $cookies);
    $cookies = $cookies[1];
    $fp = fopen($code, "w");
    fwrite($fp, $body);
    fclose($fp);
    return array("code" => "flyorder/" . $name, 'cookie' => $cookies);
}
function ESC_Login($url, $user, $pass, $code, $cookies){
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/index.php/webcenter/Login/login_do');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $post = array('action' => 'login', 'username' => $user, 'password' => $pass, 'vlcodes' => $code);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $cookies = str_replace('%3D', '=', $cookies);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $result = curl_exec($ch);
    curl_close($ch);
    list($header, $body) = explode("\r\n\r\n", $result);
    preg_match("/^Set-Cookie: (.*?);/m", $header, $cookies);
    switch($result){
    case '1': break;
    case '4': echo "<script>alert('?????????????????????!')</script>";
        break;
    case '5': echo "<script>alert('???????????????!!')</script>";
        break;
    }
    update_query("fn_setting", array("flyorder_session" => $cookies[1]), array('roomid' => $_SESSION['agent_room']));
    return ESC_getmoney($url, $cookies[1]);
}
function ESC_getmoney($url, $cookies){
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/index.php/lottery/lottery/get_json');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array('lotteryId' => 'bj_10', 'numberPostion' => 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, 1);
    return array("money" => $json['Obj']['Balance'], 'weijie' => $json['Obj']['NotCountSum']);
}
function ESC_GoBet($url, $user, $cookies, $roomid){
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/index.php/lottery/lottery/bet?');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $post = ESC_getBet($roomid, $content, $term);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, 1);
    var_dump($json);
    if($json['result'] == 1){
        $true = '??????';
    }else{
        $true = $json['msg'];
    }
    if($content == "")return;
    $money = ESC_getmoney($url, $cookies);
    insert_query("fn_flyorder", array("game" => "????????????", "term" => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
}
function ESC_getBet($roomid, & $contents, & $term){
    $yi = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $er = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $san = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $si = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $wu = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $liu = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $qi = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $ba = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $jiu = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $shi = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '0' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $he = array('3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0, '13' => 0, '14' => 0, '15' => 0, '16' => 0, '17' => 0, '18' => 0, '19' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $term = get_query_val('fn_open', 'next_term', "type = 1 order by term desc limit 1");
    $duichong = get_query_val('fn_setting', 'flyorder_duichong', array('roomid' => $roomid));
    select_query("fn_order", '*', "roomid = $roomid and `status` = '?????????' and term = '$term' and jia = 'false'");
    while($con = db_fetch_array()){
        if($con['mingci'] == '1'){
            switch($con['content']){
            case '1': $yi['1'] += $con['money'];
                break;
            case '2': $yi['2'] += $con['money'];
                break;
            case '3': $yi['3'] += $con['money'];
                break;
            case '4': $yi['4'] += $con['money'];
                break;
            case '5': $yi['5'] += $con['money'];
                break;
            case '6': $yi['6'] += $con['money'];
                break;
            case '7': $yi['7'] += $con['money'];
                break;
            case '8': $yi['8'] += $con['money'];
                break;
            case '9': $yi['9'] += $con['money'];
                break;
            case '0': $yi['0'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "???": $yi['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $yi['1'] += (int)$money;
                $yi['3'] += (int)$money;
                $yi['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $yi['7'] += (int)$money;
                $yi['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $yi['2'] += (int)$money;
                $yi['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $yi['6'] += (int)$money;
                $yi['8'] += (int)$money;
                $yi['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '2'){
            switch($con['content']){
            case '1': $er['1'] += $con['money'];
                break;
            case '2': $er['2'] += $con['money'];
                break;
            case '3': $er['3'] += $con['money'];
                break;
            case '4': $er['4'] += $con['money'];
                break;
            case '5': $er['5'] += $con['money'];
                break;
            case '6': $er['6'] += $con['money'];
                break;
            case '7': $er['7'] += $con['money'];
                break;
            case '8': $er['8'] += $con['money'];
                break;
            case '9': $er['9'] += $con['money'];
                break;
            case '0': $er['0'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "???": $er['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $er['1'] += (int)$money;
                $er['3'] += (int)$money;
                $er['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $er['7'] += (int)$money;
                $er['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $er['2'] += (int)$money;
                $er['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $er['6'] += (int)$money;
                $er['8'] += (int)$money;
                $er['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '3'){
            switch($con['content']){
            case '1': $san['1'] += $con['money'];
                break;
            case '2': $san['2'] += $con['money'];
                break;
            case '3': $san['3'] += $con['money'];
                break;
            case '4': $san['4'] += $con['money'];
                break;
            case '5': $san['5'] += $con['money'];
                break;
            case '6': $san['6'] += $con['money'];
                break;
            case '7': $san['7'] += $con['money'];
                break;
            case '8': $san['8'] += $con['money'];
                break;
            case '9': $san['9'] += $con['money'];
                break;
            case '0': $san['0'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "???": $san['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $san['1'] += (int)$money;
                $san['3'] += (int)$money;
                $san['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $san['7'] += (int)$money;
                $san['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $san['2'] += (int)$money;
                $san['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $san['6'] += (int)$money;
                $san['8'] += (int)$money;
                $san['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '4'){
            switch($con['content']){
            case '1': $si['1'] += $con['money'];
                break;
            case '2': $si['2'] += $con['money'];
                break;
            case '3': $si['3'] += $con['money'];
                break;
            case '4': $si['4'] += $con['money'];
                break;
            case '5': $si['5'] += $con['money'];
                break;
            case '6': $si['6'] += $con['money'];
                break;
            case '7': $si['7'] += $con['money'];
                break;
            case '8': $si['8'] += $con['money'];
                break;
            case '9': $si['9'] += $con['money'];
                break;
            case '0': $si['0'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "???": $si['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $si['1'] += (int)$money;
                $si['3'] += (int)$money;
                $si['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $si['7'] += (int)$money;
                $si['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $si['2'] += (int)$money;
                $si['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $si['6'] += (int)$money;
                $si['8'] += (int)$money;
                $si['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '5'){
            switch($con['content']){
            case '1': $wu['1'] += $con['money'];
                break;
            case '2': $wu['2'] += $con['money'];
                break;
            case '3': $wu['3'] += $con['money'];
                break;
            case '4': $wu['4'] += $con['money'];
                break;
            case '5': $wu['5'] += $con['money'];
                break;
            case '6': $wu['6'] += $con['money'];
                break;
            case '7': $wu['7'] += $con['money'];
                break;
            case '8': $wu['8'] += $con['money'];
                break;
            case '9': $wu['9'] += $con['money'];
                break;
            case '0': $wu['0'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "???": $wu['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $wu['1'] += (int)$money;
                $wu['3'] += (int)$money;
                $wu['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $wu['7'] += (int)$money;
                $wu['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $wu['2'] += (int)$money;
                $wu['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $wu['6'] += (int)$money;
                $wu['8'] += (int)$money;
                $wu['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '6'){
            switch($con['content']){
            case '1': $liu['1'] += $con['money'];
                break;
            case '2': $liu['2'] += $con['money'];
                break;
            case '3': $liu['3'] += $con['money'];
                break;
            case '4': $liu['4'] += $con['money'];
                break;
            case '5': $liu['5'] += $con['money'];
                break;
            case '6': $liu['6'] += $con['money'];
                break;
            case '7': $liu['7'] += $con['money'];
                break;
            case '8': $liu['8'] += $con['money'];
                break;
            case '9': $liu['9'] += $con['money'];
                break;
            case '0': $liu['0'] += $con['money'];
                break;
            case "???": $liu['???'] += $con['money'];
                break;
            case "???": $liu['???'] += $con['money'];
                break;
            case "???": $liu['???'] += $con['money'];
                break;
            case "???": $liu['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $liu['1'] += (int)$money;
                $liu['3'] += (int)$money;
                $liu['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $liu['7'] += (int)$money;
                $liu['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $liu['2'] += (int)$money;
                $liu['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $liu['6'] += (int)$money;
                $liu['8'] += (int)$money;
                $liu['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '7'){
            switch($con['content']){
            case '1': $qi['1'] += $con['money'];
                break;
            case '2': $qi['2'] += $con['money'];
                break;
            case '3': $qi['3'] += $con['money'];
                break;
            case '4': $qi['4'] += $con['money'];
                break;
            case '5': $qi['5'] += $con['money'];
                break;
            case '6': $qi['6'] += $con['money'];
                break;
            case '7': $qi['7'] += $con['money'];
                break;
            case '8': $qi['8'] += $con['money'];
                break;
            case '9': $qi['9'] += $con['money'];
                break;
            case '0': $qi['0'] += $con['money'];
                break;
            case "???": $qi['???'] += $con['money'];
                break;
            case "???": $qi['???'] += $con['money'];
                break;
            case "???": $qi['???'] += $con['money'];
                break;
            case "???": $qi['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $qi['1'] += (int)$money;
                $qi['3'] += (int)$money;
                $qi['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $qi['7'] += (int)$money;
                $qi['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $qi['2'] += (int)$money;
                $qi['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $qi['6'] += (int)$money;
                $qi['8'] += (int)$money;
                $qi['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '8'){
            switch($con['content']){
            case '1': $ba['1'] += $con['money'];
                break;
            case '2': $ba['2'] += $con['money'];
                break;
            case '3': $ba['3'] += $con['money'];
                break;
            case '4': $ba['4'] += $con['money'];
                break;
            case '5': $ba['5'] += $con['money'];
                break;
            case '6': $ba['6'] += $con['money'];
                break;
            case '7': $ba['7'] += $con['money'];
                break;
            case '8': $ba['8'] += $con['money'];
                break;
            case '9': $ba['9'] += $con['money'];
                break;
            case '0': $ba['0'] += $con['money'];
                break;
            case "???": $ba['???'] += $con['money'];
                break;
            case "???": $ba['???'] += $con['money'];
                break;
            case "???": $ba['???'] += $con['money'];
                break;
            case "???": $ba['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $ba['1'] += (int)$money;
                $ba['3'] += (int)$money;
                $ba['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $ba['7'] += (int)$money;
                $ba['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $ba['2'] += (int)$money;
                $ba['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $ba['6'] += (int)$money;
                $ba['8'] += (int)$money;
                $ba['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '9'){
            switch($con['content']){
            case '1': $jiu['1'] += $con['money'];
                break;
            case '2': $jiu['2'] += $con['money'];
                break;
            case '3': $jiu['3'] += $con['money'];
                break;
            case '4': $jiu['4'] += $con['money'];
                break;
            case '5': $jiu['5'] += $con['money'];
                break;
            case '6': $jiu['6'] += $con['money'];
                break;
            case '7': $jiu['7'] += $con['money'];
                break;
            case '8': $jiu['8'] += $con['money'];
                break;
            case '9': $jiu['9'] += $con['money'];
                break;
            case '0': $jiu['0'] += $con['money'];
                break;
            case "???": $jiu['???'] += $con['money'];
                break;
            case "???": $jiu['???'] += $con['money'];
                break;
            case "???": $jiu['???'] += $con['money'];
                break;
            case "???": $jiu['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $jiu['1'] += (int)$money;
                $jiu['3'] += (int)$money;
                $jiu['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $jiu['7'] += (int)$money;
                $jiu['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $jiu['2'] += (int)$money;
                $jiu['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $jiu['6'] += (int)$money;
                $jiu['8'] += (int)$money;
                $jiu['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '0'){
            switch($con['content']){
            case '1': $shi['1'] += $con['money'];
                break;
            case '2': $shi['2'] += $con['money'];
                break;
            case '3': $shi['3'] += $con['money'];
                break;
            case '4': $shi['4'] += $con['money'];
                break;
            case '5': $shi['5'] += $con['money'];
                break;
            case '6': $shi['6'] += $con['money'];
                break;
            case '7': $shi['7'] += $con['money'];
                break;
            case '8': $shi['8'] += $con['money'];
                break;
            case '9': $shi['9'] += $con['money'];
                break;
            case '0': $shi['0'] += $con['money'];
                break;
            case "???": $shi['???'] += $con['money'];
                break;
            case "???": $shi['???'] += $con['money'];
                break;
            case "???": $shi['???'] += $con['money'];
                break;
            case "???": $shi['???'] += $con['money'];
                break;
            case "??????": $money = $con['money'] / 3;
                $shi['1'] += (int)$money;
                $shi['3'] += (int)$money;
                $shi['5'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $shi['7'] += (int)$money;
                $shi['9'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 2;
                $shi['2'] += (int)$money;
                $shi['4'] += (int)$money;
                break;
            case "??????": $money = $con['money'] / 3;
                $shi['6'] += (int)$money;
                $shi['8'] += (int)$money;
                $shi['0'] += (int)$money;
                break;
            }
        }elseif($con['mingci'] == '???'){
            switch($con['content']){
            case '3': $he['3'] += $con['money'];
                break;
            case '4': $he['4'] += $con['money'];
                break;
            case '5': $he['5'] += $con['money'];
                break;
            case '6': $he['6'] += $con['money'];
                break;
            case '7': $he['7'] += $con['money'];
                break;
            case '8': $he['8'] += $con['money'];
                break;
            case '9': $he['9'] += $con['money'];
                break;
            case "10": $he['10'] += $con['money'];
                break;
            case "11": $he['11'] += $con['money'];
                break;
            case "12": $he['12'] += $con['money'];
                break;
            case "13": $he['13'] += $con['money'];
                break;
            case "14": $he['14'] += $con['money'];
                break;
            case "15": $he['15'] += $con['money'];
                break;
            case "16": $he['16'] += $con['money'];
                break;
            case "17": $he['17'] += $con['money'];
                break;
            case "18": $he['18'] += $con['money'];
                break;
            case "19": $he['19'] += $con['money'];
                break;
            case "???": $he['???'] += $con['money'];
                break;
            case "???": $he['???'] += $con['money'];
                break;
            case "???": $he['???'] += $con['money'];
                break;
            case "???": $he['???'] += $con['money'];
                break;
            }
        }
    }
    if($duichong == 'true'){
        if($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] == $yi['???']){
            $yi['???'] = 0;
            $yi['???'] = 0;
        }
        if($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] == $yi['???']){
            $yi['???'] = 0;
            $yi['???'] = 0;
        }
        if($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] == $er['???']){
            $er['???'] = 0;
            $er['???'] = 0;
        }
        if($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] == $er['???']){
            $er['???'] = 0;
            $er['???'] = 0;
        }
        if($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] == $san['???']){
            $san['???'] = 0;
            $san['???'] = 0;
        }
        if($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] == $san['???']){
            $san['???'] = 0;
            $san['???'] = 0;
        }
        if($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] == $si['???']){
            $si['???'] = 0;
            $si['???'] = 0;
        }
        if($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] == $si['???']){
            $si['???'] = 0;
            $si['???'] = 0;
        }
        if($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] == $wu['???']){
            $wu['???'] = 0;
            $wu['???'] = 0;
        }
        if($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] == $wu['???']){
            $wu['???'] = 0;
            $wu['???'] = 0;
        }
        if($liu['???'] > $liu['???']){
            $liu['???'] = $liu['???'] - $liu['???'];
            $liu['???'] = 0;
        }elseif($liu['???'] > $liu['???']){
            $liu['???'] = $liu['???'] - $liu['???'];
            $liu['???'] = 0;
        }elseif($liu['???'] == $liu['???']){
            $liu['???'] = 0;
            $liu['???'] = 0;
        }
        if($liu['???'] > $liu['???']){
            $liu['???'] = $liu['???'] - $liu['???'];
            $liu['???'] = 0;
        }elseif($liu['???'] > $liu['???']){
            $liu['???'] = $liu['???'] - $liu['???'];
            $liu['???'] = 0;
        }elseif($liu['???'] == $liu['???']){
            $liu['???'] = 0;
            $liu['???'] = 0;
        }
        if($qi['???'] > $qi['???']){
            $qi['???'] = $qi['???'] - $qi['???'];
            $qi['???'] = 0;
        }elseif($qi['???'] > $qi['???']){
            $qi['???'] = $qi['???'] - $qi['???'];
            $qi['???'] = 0;
        }elseif($qi['???'] == $qi['???']){
            $qi['???'] = 0;
            $qi['???'] = 0;
        }
        if($qi['???'] > $qi['???']){
            $qi['???'] = $qi['???'] - $qi['???'];
            $qi['???'] = 0;
        }elseif($qi['???'] > $qi['???']){
            $qi['???'] = $qi['???'] - $qi['???'];
            $qi['???'] = 0;
        }elseif($qi['???'] == $qi['???']){
            $qi['???'] = 0;
            $qi['???'] = 0;
        }
        if($ba['???'] > $ba['???']){
            $ba['???'] = $ba['???'] - $ba['???'];
            $ba['???'] = 0;
        }elseif($ba['???'] > $ba['???']){
            $ba['???'] = $ba['???'] - $ba['???'];
            $ba['???'] = 0;
        }elseif($ba['???'] == $ba['???']){
            $ba['???'] = 0;
            $ba['???'] = 0;
        }
        if($ba['???'] > $ba['???']){
            $ba['???'] = $ba['???'] - $ba['???'];
            $ba['???'] = 0;
        }elseif($ba['???'] > $ba['???']){
            $ba['???'] = $ba['???'] - $ba['???'];
            $ba['???'] = 0;
        }elseif($ba['???'] == $ba['???']){
            $ba['???'] = 0;
            $ba['???'] = 0;
        }
        if($jiu['???'] > $jiu['???']){
            $jiu['???'] = $jiu['???'] - $jiu['???'];
            $jiu['???'] = 0;
        }elseif($jiu['???'] > $jiu['???']){
            $jiu['???'] = $jiu['???'] - $jiu['???'];
            $jiu['???'] = 0;
        }elseif($jiu['???'] == $jiu['???']){
            $jiu['???'] = 0;
            $jiu['???'] = 0;
        }
        if($jiu['???'] > $jiu['???']){
            $jiu['???'] = $jiu['???'] - $jiu['???'];
            $jiu['???'] = 0;
        }elseif($jiu['???'] > $jiu['???']){
            $jiu['???'] = $jiu['???'] - $jiu['???'];
            $jiu['???'] = 0;
        }elseif($jiu['???'] == $jiu['???']){
            $jiu['???'] = 0;
            $jiu['???'] = 0;
        }
        if($shi['???'] > $shi['???']){
            $shi['???'] = $shi['???'] - $shi['???'];
            $shi['???'] = 0;
        }elseif($shi['???'] > $shi['???']){
            $shi['???'] = $shi['???'] - $shi['???'];
            $shi['???'] = 0;
        }elseif($shi['???'] == $shi['???']){
            $shi['???'] = 0;
            $shi['???'] = 0;
        }
        if($shi['???'] > $shi['???']){
            $shi['???'] = $shi['???'] - $shi['???'];
            $shi['???'] = 0;
        }elseif($shi['???'] > $shi['???']){
            $shi['???'] = $shi['???'] - $shi['???'];
            $shi['???'] = 0;
        }elseif($shi['???'] == $shi['???']){
            $shi['???'] = 0;
            $shi['???'] = 0;
        }
        if($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] > $yi['???']){
            $yi['???'] = $yi['???'] - $yi['???'];
            $yi['???'] = 0;
        }elseif($yi['???'] == $yi['???']){
            $yi['???'] = 0;
            $yi['???'] = 0;
        }
        if($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] > $er['???']){
            $er['???'] = $er['???'] - $er['???'];
            $er['???'] = 0;
        }elseif($er['???'] == $er['???']){
            $er['???'] = 0;
            $er['???'] = 0;
        }
        if($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] > $san['???']){
            $san['???'] = $san['???'] - $san['???'];
            $san['???'] = 0;
        }elseif($san['???'] == $san['???']){
            $san['???'] = 0;
            $san['???'] = 0;
        }
        if($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] > $si['???']){
            $si['???'] = $si['???'] - $si['???'];
            $si['???'] = 0;
        }elseif($si['???'] == $si['???']){
            $si['???'] = 0;
            $si['???'] = 0;
        }
        if($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] > $wu['???']){
            $wu['???'] = $wu['???'] - $wu['???'];
            $wu['???'] = 0;
        }elseif($wu['???'] == $wu['???']){
            $wu['???'] = 0;
            $wu['???'] = 0;
        }
    }
    $bets = array();
    foreach($yi as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 861, 'gname' => '??????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 862, 'gname' => '??????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 863, 'gname' => '??????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 864, 'gname' => '??????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 865, 'gname' => '??????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 866, 'gname' => '??????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 867, 'gname' => '??????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 868, 'gname' => '??????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 869, 'gname' => '??????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 870, 'gname' => '??????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 871, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 872, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 873, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 874, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 875, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 876, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($er as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 877, 'gname' => '??????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 878, 'gname' => '??????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 879, 'gname' => '??????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 880, 'gname' => '??????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 881, 'gname' => '??????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 882, 'gname' => '??????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 883, 'gname' => '??????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 884, 'gname' => '??????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 885, 'gname' => '??????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 886, 'gname' => '??????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 887, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 888, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 889, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 890, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 891, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 892, 'gname' => '??????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($san as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 893, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 894, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 895, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 896, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 897, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 898, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 899, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 900, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 901, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 902, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 903, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 904, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 905, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 906, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 907, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 908, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($si as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 909, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 910, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 911, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 912, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 913, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 914, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 915, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 916, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 917, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 918, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 919, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 920, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 921, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 922, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 923, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 924, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($wu as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 925, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 926, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 927, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 928, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 929, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 930, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 931, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 932, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 933, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 934, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 935, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 936, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 937, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 938, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 939, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 940, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($liu as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 941, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 942, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 943, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 944, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 945, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 946, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 947, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 948, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 949, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 950, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 951, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 952, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 953, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 954, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($qi as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 955, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 956, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 957, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 958, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 959, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 960, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 961, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 962, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 963, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 964, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 965, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 966, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 967, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 968, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($ba as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 969, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 970, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 971, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 972, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 973, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 974, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 975, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 976, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 977, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 978, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 979, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 980, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 981, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 982, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($jiu as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 983, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 984, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 985, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 986, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 987, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 988, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 989, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 990, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 991, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 992, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 993, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 994, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 995, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 996, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($shi as $key => $money){
        if($key == '1' && $money > 0){
            $bets[] = array('id' => 997, 'gname' => '?????????', 'BetContext' => '1', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $bets[] = array('id' => 998, 'gname' => '?????????', 'BetContext' => '2', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 999, 'gname' => '?????????', 'BetContext' => '3', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 1000, 'gname' => '?????????', 'BetContext' => '4', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 1001, 'gname' => '?????????', 'BetContext' => '5', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 1002, 'gname' => '?????????', 'BetContext' => '6', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 1003, 'gname' => '?????????', 'BetContext' => '7', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 1004, 'gname' => '?????????', 'BetContext' => '8', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 1005, 'gname' => '?????????', 'BetContext' => '9', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $bets[] = array('id' => 1006, 'gname' => '?????????', 'BetContext' => '10', 'Lines' => '9.95', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 1007, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 1008, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 1009, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 1010, 'gname' => '?????????', 'BetContext' => '???', 'Lines' => '1.995', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($he as $key => $money){
        if($key == '3' && $money > 0){
            $bets[] = array('id' => 4179, 'gname' => '???????????????', 'BetContext' => '3', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $bets[] = array('id' => 4180, 'gname' => '???????????????', 'BetContext' => '4', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $bets[] = array('id' => 4181, 'gname' => '???????????????', 'BetContext' => '5', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $bets[] = array('id' => 4182, 'gname' => '???????????????', 'BetContext' => '6', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $bets[] = array('id' => 4183, 'gname' => '???????????????', 'BetContext' => '7', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $bets[] = array('id' => 4184, 'gname' => '???????????????', 'BetContext' => '8', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $bets[] = array('id' => 4185, 'gname' => '???????????????', 'BetContext' => '9', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '10' && $money > 0){
            $bets[] = array('id' => 4186, 'gname' => '???????????????', 'BetContext' => '10', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '11' && $money > 0){
            $bets[] = array('id' => 4187, 'gname' => '???????????????', 'BetContext' => '11', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '12' && $money > 0){
            $bets[] = array('id' => 4188, 'gname' => '???????????????', 'BetContext' => '12', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '13' && $money > 0){
            $bets[] = array('id' => 4189, 'gname' => '???????????????', 'BetContext' => '13', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '14' && $money > 0){
            $bets[] = array('id' => 4190, 'gname' => '???????????????', 'BetContext' => '14', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '15' && $money > 0){
            $bets[] = array('id' => 4191, 'gname' => '???????????????', 'BetContext' => '15', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '16' && $money > 0){
            $bets[] = array('id' => 4192, 'gname' => '???????????????', 'BetContext' => '16', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '17' && $money > 0){
            $bets[] = array('id' => 4193, 'gname' => '???????????????', 'BetContext' => '17', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '18' && $money > 0){
            $bets[] = array('id' => 4194, 'gname' => '???????????????', 'BetContext' => '18', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '19' && $money > 0){
            $bets[] = array('id' => 4195, 'gname' => '???????????????', 'BetContext' => '19', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 4196, 'gname' => '???????????????', 'BetContext' => '???', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 4197, 'gname' => '???????????????', 'BetContext' => '???', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 4198, 'gname' => '???????????????', 'BetContext' => '???', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $bets[] = array('id' => 4199, 'gname' => '???????????????', 'BetContext' => '???', 'Lines' => '42.6', 'BetType' => 1, 'Money' => $money, 'IsTeMa' => false, 'IsForNumber' => false, 'mingxi_1' => 0);
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
    }
    $json = array('lotteryId' => "bj_10", 'betParameters' => $bets);
    return $json;
}
?>