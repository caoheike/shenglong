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
	<link rel="stylesheet" type="text/css" href="/Templates/Home/images/style.css">
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

<body class="index-sea" style="" onload="loaded ()">

 <div style="main">
		<section class="main"><div class="per-info">
	<div class="pic ">
		<span class="sw"></span>
		<img src="<?php echo $_SESSION['headimg'];
?>">
	</div>
	<div class="view" style="float: left;">
		<h3><?php echo $_SESSION['username'];?></h3>
		<!--<p>编号:10030</p>-->
					<p class="ble">余点:<?php echo get_query_val("fn_user", "money", array("roomid" => $_SESSION['roomid'], 'userid' => $_SESSION['userid']));
?></p>	</div>
	<span class="xsver">v3.9.3</span>
	<div class="apporname">
					<p>新应用-请改名</p>	</div>
</div>
<div class="game-list">
	<ul>
		<li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=pk10">
						<p><img src="/Templates/Home/images/icon_pk10.png">
												</p>
						<font>北京赛车</font>
					</a>
				</li><li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=xyft">
						<p><img src="/Templates/Home/images/icon_xfft.png">
												</p>
						<font>幸运飞艇</font>
					</a>
				</li><li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jssc">
						<p><img src="/Templates/Home/images/icon_jspk.png">
												</p>
						<font>极速赛车</font>
					</a>
				</li><li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=cqssc">
						<p><img src="/Templates/Home/images/icon_cqssc.png">
												</p>
						<font>重庆时时彩</font>
					</a>
				</li><li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=jsssc">
						<p><img src="/Templates/Home/images/icon_jsssc.png">
												</p>
						<font>极速时时彩</font>
					</a>
				</li><li class="">
					<a href="qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=azxy5 ">
						<p><img src="/Templates/Home/images/icon_ozssc.png">
												</p>
						<font>澳洲幸运5</font>
					</a>
				</li>	</ul>
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
<div style="background:url(Style/images/fhome.png) center center no-repeat;background-size:contain;     left: 50%;    margin-left: -3.8rem;   width: 7.5rem;
    height: 7.5rem;
    position: fixed;color: #e7c49c;    text-align: center;
    z-index: 1199;bottom: 0;"><div class="" style="    margin-top: 30px;" onclick="location.href='index.php'" ><div style="background:url(Style/images/i05.jpg) center center no-repeat;background-size:contain;" class="navicon"></div>大厅</div></div>
<style>
.navnav{width:20%;height:50px;text-align:center;float:left;font-size:14px;color:#e7c49c;    padding: 5px 0;}
.navicon{width:30px;height:30px;background:#333;margin:0 auto}
</style>
<div style="
    width: 100%;
    position: fixed;
    z-index: 99;
    left: 0;
    bottom: 0;
    border-top: 1px solid #baa6c8;
    background: #251159;
    background: -moz-linear-gradient(top,#744c90,rgba(34,17,87,1));
    background: -webkit-gradient(linear,0 0,0 bottom,from(#744c90),to(rgba(34,17,87,1)));
    background: -o-linear-gradient(top,#744c90,rgba(34,17,87,1));">
	<div class="navnav" onclick="location.href='/Templates/user/'"><div style="background:url(Style/images/i01.jpg) center center no-repeat;background-size:contain" class="navicon"></div>个人中心</div>
<div class="navnav" onclick="sho('hd')"><div style="background:url(Style/images/i03.jpg) center center no-repeat;background-size:contain" class="navicon"></div>上下分</div>
<div class="navnav" onclick="location.href='index.php'" ><div style="background:url(Style/images/i05.jpg) center center no-repeat;background-size:contain;" class="navicon"></div></div>

<?php
if (isset($_GET["shiwan"]) && $_GET["shiwan"] == "t") {
	$pre_roomid = $_SESSION["roomid"];
?>
<div class="navnav"
onclick="alert('退出试玩需要重新登录');location.href='qr.php?tuichu=t&room=<?php echo $_SESSION['roomid'];
?>';"> 
<div
style="background: url(Style/images/i04.jpg) center center no-repeat; background-size: contain"
	class="navicon"></div>退出试玩		</div>
<?php
}
else {
?>

<div class="navnav" onclick="alert('试玩帐号仅供试玩，资金提现等操作无效');location.href='index.php?shiwan=t';"><div style="background:url(Style/images/i04.jpg) center center no-repeat;background-size:contain" class="navicon"></div>试玩</div>

<?php
}
?>

<div class="navnav" onclick="sho('wt')"><div style="background:url(Style/images/i02.jpg) center center no-repeat;background-size:contain" class="navicon"></div>客服</div>


</div>

<div style="display:block;width:100%;height:100%;position:fixed;top:0;z-index:99999999;background:rgba(0,0,0,0.6)" id="bbbg"></div>
<div style="display:none;width:100%;height:75%;position:fixed;bottom:0;z-index:999999999;background:#fff;border-radius:10px;overflow:hidden" id="hd"><div style="width:100%;height:30px;line-height:30px;text-align:right;color:#fff;background:url(Style/images/zz.png) right center no-repeat;" onclick="cls('hd')">　</div>





<div style="width:100%;height:31rem;color:#000;overflow:scroll;"><div style="padding:10px"  class="ncon"><!--<center>最新活动信息可添加客服微信了解</center>--><!--<div style="width:40%;padding:5px 0;text-align:center;border:1px solid #000;float:left;border-radius:5px;margin-left:15px;" onclick="location.href='/Templates/user/codepay/'">上分</div><div style="width:40%;padding:5px 0;text-align:center;border:1px solid #000;float:right;border-radius:5px;margin-right:15px;" onclick="location.href='/Templates/user/finance/'">下分</div>-->

<div class="layui-m-layerchild  layui-m-anim-up" style="position:fixed;bottom:0;left:1%;width:98%; max-height:100%; min-height:40%; border: none;border-top-left-radius: 10px; border-top-right-radius: 10px; -webkit-animation-duration: .5s; animation-duration: .5s;"><div class="layui-m-layercont"><div class="score ajax_charge" style="padding-bottom: 20px;">
	<h4 class="game-tit game-tit-bg" style="background:url(Style/images/pp.png)  no-repeat;"> <b style="    padding-left: 30px;">上分、下分</b></h4>
	<div class="score-info">
		<div class="pic">
			<img src="<?php echo $_SESSION['headimg'];
?>" style="width:100%; height:100%; ">
		</div>
		<div class="view">
			<a href="/Templates/user/paylog.php" class="log">上下分记录</a>
			<h4 style="text-align: left;"><?php echo $_SESSION['username'];
?></h4>
			<p>当前余点：<?php echo get_query_val("fn_user", "money", array("roomid" => $_SESSION['roomid'], 'userid' => $_SESSION['userid']));
?></p>
		</div>
	</div>
	<dl>
		<dt class="ftype">
			<a href="/Templates/user/codepay/">上分</a>
			<a href="/Templates/user/finance/">下分</a>
		</dt>
		<!--<dd>
			<input type="number" name="money" data-max="0" step="0.01" placeholder="请输入上下分值">
		</dd>
	</dl>
	<div class="sub-btn">
		<a href="javascript:;" class="submit" data-uri="/member/ajax_charge.html">确定操作</a>
	</div>-->
	<p style="padding: 10px;margin-top: 5px; color: #999;">请添加客服转账后，发起上下分申请！</p>
</div></div></div>

</div></div>

<!--<div style="width:100px;height:30px;background:#928124;margin:0 auto; border-radius:10px;line-height:30px;text-align:center;color:#fff;margin-top:5px" onclick="cls('hd')">关闭</div>-->
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
<!-- qr2.php?room=<?php echo $_SESSION['roomid'];
?>&g=azxy5 -->
</html>