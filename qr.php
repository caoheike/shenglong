<?php session_start();?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php session_start();
$room=$_GET['room'];
$agent=$_GET['agent'];
if($room){
	$_SESSION['room'] = $room;
}else{
	$room = $_SESSION['room'];
}
if(isset($_GET['shiwan']) and $_GET['shiwan']=='t'){
	session_destroy();
}
if(isset($_GET['tuichu']) and $_GET['tuichu']=='t'){
	session_destroy();
	setcookie('logintime', '', time() - 3600);
	setcookie('agent', '', time() - 3600);
	setcookie('game', '', time() - 3600);
}

$g=$_GET['g']; //pk10,xyft,cqssc,xy28,jnd28,jsmt,jssc,jsssc,bjl

$wx['ID'] = 'wx28e75661159836b4';
$time = date('Y-m-d H:i:s',time());
if(!empty($_GET['logintype']) && $_GET['logintype'] == 'wx'){
	//make code 
	$oauth = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wx["ID"]."&redirect_uri=".urlencode("http://".$_SERVER["HTTP_HOST"]."/qr.php?agent=".$_GET['agent']."&g=".$_GET['g']."&logintype=wx&room=".$room)."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
	if($_GET['code']){
	$code=$_GET['code'];
	$login_type = 'wx';
	}else{
		Header("Location: $oauth");exit;
	}
}

echo "<form style='display:none;' id='form1' name='form1' method='post' action='http://".$_SERVER["HTTP_HOST"]."/index.php'>
              
			  <input name='logintype' value='".$login_type."'>
              <input name='verify' type='text' value='n2oqcvVPpk1M' />
			  <input name='room' type='text' value='".$room."' />
			  <input name='agent' type='text' value='".$agent."' />
			  <input name='g' type='text' value='".$g."' />
			  <input name='code' type='text' value='".$code."' />
			  <input name='time' type='text' value='".$time."' />
              			  
            </form><script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";
?>