<?php
include dirname(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__))))) . "/Public/config.php";

define('UPFILE_PATH',dirname(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)))))."/upload/");
define('ROOT_PATH',dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)));
define('DB_HOST', $db['host']); //数据库服务器地址
define('DB_USER', $db['user']);  //数据库用户名
define('DB_PWD',  $db['pass']);//数据库密码
define('DB_NAME', $db['name']);  //数据库名称
define('DB_PORT', '3306');  //数据库端口

define('DB_AUTOCOMMIT', false);  //默认false使用事物回滚 不自动提交只对InnoDB有效。
define('DB_ENCODE', 'utf8');  //数据库编码

define('THEME_PATH','template/');

include ROOT_PATH."/lib/UploadFile.php";

require_once("includes/MysqliDb.class.php");//导入mysqli连接
require_once("includes/M.class.php");//导入mysqli操作类
$m=new M(); //创建连接数据库类

/**
 * 是否是AJAx提交的
 * @return bool
 */
function isAjax(){
  if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    return true;
  }else{
    return false;
  }
}
/**
 * 是否是GET提交的
 */
function isGet(){
  return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}
/**
 * 是否是POST提交
 * @return int
 */
function isPost() {
  return ($_SERVER['REQUEST_METHOD'] == 'POST'  && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
}