<?php
switch($_COOKIE['game']){
case 'xy28': $lot = 'fn_lottery4';
    break;
case "jnd28": $lot = 'fn_lottery5';
    break;
}
$info = get_query_vals($lot, '*', array('roomid' => $_SESSION['roomid']));

//
$info1 = get_query_vals('fn_lottery1', '*', array('roomid' => $_SESSION['roomid']));
$info2 = get_query_vals('fn_lottery2', '*', array('roomid' => $_SESSION['roomid']));
$info3 = get_query_vals('fn_lottery3', '*', array('roomid' => $_SESSION['roomid']));
$info4 = get_query_vals('fn_lottery4', '*', array('roomid' => $_SESSION['roomid']));
$info5 = get_query_vals('fn_lottery5', '*', array('roomid' => $_SESSION['roomid']));
$info6 = get_query_vals('fn_lottery6', '*', array('roomid' => $_SESSION['roomid']));
$info7 = get_query_vals('fn_lottery7', '*', array('roomid' => $_SESSION['roomid']));
$info8 = get_query_vals('fn_lottery8', '*', array('roomid' => $_SESSION['roomid']));
$info9 = get_query_vals('fn_lottery9', '*', array('roomid' => $_SESSION['roomid']));
$pk10open = $info1['gameopen'];
$xyftopen = $info2['gameopen'];
$cqsscopen = $info3['gameopen'];
$xy28open = $info4['gameopen'];
$jnd28open = $info5['gameopen'];
$jsmtopen = $info6['gameopen'];
$jsscopen = $info7['gameopen'];
$jssscopen = $info8['gameopen'];
$bjlopen = $info9['gameopen'];

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="format-detection" content="telephone=no"-->
<meta name="viewport" content="user-scalable=no">
<title><?php echo $sitename ?></title>
<link rel="Stylesheet" type="text/css" href="Style/Old/css/weui.min.css" />
<link rel="Stylesheet" type="text/css" href="Style/Old/css/style.css" />
<link rel="Stylesheet" type="text/css" href="Style/Old/css/bootstrap.min.css" />
<link rel="Stylesheet" type="text/css" href="Style/Xs/Public/css/wx.css" />
<link rel="Stylesheet" type="text/css" href="Style/Xs/Public/css/layout.css" />
<link rel="Stylesheet" type="text/css" href="Style/Xs/static/css/iconfont.css" />
<script src="Style/Old/js/jquery.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="Style/Old/js/bootstrap.min.js"></script>
</head>
<body>
<script type="text/javascript">
var info = {
	'nickname': "<?php echo $_SESSION['username'] ?>", 
	'headimg':"<?php echo $_SESSION['headimg'] ?>", 
	'userid':"<?php echo $_SESSION['userid'] ?>", 
	'roomid':"<?php echo $_SESSION['roomid'] ?>", 
	'game': "<?php echo $_COOKIE['game'];
?>"
	};
var welcome = new Array(<?php echo $welcome;
?>);
var welHeadimg = "<?php echo get_query_val("fn_setting", "setting_sysimg", array("roomid" => $_SESSION['roomid']));
?>";

var sharetitle = '[<?php echo $_SESSION['username']?>]???????????????<?php echo $sitename;
?>:??????????????????????????????!';
var shareurl = '<?php echo $oauth . '&room=' . $room;
?>';
var shareImg = '<?php echo $_SESSION['headimg'];
?>';
var sharedesc="???????????????????????????????????????????????????????????????????????????[????????????]????????????????????????";
var para = {};
para.url = decodeURIComponent(location.href.split('#')[0]);
$.ajax({
	url: 'Public/initJs.php',
	type: 'post',
	data: para,
	dataType: 'json',
	success: function(data){
		wx.config({
			debug: false, // ??????????????????,???????????????api???????????????????????????alert????????????????????????????????????????????????pc?????????????????????????????????log???????????????pc?????????????????????
			appId: data.appId, // ?????????????????????????????????
			timestamp: data.timestamp, // ?????????????????????????????????
			nonceStr: data.noncestr, // ?????????????????????????????????
			signature: data.signature,// ???????????????????????????1
			jsApiList : [ "onMenuShareTimeline","onMenuShareAppMessage", "onMenuShareQQ","onMenuShareWeibo", "chooseImage","previewImage", "getNetworkType", "scanQRCode","chooseWXPay" ]
		});
	},
	error:function(error){ console.log(error);  }
});

wx.ready(function(){
	wx.onMenuShareTimeline({
		title: sharetitle, // ????????????
		link: shareurl, // ????????????
		imgUrl: shareImg, // ????????????
		success: function () { 
			
		},
		cancel: function () { 
		
		}
	});
	wx.onMenuShareAppMessage({
		title: sharetitle, // ????????????
		desc: sharedesc, // ????????????
		link: shareurl, // ????????????
		imgUrl: shareImg, // ????????????
		type: '', // ????????????,music???video???link??????????????????link
		dataUrl: '', // ??????type???music???video??????????????????????????????????????????
		success: function () { 
			// ??????????????????????????????????????????
		},
		cancel: function () { 
			// ??????????????????????????????????????????
		}
	});	
});

</script>
<!-- New Templates Update -->
<script type="text/javascript" src="/Style/Old/js/tools.js"></script>
<script type="text/javascript" src="/Style/Old/js/chat.js"></script>
<script type="text/javascript" src="/Style/Old/js/pc.js"></script>
<!-- ./New Templates Update -->

<iframe onload="iFrameHeight2();" src="/Templates/Old/shipin.php" name="ifarms" width="980" height="430" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" id="ifarms" class="ifarms"></iframe>
<!-- ????????? -->
<div class="modal fade" id="msgdialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" align="left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <center>
		<?php $qrcode = $sql['setting_qrcode'];
if($qrcode == ""){
?>
			<strong Style="font-size:25px;color:red">??????????????????????????????</strong>
		<?php }else{
?>
			<strong Style="font-size:25px;color:red">???????????????????????????</strong><br /><br />
			<img src="<?php echo $qrcode;
?>">
		<?php }
?>
		</center>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="leftdiv">
	<ul>
		<li class="ulogo"><a href="/Templates/user/"><img src="<?php echo $_SESSION['headimg'];
?>" class="mlogo"></a></li>
		<li class="index" data-id="index"><a href="/qr.php?room=<?php echo $_SESSION['roomid'];
?>"><span>??????</span></a></li>
		<li class="guess" data-id="guess"><span>??????</span></li>
		<li class="cz" data-id="cz"><a href="/Templates/user/codepay/index.php?user=<?php echo $_SESSION['username']?>"><span>??????</span></a></li>
<li class="skefu" data-id="cz"><a href="/Templates/user/finance/index.php?user=<?php echo $_SESSION['username']?>"><span>??????</span></a></li>
		<?php if($sql['display_custom'] != 'false'){
?><li class="skefu" data-id="skefu"><span>??????<em>0</em></span></li><?php }
?>
		<li class="logs" data-id="logs"><span>??????</span></li>
		<?php if($sql['display_extend'] != 'false'){
?><li class="tg" data-id="tgzq"><span>??????</span></li><?php }
?>
		<!--<li class="fresh" data-id="reload"><span>??????</span></li>-->
		<li class="cz" data-id="cz"><a href="/Templates/user/"><span>??????</span></a></li>
	</ul>
</div>
<div id="frameRIGHTH">
	<div class="nav_banner">
		<ul class="lottery">
		<?php if($sql['display_game'] != 'false'){
?>
			<li class="home" data-id="lottery">
				<span> <i class="iconfont">???</i>??????:<?php echo formatgame($game);
?></span>
			</li>
		<?php }
?>
			<li class="dh" data-id="donghua"><span>??????</span></li>
			<li class="wz" data-id="wenzi"><span>??????</span></li>
			<?php if($sql['display_plan'] != 'false'){
?><!--<li class="cl" data-id="changlong"><span>??????</span></li>--><?php }
?>
			<li class="gz" data-id="guize"><span>??????</span></li>
			<li class="sx" data-id="reload2"><span>????????????</span></li>
		</ul>
		<ul class="uinfo">
			<li class="uname">??????:<?php echo $_SESSION['username'];
?></li>
			<li class="money">????????????: <b class="balance">0</b></li>
			<li class="oline">????????????: <b class="online">0</b>???</li>
		</ul>
	</div>
	<div class="touzu rbox">
	<div class="user_messages">
			<div>
				<input placeholder="????????????/??????" type="text" id="Message">
				<img src="/Style/Xs/Public/images/kb.png" class="keybord gray">
				<span class="sendemaill">??? ???</span>
			</div>
			<span class="txtbet">????????????</span>
			<div class="keybord_div" id="keybord_div">
				<em>0</em>
				<em>1</em>
				<em>2</em>
				<em>3</em>
				<em>4</em>
				<em>5</em>
				<em>6</em>
				<em>7</em>
				<em>8</em>
				<em>9</em>
				<em>???</em>
				<em>???</em>
				<em>???</em>
				<em>???</em>
				<em>??????</em>
				<em>??????</em>
				<em>??????</em>
				<em>??????</em>
				<em>??????</em>
				<em>/</em>
				<em>-</em>
				<em>.</em>
				<em>???</em>
				<em>???</em>
				<em class="c2">??????</em>
				<em class="c">???</em>
				<em class="c">???</em>
				<em class="close">??</em>
			</div>
																																																																																			</div>
		</div>
	<div class="game-box" style="display:none">
		<div class="game-hd">
			<div class="menu">
				<ul>
					<li class="gameli"><a href="javascript:;" class="on" data-t="1">?????????</a></li>
					<li class="gameli"><a href="javascript:;" data-t="2">?????????</a></li>
					<li class="gameli"><a href="javascript:;" data-t="3">?????????</a></li>
					<li class="gameli"><a href="javascript:;" data-t="4">?????????</a></li>
					</li>
				</ul>
			</div>
			<!--<h4 id="game-gtype">???????????????</h4>-->
			<div class="infuse">
				<a href="javascript:;" class="clearnum">????????????</a>
				<em id="bet_num">???0???</em>
				<a href="javascript:;" class="confirm-pour">????????????</a>
			</div>
		</div>
		<div class="game-bd">
			<!--???????????????-->
			<div class="gamenum game-type-1">
				<div class="rank-tit"><span class="change">?????????</span></div>
				<div class="btn-box btn-grounp">
					<a href="javascript:;" class="btn mini-btn" data-val="0">
						<div class="h5">
							<h5>0</h5>
							<p><em>?? <?php echo $info['0027'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="1">
						<div class="h5">
							<h5>1</h5>
							<p><em>?? <?php echo $info['0126'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="2">
						<div class="h5">
							<h5>2</h5>
							<p><em>?? <?php echo $info['0225'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="3">
						<div class="h5">
							<h5>3</h5>
							<p><em>?? <?php echo $info['0324'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="4">
						<div class="h5">
							<h5>4</h5>
							<p><em>?? <?php echo $info['0423'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="5">
						<div class="h5">
							<h5>5</h5>
							<p><em>?? <?php echo $info['0522'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="6">
						<div class="h5">
							<h5>6</h5>
							<p><em>?? <?php echo $info['0621'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="7">
						<div class="h5">
							<h5>7</h5>
							<p><em>?? <?php echo $info['0720'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="8">
						<div class="h5">
							<h5>8</h5>
							<p><em>?? <?php echo $info['891819'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="9">
						<div class="h5">
							<h5>9</h5>
							<p><em>?? <?php echo $info['891819'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="10">
						<div class="h5">
							<h5>10</h5>
							<p><em>?? <?php echo $info['10111617'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="11">
						<div class="h5">
							<h5>11</h5>
							<p><em>?? <?php echo $info['10111617'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="12">
						<div class="h5">
							<h5>12</h5>
							<p><em>?? <?php echo $info['1215'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="13">
						<div class="h5">
							<h5>13</h5>
							<p><em>?? <?php echo $info['1314'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="14">
						<div class="h5">
							<h5>14</h5>
							<p><em>?? <?php echo $info['1314'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="15">
						<div class="h5">
							<h5>15</h5>
							<p><em>?? <?php echo $info['1215'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="16">
						<div class="h5">
							<h5>16</h5>
							<p><em>?? <?php echo $info['10111617'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="17">
						<div class="h5">
							<h5>17</h5>
							<p><em>?? <?php echo $info['10111617'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="18">
						<div class="h5">
							<h5>18</h5>
							<p><em>?? <?php echo $info['891819'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="19">
						<div class="h5">
							<h5>19</h5>
							<p><em>?? <?php echo $info['891819'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="20">
						<div class="h5">
							<h5>20</h5>
							<p><em>?? <?php echo $info['0720'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="21">
						<div class="h5">
							<h5>21</h5>
							<p><em>?? <?php echo $info['0621'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="22">
						<div class="h5">
							<h5>22</h5>
							<p><em>?? <?php echo $info['0522'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="23">
						<div class="h5">
							<h5>23</h5>
							<p><em>?? <?php echo $info['0423'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="24">
						<div class="h5">
							<h5>24</h5>
							<p><em>?? <?php echo $info['0324'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="25">
						<div class="h5">
							<h5>25</h5>
							<p><em>?? <?php echo $info['0225'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="26">
						<div class="h5">
							<h5>26</h5>
							<p><em>?? <?php echo $info['0126'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn mini-btn" data-val="27">
						<div class="h5">
							<h5>27</h5>
							<p><em>?? <?php echo $info['0027'];
?></em></p>
						</div>
					</a>
				</div>
			</div>
			<!--???????????????-->
			<div class="gamenum game-type-2">
				<div class="rank-tit"><span class="change"></span></div>
				<div class="btn-box btn-grounp">
					<a href="javascript:;" class="btn middle-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['jida'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn middle-btn" data-val="???">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['jixiao'];
?></em></p>
						</div>
					</a>
				</div>
			</div>
			<div class="gamenum game-type-3">
				<div class="rank-tit"><span class="change"></span></div>
				<div class="btn-box btn-grounp">
					<a href="javascript:;" class="btn middle-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['dadan'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn middle-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['xiaodan'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn middle-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['dashuang'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn middle-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['xiaoshuang'];
?></em></p>
						</div>
					</a>
				</div>
			</div>
			<div class="gamenum game-type-4">
				<div class="rank-tit"><span class="change"></span></div>
				<div class="btn-box btn-grounp">
					<a href="javascript:;" class="btn large-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['baozi'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn large-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['duizi'];
?></em></p>
						</div>
					</a>
					<a href="javascript:;" class="btn large-btn" data-val="??????">
						<div class="h5">
							<h5>??????</h5>
							<p><em>?? <?php echo $info['shunzi'];
?></em></p>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div id="touzhu" class="">
		<div class="pour-info">
			<h4 class="game-tit game-tit-bg" style="font-size:45px;line-height:100px;">??????????????????<a href="javascript:;" class="close">??</a></h4>
			<div class="m-bd">
				<h4>???<em class="bet_n">1</em>??????????????????<em class="bet_total">0</em>???</h4>
				<dl>
					<dt>
						<span>???????????????</span>
						<input type="number" class="text text-right bet_money" placeholder="????????????">
						<a href="javascript:;" class="money_clear">??????</a>
					</dt>
					<dd>
						<i class="m5" data-money="5"></i>
						<i class="m10" data-money="10"></i>
						<i class="m50" data-money="50"></i>
						<i class="m100" data-money="100"></i>
						<i class="m500" data-money="500"></i>
						<i class="m1000" data-money="1000"></i>
						<i class="m5000" data-money="5000"></i>
					</dd>
				</dl>
				<div class="sub-btn">
					<a href="javascript:;" class="cancel">????????????</a>
					<a href="javascript:;" class="confirm">????????????</a>
				</div>
			</div>
		</div>
	</div>
		<div class="rightdiv">
			<!--div class="saidright">
				<img src="/Public/images/gm.jpg">
				<div class="tousaidl">
					<span class="tousaid2">13:21:50</span>&nbsp;&nbsp;
					<span class="tousaid1">??????GM</span>
				</div>
				<div class="ts"> 
					<b style="border-color:transparent  transparent transparent #FFBBBB;"></b>
					<span class="neirongsaidl" style="background-color: #FFBBBB;">????????????<br>??????:632246<br>????????????????????????????????????</span>
				</div>
			</div>
			<div class="saidright">
				<img src="/Public/images/gm.jpg">
				<div class="tousaidl">
					<span class="tousaid2">13:21:50</span>&nbsp;&nbsp;
					<span class="tousaid1">??????GM</span>
				</div>
				<div class="ts"> 
					<b style="border-color:transparent  transparent transparent #98E165;"></b>
					<span class="neirongsaidl" style="background-color:#98E165;max-width: 100%">????????????<br>??????:632246<br>????????????????????????????????????</span>
				</div>
			</div>
			<div class="saidright">
				<img src="/Public/images/gm.jpg">
				<div class="tousaidl">
					<span class="tousaid2">13:21:50</span>&nbsp;&nbsp;
					<span class="tousaid1">??????GM</span>
				</div>
				<div class="ts"> 
					<b style=""></b>
					<span class="neirongsaidl" style="">????????????<br>??????:632246<br>????????????????????????????????????</span>
				</div>
			</div>
			<div class="saidleft">
				<img src="/Public/images/gm.jpg">
				<div class="tousaid">
					<span class="tousaid2">13:21:50</span>&nbsp;&nbsp;
					<span class="tousaid1">??????GM</span>
				</div>
				<div class="tsf"> 
					<b></b>
					<span class="neirongsaid" style="">????????????<br>??????:632246<br>????????????????????????????????????</span>
				</div>
			</div-->
		</div>
	</div>
	<!--div class="kefu rbox" style="display:none">
		<div class="user_messages">
			<input type="text" id="kfs"><span id="sendkf">??? ???</span>
		</div>
		<div class="kfcs">
			<div class="saidright">
				<img src="/Public/images/kefu2.jpg">
				<div class="tousaidl">
					<span class="tousaid2">16:22:17</span>&nbsp;&nbsp;<span class="tousaid1">??????</span>
				</div>
				<div class="ts"> 
					<b></b>
					<span class="neirongsaidl">?????????????????????????????????????????????????????????</span>
				</div>
			</div>
		</div>
	</div-->
	<div id="ss_menu" style="">	
		<div class="ss_nav">
			<i class="iconfont close" data-id="#ss_menu">???</i>
			<?php if($sql['display_game'] != 'false'){
?>
			<ul class="lottery">
				<li <?php if($pk10open == 'false')echo 'class="gray"';
?>>
					<a <?php if($pk10open == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=pk10'";
}
?>>
						<img src="/Style/Home/images/pk10-logo.png" title="????????????">
						<font>????????????</font>
					</a>
				</li>
				<li <?php if($xyftopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($xyftopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=xyft'";
}
?>>
						<img src="/Style/Home/images/xyft-logo.png" title="????????????">
						<font>????????????</font>
					</a>
				</li>
				<li <?php if($cqsscopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($cqsscopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=cqssc'";
}
?>>
						<img src="/Style/Home/images/cqssc-logo.png" title="???????????????">
						<font>???????????????</font>
					</a>
				</li>
				<li <?php if($xy28open == 'false')echo 'class="gray"';
?>>
					<a <?php if($xy28open == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=xy28'";
}
?>>
						<img src="/Style/Home/images/xy28-logo.png" title="??????28">
						<font>??????28</font>
					</a>
				</li>
				<li <?php if($jnd28open == 'false')echo 'class="gray"';
?>>
					<a <?php if($jnd28open == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=jnd28'";
}
?>>
						<img src="/Style/Home/images/jnd28-logo.png" title="?????????28">
						<font>?????????28</font>
					</a>
				</li>
				<li <?php if($jsmtopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($jsmtopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=jsmt'";
}
?>>
						<img src="/Style/Home/images/jsmt-logo.png" title="????????????">
						<font>????????????</font>
					</a>
				</li>
				<li <?php if($jsscopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($jsscopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=jssc'";
}
?>>
						<img src="/Style/Home/images/jssc-logo.png" title="????????????">
						<font>????????????</font>
					</a>
				</li>	
				<li <?php if($jssscopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($jssscopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=jsssc'";
}
?>>
						<img src="/Style/Home/images/jsssc-logo.png" title="???????????????">
						<font>???????????????</font>
					</a>
				</li>
				<li <?php if($bjlopen == 'false')echo 'class="gray"';
?>>
					<a <?php if($jssscopen == 'false'){
    echo 'href="#" class="gray"';
}else{
    echo "href='/qr2.php?room={$_SESSION['roomid']}&g=bjl'";
}
?>>
						<img src="/Style/Home/images/bjl-logo.png" title="???????????????">
						<font>?????????</font>
					</a>
				</li>
			</ul>
			<?php }
?>
			<ul class="menu" style="">
				<h3 class="tit">???????????????</h3>
				<!--li>
					<a href="/">
						<i class="iconfont">???</i> 
						<font>????????????</font>
					</a>
				</li-->
				<li>
					<a href="/Templates/user/">
						<i class="iconfont">???</i>
						<font>????????????</font>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<iframe width="880" height="0" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" id="iframe" class="iframe" style="display:none" onload="iFrameHeight();"/>
</div>

<div class="zytips"><div>???????????????..</div></div>
</body>
</html>