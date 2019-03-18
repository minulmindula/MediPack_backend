<?php
/**
 * This is the model class for table "Role".
 *
 * @property integer $id
 * @property string $name
 *
 * @property User[] $users
 */
namespace app\models;

use app\components\db\ActiveRecord;

class Role extends ActiveRecord
{
	const SYSADMIN =  0;
	const ADMIN    = 10;
	const MANAGER  = 12;
	const APIUSER  = 20;


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'Role';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'name'], 'required'],
			[['id', 'privLevel'], 'integer'],
			[['name'], 'string', 'max' => 50],
			[['name'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'privLevel' => 'Privilege Level',
			'name' => 'Name',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['roleId' => 'id']);
	}


	// Begin: User defined methods --------------------------------------------
	public function readAll()
	{
		$data = [];
		foreach (Role::find()->where(['NOT IN', 'id', [self::SYSADMIN]])->all() as $_o) {
			$data[] = (object) $_o->attributes;
		}
		return $data;
	}
	// End: User defined methods ==============================================
}
