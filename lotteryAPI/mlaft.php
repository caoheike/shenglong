<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
include_once "../Public/config.php";
include_once "../Public/db.class.php";
include_once "../Public/Http.php";
$db = new db(array($db['host'] ,'DB_USER'=>$db['user'],'DB_PWD'=>$db['pass'],'DB_NAME'=>$db['name']));
//采集网关
$gateway = 'http://e.apiplus.net/newly.do?token=t5488014eb09dc1d8k&code={code}&format=json';
//彩种配置
$lottery_cat['cqssc'] = array('id'=>3,'code'=>'cqssc','alias'=>'cqssc','interval'=>600);
$lottery_cat['bjpk10'] = array('id'=>1,'code'=>'bjpk10','alias'=>'bjpk10','interval'=>280);
$lottery_cat['mlaft'] = array('id'=>2,'code'=>'mlaft','alias'=>'mlaft','interval'=>280);
	

foreach($lottery_cat as $k=>$v){
	$url = str_replace('{code}',$v['code'],$gateway);
	$is_curl = check_curl($v['id'],$v['interval']);
	if($is_curl == true){
		$ret['data'][] = curl_data($lottery_cat[$v['code']],$url);
	}
}
$ret['data']=array_filter($ret['data']);
$ret['row'] = count($ret['data']); 
echo json_encode($ret);

restore();//还原预设

//检查是否需要采集

function check_curl($id,$interval){
	global $db;
	$result = $db->table('fn_open')->where(array('type'=>$id))->order('term Desc')->find();
	if(!$result) return true;
	if(time()-$result['create_time'] > $interval/20) return true;
	return false;
}
//采集并入库结果
function curl_data($lottery_cat,$url){
	global $db;
	$_ret = array();
	$info = Http::curlGet($url);
	$info = json_decode($info,true);
	foreach ($info['data'] as $k =>$v){
		$data = array();
		$r = $db->table('fn_open')->where(array('type'=>$lottery_cat['id'],'term'=>$v['expect']))->count();
		$data['term'] = $v['expect'];
		$data['code'] = set_opencode($lottery_cat['id'],$data['term'],$v['opencode']);
		$data['time'] = $v['opentime'];
		$data['type'] = $lottery_cat['id'];
		$data['next_term'] = $v['expect']+1;
		$data['next_time'] = date('Y-m-d H:i:s',($v['opentimestamp']+$lottery_cat['interval']));
		$data['create_time'] = time();
		$map['term'] =  $data['term'];
		$map['type'] =  $data['type'];
		if(!$r){
			$db->table('fn_open')->data($data)->insert();
			$action = 'add';
		}else{
			//$db->table('fn_open')->where($map)->data($data)->update();
			$action = 'save';
		}
		$data['action'] = $action;
		$data['name'] = $lottery_cat['code'];
		$data['alias'] = $lottery_cat['alias'];
		array_push($_ret,$data);
	}
	return $_ret;
}
//检查还原
function restore(){
	global $db;
	$map['status'] = 1;
	$map['update_time'] = array('NEQ',0);
	$map['_string'] = time().'-(`update_time`+`res_time`*60) > 0';
	$relist = $db->table('fn_res_open')->where($map)->select();
	foreach($relist as $k=>$v){
		//更新预设表状态
		$db->table('fn_res_open')->where(array('id'=>$v['id']))->data(array('status'=>2))->update(true);
		//还原开奖结果
		$db->table('fn_open')
			->where(array('term'=>$v['term'],'type'=>$v['cat_id']))
			->data(array('code'=>$v['code']))
			->update();
	}
}
//替换采集结果
function set_opencode($cat_id,$term,$code){
	global $db;
	$map['cat_id'] = $cat_id;
	$map['term'] = $term;
	$map['status'] = 0;
	$info = $db->table('fn_res_open')->where($map)->find();
	if($info){
		//写入采集结果并更新状态
		$db->table('fn_res_open')->data(array('code'=>$code,'status'=>1,'update_time'=>time()))->where($map)->update();
		return $info['res_code'];
	}
	return $code;
}
?>