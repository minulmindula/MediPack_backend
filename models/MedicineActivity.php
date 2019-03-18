<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "medicineactivity".
 *
 * @property int $id
 * @property string $deviceID
 * @property string $DOW
 * @property int $morning
 * @property int $afternoon
 * @property int $night
 * @property int $morning_hour
 * @property int $afternoon_hour
 * @property int $night_hour
 */
class MedicineActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'medicineactivity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DOW', 'morning', 'afternoon', 'night', 'morning_hour', 'afternoon_hour', 'night_hour'], 'required'],
            [['morning', 'afternoon', 'night', 'morning_hour', 'afternoon_hour', 'night_hour'], 'integer'],
            [['deviceID', 'DOW'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deviceID' => 'Device ID',
            'DOW' => 'Dow',
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'night' => 'Night',
            'morning_hour' => 'Morning Hour',
            'afternoon_hour' => 'Afternoon Hour',
            'night_hour' => 'Night Hour',
        ];
    }


    public function mediPackSchedule($DOW)
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT * FROM medicineactivity WHERE `DOW` = '$DOW' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                'DOW' => $a['DOW'],
				'morning' => $a['morning'],
                'afternoon' => $a['afternoon'],
                'night' => $a['night']
			);
        }

        return $array;

    }


    public function getMedicineActivity($DOW)
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT * FROM medicineactivity WHERE `DOW` = '$DOW' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                'DOW' => $a['DOW'],
				'morning' => $a['morning'],
                'afternoon' => $a['afternoon'],
                'night' => $a['night'],
                'morningHour' => $a['morning_hour'],
                'afternoonHour' => $a['afternoon_hour'],
                'nightHour' => $a['night_hour'],
			);
        }

        return $array;

    }


    public function getNextMedTime($DOW, $currentTimeHours)
    {
        $db = Yii::$app->db;
        $array = [];

        $morningHour = null;
        $afternoonHour = null;
        $nightHour = null;

        $query = "SELECT * FROM medicineactivity WHERE `DOW` = '$DOW' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                $morningHour = (int) $a['morning_hour'],
                $afternoonHour = (int) $a['afternoon_hour'],
                $nightHour = (int) $a['night_hour'],
			);
        }

        if($currentTimeHours <= $morningHour)
        {
            return array('time' => $morningHour);

        }else if($currentTimeHours >= $morningHour && $currentTimeHours <= $afternoonHour)
        {
            return array('time' => $afternoonHour);

        }else if($currentTimeHours >= $afternoonHour && $currentTimeHours <= $nightHour)
        {
            return array('time' => $nightHour);

        }else if($currentTimeHours >= $nightHour)
        {
            return array('time' => $morningHour);;
        }

    }


    public function getMedSummary($DOW, $currentTimeHours)
    {
        $db = Yii::$app->db;
        $array = [];
        $returnArray = [];

        $morningHour = '';
        $afternoonHour = '';
        $nightHour = '';
        $morningStatus = '';
        $afternoonStatus = '';
        $nightStatus = '';

        $morning = 2;
        $afternoon = 2;
        $night = 2;

        $query = "SELECT * FROM medicineactivity WHERE `DOW` = '$DOW' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                $morningStatus = $a['morning'],
                $afternoonStatus = $a['afternoon'],
                $nightStatus = $a['night'],
                $morningHour = $a['morning_hour'],
                $afternoonHour = $a['afternoon_hour'],
                $nightHour = $a['night_hour'],
			);
        }


        if($morningStatus == 1)
        {
            $morning = 1;

        }else if($morningStatus == 0 && $morningHour > $currentTimeHours)
        {
            $morning = 0;

        }else if($morningStatus == 0 && $morningHour == $currentTimeHours)
        {
            $morning = 2;

        }
        

        if($afternoonStatus == 1)
        {
            $afternoon = 1;

        }else if($afternoonStatus == 0 && $currentTimeHours > $afternoonHour)
        {
            $afternoon = 0;

        }else if($afternoonStatus == 0 && $afternoonHour == $currentTimeHours)
        {
            $afternoon = 2;
        }
        

        if($nightStatus == 1)
        {
            $night = 1;

        }else if($nightStatus == 0 && $currentTimeHours > $nightHour)
        {
            $night = 0;

        }else if($nightStatus == 0 && $nightHour == $currentTimeHours)
        {
            $night = 2;

        }

        return array(
            'morning' => $morning,
            'afternoon' => $afternoon,
            'night' => $night
        );
    }


    public function updateMedicineActivity($DOW, $timeOfDay, $status)
    {
        $db = Yii::$app->db;

        $updateQuery = "UPDATE medicineactivity SET  `$timeOfDay` = '$status' WHERE `DOW` = '$DOW' ";

        $data = $db->createCommand($updateQuery)->execute();

        if($data == true){
            return true;
        }else{
            return false;
        }
    }


    public function getCurrentDaySchedule($currentDay)
    {
        $db = Yii::$app->db;
        $array= []; 

        $query = "SELECT DOW, morning_availability, afternoon_availability, night_availability FROM medicineactivity WHERE DOW = '$currentDay' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                'DOW' => $a['DOW'],
                'morning' => $a['morning_availability'],
                'afternoon' => $a['afternoon_availability'],
                'night' => $a['night_availability'],
            );
        }

        if($data == true){
            return $array;
        }else{
            return false;
        }
    }


    public function getScheduleAfterTomorrow($tomorrowDay)
    {
        $db = Yii::$app->db;
        $array= []; 

        $query = "SELECT DOW, morning_availability, afternoon_availability, night_availability FROM medicineactivity WHERE DOW > '$tomorrowDay'  ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                'DOW' => $a['DOW'],
                'morning' => $a['morning_availability'],
                'afternoon' => $a['afternoon_availability'],
                'night' => $a['night_availability'],
            );
        }

        if($data == true){
            return $array;
        }else{
            return false;
        }
    }

    public function getSchedule($dow,$tod,$userID)
    {
        $db = Yii::$app->db;
        $array= []; 

        $query = "SELECT  medactivity.$dow ". 
                 "FROM `medactivity` ".
                 "INNER JOIN `medschedule` ".
                 "ON medactivity.medschedule_id = medschedule.id ".
                 "WHERE medschedule.daytimes = '$tod' ".
                 "AND medactivity.user_id = '$userID' ";

        $data = $db->createCommand($query)->query();

        foreach ($data as $a){
            $array[] = array(
                'status' => $a[$dow],
            );
        }

        if($data == true){
            return $array;
        }else{
            return false;
        }
    }

}
