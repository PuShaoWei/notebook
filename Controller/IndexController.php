<?php
namespace Controller;
use \TzlPHP\TzlController;
use SphinxClient;
header("Content-type: text/html; charset=utf-8");
class IndexController extends TzlController
{

	public function ajaxZan()
	{
		if (!isset($_SESSION['id']))
		{
			//如果没有登录 提示
			echo -1;
		}
		else
		{
			$blogId=(int)$_GET['id'];
			//判断没有没被赞过
			$zlm=new \Model\zanlogModel;
			$count=$zlm->where([
				'member_id'=>$_SESSION['id'],
				'blog_id'=>$blogId,
				])->count();
			if ($count>0)
			{
				//如果已经登录
				echo -2;
			}
			else
			{
				//把赞的数量加1
				$bm=new \model\BlogModel;
				$bm->zan_add($blogId);
				//记录这次赞的操作
				$zlm->add([
					'member_id'=>$_SESSION['id'],
					'blog_id'=>$blogId,
					]);
				echo 1;
			}
			
		}
	}

	public function index()
	{	
		/******1.判断缓存文件**********/
		$p=isset($_GET['p'])?$_GET['p']:1;//接收p变量
		$cf="./Cacha/index-$p.html";
		//判断有没有这个缓存文件并且判断有没有过期，
		//如果五分钟之前生成的就过期了			
		if (file_exists($cf)&&time()-filectime($cf)<300)
		 {
			//条件具备就包含这个缓存文件
			include($cf);exit;	
		}	

		//取出所有的日志
		$model=new \Model\BlogModel;
		//设置where 条件数组
		$where['is_public']='是';
		//取出总的记录数
		$count=$model->where($where)->count();
		//生成翻页 并返回一维数组
		$pageRet=\TzlPHP\page::makePage($count,5);
		//取数据		
		$data = $model->alias('a')
					  ->filed('a.id,a.title,a.zan_num,a.short_content,a.logo,a.display,a.addtime,a.is_public,a.member_id')
					  ->where($where)
					  ->limit($pageRet['limit'])
					  ->order('a.id DESC')
		  			  ->select();

		 /*********数据量大的时候 分开查询***************/
		 /*********弊端：一共就请求执行了十条SQL********/
		 /********如果人多的话就会造成大的带宽负担*/
		 /********采取缓存机制********************/	
		$MermberModel=new \Model\MemberModel; 			  
		foreach($data as $k=>$v)
		{
			$info=$MermberModel->filed('username,face')
						 ->where([
				'id '=>$v['member_id'],		 	
						 	])
						 ->find();
						 	
			$data[$k]['username']=$info['username'];
			$data[$k]['face']=$info['face'];
		}

		 //取出精品日志排行榜
		 $best=$model->getBest();
		 /**********2.调用缓存文件********/
		 ob_start();//开启ob缓冲区，把最终要输入的结果放到ob缓冲区中
   		include('View/Index/index.html');
		//从ob缓冲区中取出最终结果
		
		$obData=ob_get_contents();
		//生成缓存文件
		file_put_contents($cf, $obData);

	}

public function search()
{
	/********引入 new类 设参数 匹配模式*******/
	require('./sphinxapi.php');//包含我们的sphinx API 文件 
	$sphinx =new SphinxClient;  //new  一下这个类
	$sphinx->setServer('localhost',9312);//参数1:host主机;参数2:预先设置的端口号
	$sphinx->setMatchMode(SPH_MATCH_ANY);//设置匹配模式
	/******************sphinx*****************/
	$data=$sphinx->query($_GET['key']);//搜索GET来的值	
	
	/************用sphinx 做翻页************/
	$count=$data['total'];	//总的记录数
	$pageRet=\TzlPHP\page::makePage($count,5);
	/*******取出$pageRet里面的limit********/
	$limit=explode(',',trim($pageRet['limit']));
	/********设置sphinx中取第几页**********/
	//Ps:因为setLimits()的两个值要求必须强制为整数
	$sphinx->setLimits((int)$limit[0],(int)$limit[1]);
	//查询出下一页的数据
	$data=$sphinx->query($_GET['key']);

	if(isset($data['matches']) )
	{
	/******从返回data结果中取出记录的id********/
	$dataId=array_keys($data['matches']); 
	/**把用array_keys转化成的数组转成字符串***/
	$dataId=implode(',',$dataId);//1,2,3,4,5,6
	/******有了ID 就可以查数据库了***********/
	$model=new \Model\BlogModel; 
 $blogData=$model->filed('id,title,zan_num,short_content,logo,display,addtime,is_public,member_id')
		  ->where([
			'is_public'=>'是',
			'id'=>['in',"$dataId"],//id in(1,2,3,5,6) 
		])				//使用in的话里面变量一点要括起来
		 ->select(); //日志的数据就位
	//find_in_set('id',1,2,3)
	//in两种写法：id in(1,2,3)  或 find_in_set(id,1,2,3)
	//PDO 不支持绑定数组 in()所以在底层改成 find_in_set(id,1,2)

		$MermberModel=new \Model\MemberModel; 			  
		foreach($blogData as $k=>$v)
		{
		/**********高亮显示*************/
		//参数1:高亮的标题，参数2：索引源的名字,参数3:搜索的内容
		//参数4：一个牛逼的数组里面两个小参数before_match(前)after_match(后)
		//返回的是一个数组
		$ret=$sphinx->buildExcerpts([ $v['title']],'blog_index',$_GET['key'],
			[
			'before_match'=>'<span style="color:#F00">',
			 'after_match'=>'</span>',
			]);
	
		//把处理后的标题 覆盖原来的标题
		$blogData[$k]['title']=$ret[0];
			$info=$MermberModel->filed('username,face')
						 ->where([
				'id '=>$v['member_id'],		 	
						 	])
						 ->find();
						 	
			$blogData[$k]['username']=$info['username'];
			$blogData[$k]['face']=$info['face'];
		}
		/**********取出精品排行榜******/
		 $best=$model->getBest();
		include('View/Index/search.html');
}
else
{		
		$blogData='';
		$model=new \Model\BlogModel; 
		$best=$model->getBest();
		include('View/Index/search.html');
}	

}
	public function ok()
	{
	echo "我是主页哦";

	$data=[
	'sz00001'=>'shuzu1',
	'sz002001'=>'shuz21',
	];

	$data=array_keys($data);
	$data=implode('=? AND ',$data).'=? ';
	var_dump($data);
		$this->redirect('ww');
	}	

	public function article()
	{
		//取出地址栏ID
		$id=(int)$_GET['id'];

		$model=new \Model\BlogModel;

		$where=['id' => $id,'is_public' => '是',];
		
		// 查询 出来 
	$blogData = $model->where($where)
                  ->find();
		//更新数据
		$model->where($where)
		      ->save(['display'=>$blogData['display']+1,]); 
		 //取出精品日志排行榜
		 $best=$model->getBest();
		include('View/Index/article.html');

	}

}