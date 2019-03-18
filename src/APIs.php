<?php 

    public function actionShowDoctorsOnline()
	{

		$data = (new UserDoctor())->showOnlineDoctors();

		$this->setOutputData($data);
		$this->setOutputStatus(true);
		$this->setOutputTotal(count($data));

	}

	public function actionGetMessage()
	{
		$data = (new UserDoctor())->showOnlineDoctors();

		$this->setOutputData($data);
		$this->setOutputStatus(true);
		$this->setOutputTotal(count($data));
	}


	public function actionReadAll()
	{

		try{
			$data = (new User())->readAll();

			$this->setOutputData($data);
			$this->setOutputStatus(true);
			$this->setOutputTotal(count($data));
		}catch(Exception $ex)
		{
			$this->setOutputError($ex->getMessage(), $ex->getCode());
		}
		
	}


	public function actionLogout()
	{
		$this->logger->auditLog('user', 'logout', true);

		Yii::$app->user->logout();

		$this->setOutputStatus(true);
	}


	// Get active times
	// POST
	// dayOfWeek
	public function actionGetActiveTimes()
	{
		$post = Yii::$app->request->post();

		$DOW = @$post['dayOfWeek'];

		$data = (new MedicineActivity())->mediPackSchedule(
			$DOW
		);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
	}


	// Medicine activity
	// POST
	// dayOfWeek
	public function actionMedicineActivity()
	{
		$post = Yii::$app->request->post();

		$DOW = @$post['dayOfWeek'];

		$data = (new MedicineActivity())->getMedicineActivity($DOW);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
	}


	// Next medicine intake time
	// POST
	// dayOfWeek, currentTimeHours
	public function actionNextMedTime()
	{
		$post = Yii::$app->request->post();

		$DOW = @$post['dayOfWeek'];
		$currentTimeHours = @$post['currentTimeHours'];

		$data = (new MedicineActivity())->getNextMedTime($DOW, $currentTimeHours);

		$this->setOutputData($data);

		// if($data != null)
		// {
			$this->setOutputStatus(true);
		// }else{
		// 	$this->setOutputStatus(false);
		// }
	}


	// Medicine status
	// POST
	// dayOfWeek, currentTimeHours
	public function actionMedStatus()
	{
		$post = Yii::$app->request->post();

		$DOW = @$post['dayOfWeek'];
		$currentTimeHours = @$post['currentTimeHours'];

		$data = (new MedicineActivity())->getMedSummary($DOW, $currentTimeHours);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
	}


	// Set load cell data into schedule
	// POST
	// DOW, timeOfDay, status
	public function actionUpdateMedicineActivity()
	{
		$post = Yii::$app->request->post();

		$DOW = @$post['DOW'];
		$timeOfDay = @$post['timeOfDay'];
		$status = @$post['status'];

		$data = (new MedicineActivity())->updateMedicineActivity($DOW, $timeOfDay, $status);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}


	// Set load cell data into schedule -> All at once!
	// POST
	//Slot, data
	public function actionUpdateMedicineActivityAll()
	{
		$post = Yii::$app->request->post();

		$slot = @$post['slot'];

		$data = (new MedicineActivity())->updateMedicineActivity($DOW, $timeOfDay, $status);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}


	public function actionCurrentDaySchedule()
	{
		$post = Yii::$app->request->post();

		$currentDay = @$post['currentDay'];

		$data = (new MedicineActivity())->getCurrentDaySchedule($currentDay);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}

	}

	public function actionGetScheduleAfterTomorrow()
	{
		$post = Yii::$app->request->post();

		$tomorrowDay = @$post['tomorrowDay'];

		$data = (new MedicineActivity())->getScheduleAfterTomorrow($tomorrowDay);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
	}

	public function actionGetWeeklySchedule()
	{
		$post = Yii::$app->request->post();

		$dow = @$post['dow'];
		$tod = @$post['tod'];
		$userID = @$post['userID'];

		$data = (new MedicineActivity())->getSchedule($dow,$tod,$userID);

		$this->setOutputData($data);

		if($data != null)
		{
			$this->setOutputStatus(true);
		}else{
			$this->setOutputStatus(false);
		}
	}