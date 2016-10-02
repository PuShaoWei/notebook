<?php
namespace TzlPHP;
use PDO;
class TzlModel
{
	private $_where = [];
	private $_limit = '';
	private $_order = '';
	private $_alias	= '';
	private $_filed	= '*';
	private $_join	= '';
	// 静态变量：这个连接属于这个类，无论new多少个对象都只有这一个pdo变量
	protected static $_pdo = null;
	public function __construct()
	{
		if(null === self::$_pdo)
		{
			try{
				self::$_pdo = new PDO('mysql:host='.\TzlPHP\TzlPHP::$C['DB_HOST'].';dbname='.\TzlPHP\TzlPHP::$C['DB_NAME'], \TzlPHP\TzlPHP::$C['DB_USER'], \TzlPHP\TzlPHP::$C['DB_PASS']);
				self::$_pdo->exec('SET NAMES '.\TzlPHP\TzlPHP::$C['DB_CHARSET']);
				self::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		    catch(PDOException $e)
			{
				die('connection error : '.$e->getMessage());
			}

		}
	}
	// 插入数据库
	// 调用时需要传一个参数：就是要插入的数据，键是字段名称，值是插入到数据库中的值
	//			[
	//				'username' => $_POST['username'],
	//				'password' => md5($_POST['password'] . \TzlPHP\TzlPHP::$C['MD5_SALT']),
	//				'tel' => $_POST['tel'],
	//			]
	public function  add($data)
	{
		// 取出数组中所有的键构造一个新的数组
		$keys = array_keys($data);  // [username,password,tel]
		$keys = implode(',', $keys); // username,password,tel
		// 取出值的数量
		$count = count($data);
		// 制作一个全是?号的数组
		$va = array_fill(0, $count, '?');  // [?,?,?]
		$va = implode(',', $va);           // ?,?,?
		//INSERT INTO blog (username,password,tel) VALUES(?,?,?);
		$stmt = self::$_pdo->prepare("INSERT INTO {$this->tableName}($keys) VALUES($va)");
		// 把所有的值传成一个数组
		$values = array_values($data);    // ['test123',13400000000,'fdsafdsafsadf']
		// 执行时把值和?对应上
		$stmt->execute($values);	
	}
	//做数据查询用
	public function where($where)
	{	//先把where这个数组赋值给where这个私有变量
		$this->_where=$where;   	
		return $this;
	}
	public function limit($limit)
	{
		$this->_limit=$limit;
		return $this;
	}
	public function alias($alias)
	{
		$this->_alias=$alias;
		return $this;
	}
	public function order($order)
	{
		$this->_order=$order;
		return $this;
	}	
	public function filed($filed)
	{
		$this->_filed=$filed;
		return $this;
	}
	public function join($join)
	{
		$this->_join=$join;
		return $this;
	}
	
	public function select()
	{	
		//先解析where
		$where=$this->_whereSQL();
		//解析limit
		$limit=$this->_limitSQL();
		//解析order
		$order=$this->_orderSQL();
		//使用解析拼出SQL

		$stmt=	self::$_pdo->prepare("SELECT {$this->_filed} FROM {$this->tableName} {$this->_alias} {$this->_join} {$where['where']} $order $limit");
		$stmt->execute($where['value']);
		//取出数据
		//清空所有的条件
		$this->_clearSQL();
		//执行SQL
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	//拼出whereSQL
	private function _whereSQL()
	{
		$keys='';
		$values=[];
		if ($this->_where)
		{	/*	
			$keys=array_keys($this->_where);//['age','username'];
			$keys='WHERE '.implode('=? AND ',$keys).' =? '; 
			//拼出每个问号占位符的值
			$values =array_values($this->_where);//['10','tom']*/

			//where 1 就是 WHERE 1 AND id=XXX AND member=XXXX
			//因为后面要每个加AND
			$keys ='WHERE 1';

			foreach ($this->_where as $k => $v)
			{
				//如果ID是数字 或者ID是字符串 就转换成等于
				if (is_string($v) || is_numeric($v))
				{
					$keys .' AND '. $k.'=? ';
				    $keys .= ' AND '.$k.'=? ';
					$values[]=$v;
				}

				elseif(is_array($v))
				{
					if($v[0] == 'in')
					{
						$keys .= ' AND find_in_set('.$k.',?)'; //and find_in_set(id,?)
						$values[] = $v[1];
					}
					else
					{
						$keys .= ' AND '.$k.' '.$v[0].' ?';
						$values[] = $v[1];
					}
					
				}
			}
	   	}
	   	return [
		   	'where'=>$keys, //WHERE username=? AND age =?
	 	   	'value'=>$values,//返回每个？对应的数组这个要在execute时使用
	   	];
	}

	//拼出limitSQL
	private function _limitSQL()
	{
		$sql='';
		if ($this->_limit)
		{	
			$sql="LIMIT {$this->_limit}";
		}
		return $sql;
	}
	//清空所有连贯条件
	private function _clearSQL()
	{
		$this->_where=[];
		$this->_limit='';
		$this->_order='';
		$this->_alias='';
		$this->_filed='*';
		$this->_join='';
	}
	private function _orderSQL()
	{
		$sql='';
		if ($this->_order)
		{	
			$sql="ORDER BY   {$this->_order}";
		}
		return $sql;
	}
	//查询几条记录
	public function find()
	{	//思考这里为什么会是$_pdo 下的 prepare, 因为$_pdo 配合上面的构造函数，$_pdo属于连接数据库的这个变量
		if(true==$this->_where)// 之前这个where 为空，因为where 这个方法给它数组了它才不为空
		{
			//SELECT *FROM {$this->tableName} WHERE age=? AND username=?
			$keys=array_keys($this->_where);//['age','username'];
			$keys=implode('=? AND ',$keys).' =? '; 
			$stmt=	self::$_pdo->prepare("SELECT *FROM {$this->tableName} WHERE $keys LIMIT 1");
			//因为是预处理的所以
			$values =array_values($this->_where);//['10','tom']
			$stmt   ->execute($values); //然后补充问号
			//然后清空这where条件 不然每次都会存在where
			$this->_where=[];
		}	
		else
		{	//如果没传where的话,就只执行查询一条语句 	
				$stmt=	self::$_pdo->prepare("SELECT *FROM {$this->tableName} LIMIT 1");
		}

	$stmt->execute();
	return	$stmt->fetch(PDO::FETCH_ASSOC);
	}
		// 取总记录数
	public function count()
	{
		$where = $this->_whereSQL();
		$stmt = self::$_pdo->prepare("SELECT COUNT(*) FROM {$this->tableName} {$where['where']}");
		$stmt->execute($where['value']);
		$this->_clearSQL();
		return $stmt->fetch(PDO::FETCH_NUM)[0];
	}


	public function delete()
	{	//解析where

		$where =$this->_whereSQL();
		$limit =$this->_limitSQL();
		$stmt  =self::$_pdo->prepare("DELETE FROM {$this->tableName} {$where [ 'where']}  $limit");
		$stmt	-> execute($where['value']);
		$this	->_clearSQL();
	}
	public function save($data)
	{
		$set='';
		$uvalue=[];
		foreach ($data as $k => $v) 
		{
			$set .= "$k=?,";   
		
			$uvalue[].=$v;
		}
		$set=rtrim($set,',');

		$where =$this->_whereSQL();

		$stmt=self::$_pdo->prepare("UPDATE {$this->tableName} SET $set  {$where['where']} ")	;
		//补问号的时候 要把两个数组合并得到一起
		$stmt->execute(array_merge($uvalue,$where['value']));

		$this->_clearSQL();
	}


}