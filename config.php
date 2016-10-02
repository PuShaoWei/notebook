<?php
//配置文件
return [
/********网站名称*********/
'WEB_NAME'=>'逼乎 - 爱上每天装逼的习惯',

/********数据库配置*******/
'DB_HOST'=>'localhost',
'DB_NAME'=>'tzlblog_2',
'DB_USER'=>'root',
'DB_PASS'=>'',
'DB_CHARSET'=>'utf8',
/**********加密盐配置*****/
//加密盐设置
'MD5_SALT'=>'ase;/dqwz@*7*2739~\2109zx0-)*+-+@!@8',
//图片配置
'IMG_ROOT'=>'./Public/Uploads/',
'IMG_SIZE'=>1048576,
'IMG_TYPE'=>['image/png','image/gif','image/jpeg','image/jpg','image/pjpeg'],
//主页配置,访问控制器和方法
'DEFAULT_C'=>'Index',
'DEFAULT_A'=>'index',
];