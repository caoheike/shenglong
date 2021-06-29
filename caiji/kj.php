<?
// $api = 'http://www-k123456.com/index.php?c=api&a=updateinfo&cp=bjpk10&uptime=1488049275&chtime=33005&catid=2&modelid=9';
$api='http://api.b6api.com/api?p=json&t=jisupk10&limit=5&token=EBCD5037AE185617';

$resource = file_get_contents( $api );



	$jsondata = json_decode($resource,true);
	$jsondata = $jsondata['data'];
 $ct = $jsondata[0]['expect'];



$cd = $jsondata[0]['opentime'];

$cr = $jsondata[0]['opencode'];

$json = array();
$json['expect'] = $ct;
$json['opencode'] = $cr;
$json['opentime'] = $cd;

//var_dump($json);
echo '"open":'.'['.json_encode($json).']';
// header('Content-Type: text/xml;charset=utf8');
// $limit=strlen($ct)-2;

// $ct=substr($ct,0,$limit).''.substr($ct,$limit,$limit+2);
// //print_r($data);

// echo '<xml>
// <row expect="'.$ct.'" opencode="'.$cr.'" opentime="'.str_replace('/','-',$cd).'"/>
// </xml>';