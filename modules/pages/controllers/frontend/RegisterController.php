<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\models\ConfirmCode;
use app\models\FirstLoginForm;
use app\models\LoginForm;
use app\models\Regexpert as RegexpertForm;
use app\models\Regfizusr as RegfizusrForm;
use app\models\Regservicefizusr as RegservicefizusrForm;
use app\models\Regserviceurusr as RegserviceurusrForm;
use app\models\Regurusr as RegurusrForm;
use app\models\ResetpassForm;
use app\modules\order\models\Order;
use app\modules\pages\models\Changepasssuccess;
use app\modules\pages\models\ProfileCafedra;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\Regexpertactivateemail;
use app\modules\pages\models\Regfizusr;
use app\modules\pages\models\Regfizusractivateemail;
use app\modules\pages\models\Regfizusrsuccess;
use app\modules\pages\models\Regurusractivateemail;
use app\modules\pages\models\Resetpass;
use app\modules\pages\models\SelectPayment;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\queries\models\Queries;
use app\modules\service\models\Service;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RegisterController extends DeepController
{
    /* СТРАНИЦЫ МОДУЛЯ РЕГИСТРАЦИИ */

    public function actionIndex($model)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* редирект на регистрацию физлица */
        $regfizusrPage = Regfizusr::find()->where(['model' => Regfizusr::class, 'visible' => 1])->one();
        if ($regfizusrPage) {
            return $this->redirect($regfizusrPage->getUrlPath());
        }
        //
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionExpert($model)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $modelform = new RegexpertForm();
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionFizusr($model)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $modelform = new RegfizusrForm();
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionUrusr($model)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $modelform = new RegurusrForm();
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionServiceorder($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        $service = Service::find()
            ->where(['id' => (int)$get['service'], 'type' => Service::TYPE_TYPICAL, 'visible' => 1])
            ->andWhere(['IN', 'status', Service::CATALOG_VISIBLE_STATUSES])->one();
        if (!$service) {
            /* если услуга не найдена - редирект на каталог услуг */
            $page = \app\modules\pages\models\TargetAudiencePage::find()->where(['model' => \app\modules\pages\models\TargetAudiencePage::class, 'visible' => 1])->one();
            return $this->redirect($page->getUrlPath());
        }

        if ($service->user->status != UserAR::STATUS_ACTIVE) {
            /* если проблемы с услугой - редирект на страницу услуги */
            return $this->redirect($service->getUrlPath());
        }
        if (!in_array($service->user->role, ['expert', 'exporg'])) {
            /* если проблемы с услугой - редирект на страницу услуги */
            return $this->redirect($service->getUrlPath());
        }

        $this->setMeta($model);

        $modelform_fiz = new RegservicefizusrForm();
        $modelform_fiz->service_id = $service;
        $modelform_ur = new RegserviceurusrForm();
        $modelform_ur->service_id = $service;
        return $this->render($model->view, ['model' => $model, 'modelform_fiz' => $modelform_fiz, 'service' => $service]);
    }

    public function actionServicequery($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        $service = Service::find()
            ->where(['id' => (int)$get['service'], 'type' => Service::TYPE_CUSTOM, 'visible' => 1])
            ->andWhere(['IN', 'status', Service::CATALOG_VISIBLE_STATUSES])
            ->one();
        if (!$service) {
            /* если услуга не найдена - редирект на каталог услуг */
            $page = \app\modules\pages\models\TargetAudiencePage::find()->where(['model' => \app\modules\pages\models\TargetAudiencePage::class, 'visible' => 1])->one();
            return $this->redirect($page->getUrlPath());
        }

        if ($service->user->status != UserAR::STATUS_ACTIVE) {
            /* если проблемы с услугой - редирект на страницу услуги */
            return $this->redirect($service->getUrlPath());
        }
        if (!in_array($service->user->role, ['expert', 'exporg'])) {
            /* если проблемы с услугой - редирект на страницу услуги */
            return $this->redirect($service->getUrlPath());
        }

        $this->setMeta($model);

        $modelform_fiz = new RegservicefizusrForm();
        $modelform_fiz->service_id = $service;
        $modelform_ur = new RegserviceurusrForm();
        $modelform_ur->service_id = $service;
        return $this->render($model->view, ['model' => $model, 'modelform_fiz' => $modelform_fiz, 'service' => $service]);
    }

    public function actionRegsuccess($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionChangepasssuccess($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionActivate($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $get = Yii::$app->request->get();
        Yii::$app->view->title = $model->getNameForView();
        $message = '';
        $render_template = 'activate';
        if (isset($get['key']) && isset($get['hash'])) {
            $user = UserAR::find()->where(['password_reset_token' => $get['key']])->one();
            if ($user) {
                if ($get['hash'] == md5($user->email)) {
                    if ($user->status == UserAR::STATUS_INACTIVE) {
                        $user->password_reset_token = md5(time());
                        $user->login_count = (int)$user->login_count + 1;
                        switch ($user->role) {
                            case 'urusr':
                                $user->status = UserAR::STATUS_ACTIVE;
                                // $user->sendModeratorEmail('new_uruser');
                                $user->sendWelcomeEmail('welcome_uruser');
                                $render_template = 'activate_to_events';
                                break;
                            case 'fizusr':
                                $render_template = 'activate_to_events';
                            // no break
                            default:
                                $user->status = UserAR::STATUS_ACTIVE;
                                // if ($user->pretendent) {
                                //    $user->sendModeratorEmail('new_pretendent');
                                // } else {
                                //    $user->sendModeratorEmail('new_fizuser');
                                // }
                                $user->sendWelcomeEmail('welcome_fizuser');
                                break;
                        }
                        if ($user->save()) {
                            // залогинить и запомнить
                            $identity = \app\modules\admin\models\User::find()->where(['email' => $user->email])->one();
                            Yii::$app->user->login($identity, 3600 * 24 * 30);
                            $message = $model->content;
                            // если задан адрес возврата - то редирект по адресу.
                            if (!empty($get['return_url'])) {
                                $ret_url = base64_decode($get['return_url']);
                                return $this->redirect($ret_url);
                            }
                        } else {
                            $message = '<p>Невозможно активировать аккаунт. ' . \app\helpers\MainHelper::getHelpText();
                        }
                    } else {
                        $message = '<p>Пользователь уже активирован. ' . \app\helpers\MainHelper::getHelpText();
                    }
                } else {
                    $message = '<p>Ссылка недействительна: параметры заданы некорректно. ' . \app\helpers\MainHelper::getHelpText();
                }
            } else {
                $message = '<p>Ссылка недействительна: ключ задан некорректно. ' . \app\helpers\MainHelper::getHelpText();
            }
        } else {
            $message = '<p>Ссылка недействительна: параметры не заданы. ' . \app\helpers\MainHelper::getHelpText();
        }
        return $this->render($render_template, ['message' => $message, 'model' => $model]);
    }

    public function actionLogin($model)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $modelform = new LoginForm();
        $modelform->action = 'default';
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    /* страница сброса пароля */
    public function actionSetnewpass($model, $step = 'step1', $hash = '')
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $modelform = new ResetpassForm();
        if (!in_array($step, array_keys($modelform->scenarios()))) {
            return $this->render($model->view . '_error');
        }
        $modelform->scenario = $step;
        if ($step != 'step1') {
            if ($hash) {
                $decodedString = base64_decode($hash);
                $parts = explode('#', $decodedString);
                $modelform->email = $parts[0];
                if (count($parts) > 1) {
                    $modelform->code = $parts[1];
                }
                if (!$modelform->getUser()) {
                    return $this->render($model->view . '_error');
                }
            } else {
                return $this->render($model->view . '_error');
            }
        }
        return $this->render($model->view . '_' . $step, ['model' => $model, 'modelform' => $modelform]);
    }

    /* страница первого входа */
    public function actionFirstlogin($model)
    {
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $get = Yii::$app->request->get();
        Yii::$app->view->title = $model->getNameForView();
        $message = '';
        if (isset($get['key']) && isset($get['hash'])) {
            $user = UserAR::find()->where(['first_login_hash' => $get['key']])->one();
            if ($user) {
                if ($get['hash'] == md5($user->email)) {
                    if (in_array($user->status, [UserAR::STATUS_DELETED])) {
                        $message = '<p>Пользователь удален.</p>';
                    } else {
                        $message = null;
                        $modelform = new FirstLoginForm();
                        $modelform->key = $get['key'];
                        $modelform->hash = $get['hash'];
                    }
                } else {
                    $message = '<p>Ссылка недействительна: параметры заданы некорректно. ' . \app\helpers\MainHelper::getHelpText();
                }
            } else {
                $message = '<p>Ссылка недействительна: ключ задан некорректно. ' . \app\helpers\MainHelper::getHelpText();
            }
        } else {
            $message = '<p>Ссылка недействительна: параметры не заданы. ' . \app\helpers\MainHelper::getHelpText();
        }
        return $this->render($model->view, ['message' => $message, 'model' => $model, 'modelform' => $modelform]);
    }

    public function actionConfirmCode($model, $hash)
    {
        $this->layout = '@app/views/layouts/new-reg';
        /* редирект на профиль, если авторизован */
        if (!Yii::$app->user->isGuest) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $email = base64_decode($hash);
        $new_user = UserAR::find()->where(['email' => $email])->one();
        if (!$new_user) {
            return $this->render($model->view . '_error', [
                'error' => 'Пользователь с таким email не найден.'
            ]);
        }
        if ($new_user->status == UserAR::STATUS_DELETED) {
            return $this->render($model->view . '_error', [
                'error' => 'Данный пользователь удален. Свяжитесь со службой поддержки пользователей help@ast-academy.ru',
            ]);
        }
        $modelform = new ConfirmCode();
        $modelform->email = $email;
        return $this->render($model->view . '_form', [
            'modelform' => $modelform,
            'reset_time' => max(UserAR::TIME_TO_CONFIRM_CODE_EMAIL - $new_user->getConfirmCodeTimeDiffInSeconds(), 0),
            'resend_timer' => UserAR::TIME_TO_CONFIRM_CODE_EMAIL,
        ]);
    }


    /* СТРАНИЦЫ АПИ */

    public function actionSaveexpert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Эксперта */

        $modelform = new RegexpertForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // создать пользователя
            $new_user = new UserAR(['forceInit' => true]);
            $new_user->username = 'user_' . md5(trim($modelform->email)) . time();
            $new_user->status = UserAR::STATUS_INACTIVE;
            $new_user->email = trim($modelform->email);
            $new_user->self_registered = 1;
            $new_user->setPassword($modelform->password);
            $new_user->profile->about_myself = $modelform->about_myself;
            $new_user->profile->name = $modelform->name;
            $new_user->profile->surname = $modelform->surname;
            $new_user->profile->patronymic = $modelform->patronymic;
            $new_user->profile->phone = $modelform->phone;
            /* текущий город */
            $new_user->profile->city_id = $this->city->id;
            $new_user->pretendent = 1;
            $new_user->role = 'fizusr';
            if ($new_user->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $new_user->id;
                            $agree_sign->form_model = 'registered_fizusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                $activate_page = Regexpertactivateemail::find()->where(['model' => Regexpertactivateemail::class, 'visible' => 1])->one();
                if ($activate_page) {
                    if ($new_user->generateNewCodeAndSend()) {
                        return [
                            'status' => 'success',
                            'send_target' => 'register_expert_success',
                            'redirect_to' => $activate_page->getUrlPath(['hash' => base64_encode($new_user->email)]),
                            'message' => 'Вы успешно зарегистрировались',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Период для отправки нового письма для данного пользователя ещё не истёк',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Продолжение регистрации недоступно. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSavefizusr()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Физлицо */

        $modelform = new RegfizusrForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // создать пользователя
            $new_user = new UserAR(['forceInit' => true]);
            $new_user->username = 'user_' . md5(trim($modelform->email)) . time();
            $new_user->status = UserAR::STATUS_INACTIVE;
            $new_user->email = trim($modelform->email);
            $new_user->self_registered = 1;
            $new_user->setPassword($modelform->password);
            $new_user->profile->name = $modelform->name;
            $new_user->profile->surname = $modelform->surname;
            // $new_user->profile->patronymic = $modelform->patronymic;
            $new_user->profile->phone = $modelform->phone;
            $new_user->role = 'fizusr';
            if ($new_user->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $new_user->id;
                            $agree_sign->form_model = 'registered_fizusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }

                $activate_page = Regfizusractivateemail::find()->where(['model' => Regfizusractivateemail::class, 'visible' => 1])->one();
                if ($activate_page) {
                    if ($new_user->generateNewCodeAndSend()) {
                        return [
                            'status' => 'success',
                            'send_target' => 'register_fizusr_success',
                            'redirect_to' => $activate_page->getUrlPath(['hash' => base64_encode($new_user->email)]),
                            'message' => 'Вы успешно зарегистрировались',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Период для отправки нового письма для данного пользователя ещё не истёк',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Продолжение регистрации недоступно. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSaveurusr()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Юрлицо */

        $modelform = new RegurusrForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // создать пользователя
            $new_user = new UserAR(['forceInit' => true]);
            $new_user->username = 'user_' . md5(trim($modelform->email)) . time();
            $new_user->status = UserAR::STATUS_INACTIVE;
            $new_user->email = trim($modelform->email);
            $new_user->setPassword($modelform->password);
            $new_user->self_registered = 1;
            $new_user->profile->name = $modelform->name;
            $new_user->profile->surname = $modelform->surname;
            $new_user->profile->patronymic = $modelform->patronymic;
            $new_user->profile->organization_name = $modelform->organization_name;
            $new_user->profile->office = $modelform->office;
            $new_user->profile->phone = $modelform->phone;
            $new_user->profile->city_id = $modelform->city_id;
            $new_user->organization->inn = $modelform->inn;
            $new_user->role = 'urusr';
            if ($new_user->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $new_user->id;
                            $agree_sign->form_model = 'registered_urusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }

                $activate_page = Regurusractivateemail::find()->where(['model' => Regurusractivateemail::class, 'visible' => 1])->one();
                if ($activate_page) {
                    if ($new_user->generateNewCodeAndSend()) {
                        return [
                            'status' => 'success',
                            'send_target' => 'activate_urusr_success',
                            'redirect_to' => $activate_page->getUrlPath(['hash' => base64_encode($new_user->email)]),
                            'message' => 'Вы успешно зарегистрировались',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Период для отправки нового письма для данного пользователя ещё не истёк',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Продолжение регистрации недоступно. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSaveorderfiz()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Физлицо */

        $modelform = new RegservicefizusrForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // создать пользователя
            $new_user = new UserAR(['forceInit' => true]);
            $new_user->username = 'user_' . md5(trim($modelform->email)) . time();
            $new_user->status = UserAR::STATUS_INACTIVE;
            $new_user->email = trim($modelform->email);
            $new_user->self_registered = 1;
            $new_user->registered_order = 1;
            $new_user->setPassword($modelform->password);
            $new_user->profile->name = $modelform->name;
            $new_user->profile->surname = $modelform->surname;
            // $new_user->profile->patronymic = $modelform->patronymic;
            $new_user->profile->phone = $modelform->phone;
            $new_user->profile->city_id = $modelform->city_id;
            $new_user->role = 'fizusr';
            if ($new_user->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $new_user->id;
                            $agree_sign->form_model = 'registered_fizusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                // создать заказ
                $ret_error = [];
                // находим услугу
                $service = Service::find()->where(['id' => (int)$modelform->service_id, 'type' => Service::TYPE_TYPICAL, 'visible' => 1, 'vis_fiz' => 1])
                    ->andWhere(['IN', 'status', Service::CATALOG_VISIBLE_STATUSES])->one();
                if (!$service) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if ($service->user->status != UserAR::STATUS_ACTIVE) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if (!in_array($service->user->role, ['expert', 'exporg'])) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                // TODO: проверить наличие голосований
                /* если мы добрались до сюда, значит все ок, создаем заказ и редиректим пользователя на страницу оплаты */
                $new_order = new Order();
                $new_order->user_id = $new_user->id;
                $new_order->executor_id = $service->user->id;
                $new_order->service_id = $service->id;
                $new_order->name = 'Заказ типовой услуги';
                $new_order->service_name = $service->name;
                $new_order->service_descr = $service->description;
                // рассчет в рублях
                $new_order->price = $service->price;
                $new_order->status = Order::STATUS_NEW;
                $new_order->is_payed = 0;
                $new_order->visible = 1;

                $payment_url = false;

                if ($new_order->save()) {
                    // добавляем в историю событие создания заказа
                    $new_order->newEvent()->add();
                    // страница выбора способа оплаты заказа
                    $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                    $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $new_order->id]) : false;
                } else {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if (empty($ret_error) && $payment_url) {
                    // заказ успешно создан
                    $new_user->sendActivateOrderEmail($payment_url);
                } else {
                    // по стандартному сценарию регистрации
                    $new_user->sendActivateEmail();
                }
                if (!empty($ret_error)) {
                    return $ret_error;
                }

                $success_page = Regfizusrsuccess::find()->where(['model' => Regfizusrsuccess::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $success_page->getUrlPath(),
                    'message' => 'Вы успешно зарегистрировались',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSavequeryfiz()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Физлицо */

        $modelform = new RegservicefizusrForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // создать пользователя
            $new_user = new UserAR(['forceInit' => true]);
            $new_user->username = 'user_' . md5(trim($modelform->email)) . time();
            $new_user->status = UserAR::STATUS_INACTIVE;
            $new_user->email = trim($modelform->email);
            $new_user->self_registered = 1;
            $new_user->registered_order = 1;
            $new_user->setPassword($modelform->password);
            $new_user->profile->name = $modelform->name;
            $new_user->profile->surname = $modelform->surname;
            // $new_user->profile->patronymic = $modelform->patronymic;
            $new_user->profile->phone = $modelform->phone;
            $new_user->profile->city_id = $modelform->city_id;
            $new_user->role = 'fizusr';
            if ($new_user->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $new_user->id;
                            $agree_sign->form_model = 'registered_fizusr';
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                // создать заказ
                $ret_error = [];
                // находим услугу
                $service = Service::find()
                    ->where(['id' => (int)$modelform->service_id, 'type' => Service::TYPE_CUSTOM, 'visible' => 1, 'vis_fiz' => 1])
                    ->andWhere(['IN', 'status', Service::CATALOG_VISIBLE_STATUSES])
                    ->one();
                if (!$service) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if ($service->user->status != UserAR::STATUS_ACTIVE) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if (!in_array($service->user->role, ['expert', 'exporg'])) {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'Заказ услуги не возможен, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }

                $new_query = new Queries();
                $new_query->user_id = $new_user->id;
                $new_query->executor_id = $service->user->id;
                $new_query->service_id = $service->id;
                $new_query->name = 'Запрос на индивидуальную услугу';
                $new_query->service_name = $service->name;
                $new_query->service_descr = $service->description;
                $new_query->status = Queries::STATUS_NEW;
                $new_query->user_comment = $modelform->comment;
                $new_query->visible = 1;
                if ($new_query->save()) {
                    $new_query->setQueryNum();
                    // добавляем в историю событие создания заказа
                    $new_query->newEvent()->add();
                } else {
                    $ret_error = [
                        'status' => 'fail',
                        'message' => 'При создании запроса возникли ошибки, но вам на почту отправлена ссылка на подтверждение регистрации',
                    ];
                }
                if (empty($ret_error)) {
                    // запрос успешно создан
                    $new_user->sendActivateQueryEmail();
                } else {
                    // по стандартному сценарию регистрации
                    $new_user->sendActivateEmail();
                }
                if (!empty($ret_error)) {
                    return $ret_error;
                }

                $success_page = Regfizusrsuccess::find()->where(['model' => Regfizusrsuccess::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $success_page->getUrlPath(),
                    'message' => 'Вы успешно зарегистрировались',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionLoginform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $loginform = new LoginForm();
        if (Yii::$app->request->isAjax && $loginform->sanitize(Yii::$app->request->post())) {
            $loginform->scenario = $loginform->action;
            if ($loginform->validate()) {
                $user = $loginform->getUser()?->getUserAR();

                switch ($loginform->scenario) {
                    case 'default':
                        $needAgrees = false;
                        if ($user) {
                            if ($user && $user->use_password) {
                                return [
                                    'status' => 'success',
                                    'action' => 'showPassword',
                                ];
                            }
                            if ($loginform->hasUserAgrees($user)) {
                                $needAgrees = true;
                            }
                        } else {
                            $needAgrees = true;
                            $user = $loginform->registerUser();

                            if (!$user) {
                                return [
                                    'status' => 'fail',
                                    'message' => 'Во время регистрации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                                ];
                            }
                        }

                        $sended = $user->generateNewCodeAndSend();
                        return [
                            'status' => 'success',
                            'action' => $needAgrees ? 'showRegCode' : 'showCode',
                            'message' => ($sended ? 'Код был отправлен на указанный email' : 'Интервал для повторной отправки кода еще не прошел'),
                        ];
                        break;
                    case 'password':
                        $sended = $user->generateNewCodeAndSend();
                        $needAgrees = false;
                        if ($loginform->hasUserAgrees($user)) {
                            $needAgrees = true;
                        }
                        return [
                            'status' => 'success',
                            'action' => $needAgrees ? 'showRegCode' : 'showCode',
                            'message' => ($sended ? 'Код был отправлен на указанный email' : 'Интервал для повторной отправки кода еще не прошел'),
                        ];
                        break;
                    case 'code':
                    case 'regCode':
                        // залогинить
                        if ($loginform->login()) {
                            if (($serviceId = Yii::$app->session->get('createorder_after_login')) && !empty($user->profile->name) && !empty($user->profile->surname) && !empty($user->profile->phone)) {
                                $ret_error = '';
                                // находим услугу
                                $service = Service::findVisible()->andWhere(['service.id' => (int)$serviceId, 'type' => Service::TYPE_TYPICAL])->one();
                                if (!$service) {
                                    $ret_error = 'Заказ услуги невозможен: услуга не найдена.';
                                }
                                if ($service->user->status != UserAR::STATUS_ACTIVE) {
                                    $ret_error = 'Заказ услуги невозможен: автор услуги деактивирован.';
                                }
                                if (!in_array($service->user->role, ['expert', 'exporg'])) {
                                    $ret_error = 'Заказ услуги невозможен: автор услуги не является экспертом.';
                                }
                                /* если мы добрались до сюда, значит все ок, создаем заказ и редиректим пользователя на страницу оплаты */
                                $new_order = new Order();
                                $new_order->user_id = $user->id;
                                $new_order->executor_id = $service->user->id;
                                $new_order->service_id = $service->id;
                                $new_order->name = 'Заказ типовой услуги';
                                $new_order->service_name = $service->name;
                                $new_order->service_descr = $service->description;
                                // рассчет в рублях
                                $new_order->price = $service->price;
                                $new_order->status = Order::STATUS_NEW;
                                $new_order->is_payed = 0;
                                $new_order->visible = 1;

                                $payment_url = false;

                                if ($new_order->save()) {
                                    // добавляем в историю событие создания заказа
                                    $new_order->newEvent()->add();
                                    // страница выбора способа оплаты заказа
                                    $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                                    $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $new_order->id]) : false;
                                } else {
                                    $ret_error = 'Заказ услуги невозможен: ошибка при сохранении заказа.';
                                }
                                if (empty($ret_error) && $payment_url) {
                                    return [
                                        'status' => 'success',
                                        'action' => 'login',
                                        'url' => $payment_url
                                    ];
                                }
                            }
                            if ($redirect_url = Yii::$app->session->get('redirect_after_login')) {
                                return [
                                    'status' => 'success',
                                    'action' => 'login',
                                    'url' => $redirect_url
                                ];
                            }
                            return [
                                'status' => 'success',
                                'action' => 'login',
                                'url' => '/',
                            ];
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Авторизация не пройдена',
                        ];
                        break;
                }
            }
            return ActiveForm::validate($loginform);

        }
        return [
            'status' => 'fail',
            'message' => 'Во время авторизации возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    /* отправляет письмо для сброса пароля */
    public function actionResetpass()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new ResetpassForm();
        $step = Yii::$app->request->post('step', 'step1');
        $modelform->scenario = $step;
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = $modelform->getUser()->getUserAR();
            switch ($step) {
                case 'step1':
                    $user->generateNewCodeAndSend();
                    $hash = base64_encode($user->email);
                    return [
                        'status' => 'success',
                        'redirect_to' => Resetpass::find()->where(['model' => Resetpass::class, 'visible' => 1])->one()->getUrlPath(['step' => 'step2', 'hash' => $hash]),
                    ];
                case 'step2':
                    $hash = base64_encode($user->email . '#' . $modelform->codeFull);
                    return [
                        'status' => 'success',
                        'redirect_to' => Resetpass::find()->where(['model' => Resetpass::class, 'visible' => 1])->one()->getUrlPath(['step' => 'step3', 'hash' => $hash]),
                    ];
                case 'step3':
                    $user->setPassword($modelform->password);
                    $user->status = UserAR::STATUS_ACTIVE;
                    $user->last_login = date('Y-m-d H:i:s');
                    $user->login_count = (int)$user->login_count + 1;
                    $user->confirm_code = null;
                    $user->confirm_code_time = '1970-01-01 00:00:00';
                    $user->confirm_code_tries_left = null;
                    $user->save();
                    Yii::$app->user->login($modelform->getUser(), 3600 * 24 * 30);
                    return [
                        'status' => 'success',
                        'redirect_to' => ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one()->getUrlPath(),
                    ];
            }
        }
        return [
            'status' => 'fail',
        ];
    }

    /* сброс пароля */
    public function actionChangepass()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Эксперта */

        $modelform = new ResetpassForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // сбросить пароль
            $user = UserAR::find()->where(['password_reset_token' => $modelform->key])->one();
            if ($user) {
                // сбросить ключ
                $user->password_reset_token = md5(time());
                $user->setPassword($modelform->password);
                if ($user->save()) {
                    $success_page = Changepasssuccess::find()->where(['model' => Changepasssuccess::class, 'visible' => 1])->one();
                    return [
                        'status' => 'success',
                        'redirect_to' => $success_page->getUrlPath(),
                        'message' => 'Вы успешно сменили пароль',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Во время смены пароля возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Во время смены пароля возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    /* первый вход - форма заполнена корректно */
    public function actionFirstloginset()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* создать Эксперта */
        $modelform = new FirstLoginForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // сбросить пароль
            $user = UserAR::find()->where(['first_login_hash' => $modelform->key])->one();
            if ($user) {
                // сбросить ключ
                $user->first_login_hash = '';
                $user->setPassword($modelform->password);
                $user->last_login = date('Y-m-d H:i:s');
                $user->login_count = (int)$user->login_count + 1;
                $user->status = UserAR::STATUS_ACTIVE;

                if ($user->save()) {
                    // добавить согласия
                    if (!empty($modelform->getUseragrees($user->id))) {
                        foreach ($modelform->getUseragrees($user->id) as $agree) {
                            if ($modelform->agreements[$agree->id] == 1) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = $user->id;
                                $agree_sign->form_model = $agree->form_type;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                    // залогинить, редирект на профиль
                    Yii::$app->controller->setCity($user->profile->city_id);
                    $user_identity = \app\modules\admin\models\User::findOne($user->id);

                    Yii::$app->user->login($user_identity);
                    $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();

                    return [
                        'status' => 'success',
                        'redirect_to' => $profile_page->getUrlPath(),
                        'message' => 'Вы успешно сменили пароль',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Во время смены пароля возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Во время смены пароля возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    /* достать фото по email */
    public function actionGetphoto()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && !empty($post['email'])) {
            // найти пользователя
            $user = UserAR::find()->where(['email' => $post['email']])->one();
            if ($user) {
                return [
                    'status' => 'success',
                    'photo' => $user->profile->getThumb('image', 'main'),
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Пользователь не найден',
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    /* отослать код заного */
    public function actionResendcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $email = Yii::$app->request->post('email');
        $new_user = UserAR::find()->where(['email' => $email])->one();
        if (!$new_user) {
            Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'fail',
                'message' => 'Пользователь не найден'
            ];
        }
        // проверка на отсылку письма один раз в 15 минут внутри функции
        if ($new_user->generateNewCodeAndSend()) {
            return [
                'status' => 'success',
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'Период для отправки нового письма для данного пользователя ещё не истёк',
        ];

    }

    /* используется при регистрации и авторизации */
    public function actionCheckconfirmcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new ConfirmCode();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $send_target = false;
            /** @var UserAR $user */
            $user = UserAR::find()->where(['email' => $modelform->email])->one();
            $activate_user = false;
            if ($user->status == UserAR::STATUS_INACTIVE) {
                $user->status = UserAR::STATUS_ACTIVE;
                $activate_user = true;
            }
            $user->confirm_code = null;
            $user->confirm_code_time = '1970-01-01 00:00:00';
            $user->confirm_code_tries_left = null;
            $user->login_count = (int)$user->login_count + 1;
            if ($user->save()) {
                if ($activate_user) {
                    switch ($user->role) {
                        case 'urusr':
                            $user->sendWelcomeEmail('welcome_uruser');
                            $send_target = 'activate_urusr_success';
                            $resultPage = Regurusractivateemail::find()->where(['model' => Regurusractivateemail::class, 'visible' => 1])->one();
                            break;
                        case 'fizusr':
                            $user->sendWelcomeEmail('welcome_fizuser');
                            if ($user->pretendent) {
                                $send_target = 'activate_expert_success';
                                $resultPage = Regexpertactivateemail::find()->where(['model' => Regexpertactivateemail::class, 'visible' => 1])->one();
                            } else {
                                $send_target = 'activate_fizusr_success';
                                $resultPage = Regfizusractivateemail::find()->where(['model' => Regfizusractivateemail::class, 'visible' => 1])->one();
                            }
                            break;
                    }
                    if (!$resultPage) {
                        return [
                            'status' => 'fail',
                            'error_html' => $this->renderPartial('confirm_code_error', [
                                'error' => 'Регистрация данного типа пользователя сейчас невозможна.',
                            ])
                        ];
                    }
                }
                // залогинить и запомнить
                $identity = \app\modules\admin\models\User::find()->where(['email' => $user->email])->one();
                Yii::$app->user->login($identity, 3600 * 24 * 30);
                if ($redirect_url = Yii::$app->session->get('redirect_after_login')) {
                    return [
                        'status' => 'success',
                        'send_target' => $send_target,
                        'redirect_to' => $redirect_url
                    ];
                }
                if ($serviceId = Yii::$app->session->get('createorder_after_login')) {
                    $ret_error = '';
                    // находим услугу
                    $service = Service::findVisible()->andWhere(['service.id' => (int)$serviceId, 'type' => Service::TYPE_TYPICAL])->one();
                    if (!$service) {
                        $ret_error = 'Заказ услуги невозможен: услуга не найдена.';
                    }
                    if ($service->user->status != UserAR::STATUS_ACTIVE) {
                        $ret_error = 'Заказ услуги невозможен: автор услуги деактивирован.';
                    }
                    if (!in_array($service->user->role, ['expert', 'exporg'])) {
                        $ret_error = 'Заказ услуги невозможен: автор услуги не является экспертом.';
                    }
                    /* если мы добрались до сюда, значит все ок, создаем заказ и редиректим пользователя на страницу оплаты */
                    $new_order = new Order();
                    $new_order->user_id = $user->id;
                    $new_order->executor_id = $service->user->id;
                    $new_order->service_id = $service->id;
                    $new_order->name = 'Заказ типовой услуги';
                    $new_order->service_name = $service->name;
                    $new_order->service_descr = $service->description;
                    // рассчет в рублях
                    $new_order->price = $service->price;
                    $new_order->status = Order::STATUS_NEW;
                    $new_order->is_payed = 0;
                    $new_order->visible = 1;

                    $payment_url = false;

                    if ($new_order->save()) {
                        // добавляем в историю событие создания заказа
                        $new_order->newEvent()->add();
                        // страница выбора способа оплаты заказа
                        $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                        $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $new_order->id]) : false;
                    } else {
                        $ret_error = 'Заказ услуги невозможен: ошибка при сохранении заказа.';
                    }
                    if (empty($ret_error) && $payment_url) {
                        return [
                            'status' => 'success',
                            'send_target' => $send_target,
                            'redirect_to' => $payment_url
                        ];
                    }
                    if (!empty($ret_error)) {
                        /* TODO: сделать общую страницу ошибок для ЛК, без привязки к моделям */
                        return [
                            'status' => 'fail',
                            'error_html' => $this->renderPartial('confirm_code_error', [
                                'error' => $ret_error,
                            ])
                        ];
                    }
                }
                /* если это обычный вход, без перехода из каталогов на покупку услуги/билета */
                if (!$activate_user) {
                    // обычный вход
                    $resultPage = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                    return [
                        'status' => 'success',
                        'redirect_to' => $resultPage->getUrlPath(),
                        'message' => 'Вы успешно авторизованы',
                    ];
                }
                /* если пользователь только что активирован */
                /* если пользователь претендент в эксперты */
                if ($user->pretendent) {
                    $selectCafedraPage = ProfileCafedra::find()->where(['model' => ProfileCafedra::class, 'visible' => 1])->one();
                    return [
                        'status' => 'success',
                        'redirect_to' => $selectCafedraPage->getUrlPath(),
                    ];
                }
                return [
                    'status' => 'success',
                    'send_target' => $send_target,
                    'success_html' => $this->renderPartial($resultPage->view . '_success', [
                        'model' => $resultPage,
                    ])
                ];
            }
            return [
                'status' => 'fail',
                'error_html' => $this->renderPartial('confirm_code_error', [
                    'error' => 'Невозможно активировать аккаунт.',
                ])
            ];

        }
        return [
            'status' => 'fail',
            'error_html' => $this->renderPartial('confirm_code_error', [
                'error' => 'Ошибка при проверке кода',
            ])
        ];
    }
}
