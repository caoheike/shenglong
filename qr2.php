<?php session_start();?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php session_start();
$room=$_GET['room'];
$agent=$_GET['agent'];
$g=$_GET['g']; //pk10,xyft,cqssc,xy28,jnd28,jsmt,jssc,jsssc,bjl

$wx['ID'] = 'wx28e75661159836b4';
$time = date('Y-m-d H:i:s',time());

//make code 
$oauth = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wx["ID"]."&redirect_uri=".urlencode("http://".$_SERVER["HTTP_HOST"]."/qr2.php?agent=".$_GET['agent']."&g=".$_GET['g']."&room=".$_GET['room'])."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
if($_GET['code']){
$code=$_GET['code'];

}else{
	if(empty($_SESSION['userid']) || empty($_SESSION['username'])){
		header('Location:/qr.php?room='.$room);exit;
	}
//Header("Location: $oauth");
}
echo "<form style='display:none;' id='form1' name='form1' method='post' action='http://".$_SERVER["HTTP_HOST"]."/home.php'>
             
              <input name='verify' type='text' value='n2oqcvVPpk1M' />
			  <input name='room' type='text' value='".$room."' />
			  <input name='agent' type='text' value='".$agent."' />
			  <input name='g' type='text' value='".$g."' />
			  <input name='code' type='text' value='".$code."' />
			  <input name='time' type='text' value='".$time."' />
              			  
            </form><script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";
?>