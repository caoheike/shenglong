<?php
/* 
 * 多线程采集 需要环境支持
一、下载pthreads扩展
下载地址：http://windows.php.net/downloads/pecl/releases/pthreads
根据环境下载，如：php_pthreads-2.0.10-5.5-ts-vc11-x86.zip。
二、安装pthreads扩展
复制php_pthreads.dll 到目录 bin\php\ext\ 下面,(本人路径D:\wamp\bin\php\php5.5.12\ext)。
复制pthreadVC2.dll 到目录 bin\php\ 下面,(本人路径D:\wamp\bin\php\php5.5.12)。
复制pthreadVC2.dll 到目录 C:\windows\system32 下面。
打开php配置文件php.ini,在后面加上extension=php_pthreads.dll
提示:Windows系统需要将 pthreadVC2.dll 所在路径加入到 PATH 环境变量中。pthreadVC2.dll的完整路，如C:\WINDOWS\system32\pthreadVC2.dll。
 * */
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include_once "../Public/config.php";
include_once "lib/Http.php";

class Lottery extends Thread {
    public $url;
    public $result;
    public function __construct($url) {
        $this->url = $url;
    }
    
    public function run() {
		$headers = array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
		);
        if ($this->url) {
            $this->result = Http::curlGet($this->url,5,$headers);
        }
    }
	public function getResult(){
		return $this->result;
	}
}

$gateway = 'https://www.pk103.com/api/newest?code={code}&t='.time();


//URL地址
$urls = array();
$urls['pk10'] = str_replace('{code}','pk10',$gateway);
$urls['xyft'] = str_replace('{code}','xyft',$gateway);
$urls['cq_ssc'] = str_replace('{code}','cq_ssc',$gateway);
$urls['xy28'] = str_replace('{code}','xy28',$gateway);
$urls['jnd28'] = str_replace('{code}','jnd28',$gateway);
$urls['speed10'] = str_replace('{code}','speed10',$gateway);
$urls['speed5'] = str_replace('{code}','speed5',$gateway);
$urls['js_k3'] = str_replace('{code}','js_k3',$gateway);

//创建线程
$t = microtime(true);

foreach ($urls as $k=>$v) {
    $workers[$k] = new Lottery($v);
    $workers[$k]->start();
}
foreach ($workers as $key=>$worker) {
    while($workers[$key]->isRunning()) {
        usleep(100);  
    }
    if ($workers[$key]->join()) {
        $_result[$key] = $workers[$key]->result;
    }
}
$e = microtime(true);

//组装数据
$data = $temp = array();
$data['rows'] = count($_result);
$data['now'] = date('Y-m-d H:i:s',time());

foreach ($_result as $k => $v) {
	$_temp = array();$_v = '';
	$_v = json_decode($v,TRUE);
	$_v = $_v['data'];
	$_temp['code'] = $k;	
	$_temp['open_phase'] = $_v['newest']['issue'];	
	$_temp['open_index'] = $_v['newest']['term'];	
	$_temp['open_result'] = $_v['newest']['code'];	
	$_temp['open_time'] = $_v['newest']['time'];	
	$_temp['load_time'] = date('Y-m-d H:i:s',(strtotime($_temp['open_time'])+$_v['interval']));	
	$_temp['next_phase'] = $_v['current'];	
	$_temp['next_index'] = $_v['currentNo'];	
	$_temp['next_time'] = date('Y-m-d H:i:s',(strtotime($_temp['open_time'])+$_v['interval']));	
	$_temp['now']= date('Y-m-d H:i:s',time());
	array_push($temp,$_temp);

}
$data['data'] = $temp;
$data['runtime'] = ($e-$t);
header('Content-type:text/json');
echo json_encode($data);