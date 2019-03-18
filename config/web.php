<?php
$config = [
	'id' => 'basic',
	'basePath' => dirname(__DIR__),

	// Path must be writable by web user (defaults to `runtime/`)
	'runtimePath' => 'C:/error_log',

	'timeZone' => 'Asia/Colombo',

	'bootstrap' => [
		'log',
		'logger',
	],

	'components' => [
		'session' => [
			'class' => 'yii\web\Session',

			'cookieParams' => [
//				'lifetime' => null,
//				'path' => null,
//				'domain' => null,
				'httpOnly' => true,
//				'secure' => true, // Requires HTTPS
			],
		],

		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
			'cookieValidationKey' => 'i56hcZ8U93Gb42A7Von64mvj3GNQ5EXP92b7x8',
			'enableCsrfValidation' => false,
			'enableCookieValidation' => true,
		],
/*
		'response' => [
			'class' => 'yii\web\Response',
			'on beforeSend' => function ($event) {
				$response = $event->sender;

				if (null !== $response->data) {
					$response->statusCode = 200;
					$response->data = [
						'success' => $response->isSuccessful,
						'data' => $response->data,
					];
				}
			},
		],
*/
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],

		'assetManager' => array(
			// Path should be visible to web server
			'basePath' => 'C:/xampp/htdocs/resources/vendor/yiisoft/yii2/web',
		),

		'user' => [
			'identityClass' => 'app\models\User',
			'enableAutoLogin' => true,
		],

		'errorHandler' => [
			'errorAction' => 'site/error',
//			'maxSourceLines' => 20,
		],

		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => true,
		],

		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [[
				'class' => 'yii\log\FileTarget',
				'levels' => ['error', 'warning'],
			]],
		],

		'logger' => require(__DIR__ . '/logger.php'),

		'db' => require(__DIR__ . '/db.php'),

		/*
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
			],
		],
		*/
	],

	'params' => require(__DIR__ . '/params.php'),
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		// uncomment the following to add your IP if you are not connecting from localhost.
		//'allowedIPs' => ['127.0.0.1', '::1'],
	];

	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		// uncomment the following to add your IP if you are not connecting from localhost.
		//'allowedIPs' => ['127.0.0.1', '::1'],
	];
}

return $config;
