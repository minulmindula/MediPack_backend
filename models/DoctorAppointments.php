<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_appointments".
 *
 * @property int $id
 * @property string $doctorID
 * @property string $userId
 * @property string $appointmentTime
 * @property string $appointmentLocation
 * @property string $appointmentHospitalName
 */
class DoctorAppointments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_appointments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appointmentTime'], 'safe'],
            [['doctorID', 'userId', 'appointmentLocation', 'appointmentHospitalName'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doctorID' => 'Doctor ID',
            'userId' => 'User ID',
            'appointmentTime' => 'Appointment Time',
            'appointmentLocation' => 'Appointment Location',
            'appointmentHospitalName' => 'Appointment Hospital Name',
        ];
    }

    //Get Doctor appointments
    public function getDocAppointment($username, $date)
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT * FROM ".$this->tableName(). " WHERE username = :userid AND appointmentDate = :aptDate ";

        $stmt = $db->createCommand($query)
            ->bindValue(':userid', $username)
            ->bindValue(':aptDate', $date)
            ->query();

        foreach ($stmt as $a){
            $array[] = array(
                'doctorID' => $a['doctorID'],
                'username' => $a['username'],
                'appointmentTime' => $a['appointmentTime'],
                'appointmentDate' => $a['appointmentDate'],
                'appointmentLocation' => $a['appointmentLocation'],
                'appointmentHospitalName' => $a['appointmentHospitalName']
            );
        }

        return $array;
    }

}
