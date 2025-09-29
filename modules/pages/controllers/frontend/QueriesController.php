<?php

namespace app\modules\pages\controllers\frontend;

use app\models\OfferOrderForm;
use app\models\QueriesPriceForm;
use app\models\QueriesRejectForm;
use app\modules\message\models\Chat;
use app\modules\order\models\Order;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\QueriesView;
use app\modules\pages\models\SelectPayment;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\queries\models\Queries;
use app\modules\service\models\Service;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\Url;
use yii\web\Response;


class QueriesController extends LKController
{
    /* страница списка заявок (исходящих) */
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
        // получить список заявок пользователя
        $cur_status = Yii::$app->request->get('status', '');
        switch ($cur_status) {
            case 'new':
                $aviable_statuses = [Queries::STATUS_NEW];
                break;
            case 'agreement':
                $aviable_statuses = [Queries::STATUS_AGREEMENT];
                break;
            case 'offer':
                $aviable_statuses = [Queries::STATUS_OFFER];
                break;
            default:
                $aviable_statuses = [Queries::STATUS_NEW, Queries::STATUS_AGREEMENT, Queries::STATUS_OFFER];
        }

        $my_queries = Queries::find()->where(['user_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_queries, 'cur_status' => $cur_status]);

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
        /* если у пользователя нет прав на создание услуг, то и на запросы у него прав тоже быть не может */
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
            case 'new':
                $aviable_statuses = [Queries::STATUS_NEW];
                break;
            case 'agreement':
                $aviable_statuses = [Queries::STATUS_AGREEMENT];
                break;
            default:
                $aviable_statuses = [Queries::STATUS_NEW, Queries::STATUS_AGREEMENT];
        }

        $my_queries = Queries::find()->where(['executor_id' => $user->id, 'visible' => 1])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_queries, 'cur_status' => $cur_status]);

    }

    /* страница списка заявок (МКС) */
    public function actionIndexmks($model)
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
        // получить список заявок пользователей
        $get = Yii::$app->request->get();

        // просроченные
        $expired = Queries::find()->where(['visible' => 1]);
        $expired_statuses = [Queries::STATUS_NEW];
        $expired->andWhere(['IN', 'status', $expired_statuses]);
        $expired->andWhere(['<=', 'created_at', time() - 48 * 60 * 60]);

        // отменённые
        $canceled = Queries::find()->where(['visible' => 1]);
        $canceled_statuses = [Queries::STATUS_EXECCANCEL];
        $canceled->andWhere(['IN', 'status', $canceled_statuses]);

        // ждут предложений
        $offer = Queries::find()->where(['visible' => 1]);
        $offer_statuses = [Queries::STATUS_OFFER];
        $offer->andWhere(['IN', 'status', $offer_statuses]);

        // все
        $queries = Queries::find()->where(['visible' => 1]);
        $queries->andWhere(['OR',
            ['status' => Queries::STATUS_EXECCANCEL],
            ['status' => Queries::STATUS_OFFER],
            ['AND',
                ['<=', 'created_at', time() - 48 * 60 * 60],
                ['status' => Queries::STATUS_NEW],
            ]
        ]);
        $expired_count = $expired->count();
        $canceled_count = $canceled->count();
        $offer_count = $offer->count();
        $queries_count = $queries->count();

        switch ($get['status']) {
            case 'expired':
                $items = $expired->orderBy(['created_at' => SORT_DESC])->all();
                break;
            case 'canceled':
                $items = $canceled->orderBy(['created_at' => SORT_DESC])->all();
                break;
            case 'offer':
                $items = $offer->orderBy(['created_at' => SORT_DESC])->all();
                break;
            default:
                $items = $queries->orderBy(['created_at' => SORT_DESC])->all();
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $items, 'expired_count' => $expired_count, 'canceled_count' => $canceled_count, 'offer_count' => $offer_count, 'queries_count' => $queries_count]);
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
            case 'cancel':
                $aviable_statuses = [Queries::STATUS_EXECCANCEL, Queries::STATUS_USERCANCEL,];
                break;
            case 'done':
                $aviable_statuses = [Queries::STATUS_DONE];
                break;
            case 'close':
                $aviable_statuses = [Queries::STATUS_CLOSE];
                break;
            default:
                $aviable_statuses = [Queries::STATUS_EXECCANCEL, Queries::STATUS_USERCANCEL, Queries::STATUS_DONE, Queries::STATUS_CLOSE];
        }

        $my_queries = Queries::find()->where(['visible' => 1])->andWhere(['user_id' => $user->id])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_queries, 'cur_status' => $cur_status]);

    }

    /* страница архивных заказов (Я - Эксперт) */
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
            case 'cancel':
                $aviable_statuses = [Queries::STATUS_EXECCANCEL, Queries::STATUS_USERCANCEL,];
                break;
            case 'done':
                $aviable_statuses = [Queries::STATUS_DONE];
                break;
            case 'close':
                $aviable_statuses = [Queries::STATUS_CLOSE];
                break;
            default:
                $aviable_statuses = [Queries::STATUS_EXECCANCEL, Queries::STATUS_USERCANCEL, Queries::STATUS_DONE, Queries::STATUS_CLOSE];
        }

        $my_queries = Queries::find()->where(['visible' => 1])->andWhere(['executor_id' => $user->id])->andWhere(['IN', 'status', $aviable_statuses])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_queries, 'cur_status' => $cur_status]);

    }

    /* страница просмотра запроса */
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
        // находим запрос
        $query = Queries::findOne((int)$id);
        if (empty($query)) {
            throw new \yii\web\NotFoundHttpException('Заказ не найден');
        }
        $view = $model->view;
        $this->setMeta($model);
        if ($user->id == $query->user_id) {
            // запрос просматривает клиент
            switch ($query->status) {
                case Queries::STATUS_NEW:
                    $view = $model->view . '_user_new';
                    break;
                case Queries::STATUS_EXECCANCEL:
                case Queries::STATUS_USERCANCEL:
                    $view = $model->view . '_user_cancel';
                    break;
                case Queries::STATUS_AGREEMENT:
                    $view = $model->view . '_user_agreement';
                    break;
                case Queries::STATUS_DONE:
                    $view = $model->view . '_user_done';
                    break;
                case Queries::STATUS_CLOSE:
                    $view = $model->view . '_user_close';
                // no break
                case Queries::STATUS_OFFER:
                    $view = $model->view . '_user_offer';
                    break;
            }
        } elseif ($user->id == $query->executor_id) {
            // заказ просматривает исполнитель
            switch ($query->status) {
                case Queries::STATUS_NEW:
                    $view = $model->view . '_executor_new';
                    break;
                case Queries::STATUS_EXECCANCEL:
                case Queries::STATUS_USERCANCEL:
                    $view = $model->view . '_executor_cancel';
                    break;
                case Queries::STATUS_AGREEMENT:
                    $view = $model->view . '_executor_agreement';
                    break;
                case Queries::STATUS_DONE:
                    $view = $model->view . '_executor_done';
                    break;
                case Queries::STATUS_CLOSE:
                case Queries::STATUS_OFFER:
                    $view = $model->view . '_executor_close';
                    break;
            }
        } elseif ($user->role == 'mks') {
            // заказ просматривает МКС и видит одно и то же кроме двух статусов - отклонен исполнителем и просрочен
            switch ($query->status) {
                case Queries::STATUS_NEW:
                    $view = $model->view . '_mks_new';
                    if ($query->created_at < (time() - 48 * 60 * 60)) {
                        $query->setToMks($user->id, 'Не обработан');
                    }
                    break;
                case Queries::STATUS_EXECCANCEL:
                    $query->setToMks($user->id, 'Отклонён экспертом');
                    $view = $model->view . '_mks_cancel';
                    break;
                case Queries::STATUS_OFFER:
                    $query->setToMks($user->id, 'Ждёт предложений');
                    $view = $model->view . '_mks_offer';
                    break;
                case Queries::STATUS_USERCANCEL:
                case Queries::STATUS_AGREEMENT:
                case Queries::STATUS_DONE:
                case Queries::STATUS_CLOSE:
                    $view = $model->view . '_mks_onlyview';
                    break;
            }
        } else {
            // а больше никому нельзя
            throw new \yii\web\NotFoundHttpException('Нет прав на просмотр деталей заказа');
        }

        if ($query) {
            $query->readNotification($user->id);
        }

        return $this->render($view, ['model' => $model, 'query' => $query]);
    }

    /* АПИ */
    /* требования для создания заказа индивидуальной услуги:
     * пользователь авторизован
     * запрос пришел аяксом (прямого создания запроса нет - только с кнопки в списке услуг)
     * пользователь имеет статус Активен
     * пользователь относится к Участникам АСТ (expert, exporg, fizusr, urusr)
     * услуга является индивидуальной
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
        $modelform = new \app\models\Queries();
        if (!$modelform->sanitize(Yii::$app->request->post()) or !$modelform->validate()) {
            return [
                'status' => 'fail',
                'message' => 'Параметры заданы неверно',
            ];
        }
        $service = Service::findVisible()->andWhere(['service.id' => (int)$modelform->service_id, 'type' => Service::TYPE_CUSTOM])->one();
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
            Yii::$app->session->remove('createorder_after_login');
            Yii::$app->session->set('redirect_after_login', $service->getUrlPath());
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
        /* если мы добрались до сюда, значит все ок, создаем запрос и редиректим пользователя на страницу просмотра запроса */

        $new_query = new Queries();
        $new_query->user_id = $user->id;
        $new_query->executor_id = $service->user->id;
        $new_query->service_id = $service->id;
        $new_query->name = 'Запрос на индивидуальную услугу';
        $new_query->service_name = $service->name;
        $new_query->service_descr = $service->description;
        // рассчет в рублях
        $new_query->status = Queries::STATUS_NEW;
        $new_query->user_comment = $modelform->comment;
        $new_query->visible = 1;
        if ($new_query->save()) {
            $modelform->updateUserProfile($user->profile);

            $new_query->setQueryNum();
            // добавляем в историю событие создания заказа
            $new_query->newEvent()->add();
            // страница выбора способа оплаты заказа
            $view_query_page = QueriesView::find()->where(['model' => QueriesView::class, 'visible' => 1])->one();
            $view_url = (!empty($view_query_page)) ? Url::toRoute([$view_query_page->getUrlPath(), 'id' => $new_query->id]) : false;
            return [
                'status' => 'success',
                'redirect_to' => $view_url,
                'message' => 'Запрос создан',
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'При создании запроса возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionGetinfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $post = Yii::$app->request->post();
        if (empty((int)$post['service'])) {
            return [
                'status' => 'fail',
                'message' => 'Неверно переданы параметры',
            ];
        }
        $service = Service::findVisible()->andWhere(['service.id' => (int)$post['service'], 'type' => Service::TYPE_CUSTOM])->one();
        if (!$service) {
            return [
                'status' => 'fail',
                'message' => 'Услуга не найдена',
            ];
        }
        /* редирект, если неавторизован */
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->remove('createorder_after_login');
            Yii::$app->session->set('redirect_after_login', $service->getUrlPath());
            return [
                'status' => 'need_register',
            ];
        }
        /* если все ок - отдаем данные */
        return [
            'status' => 'success',
            'name' => $service->name,
            'description' => $service->short_description,
        ];
    }

    public function actionRejectquery()
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
        // роль
        if (!in_array($user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение запроса',
            ];
        }
        $modelform = new QueriesRejectForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // находим запрос
            // отменить запрос могут оба - и клиент и исполнитель на любом из двух статусов (STATUS_NEW, STATUS_AGREEMENT)
            $query = Queries::find()->where(['id' => (int)$modelform->query_id])->andWhere(['IN', 'status', [Queries::STATUS_NEW, Queries::STATUS_AGREEMENT]])->one();
            if (!$query) {
                return [
                    'status' => 'fail',
                    'message' => 'Запрос не найден',
                ];
            }
            // причина отмены запроса
            $reason_text = [];
            if (!empty($modelform->reason_id) && is_array($modelform->reason_id)) {
                foreach ($modelform->reason_id as $ref_id) {
                    $ref = \app\modules\reference\models\Queriesreason::findOne((int)$ref_id);
                    if ($ref) {
                        $reason_text[] = $ref->name;
                    }
                }
            }
            if (!empty($modelform->reason_text)) {
                $reason_text[] = $modelform->reason_text;
            }
            // отменяем запрос
            if ($query->user_id == $user->id) {
                // запрашивает отмену клиент
                $query->status = Queries::STATUS_USERCANCEL;
                $query->reject_reason_user = implode(', ', $reason_text);
                $source = 'user';
            } elseif ($query->executor_id == $user->id) {
                // запрашивает отмену исполнитель
                $query->status = Queries::STATUS_EXECCANCEL;
                $query->reject_reason_executor = implode(', ', $reason_text);
                $source = 'executor';
            }

            $query->closed_at = date('d.m.Y H:i:s');

            if ($query->save()) {
                // добавляем в историю событие отмены запроса:
                $query->newEvent()->rejectQuery($source);
                // закрываем общий чат
                $query->chatAll->status = Chat::STATUS_CLOSED;
                $query->chatAll->save();
                return [
                    'status' => 'success',
                    'message' => 'Запрос отменен',
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

    public function actionGetagreement()
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
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на изменение заявки',
            ];
        }
        $modelform = new QueriesPriceForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // находим запрос
            $query = Queries::find()->where(['id' => (int)$modelform->query_id, 'executor_id' => $user->id])->andWhere(['IN', 'status', [Queries::STATUS_NEW, Queries::STATUS_AGREEMENT]])->one();
            if (!$query) {
                return [
                    'status' => 'fail',
                    'message' => 'Запрос не найден',
                ];
            }
            // заполняем срок и сумму запроса, переводим в статус Согласование, пишем событие и сообщение в чат
            $query->offered_price = (int)$modelform->price;
            $query->offered_datestart = $modelform->date_start;
            $query->offered_dateend = $modelform->date_end;
            $query->special = $modelform->special;
            $query->status = Queries::STATUS_AGREEMENT;
            if ($query->save()) {
                // добавляем в историю событие отмены запроса:
                $query->newEvent()->startAgree();
                return [
                    'status' => 'success',
                    'message' => 'Запрос отменен',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При изменении запроса возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    /* закрыть запрос, создать заказ, перенаправить на оплату заказа */
    public function actionFinalize()
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
                'message' => 'У вас нет прав на изменение запроса',
            ];
        }
        // находим запрос
        $post = Yii::$app->request->post();
        $query = Queries::find()->where(['id' => (int)$post['query'], 'user_id' => $user->id])->andWhere(['IN', 'status', [Queries::STATUS_AGREEMENT]])->one();
        if (!$query) {
            return [
                'status' => 'fail',
                'message' => 'Запрос не найден',
            ];
        }
        if (empty((int)$query->offered_price) or empty($query->offered_datestart) or empty($query->offered_dateend)) {
            return [
                'status' => 'fail',
                'message' => 'Условия запроса заданы некорректно, попросите исполнителя уточнить условия выполнения запроса',
            ];
        }
        // если до сюда дошли - переводим запрос в заказ
        // закрываем запрос
        $query->status = Queries::STATUS_DONE;
        $query->closed_at = date('d.m.Y H:i:s');

        if ($query->save()) {
            // добавляем в историю событие закрытия запроса:
            $query->newEvent()->queryDone();

            // создаем запроса на основе запроса
            $new_order = new Order();
            $new_order->user_id = $query->user_id;
            $new_order->executor_id = $query->executor_id;
            $new_order->service_id = $query->service_id;
            $new_order->query_id = $query->id;
            $new_order->b24_list_id = $query->b24_list_id;
            $new_order->b24_deal_id = $query->b24_deal_id;
            $new_order->name = 'Заказ индивидуальной услуги';
            $new_order->service_name = $query->service_name;
            $new_order->service_descr = $query->service_descr;
            if (!empty($query->user_comment)) {
                $new_order->service_descr = $new_order->service_descr . ' Комментарий: ' . $query->user_comment;
            }
            $new_order->special = $query->special;
            // рассчет в рублях
            $new_order->price = (int)$query->offered_price;
            $new_order->status = Order::STATUS_NEW;
            $new_order->is_payed = 0;
            $new_order->visible = 1;
            $new_order->execute_start = $query->offered_datestart;
            $new_order->execute_before = $query->offered_dateend;

            if ($new_order->save()) {
                // добавляем в историю событие создания заказа
                $new_order->newEvent()->add();
                $query->newEvent()->addOrder(true, $new_order->orderNum);
                $query->chatAll->status = Chat::STATUS_CLOSED;
                $query->chatAll->save();
                // страница выбора способа оплаты заказа
                $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $new_order->id]) : false;
                return [
                    'status' => 'success',
                    'redirect_to' => $payment_url,
                    'message' => 'Изменения приняты',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При создании заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При изменении запроса возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionClosequery()
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
                'message' => 'У вас нет прав на закрытие запроса',
            ];
        }
        // находим запрос
        $post = Yii::$app->request->post();
        $query = Queries::findOne((int)$post['query']);
        if (!$query) {
            return [
                'status' => 'fail',
                'message' => 'Запрос не найден',
            ];
        }
        // если до сюда дошли - закрываем запрос
        switch ($post['action']) {
            case 'close':
                // закрываем запрос
                $query->status = Queries::STATUS_CLOSE;
                $query->closed_at = date('d.m.Y H:i:s');
                if ($query->save()) {
                    // добавляем в историю событие закрытия запроса:
                    $query->newEvent()->queryClose();
                    $query->chatAll->status = Chat::STATUS_CLOSED;
                    $query->chatAll->save();
                    if ($query->chatMKSExpert) {
                        $query->chatMKSExpert->status = Chat::STATUS_CLOSED;
                        $query->chatMKSExpert->save();
                    }
                    if ($query->chatMKSClient) {
                        $query->chatMKSClient->status = Chat::STATUS_CLOSED;
                        $query->chatMKSClient->save();
                    }
                    $query->closeByMks($user->id, date('d.m.Y H:i:s') . ' - Запрос закрыт');
                    return [
                        'status' => 'success',
                        'message' => 'Изменения приняты',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'При изменении запроса возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];
            case 'offer':
                // закрываем запрос
                $query->status = Queries::STATUS_OFFER;
                $query->closed_at = date('d.m.Y H:i:s');
                if ($query->save()) {
                    // добавляем в историю событие закрытия запроса:
                    $query->newEvent()->queryOffer();
                    $query->chatAll->status = Chat::STATUS_CLOSED;
                    $query->chatAll->save();
                    $query->workByMks($user->id, date('d.m.Y H:i:s') . ' - Запрос переведен в ожидание предложений. <br>');
                    return [
                        'status' => 'success',
                        'message' => 'Изменения приняты',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'При изменении запроса возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];
            default:
                return [
                    'status' => 'fail',
                    'message' => 'Действие не определено',
                ];
        }
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
            $query = Queries::find()->where(['id' => $modelform->order_id, 'status' => Queries::STATUS_OFFER])->one();
            if ($query) {
                $offered_user = UserAR::find()->where(['id' => $modelform->user_id, 'status' => UserAR::STATUS_ACTIVE])->one();
                if (!$offered_user && (in_array($offered_user->role, ['expert', 'exporg']))) {
                    return [
                        'status' => 'fail',
                        'message' => 'Предложенный эксперт не найден',
                    ];
                }
                $offer_text = $modelform->offer_text . '<br><br><a href="' . $offered_user->getUrlPath() . '" class="button lk">' . $offered_user->profile->halfname . '</a>';
                $query->newEvent()->offerOrder($offer_text);
                $query->workByMks($user->id, date('d.m.Y H:i:s') . ' - МКС предложил альтернативу клиенту (' . $offered_user->profile->halfname . '). <br>');
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
}
