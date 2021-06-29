<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>开奖动画</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>

    <script src="js/jquery.min.js"></script>
	<style type="text/css">
	
	html,body{width:100%;height:100%;background:url(http://1683580.com/js/lib/video/pcEgg_video/img/pcEgg_bg.png) top center repeat-x #291a75;background-size:980px 450px;margin: 0;padding: 0;font-size:48px;overflow: hidden;}
.hm{padding:5px 10px;background: #43a9aa;font-size:14px;border-radius:15px;color: #fff;}

.hz{padding:5px 10px;background: #ff0000;font-size:14px;border-radius:15px;color: #fff;}




.haoma_gun{
	animation:myfirst 100s;
-moz-animation:myfirst 100s; /* Firefox */
-webkit-animation:myfirst 100s; /* Safari and Chrome */
-o-animation:myfirst 100s; /* Opera */
}


@keyframes myfirst
{
from {transform:rotate(200000deg);
-ms-transform:rotate(200000deg); /* Internet Explorer */
-moz-transform:rotate(200000deg); /* Firefox */
-webkit-transform:rotate(200000deg); /* Safari 和 Chrome */
-o-transform:rotate(200000deg); /* Opera */}
to {transform:rotate(0deg);
-ms-transform:rotate(0deg); /* Internet Explorer */
-moz-transform:rotate(0deg); /* Firefox */
-webkit-transform:rotate(0deg); /* Safari 和 Chrome */
-o-transform:rotate(0deg); /* Opera */}
}

@-moz-keyframes myfirst /* Firefox */
{
from {transform:rotate(200000deg);
-ms-transform:rotate(200000deg); /* Internet Explorer */
-moz-transform:rotate(200000deg); /* Firefox */
-webkit-transform:rotate(200000deg); /* Safari 和 Chrome */
-o-transform:rotate(200000deg); /* Opera */}
to {transform:rotate(0deg);
-ms-transform:rotate(0deg); /* Internet Explorer */
-moz-transform:rotate(0deg); /* Firefox */
-webkit-transform:rotate(0deg); /* Safari 和 Chrome */
-o-transform:rotate(0deg); /* Opera */}
}

@-webkit-keyframes myfirst /* Safari 和 Chrome */
{
from {transform:rotate(200000deg);
-ms-transform:rotate(200000deg); /* Internet Explorer */
-moz-transform:rotate(200000deg); /* Firefox */
-webkit-transform:rotate(200000deg); /* Safari 和 Chrome */
-o-transform:rotate(200000deg); /* Opera */}
to {transform:rotate(0deg);
-ms-transform:rotate(0deg); /* Internet Explorer */
-moz-transform:rotate(0deg); /* Firefox */
-webkit-transform:rotate(0deg); /* Safari 和 Chrome */
-o-transform:rotate(0deg); /* Opera */}
}

@-o-keyframes myfirst /* Opera */
{
from {transform:rotate(200000deg);
-ms-transform:rotate(200000deg); /* Internet Explorer */
-moz-transform:rotate(200000deg); /* Firefox */
-webkit-transform:rotate(200000deg); /* Safari 和 Chrome */
-o-transform:rotate(200000deg); /* Opera */}
to {transform:rotate(0deg);
-ms-transform:rotate(0deg); /* Internet Explorer */
-moz-transform:rotate(0deg); /* Firefox */
-webkit-transform:rotate(0deg); /* Safari 和 Chrome */
-o-transform:rotate(0deg); /* Opera */}
}





	</style>
</head>
<body>
	
	<br>
	<div style="width:100%;height:50px;line-height:50px;text-align:center;color:#fff">加拿大28第 <b><span id="dangqi">--</span></b> 期开奖</div>
	
	
	<div style="width:150px;height:150px;line-height:150px;text-align:center;color:#fff;background:#29768c;border:2px solid #fff;border-radius:77px;position:absolute;top:150px;left:50%;margin-left:-275px;font-size:82px" id="h1" class="haoma">?</div>
	
	
	<div style="width:150px;height:150px;line-height:150px;text-align:center;color:#fff;background:#29768c;border:2px solid #fff;border-radius:77px;position:absolute;top:150px;left:50%;margin-left:-75px;font-size:82px" id="h2" class="haoma">?</div>
	
	
	<div style="width:150px;height:150px;line-height:150px;text-align:center;color:#fff;background:#29768c;border:2px solid #fff;border-radius:77px;position:absolute;top:150px;left:50%;margin-left:125px;font-size:82px" id="h3" class="haoma">?</div>
	
	
	<div style="width:100%;height:30px;line-height:30px;background:rgba(0,0,0,0.7);position:fixed;bottom:80px;text-align:center;color:#fff;font-size:24px">第 <b><span id="xiaqi">--</span></b> 期开奖时间：<span id="xiaqishijian">--</span></div>
	<div style="width:100%;height:80px;line-height:80px;background:rgba(0,0,0,0.7);position:fixed;bottom:0;text-align:center;color:#fff">开奖倒计时：<span id="daojishi">--:--</span></div>
	

	<script>
		load();
		
		
		
		function load(){

			 $.ajax({
                    url: "api.php?way=jnd28",
                    type: "GET",
  
                  
                    success: function (data) {
						
					
					
					
						if(data.code==200){
							
							var unixTimestamp = new Date( data.xiaqishijian*1000 ) ;
commonTime = unixTimestamp.toLocaleString();


$('#xiaqishijian').text(commonTime);
						$('#dangqi').text(data.dangqi);
						$('#h1').text(data.diyiwei);
						$('#h2').text(data.dierwei);
						$('#h3').text(data.disanwei);
						$('#xiaqi').text(data.xiaqi);
	var kaijiang = setInterval(GetRTime,1000); 

	var nowtimes=data.n_time;
var khdsj = new Date();//客户端当前时间
var chazhi =(nowtimes+'000') - khdsj.getTime();//时间差

function GetRTime(){
var NowTime = new Date();//当前时间
var t =data.xiaqishijian+'000' - (NowTime.getTime()+chazhi);


//var d=Math.floor(t/1000/60/60/24);//天
var h=Math.floor(t/1000/60/60%24);//时
var m=Math.floor(t/1000/60%60);//分
var s=Math.floor(t/1000%60);//秒
if(parseInt(h)<10){
h="0"+h;
}
if(parseInt(m)<10){
m="0"+m;
}
if(parseInt(s)<10){
s="0"+s;
}

if(m<="00" && s<="00"){
	
	$('#dangqi').text(data.xiaqi);
	$('#h1').text('?');
			$('#h2').text('?');
			$('#h3').text('?');
gundong();
var kk = setInterval("kk()",1000); 
clearInterval(kaijiang);


return;
}

if(h>0){h=h+':';}
else{h='';}
$("#daojishi").text(h+m + ":"+s );


}


                    }
					},
                    error: function () {
                  
                    }
                });
		}
		
		
		
		

		function kk(){
			
			
			
			$.ajax({
                    url: "api.php?way=jnd28",
                    type: "GET",
  
                  
                    success: function (data) {
						
					
					
					
						if(data.code==200){
							
							
if(data.dangqi==$('#dangqi').text()){
	tingzhigundong();
	clearInterval(kk);
	load();
}


						}
					},
                    error: function () {
                clearInterval(kk);
	load();
                    }
                });
			
		}
		
		
		function gundong(){
			
		
			$('.haoma').attr('class','haoma_gun')
			
			
			
		}
		function tingzhigundong(){
			
		
			$('.haoma_gun').attr('class','haoma')
			
			
			
		}
		

	</script>
	
</body>


</html>