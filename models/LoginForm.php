<?php

namespace app\models;

use app\modules\admin\models\User;
use app\modules\formagree\models\Formagree;
use app\modules\users\models\UserAR;
use app\modules\usersigns\models\Usersigns;
use Yii;

class LoginForm extends \app\modules\admin\components\UserModel
{
    public $email;
    public $password;
    public $action;
    public $code;
    public $rememberMe = true;
    public $agreements;
    private $_user = false;

    public function sanitize($data, $formName = null)
    {
        return parent::sanitize($data, $formName = null);
    }

    public function rules()
    {
        $return = [
            // username and password are both required
            [['email'], 'required'],
            ['rememberMe', 'boolean'],

            [['code', 'password', 'action'], 'safe'],

            ['password', 'required', 'on' => 'password'],
            [['password'], 'validatePassword', 'on' => ['password', 'code', 'regCode']],

            [['code'], 'required', 'on' => ['code', 'regCode']],
            [['code'], 'validateCode', 'on' => ['code', 'regCode']],

            [['agreements'], 'agreement', 'skipOnEmpty' => false],
        ];
        return array_merge(parent::rules(), $return);
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
            'password' => 'Пароль',
            'rememberMe' => 'Оставаться в системе',
            'code' => 'Проверочный код',
        ];
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

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->where(['email' => $this->email])->one();
        }
        return $this->_user;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (empty($user->password_hash) || !$user->use_password) {
                return true;
            }
            if ($user) {
                if ($user->validatePassword($this->password)) {
                    switch ($user->status) {
                        case UserAR::STATUS_DELETED:
                            $this->addError($attribute, 'Аккаунт удален. Для восстановления доступа свяжитесь со службой поддержки пользователей help@ast-academy.ru');
                            break;
                        case UserAR::STATUS_INACTIVE:
                        case UserAR::STATUS_ACTIVE:
                            /* доступ разрешен */
                            break;
                        default:
                            $this->addError($attribute, 'Доступ запрещен. Свяжитесь со службой поддержки пользователей help@ast-academy.ru');
                            break;
                    }
                } else {
                    $this->addError($attribute, 'Неверный пароль');
                }
            } else {
                $this->addError($attribute, 'Пользователь с таким email не найден');
            }
        }
    }

    /* поиск пользователя по username */

    public function login()
    {
        if (($this->scenario == 'code' || $this->scenario == 'regCode') && $this->validate()) {
            $user = $this->getUser();
            if ($user) {
                if (!empty($this->agrees)) {
                    foreach ($this->agrees as $agree) {
                        if ($this->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $user->id;
                            $agree_sign->form_model = 'registered_fizusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                $userAR = $user->userAR;
                if ($userAR->status == UserAR::STATUS_INACTIVE) {
                    $userAR->status = UserAR::STATUS_ACTIVE;
                }
                $userAR->login_count = (int)$userAR->login_count + 1;
                // сбросить код
                $userAR->confirm_code = null;
                $userAR->confirm_code_time = '1970-01-01 00:00:00';
                $userAR->confirm_code_tries_left = null;

                $userAR->updateAttributes([
                    'status' => $userAR->status,
                    'login_count' => $userAR->login_count,
                    'confirm_code' => $userAR->confirm_code,
                    'confirm_code_time' => $userAR->confirm_code_time,
                    'confirm_code_tries_left' => $userAR->confirm_code_tries_left,
                ]);

                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
            return false;

        }
        return false;
    }

    public function registerUser()
    {
        // создать пользователя
        $new_user = new UserAR(['forceInit' => true]);
        $new_user->username = 'user_' . md5(trim($this->email)) . time();
        $new_user->email = trim($this->email);
        $new_user->status = UserAR::STATUS_INACTIVE;
        $new_user->self_registered = 1;
        $new_user->role = 'fizusr';

        do {
            $bytes = random_bytes(10);
            $url = bin2hex($bytes);
        } while (UserAR::find()->where(['url' => $url])->exists());

        $new_user->url = $url;

        if ($new_user->save()) {
            return $new_user;
        }
        return null;
    }

    public function agreement($attribute, $params)
    {
        $agrees = $this->getAgrees();
        if ($this->scenario == 'regCode' && !empty($agrees)) {
            foreach ($agrees as $agree) {
                if (!isset($this->{$attribute}[$agree->id]) or ($this->{$attribute}[$agree->id] != 1)) {
                    $this->addError($attribute, 'Для продолжения необходимо подтвердить согласие с условиями.');
                }
            }
        }
    }

    public function getAgrees()
    {
        return Formagree::find()->where(['form_type' => Formagree::TYPE_REGFIZUSR, 'visible' => 1])->orderBy(['order' => SORT_ASC])->all();
    }

    public function hasUserAgrees(UserAR $userAR)
    {
        $agrees = $this->getAgrees();
        foreach ($agrees as $key => $agree) {
            // если соглашение уже подписано пользователем - исключаем из списка
            $usersign = Usersigns::find()->where(['user_id' => $userAR->id, 'form_model' => Formagree::TYPE_REGFIZUSR, 'agreement_id' => $agree->id])->one();
            if ($usersign) {
                // исключить из списка
                unset($agrees[$key]);
            }
        }
        return !empty($agrees);
    }
}
