<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hospitaldetails".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $emergencyContact
 * @property string $hotline
 */
class Hospitaldetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hospitaldetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'emergencyContact', 'hotline'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'emergencyContact' => 'Emergency Contact',
            'hotline' => 'Hotline',
        ];
    }


    public function getHospitalEmg()
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT * FROM `hospitaldetails` ";

        $stmt = $db->createCommand($query)->query();

        foreach($stmt as $row)
        {
            $array[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'emergencyContact' => $row['emergencyContact'],
                'address' => $row['address'],
                'logoURL' => $row['logoURL']
            );
        }

        return $array;
    }
}
