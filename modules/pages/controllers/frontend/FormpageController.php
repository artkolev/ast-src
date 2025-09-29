<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\models\ConfirmCode;
use app\models\FosForm;
use app\modules\events\models\Events;
use app\modules\formslist\models\Formslist;
use app\modules\formsresult\models\Formsresult;
use app\modules\pages\models\Formpage;
use app\modules\payment\models\Payment;
use app\modules\payment\models\PaymentHistory;
use app\modules\payment_system\models\AlfaSystem;
use app\modules\users\models\UserAR;
use Yii;
use yii\web\Response;

class FormpageController extends DeepController
{
    public function actionIndex($model)
    {
        $this->setMeta($model);
        if ($model instanceof Formpage) {
            return $this->redirect('/');
        }
        $parent = Formpage::find()->where(['model' => Formpage::class, 'visible' => 1])->one();
        /* проверить мероприятие */
        $error_message = '';
        if (!empty($model->ownermodel) && ($model->ownermodel instanceof Events)) {
            if (!$model->ownermodel->canPublish() or $model->ownermodel->status != Events::STATUS_PUBLIC) {
                $error_message = 'Регистрация на мероприятие «' . $model->ownermodel->name . '» закрыта';
            }
        }
        if (!empty($error_message)) {
            return $this->render('error_forms', ['model' => $model, 'error_message' => $error_message]);
        }
        $this->layout = '@app/views/layouts/' . $model->layout;
        return $this->render($model->view, ['model' => $model, 'parent' => $parent]);

    }

    public function actionSaveform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new FosForm();
        $modelform->referer_url = Yii::$app->request->referrer;
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post())) {
            // устанавливаем сценарий валидации
            if ($modelform->register_user) {
                if (Yii::$app->user->isGuest) {
                    // в текущей реализации неавторизованный пользователь не должен сюда попадать, если форма требует регистрации.
                    return [
                        'status' => 'fail',
                        'message' => 'Для продолжения требуется авторизоваться',
                    ];
                }
                // сценарий для формы, требующей регистрации
                $modelform->scenario = 'register_user';
            }
            if ($modelform->validate()) {
                // находим форму для обработки полученных данных
                $formfos = Formslist::findOne($modelform->form_id);
                // сохранить результат формы в модуль результатов
                $result_form = new Formsresult();
                $result_form->form_id = $modelform->form_id;
                $fields = [];
                $mail_for_letter = [];
                $name_for_payment = [];
                // заполняем email для отправки писем из пользовательских полей формы, ФИО для оплат из пользовательских полей формы и собираем пары название => результат для пользовательских полей.
                foreach ($formfos->form_fields as $key => $field_config) {
                    if ($field_config['visible'] == 0) {
                        continue;
                    }
                    if ($field_config['use_as_email'] == 1) {
                        if (filter_var($modelform->fields[$field_config['sysname']], FILTER_VALIDATE_EMAIL)) {
                            $mail_for_letter[] = $modelform->fields[$field_config['sysname']];
                        }
                    }
                    if ($field_config['use_as_name'] == 1) {
                        $name_for_payment[] = $modelform->fields[$field_config['sysname']];
                    }
                    $fields[$field_config['name']] = $modelform->fields[$field_config['sysname']];
                }
                /* если нашли поля, используемые как email, берем певое в списке */
                if (!empty($mail_for_letter)) {
                    $mail_for_letter = $mail_for_letter[0];
                } else {
                    $mail_for_letter = false;
                }
                // заполняем массив с результатами с формы
                $result_form->fields = $fields;
                // если форма требует регистрации - заполняем поля, характерные для сценария регистрации из переданных полей
                if ($formfos->register_user) {
                    $result_form->name = $modelform->name;
                    $result_form->surname = $modelform->surname;
                    $result_form->patronymic = $modelform->patronymic;
                    $result_form->email = $modelform->email;
                    $result_form->phone = $modelform->phone;
                    $result_form->register_user = true;
                    // идентификатор пользователя берем от авторизованного пользователя
                    $result_form->user_id = Yii::$app->user->identity->id;
                }
                if ($result_form->save()) {
                    // сохранить согласия
                    if (!empty($modelform->agrees)) {
                        foreach ($modelform->agrees as $agree) {
                            if ($modelform->agreements[$agree->id] == 1) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                                $agree_sign->form_model = $result_form::class;
                                $agree_sign->form_id = $result_form->id;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                    // отправить email-уведомление о новой записи на указанные в форме адреса
                    if (!empty($formfos->emails)) {
                        \app\helpers\Mail::sendMail(
                            $result_form,
                            $formfos->emails,
                            '@app/modules/formsresult/mails/letter',
                            'Заполнена форма ' . $formfos->name
                        );
                    }
                    // если форма требует регистрации, то email для отправки письма и ФИО для оплаты берем из полей, заполненных пользователем в форме
                    if ($formfos->register_user) {
                        $mail_for_letter = $result_form->email;
                        $name_for_payment = [$modelform->surname, $modelform->name, $modelform->patronymic];
                    }
                    // если пользователю нужно отправить приветственное письмо - отправляем
                    if ($formfos->send_letter) {
                        if (!empty($mail_for_letter)) {
                            \app\helpers\Mail::sendMail(
                                $result_form,
                                $mail_for_letter,
                                '@app/modules/formsresult/mails/letter_user',
                                $formfos->letter_theme,
                                '',
                                false,
                                base_layout: '@app/mail/layouts/html_fos',
                            );
                        }
                    }
                    /* если форма является платежной, то получить ссылку на оплату и закинуть её в результат */
                    if ($formfos->payment_form) {
                        /* найти оплату и проверить, что все корректно заполнено */
                        $payment_data = false;
                        if (!empty($formfos->payments)) {
                            foreach ($formfos->payments as $payment) {
                                if ($payment['sysname'] == $modelform->payment_type) {
                                    $payment_data = $payment;
                                    $result_form->payment_name = $payment['name'];
                                    $result_form->payment_summ = $payment['summ'];
                                    $result_form->updateAttributes(['payment_name' => $result_form->payment_name, 'payment_summ' => $result_form->payment_summ]);
                                }
                            }
                        }
                        if (!$payment_data or ($payment_data['summ'] == 0)) {
                            return [
                                'status' => 'fail',
                                'message' => 'Невозможно оплатить. ' . \app\helpers\MainHelper::getHelpText(),
                            ];
                        }

                        $callback_url = $result_form->getSuccessPaymentUrl();

                        // создать оплату в админке
                        $payment = new Payment();
                        $payment->user_id = $result_form->user_id;
                        $payment->type_model = $result_form::class;
                        $payment->model_id = $result_form->id;
                        $payment->summ = (int)$result_form->payment_summ;
                        $payment->status = Payment::STATUS_NEW;
                        $payment->payment_system = AlfaSystem::class;
                        $payment->comment = $result_form->payment_name;
                        $payment->check_type = 'prepay';

                        if ($payment->save()) {
                            // логирование
                            PaymentHistory::add($payment->id, 'Создан платеж из Конструктора форм');

                            $payment_data = [
                                'amount' => (int)$payment->summ * 100, // сумма в копейках
                                'orderNumber' => $payment->id . (Yii::$app->params['is_original_server'] ? '' : '_test'),
                                'description' => $result_form->getServicename(),
                                'returnUrl' => $callback_url,
                                'failUrl' => Yii::$app->request->absoluteUrl,
                                'email' => $mail_for_letter,
                                'phone' => $modelform->phone,
                                'clientId' => $result_form->user_id,
                                'taxSystem' => "2",
                                'dynamicCallbackUrl' => 'https://' . $_SERVER['HTTP_HOST'] . AlfaSystem::$checkout_url,
                                'orderBundle' => json_encode($result_form->getCartAlfa()),
                                'jsonParams' => json_encode([
                                    'sbpSenderFIO' => implode(' ', $name_for_payment),
                                ])
                            ];

                            $payment_url = AlfaSystem::addPayment($payment_data, $payment);
                            if (!$payment_url) {
                                return [
                                    'status' => 'fail',
                                    'message' => 'Невозможно совершить оплату. ' . \app\helpers\MainHelper::getHelpText(),
                                ];
                            }
                        } else {
                            return [
                                'status' => 'fail',
                                'message' => 'Невозможно создать оплату. ' . \app\helpers\MainHelper::getHelpText(),
                            ];
                        }
                        // если форма отмечена как платежная, и все проверки пройдены - вернуть ссылку на оплату
                        return [
                            'status' => 'success',
                            'is_payment' => 'Y',
                            'payment_url' => $payment_url,
                            'message' => $formfos->success_text,
                        ];
                    }
                    return [
                        'status' => 'success',
                        'is_payment' => 'N',
                        'result_id' => $result_form->id,
                        'message' => $formfos->success_text,
                    ];
                }
                // ошибка сохранения записи в результатах форм
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно отправить данные. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            // ошибка валидации
            return [
                'status' => 'fail',
                'message' => 'Отправленные данные невалидны. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    /* при клике по кнопке оплаты в сообщении об успешной отправке формы */
    public function actionGetpayment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        /* проверяем параметры */
        if (empty($data['result']) or empty($data['value'])) {
            return [
                'status' => 'fail',
                'message' => 'Невозможно оплатить. Неверные параметры',
            ];
        }
        /* наличие заполненных результатов */
        $result_form = Formsresult::findOne((int)$data['result']);
        if (empty($result_form)) {
            return [
                'status' => 'fail',
                'message' => 'Невозможно оплатить. Не переданы данные',
            ];
        }
        // $result_form->form;
        /* наличие описания оплаты в форме по которой отправлен результат */
        if (empty($result_form->form)) {
            return [
                'status' => 'fail',
                'message' => 'Невозможно оплатить. Ошибка данных формы',
            ];
        }
        if (empty($result_form->form->payments)) {
            return [
                'status' => 'fail',
                'message' => 'Невозможно оплатить. Нет данных по платежу',
            ];
        }

        $payment_data = false;
        foreach ($result_form->form->payments as $payment) {
            if ($payment['sysname'] == $data['value']) {
                $payment_data = $payment;
                $result_form->payment_name = $payment['name'];
                $result_form->payment_summ = $payment['summ'];
                $result_form->updateAttributes(['payment_name' => $result_form->payment_name, 'payment_summ' => $result_form->payment_summ]);
            }
        }
        if (!$payment_data or ($payment_data['summ'] == 0)) {
            return [
                'status' => 'fail',
                'message' => 'Невозможно оплатить. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        $name_for_payment = [];
        $mail_for_letter = [];
        if ($result_form->form->register_user) {
            $name_for_payment = [$result_form->surname, $result_form->name, $result_form->patronymic];
            $mail_for_letter = $result_form->email;
        } else {
            foreach ($result_form->form->form_fields as $key => $field_config) {
                if ($field_config['use_as_name'] == 1) {
                    $name_for_payment[] = $result_form->fields[$field_config['name']];
                }
                if ($field_config['use_as_email'] == 1) {
                    $mail_for_letter[] = $result_form->fields[$field_config['name']];
                }
            }
            if (!empty($mail_for_letter)) {
                $mail_for_letter = $mail_for_letter[0];
            }
        }
        $callback_url = $result_form->getSuccessPaymentUrl();

        // создать оплату в админке
        $payment = new Payment();
        $payment->user_id = $result_form->user_id;
        $payment->type_model = $result_form::class;
        $payment->model_id = $result_form->id;
        $payment->summ = (int)$result_form->payment_summ;
        $payment->status = Payment::STATUS_NEW;
        $payment->payment_system = AlfaSystem::class;
        $payment->comment = $result_form->payment_name;
        $payment->check_type = 'prepay';

        if ($payment->save()) {
            // логирование
            PaymentHistory::add($payment->id, 'Создан платеж из Конструктора форм');

            $payment_data = [
                'amount' => (int)$payment->summ * 100, // сумма в копейках
                'orderNumber' => $payment->id . (Yii::$app->params['is_original_server'] ? '' : '_test'),
                'description' => $result_form->getServicename(),
                'returnUrl' => $callback_url,
                'failUrl' => Yii::$app->request->absoluteUrl,
                'email' => $mail_for_letter,
                'phone' => $result_form->phone,
                'clientId' => $result_form->user_id,
                'taxSystem' => "2",
                'dynamicCallbackUrl' => 'https://' . $_SERVER['HTTP_HOST'] . AlfaSystem::$checkout_url,
                'orderBundle' => json_encode($result_form->getCartAlfa()),
                'jsonParams' => json_encode([
                    'sbpSenderFIO' => implode(' ', $name_for_payment),
                ])
            ];

            $payment_url = AlfaSystem::addPayment($payment_data, $payment);
            if (!$payment_url) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно совершить оплату. ' . \app\helpers\MainHelper::getHelpText(),
                ];
            }
        } else {
            return [
                'status' => 'fail',
                'message' => 'Невозможно создать оплату. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'success',
            'payment_url' => $payment_url,
        ];
    }

    public function actionReguser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new FosForm();
        $modelform->scenario = 'register_new_user';
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // регистрация физлица в одну строку
            if ($new_user_id = UserAR::registerNewFizurs($modelform->surname, $modelform->name, $modelform->patronymic, $modelform->email, $modelform->phone, $modelform->password)) {
                $new_user = UserAR::findOne($new_user_id);
                if ($new_user->generateNewCodeAndSend()) {
                    return [
                        'status' => 'success',
                        'new_user_id' => $new_user_id,
                        'email' => $modelform->email,
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Период для отправки нового письма для данного пользователя ещё не истёк',
                ];

            }
        }
        return [
            'status' => 'fail',
            'message' => 'Не удалось зарегистрировать пользователя',
        ];
    }

    public function actionConfirmcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new ConfirmCode();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = UserAR::find()->where(['email' => $modelform->email])->one();
            $user->status = UserAR::STATUS_ACTIVE;
            $user->confirm_code = null;
            $user->confirm_code_time = '1970-01-01 00:00:00';
            $user->confirm_code_tries_left = null;
            $user->login_count = (int)$user->login_count + 1;
            if ($user->save()) {
                $identity = \app\modules\admin\models\User::find()->where(['email' => $user->email])->one();
                Yii::$app->user->login($identity, 3600 * 24 * 30);
                return [
                    'status' => 'success',
                    'param' => Yii::$app->getRequest()->csrfParam,
                    'token' => Yii::$app->getRequest()->getCsrfToken(),
                ];
            }
        }
        return [
            'status' => 'fail',
            'message' => 'Не удалось подтвердить код пользователя',
        ];
    }
}
