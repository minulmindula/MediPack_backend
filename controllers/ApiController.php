<?php
/**
 * Application
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-21 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd.
 */
namespace app\controllers;

use app\models\DoctorAppointments;
use app\models\Hospitaldetails;
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
					'authnz', 'authnz-doc', 'register-app-user', 'register-doc', 'get-next-med-time', 'get-summary-of-day',
                    'get-weekly-schedule', 'get-singleday-schedule', 'get-doctors', 'get-hospital-emg-contact', 'get-doctor-appointments'
				],

				'rules' => [[
					// Anonymouns
					'allow' => true,
					'roles' => ['?'],
					'actions' => [
                        'authnz', 'authnz-doc', 'register-app-user', 'register-doc', 'get-next-med-time', 'get-summary-of-day',
                        'get-weekly-schedule', 'get-singleday-schedule', 'get-doctors', 'get-hospital-emg-contact', 'get-doctor-appointments'
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

	public function actionAuthnzDoc()
    {
        $post = Yii::$app->request->post();

        $username = @$post['username'];
        $password = @$post['password'];

        $data = (new User())->authnzDoc(
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

		

		if($data == true)
		{
			$this->setOutputStatus(true);
			$this->setOutputData('User added successfully');
		}else{
			$this->setOutputStatus(false);
			$this->setOutputData('User already exists');
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
		$userid = @$post['userid'];

		$data = (new MedActivity())->getNextMedTime(
			$currentTime,
			$userid
		);

		if($data == true)
		{
			$this->setOutputStatus(true);
			$this->setOutputData($data);
		}else{
			$this->setOutputStatus(false);
			$this->setOutputData("Invalid userid!");
		}

	}


	//Get summary details per day
    public function actionGetSummaryOfDay()
    {
        $post = Yii::$app->request->post();

        $userid = @$post['userid'];
        $day = @$post['dow'];
        $time = @$post['currentTimeHrs'];

        $data = (new MedActivity())->getSummaryOfDay(
            $userid,
            $day,
            $time
        );

        if($data == true)
        {
            $this->setOutputStatus(true);
            $this->setOutputData($data);
        }else{
            $this->setOutputStatus(false);
            $this->setOutputData("Invalid userid!");
        }

    }

    //Weekly schedule
    public function actionGetWeeklySchedule()
    {
        $post = Yii::$app->request->post();

        $userid = @$post['userid'];

        $data = (new MedActivity())->getWeeklySchedule(
            $userid
        );

        if($data == true)
        {
            $this->setOutputStatus(true);
            $this->setOutputData($data);
        }else{
            $this->setOutputStatus(false);
            $this->setOutputData("Invalid userid!");
        }

    }


    //Get single day schedule
    public function actionGetSingledaySchedule()
    {
        $post = Yii::$app->request->post();

        $userid = @$post['userid'];
        $day = @$post['day'];

        $data = (new MedActivity())->getSingledaySchedule(
            $userid,
            $day
        );

        if($data == true)
        {
            $this->setOutputStatus(true);
            $this->setOutputData($data);
        }else{
            $this->setOutputStatus(false);
            $this->setOutputData("Invalid userid or incorrect day entered!");
        }

    }


    //Get Doctor details
    public function actionGetDoctors()
    {
        $data = (new User())->showOnlineDoctors();

        $this->setOutputData($data);
        $this->setOutputStatus(true);
        $this->setOutputTotal(count($data));
    }


    //Get hospital details
    public function actionGetHospitalEmgContact()
    {
        $data = (new Hospitaldetails())->getHospitalEmg();

        $this->setOutputData($data);
        $this->setOutputStatus(true);
        $this->setOutputTotal(count($data));
    }

    //GetDoctorAppointment
    public function actionGetDoctorAppointments()
    {
        $post = Yii::$app->request->post();

        $username = @$post['username'];
        $date = @$post['curDate'];

        $data = (new DoctorAppointments())->getDocAppointment(
            $username,
            $date
        );

        if($data == true)
        {
            $this->setOutputStatus(true);
            $this->setOutputData($data);
        }else{
            $this->setOutputStatus(false);
            $this->setOutputData("No data!");
        }
    }


}
