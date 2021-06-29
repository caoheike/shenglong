<?php include_once(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/config.php");
//session_start();
$info = "http://" . $_SERVER["HTTP_HOST"] ."/qr2.php?room=" . $_SESSION["roomid"] . "&agent=" . $_SESSION["userid"];
$info1 = "http://" . $_SERVER["HTTP_HOST"] . "%2Fqr2.php%3Froom%3D" . $_SESSION["roomid"] . "%26agent%3D" . $_SESSION["userid"];
$html = file_get_contents("https://cli.im/api/qrcode/code?text=" . $info1 . "&mhid=13128053083");
$qrcode = getSubstr($html, "<img src=\"", "\" id=");
print($info1);

//print($qrcode);



echo "<!DOCTYPE html>\n<html>\n\n<head>\n\t<title>代理通道</title>\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;\">\n    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n    <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">\n    <meta name=\"format-detection\" content=\"telephone=no\">\n    <link href=\"/Style/New/css/activity-style.css\" rel=\"stylesheet\" type=\"text/css\">\n\t<script src=\"/Style/Old/js/kefu.js\"></script>\n<style>\nbody {\n\tfont-family: Arial, Helvetica, sans-serif;\n\tbackground: #000 url(/Style/Xs/Public/images/bg.png);\n}\n</style>\n</head>\n\n<body>\n        <div class=\"main\">\n                <div id=\"outercont\">\n                    <div id=\"outer-cont\">\n                         <div id=\"outer\"><img src=\"";
echo $qrcode;
echo "\" width=\"200\"/><br><center><a href=\"";
$longurl = "http://api.t.sina.com.cn/short_url/shorten.json?source=3271760578&url_long=" . $info1;
$r = file_get_contents($longurl);
$items = json_decode($r);
$surl;
foreach ($items as $item) {
	$surl = $item->url_short;
}
echo $surl;
echo "\" style=\"color:red;text-decoration:none;\"><p><strong>\n                                    ";
$longurl = "http://api.t.sina.com.cn/short_url/shorten.json?source=3271760578&url_long=" . $info1;
$r = file_get_contents($longurl);
$items = json_decode($r);
$surl;
foreach ($items as $item) {
	$surl = $item->url_short;
}
echo $surl;
echo "                    </strong></p></a></center></div>\n                    </div>\n                </div>\n                <div class=\"content\">\n                    <div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">转发小提示：</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n                                <p>方法1：长按二维码，保存到手机发送给朋友！</p>\n                                <p>方法2：长按短链接选择拷贝然后发送给朋友！</p>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">代理小提示：</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n\t\t\t\t\t\t\t\t<p>提示1：每个代理的二维码与短链接都是专属！</p>\n\t\t\t\t\t\t\t\t<p>提示2：通过两种方式进来的用户都是您下线！</p>\n\t\t\t\t\t\t\t\t<p>提示3：如果您想成为代理请加客服微信咨询！</p>\n\n                            </div>\n                        </div>\n                    </div>\n\t\t\t\t\t<div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">源码客户专属福利：</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n\t\t\t\t\t\t\t\t<p>好消息，崇兴娱乐客户将免费获得专属的二维码效果设计！</p>\n\n\n                            </div>\n                        </div>\n                    </div>\n                </div>\n        </div>\n\n\n\n</body>\n\n</html>\n\n\n<!-- 以下是统计及其他信息，与演示无关，不必理会 -->\n</div>";
function getSubstr($str, $leftStr, $rightStr)
{
	$left = strpos($str, $leftStr);
	$right = strpos($str, $rightStr, $left);
	if ($left < 0 || $right < $left) {
		return "";
	}
	return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}