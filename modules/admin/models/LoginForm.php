<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * модель для авторизации в админке
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $code;
    public $action;
    public $rememberMe = true;

    private $_user = false;

    /*
     сценарии:
        default - для выдачи кода
        code - для проверки кода и авторизации
    */
    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => '{attribute} не может быть пустым'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['code', 'safe'],
            ['action', 'safe'],
            ['code', 'required', 'on' => 'code'],
            ['code', 'validateCode', 'on' => 'code'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или пароль.');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public function validateCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                $user = $user->userAR;
                $triesLeft = $user->confirm_code_tries_left;
                if ($triesLeft === 0) {
                    $this->addError($attribute, 'Вы исчерпали попытки ввода кода. Для продолжения отправьте код заново.');
                } elseif ($this->code != $user->confirm_code) {
                    $user->confirm_code_tries_left = --$triesLeft;
                    $user->save();
                    if ($triesLeft === 0) {
                        $this->addError($attribute, 'Введён неправильный код. Для продолжения необходимо отправить код заново.');
                    } else {
                        $this->addError($attribute, "Введён неправильный код. Осталось попыток: {$triesLeft}.");
                    }
                } elseif ($user->getConfirmCodeTimeDiffInSeconds() >= 60 * 60 * 24 * 3) {
                    $this->addError($attribute, 'Ваш код устарел, пожалуйста, получите новый');
                }
            } else {
                $this->addError($attribute, 'Пользователь не найден');
            }
        }
    }

    /* поиск пользователя по username */

    public function login()
    {
        if (($this->scenario == 'code') && $this->validate()) {
            $this->getUser()->userAR->login_count = (int)$this->getUser()->userAR->login_count + 1;
            // сбросить код
            $this->getUser()->userAR->confirm_code = null;
            $this->getUser()->userAR->confirm_code_time = '1970-01-01 00:00:00';
            $this->getUser()->userAR->confirm_code_tries_left = null;

            $this->getUser()->userAR->updateAttributes([
                'login_count' => $this->getUser()->userAR->login_count,
                'confirm_code' => $this->getUser()->userAR->confirm_code,
                'confirm_code_time' => $this->getUser()->userAR->confirm_code_time,
                'confirm_code_tries_left' => $this->getUser()->userAR->confirm_code_tries_left,
            ]);

            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }
}
