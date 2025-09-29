<?php

namespace app\modules\admin\models;

use app\modules\users\models\UserAR;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель реализует интерфейс IdentityInterface
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $userARModel;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->andWhere(['IN', 'status', [UserAR::STATUS_ACTIVE]])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->andWhere(['IN', 'status', [UserAR::STATUS_ACTIVE]])->one();
    }

    public static function findByEmail($username)
    {
        return static::find()->where(['email' => $username])->andWhere(['IN', 'status', [UserAR::STATUS_ACTIVE]])->one();
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'email' => 'E-mail',
            'status' => 'Статус',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /* статус по умолчанию - не активен */
            ['status', 'default', 'value' => UserAR::STATUS_INACTIVE],
            /* статус можно задать только из списка */
            ['status', 'in', 'range' => [UserAR::STATUS_ACTIVE, UserAR::STATUS_DELETED, UserAR::STATUS_INACTIVE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getUserAR()
    {
        if (!isset($this->userARModel)) {
            $this->userARModel = UserAR::findOne($this->getPrimaryKey());
        }

        return $this->userARModel;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        $this->generateAuthKey();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $auth_key = Yii::$app->security->generateRandomString();
        $check_unique = UserAR::find()->where(['auth_key' => $auth_key])->count();
        while ($check_unique) {
            $auth_key = Yii::$app->security->generateRandomString();
            $check_unique = UserAR::find()->where(['auth_key' => $auth_key])->count();
        }
        $this->updateAttributes(['auth_key' => $auth_key]);
    }
}
