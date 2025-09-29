<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\models\SelectPayment;
use app\modules\eduprogorder\models\Eduprogorder;
use app\modules\eventsorder\models\Eventsorder;
use app\modules\order\models\Order;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\payment\models\Payment;
use app\modules\payment_system\models\AlfaAnoSystem;
use app\modules\payment_system\models\AlfaSystem;
use app\modules\payment_system\models\BillAnoSystem;
use app\modules\payment_system\models\BillSystem;
use app\modules\payment_system\models\PaykeeperSystem;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\payment_system\models\ProdamusAnoSystem;
use app\modules\payment_system\models\ProdamusSystem;
use app\modules\payment_system\models\TinkoffAnoSystem;
use app\modules\payment_system\models\TinkoffSystem;
use app\modules\tariff\models\Tariff;
use Yii;

class PaymentController extends DeepController
{
    public function beforeAction($action)
    {
        if (in_array($action->id, ['checkout', 'checkout-paykeeper', 'checkout-tinkoff', 'checkout-prodamus', 'checkout-ano-tinkoff', 'checkout-alfabank', 'checkout-ano-alfabank', 'checkout-ano-prodamus'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /* страница выбора типа оплаты */
    public function actionIndex($model, $category, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только авторизованным */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);

        $errors = [];

        $modelform = new SelectPayment(['category' => $category]);

        /* других пока нет, но будут */
        switch ($category) {
            case PaymentSystem::USEDIN_SERVICES:
                $order = Order::find()->where(['id' => $id, 'status' => Order::STATUS_NEW, 'user_id' => $user->id, 'is_payed' => 0, 'visible' => 1])->one();
                if (empty($order)) {
                    $errors[] = 'Заказ, требующий оплаты не найден.';
                }
                break;
            case PaymentSystem::USEDIN_EVENTS:
                $order = Eventsorder::find()->where(['id' => $id, 'user_id' => $user->id, 'is_payed' => 0, 'visible' => 1])->one();
                /* проверяем существует ли заказ */
                if (empty($order)) {
                    $errors[] = 'Заказ, требующий оплаты не найден.';
                    /* существует ли мероприятие */
                } elseif (empty($order->event)) {
                    $errors[] = 'Мероприятие, на которое вы покупаете билеты удалено.';
                    /* не отключены ли продажи на мероприятие */
                } elseif (!$order->event->canSale()) {
                    $errors[] = 'На мероприятие <a href="' . $order->event->getUrlPath() . '">«' . $order->event->name . '»</a> продажа билетов закрыта.';
                } else {
                    /* есть ли доступное количество билетов по каждому тарифу в заказе */
                    foreach ($order->getItems_group('tariff_id') as $tariff_id => $count) {
                        $tariff = Tariff::findOne($tariff_id);
                        if (!$order->event->canBuyTarif($tariff, $count)) {
                            $tariff_text = ($tariff ? 'по тарифу «' . $tariff->name . '»' : 'по выбранному тарифу');
                            $errors[] = 'Извините, билеты ' . $tariff_text . ' невозможно приобрести. Вы можете посмотреть доступные тарифы по <a href="' . $order->event->getUrlPath() . '">ссылке</a>';
                        }
                    }
                    /* не изменилась ли сумма за заказ по тарифам */
                    if (!$order->isActualSumm()) {
                        $errors[] = 'Извините, цены в вашем заказе не актуальны. Вы можете снова оформить билеты по <a href="' . $order->event->getUrlPath() . '">ссылке</a>';
                    }
                }
                break;
            case PaymentSystem::USEDIN_EDUPROG:
                // выключатель ДПО
                if (!Yii::$app->params['enable_dpo']) {
                    throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
                }
                $order = Eduprogorder::find()->where(['id' => $id, 'user_id' => $user->id, 'is_payed' => 0, 'visible' => 1])->one();
                /* проверяем существует ли заказ */
                if (empty($order)) {
                    $errors[] = 'Заказ, требующий оплаты не найден.';
                    /* существует ли мероприятие */
                } elseif (empty($order->eduprog)) {
                    $errors[] = 'Программа, на которую вы покупаете участие удалена.';
                    /* не отключены ли продажи на мероприятие */
                } elseif (!$order->eduprog->canSale()) {
                    $errors[] = 'На программу <a href="' . $order->eduprog->getUrlPath() . '">«' . $order->eduprog->name . '»</a> продажа билетов закрыта.';
                } else {
                    /* есть ли доступное количество билетов по каждому тарифу в заказе */
                    foreach ($order->getItems_group('tariff_id') as $tariff_id => $count) {
                        if (!$order->eduprog->canBuyTarif($tariff_id, $count)) {
                            $tariff = Tariff::findOne($tariff_id);
                            $tariff_text = ($tariff ? 'по тарифу «' . $tariff->name . '»' : 'по выбранному тарифу');
                            $errors[] = 'Извините, ' . $tariff_text . ' невозможно приобрести. Вы можете посмотреть доступные тарифы по <a href="' . $order->eduprog->getUrlPath() . '">ссылке</a>';
                        }
                    }
                    /* не изменилась ли сумма за заказ по тарифам */
                    if (!$order->isActualSumm()) {
                        $errors[] = 'Извините, цены в вашем заказе не актуальны. Вы можете снова оформить участие по <a href="' . $order->eduprog->getUrlPath() . '">ссылке</a>';
                    }
                }
                $modelform->holder = $order->holder;
                break;
            default:
                throw new \yii\web\NotFoundHttpException('Что-то пошло не так');
        }

        if (!empty($errors)) {
            $message = implode('<br>', $errors);
            return $this->render('error_payment', ['model' => $model, 'message' => $message]);
        }

        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if (!empty($modelform->agrees)) {
                foreach ($modelform->agrees as $agree) {
                    if ($modelform->agreements[$agree->id] == 1) {
                        $agree_sign = new \app\modules\usersigns\models\Usersigns();
                        $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                        $agree_sign->form_model = \app\modules\formagree\models\Formagree::TYPE_PAYMENT;
                        $agree_sign->agreement_id = $agree->id;
                        $agree_sign->comment = $agree->label_text;
                        $agree_sign->save();
                    }
                }
            }

            $paymentSystem = PaymentSystem::findOne($modelform->type);
            if (!$paymentSystem) {
                throw new \yii\web\NotFoundHttpException('Что-то пошло не так');
            }

            $params = [];
            if (!empty($modelform->contragent_inn)) {
                $params['contragent_inn'] = $modelform->contragent_inn;
            }
            if (!empty($modelform->contragent_name)) {
                $params['contragent_name'] = $modelform->contragent_name;
            }
            if (!empty($modelform->contragent_phone)) {
                $params['contragent_phone'] = $modelform->contragent_phone;
            }
            if (!empty($modelform->contragent_email)) {
                $params['contragent_email'] = $modelform->contragent_email;
            }
            if (!empty($modelform->contragent_type)) {
                $params['contragent_type'] = $modelform->contragent_type;
            }
            if (!empty($modelform->contragent_edo)) {
                $params['contragent_edo'] = $modelform->contragent_edo;
            }
            $payment_url = $paymentSystem->createPayment($order, $params);

            return $this->redirect($payment_url);
        }
        return $this->render($model->view . '_' . $category, ['model' => $model, 'order' => $order, 'modelform' => $modelform]);
    }

    public function actionSuccess($model, $type = 'order')
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* пока никаких функций - просто текстовая страница, потом будет выводится информация об оплате. Возможно. */
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'type' => $type]);
    }

    public function actionCheckoutPaykeeper()
    {
        return $this->actionCheckout(PaykeeperSystem::class);
    }

    public function actionCheckout($paymentClass = PaykeeperSystem::class)
    {
        return $paymentClass::checkout();
    }

    public function actionCheckoutTinkoff()
    {
        return $this->actionCheckout(TinkoffSystem::class);
    }

    public function actionCheckoutAnoTinkoff()
    {
        return $this->actionCheckout(TinkoffAnoSystem::class);
    }

    public function actionCheckoutAlfabank()
    {
        return $this->actionCheckout(AlfaSystem::class);
    }

    public function actionCheckoutAnoAlfabank()
    {
        return $this->actionCheckout(AlfaAnoSystem::class);
    }

    public function actionCheckoutProdamus()
    {
        return $this->actionCheckout(ProdamusSystem::class);
    }

    public function actionCheckoutAnoProdamus()
    {
        return $this->actionCheckout(ProdamusAnoSystem::class);
    }

    /* скачивание pdf счета */
    public function actionBill($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ и фин. менеджеру */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $id = Yii::$app->request->get('id');
        $bill = Payment::find()
            ->where(['OR', ['payment_system' => BillSystem::class], ['payment_system' => BillAnoSystem::class]])
            ->andWhere(['id' => $id])
            ->andWhere(['user_id' => $user->id])
            ->one();
        if (empty($bill)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        return $this->render($model->view, ['model' => $model, 'bill' => $bill]);
    }
}
