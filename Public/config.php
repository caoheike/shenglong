<?php

session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/html;charset=utf-8");
$load = 5;
include_once ("sql.php");
$console = "创新娱乐";
$db['host'] = "localhost";
$db['user'] = "feiniao_com";
$db['pass'] = "Em7i7FaccaPcK33n";
$db['name'] = "feiniao_com";
$dbconn = db_connect($db['host'], $db['user'], $db['pass'], $db['name']);
include_once ("db.class.php");
$mydb = new db(array($db['host'], 'DB_USER' => $db['user'], 'DB_PWD' => $db['pass'], 'DB_NAME' => $db['name']));
$wx['ID'] = 'wx28e75661159836b4';
$wx['key'] = '8f333238acd17ec01f262ed3fd49aee9';
$redirect_uri = urlencode("http://{$_SERVER["HTTP_HOST"]}/wx_login.php?agent={$_GET['agent']}&g={$_GET['g']}&room={$_GET['room']}&logintype=wx");
//$oauth = "http://ssc.mpqdx.com.com/wx_login.php?appid={$wx["ID"]}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
$oauth = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wx["ID"]}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
function room_isOK($roomid) {
    $status = get_query_val('fn_room', 'id', array('roomid' => $roomid));
    if ($status == "") {
        return false;
    }
    return true;
}

function vpost($url, $data = array()) {// 模拟提交数据函数
    $curl = curl_init();
    // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1);
    // 发送一个常规的Post请求
    @curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl);
    // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);
        //捕抓异常
    }
    curl_close($curl);
    // 关闭CURL会话
    return $tmpInfo;
    // 返回数据
}

function wx_gettoken($Appid, $Appkey, $code) {
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $Appid . "&secret=" . $Appkey . "&code=" . $code . "&grant_type=authorization_code";
    $html = vpost($url);
    $json = json_decode($html, 1);
    $access_token = $json['access_token'];
    $openid = $json['openid'];
    return array("token" => $access_token, 'openid' => $openid);
}

//2017-10-21 获取全局access token
// function wx_getaccesstoken($Appid, $Appkey){
// $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". $Appid ."&secret=". $Appkey;
// $html = file_get_contents($url);
// $json = json_decode($html, 1);
// $access_token = $json['access_token'];
// $expires = $json['expires_in'];
// return array("access_token" => $access_token, 'expires_in' => $expires);
// }
//用全局access token 和 openid 获取用户详情
// function wx_getinfo2($token, $openid){
// $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=". $token ."&openid=". $openid;
// $html = file_get_contents($url);
// $json = json_decode($html, 1);
// $nickname = $json['nickname'];
// $headhtml = $json['headimgurl'];
// return array("nickname" => $nickname, 'headimg' => $headhtml);
// }

function wx_getinfo($token, $openid) {
    $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token . "&openid=" . $openid . "&lang=zh_CN";
    $html = vpost($url);
    $json = json_decode($html, 1);
    $nickname = $json['nickname'];
    $headhtml = $json['headimgurl'];
    return array("nickname" => $nickname, 'headimg' => $headhtml);
}

function U_create($userid, $username, $headimg, $agent = "null") {
    if ($agent == "") {
        $agent = 'null';
    }
    //insert_query("fn_user", array("userid" => $userid, 'username' => $username, 'headimg' => $_SESSION['headimg'], 'money' => '0', 'roomid' => $_SESSION['roomid'], 'statustime' => time(), 'agent' => $agent, 'isagent' => 'false', 'jia' => 'false'));
    insert_query("fn_user", array("userid" => $userid, 'username' => $username, 'headimg' => $headimg, 'money' => '0', 'roomid' => $_SESSION['roomid'], 'statustime' => time(), 'agent' => $agent, 'isagent' => 'false', 'jia' => 'false','upass'=>$agent));
    return true;
}

function U_isOK($userid, $headimg) {
    $status = get_query_val('fn_user', 'id', array('userid' => $userid, 'roomid' => $_SESSION['roomid']));
    if ($status == "") {
        return false;
    }
    update_query("fn_user", array("headimg" => $headimg), array('id' => $status));
    return true;
}

//网页登录处理
function web_login() {
    global $mydb;
    $loginuser = $_POST['loginuser'];
    $loginpass = md5($_POST['loginpass']);
    $r = $mydb->table('fn_user')->where(array('loginuser' => $loginuser, 'loginpass' => $loginpass))->find();
    if ($r) {
        return auto_login($r['id']);
    } else {
        return false;
    }
}

//登录过程
function auto_login($id) {
    global $mydb;
    $_r = $mydb->table('fn_user')->where(array('id' => $id))->find();
    $_SESSION['userid'] = $_r['userid'];
    $_SESSION['username'] = $_r['username'];
    $_SESSION['headimg'] = $_r['headimg'];
    return true;
}

//检查是否登录
function check_login() {
    if (empty($_SESSION['userid'])) {
        if (isWeixin()) {
            header('Location: wx_login.php');
        } else {
            header('Location: web_login.php');
        }
    }
}

//是否在微信内
function isWeixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

function get_user_money() {
    global $mydb;
    $r = $mydb->table('fn_user')->field('money')->where(array('userid' => $_SESSION['userid']))->find();
    $time = array();
    $time[0] = date('Y-m-d') . " 00:00:00";
    $time[1] = date('Y-m-d') . " 23:59:59";
    $map['roomid'] = $_SESSION['roomid'];
    $map['userid'] = $_SESSION['userid'];

    $map['type'] = '上分';
    $map['time'] = array('between', array('' . $time[0] . '', '' . $time[1] . ''));
    $sf = $mydb->table('fn_upmark')->field('sum(money)')->where($map)->find();
    $sf = (int) $sf['sum(money)'];

    $map['type'] = '下分';
    $xf = $mydb->table('fn_upmark')->field('sum(money)')->where($map)->find();
    $xf = (int) $xf['sum(money)'];

    unset($map['type']);
    unset($map['time']);
    $map['addtime'] = array('between', array('' . $time[0] . '', '' . $time[1] . ''));
    $map['_string'] = 'status > 0';
    $allz = $mydb->table('fn_order')->field('sum(status)')->where($map)->find();
    $allz = $allz['sum(status)'];
    
    $sscz = $mydb->table('fn_sscorder')->field('sum(status)')->where($map)->find();
    $sscz = $sscz['sum(status)'];
    $jssscz = $mydb->table('fn_jssscorder')->field('sum(status)')->where($map)->find();
    $jssscz = $jssscz['sum(status)'];
    $jsscz = $mydb->table('fn_jsscorder')->field('sum(status)')->where($map)->find();
    $jsscz = $jsscz['sum(status)'];
    $mtz = $mydb->table('fn_mtorder')->field('sum(status)')->where($map)->find();
    $mtz = $mtz['sum(status)'];
    $pcz = $mydb->table('fn_pcorder')->field('sum(status)')->where($map)->find();
    $pcz = $pcz['sum(status)'];
    $bjlz = $mydb->table('fn_bjlorder')->field('sum(status)')->where($map)->find();
    $bjlz = $bjlz['sum(status)'];
    
    unset($map['status']);
    $map['_string'] = 'status > 0 or status < 0';
    $allm = $mydb->table('fn_order')->field('sum(money)')->where($map)->find();
    $allm = $allm ['sum(money)'];
    
    
    $sscm = $mydb->table('fn_sscorder')->field('sum(money)')->where($map)->find();
    $sscm = $sscm['sum(status)'];
    $jssscm = $mydb->table('fn_jssscorder')->field('sum(money)')->where($map)->find();
    $jssscm = $jssscm['sum(status)'];
    $jsscm = $mydb->table('fn_jsscorder')->field('sum(money)')->where($map)->find();
    $jsscm = $jsscm['sum(status)'];
    $mtm = $mydb->table('fn_mtorder')->field('sum(money)')->where($map)->find();
    $mtm = $mtm['sum(status)'];
    $pcm = $mydb->table('fn_pcorder')->field('sum(money)')->where($map)->find();
    $pcm = $pcm['sum(status)'];
    $bjlm = $mydb->table('fn_bjlorder')->field('sum(money)')->where($map)->find();
    $bjlm = $bjlm['sum(money)'];

    $sscyk = $sscz - $sscm;
    $jssscyk = $jssscz - $jssscm;
    $jsscyk = $jsscz - $jsscm;
    $mtyk = $mtz - $mtm;
    $pcyk = $pcz - $pcm;
    $bjlyk = $bjlz - $bjlm;
    $yk = $allz - $allm;
    $yk += $pcyk + $mtyk + $sscyk + $jsscyk + $jssscyk+$bjlyk;
    $allm += $pcm + $mtm + $sscm + $jsscm + $jssscm+$bjlm;
    $yk = round($yk, 2);
    $r['yk'] = $yk;
    $r['liu'] = $allm;

    return $r;
}

?>
