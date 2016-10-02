create database tzlblog_2 default charset=utf8;
use tzlblog_2;

create table member
(
	id mediumint unsigned not null auto_increment comment 'Id',
	username varchar(18) not null comment '用户名',
	password char(32) not null comment '密码',
	tel char(11) not null comment '手机号',
	face varchar(150) not null default '' comment '头像',

	primary key (id)
)engine=InnoDB comment '会员表';

create table category
(
	id mediumint unsigned not null auto_increment comment 'Id',
	cat_name varchar(30) not null comment '分类名称',
	member_id mediumint unsigned not null comment '会员Id',
	primary key (id)
)engine=InnoDB comment '分类表';

create table blog
(
	id mediumint unsigned not null auto_increment comment 'Id',
	logo varchar(150) not null default '' comment '封面图片',
	title varchar(60) not null comment '标题',
	content longtext not null comment '内容',
	addtime datetime default current_timestamp comment '发表时间',
	is_public enum('是','否') not null default '是' comment '是否公开',
	member_id mediumint unsigned not null comment '会员Id',
	cat_id mediumint(8) unsigned not null comment '分类的Id',
	display mediumint(8) unsigned not null default '0' comment '浏览量',
	zan_num mediumint(8) unsigned not null default '0' comment '赞的次数',
	short_content longtext not null default'' comment'预览',
	index is_public(is_public),
	index zan_num(zan_num),	
	index addtime(addtime),
	logo_2 varchar(150) not null default '' comment'日志图片',
	sphinx_indexed tinyint unsigned not null default '0' comment '是否被sphinx索引过',	
	primary key (id)
)engine=InnoDB comment '日志表';

create table zan_log
(
	member_id mediumint unsigned not null comment '会员Id',
	blog_id mediumint unsigned not null comment '日志Id',
	primary key (member_id,blog_id)
)engine=InnoDB comment '赞的记录';


存储过程：
DELIMITER //
create procedure proc_countNum(in columnIdint,out rowsNo int)
begin
select count(*) into rowsNo from member where id=columnId;    
end
call proc_countNum(1,@no);
select @no;


视图：
create view v_countNum as select id,count(*) as countNum from tzl_goods group by id;
select countNum from v_countNum where id=1;
删除视图 执行 drop view  v_countNum;



视图：
create view v_countNum as select member_id,count(*) as countNum from member group by member_id
select countNum from v_countNum where member_id=1