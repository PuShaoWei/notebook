<?php
//所有控制器中的父控制器，咱项目所有控制器都要继承他
namespace TzlPHP;
class TzlController
{	
	public function __construct()
	{
		
	}
	//直接跳转
	protected function redirect($url)
	{
		echo "$url";
	}	
	//显示成功后三秒跳转
	public function success($message, $url = '', $seconds = 3)
	{
		include('./View/success.html');
		exit;
	}
	//显示失败后三秒跳转
	protected function error($error,$url='',$seconds=3)
	{

		include('./View/error.html');exit;	
	}	
}

