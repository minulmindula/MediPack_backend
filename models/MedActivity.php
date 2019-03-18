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
    public function getNextMedTime($currentTime)
    {
        $db = Yii::$app->db;
        $array = [];

        $morningHour = null;
        $afternoonHour = null;
        $nightHour = null;

        $query = "SELECT * FROM scheduletimes";

        $stmt = $db->createCommand($query)->query();

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

    }

    
}
