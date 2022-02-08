<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property  int   id
 * @property string login
 * @property string password
 * @property string auth_key
 * @property string status
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($this->password);
        $this->generateAuthKey();
        return parent::beforeSave($insert);
    }

    public function rules()
    {
        return [
            [['id'], 'unique'],
            [['login'], 'unique', 'message' => 'Такой логин уже используется. Попробуйте другой'],
            [['password'], 'string', 'min' => 6, 'tooShort' => 'Минимальная длина строки - 6 символов'],
            [['status'], 'string'],
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $this->getAuthKey();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }
}