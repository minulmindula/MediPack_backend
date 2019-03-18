<?php
$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),

	// Path must be writable by web user (defaults to `runtime/`)
	'runtimePath' => '/tmp/',

	'controllerNamespace' => 'app\commands',

	'bootstrap' => [
		'log',
		'logger',
	],

	'components' => [
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],

		'log' => [
			'targets' => [[
				'class' => 'yii\log\FileTarget',
				'levels' => ['error', 'warning'],
			]],
		],

		'logger' => require(__DIR__ . '/logger.php'),
		'db' => require(__DIR__ . '/db.php'),
	],

	'params' => require(__DIR__ . '/params.php'),

	/*
	'controllerMap' => [
		'fixture' => [ // Fixture generation command line.
			'class' => 'yii\faker\FixtureController',
		],
	],
	*/
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
