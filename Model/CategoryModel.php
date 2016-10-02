<?php
namespace Model;
use \TzlPHP\TzlModel;
//继承了TzlModel 这个父亲该有的自动查询数据库的这个功能
class CategoryModel extends TzlModel
{
	protected $tableName='category';
}