<?php
/**
 * Application
 *
 * Regular maintenance process handler
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-21 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd.
 */
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Role;

class MaintainController extends Controller
{
	private $logger    = null;
	private $appParams = null;

	private $hostIp = null;
	private $sessId = null;
	private $pid    = null;


	public function init()
	{
		$this->hostIp = '127.0.0.1';
		$this->sessId = uniqid();
		$this->pid    = getmypid();
		$this->sessId = sprintf('%s-%s', uniqid(), $this->pid);

		// For safety, set to infinity
		set_time_limit(0);

		$this->logger    = Yii::$app->logger;
		$this->appParams = Yii::$app->params;

		// Get the process running user info
		$_u = posix_getpwuid(posix_geteuid());

		// Set logger params
		$this->logger->setSessionId($this->sessId);
		$this->logger->setHostAddr($this->hostIp);
		$this->logger->setRemoteAddr();
		$this->logger->setLoggedInUserId(sprintf('%s:%s', $_u['uid'], $_u['name']));
	}


	public function beforeAction($action)
	{
		$this->logger->setLoggedInUserName('SYSTEM');
		$this->logger->actLog('Maintain commands started; Current max-execution-time:' . ini_get('max_execution_time'));

		return parent::beforeAction($action);
	}

	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);

		$this->logger->setLoggedInUserName('SYSTEM');
		$this->logger->actLog('Maintain commands finished');

		return $result;
	}


	/**
	 * Sample code...
	 */
	public function actionIndex()
	{
		$this->logger->setLoggedInUserName('SYSTEM-INDEX');
		$this->logger->actLog('Started');

		print_r((new Role())->readAll());

		$this->logger->actLog('Finihed');

		return self::EXIT_CODE_NORMAL;
//		return self::EXIT_CODE_ERROR;
	}
}
