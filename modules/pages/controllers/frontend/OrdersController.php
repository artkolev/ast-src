<?php

namespace app\modules\pages\controllers\frontend;

use app\models\OfferOrderForm;
use app\models\OrderPriceForm;
use app\models\OrderRejectForm;
use app\modules\message\models\Chat;
use app\modules\order\models\Order;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\SelectPayment;
use app\modules\payment\models\Payment;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\service\models\Service;
use app\modules\settings\models\Settings;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class OrdersController extends LKController
{
    /* страница списка заказов (исходящих) */
    public function actionIndex($model)
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
        // получить список заказов пользователя
        $cur_status = Yii::$app->request->get('status', '');
        switch ($cur_status) {
            case 'new':
                $aviable_statuses = [Order::STATUS_NEW];
                break;
            case 'payed':
                $aviable_statuses = [Order::STATUS_PAYED];
                break;
            case 'inwork':
                $aviable_statuses = [Order::STATUS_INWORK];
                break;
            case 'cancel':
                $aviable_statuses = [Order::STATUS_EXECCANCEL, Order::STATUS_USERCANCEL];
                break;
            case 'ready':
                $aviable_statuses = [Order::STATUS_EXECDONE];
                break;
            case 'discus':
                $aviable_statuses = [Order::STATUS_DISCUS];
                break;
            case 'offers':
                $aviable_statuses = [Order::STATUS_OFFER];
                break;
            default:
                $aviable_statuses = [Order::STATUS_NEW, Order::STATUS_PAYED, Order::STATUS_INWORK, Order::STATUS_EXECDONE, Order::STATUS_EXECCANCEL, Order::STATUS_USERCANCEL, Order::STATUS_DISCUS, Order::STATUS_OFFER];
        }

        $count_discus_orders = Order::find()->where(['user_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', [Order::STATUS_DISCUS]])->count();
        $count_offer_orders = Order::find()->where(['user_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', [Order::STATUS_OFFER]])->count();

        $my_orders = Order::find()->where(['user_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_orders, 'count_discus_orders' => $count_discus_orders, 'count_offer_orders' => $count_offer_orders, 'cur_status' => $cur_status]);

    }

    /* страница списка текущих заказов (входящих) */
    public function actionIncoming($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* если у пользователя нет прав на создание услуг, то и на заказы у него прав тоже быть не может */
        if (!$user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только экспертам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        // получить список заказов пользователя
        $cur_status = Yii::$app->request->get('status', '');
        switch ($cur_status) {
            case 'payed':
                $aviable_statuses = [Order::STATUS_PAYED];
                break;
            case 'inwork':
                $aviable_statuses = [Order::STATUS_INWORK];
                break;
            case 'cancel':
                $aviable_statuses = [Order::STATUS_EXECCANCEL, Order::STATUS_USERCANCEL];
                break;
            case 'ready':
                $aviable_statuses = [Order::STATUS_EXECDONE];
                break;
            case 'discus':
                $aviable_statuses = [Order::STATUS_DISCUS];
                break;
            default:
                $aviable_statuses = [Order::STATUS_PAYED, Order::STATUS_INWORK, Order::STATUS_EXECDONE, Order::STATUS_EXECCANCEL, Order::STATUS_USERCANCEL, Order::STATUS_DISCUS];
        }

        $count_discus_orders = Order::find()->where(['executor_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', [Order::STATUS_DISCUS]])->count();

        $my_orders = Order::find()->where(['executor_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_orders, 'cur_status' => $cur_status, 'count_discus_orders' => $count_discus_orders]);

    }

    /* страница списка заказов (МКС) */
    public function actionIndex_mks($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['mks'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        // получить список заказов пользователя

        $get = Yii::$app->request->get();

        // не обработанные
        $expiried_new = Order::find()->where(['orders.visible' => 1]);
        $expiried_new_statuses = [Order::STATUS_PAYED];
        $expiried_new->andWhere(['IN', 'orders.status', $expiried_new_statuses]);
        $expiried_new->leftJoin('payment', 'payment.model_id = orders.id')->andWhere(['payment.type_model' => Order::class, 'payment.status' => Payment::STATUS_ACCEPTED]);
        $expiried_new->andWhere(['<=', 'payment.payment_date', date('Y-m-d H:i:s', time() - 48 * 60 * 60)]);

        // просроченные
        $expiried = Order::find()->where(['orders.visible' => 1]);
        $expiried_statuses = [Order::STATUS_INWORK];
        $expiried->andWhere(['IN', 'orders.status', $expiried_statuses]);
        $expiried->andWhere(['<=', 'orders.execute_before', date('Y-m-d H:i:s')]);

        // отменённые
        $canceled = Order::find()->where(['orders.visible' => 1]);
        $canceled_statuses = [Order::STATUS_EXECCANCEL];
        $canceled->andWhere(['IN', 'orders.status', $canceled_statuses]);

        // контроль качества
        $discus = Order::find()->where(['orders.visible' => 1]);
        $discus_statuses = [Order::STATUS_DISCUS];
        $discus->andWhere(['IN', 'orders.status', $discus_statuses]);

        // ждут предложений
        $offers = Order::find()->where(['orders.visible' => 1]);
        $offers_statuses = [Order::STATUS_OFFER];
        $offers->andWhere(['IN', 'orders.status', $offers_statuses]);

        // все
        $orders = Order::find()->where(['orders.visible' => 1]);
        $aviable_statuses = [Order::STATUS_EXECCANCEL, Order::STATUS_DISCUS, Order::STATUS_OFFER];

        // пока МКС работает только с оплаченными заказами, пока так получается, если будут изменения - надо будет проверить тут
        $orders->leftJoin('payment', 'payment.model_id = orders.id')->andWhere(['payment.type_model' => Order::class, 'payment.status' => Payment::STATUS_ACCEPTED]);

        $orders->andWhere(['OR',
            ['IN', 'orders.status', $aviable_statuses],
            ['AND',
                ['orders.status' => Order::STATUS_INWORK],
                ['<=', 'orders.execute_before', date('Y-m-d H:i:s')]
            ],
            ['AND',
                ['orders.status' => Order::STATUS_PAYED],
                ['<=', 'payment.payment_date', date('Y-m-d H:i:s', time() - 48 * 60 * 60)]
            ]
        ]);

        $expiried_new_count = $expiried_new->count();
        $expiried_count = $expiried->count();
        $canceled_count = $canceled->count();
        $discus_count = $discus->count();
        $offers_count = $offers->count();
        $orders_count = $orders->count();

        switch ($get['status']) {
            case 'expiried_new':
                $items = $expiried_new->orderBy(['orders.created_at' => SORT_DESC])->all();
                break;
            case 'expiried':
                $items = $expiried->orderBy(['orders.created_at' => SORT_DESC])->all();
                break;
            case 'canceled':
                $items = $canceled->orderBy(['orders.created_at' => SORT_DESC])->all();
                break;
            case 'discus':
                $items = $discus->orderBy(['orders.created_at' => SORT_DESC])->all();
                break;
            case 'offers':
                $items = $offers->orderBy(['orders.created_at' => SORT_DESC])->all();
                break;
            default:
                $items = $orders->orderBy(['orders.created_at' => SORT_DESC])->all();
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $items, 'expiried_new_count' => $expiried_new_count, 'expiried_count' => $expiried_count, 'canceled_count' => $canceled_count, 'discus_count' => $discus_count, 'offers_count' => $offers_count, 'orders_count' => $orders_count]);
    }

    /* страница архивных заказов (Я - клиент) */
    public function actionArchive($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только экспертам АСТ */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        // получить список заказов пользователя
        $cur_status = Yii::$app->request->get('status', '');
        switch ($cur_status) {
            case 'done':
                $aviable_statuses = [Order::STATUS_DONE];
                break;
            case 'close':
                $aviable_statuses = [Order::STATUS_CLOSE];
                break;
            default:
                $aviable_statuses = [Order::STATUS_DONE, Order::STATUS_CLOSE];
        }

        $my_orders = Order::find()->where(['visible' => 1])->andWhere(['user_id' => $user->id])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_orders, 'cur_status' => $cur_status]);

    }

    /* страница архивных заказов (Я - эксперт) */
    public function actionArchiveincoming($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только экспертам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        // получить список заказов пользователя
        $cur_status = Yii::$app->request->get('status', '');
        switch ($cur_status) {
            case 'done':
                $aviable_statuses = [Order::STATUS_DONE];
                break;
            case 'close':
                $aviable_statuses = [Order::STATUS_CLOSE];
                break;
            default:
                $aviable_statuses = [Order::STATUS_DONE, Order::STATUS_CLOSE];
        }

        $my_orders = Order::find()->where(['visible' => 1])->andWhere(['executor_id' => $user->id])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_orders, 'cur_status' => $cur_status]);

    }

    /* страница просмотра заказа */
    public function actionView($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна всем участникам АСТ */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr', 'mks'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        // находим заказ
        $order = Order::findOne((int)$id);
        if (empty($order)) {
            throw new \yii\web\NotFoundHttpException('Заказ не найден');
        }
        $view = $model->view;
        $this->setMeta($model);
        if ($user->id == $order->user_id) {
            // заказ просматривает заказчик
            switch ($order->status) {
                case Order::STATUS_NEW:
                    $view = $model->view . '_user_new';
                    break;
                case Order::STATUS_PAYED:
                    $view = $model->view . '_user_payed';
                    break;
                case Order::STATUS_INWORK:
                    $view = $model->view . '_user_inwork';
                    break;
                case Order::STATUS_EXECCANCEL:
                case Order::STATUS_USERCANCEL:
                    $view = $model->view . '_user_cancel';
                    break;
                case Order::STATUS_EXECDONE:
                    $view = $model->view . '_user_ready';
                    break;
                case Order::STATUS_DISCUS:
                    $view = $model->view . '_user_discus';
                    break;
                case Order::STATUS_OFFER:
                    $view = $model->view . '_user_offer';
                    break;
                case Order::STATUS_DONE:
                case Order::STATUS_CLOSE:
                    $view = $model->view . '_user_done';
                    break;
            }
        } elseif ($user->id == $order->executor_id) {
            // заказ просматривает исполнитель
            switch ($order->status) {
                case Order::STATUS_PAYED:
                    $view = $model->view . '_executor_payed';
                    break;
                case Order::STATUS_INWORK:
                    $view = $model->view . '_executor_inwork';
                    break;
                case Order::STATUS_EXECCANCEL:
                case Order::STATUS_USERCANCEL:
                    $view = $model->view . '_executor_cancel';
                    break;
                case Order::STATUS_EXECDONE:
                    $view = $model->view . '_executor_ready';
                    break;
                case Order::STATUS_DISCUS:
                    $view = $model->view . '_executor_discus';
                    break;
                case Order::STATUS_DONE:
                case Order::STATUS_OFFER:
                case Order::STATUS_CLOSE:
                    $view = $model->view . '_executor_done';
                    break;
            }
        } elseif ($user->role == 'mks') {
            // заказ просматривает МКС
            switch ($order->status) {
                case Order::STATUS_PAYED:
                    // если с момента оплаты прошло более 48 часов
                    if ($order->payment && (strtotime($order->payment->payment_date) <= (time() - 48 * 60 * 60))) {
                        $order->setToMks($user->id, 'Не обработан');
                    }
                    $view = $model->view . '_mks_payed';
                    break;
                case Order::STATUS_INWORK:
                    if (strtotime($order->execute_before) <= time()) {
                        $order->setToMks($user->id, 'Просрочен');
                    }
                    $view = $model->view . '_mks_inwork';
                    break;
                case Order::STATUS_EXECCANCEL:
                    $order->setToMks($user->id, 'Заказ отклонён экспертом');
                    $view = $model->view . '_mks_cancel';
                    break;
                case Order::STATUS_DISCUS:
                    $order->setToMks($user->id, 'Контроль качества');
                    $view = $model->view . '_mks_discus';
                    break;
                case Order::STATUS_OFFER:
                    $order->setToMks($user->id, 'Ждет предложений');
                    $view = $model->view . '_mks_offers';
                    break;
                case Order::STATUS_NEW:
                case Order::STATUS_USERCANCEL:
                case Order::STATUS_DONE:
                case Order::STATUS_CLOSE:
                case Order::STATUS_EXECDONE:
                    $view = $model->view . '_mks_onlyview';
                    break;
            }
        } else {
            // а больше никому нельзя
            throw new \yii\web\NotFoundHttpException('Не прав на просмотр деталей заказа');
        }

        if ($order) {
            $order->readNotification($user->id);
        }

        return $this->render($view, ['model' => $model, 'order' => $order]);
    }

    /* АПИ */
    /* требования для создания заказа типовой услуги:
     * пользователь авторизован
     * запрос пришел аяксом (прямого создания заказа нет - только с кнопки в списке услуг)
     * пользователь имеет статус Активен
     * пользователь относится к Участникам АСТ (expert, exporg, fizusr, urusr)
     * услуга является типовой
     * услуга является действующей - не архивная, не скрытая
     * услуга входит в область видимости для текущего пользователя
     * владелец услуги не является текущим пользователем (нельзя заказать услугу у самого себя)
     * владелец услуги имеет статус Активен
     * владелец услуги относится к Экспертам АСТ (expert, exporg)
     * на владельца услуги не создано голосование на исключение из Экспертов.
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        // находим услугу
        $post = Yii::$app->request->post();
        $modelform = new \app\models\Orders();
        if ($modelform->load($post)) {
            if (!$modelform->validate()) {
                return [
                    'status' => 'fail',
                    'message' => 'Параметры заданы неверно',
                ];
            }
            $serviceId = $modelform->service_id;
        } else {
            $modelform = null;
            $serviceId = (int)$post['service'];
        }
        $service = Service::findVisible()->where(['service.id' => $serviceId, 'type' => Service::TYPE_TYPICAL])->one();
        if (!$service) {
            return [
                'status' => 'fail',
                'message' => 'Услуга недоступна',
            ];
        }

        if ($service->user->status != UserAR::STATUS_ACTIVE) {
            return [
                'status' => 'fail',
                'message' => 'Исполнитель отстранен от обслуживания клиентов',
            ];
        }
        if (!in_array($service->user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'Исполнитель более не является участником АСТ и не может предоставлять услуги',
            ];
        }

        /* редирект, если неавторизован */
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->set('redirect_after_login', $service->getUrlPath());
            Yii::$app->session->set('createorder_after_login', $service->id);
            return [
                'status' => 'need_register',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание заказа',
            ];
        }
        // проверяем исполнителя услуги
        if ($service->user->id == $user->id) {
            return [
                'status' => 'fail',
                'message' => 'Нельзя заказать свою услугу',
            ];
        }
        // TODO: проверить наличие голосований
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
        if ($new_order->save()) {
            if ($modelform) {
                $modelform->updateUserProfile($user->profile);
            }
            // добавляем в историю событие создания заказа
            $new_order->newEvent()->add();
            // страница выбора способа оплаты заказа
            $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
            $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $new_order->id]) : false;
            return [
                'status' => 'success',
                'redirect_to' => $payment_url,
                'message' => 'Заказ создан',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При создании заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionNewdeadline()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }

        $modelform = new OrderPriceForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // находим заказ
            $order = Order::find()->where(['id' => (int)$modelform->order_id, 'executor_id' => $user->id])->andWhere(['IN', 'status', [Order::STATUS_INWORK, Order::STATUS_PAYED]])->one();
            if (!$order) {
                return [
                    'status' => 'fail',
                    'message' => 'Заказ не найден',
                ];
            }
            // заполняем срок и условия заказа, переводим в статус В работе, пишем событие и сообщение в чат
            // TODO: если сроки равны уже заданным, то пропускаем (для заказов из заявок актуально на первом шаге после оплаты)
            $order->offered_datestart = $modelform->date_start;
            $order->offered_dateend = $modelform->date_end;
            $order->offered_special = $modelform->special;
            $order->status = Order::STATUS_INWORK;
            if ($order->save()) {
                // добавляем в историю событие изменения дедлайна:
                $order->newEvent()->approveDate();
                return [
                    'status' => 'success',
                    'message' => 'Предложены новые даты',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Возникли ошибки: ' . var_dump($modelform->errors),
        ];

    }

    public function actionSetoffer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['mks'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }

        $modelform = new OfferOrderForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // отправить сообщение с кнопкой
            $order = Order::find()->where(['id' => $modelform->order_id, 'status' => Order::STATUS_OFFER])->one();
            if ($order) {
                $offered_user = UserAR::find()->where(['id' => $modelform->user_id, 'status' => UserAR::STATUS_ACTIVE])->one();
                if (!$offered_user && (in_array($offered_user->role, ['expert', 'exporg']))) {
                    return [
                        'status' => 'fail',
                        'message' => 'Предложенный эксперт не найден',
                    ];
                }
                $offer_text = $modelform->offer_text . '<br><br><a href="' . $offered_user->getUrlPath() . '" class="button lk">' . $offered_user->profile->halfname . '</a>';
                $order->newEvent()->offerOrder($offer_text);
                $order->workByMks($user->id, date('d.m.Y H:i:s') . ' - МКС предложил альтернативу клиенту (' . $offered_user->profile->halfname . '). <br>');
                return [
                    'status' => 'success',
                    'message' => 'Предложение добавлено в чат',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Возникли ошибки: ' . var_dump($modelform->errors),
        ];

    }

    public function actionChangedeadline()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }
        // находим заказ
        $post = Yii::$app->request->post();
        $order = Order::find()->where(['id' => (int)$post['order'], 'status' => Order::STATUS_INWORK])->one();
        if (!$order) {
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];
        }
        if ($user->id == $order->executor_id) {
            $actor = 'Исполнитель';
            // исполнитель может только отклонить дедлайн
            if ($post['action'] == 'decline') {
                $order->offered_datestart = null;
                $order->offered_dateend = null;
                $order->offered_special = null;
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Действие не определено',
                ];
            }
        } elseif ($user->id == $order->user_id) {
            $actor = 'Заказчик';
            switch ($post['action']) {
                case 'accept':
                    if (empty($order->offered_datestart) or empty($order->offered_dateend)) {
                        return [
                            'status' => 'fail',
                            'message' => 'Новый дедлайн не задан.',
                        ];
                    }
                    $order->execute_start = $order->offered_datestart;
                    $order->execute_before = $order->offered_dateend;
                    $order->special = $order->offered_special;
                    $order->offered_datestart = null;
                    $order->offered_dateend = null;
                    $order->offered_special = null;
                    break;
                case 'decline':
                    $order->offered_datestart = null;
                    $order->offered_dateend = null;
                    $order->offered_special = null;
                    break;
                default:
                    return [
                        'status' => 'fail',
                        'message' => 'Действие не определено',
                    ];
            }

        }
        if ($order->save()) {
            // добавляем в историю событие предложения нового дедлайна
            switch ($post['action']) {
                case 'accept':
                    $order->newEvent()->acceptNewDate();
                    return [
                        'status' => 'success',
                        'message' => 'Новые условия приняты',
                    ];
                case 'decline':
                    $order->newEvent()->declineNewDate(true, $actor);
                    return [
                        'status' => 'success',
                        'message' => 'Новые условия отклонёны',
                    ];
            }
        } else {
            return [
                'status' => 'fail',
                'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
    }

    public function actionReabil()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }
        // находим заказ
        $post = Yii::$app->request->post();
        $order = Order::find()->where(['id' => (int)$post['order']])->andWhere(['IN', 'status', [Order::STATUS_EXECCANCEL, Order::STATUS_USERCANCEL]])->one();
        if (!$order) {
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];
        }
        if (($user->id == $order->executor_id) && ($order->status == Order::STATUS_EXECCANCEL) or ($user->id == $order->user_id) && ($order->status == Order::STATUS_USERCANCEL)) {
            // все ок, возвращаем заказ в работу.
            $order->status = Order::STATUS_INWORK;
            if ($order->save()) {
                // добавляем в историю событие возврата в работу
                $actor = ($user->id == $order->executor_id) ? 'Эксперт' : (($user->id == $order->user_id) ? 'Клиент' : '');
                // возвращаем чат клиента с экспертом
                $order->chatAll->status = Chat::STATUS_OPEN;
                $order->chatAll->save();

                $order->newEvent()->reabilitate(true, $actor);
                return [
                    'status' => 'success',
                    'message' => 'Заказ возвращен в работу',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Действие не разрешено',
        ];

    }

    public function actionExecorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }
        // находим заказ
        $post = Yii::$app->request->post();
        $order = Order::find()->where(['id' => (int)$post['order'], 'executor_id' => $user->id, 'status' => Order::STATUS_INWORK])->one();
        if (!$order) {
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];
        }
        // если до сюда дошли - переводим заказ в следующий статус
        $order->status = Order::STATUS_EXECDONE;
        $order->execute_done = date('d.m.Y H:i:s');
        if ($order->save()) {
            // добавляем в историю событие предложения нового дедлайна:
            $order->newEvent()->preDone();

            // TODO: формировать Акт выполненных работ и чек полной оплаты в Атолл

            return [
                'status' => 'success',
                'message' => 'Заказ выполнен.',
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionRejectorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти пользователя */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }
        $modelform = new OrderRejectForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // находим заказ
            $order = Order::find()->where(['id' => (int)$modelform->order_id, 'status' => Order::STATUS_INWORK])->one();
            if (!$order) {
                return [
                    'status' => 'fail',
                    'message' => 'Заказ не найден',
                ];
            }
            $reason_text = [];
            if (!empty($modelform->reason_id) && is_array($modelform->reason_id)) {
                foreach ($modelform->reason_id as $ref_id) {
                    $ref = \app\modules\reference\models\Orderreason::findOne((int)$ref_id);
                    if ($ref) {
                        $reason_text[] = $ref->name;
                    }
                }
            }
            if (!empty($modelform->reason_text)) {
                $reason_text[] = $modelform->reason_text;
            }

            if ($user->id == $order->executor_id) {
                $order->status = Order::STATUS_EXECCANCEL;
                $order->reject_reason_executor = implode(', ', $reason_text);
            } elseif ($user->id == $order->user_id) {
                if (strtotime($order->execute_start) < time() + 48 * 60 * 60) {
                    return [
                        'status' => 'fail',
                        'message' => 'Заказ может быть отменён не позднее чем за 2 дня до начала выполнения',
                    ];
                }
                $order->status = Order::STATUS_USERCANCEL;
                $order->reject_reason_user = implode(', ', $reason_text);
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'У вас нет прав на изменение заказа',
                ];
            }
            if ($order->save()) {
                // добавляем в историю событие отмены заказа:
                $order->newEvent()->execReject();
                // если от заказа отказались - общий чат закрывается.
                $order->chatAll->status = Chat::STATUS_CLOSED;
                $order->chatAll->save();
                return [
                    'status' => 'success',
                    'message' => 'Заказ отклонён.',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionConfirmorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr', 'mks'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заказа',
            ];
        }
        // находим заказ
        $post = Yii::$app->request->post();
        $order = Order::find()->where(['id' => (int)$post['order']])->one();

        if (!$order) {
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];
        }
        // проверять состояния и авторство
        switch ($post['action']) {
            case 'accept':
                // только для заказчика и МКС
                // только для заказа в статусе завершен исполнителем и ведется спор
                if ((($user->id == $order->user_id) or ($user->role == 'mks')) && (in_array($order->status, [Order::STATUS_EXECDONE, Order::STATUS_DISCUS]))) {
                    $order->status = Order::STATUS_DONE;
                    $order->closed_at = date('d.m.Y H:i:s');
                    if ($order->save()) {
                        // акт
                        $act = new \app\modules\serviceact\models\Serviceact();
                        $act->processOrder($order);

                        // закрытие чека
                        $order->payment->finalizeCheck();

                        if ($user->role == 'mks') {
                            $actor = 'Менеджер клиентского сервиса';
                            $order->workByMks($user->id, date('d.m.Y H:i:s') . ' - Заказ переведен в статус "Выполнен в полном объёме"');
                        } else {
                            $actor = 'Клиент';
                        }
                        $order->newEvent()->done($actor);
                        $order->chatAll->status = Chat::STATUS_CLOSED;
                        $order->chatAll->save();
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'status' => 'success',
                            'message' => 'Изменения приняты',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно изменить статус',
                ];
            case 'discus':
                // только для заказа в статусе завершен исполнителем
                // только для заказчика
                if (($user->id == $order->user_id) && (in_array($order->status, [Order::STATUS_EXECDONE]))) {
                    $order->status = Order::STATUS_DISCUS;
                    if (empty($post['reason'])) {
                        return [
                            'status' => 'fail',
                            'message' => 'Необходимо указать причину возражений по выполненному заказу.',
                        ];
                    }
                    $order->reject_reason_user = $post['reason'];
                    if ($order->save()) {
                        $order->newEvent()->discus();
                        // отправить email модератору
                        \app\helpers\Mail::sendMail(
                            $order,
                            Settings::getInfo('order_moderator_email'),
                            '@app/modules/order/mails/discus_moderator',
                            'Возникли возражения по заказу'
                        );
                        return [
                            'status' => 'success',
                            'message' => 'Изменения приняты',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно изменить статус',
                ];
            case 'close':
                if ($user->role == 'mks') {
                    $order->status = Order::STATUS_CLOSE;
                    $order->closed_at = date('d.m.Y H:i:s');
                    if ($order->save()) {
                        $order->newEvent()->closeOrder();
                        $order->closeByMks($user->id, date('d.m.Y H:i:s') . ' - Запрос закрыт');
                        $order->chatAll->status = Chat::STATUS_CLOSED;
                        $order->chatAll->save();
                        if ($order->chatMKSExpert) {
                            $order->chatMKSExpert->status = Chat::STATUS_CLOSED;
                            $order->chatMKSExpert->save();
                        }
                        if ($order->chatMKSClient) {
                            $order->chatMKSClient->status = Chat::STATUS_CLOSED;
                            $order->chatMKSClient->save();
                        }
                        return [
                            'status' => 'success',
                            'message' => 'Изменения приняты',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно изменить статус',
                ];

            case 'offer':
                if ($user->role == 'mks') {
                    $order->status = Order::STATUS_OFFER;
                    $order->closed_at = date('d.m.Y H:i:s');
                    if ($order->save()) {
                        $order->newEvent()->switchOffer();
                        $order->workByMks($user->id, date('d.m.Y H:i:s') . ' - Заказ переведен в ожидание предложений.');
                        $order->chatAll->status = Chat::STATUS_CLOSED;
                        $order->chatAll->save();
                        return [
                            'status' => 'success',
                            'message' => 'Изменения приняты',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно изменить статус',
                ];
            case 'inwork':
                if ($user->role == 'mks') {
                    $order->status = Order::STATUS_INWORK;
                    // что делать с выданным актом выполненных работ?
                    if ($order->save()) {
                        $order->newEvent()->reabilitate(true, 'Менеджер клиентского сервиса');
                        $order->closeByMks($user->id, 'Доработка заказа');
                        return [
                            'status' => 'success',
                            'message' => 'Изменения приняты',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'При изменении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно изменить статус',
                ];
        }
        return [
            'status' => 'fail',
            'message' => 'Действие не определено',
        ];
    }

    public function actionGetoffered()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity->userAR;
            // обрабатываем только ajax-запросы
            $get = Yii::$app->request->get();
            $q = mb_strtolower($get['q'], 'UTF8');
            // найти пользователей, у которых в ФИО встречается заданная строка, и пользователь может оказывать услуги

            $roles = ['expert', 'exporg'];
            $query = UserAR::find();
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->leftJoin('organization', 'organization.user_id = user.id');
            $query->andWhere(
                ['or',
                    ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`," ",`profile`.`patronymic`))', $q],
                    ['LIKE', 'profile.organization_name', $q]
                ]
            )->andWhere(['user.status' => UserAR::STATUS_ACTIVE, 'organization.can_service' => 1]);

            $profiles = $query->all();
            $items = [];
            foreach ($profiles as $item) {
                $items[] = ['id' => $item->profile->user_id, 'text' => $item->profile->getFullname()];
            }
            return [
                'status' => 'success',
                'items' => $items,
            ];
        }
        return [
            'status' => 'fail',
        ];

    }
}
