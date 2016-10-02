<?php
namespace Controller;

class LoginController extends \TzlPHP\TzlController
{
	//判断用户有没有被登录
	public function chkLogin()
	{
		if (isset($_SESSION['id'])) 
		{			//告诉客户端 当前用户名 返回
			echo json_encode([
			'ok'=>1, 
			'username'=>$_SESSION['username'],
				]);			
		}
	}
	// 显示验证图片
	public function captcha()
	{
		// 生成图片并把验证码保存到session中
		\TzlPHP\Captcha::img();
	}
	// 显示表单和处理表单
	public function regist()
	{
		// 是否是提交表单
		if($_SERVER['REQUEST_METHOD'] == 'POST') 
		{
			/************* 处理表单代码 ********************/
			try{
				// 表单验证
				\TzlPHP\Validator::check($_POST, [
					// 用name6-18对应的正则验证$_POST['username']
					['username', 'name6-18', '用户名必须是6~18位的数字、字母和下划线！'],
					// 用tel对应的正则验证$_POST['tel']
					['tel', 'tel', '请输入正确的手机号码！'],
					['password', 'name6-18', '密码必须是6~18位的数字、字母和下划线！'],
					// 判断表单中的cpassword和password这两个字段是否相等
					['cpassword', 'password', '两次密码输入不一致！', 'confirm'],
					// 验证码验证
					['captcha', 'captcha', '验证码不正确!'],
				]);
				// 插入数据库
				$model = new \Model\MemberModel;
				$model->add([
					'username' => $_POST['username'],
					'password' => md5($_POST['password'] . \TzlPHP\TzlPHP::$C['MD5_SALT']),
					'tel' => $_POST['tel'],
				]);
				// 提示信息
				$this->success('注册成功！', 'index.php?c=Login&a=login');

			}catch(\Exception $e){
				// 提示错误信息，3秒之后返回上一个页面
				$this->error($e->getMessage());
			}
		}
		else
		{
			// 显示表单代码
			include('View/Login/regist.html');
		}
	}
	// 登录
	public function login()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// 处理登录的表单
			try{
				// 表单验证
				\TzlPHP\Validator::check($_POST, [
					['username', 'name6-18', '用户名必须是6~18位'],
					['password', 'name6-18', '密码必须是6~18位'],
					['captcha', 'captcha', '验证码不正确'],
				]);

				
				// 登录
				$model = new \Model\MemberModel;
				$model->login();
				// 先从session中取出要返回的地址
				if(isset($_SESSION['returnUrl']))
				{
					$url = $_SESSION['returnUrl'];
					unset($_SESSION['returnUrl']);
				}
				else
					$url = '/';  // 默认跳到首页
				$this->success('登录成功！', $url);
				
			}
			catch(\Exception $e)
			{
				$this->error($e->getMessage());
			}
		}
		else
		{
			// 显示登录的表单
			include('View/Login/login.html');
		}
	}
	public function logout()
	{
		$_SESSION = [];
		$this->success('退出成功！', '/');
	}
}