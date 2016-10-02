<?php
namespace Controller;
header('content-type:text/html;charset=utf8');
//用use导入
use \TzlPHP\TzlController;
class AdminController extends TzlController
{
	public function __construct()
	{	//如果一个函数的父类有构造函数 那么我们先得让它爸也出来
		//判断一下 它的父类有没有构造函数 执行 method_exists函数
		if(method_exists('\TzlPHP\TzlController','__construct'))
		{
			parent::__construct();
		}		
		//这里执行判断 要是没有登录 就跳转到登录页面
		//
		if(!isset($_SESSION['id']))
		{
			//先把当前访问的地址存到session  中
			$_SESSION['returnUrl']=$_SERVER['REQUEST_URI'];
			$this->error('必须先登录','index.php?c=Login&a=login');
		}
	}


}