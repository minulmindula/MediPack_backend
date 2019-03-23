<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "medactivity".
 *
 * @property int $id
 * @property string $medschedule_id
 * @property string $user_id
 * @property int $monday
 * @property int $tuesday
 * @property int $wednesday
 * @property int $thursday
 * @property int $friday
 * @property int $saturday
 * @property int $sunday
 *
 * @property Medschedule $medschedule
 * @property User $user
 */
class MedActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'medactivity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['medschedule_id', 'user_id'], 'required'],
            [['medschedule_id', 'user_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 'integer'],
            [['medschedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medschedule::className(), 'targetAttribute' => ['medschedule_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'medschedule_id' => 'Medschedule ID',
            'user_id' => 'User ID',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedschedule()
    {
        return $this->hasOne(Medschedule::className(), ['id' => 'medschedule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    //Get next medicine time from current time
    public function getNextMedTime($currentTime,$userid)
    {
        $db = Yii::$app->db;
        $array = [];

        $morningHour = null;
        $afternoonHour = null;
        $nightHour = null;

        $query = "SELECT * FROM scheduletimes WHERE `user_id` = :userid ";

        $stmt = $db->createCommand($query)
                   ->bindValue(':userid', $userid)                   
                   ->query();

        if(count($stmt) > 0){

            foreach($stmt as $times)
            {
                $array[] = array(
                    $morningHour = (int) $times['morning_time'],
                    $afternoonHour = (int) $times['afternoon_time'],
                    $nightHour = (int) $times['night_time'],
                );
            }
    
            if($currentTime <= $morningHour)
            {
                return array('time' => $morningHour);
    
            }else if($currentTime >= $morningHour && $currentTime <= $afternoonHour)
            {
                return array('time' => $afternoonHour);
    
            }else if($currentTime >= $afternoonHour && $currentTime <= $nightHour)
            {
                return array('time' => $nightHour);
    
            }else if($currentTime >= $nightHour)
            {
                return array('time' => $morningHour);;
            }

        }else{
            return false;
        }

    }


    //Get summary of the day
    public function getSummaryOfDay($userid,$day,$time)
    {
        $db = Yii::$app->db;
        $array = [];

        $validateQuery = "SELECT firstname, lastname FROM user WHERE `userid` = :userid ";
        $validate = $db->createCommand($validateQuery)
                       ->bindValue(':userid', $userid)
                       ->query();

        if(count($validate) > 0)
        {

            $morning = '';
            $afternoon = '';
            $night = '';

            $mor_stat = '';
            $aft_stat = '';
            $night_stat = '';

            $mor_time = '';
            $aft_time = '';
            $night_time = '';

            $query =  "SELECT m.user_id, d.days, m.morningStatus, m.afternoonStatus, m.nightStatus, s.morning_time, ".
                      "s.afternoon_time, s.night_time FROM medactivity m ".
                      "INNER JOIN daysofweek d ON m.dow_id = d.id ".
                      "INNER JOIN scheduletimes s on m.user_id = s.user_id ".
                      "WHERE m.user_id = :userid AND m.dow_id = :day ";

            $data = $db->createCommand($query)
                ->bindValue(':userid', $userid)
                ->bindValue(':day', $day)
                ->query();

            foreach($data as $row)
            {
                $array[] = array(
                    $mor_stat = $row['morningStatus'],
                    $aft_stat = $row['afternoonStatus'],
                    $night_stat = $row['nightStatus'],
                    $mor_time = $row['morning_time'],
                    $aft_time = $row['afternoon_time'],
                    $night_time = $row['night_time']
                );
            }

            if($mor_stat == 1)
            {
                $morning = 1;
            }else if($mor_stat == 0 && $mor_time > $time)
            {
                $morning = 0;

            }else if($mor_stat == 0 && $mor_time == $time)
            {
                $morning = 2;

            }

            if($aft_stat == 1)
            {
                $afternoon = 1;
            }else if($aft_stat == 0 && $aft_time > $time)
            {
                $afternoon = 0;

            }else if($aft_stat == 0 && $aft_time == $time)
            {
                $afternoon = 2;

            }

            if($night_stat == 1)
            {
                $night = 1;
            }else if($night_stat == 0 && $night_time > $time)
            {
                $night = 0;

            }else if($night_stat == 0 && $night_time == $time)
            {
                $night = 2;

            }

            return array(
                'morning' => $morning,
                'afternoon' => $afternoon,
                'night' => $night
            );


        }else{
            return false;
        }


    }


    public function getWeeklySchedule($userid)
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT d.days, m.morningStatus, m.afternoonStatus, m.nightStatus FROM medactivity m ".
                 "INNER JOIN daysofweek d ".
                 "ON m.dow_id = d.id ".
                 "WHERE m.user_id = :userid ".
                 "ORDER BY m.dow_id ASC ";

        $stmt = $db->createCommand($query)
                   ->bindValue(':userid', $userid)
                   ->query();

        foreach($stmt as $row)
        {
            $array[] = array(
                'day' => $row['days'],
                'morning' => $row['morningStatus'],
                'afternoon' => $row['afternoonStatus'],
                'night' => $row['nightStatus']
            );
        }

        return $array;
    }


    public function getSingledaySchedule($userid, $day)
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT d.days, m.morningStatus, m.afternoonStatus, m.nightStatus FROM medactivity m ".
                 "INNER JOIN daysofweek d ".
                 "ON m.dow_id = d.id ".
                 "WHERE m.user_id = :userid AND d.days = :day ";

        $stmt = $db->createCommand($query)
                   ->bindValue(':userid', $userid)
                   ->bindValue(':day', $day)
                   ->query();

        foreach($stmt as $row)
        {
            $array[] = array(
                'day' => $row['days'],
                'morning' => $row['morningStatus'],
                'afternoon' => $row['afternoonStatus'],
                'night' => $row['nightStatus']
            );
        }

        return $array;
    }


    
}
