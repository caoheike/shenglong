<?php 
include_once 'Public/config.php';
if ($_SESSION['userid'] == "") {
	if (!empty($_GET['code'])) {
		$token = wx_gettoken($wx['ID'], $wx['key'], $_GET['code']);
		$userinfo = wx_getinfo($token['token'], $token['openid']);
		if ($token['openid'] == "") {
			header('Location:' . $oauth);
		}
		if (U_isOK($token['openid'], $userinfo['headimg'])) {
			$r = $mydb->table('fn_user')->where(array('userid' => $token['openid']))->find();
			auto_login($r['id']);
			header("Location: qr.php?room=" . $_SESSION['roomid']);
		} else {
			U_create($token['openid'], $userinfo['nickname'], $userinfo['headimg'], $_COOKIE['agent']);
			$r = $mydb->table('fn_user')->where(array('userid' => $token['openid']))->find();
			auto_login($r['id']);
			header("Location: qr.php?room=" . $_SESSION['roomid']);
		}
	} else {
		header("Location:" . $oauth);
	}
} else {
	header("Location: qr.php?room=" . $_SESSION['roomid']);
}