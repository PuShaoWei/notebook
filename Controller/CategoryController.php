<?php
namespace Controller;
header('content-type:text/html;charset=utf8');
use Exception;
use TzlPHP\Url;
class CategoryController extends AdminController
{

	public function add()
	{
		//首先判断这个会员有没有登录
		if($_SERVER['REQUEST_METHOD']=='POST')
		{
				//处理表单
			try
			{
				//表单验证
				\TzlPHP\Validator::check($_POST,[
				['cat_name','require','分类名称不能为空'],
					]);
				//表单验证成功后就入数据库
				$cat_nameModel= new \Model\CategoryModel;
				$cat_nameModel->add([
					'cat_name'=>$_POST['cat_name'],
					'member_id'=>$_SESSION['id'],
					]);
				$this->success('添加成功',Url::U('lst'));
			}
			catch(Exception $e)
			{	//抛出异常
				$this->error($e->getMessage());
			}
		}
		else
		{
			//显示表单
			include ('View/Category/add.html');
		}
		
	}
	public function edit()
	{
		$id = (int)$_GET['id'];
		// 取出这条记录的信息
		$model = new \Model\CategoryModel;

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// 处理表单
			try {
				// 表单验证
				\TzlPHP\Validator::check($_POST, [
					['cat_name', 'require', '分类名称不能为空！'],
				]);
				$model->where([
					'id' => $id,
					'member_id' => $_SESSION['id'],
				])->save([
					'cat_name' => $_POST['cat_name'],
				]);
				// 提示信息
				$this->success('操作成功！', Url::U('lst'));
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
		}
		else
		{
			$info = $model->where([
				'id' => $id,
				'member_id' => $_SESSION['id'],
			])->find();
			include('View/Category/edit.html');
		}
	}

	public function delete()
	{	
		//取出模型
		$model=new \Model\CategoryModel;
		//调出模型的DELETC方法
		$model->where([
		'id'=>(int)$_GET['id'],
		'member_id'=>$_SESSION['id'],
		])->delete();
		$this->success('删除成功',\TzlPHP\Url::U('lst'));
	}
	public function lst()
	{	
		//取出所有的分类
		$model=new \Model\CategoryModel;
		//取出总的记录数
		$count=$model->count();
		//生成翻页 并返回一维数组
		$pageRet=\TzlPHP\page::makePage($count,10);
		//取数据
		
		$data = $model->where([
						'member_id' => $_SESSION['id'],
					])->limit($pageRet['limit'])
					  ->order('id ASC')
		  			  ->select();

		include('View/Category/lst.html');
		
	}

}