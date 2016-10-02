<?php
namespace Controller;
header('content-type:text/html;charset=utf8');
//用use导入
use \TzlPHP\TzlController;
use Exception;
class TestController extends TzlController
{

    public function reg()
	{


	      	$data=[	//用name6-18验证的$_POST['username']
				     ['username','require','用户名不能为空'],
						//用tel对应的正则验证$_POST['tel']
					 ['tel','tel','手机号码输入不正确'],
				//判断表单中的cpassword和password
				     ['password','name6-18','密码不正确'],
				//验证码验证
				     ['cpassword','password','两次密码不一致','confirm'],
				     ['captcha','captcha','验证码输入不正确'],
			
				];	
				var_dump($data);

				foreach ($data as $key => $value) 
				{
					echo "$value[2]";
				}

	}

			
						
} 