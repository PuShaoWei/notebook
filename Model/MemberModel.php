<?php
namespace Model;
use TzlPHP\TzlModel;
class MemberModel extends TzlModel
{
	// 关联哪个表
	protected $tableName = 'member';

	public function login()
	{
		// 根据用户名查询出这条记录
		$user = $this->where([
			'username' => $_POST['username'],
		])->find();
		if($user)
		{
			if($user['password'] == md5($_POST['password'] . \TzlPHP\TzlPHP::$C['MD5_SALT']))
			{
				$_SESSION['id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['face'] = $user['face'];
			}
			else
				throw new \Exception('密码不正确！');
		}
		else
			throw new \Exception('用户名不存在！');
	}
}
