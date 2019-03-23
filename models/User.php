<?php

namespace app\models;

use Yii;
use app\components\Utils;

/**
 * This is the model class for table "user".
 *
 * @property string $id System generated Id
 * @property string $device_id
 * @property string $username
 * @property string $roleName
 * @property string $firstname
 * @property string $lastname
 * @property string $surname
 * @property string $contact Mobile Number in International Format
 * @property string $password MD5 password
 * @property int $roleId
 * @property int $status User status:{0:disabled, 1:enabled, 2:temp-locked}
 * @property string $timeLastLogin Last successful login time
 * @property string $sessionId HTTP session Id
 * @property string $timeLastPswdChange Last password changed time
 * @property int $loginAttempts Unsuccessful login attempts
 * @property string $timeLocked Account locked time
 * @property string $timeCreated Record create time; Insert by code
 * @property string $userCreated Record create username (do NOT use reference)
 * @property string $timeUpdated Record modify time
 * @property string $userUpdated Record modify username (do NOT use reference)
 * @property string $prevPswds Insert by code as "[null,null,null,null,null]"; Previous N passwords; JSON array; N = number of array elements
 *
 * @property Device[] $devices
 * @property Medactivity[] $medactivities
 * @property Scheduletimes[] $scheduletimes
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord
{

    var $role4Doc = '3';
    var $role4User = '2';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'device_id', 'username', 'roleName', 'firstname', 'lastname', 'surname'], 'required'],
            [['id', 'contact'], 'integer'],
            [['timeLastLogin', 'timeLastPswdChange', 'timeLocked', 'timeCreated', 'timeUpdated'], 'safe'],
            [['device_id'], 'string', 'max' => 20],
            [['username', 'sessionId', 'userCreated', 'userUpdated'], 'string', 'max' => 30],
            [['roleName', 'firstname', 'lastname', 'surname'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 32],
            [['roleId', 'status', 'loginAttempts'], 'string', 'max' => 3],
            [['prevPswds'], 'string', 'max' => 200],
            [['id'], 'unique'],
            [['roleId'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['roleId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'System generated Id',
            'device_id' => 'Device ID',
            'username' => 'Username',
            'roleName' => 'Role Name',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'surname' => 'Surname',
            'contact' => 'Mobile Number in International Format',
            'password' => 'MD5 password',
            'roleId' => 'Role ID',
            'status' => 'User status:{0:disabled, 1:enabled, 2:temp-locked}',
            'timeLastLogin' => 'Last successful login time',
            'sessionId' => 'HTTP session Id',
            'timeLastPswdChange' => 'Last password changed time',
            'loginAttempts' => 'Unsuccessful login attempts',
            'timeLocked' => 'Account locked time',
            'timeCreated' => 'Record create time; Insert by code',
            'userCreated' => 'Record create username (do NOT use reference)',
            'timeUpdated' => 'Record modify time',
            'userUpdated' => 'Record modify username (do NOT use reference)',
            'prevPswds' => 'Insert by code as \"[null,null,null,null,null]\"; Previous N passwords; JSON array; N = number of array elements',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['device_id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedactivities()
    {
        return $this->hasMany(Medactivity::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScheduletimes()
    {
        return $this->hasMany(Scheduletimes::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'roleId']);
    }


    //Authenticate user
    public function authnz($username,$password)
    {
       
        $db = Yii::$app->db;
        $array = [];

        $passHash = Utils::encryptPassword($password);

        $query = "SELECT * FROM ".$this->tableName()." WHERE username = :username AND password = :pass AND roleId = :roleId ";

        $stmt = $db->createCommand($query)
                ->bindValue(':username', $username)
                ->bindValue(':pass', $passHash)
                ->bindValue(':roleId', '2')
                ->query();
      
        foreach ($stmt as $a){
            $array[] = array(
                'userid' => $a['userid'],
                'roleID' => $a['roleId'], 
                'username' => $a['username'],
                'firstname' => $a['firstname'],
                'lastname' => $a['lastname'],
                'contact' => $a['contact'],
                'status' => $a['status']
            );
        }
        
        return $array;

    }


    //Authentication for doctor
    public function authnzDoc($username, $password)
    {
        $db = Yii::$app->db;
        $array = [];

        $passHash = Utils::encryptPassword($password);

        $query = "SELECT * FROM ".$this->tableName()." WHERE username = :username AND password = :pass AND roleId = :roleId ";

        $stmt = $db->createCommand($query)
            ->bindValue(':username', $username)
            ->bindValue(':pass', $passHash)
            ->bindValue(':roleId', '3')
            ->query();

        foreach ($stmt as $a){
            $array[] = array(
                'userid' => $a['userid'],
                'roleID' => $a['roleId'],
                'username' => $a['username'],
                'firstname' => $a['firstname'],
                'lastname' => $a['lastname'],
                'contact' => $a['contact'],
                'status' => $a['status']
            );
        }

        return $array;
    }

    //Register app user
    public function registerAppUser($firstname,$lastname,$contact,$email,$password)
    {
        $db = Yii::$app->db;

        $passHash = Utils::encryptPassword($password);

        $username = strtolower($firstname);

        //Check for data in DB
        $validateQuery = "SELECT firstname, lastname FROM user WHERE `firstname` = '$firstname' AND `lastname` = '$lastname' ";
        $validate = $db->createCommand($validateQuery)->query();

        if(count($validate) > 0)
        {
            return false;

        }else{
            $query = "INSERT INTO " . $this->tableName() . 
            "(`userid`,`username`, `firstname`, `lastname`, `contact`, `password`, `roleId`, `userCreatedTime`) ".
            "VALUES (:id,:username,:firstname,:lastname,:contact,:pass,:roleId,:userCreated) " ;
    
            $stmt = $db->createCommand($query)
                    ->bindValue(':id', Utils::getUniqUserId())
                    ->bindValue(':username', $username . '_' .date('dhs'))
                    ->bindValue(':firstname', $firstname)
                    ->bindValue(':lastname', $lastname)
                    ->bindValue(':contact', $contact)
                    ->bindValue(':pass', $passHash)
                    ->bindValue(':roleId', $this->role4User)
                    ->bindValue(':userCreated', date('Y-m-d'))
                    ->execute();
            
            return true;

        }
        
    }

    //Register doctor
    public function registerDoc($firstname,$lastname,$contact,$email,$password,$author)
    {
        $db = Yii::$app->db;

        $passHash = Utils::encryptPassword($password);

        $username = strtolower($firstname);

        //Check for data in DB
        $validateQuery = "SELECT firstname, lastname FROM user WHERE `firstname` = '$firstname' AND `lastname` = '$lastname' ";
        $validate = $db->createCommand($validateQuery)->query();

        if(count($validate) > 0)
        {
            return false;

        }else{

            if($author == 'admin')
            {
                $query = "INSERT INTO " . $this->tableName() . 
                "(`userid`,`username`, `firstname`, `lastname`, `contact`, `password`, `roleId`, `userCreatedTime`) ".
                "VALUES (:id,:username,:firstname,:lastname,:contact,:pass,:roleId,:userCreatedTime) " ;
        
                $stmt = $db->createCommand($query)
                        ->bindValue(':id', Utils::getUniqUserId())
                        ->bindValue(':username', $username . '_' .date('dhs'))
                        ->bindValue(':firstname', $firstname)
                        ->bindValue(':lastname', $lastname)
                        ->bindValue(':contact', $contact)
                        ->bindValue(':pass', $passHash)
                        ->bindValue(':roleId', $this->role4Doc)
                        ->bindValue(':userCreatedTime', date("Y-m-d H-i-s"))
                        ->execute();
                
                return true;
            }else{
                return "You are not authorized to perform this action!";
            }

        }

        
    }


    public function showOnlineDoctors()
    {
        $db = Yii::$app->db;
        $array = [];

        $query = "SELECT * FROM user WHERE `roleId` = '3' ";

        $stmt = $db->createCommand($query)->query();

        foreach($stmt as $row)
        {
            $array[] = array(
                'userid' => $row['userid'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'status' => $row['status']
            );
        }

        return $array;
    }


}
