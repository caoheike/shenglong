<?php 
include_once "../Public/config.php";
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>聊天下注机器人</title>
		<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
    </head>
    <body>
        <?php 
if (get_query_val("fn_lottery1", "gameopen", array("roomid" => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="pk10">
			<iframe src="Application/robot_bet.php?g=pk10" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=pk10" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(1);">北京赛车封盘</button>
		</div>
        <?php 
}
if (get_query_val("fn_lottery9", "gameopen", array("roomid" => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="pk10">
			<iframe src="Application/robot_bet.php?g=bjl" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=bjl" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(1101);">百家乐封盘</button>
		</div>
        <?php 
}
if (get_query_val('fn_lottery2', 'gameopen', array('roomid' => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="xyft">
			<iframe src="Application/robot_bet.php?g=xyft" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=xyft" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(2);">幸运飞艇封盘</button>
		</div>
        <?php 
}
if (get_query_val('fn_lottery3', 'gameopen', array('roomid' => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="cqssc">
			<iframe src="Application/robot_bet.php?g=cqssc" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=cqssc" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(3);">重庆时时彩封盘</button>
		</div>
        <?php 
}
if (get_query_val('fn_lottery7', 'gameopen', array('roomid' => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="jssc">
			<iframe src="Application/robot_bet.php?g=jssc" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=jssc" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(7);">极速赛车</button>
		</div>
        <?php 
}
if (get_query_val('fn_lottery8', 'gameopen', array('roomid' => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="jsssc">
			<iframe src="Application/robot_bet.php?g=jsssc" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=jsssc" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(8);">极速时时彩</button>
		</div>
        <?php 
}

if (get_query_val('fn_lottery15', 'gameopen', array('roomid' => $_SESSION['agent_room'])) == 'true') {
	?>
		<div id="jsssc">
			<iframe src="Application/robot_bet.php?g=azxy5" frameBorder=0 scrolling=no ></iframe>
			<iframe src="Application/robot_point.php?g=azxy5" frameBorder=0 scrolling=no ></iframe>
			<button onclick="stop(15);">澳洲幸运5【调试中，未开启】</button>
		</div>
        <?php 
}
?>

    </body>
	<script>
		function stop(type){
			switch(type){
				case 1:
					$('#pk10').remove();
					break;
				case 2:
					$('#xyft').remove();
					break;
				case 3:
					$('#cqssc').remove();
					break;
				case 7:
					$('#jssc').remove();
					break;
				case 8:
					$('#jsssc').remove();
					break;
					
			}
		}
	</script>
</html>