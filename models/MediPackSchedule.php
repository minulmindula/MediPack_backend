<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "medipackschedule".
 *
 * @property int $id
 * @property string $deviceID
 * @property string $DOW
 * @property int $morning
 * @property int $afternoon
 * @property int $night
 */
class MediPackSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'medipackschedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DOW', 'morning', 'afternoon', 'night'], 'required'],
            [['morning', 'afternoon', 'night'], 'integer'],
            [['deviceID'], 'string', 'max' => 20],
            [['DOW'], 'string', 'max' => 10],
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
        ];
    }


    


    

}
