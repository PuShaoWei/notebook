<?php
namespace Model;
use \TzlPHP\TzlModel;
use PDO;
//继承了TzlModel 这个父亲该有的自动查询数据库的这个功能
class BlogModel extends TzlModel
{
	//因为它爸哪里有个晚绑定的$this 这个晚绑定的
	//调用的时候就相当于告诉哪个表名等于member表嘛
	
	protected $tableName='blog';

	public function zan_add($blogId)
	{
		$stmt=self::$_pdo->prepare("UPDATE {$this->tableName} SET zan_num=zan_num+1 where id=?");

		$stmt->execute([$blogId]);
	}

	public function getBest($limit=10)
	{
		$where['is_public']='是'; 
return 	$this
		->filed('id,title')
		->order('zan_num DESC')
		->where($where)
		->limit($limit)
		->select();
	}
}

