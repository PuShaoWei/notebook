<?php
namespace Controller;
use Exception;
class BlogController extends AdminController
{
	public function add()
	{
		set_time_limit(0);

/*		$blogModel = new \Model\BlogModel;
	   $str = null;
	   $strPol = "PHP，是，世，界，上，最，的，语，言，没，有，之，一，不，服，来，辩，PHP，是，世，界上，最，的，语言，没有之一，不，服来辩";
	   $max = strlen($strPol)-1;
	   for($i=0;$i<100;$i++)
	   {

	    $str.= substr($strPol,mt_rand(0,8)*3,3);//rand($min,$max)生成介于min和max两个数之间的一个随机整数

	   }
					for ($i=216504; $i<=500000 ; $i++) { 					
					$blogModel->add([
						'logo'=>'',
						'cat_id' => '1',
						'title' => '第'. $i.' 个日志标题',
						// 这个内容因为是在线编辑器，所以需要使用原始的内容单独过滤
						'content' =>'第'. $i.' 个日志内容' ,
						'short_content' =>'第'. $i.' 个日志预览',			
						'member_id' => $_SESSION['id'],
						'is_public' => '是',
						]);
					}
					die;*/

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// 处理表单
			try {
				//表单验证
				\TzlPHP\Validator::check($_POST,[
					['cat_id', 'require', '必须选择一个分类！'],
					['title', 'require', '标题不能为空！'],
					['content', 'require', '日志内容不能为空！'],
					]);
				//表单验证成功后就入数据库
				// 插入数据库
				$img='';
				//执行正则并把匹配到的参数放到第三个参数上边  ，这个正则是指把第一个IMG弄出来	
				if(preg_match('/<img.+src="(.+)"/U', $GLOBALS['_RAW_POST']['content'],$jieguo))
				{
					$img=$jieguo[1];
				}

				$c= \TzlPHP\XSS::removeXSS($GLOBALS['_RAW_POST']['content']);
			
				$blogModel = new \Model\BlogModel;

				$blogModel->add([
					
					'logo'=>$img,
					'cat_id' => $_POST['cat_id'],
					'title' => $_POST['title'],
					// 这个内容因为是在线编辑器，所以需要使用原始的内容单独过滤
					'content' =>$c,
					'short_content' =>mb_substr(strip_tags($c),0,88,'utf-8').'...',					
					'member_id' => $_SESSION['id'],
					'is_public' => $_POST['is_public'],
													]);
			


				$this->success('操作成功！', \TzlPHP\Url::U('lst'));

			}

			catch(Exception $e)
			{	//抛出异常
				$this->error($e->getMessage());
			}	
		}
		else
	{
			// 取出所有的分类的数据
			$catModel = new \Model\CategoryModel;
			$catData = $catModel->where([
				'member_id' => $_SESSION['id'],
			])->select();
			// 显示表单的代码
			include('View/Blog/add.html');
		}
		

	}
	public function edit()
	{
		$id = (int)$_GET['id'];
		// 取出这条记录的信息
		$model = new \Model\BlogModel;

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// 处理表单
			try {
				//表单验证
				\TzlPHP\Validator::check($_POST,[
					['cat_id', 'require', '必须选择一个分类！'],
					['title', 'require', '标题不能为空！'],
					['content', 'require', '日志内容不能为空！'],
					]);
				// 插入数据库
				$img='';
				//执行正则并把匹配到的参数放到第三个参数上边  ，这个正则是指把第一个IMG弄出来	
				if(preg_match('/<img.+src="(.+)"/U', $GLOBALS['_RAW_POST']['content'],$jieguo))
				{
					$img=$jieguo[1];
				}

				$c= \TzlPHP\XSS::removeXSS($GLOBALS['_RAW_POST']['content']);				
				$blogModel = new \Model\BlogModel;
				$blogModel->where(
					[
					 'id' => $id,
					'member_id' => $_SESSION['id'],]
					)->save([
					'cat_id' => $_POST['cat_id'],
					'title' => $_POST['title'],
					// 这个内容因为是在线编辑器，所以需要使用原始的内容单独过滤
					'logo'=>$img,
					'content' =>$c,
					'short_content' =>mb_substr(strip_tags($c),0,88,'utf-8').'...',	
					'member_id' => $_SESSION['id'],
					'is_public' => $_POST['is_public'],
					'sphinx_indexed'=>0,]);//下次重建索引
				 /***********更新sphinxk中这条记录的is_delete属性为1**********/
				 	/********引入 new类 设参数 匹配模式*******/
				require('./sphinxapi.php');//包含我们的sphinx API 文件 
				$sphinx =new \SphinxClient;  //new  一下这个类
				$sphinx->setServer('localhost',9312);//参数1:host主机;参数2:预先设置的端口号
				$sphinx->setMatchMode(SPH_MATCH_ALL);//设置匹配模式
				//设置属性
				$sphinx->updateAttributes('blog_index',['is_delete'],[
					[
						$id=>1,
					],
					]);


				$this->success('操作成功！', \TzlPHP\Url::U('lst'));
			}
			 catch (\Exception $e) {
				$this->error($e->getMessage());
			}
		}
		else
		{	

		//修改表单
			$editData = $model->where([
				'id' => $id,
				'member_id' => $_SESSION['id'],
			])->find();
		// 取出所有的分类的数据
			$catModel = new \Model\CategoryModel;
			$catData = $catModel->where([
				'member_id' => $_SESSION['id'],
			])->select();

			include('View/Blog/edit.html');
		}
	}
	public function delete()
	{
		//取出模型
		$model=new \Model\BlogModel;
		//调出模型的DELETC方法
		$id=(int)$_GET['id'];
		$model->where([
		'id'=>$id,
		'member_id'=>$_SESSION['id'],
		])->delete();
		/***********更新sphinxk中这条记录的is_delete属性为1**********/
	 	/********引入 new类 设参数 匹配模式*******/
		require('./sphinxapi.php');//包含我们的sphinx API 文件 
		$sphinx =new \SphinxClient;  //new  一下这个类
		$sphinx->setServer('localhost',9312);//参数1:host主机;参数2:预先设置的端口号
		$sphinx->setMatchMode(SPH_MATCH_ALL);//设置匹配模式
		//设置属性
		$sphinx->updateAttributes('blog_index',['is_delete'],[
			[
				$id=>1,
			],
			]);

		$this->success('删除成功',\TzlPHP\Url::U('lst'));

	}
	public function lst()
	{

		//取出所有的分类
		$model=new \Model\BlogModel;
		//取出总的记录数
		$count=$model->count();
		//生成翻页 并返回一维数组
		$pageRet=\TzlPHP\page::makePage($count,10);
		//取数据
		/********************* 拼where数组 *********************/
		$where['a.member_id'] = $_SESSION['id'];
		// 分类搜索
		if(isset($_GET['cid']) && $_GET['cid'])
			$where['a.cat_id'] = $_GET['cid'];             // a.cat_id = xxx
		// 标题搜索
		if(isset($_GET['t']) && $_GET['t'])
			$where['a.title'] = ['like', "%{$_GET['t']}%"];   // title like %$_GET[t]%

		// 开始时间
		if(isset($_GET['st']) && $_GET['st'])
			$where['a.addtime'] = ['>=', $_GET['st']];    // a.addtime >= $_GET['st']

		// 结束时间
		if(isset($_GET['et']) && $_GET['et'])
			$where['a.addtime'] = ['<=', $_GET['et']];    // a.addtime <= $_GET['et']


		
		$data = $model->alias('a')
					  ->filed('a.id,a.title,a.addtime,a.logo,a.is_public,b.cat_name')
					  ->join('LEFT JOIN category b ON a.cat_id=b.id')
					  ->where($where)
					  ->limit($pageRet['limit'])
					  ->order('a.id DESC')
		  			  ->select();

		// 取出所有的分类的数据
		$catModel = new \Model\CategoryModel;
		$catData = $catModel->where([
			'member_id' => $_SESSION['id'],
		])->select();

		include('View/Blog/lst.html');
		

	}

}
