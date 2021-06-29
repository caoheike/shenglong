<?php include_once(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/config.php");
$sql = get_query_vals('fn_setting', '*', array('roomid' => $_SESSION['roomid']));
if($_GET['g'] == ''){
    setcookie('game', $sql['setting_game'], time() + 36000000);
}else{
    $version = get_query_val('fn_room', 'version', array('roomid' => $_SESSION['roomid']));
    if($version != '会员版' && $version != '尊享版'){
        echo '<center><strong style="color:red;font-size:150px">不支持此功能</strong></center>';
        exit;
    }
    setcookie("game", $_GET['g'], time() + 36000000);
}
select_query("fn_welcome", '*', array("roomid" => $_SESSION['roomid']));
while($con = db_fetch_array()){
    $welcome .= "{$con['content']},";
}
$welcome = substr($welcome, 0, strlen($welcome) - 1);
?>
<!DOCTYPE html>
<html lang="en" data-dpr="2" style="font-size: 14px;">
<head>
    <meta charset="UTF-8">
    <title><?php echo $sitename ?></title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<script src="Style/js/jquery.js" type="text/javascript"></script>
	<style>
	   #circularMenu{padding:0;margin:0 auto;list-style:none;position:relative;width:300px;height:300px; }
#circularMenu li{display:block;width:80px;height:80px;position:absolute;}


#circularMenu li.home{left:110px;top:14px;}

#circularMenu li.chat{left:190px;top:50px;}
#circularMenu li.upload{left:30px;top:50px;}

#circularMenu li.email{left:220px;top:130px;}
#circularMenu li.address{left:-0px;top:130px;}


#circularMenu li.shop{left:190px;top:210px;}
#circularMenu li.search{left:30px;top:210px;}

#circularMenu li.aibjl{left:110px;top:130px;}
#circularMenu li.delivery{left:110px;top:246px;}




	  </style>
    <style type="text/css">
       body,html{margin:0;padding:0;width:100%;height:100%;max-width:640px;margin:0 auto}
	   body{background:url(upload/bg_10.jpg) no-repeat #000;background-position:bottom center; background-size:contain; min-height:100%; max-width:640px}
	   *{max-width:640px}
	   .ncon img{width:50%;display:block;margin:0 auto}
	   .abtn{width:149px;float:left;height:35px;color:#a56010;line-height:35px;text-align:center;background:rgba(255,255,255,0.8);font-size:16px}
	   .b1{border-radius:17px 0 0 17px;border-right:1px solid #ccc;}
	   
	   .b2{border-radius:0 17px 17px 0}
	   
	   .aact{color:#a56010;background:url(upload/sbg.png);}
    </style>

</head>

<body style="" onload="loaded ()">

  <img src="upload/aa1.jpg" width="100%" style="display:block;height:138px">
  <div style="width:100%;height:26px;font-size:16px;line-height:25px;color:#fff;background:#000;margin-top:0;overflow:hidden"><img src="<?php echo $_SESSION['headimg'];
?>" style="display:block;margin:3px 0 3px 10px;float:left;width:20px;height:20px;border-radius:12px"><span style="margin-left:10px"><?php echo $_SESSION['username'];
?></span><span style="float:right;margin-right:10px">剩余点数：<?php echo get_query_val("fn_user", "money", array("roomid" => $_SESSION['roomid'], 'userid' => $_SESSION['userid']));
?></span></div>

  <marquee style="background:#000;height:30px;margin:0;padding:0;overflow:hidden;line-height:30px;display:block"><span style="font-size: 12px;color: #d1bb40;">抵制不良游戏，拒绝盗版游戏。 适度游戏益脑，沉迷游戏伤身。 合理安排时间，享受健康生活 </marquee>

<div style="width:300px;margin:0 auto">
<div class="abtn b1 aact" onclick="cpyx()">彩票游戏</div>
<div class="abtn b2" onclick="zrsx()">真人视讯</div>
</div>


<div style="width:100%;height:calc(100vh - 265px);overflow:hidden" id="isc">



<ul id="circularMenu">
	<li class="home"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=pk10"><img src="/Style/images/bjpk10.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="chat"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=xyft"><img src="/Style/images/xyft.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="email"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=cqssc"><img src="/Style/images/cqssc.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="shop"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=xy28"><img src="/Style/images/xy28.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="aibjl"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=bjl"><img src="/Style/images/bjl.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="search"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jsmt"><img src="/Style/images/jsmt.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="address"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jssc"><img src="/Style/images/jssc.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
	<li class="upload"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jsssc"><img src="/Style/images/jsssc.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
<li class="delivery"><a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jnd28"><img src="/Style/images/jnd28.png" style="display:block;width:100%;float:left; border-radius:50%"/></a></li>
</ul>
	
	
	
</div>


<div style="width:100%;height:calc(100vh - 230px);overflow:hidden;display:none" id="isc2">


    <img src="upload/lhc.png" style="display:block;margin:10px auto 0 auto;width:300px"/>
	<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=bjl"><img src="upload/bjl.png" style="display:block;margin:10px auto 0 auto;width:300px"/></a>
	
</div>	
<script src='Style/Home/js/iscroll.js'></script>
<script type="text/javascript">
var myScroll;

function loaded () {
	myScroll = new IScroll('#isc', {
		scrollbars: false,
		mouseWheel: true,
		interactiveScrollbars: false,
		shrinkScrollbars: 'scale',
		fadeScrollbars: false,
		bounce:true,
		 click:true,
	});
	
	myScroll = new IScroll('#isc2', {
		scrollbars: false,
		mouseWheel: true,
		interactiveScrollbars: false,
		shrinkScrollbars: 'scale',
		fadeScrollbars: false,
		bounce:true,
		 click:true,
	});
}
</script>

<style>
.navnav{width:20%;height:50px;text-align:center;float:left;font-size:14px;color:#fff;font-weight:bold}
.navicon{width:30px;height:30px;background:#333;margin:0 auto}
</style>
<div style="width:100%;height:50px;z-index:99999;position:fixed;bottom:0">
<div class="navnav" onclick="location.href='index.php'"><div style="background:url(Style/images/i05.png) center center no-repeat;background-size:contain" class="navicon"></div>大厅首页</div>
<div class="navnav" onclick="sho('hd')"><div style="background:url(Style/images/i02.png) center center no-repeat;background-size:contain" class="navicon"></div>上下分</div>
<div class="navnav" onclick="location.href='/Templates/user/'"><div style="background:url(Style/images/i03.png) center center no-repeat;background-size:contain" class="navicon"></div>个人中心</div>

<?php
if (isset($_GET["shiwan"]) && $_GET["shiwan"] == "t") {
	$pre_roomid = $_SESSION["roomid"];
?>
<div class="navnav"
onclick="alert('退出试玩需要重新登录');location.href='qr.php?tuichu=t&room=<?php echo $_SESSION['roomid'];
?>';"> 
<div
style="background: url(Style/images/i04.png) center center no-repeat; background-size: contain"
	class="navicon"></div>退出试玩		</div>
<?php
}
else {
?>

<div class="navnav" onclick="alert('试玩帐号仅供试玩，资金提现等操作无效');location.href='index.php?shiwan=t';"><div style="background:url(Style/images/i04.png) center center no-repeat;background-size:contain" class="navicon"></div>试玩</div>

<?php
}
?>

<div class="navnav" onclick="sho('wt')"><div style="background:url(Style/images/i01.png) center center no-repeat;background-size:contain" class="navicon"></div>常见问题</div>


</div>

<div style="display:block;width:100%;height:100%;position:fixed;top:0;z-index:99999999;background:rgba(0,0,0,0.6)" id="bbbg"></div>
<div style="display:none;width:100%;height:100px;position:fixed;bottom:0;z-index:999999999;background:#fff;border:2px solid #928124;border-radius:10px;overflow:hidden" id="hd"><div style="width:100%;height:30px;line-height:30px;text-align:right;background:#928124;color:#fff" onclick="cls('hd')">关闭　　</div>

<div style="width:100%;height:230px;color:#000;overflow:scroll;"><div style="padding:10px"  class="ncon"><!--<center>最新活动信息可添加客服微信了解</center>--><div style="width:40%;padding:5px 0;text-align:center;border:1px solid #000;float:left;border-radius:5px;margin-left:15px;" onclick="location.href='/Templates/user/codepay/'">上分</div><div style="width:40%;padding:5px 0;text-align:center;border:1px solid #000;float:right;border-radius:5px;margin-right:15px;" onclick="location.href='/Templates/user/finance/'">下分</div></div></div>

<div style="width:100px;height:30px;background:#928124;margin:0 auto; border-radius:10px;line-height:30px;text-align:center;color:#fff;margin-top:5px" onclick="cls('hd')">关闭</div>
</div>



<div style="display:block;width:300px;height:300px;position:fixed;top:50%;margin-top:-152px;left:50%;margin-left:-152px;z-index:999999999;background:#fff;border:2px solid #928124;border-radius:10px;overflow:hidden" id="wt"><div style="width:100%;height:30px;line-height:30px;text-align:center;background:#928124;color:#fff">常见问题</div>

<?php $kefu = get_query_val('fn_setting', 'setting_kefu', array("roomid" => $_SESSION['roomid']));
if($kefu != ""){
    ?>
<div style="width:100%;height:230px;color:#928124;overflow:scroll"><div style="padding:10px" class="ncon"><?php echo $kefu;
    ?></div></div>
<?php }
?>

<div style="width:100px;height:30px;background:#928124;margin:0 auto; border-radius:10px;line-height:30px;text-align:center;color:#fff;margin-top:5px" onclick="cls('wt')">关闭</div>
</div>
<script>
function cls(a){
	
	$('#bbbg').hide();
	
	
	$('#'+a).hide();
}

function sho(a){
	
	$('#bbbg').show();
	
	
	$('#'+a).show();
}

function cpyx(){
	
	$('.b1').attr('class','abtn b1 aact');
	$('.b2').attr('class','abtn b2');
	$("#isc2").hide();
	$("#isc").show();
}

function zrsx(){
	
	$('.b1').attr('class','abtn b1');
	$('.b2').attr('class','abtn b2 aact');
	$("#isc").hide();
	$("#isc2").show();
}
</script>
</body>

</html>