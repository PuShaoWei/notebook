<?php
namespace TzlPHP; 
class Url
{
	// u('lst') :指当前控制器的LST
	// U('login/login ')
	public static function U($url)
	{	//首先把url地址转换成数组
		$url = explode('?', $url);
		$path = $url[0];
		if(isset($url[1]))
			$param = '&'.$url[1];
		else
			$param = '';
		// 判断URL里面有没有/
		if(FALSE !== strpos($path, '/'))
		{
			$_url = explode('/', $path);
			$c = $_url[0];
			$a = $_url[1];
		}
		else
		{	
			$c = $_GET['c'];
			$a = $path;
		}
	return	"/index.php?c=$c&a=$a$param";
	
	}
}

