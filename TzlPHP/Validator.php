<?php
namespace TzlPHP;
use Exception;
class Validator
{
	private static 	$_rule=[
	'require'=>'/^.+$/s',//代表非空字符
	'tel'=> '/^1[358][0-9]{9}$/',
	'name6-18'=>'/^\w{6,18}$/',
	];
/*
	['username','name6-18','用户名不能为空'],
	['tel','tel','手机号码输入不争取'],
	['password','name6-18','密码不正确'],
	['cpassword','password','两次密码不一致','confirm'],
		confirm 代表 的是两个字段是否相等
	['captcha','captcha','验证码输入不正确'],
	]);	
		
*/

public static function check($data,$rule)
{
/*
	$data 传过来的是POST  $_rule 传过来的是预先设定的二维数组
			
$rule=[
		['username','require','用户名不能为空'],

		//用tel对应的正则验证$_POST['tel']

		['tel','tel','手机号码输入不正确'],

		//判断表单中的cpassword和password

		['password','name6-18','密码不正确'],
					
		//验证码验证

		['cpassword','password','两次密码不一致','confirm'],

		['captcha','captcha','验证码输入不正确'],
		]);				

*/	
	foreach ($rule as $k=>$v)
	{
/*		var_dump(self::$_rule);
		echo '我是$v[1]-/-------------'.(self::$_rule[$v[1]]);
		echo "<br>$v[1]";
		var_dump($v);
		echo "<br>";
		echo '我是post[$v[0]]----'.($_POST[$v[0]]);
		echo "<br>";
		echo (self::$_rule [$v[1]] );
		echo "<br>".'我是$v[2]:';
		echo ($v[2]);
		echo "<hr>"; 
			exit;
*/
	if (!isset($data[$v[0]]))
	 { //如果表单中没有要验证的字段
		throw new exception($v[2]);
	}

	//正则验证，如果有这个正则表达式那么
	if(isset(self::$_rule[$v[1]]))
	{	//preg_match正则验证
		if(!preg_match(self::$_rule[$v[1]],$data[$v[0]]) )
		{
			throw new Exception($v[2]);
		}
	}
	else
	{	//验证码验证
		if ($v[1] == 'captcha') 
		{// 验证码验证							
			if (!isset($_SESSION['captcha']))
		 		//因为刷新一次 验证码就会存在session中
		 		//后续的话只需用这个session就可以了
		 		//所以用完要把session删除，下次再重新生成
				throw new \Exception($v[2]);
				$captcha=$_SESSION['captcha'];
				unset($_SESSION['captcha']);	
				
			//将字符串转换成小写
			if (strtolower($_POST[$v[0]]) !=strtolower($captcha)) 
			{
				throw new Exception($v[2]);
			}
			//判断两个字段是否相同
			if(isset($v[3])&&$V[3]=='confirm')
			{
				if ($data[$v[0]]==$data[$v[1]])
				{
					throw new Exception($v[2]);
				}
			}
		}
		}

	}
}
}