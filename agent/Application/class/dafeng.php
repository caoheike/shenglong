<?php
 function y88_login($url, $user, $pass){
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/login/login_ok');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $post = array('loginName' => $user, 'loginPwd' => $pass);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $result = curl_exec($ch);
    curl_close($ch);
    list($header, $body) = explode("\r\n\r\n", $result);
    preg_match_all("/^Set-Cookie: (.*?);/m", $header, $cookies);
    if(count($cookies[1]) > 1){
        $cookies = $cookies[1][1];
    }else{
        $cookies = $cookies[1][0];
    }
    $json = json_decode($body, true);
    if($json['success'] != '1'){
        echo '<script>alert("' . $json['msg'] . '")</script>';
        return;
    }
    update_query("fn_setting", array("flyorder_session" => $cookies), array('roomid' => $_SESSION['agent_room']));
    return y88_getmoney($url, $cookies);
}
function y88_getmoney($url, $cookies){
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/main/leftinfo');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array('optype' => 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, 1);
    return array("money" => $json['money'], 'weijie' => '????????????????????????', 'err' => $result, 'cookies' => $cookies);
}
function y88_getskey($url, $cookies, $gamecode){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '/gamessc/getseckey');
    if($SSL){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('game_code' => $gamecode, 'type' => 2)));
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = prepareJSON($result);
    $json = json_decode($result, true);
    return $json['newkey'];
}
function y88_GoBet($url, $user, $cookies, $roomid, $game = 'pk10'){
    if($game == 'pk10'){
        $post = y88_getBet($roomid, $content, $term);
        $gametitle = '????????????';
        $gamecode = 51;
    }elseif($game == 'xyft'){
        $post = y88_getBetXYFT($roomid, $content, $term);
        $gametitle = '????????????';
        $gamecode = 159;
    }
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if(count($post['liangmian']) == 0 && count($post['danhao']) == 0 && count($post['hezhi']) == 0){
        return;
    }
    if(count($post['liangmian']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['liangmian']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet_saiche');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['liangmian']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true = '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true = '????????????;';
        }
    }
    if(count($post['danhao']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['danhao']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet_saiche');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['danhao']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true .= '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true .= '????????????;';
        }
    }
    if(count($post['hezhi']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['hezhi']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet_saiche');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['hezhi']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true .= '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true .= '????????????;';
        }
    }
    $money = y88_getmoney($url, $cookies);
    insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
}
function y88_GoBetSSC($url, $user, $cookies, $roomid){
    $post = y88_getBetSSC($roomid, $content, $term);
    $gametitle = '???????????????';
    $gamecode = 2;
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if(count($post['shuzi']) == 0 && count($post['liangmian']) == 0 && count($post['qzhsan']) == 0){
        return;
    }
    if(count($post['liangmian']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['liangmian']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['liangmian']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true = '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true = '????????????;';
        }
    }
    if(count($post['shuzi']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['shuzi']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['shuzi']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true .= '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true .= '????????????;';
        }
    }
    if(count($post['qzhsan']) > 0){
        $seckey = y88_getskey($url, $cookies, $gamecode);
        $post['qzhsan']['seckey'] = $seckey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/bill/postbet');
        if($SSL){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post['qzhsan']));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $result = curl_exec($ch);
        curl_close($ch);
        if(strpos($result, '?????????') === false){
            $money = y88_getmoney($url, $cookies);
            $true .= '????????????';
            insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
            exit;
        }else{
            $true .= '????????????;';
        }
    }
    $money = y88_getmoney($url, $cookies);
    insert_query("fn_flyorder", array("game" => $gametitle, 'term' => $term, 'content' => substr($content, 0, strlen($content)-1), 'pan' => $url, 'panuser' => $user, 'money' => $money['money'], 'time' => 'now()', 'status' => $true, 'roomid' => $roomid));
}
function y88_getBet($roomid, & $contents, & $term){
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
        }
    }
    if($duichong == 'true'){
        $arr = array($yi['1'], $yi['2'], $yi['3'], $yi['4'], $yi['5'], $yi['6'], $yi['7'], $yi['8'], $yi['9'], $yi['0']);
        sort($arr);
        $yi['1'] = $yi['1'] - $arr[0];
        $yi['2'] = $yi['2'] - $arr[0];
        $yi['3'] = $yi['3'] - $arr[0];
        $yi['4'] = $yi['4'] - $arr[0];
        $yi['5'] = $yi['5'] - $arr[0];
        $yi['6'] = $yi['6'] - $arr[0];
        $yi['7'] = $yi['7'] - $arr[0];
        $yi['8'] = $yi['8'] - $arr[0];
        $yi['9'] = $yi['9'] - $arr[0];
        $yi['0'] = $yi['0'] - $arr[0];
        $arr = array($er['1'], $er['2'], $er['3'], $er['4'], $er['5'], $er['6'], $er['7'], $er['8'], $er['9'], $er['0']);
        sort($arr);
        $er['1'] = $er['1'] - $arr[0];
        $er['2'] = $er['2'] - $arr[0];
        $er['3'] = $er['3'] - $arr[0];
        $er['4'] = $er['4'] - $arr[0];
        $er['5'] = $er['5'] - $arr[0];
        $er['6'] = $er['6'] - $arr[0];
        $er['7'] = $er['7'] - $arr[0];
        $er['8'] = $er['8'] - $arr[0];
        $er['9'] = $er['9'] - $arr[0];
        $er['0'] = $er['0'] - $arr[0];
        $arr = array($san['1'], $san['2'], $san['3'], $san['4'], $san['5'], $san['6'], $san['7'], $san['8'], $san['9'], $san['0']);
        sort($arr);
        $san['1'] = $san['1'] - $arr[0];
        $san['2'] = $san['2'] - $arr[0];
        $san['3'] = $san['3'] - $arr[0];
        $san['4'] = $san['4'] - $arr[0];
        $san['5'] = $san['5'] - $arr[0];
        $san['6'] = $san['6'] - $arr[0];
        $san['7'] = $san['7'] - $arr[0];
        $san['8'] = $san['8'] - $arr[0];
        $san['9'] = $san['9'] - $arr[0];
        $san['0'] = $san['0'] - $arr[0];
        $arr = array($si['1'], $si['2'], $si['3'], $si['4'], $si['5'], $si['6'], $si['7'], $si['8'], $si['9'], $si['0']);
        sort($arr);
        $si['1'] = $si['1'] - $arr[0];
        $si['2'] = $si['2'] - $arr[0];
        $si['3'] = $si['3'] - $arr[0];
        $si['4'] = $si['4'] - $arr[0];
        $si['5'] = $si['5'] - $arr[0];
        $si['6'] = $si['6'] - $arr[0];
        $si['7'] = $si['7'] - $arr[0];
        $si['8'] = $si['8'] - $arr[0];
        $si['9'] = $si['9'] - $arr[0];
        $si['0'] = $si['0'] - $arr[0];
        $arr = array($wu['1'], $wu['2'], $wu['3'], $wu['4'], $wu['5'], $wu['6'], $wu['7'], $wu['8'], $wu['9'], $wu['0']);
        sort($arr);
        $wu['1'] = $wu['1'] - $arr[0];
        $wu['2'] = $wu['2'] - $arr[0];
        $wu['3'] = $wu['3'] - $arr[0];
        $wu['4'] = $wu['4'] - $arr[0];
        $wu['5'] = $wu['5'] - $arr[0];
        $wu['6'] = $wu['6'] - $arr[0];
        $wu['7'] = $wu['7'] - $arr[0];
        $wu['8'] = $wu['8'] - $arr[0];
        $wu['9'] = $wu['9'] - $arr[0];
        $wu['0'] = $wu['0'] - $arr[0];
        $arr = array($liu['1'], $liu['2'], $liu['3'], $liu['4'], $liu['5'], $liu['6'], $liu['7'], $liu['8'], $liu['9'], $liu['0']);
        sort($arr);
        $liu['1'] = $liu['1'] - $arr[0];
        $liu['2'] = $liu['2'] - $arr[0];
        $liu['3'] = $liu['3'] - $arr[0];
        $liu['4'] = $liu['4'] - $arr[0];
        $liu['5'] = $liu['5'] - $arr[0];
        $liu['6'] = $liu['6'] - $arr[0];
        $liu['7'] = $liu['7'] - $arr[0];
        $liu['8'] = $liu['8'] - $arr[0];
        $liu['9'] = $liu['9'] - $arr[0];
        $liu['0'] = $liu['0'] - $arr[0];
        $arr = array($qi['1'], $qi['2'], $qi['3'], $qi['4'], $qi['5'], $qi['6'], $qi['7'], $qi['8'], $qi['9'], $qi['0']);
        sort($arr);
        $qi['1'] = $qi['1'] - $arr[0];
        $qi['2'] = $qi['2'] - $arr[0];
        $qi['3'] = $qi['3'] - $arr[0];
        $qi['4'] = $qi['4'] - $arr[0];
        $qi['5'] = $qi['5'] - $arr[0];
        $qi['6'] = $qi['6'] - $arr[0];
        $qi['7'] = $qi['7'] - $arr[0];
        $qi['8'] = $qi['8'] - $arr[0];
        $qi['9'] = $qi['9'] - $arr[0];
        $qi['0'] = $qi['0'] - $arr[0];
        $arr = array($ba['1'], $ba['2'], $ba['3'], $ba['4'], $ba['5'], $ba['6'], $ba['7'], $ba['8'], $ba['9'], $ba['0']);
        sort($arr);
        $ba['1'] = $ba['1'] - $arr[0];
        $ba['2'] = $ba['2'] - $arr[0];
        $ba['3'] = $ba['3'] - $arr[0];
        $ba['4'] = $ba['4'] - $arr[0];
        $ba['5'] = $ba['5'] - $arr[0];
        $ba['6'] = $ba['6'] - $arr[0];
        $ba['7'] = $ba['7'] - $arr[0];
        $ba['8'] = $ba['8'] - $arr[0];
        $ba['9'] = $ba['9'] - $arr[0];
        $ba['0'] = $ba['0'] - $arr[0];
        $arr = array($jiu['1'], $jiu['2'], $jiu['3'], $jiu['4'], $jiu['5'], $jiu['6'], $jiu['7'], $jiu['8'], $jiu['9'], $jiu['0']);
        sort($arr);
        $jiu['1'] = $jiu['1'] - $arr[0];
        $jiu['2'] = $jiu['2'] - $arr[0];
        $jiu['3'] = $jiu['3'] - $arr[0];
        $jiu['4'] = $jiu['4'] - $arr[0];
        $jiu['5'] = $jiu['5'] - $arr[0];
        $jiu['6'] = $jiu['6'] - $arr[0];
        $jiu['7'] = $jiu['7'] - $arr[0];
        $jiu['8'] = $jiu['8'] - $arr[0];
        $jiu['9'] = $jiu['9'] - $arr[0];
        $jiu['0'] = $jiu['0'] - $arr[0];
        $arr = array($shi['1'], $shi['2'], $shi['3'], $shi['4'], $shi['5'], $shi['6'], $shi['7'], $shi['8'], $shi['9'], $shi['0']);
        sort($arr);
        $shi['1'] = $shi['1'] - $arr[0];
        $shi['2'] = $shi['2'] - $arr[0];
        $shi['3'] = $shi['3'] - $arr[0];
        $shi['4'] = $shi['4'] - $arr[0];
        $shi['5'] = $shi['5'] - $arr[0];
        $shi['6'] = $shi['6'] - $arr[0];
        $shi['7'] = $shi['7'] - $arr[0];
        $shi['8'] = $shi['8'] - $arr[0];
        $shi['9'] = $shi['9'] - $arr[0];
        $shi['0'] = $shi['0'] - $arr[0];
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
    $danhao = array();
    $liangmian = array();
    $hezhi = array();
    foreach($yi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3001-1'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3001-2'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3001-3'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3001-4'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3001-5'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3001-6'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3001-7'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3001-8'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3001-9'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3001-10'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3011'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3012'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3013'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3014'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3015'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3016'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($er as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3002-1'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3002-2'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3002-3'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3002-4'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3002-5'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3002-6'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3002-7'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3002-8'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3002-9'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3002-10'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3011'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3012'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3013'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3014'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3015'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3016'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($san as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3003-1'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3003-2'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3003-3'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3003-4'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3003-5'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3003-6'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3003-7'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3003-8'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3003-9'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3003-10'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3011'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3012'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3013'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3014'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3015'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3016'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($si as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3004-1'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3004-2'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3004-3'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3004-4'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3004-5'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3004-6'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3004-7'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3004-8'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3004-9'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3004-10'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3011'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3012'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3013'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3014'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3015'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3016'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($wu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3005-1'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3005-2'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3005-3'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3005-4'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3005-5'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3005-6'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3005-7'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3005-8'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3005-9'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3005-10'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3011'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3012'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3013'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3014'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3015'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3016'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($liu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3006-1'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3006-2'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3006-3'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3006-4'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3006-5'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3006-6'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3006-7'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3006-8'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3006-9'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3006-10'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3011'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3012'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3013'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3014'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($qi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3007-1'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3007-2'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3007-3'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3007-4'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3007-5'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3007-6'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3007-7'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3007-8'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3007-9'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3007-10'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3011'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3012'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3013'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3014'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($ba as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3008-1'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3008-2'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3008-3'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3008-4'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3008-5'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3008-6'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3008-7'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3008-8'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3008-9'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3008-10'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3011'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3012'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3013'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3014'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($jiu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3009-1'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3009-2'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3009-3'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3009-4'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3009-5'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3009-6'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3009-7'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3009-8'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3009-9'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3009-10'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3011'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3012'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3013'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3014'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($shi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3010-1'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3010-2'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3010-3'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3010-4'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3010-5'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3010-6'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3010-7'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3010-8'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3010-9'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3010-10'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3011'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3012'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3013'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3014'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($he as $key => $money){
        if($key == '3' && $money > 0){
            $hezhi['ip_3021-3'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $hezhi['ip_3021-4'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $hezhi['ip_3021-5'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $hezhi['ip_3021-6'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $hezhi['ip_3021-7'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $hezhi['ip_3021-8'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $hezhi['ip_3021-9'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '10' && $money > 0){
            $hezhi['ip_3021-10'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '11' && $money > 0){
            $hezhi['ip_3021-11'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '12' && $money > 0){
            $hezhi['ip_3021-12'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '13' && $money > 0){
            $hezhi['ip_3021-13'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '14' && $money > 0){
            $hezhi['ip_3021-14'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '15' && $money > 0){
            $hezhi['ip_3021-15'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '16' && $money > 0){
            $hezhi['ip_3021-16'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '17' && $money > 0){
            $hezhi['ip_3021-17'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '18' && $money > 0){
            $hezhi['ip_3021-18'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '19' && $money > 0){
            $hezhi['ip_3021-19'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3017'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3018'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3019'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3020'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
    }
    if(count($danhao) > 0){
        $danhao['game_code'] = 51;
        $danhao['typecode'] = 2;
        $danhao['round'] = $term;
        $danhao['confirm'] = '??? ???';
    }
    if(count($liangmian) > 0){
        $liangmian['game_code'] = 51;
        $liangmian['typecode'] = 0;
        $liangmian['round'] = $term;
        $liangmian['confirm'] = '??? ???';
    }
    if(count($hezhi) > 0){
        $hezhi['game_code'] = 51;
        $hezhi['typecode'] = 1;
        $hezhi['round'] = $term;
        $hezhi['confirm'] = '??? ???';
    }
    $json = array('danhao' => $danhao, 'liangmian' => $liangmian, 'hezhi' => $hezhi);
    return $json;
}
function y88_getBetXYFT($roomid, & $contents, & $term){
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
    $term = get_query_val('fn_open', 'next_term', "type = 2 order by term desc limit 1");
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
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
            continue;
        }
    }
    if($duichong == 'true'){
        $arr = array($yi['1'], $yi['2'], $yi['3'], $yi['4'], $yi['5'], $yi['6'], $yi['7'], $yi['8'], $yi['9'], $yi['0']);
        sort($arr);
        $yi['1'] = $yi['1'] - $arr[0];
        $yi['2'] = $yi['2'] - $arr[0];
        $yi['3'] = $yi['3'] - $arr[0];
        $yi['4'] = $yi['4'] - $arr[0];
        $yi['5'] = $yi['5'] - $arr[0];
        $yi['6'] = $yi['6'] - $arr[0];
        $yi['7'] = $yi['7'] - $arr[0];
        $yi['8'] = $yi['8'] - $arr[0];
        $yi['9'] = $yi['9'] - $arr[0];
        $yi['0'] = $yi['0'] - $arr[0];
        $arr = array($er['1'], $er['2'], $er['3'], $er['4'], $er['5'], $er['6'], $er['7'], $er['8'], $er['9'], $er['0']);
        sort($arr);
        $er['1'] = $er['1'] - $arr[0];
        $er['2'] = $er['2'] - $arr[0];
        $er['3'] = $er['3'] - $arr[0];
        $er['4'] = $er['4'] - $arr[0];
        $er['5'] = $er['5'] - $arr[0];
        $er['6'] = $er['6'] - $arr[0];
        $er['7'] = $er['7'] - $arr[0];
        $er['8'] = $er['8'] - $arr[0];
        $er['9'] = $er['9'] - $arr[0];
        $er['0'] = $er['0'] - $arr[0];
        $arr = array($san['1'], $san['2'], $san['3'], $san['4'], $san['5'], $san['6'], $san['7'], $san['8'], $san['9'], $san['0']);
        sort($arr);
        $san['1'] = $san['1'] - $arr[0];
        $san['2'] = $san['2'] - $arr[0];
        $san['3'] = $san['3'] - $arr[0];
        $san['4'] = $san['4'] - $arr[0];
        $san['5'] = $san['5'] - $arr[0];
        $san['6'] = $san['6'] - $arr[0];
        $san['7'] = $san['7'] - $arr[0];
        $san['8'] = $san['8'] - $arr[0];
        $san['9'] = $san['9'] - $arr[0];
        $san['0'] = $san['0'] - $arr[0];
        $arr = array($si['1'], $si['2'], $si['3'], $si['4'], $si['5'], $si['6'], $si['7'], $si['8'], $si['9'], $si['0']);
        sort($arr);
        $si['1'] = $si['1'] - $arr[0];
        $si['2'] = $si['2'] - $arr[0];
        $si['3'] = $si['3'] - $arr[0];
        $si['4'] = $si['4'] - $arr[0];
        $si['5'] = $si['5'] - $arr[0];
        $si['6'] = $si['6'] - $arr[0];
        $si['7'] = $si['7'] - $arr[0];
        $si['8'] = $si['8'] - $arr[0];
        $si['9'] = $si['9'] - $arr[0];
        $si['0'] = $si['0'] - $arr[0];
        $arr = array($wu['1'], $wu['2'], $wu['3'], $wu['4'], $wu['5'], $wu['6'], $wu['7'], $wu['8'], $wu['9'], $wu['0']);
        sort($arr);
        $wu['1'] = $wu['1'] - $arr[0];
        $wu['2'] = $wu['2'] - $arr[0];
        $wu['3'] = $wu['3'] - $arr[0];
        $wu['4'] = $wu['4'] - $arr[0];
        $wu['5'] = $wu['5'] - $arr[0];
        $wu['6'] = $wu['6'] - $arr[0];
        $wu['7'] = $wu['7'] - $arr[0];
        $wu['8'] = $wu['8'] - $arr[0];
        $wu['9'] = $wu['9'] - $arr[0];
        $wu['0'] = $wu['0'] - $arr[0];
        $arr = array($liu['1'], $liu['2'], $liu['3'], $liu['4'], $liu['5'], $liu['6'], $liu['7'], $liu['8'], $liu['9'], $liu['0']);
        sort($arr);
        $liu['1'] = $liu['1'] - $arr[0];
        $liu['2'] = $liu['2'] - $arr[0];
        $liu['3'] = $liu['3'] - $arr[0];
        $liu['4'] = $liu['4'] - $arr[0];
        $liu['5'] = $liu['5'] - $arr[0];
        $liu['6'] = $liu['6'] - $arr[0];
        $liu['7'] = $liu['7'] - $arr[0];
        $liu['8'] = $liu['8'] - $arr[0];
        $liu['9'] = $liu['9'] - $arr[0];
        $liu['0'] = $liu['0'] - $arr[0];
        $arr = array($qi['1'], $qi['2'], $qi['3'], $qi['4'], $qi['5'], $qi['6'], $qi['7'], $qi['8'], $qi['9'], $qi['0']);
        sort($arr);
        $qi['1'] = $qi['1'] - $arr[0];
        $qi['2'] = $qi['2'] - $arr[0];
        $qi['3'] = $qi['3'] - $arr[0];
        $qi['4'] = $qi['4'] - $arr[0];
        $qi['5'] = $qi['5'] - $arr[0];
        $qi['6'] = $qi['6'] - $arr[0];
        $qi['7'] = $qi['7'] - $arr[0];
        $qi['8'] = $qi['8'] - $arr[0];
        $qi['9'] = $qi['9'] - $arr[0];
        $qi['0'] = $qi['0'] - $arr[0];
        $arr = array($ba['1'], $ba['2'], $ba['3'], $ba['4'], $ba['5'], $ba['6'], $ba['7'], $ba['8'], $ba['9'], $ba['0']);
        sort($arr);
        $ba['1'] = $ba['1'] - $arr[0];
        $ba['2'] = $ba['2'] - $arr[0];
        $ba['3'] = $ba['3'] - $arr[0];
        $ba['4'] = $ba['4'] - $arr[0];
        $ba['5'] = $ba['5'] - $arr[0];
        $ba['6'] = $ba['6'] - $arr[0];
        $ba['7'] = $ba['7'] - $arr[0];
        $ba['8'] = $ba['8'] - $arr[0];
        $ba['9'] = $ba['9'] - $arr[0];
        $ba['0'] = $ba['0'] - $arr[0];
        $arr = array($jiu['1'], $jiu['2'], $jiu['3'], $jiu['4'], $jiu['5'], $jiu['6'], $jiu['7'], $jiu['8'], $jiu['9'], $jiu['0']);
        sort($arr);
        $jiu['1'] = $jiu['1'] - $arr[0];
        $jiu['2'] = $jiu['2'] - $arr[0];
        $jiu['3'] = $jiu['3'] - $arr[0];
        $jiu['4'] = $jiu['4'] - $arr[0];
        $jiu['5'] = $jiu['5'] - $arr[0];
        $jiu['6'] = $jiu['6'] - $arr[0];
        $jiu['7'] = $jiu['7'] - $arr[0];
        $jiu['8'] = $jiu['8'] - $arr[0];
        $jiu['9'] = $jiu['9'] - $arr[0];
        $jiu['0'] = $jiu['0'] - $arr[0];
        $arr = array($shi['1'], $shi['2'], $shi['3'], $shi['4'], $shi['5'], $shi['6'], $shi['7'], $shi['8'], $shi['9'], $shi['0']);
        sort($arr);
        $shi['1'] = $shi['1'] - $arr[0];
        $shi['2'] = $shi['2'] - $arr[0];
        $shi['3'] = $shi['3'] - $arr[0];
        $shi['4'] = $shi['4'] - $arr[0];
        $shi['5'] = $shi['5'] - $arr[0];
        $shi['6'] = $shi['6'] - $arr[0];
        $shi['7'] = $shi['7'] - $arr[0];
        $shi['8'] = $shi['8'] - $arr[0];
        $shi['9'] = $shi['9'] - $arr[0];
        $shi['0'] = $shi['0'] - $arr[0];
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
    $danhao = array();
    $liangmian = array();
    $hezhi = array();
    foreach($yi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3001-1'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3001-2'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3001-3'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3001-4'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3001-5'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3001-6'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3001-7'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3001-8'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3001-9'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3001-10'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3011'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3012'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3013'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3014'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3015'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3001-3016'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($er as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3002-1'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3002-2'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3002-3'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3002-4'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3002-5'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3002-6'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3002-7'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3002-8'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3002-9'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3002-10'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3011'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3012'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3013'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3014'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3015'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3002-3016'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($san as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3003-1'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3003-2'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3003-3'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3003-4'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3003-5'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3003-6'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3003-7'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3003-8'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3003-9'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3003-10'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3011'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3012'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3013'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3014'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3015'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3003-3016'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($si as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3004-1'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3004-2'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3004-3'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3004-4'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3004-5'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3004-6'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3004-7'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3004-8'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3004-9'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3004-10'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3011'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3012'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3013'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3014'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3015'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3004-3016'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($wu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3005-1'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3005-2'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3005-3'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3005-4'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3005-5'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3005-6'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3005-7'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3005-8'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3005-9'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3005-10'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3011'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3012'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3013'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3014'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3015'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3005-3016'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($liu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3006-1'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3006-2'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3006-3'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3006-4'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3006-5'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3006-6'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3006-7'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3006-8'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3006-9'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3006-10'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3011'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3012'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3013'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3006-3014'] = $money;
            $contents .= '6/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($qi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3007-1'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3007-2'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3007-3'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3007-4'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3007-5'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3007-6'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3007-7'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3007-8'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3007-9'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3007-10'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3011'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3012'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3013'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3007-3014'] = $money;
            $contents .= '7/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($ba as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3008-1'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3008-2'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3008-3'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3008-4'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3008-5'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3008-6'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3008-7'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3008-8'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3008-9'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3008-10'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3011'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3012'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3013'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3008-3014'] = $money;
            $contents .= '8/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($jiu as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3009-1'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3009-2'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3009-3'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3009-4'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3009-5'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3009-6'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3009-7'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3009-8'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3009-9'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3009-10'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3011'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3012'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3013'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3009-3014'] = $money;
            $contents .= '9/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($shi as $key => $money){
        if($key == '1' && $money > 0){
            $danhao['ip_3010-1'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $danhao['ip_3010-2'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $danhao['ip_3010-3'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $danhao['ip_3010-4'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $danhao['ip_3010-5'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $danhao['ip_3010-6'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $danhao['ip_3010-7'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $danhao['ip_3010-8'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $danhao['ip_3010-9'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $danhao['ip_3010-10'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3011'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3012'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3013'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3010-3014'] = $money;
            $contents .= '10/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($he as $key => $money){
        if($key == '3' && $money > 0){
            $hezhi['ip_3021-3'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $hezhi['ip_3021-4'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $hezhi['ip_3021-5'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $hezhi['ip_3021-6'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $hezhi['ip_3021-7'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $hezhi['ip_3021-8'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $hezhi['ip_3021-9'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '10' && $money > 0){
            $hezhi['ip_3021-10'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '11' && $money > 0){
            $hezhi['ip_3021-11'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '12' && $money > 0){
            $hezhi['ip_3021-12'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '13' && $money > 0){
            $hezhi['ip_3021-13'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '14' && $money > 0){
            $hezhi['ip_3021-14'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '15' && $money > 0){
            $hezhi['ip_3021-15'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '16' && $money > 0){
            $hezhi['ip_3021-16'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '17' && $money > 0){
            $hezhi['ip_3021-17'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '18' && $money > 0){
            $hezhi['ip_3021-18'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '19' && $money > 0){
            $hezhi['ip_3021-19'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3017'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3018'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3019'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $hezhi['ip_3020'] = $money;
            $contents .= '???/' . $key . '/' . $money . ',';
            continue;
        }
    }
    if(count($danhao) > 0){
        $danhao['game_code'] = 159;
        $danhao['typecode'] = 2;
        $danhao['round'] = $term;
        $danhao['confirm'] = '??? ???';
    }
    if(count($liangmian) > 0){
        $liangmian['game_code'] = 159;
        $liangmian['typecode'] = 0;
        $liangmian['round'] = $term;
        $liangmian['confirm'] = '??? ???';
    }
    if(count($hezhi) > 0){
        $hezhi['game_code'] = 159;
        $hezhi['typecode'] = 1;
        $hezhi['round'] = $term;
        $hezhi['confirm'] = '??? ???';
    }
    $json = array('danhao' => $danhao, 'liangmian' => $liangmian, 'hezhi' => $hezhi);
    return $json;
}
function y88_getBetSSC($roomid, & $contents, & $term){
    $wan = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $qian = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $bai = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $shi = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $ge = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $zong = array('???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0, '???' => 0);
    $q3 = array('??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0);
    $z3 = array('??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0);
    $h3 = array('??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0, '??????' => 0);
    $term = get_query_val('fn_open', 'next_term', "type = '3' order by term desc limit 1");
    select_query("fn_sscorder", '*', "`roomid` = '{$roomid}' and `status` = '?????????' and `term` = '$term' and `jia` = 'false'");
    while($con = db_fetch_array()){
        if($con['mingci'] == '1'){
            switch($con['content']){
            case '0': $wan['0'] += $con['money'];
                break;
            case '1': $wan['1'] += $con['money'];
                break;
            case '2': $wan['2'] += $con['money'];
                break;
            case '3': $wan['3'] += $con['money'];
                break;
            case '4': $wan['4'] += $con['money'];
                break;
            case '5': $wan['5'] += $con['money'];
                break;
            case '6': $wan['6'] += $con['money'];
                break;
            case '7': $wan['7'] += $con['money'];
                break;
            case '8': $wan['8'] += $con['money'];
                break;
            case '9': $wan['9'] += $con['money'];
                break;
            case '0': $wan['0'] += $con['money'];
                break;
            case "???": $wan['???'] += $con['money'];
                break;
            case "???": $wan['???'] += $con['money'];
                break;
            case "???": $wan['???'] += $con['money'];
                break;
            case "???": $wan['???'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '2'){
            switch($con['content']){
            case '0': $qian['0'] += $con['money'];
                break;
            case '1': $qian['1'] += $con['money'];
                break;
            case '2': $qian['2'] += $con['money'];
                break;
            case '3': $qian['3'] += $con['money'];
                break;
            case '4': $qian['4'] += $con['money'];
                break;
            case '5': $qian['5'] += $con['money'];
                break;
            case '6': $qian['6'] += $con['money'];
                break;
            case '7': $qian['7'] += $con['money'];
                break;
            case '8': $qian['8'] += $con['money'];
                break;
            case '9': $qian['9'] += $con['money'];
                break;
            case '0': $qian['0'] += $con['money'];
                break;
            case "???": $qian['???'] += $con['money'];
                break;
            case "???": $qian['???'] += $con['money'];
                break;
            case "???": $qian['???'] += $con['money'];
                break;
            case "???": $qian['???'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '3'){
            switch($con['content']){
            case '0': $bai['0'] += $con['money'];
                break;
            case '1': $bai['1'] += $con['money'];
                break;
            case '2': $bai['2'] += $con['money'];
                break;
            case '3': $bai['3'] += $con['money'];
                break;
            case '4': $bai['4'] += $con['money'];
                break;
            case '5': $bai['5'] += $con['money'];
                break;
            case '6': $bai['6'] += $con['money'];
                break;
            case '7': $bai['7'] += $con['money'];
                break;
            case '8': $bai['8'] += $con['money'];
                break;
            case '9': $bai['9'] += $con['money'];
                break;
            case '0': $bai['0'] += $con['money'];
                break;
            case "???": $bai['???'] += $con['money'];
                break;
            case "???": $bai['???'] += $con['money'];
                break;
            case "???": $bai['???'] += $con['money'];
                break;
            case "???": $bai['???'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '4'){
            switch($con['content']){
            case '0': $shi['0'] += $con['money'];
                break;
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
            }
            continue;
        }elseif($con['mingci'] == '5'){
            switch($con['content']){
            case '0': $ge['0'] += $con['money'];
                break;
            case '1': $ge['1'] += $con['money'];
                break;
            case '2': $ge['2'] += $con['money'];
                break;
            case '3': $ge['3'] += $con['money'];
                break;
            case '4': $ge['4'] += $con['money'];
                break;
            case '5': $ge['5'] += $con['money'];
                break;
            case '6': $ge['6'] += $con['money'];
                break;
            case '7': $ge['7'] += $con['money'];
                break;
            case '8': $ge['8'] += $con['money'];
                break;
            case '9': $ge['9'] += $con['money'];
                break;
            case '0': $ge['0'] += $con['money'];
                break;
            case "???": $ge['???'] += $con['money'];
                break;
            case "???": $ge['???'] += $con['money'];
                break;
            case "???": $ge['???'] += $con['money'];
                break;
            case "???": $ge['???'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '???'){
            switch($con['content']){
            case '???': $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            case "???": $zong['???'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '??????'){
            switch($con['content']){
            case '??????': $q3['??????'] += $con['money'];
                break;
            case "??????": $q3['??????'] += $con['money'];
                break;
            case "??????": $q3['??????'] += $con['money'];
                break;
            case "??????": $q3['??????'] += $con['money'];
                break;
            case "??????": $q3['??????'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '??????'){
            switch($con['content']){
            case '??????': $z3['??????'] += $con['money'];
                break;
            case "??????": $z3['??????'] += $con['money'];
                break;
            case "??????": $z3['??????'] += $con['money'];
                break;
            case "??????": $z3['??????'] += $con['money'];
                break;
            case "??????": $z3['??????'] += $con['money'];
                break;
            }
            continue;
        }elseif($con['mingci'] == '??????'){
            switch($con['content']){
            case '??????': $h3['??????'] += $con['money'];
                break;
            case "??????": $h3['??????'] += $con['money'];
                break;
            case "??????": $h3['??????'] += $con['money'];
                break;
            case "??????": $h3['??????'] += $con['money'];
                break;
            case "??????": $h3['??????'] += $con['money'];
                break;
            }
            continue;
        }
    }
    if($duichong == 'true'){
        $arr = array($wan['1'], $wan['2'], $wan['3'], $wan['4'], $wan['5'], $wan['6'], $wan['7'], $wan['8'], $wan['9'], $wan['0']);
        sort($arr);
        $wan['1'] = $wan['1'] - $arr[0];
        $wan['2'] = $wan['2'] - $arr[0];
        $wan['3'] = $wan['3'] - $arr[0];
        $wan['4'] = $wan['4'] - $arr[0];
        $wan['5'] = $wan['5'] - $arr[0];
        $wan['6'] = $wan['6'] - $arr[0];
        $wan['7'] = $wan['7'] - $arr[0];
        $wan['8'] = $wan['8'] - $arr[0];
        $wan['9'] = $wan['9'] - $arr[0];
        $wan['0'] = $wan['0'] - $arr[0];
        if($wan['???'] > $wan['???']){
            $wan['???'] = $wan['???'] - $wan['???'];
            $wan['???'] = 0;
        }elseif($wan['???'] > $wan['???']){
            $wan['???'] = $wan['???'] - $wan['???'];
            $wan['???'] = 0;
        }elseif($wan['???'] == $wan['???']){
            $wan['???'] = 0;
            $wan['???'] = 0;
        }
        if($wan['???'] > $wan['???']){
            $wan['???'] = $wan['???'] - $wan['???'];
            $wan['???'] = 0;
        }elseif($wan['???'] > $wan['???']){
            $wan['???'] = $wan['???'] - $wan['???'];
            $wan['???'] = 0;
        }elseif($wan['???'] == $wan['???']){
            $wan['???'] = 0;
            $wan['???'] = 0;
        }
        $arr = array($qian['1'], $qian['2'], $qian['3'], $qian['4'], $qian['5'], $qian['6'], $qian['7'], $qian['8'], $qian['9'], $qian['0']);
        sort($arr);
        $qian['1'] = $qian['1'] - $arr[0];
        $qian['2'] = $qian['2'] - $arr[0];
        $qian['3'] = $qian['3'] - $arr[0];
        $qian['4'] = $qian['4'] - $arr[0];
        $qian['5'] = $qian['5'] - $arr[0];
        $qian['6'] = $qian['6'] - $arr[0];
        $qian['7'] = $qian['7'] - $arr[0];
        $qian['8'] = $qian['8'] - $arr[0];
        $qian['9'] = $qian['9'] - $arr[0];
        $qian['0'] = $qian['0'] - $arr[0];
        if($qian['???'] > $qian['???']){
            $qian['???'] = $qian['???'] - $qian['???'];
            $qian['???'] = 0;
        }elseif($qian['???'] > $qian['???']){
            $qian['???'] = $qian['???'] - $qian['???'];
            $qian['???'] = 0;
        }elseif($qian['???'] == $qian['???']){
            $qian['???'] = 0;
            $qian['???'] = 0;
        }
        if($qian['???'] > $qian['???']){
            $qian['???'] = $qian['???'] - $qian['???'];
            $qian['???'] = 0;
        }elseif($qian['???'] > $qian['???']){
            $qian['???'] = $qian['???'] - $qian['???'];
            $qian['???'] = 0;
        }elseif($qian['???'] == $qian['???']){
            $qian['???'] = 0;
            $qian['???'] = 0;
        }
        $arr = array($bai['1'], $bai['2'], $bai['3'], $bai['4'], $bai['5'], $bai['6'], $bai['7'], $bai['8'], $bai['9'], $bai['0']);
        sort($arr);
        $bai['1'] = $bai['1'] - $arr[0];
        $bai['2'] = $bai['2'] - $arr[0];
        $bai['3'] = $bai['3'] - $arr[0];
        $bai['4'] = $bai['4'] - $arr[0];
        $bai['5'] = $bai['5'] - $arr[0];
        $bai['6'] = $bai['6'] - $arr[0];
        $bai['7'] = $bai['7'] - $arr[0];
        $bai['8'] = $bai['8'] - $arr[0];
        $bai['9'] = $bai['9'] - $arr[0];
        $bai['0'] = $bai['0'] - $arr[0];
        if($bai['???'] > $bai['???']){
            $bai['???'] = $bai['???'] - $bai['???'];
            $bai['???'] = 0;
        }elseif($bai['???'] > $bai['???']){
            $bai['???'] = $bai['???'] - $bai['???'];
            $bai['???'] = 0;
        }elseif($bai['???'] == $bai['???']){
            $bai['???'] = 0;
            $bai['???'] = 0;
        }
        if($bai['???'] > $bai['???']){
            $bai['???'] = $bai['???'] - $bai['???'];
            $bai['???'] = 0;
        }elseif($bai['???'] > $bai['???']){
            $bai['???'] = $bai['???'] - $bai['???'];
            $bai['???'] = 0;
        }elseif($bai['???'] == $bai['???']){
            $bai['???'] = 0;
            $bai['???'] = 0;
        }
        $arr = array($shi['1'], $shi['2'], $shi['3'], $shi['4'], $shi['5'], $shi['6'], $shi['7'], $shi['8'], $shi['9'], $shi['0']);
        sort($arr);
        $shi['1'] = $shi['1'] - $arr[0];
        $shi['2'] = $shi['2'] - $arr[0];
        $shi['3'] = $shi['3'] - $arr[0];
        $shi['4'] = $shi['4'] - $arr[0];
        $shi['5'] = $shi['5'] - $arr[0];
        $shi['6'] = $shi['6'] - $arr[0];
        $shi['7'] = $shi['7'] - $arr[0];
        $shi['8'] = $shi['8'] - $arr[0];
        $shi['9'] = $shi['9'] - $arr[0];
        $shi['0'] = $shi['0'] - $arr[0];
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
        $arr = array($ge['1'], $ge['2'], $ge['3'], $ge['4'], $ge['5'], $ge['6'], $ge['7'], $ge['8'], $ge['9'], $ge['0']);
        sort($arr);
        $ge['1'] = $ge['1'] - $arr[0];
        $ge['2'] = $ge['2'] - $arr[0];
        $ge['3'] = $ge['3'] - $arr[0];
        $ge['4'] = $ge['4'] - $arr[0];
        $ge['5'] = $ge['5'] - $arr[0];
        $ge['6'] = $ge['6'] - $arr[0];
        $ge['7'] = $ge['7'] - $arr[0];
        $ge['8'] = $ge['8'] - $arr[0];
        $ge['9'] = $ge['9'] - $arr[0];
        $ge['0'] = $ge['0'] - $arr[0];
        if($ge['???'] > $ge['???']){
            $ge['???'] = $ge['???'] - $ge['???'];
            $ge['???'] = 0;
        }elseif($ge['???'] > $ge['???']){
            $ge['???'] = $ge['???'] - $ge['???'];
            $ge['???'] = 0;
        }elseif($ge['???'] == $ge['???']){
            $ge['???'] = 0;
            $ge['???'] = 0;
        }
        if($ge['???'] > $ge['???']){
            $ge['???'] = $ge['???'] - $ge['???'];
            $ge['???'] = 0;
        }elseif($ge['???'] > $ge['???']){
            $ge['???'] = $ge['???'] - $ge['???'];
            $ge['???'] = 0;
        }elseif($ge['???'] == $ge['???']){
            $ge['???'] = 0;
            $ge['???'] = 0;
        }
        if($zong['???'] > $zong['???']){
            $zong['???'] = $zong['???'] - $zong['???'];
            $zong['???'] = 0;
        }elseif($zong['???'] > $zong['???']){
            $zong['???'] = $zong['???'] - $zong['???'];
            $zong['???'] = 0;
        }elseif($zong['???'] == $zong['???']){
            $zong['???'] = 0;
            $zong['???'] = 0;
        }
        if($zong['???'] > $zong['???']){
            $zong['???'] = $zong['???'] - $zong['???'];
            $zong['???'] = 0;
        }elseif($zong['???'] > $zong['???']){
            $zong['???'] = $zong['???'] - $zong['???'];
            $zong['???'] = 0;
        }elseif($zong['???'] == $zong['???']){
            $zong['???'] = 0;
            $zong['???'] = 0;
        }
    }
    $bets = array();
    foreach($wan as $key => $money){
        if($key == '1' && $money > 0){
            $shuzi['ip_1000-1'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $shuzi['ip_1000-2'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $shuzi['ip_1000-3'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $shuzi['ip_1000-4'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $shuzi['ip_1000-5'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $shuzi['ip_1000-6'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $shuzi['ip_1000-7'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $shuzi['ip_1000-8'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $shuzi['ip_1000-9'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $shuzi['ip_1000-0'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1-1005'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1-1006'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1-1007'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1-1008'] = $money;
            $contents .= '1/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($qian as $key => $money){
        if($key == '1' && $money > 0){
            $shuzi['ip_1001-1'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $shuzi['ip_1001-2'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $shuzi['ip_1001-3'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $shuzi['ip_1001-4'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $shuzi['ip_1001-5'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $shuzi['ip_1001-6'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $shuzi['ip_1001-7'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $shuzi['ip_1001-8'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $shuzi['ip_1001-9'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $shuzi['ip_1001-0'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_2-1005'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_2-1006'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_2-1007'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_2-1008'] = $money;
            $contents .= '2/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($bai as $key => $money){
        if($key == '1' && $money > 0){
            $shuzi['ip_1002-1'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $shuzi['ip_1002-2'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $shuzi['ip_1002-3'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $shuzi['ip_1002-4'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $shuzi['ip_1002-5'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $shuzi['ip_1002-6'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $shuzi['ip_1002-7'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $shuzi['ip_1002-8'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $shuzi['ip_1002-9'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $shuzi['ip_1002-0'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3-1005'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3-1006'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3-1007'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_3-1008'] = $money;
            $contents .= '3/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($shi as $key => $money){
        if($key == '1' && $money > 0){
            $shuzi['ip_1003-1'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $shuzi['ip_1003-2'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $shuzi['ip_1003-3'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $shuzi['ip_1003-4'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $shuzi['ip_1003-5'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $shuzi['ip_1003-6'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $shuzi['ip_1003-7'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $shuzi['ip_1003-8'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $shuzi['ip_1003-9'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $shuzi['ip_1003-0'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_4-1005'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_4-1006'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_4-1007'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_4-1008'] = $money;
            $contents .= '4/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($ge as $key => $money){
        if($key == '1' && $money > 0){
            $shuzi['ip_1004-1'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '2' && $money > 0){
            $shuzi['ip_1004-2'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '3' && $money > 0){
            $shuzi['ip_1004-3'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '4' && $money > 0){
            $shuzi['ip_1004-4'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '5' && $money > 0){
            $shuzi['ip_1004-5'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '6' && $money > 0){
            $shuzi['ip_1004-6'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '7' && $money > 0){
            $shuzi['ip_1004-7'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '8' && $money > 0){
            $shuzi['ip_1004-8'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '9' && $money > 0){
            $shuzi['ip_1004-9'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '0' && $money > 0){
            $shuzi['ip_1004-0'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_5-1005'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_5-1006'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_5-1007'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_5-1008'] = $money;
            $contents .= '5/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($zong as $key => $money){
        if($key == '???' && $money > 0){
            $liangmian['ip_1009'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1010'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1011'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1012'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1013'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1014'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '???' && $money > 0){
            $liangmian['ip_1015'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($q3 as $key => $money){
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1016'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1017'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1018'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1019'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1020'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($z3 as $key => $money){
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1021'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1022'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1023'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1024'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1025'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
    }
    foreach($h3 as $key => $money){
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1026'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1027'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1028'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1029'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
        if($key == '??????' && $money > 0){
            $qzhsan['ip_1030'] = $money;
            $contents .= '??????/' . $key . '/' . $money . ',';
            continue;
        }
    }
    $term = substr($term, 0, 8) . '-' . substr($term, 8, 3);
    if(count($shuzi) > 0){
        $shuzi['JeuValidate'] = 155312191;
        $shuzi['game_code'] = 2;
        $shuzi['typecode'] = 6;
        $shuzi['round'] = $term;
        $shuzi['confirm'] = '??? ???';
    }
    if(count($liangmian) > 0){
        $liangmian['JeuValidate'] = 155312191;
        $liangmian['game_code'] = 2;
        $liangmian['typecode'] = 0;
        $liangmian['round'] = $term;
        $liangmian['confirm'] = '??? ???';
    }
    if(count($qzhsan) > 0){
        $qzhsan['game_code'] = 2;
        $qzhsan['typecode'] = 1;
        $qzhsan['round'] = $term;
        $qzhsan['confirm'] = '??? ???';
    }
    $json = array('shuzi' => $shuzi, 'liangmian' => $liangmian, 'qzhsan' => $qzhsan);
    return $json;
}
?>