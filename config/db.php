<?php
return [
//	'class' => 'app\components\db\Connection',
//
//	// MySQL
//	'dsn' => 'mysql:host=127.0.0.1;dbname=DEMO',
//	'username' => 'demo',
//	'password' => 'demo',
//	'charset' => 'utf8',
//
//	// Oracle
////	'dsn' => 'oci:dbname=//192.168.56.102:1521/XE;charset=UTF8',
////	'username' => 'SFA_ADMIN',
////	'password' => 'sfa',
//
//	'enableSchemaCache' => true,
//	'schemaCacheDuration' => 604800, // 1 week; Need caching

    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;port=3306;dbname=medipackdatabase',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',

];
