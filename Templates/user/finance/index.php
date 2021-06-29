<?php
require_once("global.php");
$sql="select * from `fn_user` where `roomid`={roomid} and `userid`='{userid}'";
$sql=str_replace(array('{roomid}','{userid}'),array($_SESSION['roomid'],$_SESSION['userid']),$sql);
$info = $m->getOne( $sql, '', false); //$rs返回查询到的结果 没有结果则返回false
if(!$info)die('not userinfo exists');
$do = !empty($_GET['do'])?$_GET['do']:'index';
switch($do){
	case 'upload':
	    $r = upload();
		echo json_encode($r);
		break;
	case 'submit':
		if(isAjax()){
			$rsult=array();
			$result['status'] = 0;
			$data = $_POST;
			if(!preg_match("/^1[34578]\d{9}$/", $data['mobile'])){
				$result['msg'] = '[10001]手机号码格式不对';
				echo json_encode($result);
				return false;
			}
			if(empty($data['qrcode_url'])){
				$result['msg'] = '[10002]请上传转帐二维码';
				echo json_encode($result);
				return false;
			}
			$money = (float)($data['money']);
			if(empty($money) || $money>$info['money']){
				$result['msg'] = '[10002]提现金额错误';
				echo json_encode($result);
				return false;
			}
			//开始提现
			$sql = "update `fn_user` set money = money - {$money} where userid = '{$data['userid']}' and money >= $money";
			//减少金额
			if($m->runSql($sql)){

				$_time = date('Y-m-d H:i:s',time());
				$time = time();
				$sql="INSERT INTO `fn_finance` (`userid`, `username`, `mobile`, `money`, `money_type`,`qrcode_url`,`create_time`,`roomid`)values('{$info['userid']}','{$info['username']}','{$data['mobile']}',{$money},{$data['money_type']},'{$data['qrcode_url']}',{$time},{$_SESSION['roomid']})";
             	if($m->runSql($sql)){
             		$result['status'] = 1;
             		$result['msg'] = '提现操作成功，预计5分钟内到帐!';
					echo json_encode($result);
					return true;
             	}
			}else{
				$result['msg'] = '[10004]提现信息不完整';
				echo json_encode($result);
				return false;
			}
		}else{
			header('Location: index.php');
			die('not params exists');
		}
		break;
	default:
		include THEME_PATH.'index.html';
		break;
	
}

function upload($config = array()){
		$data = array();
        $config['DIR_NAME'] = date('Y-m-d');

        $path =  'finance/' . $config['DIR_NAME'] . '/';
        //上传
        $upload = new UploadFile();
        $upload->savePath = UPFILE_PATH. $path;
        $upload->allowExts = array('jpg','gif','png');
        $upload->maxSize = 10*1024*1024;
        $upload->saveRule = 'md5_file';
        if (!$upload->upload()) {
        	$data['status'] = 0;
            $data['info'] = $upload->getErrorMsg();
            return $data;
        }
        //上传信息
        $info = $upload->getUploadFileInfo();
        $info = current($info);
        //设置基本信息
        $file = $path . $info['savename'];
        $fileUrl = './upload/' . $file;
        $filePath = pathinfo($info['savename']);
        $fileName = $filePath['filename'];
        $fileTitle = pathinfo($info['name']);
        $fileTitle = $fileTitle['filename'];
        $fileExt = $info['extension'];
        //设置保存文件名(针对图片有效)
        if($config['SAVE_EXT']){
            $saveName = $fileName. '.' . $config['SAVE_EXT'];
        }else{
            $saveName = $info['savename'];
        }
       
        //录入文件信息
        $data['status'] = 1;
        $data['url'] = './upload/' . $file;
        $data['original'] = $fileUrl;
        $data['title'] = $fileTitle;
        $data['ext'] = $fileExt;
        $data['size'] = $info['size'];
        $data['time'] = time();
        return $data;
    }
?>