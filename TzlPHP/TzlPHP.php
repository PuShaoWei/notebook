<?php
//这个类用作启动文件
namespace TzlPHP;
class TzlPHP 
{
	public static  $C;
	public static  function start()
	 {
 		session_start();
		error_reporting(E_ALL);
	 	//加载配置文件并保存到这个类中，如果以后要使用某一项也可以这样用
	 	// \TzlPHP::$C['DB_HOST'];←如果下次项目中要用到配置文件的话
	 	self::$C = include('./config.php');
		//注册自动加载类
		/**记得测试一下不调用命名空间是否可以**/
		spl_autoload_register('\TzlPHP\TzlPHP::load');

	 	/*******************在所有控制器启动之前启动数据过滤系统**************/
	 	self::_removeXSS();

	 	//解析C和A变量，取出控制器和方法并调用
	 	$c=isset($_GET['c'])?$_GET['c']:\TzlPHP\TzlPHP::$C['DEFAULT_C'];//Index
	 	$a=isset($_GET['a'])?$_GET['a']:\TzlPHP\TzlPHP::$C['DEFAULT_A'];//index
	 	/******************↓↓轻微绕↓↓*************************/	 	
	 	//调用相应的控制器和方法 
	 	$controller='\Controller\\'.ucfirst($c).'Controller'; // '\Controller\IndexController';
	 	//生成控制器类对象
	 	$controller= new $controller;  //是这个意思，首先这个变量等于这个controller这个命名空间下IndexController这个类的。
	 	//然后这个变量又等于new 
	 	//这个类，因为在命名空间下嘛，所以前面要加上命名空间的名字
	 	//然后这个变量就等于这个new这个类
	 	//在然后就开始应对它的对象 ，因为在前面中$a 我们知道了是index，
	 	//所以呢，这里是调用Controller\IndexController 这个类下面的index这个方法，所以后面这个控制器就可以打开啦
	 
	 	//调用控制器中的方法	
	 	$controller->$a();	 
	 	/*****************↑↑轻微绕↑↑**************************/
	 }
	 /**************过滤XSS跨站脚本攻击***********/
	 private static  function _removeXSS()
	 {	
	 	//过滤前把所有未过滤的数据保存起来
	 	$GLOBALS['_RAW_POST']=$_POST;
	 	$GLOBALS['_RAW_GET']=$_GET;
	 	$GLOBALS['_RAW_COOKIE']=$_COOKIE;

	 	//循环把$_POST中数据进行过滤
	 	foreach ($_POST as $key => $value) 
	 	{
	 		//过滤完在存回这个值
	 		$_POST[$key]=htmlspecialchars(trim($value));
	 	}	

	 	foreach ($_GET as $key => $value) 
	 	{
	 		//过滤完在存回这个值
	 		$_GET[$key]=htmlspecialchars(trim($value));
	 	}	 	

	 	foreach ($_COOKIE as $key => $value) 
	 	{
	 		//过滤完在存回这个值
	 		$_COOKIE[$key]=htmlspecialchars(trim($value));
	 	}
	 }


	 /**************自动加载***************/
	 public static function load($className)
	 {
	 	include($className.'.php');
	 }
}