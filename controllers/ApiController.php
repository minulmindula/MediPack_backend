<?php
/**
 * Application
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-21 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd.
 */
namespace app\controllers;

use Exception;
use Yii;
use yii\filters\AccessControl;
use app\components\JsonController;
use app\components\ExitCode;
use app\models\Role;
use app\models\User;
use app\models\UserPatient;
use app\models\UserDoctor;
use app\models\MediPackSchedule;
use app\models\MedicineActivity;
use app\models\MedActivity;

class ApiController extends JsonController
{
	public function beforeAction($action)
	{
		// $this->setupCorsHeaders();

		return parent::beforeAction($action);
	}


	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),

				'denyCallback' => function($rule, $action) {
					parent::showNotAuth($rule, $action);
				},

				'only' => [
					'authnz', 'read-all', 'show-doctors-online', 'get-active-times', 'medicine-activity',
					'next-med-time', 'med-status', 'update-medicine-activity', 'current-day-schedule', 'get-schedule-after-tomorrow',
					'get-weekly-schedule', 'register-app-user', 'register-doc', 'get-next-med-time'
				],

				'rules' => [[
					// Anonymouns
					'allow' => true,
					'roles' => ['?'],
					'actions' => [
						'authnz', 'authn-patient', 'authn-doc', 'show-doctors-online', 'get-active-times', 'medicine-activity',
						'next-med-time', 'med-status', 'update-medicine-activity', 'current-day-schedule', 'get-schedule-after-tomorrow',
						'get-weekly-schedule', 'register-app-user', 'register-doc', 'get-next-med-time'
					],
				], [
					// Authorized
					'allow' => true,
					'roles' => ['@'],

					'actions' => [
						
					],
				]]
			],
		];
	}

	//Authenticate user
	public function actionAuthnz()
	{
		$post = Yii::$app->request->post();

		$username = @$post['username'];
		$password = @$post['password'];

		$data = (new User())->authnz(
			$username, 
			$password
		);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
		
	}

	//Register app user
	public function actionRegisterAppUser()
	{
		$post = Yii::$app->request->post();

		$firstname = @$post['firstname'];
		$lastname = @$post['lastname'];
		$contact = @$post['contact'];
		$email = @$post['email'];
		$password = @$post['password'];

		$data = (new User())->registerAppUser(
			$firstname,
			$lastname,
			$contact,
			$email,
			$password
		);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}

	//Register doctor
	public function actionRegisterDoc()
	{
		$post = Yii::$app->request->post();

		$firstname = @$post['firstname'];
		$lastname = @$post['lastname'];
		$contact = @$post['contact'];
		$email = @$post['email'];
		$password = @$post['password'];
		$author = @$post['author'];

		$data = (new User())->registerDoc(
			$firstname,
			$lastname,
			$contact,
			$email,
			$password,
			$author
		);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}

	//Next med time from current time
	public function actionGetNextMedTime()
	{
		$post = Yii::$app->request->post();

		$currentTime = @$post['currentTime'];

		$data = (new MedActivity())->getNextMedTime(
			$currentTime
		);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}


}
