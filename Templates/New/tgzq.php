<?php include_once(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))) . "/Public/config.php");
//session_start();
$info = "http://" . $_SERVER["HTTP_HOST"] ."/qr2.php?room=" . $_SESSION["roomid"] . "&agent=" . $_SESSION["userid"];
$info1 = "http://" . $_SERVER["HTTP_HOST"] . "%2Fqr2.php%3Froom%3D" . $_SESSION["roomid"] . "%26agent%3D" . $_SESSION["userid"];
$html = file_get_contents("https://cli.im/api/qrcode/code?text=" . $info1 . "&mhid=13128053083");
$qrcode = getSubstr($html, "<img src=\"", "\" id=");
print($info1);

//print($qrcode);



echo "<!DOCTYPE html>\n<html>\n\n<head>\n\t<title>ไปฃ็้้</title>\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;\">\n    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n    <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">\n    <meta name=\"format-detection\" content=\"telephone=no\">\n    <link href=\"/Style/New/css/activity-style.css\" rel=\"stylesheet\" type=\"text/css\">\n\t<script src=\"/Style/Old/js/kefu.js\"></script>\n<style>\nbody {\n\tfont-family: Arial, Helvetica, sans-serif;\n\tbackground: #000 url(/Style/Xs/Public/images/bg.png);\n}\n</style>\n</head>\n\n<body>\n        <div class=\"main\">\n                <div id=\"outercont\">\n                    <div id=\"outer-cont\">\n                         <div id=\"outer\"><img src=\"";
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
echo "                    </strong></p></a></center></div>\n                    </div>\n                </div>\n                <div class=\"content\">\n                    <div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">่ฝฌๅๅฐๆ็คบ๏ผ</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n                                <p>ๆนๆณ1๏ผ้ฟๆไบ็ปด็?๏ผไฟๅญๅฐๆๆบๅ้็ปๆๅ๏ผ</p>\n                                <p>ๆนๆณ2๏ผ้ฟๆ็ญ้พๆฅ้ๆฉๆท่ด็ถๅๅ้็ปๆๅ๏ผ</p>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">ไปฃ็ๅฐๆ็คบ๏ผ</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n\t\t\t\t\t\t\t\t<p>ๆ็คบ1๏ผๆฏไธชไปฃ็็ไบ็ปด็?ไธ็ญ้พๆฅ้ฝๆฏไธๅฑ๏ผ</p>\n\t\t\t\t\t\t\t\t<p>ๆ็คบ2๏ผ้่ฟไธค็งๆนๅผ่ฟๆฅ็็จๆท้ฝๆฏๆจไธ็บฟ๏ผ</p>\n\t\t\t\t\t\t\t\t<p>ๆ็คบ3๏ผๅฆๆๆจๆณๆไธบไปฃ็่ฏทๅ?ๅฎขๆๅพฎไฟกๅจ่ฏข๏ผ</p>\n\n                            </div>\n                        </div>\n                    </div>\n\t\t\t\t\t<div class=\"content\">\n                        <div>\n                            <div style=\"background: #00bfff\">ๆบ็?ๅฎขๆทไธๅฑ็ฆๅฉ๏ผ</div>\n                            <div class=\"Detail\" style=\"background: #ffffff\">\n\t\t\t\t\t\t\t\t<p>ๅฅฝๆถๆฏ๏ผๅดๅดๅจฑไนๅฎขๆทๅฐๅ่ดน่ทๅพไธๅฑ็ไบ็ปด็?ๆๆ่ฎพ่ฎก๏ผ</p>\n\n\n                            </div>\n                        </div>\n                    </div>\n                </div>\n        </div>\n\n\n\n</body>\n\n</html>\n\n\n<!-- ไปฅไธๆฏ็ป่ฎกๅๅถไปไฟกๆฏ๏ผไธๆผ็คบๆ?ๅณ๏ผไธๅฟ็ไผ -->\n</div>";
function getSubstr($str, $leftStr, $rightStr)
{
	$left = strpos($str, $leftStr);
	$right = strpos($str, $rightStr, $left);
	if ($left < 0 || $right < $left) {
		return "";
	}
	return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}