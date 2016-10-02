<?php

$m=new Memcache;
$m->addServer('192.168.1.1',112110);
$m->addServer('192.168.1.2',112110);
$m->addServer('192.168.1.3',112110);
$m->addServer('192.168.1.4',112110);
$m->addServer('192.168.1.6',112110);
$m->addServer('192.168.1.5',112110);

$m->connect('localhost',11211);//短连接

$m->setCompressThre	