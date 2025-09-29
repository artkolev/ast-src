<?php

namespace app\modules\pages\controllers\frontend;

use app\helpers\ExcelExportHelper;
use app\models\EventOrderForm;
use app\models\LKEvent;
use app\models\LKEventsCancel as EventCancelForm;
use app\models\LKMaterial;
use app\models\LKNews;
use app\models\LKProject;
use app\modules\admin\components\FilestoreModel;
use app\modules\eventmoder\models\Eventmoder;
use app\modules\events\models\Events;
use app\modules\events\models\EventsDatechange;
use app\modules\eventsform\models\Eventsform;
use app\modules\eventsorder\models\Eventsorder;
use app\modules\eventsorder\models\EventsorderItem;
use app\modules\formagree\models\Formagree;
use app\modules\formslist\models\Formslist;
use app\modules\formsresult\models\Formsresult;
use app\modules\lenta\models\Material;
use app\modules\lenta\models\News;
use app\modules\lenta\models\Project;
use app\modules\pages\models\Eventspage;
use app\modules\pages\models\LKEventsCancel;
use app\modules\pages\models\LKEventsEdit;
use app\modules\pages\models\LKEventsList;
use app\modules\pages\models\LKEventsTickets;
use app\modules\pages\models\LKMaterialList;
use app\modules\pages\models\LKNewsList;
use app\modules\pages\models\LKProjectList;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\SelectPayment;
use app\modules\pages\models\Ticketbuy;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\reference\models\Eventstag;
use app\modules\settings\models\SettingsText;
use app\modules\tariff\models\Tariff;
use app\modules\tariff\models\TariffPrice;
use app\modules\users\models\UserAR;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

class ActivitiesController extends LKController
{
    /* страница создания новости */
    public function actionNewscreate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $news_model = new LKNews();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'news_model' => $news_model]);
    }

    /* страница списка новостей */
    public function actionNewslist($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $news_items = News::find()->where(['author_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $news_items]);
    }

    /* страница редактирования новости */
    public function actionNewsedit($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }


        $get = Yii::$app->request->get();
        /* если че не так - редирект на страницу списка новостей - там разберется. */
        $news_list_page = LKNewsList::find()->where(['model' => LKNewsList::class, 'visible' => 1])->one();
        if (isset($get['id'])) {
            $news = News::findOne((int)$get['id']);
            if ($news->author_id == $user->id) {
                $view = $model->view;
                $modelform = new LKNews();
                $modelform->loadFromNews($news);
                $this->setMeta($model);
                return $this->render($view, ['model' => $model, 'news_model' => $modelform, 'original' => $news]);
            }
            return $this->redirect($news_list_page->getUrlPath());

        }
        return $this->redirect($news_list_page->getUrlPath());

    }

    /* страница создания проекта */
    public function actionProjectcreate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $project_model = new LKProject();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'project_model' => $project_model]);
    }

    /* страница списка проектов */
    public function actionProjectlist($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $project_items = Project::find()->where(['author_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $project_items]);
    }

    /* страница редактирования проекта */
    public function actionProjectedit($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        /* если че не так - редирект на страницу списка новостей - там разберется. */
        $project_list_page = LKProjectList::find()->where(['model' => LKProjectList::class, 'visible' => 1])->one();
        if (isset($get['id'])) {
            $project = Project::findOne((int)$get['id']);
            if ($project->author_id == $user->id) {
                $view = $model->view;
                $modelform = new LKProject();
                $modelform->loadFromBlog($project);
                $this->setMeta($model);
                return $this->render($view, ['model' => $model, 'project_model' => $modelform, 'original' => $project]);
            }
            return $this->redirect($project_list_page->getUrlPath());

        }
        return $this->redirect($project_list_page->getUrlPath());

    }

    /* страница создания материала */
    public function actionMaterialcreate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $material_model = new LKMaterial();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'material_model' => $material_model]);
    }

    /* страница списка материалов */
    public function actionMateriallist($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $material_items = Material::find()->where(['author_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $material_items]);
    }

    /* страница редактирования материала */
    public function actionMaterialedit($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        /* если че не так - редирект на страницу списка новостей - там разберется. */
        $material_list_page = LKMaterialList::find()->where(['model' => LKMaterialList::class, 'visible' => 1])->one();
        if (isset($get['id'])) {
            $material = Material::findOne((int)$get['id']);
            if ($material->author_id == $user->id) {
                $view = $model->view;
                $modelform = new LKMaterial();
                $modelform->loadFromBlog($material);
                $this->setMeta($model);
                return $this->render($view, ['model' => $model, 'material_model' => $modelform, 'original' => $material]);
            }
            return $this->redirect($material_list_page->getUrlPath());

        }
        return $this->redirect($material_list_page->getUrlPath());

    }

    /* страница создания/редактирования мероприятия */
    public function actionEventsedit($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $curr_step = Yii::$app->request->get('step', '1');

        $event_id = Yii::$app->request->get('id', null);
        $original_event = false;

        /* страница списка мероприятий */
        $events_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();

        if ($event_id) {
            $original_event = Events::findOne((int)$event_id);

            /* если не нашли или редактировать пытается не автор, то редирект на список. */
            if ((!$original_event) or ($original_event->author_id != $user->id)) {
                return $this->redirect($events_page->getUrlPath());
            }

            /* мероприятия на модерации, отменённые и отклонённые редактировать нельзя */
            if (in_array($original_event->status, [Events::STATUS_MODERATE, Events::STATUS_CANCELLED, Events::STATUS_DECLINED])) {
                return $this->redirect($events_page->getUrlPath());
            }

            /* мероприятия-черновики без регистрации на МП редактировать нельзя */
            if (in_array($original_event->status, [Events::STATUS_NEW]) && !$user->organization->can_service) {
                $this->setMeta($model);
                return $this->render('events_create_noreg', ['model' => $model]);
            }
        } elseif ($curr_step != 1) {
            /* если id не указан и это не первый шаг, то редирект на список */
            return $this->redirect($events_page->getUrlPath());
        } elseif (!$user->organization->can_service) {
            /* если пользователь, без регистрации на МП пытается создать мероприятие - запретить. Вывести страницу с текстом */
            $this->setMeta($model);
            return $this->render('events_create_noreg', ['model' => $model]);
        }

        $event_model = new LKEvent();
        switch ($curr_step) {
            case '2':
                $event_model->loadFromEvent($original_event);
                if (empty($event_model->event_timezone)) {
                    $event_model->event_timezone = 3;
                }
                if (empty($event_model->city_id)) {
                    $event_model->city_id = $user->profile->city_id;
                }
                $event_model->step = 'step2';
                $event_model->scenario = 'step2';
                break;
            case '3':
                $event_model->step = 'step3';
                $event_model->loadFromEvent($original_event);
                $event_model->scenario = 'step3';
                break;
            case '4':
                $event_model->step = 'step4';
                $event_model->loadFromEvent($original_event);
                $event_model->scenario = 'step4';
                break;
            case '5':
                $event_model->step = 'step5';
                $event_model->loadFromEvent($original_event);
                $event_model->scenario = 'step5';
                break;
            case '1':
            default:
                $curr_step = '1';
                $event_model->step = 'step1';
                $event_model->scenario = 'step1';
                if ($original_event) {
                    $event_model->loadFromEvent($original_event);
                }
                break;
        }
        $this->setMeta($model);
        return $this->render($model->view . '_step_' . $curr_step, ['model' => $model, 'event_model' => $event_model, 'original' => $original_event]);
    }

    /* страница списка мероприятий */
    public function actionEventslist($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* ищем мероприятия текущего пользователя */
        $events_query = Events::find()->andWhere(['author_id' => $user->id, 'visible' => 1])->andWhere(['!=', 'status', Events::STATUS_DECLINED]);

        $curr_status = Yii::$app->request->get('status', '');
        /* фильтрация по выбранному статусу */
        switch ($curr_status) {
            case 'published':
                $events_query->andWhere(['status' => Events::STATUS_PUBLIC])->andWhere(['<', 'start_publish', new \yii\db\Expression('NOW()')])->andWhere(['>=', 'event_date_end', new \yii\db\Expression('CURDATE()')]);
                break;
            case 'planned':
                $events_query->andWhere(['status' => Events::STATUS_PUBLIC])->andWhere(['>', 'start_publish', new \yii\db\Expression('NOW()')])->andWhere(['>=', 'event_date_end', new \yii\db\Expression('CURDATE()')]);
                break;
            case 'moderate':
                $events_query->andWhere(['status' => Events::STATUS_MODERATE]);
                break;
            case 'cancelled':
                $events_query->andWhere(['status' => Events::STATUS_CANCELLED]);
                break;
            case 'moderate_edit':
                $events_query->andWhere(['status' => Events::STATUS_NEED_EDIT]);
                break;
            // не выводится сейчас
            case 'invisible':
                $events_query->andWhere(['status' => Events::STATUS_PUBLIC, 'events.visible' => 0])->andWhere(['status' => Events::STATUS_PUBLIC])->andWhere(['>', 'event_date_end', new \yii\db\Expression('NOW()')]);
                break;
            case 'draft':
                $events_query->andWhere(['status' => Events::STATUS_NEW]);
                break;
            case 'archive':
                $events_query->andWhere(['status' => Events::STATUS_PUBLIC])->andWhere(['<', 'event_date_end', date('Y-m-d')]);
                break;
        }
        $events_query->orderBy([new \yii\db\Expression('FIELD( status, "' . implode('","', [Events::STATUS_NEED_EDIT, Events::STATUS_MODERATE, Events::STATUS_NEW, Events::STATUS_PUBLIC, Events::STATUS_CANCELLED]) . '")'), 'event_date_end' => SORT_DESC]);
        $events_items = $events_query->all();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $events_items, 'curr_status' => $curr_status]);
    }

    /* страница списка мероприятий с купленными билетами */
    public function actionEventstickets($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна всем залогиненым пользователям */

        $connection = Yii::$app->db;

        $sql = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
        $command = $connection->createCommand($sql)->execute();

        $items = (new \yii\db\Query())
            ->select(['events_id'])
            ->from('eventsorder')
            ->where(['user_id' => $user->id, 'is_payed' => 1])
            ->orderBy(['created_at' => SORT_DESC])
            ->groupBy(['events_id'])
            ->column();

        if (!empty($items)) {
            $events_list = Events::find()
                ->where(['IN', 'id', $items])
                ->orderBy([new Expression('FIELD(id, ' . implode(',', $items) . ')')])  // Сортировка по порядку событийных ID
                ->all();
        } else {
            $events_list = [];  // Возвращаем пустой массив, если нет событий
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $events_list]);
    }

    /* страница Просмотра списка билетов */
    public function actionTicketsview($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $get = Yii::$app->request->get();
        /* если че не так - редирект на страницу списка билетов-мероприятий - там разберется. */
        $events_list_page = LKEventsTickets::find()->where(['model' => LKEventsTickets::class, 'visible' => 1])->one();
        if (isset($get['id'])) {
            $event = Events::findOne((int)$get['id']);
            if ($event) {
                $this->setMeta($model);
                /* найти список тарифов по купленным билетам для этого мероприятия */
                $tariffs_ids = (new \yii\db\Query())
                    ->select(['eventsorder_item.tariff_id'])
                    ->from('eventsorder_item')
                    ->leftJoin('eventsorder', 'eventsorder_item.eventorder_id = eventsorder.id')
                    ->where(['eventsorder.user_id' => $user->id, 'eventsorder.events_id' => $event->id, 'is_payed' => 1])
                    ->groupBy(['eventsorder_item.tariff_id'])
                    ->column();
                $tickets_list = [];
                foreach ($tariffs_ids as $tariff_id) {
                    $tickets_list[$tariff_id] = EventsorderItem::find()->leftJoin('eventsorder', 'eventsorder_item.eventorder_id = eventsorder.id')
                        ->where(['eventsorder.user_id' => $user->id, 'eventsorder.events_id' => $event->id, 'is_payed' => 1, 'tariff_id' => $tariff_id])->orderBy(['eventsorder_item.ticketNum' => SORT_DESC])->all();
                }
                return $this->render($model->view, ['model' => $model, 'event' => $event, 'tickets_list' => $tickets_list, 'tariffs_ids' => $tariffs_ids]);
            }
            return $this->redirect($events_list_page->getUrlPath());

        }
        return $this->redirect($events_list_page->getUrlPath());

    }

    /* страница просмотра мероприятия */

    public function actionEventsview($model, $id, $form_id = null, $event_form_id = null)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        /* если че не так - редирект на страницу списка мероприятий - там разберется. */
        $events_list_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();
        if (isset($get['id'])) {
            $event = Events::findOne((int)$get['id']);
            if ($event->author_id == $user->id) {
                if (in_array($event->status, [Events::STATUS_MODERATE, Events::STATUS_DECLINED])) {
                    return $this->redirect($events_list_page->getUrlPath());
                }
                $this->setMeta($model);

                $event_forms_list = $event->eventsFormsAll;
                if ($event_form_id) {
                    foreach ($event_forms_list as $key => $form) {
                        if ($event_form_id == $form->id) {
                            $event_active_form = $event_forms_list[$key];
                            break;
                        }
                    }
                } else {
                    $event_active_form = $event_forms_list[0];
                }
                $forms_list = $event->formsAll;
                if ($form_id) {
                    foreach ($forms_list as $key => $form) {
                        if ($form_id == $form->id) {
                            $active_form = $forms_list[$key];
                            break;
                        }
                    }
                } else {
                    $active_form = $forms_list[0];
                }
                /* количество и сумма заказов */
                $event_orders_data = Eventsorder::find()->select(['COUNT(*) as count', 'SUM(price) as summ'])->where([
                    'events_id' => $event->id,
                    'form_id' => $event_active_form->id,
                    'is_payed' => 1,
                ])->asArray()->one();
                $orders_data = Formsresult::find()->select(['COUNT(*) as count'])->where([
                    'form_id' => $active_form->id,
                    'is_payed' => 1,
                ])->asArray()->one();

                $event_orders_ids = $event->getOrders($event_active_form->id)->select(['id'])->asArray()->column();
                $event_order_items_query = Eventsorder::find()->where(['IN', 'id', $event_orders_ids]);

                $order_items_query = Formsresult::find()->where(['form_id' => $active_form->id]);

                // пагинация
                $event_countQuery = clone $event_order_items_query;
                $event_count_items = $event_countQuery->count();

                $countQuery = clone $order_items_query;
                $count_items = $countQuery->count();

                $pageparams = Yii::$app->request->get();
                unset($pageparams['model']);
                unset($pageparams['_csrf']);

                $event_pages = new Pagination([
                    'totalCount' => $event_count_items,
                    'defaultPageSize' => 20,
                    'route' => $model->getUrlPath(),
                    'params' => $pageparams,
                ]);

                $pages = new Pagination([
                    'totalCount' => $count_items,
                    'defaultPageSize' => 20,
                    'route' => $model->getUrlPath(),
                    'params' => $pageparams,
                ]);

                $event_order_items = $event_order_items_query->offset($event_pages->offset)
                    ->limit($event_pages->limit)
                    ->all();

                $order_items = $order_items_query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $event_html_data = '';
                    $html_data = '';
                    if (!empty($event_order_items)) {
                        foreach ($event_order_items as $event_order_item) {
                            $event_html_data .= $this->renderPartial('_event_orders_list_card', ['event_order_item' => $event_order_item]);
                        }
                    } else {
                        $event_html_data = '<tr><td colspan="4">По указанным параметрам заказы не найдены.</tr>';
                    }
                    if (!empty($order_items)) {
                        foreach ($order_items as $order_item) {
                            $html_data .= $this->renderPartial('_orders_list_card', ['event_order_item' => $order_item]);
                        }
                    } else {
                        $html_data = '<tr><td colspan="4">По указанным параметрам заказы не найдены.</tr>';
                    }
                    return [
                        'status' => 'success',
                        'event_html' => $event_html_data,
                        'html' => $html_data,
                        'event_pager' => \app\widgets\pagination\LinkPager::widget(['pages' => $event_pages, 'is_ajax' => true]),
                        'pager' => \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true]),
                    ];
                }

                return $this->render($model->view, [
                    'model' => $model,
                    'event' => $event,
                    'active_form' => $active_form,
                    'event_active_form' => $event_active_form,
                    'orders_data' => $orders_data,
                    'event_orders_data' => $event_orders_data,
                    'order_items' => $order_items,
                    'forms_list' => $forms_list,
                    'event_order_items' => $event_order_items,
                    'event_forms_list' => $event_forms_list,
                ]);
            }
            return $this->redirect($events_list_page->getUrlPath());

        }
        return $this->redirect($events_list_page->getUrlPath());

    }

    /* страница отмены мероприятия */
    public function actionEventscancel($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $event_id = Yii::$app->request->get('id', null);

        /* страница списка мероприятий */
        $events_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();

        /* если не указан id отменяемого мероприятия */
        if (!$event_id) {
            return $this->redirect($events_page->getUrlPath());
        }

        $original_event = Events::findOne((int)$event_id);

        /* если не нашли или редактировать пытается не автор, то редирект на список. */
        if ((!$original_event) or ($original_event->author_id != $user->id)) {
            return $this->redirect($events_page->getUrlPath());
        }

        /* если мероприятие ни в статусе На доработке или Опубликовано, или оно уже началось или у него нет купленных билетов - редирект на список */
        if (!in_array($original_event->status, [Events::STATUS_NEED_EDIT, Events::STATUS_PUBLIC])
            or (strtotime($original_event->event_date) <= time())
            or empty($original_event->ordersAll)) {
            /* редирект - такое мероприятие нельзя отменять через форму */
            return $this->redirect($events_page->getUrlPath());
        }

        $cancel_model = new EventCancelForm();
        $cancel_model->id = $original_event->id;
        /* заполнить стандартный текст письма */
        if (!empty($original_event->cancel_letter)) {
            $cancel_model->cancel_letter = $original_event->cancel_letter;
        } else {
            if (!empty(SettingsText::getInfo('cancel_letter_text'))) {
                $cancel_model->cancel_letter = SettingsText::getInfo('cancel_letter_text');
            }
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'cancel_model' => $cancel_model, 'original' => $original_event]);
    }

    /* страница переноса мероприятия */
    public function actionEventschangedate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $event_id = Yii::$app->request->get('id', null);

        /* страница списка мероприятий */
        $events_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();

        /* если не указан id переносимого мероприятия */
        if (!$event_id) {
            return $this->redirect($events_page->getUrlPath());
        }

        $original_event = Events::findOne((int)$event_id);

        /* если не нашли или редактировать пытается не автор, то редирект на список. */
        if ((!$original_event) or ($original_event->author_id != $user->id)) {
            return $this->redirect($events_page->getUrlPath());
        }

        /* если мероприятие не на доработке или готов к публикации, либо архивное, либо у него нет проданных билетов - редирект на список */
        if (!in_array($original_event->status, [Events::STATUS_NEED_EDIT, Events::STATUS_PUBLIC])
            or (strtotime($original_event->event_date_end) < strtotime(date('Y-m-d')))
            or empty($original_event->ordersAll)) {
            /* редирект - такое мероприятие нельзя переносить через форму */
            return $this->redirect($events_page->getUrlPath());
        }

        $changedate_model = new EventsDatechange();
        $changedate_model->events_id = $original_event->id;
        $changedate_model->new_event_date = Yii::$app->formatter->asDate($original_event->event_date, 'php:d.m.Y');
        $changedate_model->new_event_date_end = Yii::$app->formatter->asDate($original_event->event_date_end, 'php:d.m.Y');
        $changedate_model->new_event_time_start = $original_event->event_time_start;
        $changedate_model->new_event_time_end = $original_event->event_time_end;
        $changedate_model->new_event_timezone = $original_event->event_timezone;

        /* заполнить стандартный текст письма */
        $changedate_model->letter_text = SettingsText::getInfo('changedate_letter_text');

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'changedate_model' => $changedate_model, 'original' => $original_event]);
    }

    /* АПИ */
    public function actionSavenews()
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
        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание новости',
            ];
        }

        // проверить валидацию и создать типовую услугу
        $modelform = new LKNews();

        $modelform->image = UploadedFile::getInstance($modelform, 'image');
        $modelform->video1 = UploadedFile::getInstance($modelform, 'video1');
        $modelform->video2 = UploadedFile::getInstance($modelform, 'video2');

        // проверить существующие теги
        $post = Yii::$app->request->post();
        if (!empty($post['LKNews']['tags'])) {
            foreach ($post['LKNews']['tags'] as $key => $tag) {
                if ((int)$tag == 0) {
                    $lower = trim(mb_strtolower($tag), 'UTF-8');
                    $tag_exists = \app\modules\reference\models\Lentatag::find()->where(['like binary', 'name', $lower])->one();
                    if (!empty($tag_exists)) {
                        $post['LKNews']['tags'][$key] = strval($tag_exists->id);
                    }
                }
            }
        }
        if (Yii::$app->request->isAjax && $modelform->sanitize($post) && $modelform->validate()) {
            $send_to_direct = false;
            if (!empty($modelform->id)) {
                // редактирование новости
                $new_news = News::findOne((int)$modelform->id);
                if ($new_news->author_id != $user->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка доступа к новости',
                    ];
                }
                /* если были отмечены флаги "Удалить изображение" */
                $attributes = ['image', 'video1', 'video2'];
                foreach ($attributes as $image_attribute) {
                    if ($modelform->{$image_attribute . '_delete'} && !empty($new_news->{$image_attribute})) {
                        $new_news->{$image_attribute}->delete();
                    }
                }

            } else {
                // создание новости
                if (is_null($user->directionM)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Главная кафедра не задана',
                    ];
                }
                $new_news = new News();
                $new_news->author_id = $user->id;
                $new_news->direction_id = $user->directionM->id;
                $new_news->published = date('d.m.Y H:i:s');
            }

            $new_news->name = $modelform->name;
            if (!empty($modelform->url)) {
                $new_news->url = $modelform->url;
            } else {
                $new_news->url = date('d.m.Y') . $modelform->name;
            }
            $new_news->city_id = $modelform->city_id;
            $new_news->content = $modelform->content;
            $new_news->direction = $modelform->direction;
            $new_news->tags = $modelform->tags;
            $new_news->start_publish = $modelform->start_publish;
            $new_news->end_publish = $modelform->end_publish;
            $new_news->video1_name = $modelform->video1_name;
            $new_news->video1_link = $modelform->video1_link;
            $new_news->video2_name = $modelform->video2_name;
            $new_news->video2_link = $modelform->video2_link;
            $new_news->image_loader = $modelform->image;
            $new_news->video1_loader = $modelform->video1;
            $new_news->video2_loader = $modelform->video2;
            $new_news->visible = $modelform->visible;
            $new_news->keywords = $modelform->keywords;
            switch ($modelform->vis_for) {
                case 'all':
                    $new_news->vis_fiz = 1;
                    $new_news->vis_ur = 1;
                    break;
                case 'experts':
                    $new_news->vis_fiz = 0;
                    $new_news->vis_ur = 0;
                    break;
            }
            $new_news->vis_expert = 1;
            $new_news->vis_exporg = 1;

            if (!empty($post['LKNews']['tags'])) {
                foreach ($post['LKNews']['tags'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $new_news->tags[$key]->name = $tag;
                    }
                }
            }

            if ($new_news->save()) {
                /* страница списка новостей */
                $news_list_page = LKNewsList::find()->where(['model' => LKNewsList::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $news_list_page->getUrlPath(),
                    'message' => 'Новость успешно создана и будет опубликована в указанное время.',
                ];
            }
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'news_edit_log.txt', date('d.m.Y H:i:s') . 'Ошибка при сохранении формы. Данные: ' . "\r\n" . var_export($new_news->errors, true) . "\r\n" . "Данные с формы: \r\n" . var_export($_POST, true));
            return [
                'status' => 'fail',
                'error' => $new_news->getErrors(),
                'tags' => $new_news->tags,
                'post' => $post['LKNews']['tags'],
                'message' => 'При сохранении новости возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionSaveproject()
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
        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание проекта',
            ];
        }

        // проверить валидацию и создать типовую услугу
        $modelform = new LKProject();
        $modelform->image = UploadedFile::getInstance($modelform, 'image');
        $modelform->videoimage = UploadedFile::getInstance($modelform, 'videoimage');

        // проверить существующие теги
        $post = Yii::$app->request->post();
        if (!empty($post['LKProject']['tags'])) {
            foreach ($post['LKProject']['tags'] as $key => $tag) {
                if ((int)$tag == 0) {
                    $lower = trim(mb_strtolower($tag), 'UTF-8');
                    $tag_exists = \app\modules\reference\models\Lentatag::find()->where(['like binary', 'name', $lower])->one();
                    if (!empty($tag_exists)) {
                        $post['LKProject']['tags'][$key] = strval($tag_exists->id);
                    }
                }
            }
        }
        if (Yii::$app->request->isAjax && $modelform->sanitize($post) && $modelform->validate()) {
            $send_to_direct = false;
            if (!empty($modelform->id)) {
                // редактирование проекта
                $new_project = Project::findOne((int)$modelform->id);
                if ($new_project->author_id != $user->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка доступа к проекту',
                    ];
                }
                /* если были отмечены флаги "Удалить изображение" */
                $attributes = ['image', 'videoimage'];
                foreach ($attributes as $image_attribute) {
                    if ($modelform->{$image_attribute . '_delete'} && !empty($new_project->{$image_attribute})) {
                        $new_project->{$image_attribute}->delete();
                    }
                }
            } else {
                if (is_null($user->directionM)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Главная кафедра не задана',
                    ];
                }
                // создание проекта
                $new_project = new Project();
                $new_project->author_id = $user->id;
                $new_project->direction_id = $user->directionM->id;
                $new_project->published = date('d.m.Y H:i:s');

            }

            $new_project->name = $modelform->name;
            $new_project->city_id = $modelform->city_id;
            $new_project->content = $modelform->content;
            $new_project->direction = $modelform->direction;
            $new_project->tags = $modelform->tags;
            $new_project->image_loader = $modelform->image;
            $new_project->start_publish = $modelform->start_publish;
            $new_project->end_publish = $modelform->end_publish;
            $new_project->visible = $modelform->visible;
            $new_project->keywords = $modelform->keywords;
            $new_project->video_name = $modelform->video_name;
            $new_project->video_link = $modelform->video_link;
            $new_project->videoimage_loader = $modelform->videoimage;
            switch ($modelform->vis_for) {
                case 'all':
                    $new_project->vis_fiz = 1;
                    $new_project->vis_ur = 1;
                    break;
                case 'experts':
                    $new_project->vis_fiz = 0;
                    $new_project->vis_ur = 0;
                    break;
            }
            $new_project->vis_expert = 1;
            $new_project->vis_exporg = 1;

            if (!empty($post['LKProject']['tags'])) {
                foreach ($post['LKProject']['tags'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $new_project->tags[$key]->name = $tag;
                    }
                }
            }

            if ($new_project->save()) {
                /* страница списка проетов */
                $project_list_page = LKProjectList::find()->where(['model' => LKProjectList::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $project_list_page->getUrlPath(),
                    'message' => 'Проект успешно создан и будет опубликован в указанное время.',
                ];
            }
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'project_edit_log.txt', date('d.m.Y H:i:s') . 'Ошибка при сохранении формы. Данные: ' . "\r\n" . var_export($new_project->errors, true) . "\r\n" . "Данные с формы: \r\n" . var_export($_POST, true));
            return [
                'status' => 'fail',
                'message' => 'При сохранении проекта возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionSavematerial()
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
        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание материала',
            ];
        }

        // проверить валидацию и создать типовой материал
        $modelform = new LKMaterial();
        $modelform->image = UploadedFile::getInstance($modelform, 'image');
        $modelform->videoimage = UploadedFile::getInstance($modelform, 'videoimage');

        // проверить существующие теги
        $post = Yii::$app->request->post();
        if (!empty($post['LKMaterial']['tags'])) {
            foreach ($post['LKMaterial']['tags'] as $key => $tag) {
                if ((int)$tag == 0) {
                    $lower = trim(mb_strtolower($tag), 'UTF-8');
                    $tag_exists = \app\modules\reference\models\Lentatag::find()->where(['like binary', 'name', $lower])->one();
                    if (!empty($tag_exists)) {
                        $post['LKMaterial']['tags'][$key] = strval($tag_exists->id);
                    }
                }
            }
        }
        if (Yii::$app->request->isAjax && $modelform->sanitize($post) && $modelform->validate()) {
            $send_to_direct = false;
            if (!empty($modelform->id)) {
                // редактирование проекта
                $new_material = Material::findOne((int)$modelform->id);
                if ($new_material->author_id != $user->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка доступа к материалу',
                    ];
                }
                /* если были отмечены флаги "Удалить изображение" */
                $attributes = ['image', 'videoimage'];
                foreach ($attributes as $image_attribute) {
                    if ($modelform->{$image_attribute . '_delete'} && !empty($new_material->{$image_attribute})) {
                        $new_material->{$image_attribute}->delete();
                    }
                }
            } else {
                if (is_null($user->directionM)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Главная кафедра не задана',
                    ];
                }
                // создание материала
                $new_material = new Material();
                $new_material->author_id = $user->id;
                $new_material->direction_id = $user->directionM->id;
                $new_material->published = date('d.m.Y H:i:s');
            }

            $new_material->name = $modelform->name;
            if (!empty($modelform->url)) {
                $new_material->url = $modelform->url;
            } else {
                $new_material->url = date('d.m.Y') . $modelform->name;
            }
            $new_material->description = $modelform->description;
            $new_material->link = $modelform->link;
            $new_material->image_loader = $modelform->image;
            $new_material->content = $modelform->content;
            $new_material->direction = $modelform->direction;
            $new_material->tags = $modelform->tags;
            $new_material->start_publish = $modelform->start_publish;
            $new_material->end_publish = $modelform->end_publish;
            $new_material->visible = $modelform->visible;
            $new_material->keywords = $modelform->keywords;
            $new_material->video_name = $modelform->video_name;
            $new_material->video_link = $modelform->video_link;
            $new_material->videoimage_loader = $modelform->videoimage;

            switch ($modelform->vis_for) {
                case 'all':
                    $new_material->vis_fiz = 1;
                    $new_material->vis_ur = 1;
                    break;
                case 'experts':
                    $new_material->vis_fiz = 0;
                    $new_material->vis_ur = 0;
                    break;
            }
            $new_material->vis_expert = 1;
            $new_material->vis_exporg = 1;

            if (!empty($post['LKMaterial']['tags'])) {
                foreach ($post['LKMaterial']['tags'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $new_material->tags[$key]->name = $tag;
                    }
                }
            }
            if ($new_material->save()) {
                /* страница списка материалов */
                $material_list_page = LKMaterialList::find()->where(['model' => LKMaterialList::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $material_list_page->getUrlPath(),
                    'message' => 'Материал успешно создан и будет опубликован в указанное время.',
                ];
            }
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'material_edit_log.txt', date('d.m.Y H:i:s') . 'Ошибка при сохранении формы. Данные: ' . "\r\n" . var_export($new_material->errors, true) . "\r\n" . "Данные с формы: \r\n" . var_export($_POST, true));
            return [
                'status' => 'fail',
                'message' => 'При сохранении материала возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        } elseif (!empty($modelform->errors)) {
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'material_edit_log.txt', date('d.m.Y H:i:s') . 'Ошибка при валидации формы. Данные: ' . "\r\n" . var_export($modelform->errors, true) . "\r\n" . "Данные с формы: \r\n" . var_export($_POST, true));
            return [
                'status' => 'fail',
                'message' => 'При сохранении материала возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionSaveevent()
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
        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;

        /* публиковать мероприятия могут только Участники АСТ */
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание мероприятия',
            ];
        }

        /* проверяем указанный шаг и загружаем соответствующий сценарий для модели LKEvent */
        $curr_step = Yii::$app->request->get('step', '1');
        if (in_array($curr_step, ['1', '2', '3', '4', '5'])) {
            $modelform = new LKEvent();
            $modelform->scenario = 'step' . $curr_step;
        } else {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры',
            ];
        }

        // загружаем изображения для валидации на бэке (все изображения у нас на 3-м шаге)
        if ($curr_step == 3) {
            $modelform->report = UploadedFile::getInstances($modelform, 'report');
            $modelform->image = UploadedFile::getInstance($modelform, 'image');
            $modelform->video1 = UploadedFile::getInstance($modelform, 'video1');
            $modelform->video2 = UploadedFile::getInstance($modelform, 'video2');
        }

        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {

            /* получаем данные из POST */
            $post = Yii::$app->request->post();

            /* если в полученных данных указан id - ищем мероприятие */
            if (!empty($modelform->id)) {
                $new_event = Events::findOne((int)$modelform->id);
                /* если текущий пользователь не является организатором мероприятия - ошибка. */
                if ($new_event->author_id != $user->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'У вас нет прав на редактирование мероприятия',
                    ];
                }
                /* иначе создаем новое */
            } else {
                /* пользователи, не зарегистрированные на МП больше не могут создавать мероприятия */
                if (!$user->organization->can_service) {
                    return [
                        'status' => 'fail',
                        'message' => 'Для создания мероприятия требуется зарегистрироваться на маркетплейс.',
                    ];
                }

                if (is_null($user->directionM)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Главная кафедра не задана',
                    ];
                }
                $new_event = new Events();
                /* заполняем обязательные поля (name и format_id должны быть заполнены на первом шаге.) */
                $new_event->event_date = date('d.m.Y');
                $new_event->event_date_end = date('d.m.Y');
                $new_event->event_time_start = '00:00';
                $new_event->event_time_end = '00:00';
                $new_event->status = Events::STATUS_NEW;
                $new_event->visible = 1;
                $new_event->author_id = $user->id;
                $new_event->contact_email = $user->email;
                $new_event->direction_id = $user->directionM->id;
            }

            /* в зависимости от текущего шага выполняем необходимые действия */
            /* заявка на модерацию заполняется только если есть изменённые данные, подлежащие модерации */
            $moderation = false;

            switch ($curr_step) {
                case '1':
                    /* заполняем немодерируемые поля из формы в мероприятие */
                    $new_event->format_id = $modelform->format_id;
                    $new_event->age_id = $modelform->age_id;

                    /* модерация требуется, если мероприятие новое, или поля name, anons, licensed были изменены */
                    if ($new_event->isNewRecord
                        or ($new_event->name != $modelform->name)
                        or ($new_event->anons != $modelform->anons)
                        or ($new_event->licensed != $modelform->licensed)) {

                        /* если мероприятие новое или у него нет текущей записи о модерации, то создаем новую модерацию */
                        if ($new_event->isNewRecord or empty($new_event->currentModeration)) {
                            $moderation = $new_event->addNewModeration();
                        } else {
                            /* достаем текущую заявку на модерацию */
                            $moderation = $new_event->currentModeration;
                        }

                        /* если мероприятие новое, то */
                        if ($new_event->isNewRecord) {
                            /* модерация первичная */
                            $moderation->first_moderation = true;
                            /* поля name и licensed заполняем и для мероприятия тоже */
                            $new_event->name = $modelform->name;
                            $new_event->anons = $modelform->anons;
                            $new_event->licensed = $modelform->licensed;
                        }

                        /* заполняем поля модерации */
                        $moderation->name = $modelform->name;
                        $moderation->anons = $modelform->anons;
                        $moderation->licensed = $modelform->licensed;
                    }
                    break;
                case '2':
                    /* на втором шаге нет модерируемых полей - просто заполняем данные */
                    if (empty($new_event->ordersAll)) {
                        /* для мероприятий с проданными билетами дата заполняется не здесь */
                        $new_event->event_date = $modelform->event_date;
                        $new_event->event_date_end = $modelform->event_date_end;
                        $new_event->event_time_start = $modelform->event_time_start;
                        $new_event->event_time_end = $modelform->event_time_end;
                        $new_event->event_timezone = $modelform->event_timezone;
                    }
                    $new_event->city_id = $modelform->city_id;
                    $new_event->street = $modelform->street;
                    $new_event->place = $modelform->place;
                    $new_event->online_place = $modelform->online_place;
                    $new_event->translation = $modelform->translation;
                    $new_event->type = $modelform->type;
                    break;
                case '3':
                    /* заполняем тэги */
                    /* теги идут без модерации, пока. Ждем задачу на объединение тегов */
                    $new_event->tags = $modelform->tags;
                    $new_event->keywords = $modelform->keywords;
                    if (!empty($post['LKEvent']['tags'])) {
                        $tags_list = $new_event->tags;
                        foreach ($post['LKEvent']['tags'] as $key => $tag) {
                            if ((int)$tag == 0) {
                                $exist_tag = Eventstag::find()->where(['name' => $tag])->one();
                                if ($exist_tag) {
                                    $tags_list[$key] = $exist_tag;
                                } else {
                                    $tags_list[$key]->name = $tag;
                                }
                            }
                        }
                        $new_event->tags = $tags_list;
                    }

                    /* если были отмечены флаги "Удалить изображение" */
                    /* удаляем то изображение, которое пользователь видел, когда нажимал "Удалить" */
                    $attributes = ['image', 'video1', 'video2'];
                    foreach ($attributes as $image_attribute) {
                        if ($modelform->{$image_attribute . '_delete'}) {
                            /* если есть текущая модерация и у модели модерации есть загруженное изображение в этом поле - удаляем */
                            if (!empty($new_event->currentModeration) && !empty($new_event->currentModeration->{$image_attribute})) {
                                $new_event->currentModeration->{$image_attribute}->delete();
                            } elseif (!empty($new_event->{$image_attribute})) {
                                /* иначе - если в модели мероприятия есть загруженное изображение - удаляем */
                                $new_event->{$image_attribute}->delete();
                            }
                        }
                    }
                    $preloaded_report = $new_event->report;
                    if ($new_event->currentModeration) {
                        $preloaded_report = array_merge($preloaded_report, $new_event->currentModeration->report);
                        if (!empty($new_event->currentModeration->remove_report)) {
                            foreach ($preloaded_report as $key => $image) {
                                if (in_array($image->id, $new_event->currentModeration->remove_report)) {
                                    unset($preloaded_report[$key]);
                                }
                            }
                        }
                    }
                    $preloaded_ids = ArrayHelper::map($preloaded_report, 'id', 'id');
                    /* если предзагруженные изображения отличаются от предзагруженных в форме - значит что-то удалили - отправляем в модерацию */
                    $model_preloaded = $modelform->report_preload ? $modelform->report_preload : [];
                    $report_to_delete = array_diff($preloaded_ids, $model_preloaded);
                    /* если были отредактированы модерируемые поля, или были загружены изображения, то добавляем данные в модерацию */
                    if (($new_event->content != $modelform->content)
                        or ($new_event->dop_content != $modelform->dop_content)
                        or ($new_event->video1_name != $modelform->video1_name)
                        or ($new_event->video1_link != $modelform->video1_link)
                        or ($new_event->video2_name != $modelform->video2_name)
                        or ($new_event->video2_link != $modelform->video2_link)
                        or (!empty($modelform->image))
                        or (!empty($modelform->video1))
                        or (!empty($modelform->video2))
                        or (!empty($modelform->report))
                        or (!empty($report_to_delete))
                    ) {

                        /* если у мероприятия нет текущей записи о модерации, то создаем новую модерацию */
                        if (empty($new_event->currentModeration)) {
                            $moderation = $new_event->addNewModeration();
                        } else {
                            /* достаем текущую заявку на модерацию */
                            $moderation = $new_event->currentModeration;
                        }

                        /* если мероприятие новое, то заполняем поля и для мероприятия */
                        if ($new_event->status == Events::STATUS_NEW) {
                            $new_event->content = $modelform->content;
                            $new_event->dop_content = $modelform->dop_content;
                            $new_event->video1_name = $modelform->video1_name;
                            $new_event->video1_link = $modelform->video1_link;
                            $new_event->video2_name = $modelform->video2_name;
                            $new_event->video2_link = $modelform->video2_link;
                        }

                        /* заполняем поля модерации */
                        $moderation->content = $modelform->content;
                        $moderation->dop_content = $modelform->dop_content;
                        $moderation->video1_name = $modelform->video1_name;
                        $moderation->video1_link = $modelform->video1_link;
                        $moderation->video2_name = $modelform->video2_name;
                        $moderation->video2_link = $modelform->video2_link;

                        /* если были загружены изображения - добавляем их только к заявке на модерацию (чтобы не дублировать) */
                        $moderation->image_loader = $modelform->image;
                        $moderation->video1_loader = $modelform->video1;
                        $moderation->video2_loader = $modelform->video2;

                        /* добавляем файлы галереи к заявке на модерацию */
                        if (!empty($modelform->report)) {
                            $moderation->report_loader = $modelform->report;
                        }

                        if (!empty($report_to_delete)) {
                            foreach ($report_to_delete as $id_to_del) {
                                // если нужно удалить изображение, привязанное к заявке на модерацию - удаляем.
                                // если нужно удалить изображение, привязанное к модели мероприятия - добавляем в список к удалению.
                                $image = FilestoreModel::findOne($id_to_del);
                                if (($image->keeper_class == Events::class) && ($image->keeper_id == $new_event->id)) {
                                    $moderation->remove_report = array_merge($moderation->remove_report ?? [], [$id_to_del]);
                                } elseif (($image->keeper_class == Eventmoder::class) && ($image->keeper_id == $moderation->id)) {
                                    $image->delete();
                                }
                            }
                        }
                    }
                    break;
                case '4':
                    $new_event->need_tariff = $modelform->need_tariff;
                    // если регистрация не требуется, то формы и тарифы не удаляются, просто не выводятся.
                    // если требуется регистрация (тарифы/билеты)
                    if ($modelform->need_tariff) {
                        // если не пришло ни одной формы - удалить действующие формы с тарифами
                        if (count($modelform['forms']) == 0) {
                            $new_event->need_tariff = 0;
                            // удалить формы
                            foreach ($new_event->eventsFormsAll as $event_form) {
                                $event_form->delete();
                            }
                        } else {
                            // удалить формы, о которых нет данных в POST
                            $id_list_current = ArrayHelper::map($new_event->eventsFormsAll, 'id', 'id');
                            // смотрим какие формы пришли:
                            foreach ($modelform['forms'] as $key => $form_data) {
                                if (isset($form_data['id']) && in_array($form_data['id'], $id_list_current)) {
                                    // редактируем существующую форму
                                    $event_form = Eventsform::findOne((int)$form_data['id']);
                                    // проверить каждое поле, чтобы не удалять неизменные
                                    unset($id_list_current[$form_data['id']]);
                                } else {
                                    // id не задан либо некорректен - создаем новую форму
                                    $event_form = new Eventsform();
                                    $event_form->event_id = $new_event->id;
                                }
                                $event_form->name = $form_data['name'];
                                $event_form->payregister = $form_data['payregister'];
                                $event_form->order = $key;
                                // поля формы не редактируем - просто заменяем на полученные.
                                $event_form->form_fields = $form_data['fields'];
                                if ($event_form->save()) {
                                    // найти тарифы со временной привязкой к сохраненной форме, заменить привязку
                                    $new_tariff = Tariff::find()->where(['event_form_id' => 'temp_' . $new_event->id . '_' . $key, 'visible' => 1])->all();
                                    foreach ($new_tariff as $new_trf) {
                                        $new_trf->event_form_id = $event_form->id;
                                        $new_trf->save();
                                    }
                                } else {
                                    $log_path = Yii::getAlias('@app/logs/');
                                    file_put_contents($log_path . 'event_edit_log.txt', date('d.m.Y H:i:s') . ' - Ошибка сохранения формы регистрации к мероприятию ' . $new_event->id . "\r\n" . var_export($event_form->errors, true) . "\r\n", FILE_APPEND);
                                    return [
                                        'status' => 'fail',
                                        'message' => 'При сохранении формы регистрации возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
                                    ];
                                }
                            }
                            // если после обработки в id_list_current остались элементы - удалить эти формы
                            if (!empty($id_list_current)) {
                                foreach ($id_list_current as $form_id) {
                                    $delete_form = Eventsform::findOne((int)$form_id);
                                    $delete_form->delete();
                                }
                            }
                            // удалить тарифы мероприятия, у которых осталась временная привязка
                            $lost_tariff = Tariff::find()->where(['page_id' => $new_event->id, 'visible' => 1])->andWhere(['like', 'event_form_id', 'temp'])->all();
                            foreach ($lost_tariff as $lost_trf) {
                                $lost_trf->delete();
                            }
                        }
                    }
                    break;
                case '5':
                    // финальный шаг - убираем из черновиков.

                    if ($modelform->start_publish_late == 1) {
                        // время публикации и часовой пояс пока не учитываю (сроки...)
                        $new_event->start_publish = $modelform->start_publish_date;
                    } else {
                        $new_event->start_publish = date('d.m.Y');
                    }
                    /* достаем текущую заявку на модерацию, если есть */
                    $moderation = $new_event->currentModeration;

                    if (($new_event->contact_email != $modelform->contact_email)
                        or ($new_event->contact_phone != $modelform->contact_phone)
                        or ($new_event->contact_wa != $modelform->contact_wa)
                        or ($new_event->contact_telegram != $modelform->contact_telegram)) {

                        /* если у мероприятия нет текущей записи о модерации, то создаем новую модерацию */
                        if (empty($moderation)) {
                            $moderation = $new_event->addNewModeration();
                        }

                        /* если мероприятие новое, то заполняем поля и для мероприятия */
                        if ($new_event->status == Events::STATUS_NEW) {
                            $new_event->contact_email = $modelform->contact_email;
                            $new_event->contact_phone = $modelform->contact_phone;
                            $new_event->contact_wa = $modelform->contact_wa;
                            $new_event->contact_telegram = $modelform->contact_telegram;
                        }

                        /* заполняем поля модерации */
                        $moderation->contact_email = $modelform->contact_email;
                        $moderation->contact_phone = $modelform->contact_phone;
                        $moderation->contact_wa = $modelform->contact_wa;
                        $moderation->contact_telegram = $modelform->contact_telegram;
                    }
                    switch ($modelform->vis_for) {
                        case 'all':
                            $new_event->vis_fiz = 1;
                            $new_event->vis_ur = 1;
                            break;
                        case 'experts':
                            $new_event->vis_fiz = 0;
                            $new_event->vis_ur = 0;
                            break;
                    }
                    $new_event->vis_expert = 1;
                    $new_event->vis_exporg = 1;

                    /* если у мероприятия есть заявка на модерацию в статусе Новая - то отправляем мероприятие на модерацию */
                    if (!empty($moderation)) {
                        $new_event->status = Events::STATUS_MODERATE;
                        $moderation->status = Eventmoder::STATUS_MODERATE;
                    }
                    break;
            }

            /* сохраняем мероприятие */
            if ($new_event->save()) {
                /* если для мероприятия есть изменения в модерации */
                if (!empty($moderation)) {
                    /* если заявка на модерацию новая, привязываем её к мероприятию */
                    if ($moderation->isNewRecord) {
                        $moderation->event_id = $new_event->id;
                    }
                    /* сохраняем заявку на модерацию */
                    if (!$moderation->save()) {
                        /* если не сохранилось - пишем лог и выводим ошибку пользователю */
                        $log_path = Yii::getAlias('@app/logs/');
                        file_put_contents($log_path . 'event_edit_log.txt', date('d.m.Y H:i:s') . ' - Ошибка сохранения заявки на модерацию к мероприятию ' . $new_event->id . "\r\n" . var_export($moderation->errors, true) . "\r\n", FILE_APPEND);
                        return [
                            'status' => 'fail',
                            'message' => 'При сохранении мероприятия возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
                        ];
                    }
                }
                /* следующий шаг */
                $next_step = ++$curr_step;
                /* если шаги закончились */
                if ($next_step > 5) {
                    // готово, возвращаемся к списку мероприятий
                    $next_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();
                    $redirect_to = $next_page->getUrlPath();
                    if ($new_event->licensed && !empty(SettingsText::getInfo('licensed_modal'))) {
                        $message = SettingsText::getInfo('licensed_modal');
                    } else {
                        $message = SettingsText::getInfo('event_moder_modal');
                    }
                } else {
                    // следующий шаг
                    $next_page = LKEventsEdit::find()->where(['model' => LKEventsEdit::class, 'visible' => 1])->one();
                    $redirect_to = Url::toRoute([$next_page->getUrlPath(), 'step' => $next_step, 'id' => $new_event->id]);
                    $message = 'Переход на следующий шаг.';
                }
                /* свозвращаем результат */
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_to,
                    'message' => $message,
                    /* если мероприятие на модерации - то показать сообщение о модерации */
                    'show_message' => ($new_event->status == Events::STATUS_MODERATE ? 'show' : 'hide'), // show
                ];
            }
            /* если мероприятие не сохранилось - пишем лог и выдаем ошибку пользователю */
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'event_edit_log.txt', date('d.m.Y H:i:s') . ' - Ошибка сохранения мероприятия ' . $new_event->id . "\r\n" . var_export($new_event->errors, true) . "\r\n", FILE_APPEND);
            return [
                'status' => 'fail',
                'message' => 'При сохранении мероприятия возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    /* отправка формы отмены мероприятия */
    public function actionSendcancelform()
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

        /* публиковать мероприятия могут только Участники АСТ */
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на отмену мероприятия',
            ];
        }

        $modelform = new EventCancelForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            /* находим мероприятие, которое нужно отменить */
            $original_event = Events::findOne((int)$modelform->id);
            if (empty($original_event)) {
                /* если не нашли - ошибка */
                return [
                    'status' => 'fail',
                    'message' => 'Мероприятие не найдено',
                ];
            }

            /* если автор не соответствует текущему пользователю - ошибка */
            if ($original_event->author_id != $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'У вас нет прав на редактирование мероприятия',
                ];
            }

            /* если мероприятие ни в статусе На доработке или Опубликовано, или оно уже началось или у него нет купленных билетов - ошибка */
            if (!in_array($original_event->status, [Events::STATUS_NEED_EDIT, Events::STATUS_PUBLIC])
                or (strtotime($original_event->event_date) <= time())
                or empty($original_event->ordersAll)) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно отменить мероприятие. Условия не соблюдены.',
                ];
            }

            /* все ок - переносим поля, ставим статус и уведомляем администратора */
            $original_event->cancel_reason = $modelform->cancel_reason;
            $original_event->cancel_letter = $modelform->cancel_letter;
            $original_event->status = Events::STATUS_CANCELLED;
            if ($original_event->save()) {
                /* отправляем уведомление администратору */
                $original_event->sendCancelLetterAdmin();
                /* и сообщение пользователю */
                $redirect_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();
                $redirect_to = $redirect_page->getUrlPath();
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_to,
                    'message' => 'Мероприятие успешно отменено',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно отменить мероприятие. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    /* отправка формы переноса мероприятия */
    public function actionSendchangedateform()
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

        /* публиковать мероприятия могут только Участники АСТ */
        if (!in_array($user->role, ['expert', 'exporg'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на отмену мероприятия',
            ];
        }

        $modelform = new EventsDatechange();
        /* при сохранении все поля дозаполнятся автоматически */
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->save()) {
            /* после сейва чтоб даты прогрузились в нужном формате */
            $modelform->refresh();
            /* меняем дату проведения у мероприятия */
            $original_event = $modelform->event;
            $original_event->event_date = $modelform->new_event_date;
            $original_event->event_date_end = $modelform->new_event_date_end;
            $original_event->event_time_start = $modelform->new_event_time_start;
            $original_event->event_time_end = $modelform->new_event_time_end;
            $original_event->event_timezone = $modelform->new_event_timezone;
            if ($original_event->save()) {
                /* отправляем уведомление администратору */
                $modelform->sendChangeDateLetterAdmin();
                /* и сообщение пользователю */
                $redirect_page = LKEventsList::find()->where(['model' => LKEventsList::class, 'visible' => 1])->one();
                $redirect_to = $redirect_page->getUrlPath();
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_to,
                    'message' => 'Мероприятие успешно перенесено',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно изменить дату мероприятия. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionCopyevent()
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
                'message' => 'У вас нет прав на создание мероприятия',
            ];
        }

        /* если пользователь без регистрации на МП - отправить на страницу создания мероприятия. Там будет текст про регистрацию */
        if (!$user->organization->can_service) {
            $edit_page = LKEventsEdit::find()->where(['model' => LKEventsEdit::class, 'visible' => 1])->one();
            $edit_url = (!empty($edit_page) ? $edit_page->getUrlPath() : false);
            return [
                'status' => 'success',
                'redirect_to' => $edit_url,
                'message' => 'Создание мероприятия запрещено.',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_event = Events::findOne($origin_id);
            if (!empty($origin_event) && ($origin_event->author_id == $user->id)) {
                $copy_element = $origin_event->addCopy();
                if ($copy_element) {
                    /* сразу при создании копии мероприятия создаем первичную заявку на модерацию, т.к. пользователь может изменить только дату проведения (не является модерируемым полем) и отправить мероприятие на публикацию. */
                    $moderation = $copy_element->addNewModeration();
                    $moderation->first_moderation = true;
                    $moderation->event_id = $copy_element->id;
                    $moderation->save();

                    $edit_page = LKEventsEdit::find()->where(['model' => LKEventsEdit::class, 'visible' => 1])->one();
                    $edit_url = (!empty($edit_page) ? Url::toRoute([$edit_page->getUrlPath(), 'id' => $copy_element->id]) : false);
                    return [
                        'status' => 'success',
                        'redirect_to' => $edit_url,
                        'message' => 'Создано новое мероприятие на основе выбранного',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно создать копию мероприятия',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Мероприятие не найдено',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionDeleteevent()
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
                'message' => 'У вас нет прав на удаление мероприятия',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_event = Events::findOne($origin_id);
            if (!empty($origin_event) && ($origin_event->author_id == $user->id)) {
                /* пробуем удалить */
                if ($origin_event->delete()) {
                    return [
                        'status' => 'success',
                        'message' => 'Мероприятие успешно удалено',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно удалить мероприятие. Условия не соблюдены.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Мероприятие не найдено',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionCancellevent()
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
                'message' => 'У вас нет прав на работу с мероприятием',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_event = Events::findOne($origin_id);
            if (!empty($origin_event) && ($origin_event->author_id == $user->id)) {
                /* если мероприятие в статусе Опубликовано или На доработке то его можно отменить */
                if (in_array($origin_event->status, [Events::STATUS_NEED_EDIT, Events::STATUS_PUBLIC])) {
                    /* если мероприятие еще не началось, либо на него нет приобретённых билетов - то можно отменить */
                    if (strtotime($origin_event->event_date) > time() or empty($origin_event->ordersAll)) {
                        /* если на мероприятие уже были куплены билеты (даже если не оплачены) */
                        if (!empty($origin_event->ordersAll)) {
                            /* редирект на страницу заполнения формы с письмом */
                            $cancel_page = LKEventsCancel::find()->where(['model' => LKEventsCancel::class, 'visible' => 1])->one();
                            if ($cancel_page) {
                                return [
                                    'status' => 'success',
                                    'redirect_to' => Url::toRoute([$cancel_page->getUrlPath(), 'id' => $origin_event->id]),
                                    'message' => 'Переход на страницу отмены',
                                ];
                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Отмена мероприятий с купленными билетами невозможна. ' . \app\helpers\MainHelper::getHelpText()
                            ];

                        }
                        /* просто меняем статус на Отменено */
                        $origin_event->status = Events::STATUS_CANCELLED;
                        if ($origin_event->save()) {
                            return [
                                'status' => 'success',
                                'message' => 'Мероприятие успешно отменено',
                            ];
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Ошибка при изменении мероприятия. ' . \app\helpers\MainHelper::getHelpText(),
                        ];


                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно отменить мероприятие, которое уже идет, если есть приобретённые билеты',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно отменить мероприятие. Условия не соблюдены.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Мероприятие не найдено',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];


    }

    public function actionCleareventimage()
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
                'message' => 'У вас нет прав на редактирование мероприятия',
            ];
        }
        $event_id = Yii::$app->request->get('event_id', null);
        $new_event = Events::findOne((int)$event_id);
        if ((empty($new_event)) or ($new_event->author_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование мероприятия',
            ];
        }
        if ($new_event->image) {
            if ($new_event->image->delete()) {
                return [
                    'status' => 'success',
                    'message' => 'Обложка удалена',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно удалить обложку',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Обложка отсутствует',
        ];

    }

    public function actionSavetariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* сохранить тарифф */

        $modelform = new LKEvent();
        $modelform->scenario = 'tariff';
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            if (!$user->organization->can_service) {
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете добавлять тарифы к мероприятию',
                ];
            }
            // найти мероприятие
            $event = Events::findOne((int)$modelform->id);
            if ($event->author_id == $user->id) {
                if (!empty($modelform->tariff_id)) {
                    $tariff = Tariff::findOne((int)$modelform->tariff_id);
                    $action = 'edit';
                    if ($tariff->event->id != $event->id) {
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно создать тариф для этого мероприятия.',
                        ];
                    }
                } else {
                    $tariff = new Tariff();
                    $action = 'create';
                }
                if (empty($modelform->tariff_prices) or empty($modelform->tariff_prices_dates)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Не переданы цены для тарифа',
                    ];
                }
                $prices = $modelform->tariff_prices;
                $dates = $modelform->tariff_prices_dates;
                $full_prices = array_diff($prices, ['']);
                if (empty($full_prices)) {
                    // пользователь не заполнил ни одной цены
                    return [
                        'status' => 'fail',
                        'message' => 'Необходимо указать минимум одну цену для тарифа',
                    ];
                }
                // первый элемент массива заполняем датой начала публикации тарифа
                $dates[0] = $modelform->tariff_start_publish;
                foreach ($prices as $key => $price) {
                    if (empty($price) and empty($dates[$key])) {
                        unset($prices[$key]);
                        unset($dates[$key]);
                    } elseif (!isset($price)) {
                        return [
                            'status' => 'fail',
                            'message' => 'Для каждой даты начала публикации должна быть указана цена',
                        ];
                    } elseif (empty($dates[$key])) {
                        return [
                            'status' => 'fail',
                            'message' => 'Для каждой цены должна быть указана дата начала публикации',
                        ];
                    } else {
                        if (strtotime($modelform->tariff_start_publish) > strtotime($dates[$key])) {
                            return [
                                'status' => 'fail',
                                'message' => 'Дата начала публикации тарифа не может быть меньше даты начала публикации тарифа',
                            ];
                        }
                        if (strtotime($modelform->tariff_end_publish) < strtotime($dates[$key])) {
                            return [
                                'status' => 'fail',
                                'message' => 'Дата начала публикации тарифа не может быть больше даты окончания публикации тарифа',
                            ];
                        }
                        $dates[$key] = strtotime($dates[$key]);
                    }
                }

                $uniq_dates = array_unique($dates);
                if ($uniq_dates !== $dates) {
                    return [
                        'status' => 'fail',
                        'message' => 'На один день может быть задана только одна цена',
                    ];
                }
                $prices_dates = array_combine($dates, $prices);
                ksort($prices_dates);
                $dates = array_keys($prices_dates);
                $prices = array_values($prices_dates);
                $prices_dates = [];
                foreach ($dates as $key => $date) {
                    if (($key + 1) == count($dates)) {
                        $date_end = strtotime($modelform->tariff_end_publish);
                    } else {
                        // если дата не последняя, то заканчивается цена за сутки до начала следующей
                        $date_end = $dates[$key + 1] - 24 * 3600;
                    }
                    $prices_dates[] = ['date_start' => $date, 'price' => $prices[$key], 'date_end' => $date_end];
                }
                // проверить валидность указанных цен
                // наличие хотя бы 1 заполненной цены
                // отсутствие нескольких цен на один день
                // период публикации цены не должен выходить за пределы публикации тарифа
                // отсортировать цены по дате начала публикации (time() - как ключ массива)
                // заполнить поля тарифа
                $tariff->page_id = $modelform->id;
                $tariff->name = $modelform->tariff_name;
                // для существующих форм
                $tariff->event_form_id = $modelform->tariff_form_id;

                $tariff->description = $modelform->tariff_description;
                $tariff->start_publish = $modelform->tariff_start_publish;
                /*
                    Из формы получаем дату и дописываем время на конец дня для того чтобы можно было указать пириод публикации 1 день. В админке время задается пользователем, а из ЛК доступно только указать дату.
                */
                $tariff->end_publish = $modelform->tariff_end_publish . ' 23:59:59';
                $tariff->limit_tickets = !($modelform->tariff_unlimit_ticket);
                $tariff->tickets_count = $modelform->tariff_count_ticket;
                $tariff->visible = $modelform->tariff_visible;
                if ($tariff->save()) {
                    // перезаписать цены на тариф
                    if (is_array($modelform->tariff_prices)) {
                        if (!empty($tariff->prices)) {
                            foreach ($tariff->prices as $price) {
                                $price->delete();
                            }
                        }
                        foreach ($prices_dates as $price_item) {
                            $new_price = new TariffPrice();
                            $new_price->tariff_id = $tariff->id;
                            $new_price->events_id = $event->id;
                            $new_price->start_publish = date('d.m.Y', $price_item['date_start']);
                            $new_price->end_publish = date('d.m.Y', $price_item['date_end']);
                            $new_price->price = $price_item['price'];
                            $new_price->save();
                        }
                    }
                    $tariff->refresh();
                    return [
                        'status' => 'success',
                        'new_tariff_html' => $this->renderPartial('_tariff_line', ['tariff' => $tariff]),
                        'message' => 'Тариф добавлен.',
                        'action' => $action,
                        'tariff_id' => $tariff->id,
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => implode('<br>', $tariff->firstErrors),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно создать тариф для этого мероприятия.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionSwitchform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Тариф */
        $user = Yii::$app->user->identity->userAR;
        $post = Yii::$app->request->post();
        if (!empty($post['form_id'])) {
            $eventsform = Eventsform::findOne((int)$post['form_id']);
            if ($eventsform) {
                if ($eventsform->event->author_id == Yii::$app->user->id) {
                    $eventsform->visible = !((int)$eventsform->visible);
                    $eventsform->updateAttributes(['visible' => $eventsform->visible]);
                    return [
                        'status' => 'success',
                        'visible' => $eventsform->visible,
                        'message' => 'Активность формы изменена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Ошибка авторизации, форма не найдена.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Форма не найдена',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionRemovetariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Тариф */
        $user = Yii::$app->user->identity->userAR;
        $post = Yii::$app->request->post();
        if (!empty($post['tariff_id'])) {
            $tariff = Tariff::findOne((int)$post['tariff_id']);
            if ($tariff) {
                if ($tariff->event->author_id == Yii::$app->user->id) {
                    if ($tariff->delete()) {
                        return [
                            'status' => 'success',
                            'message' => 'Тариф удален.',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка удаления. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Ошибка авторизации, тариф не найден.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Тариф не найден',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    /* страница выгрузки данных из конструктора форм + для пользователей с правом выгрузки */
    public function actionEvents_export($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если get-запрос содержит id формы */
        $form_id = Yii::$app->request->get('form_id', false);
        $error_message = [];
        if ($form_id) {
            $form = Formslist::findOne((int)$form_id);
            if ($form) {
                if (in_array($user->id, ArrayHelper::map($form->user, 'id', 'id'))) {
                    $columns = [];
                    if ($form->register_user) {
                        $columns[] = 'surname:text:Фамилия';
                        $columns[] = 'name:text:Имя';
                        $columns[] = 'patronymic:text:Отчество';
                        $columns[] = 'email:text:E-mail';
                        $columns[] = 'phone:text:Телефон';
                    }
                    if (!empty($form->form_fields)) {
                        foreach ($form->form_fields as $key => $field) {
                            // если поле скрыто, то не выгружаем данные
                            // if (!$field['visible']) continue;
                            $columns[] = 'field_' . $key . ':text:' . $field['name'];
                        }
                    }
                    if (!empty($columns)) {
                        $models = \app\modules\formsresult\models\Formsresult::find()->where(['form_id' => $form_id])->orderBy(['created_at' => SORT_DESC])->all();
                        $models_data = [];
                        foreach ($models as $key_d => $model) {
                            $name = $model->name;
                            $surname = $model->surname;
                            $patronymic = $model->patronymic;
                            $models_data[$key_d] = [
                                'name' => $name ? $name : '',
                                'surname' => $surname ? $surname : '',
                                'patronymic' => $patronymic ? $patronymic : '',
                                'email' => $model->email,
                                'phone' => ' ' . $model->phone,
                            ];
                            if (!empty($form->form_fields)) {
                                foreach ($form->form_fields as $key => $field) {
                                    // если поле скрыто, то не выгружаем данные
                                    // if (!$field['visible']) continue;
                                    $models_data[$key_d]['field_' . $key] = (is_array($model->fields) && isset($model->fields[$field['name']])) ? (is_array($model->fields[$field['name']]) ? implode(', ', $model->fields[$field['name']]) : $model->fields[$field['name']]) : '';
                                }
                            }
                        }
                        ExcelExportHelper::excelDownload($columns, $models_data, 'Заявки ' . strip_tags($form->name));
                    }
                    $error_message[] = 'Отсутствуют поля для выгрузки данных';

                } else {
                    $error_message[] = 'У вас нет прав на получение данных по запрошенной форме';
                }
            } else {
                $error_message[] = 'Запрошенная форма не найдена';
            }
        }
        $error_message = implode('<br>', $error_message);

        /* находим формы с правами на выгрузку */
        $formslist = Formslist::find()->leftJoin('formslist_export_rules', 'formslist_export_rules.formslist_id = formslist.id')->where(['formslist_export_rules.user_id' => $user->id])->andWhere(['formslist.visible' => 1])->all();

        /* группируем формы по мероприятиям */
        /* считается, что конструктор форм может быть привязан только к Мероприятиям. Если это изменится, нужно добавить еще одну группу */
        $forms_group = ['events' => [], 'other' => []];
        foreach ($formslist as $form) {
            if ($form->ownermodel && (get_class($form->ownermodel) == Events::class)) {
                if (!isset($forms_group['events'][$form->ownermodel->id])) {
                    $forms_group['events'][$form->ownermodel->id] = ['event' => $form->ownermodel, 'forms' => []];
                }
                $forms_group['events'][$form->ownermodel->id]['forms'][] = $form;
            } else {
                // если форма не привязана к мероприятию
                $forms_group['other'][] = $form;
            }
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'forms_group' => $forms_group, 'error_message' => $error_message]);
    }

    /** ajax-выгрузка билетов на мероприятие со страницы просмотра мероприятия
     *
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionExporttickets()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        $event_id = Yii::$app->request->get('event_id');
        $form_id = Yii::$app->request->get('form_id');
        if ($event_id && $form_id) {
            $event = Events::findOne((int)$event_id);
            if (empty($event) or ($event->author_id != $user->id)) {
                return [
                    'status' => 'fail',
                    'message' => 'Мероприятие не найдено',
                ];
            }

            $orders = $event->getOrders((int)$form_id, false)->all();
            [$data, $columns] = ExcelExportHelper::dataEventTickets($orders);

            // формирование и экспорт файла
            ExcelExportHelper::excelDownload($columns, $data, 'Билеты на мероприятие ' . $event->name);
        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры.',
        ];

    }

    /** ajax-выгрузка данных из конструктора форм, привязанного к мероприятию со страницы просмотра мероприятия в ЛК
     *
     * @throws Exception
     */
    public function actionExportform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        $get = Yii::$app->request->get();

        if (isset($get['event_id']) && isset($get['form_id'])) {
            $event = Events::findOne((int)$get['event_id']);
            if (empty($event) or ($event->author_id != $user->id)) {
                return [
                    'status' => 'fail',
                    'message' => 'Мероприятие не найдено',
                ];
            }
            $form = Formslist::findOne((int)$get['form_id']);
            if (empty($form) or (get_class($form->ownermodel) != $event::class) or ($form->ownermodel->id != $event->id)) {
                return [
                    'status' => 'fail',
                    'message' => 'Форма не найдена',
                ];
            }

            $columns = [];
            if ($form->register_user) {
                $columns[] = 'surname:text:Фамилия';
                $columns[] = 'name:text:Имя';
                $columns[] = 'patronymic:text:Отчество';
                $columns[] = 'email:text:E-mail';
                $columns[] = 'phone:raw:Телефон';
            }
            if (!empty($form->form_fields)) {
                foreach ($form->form_fields as $key => $field) {
                    // if (!$field['visible']) continue;
                    $columns[] = 'field_' . $key . ':text:' . $field['name'];
                }
            }
            $models = $form->resultRecords;
            $models_data = [];
            foreach ($models as $key_d => $model) {
                $models_data[$key_d] = [
                    'name' => $model->name ? $model->name : '',
                    'surname' => $model->surname ? $model->surname : '',
                    'patronymic' => $model->patronymic ? $model->patronymic : '',
                    'email' => $model->email,
                    'phone' => ' ' . $model->phone,
                ];
                if (!empty($form->form_fields)) {
                    foreach ($form->form_fields as $key => $field) {
                        // if (!$field['visible']) continue;
                        $models_data[$key_d]['field_' . $key] = (is_array($model->fields) && isset($model->fields[$field['name']])) ? (is_array($model->fields[$field['name']]) ? implode(', ', $model->fields[$field['name']]) : $model->fields[$field['name']]) : '';
                    }
                }
            }

            // формирование и экспорт файла
            ExcelExportHelper::excelDownload($columns, $models_data, 'Заявки ' . $event->name);
        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры.',
        ];

    }

    public function actionSwitchfield()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        if (Yii::$app->request->isAjax) {
            $get = Yii::$app->request->get();
            if (isset($get['id']) && isset($get['attribute'])) {
                if (in_array($get['attribute'], ['visible'])) {
                    $event = Events::findOne((int)$get['id']);
                    if ($event) {
                        if ($event->author_id == $user->id) {
                            $attribute = $get['attribute'];
                            if (($attribute == 'visible') && empty($event->ordersAll)) {
                                $event->{$attribute} = ($event->{$attribute} == 0) ? 1 : 0;
                                if ($event->updateAttributes([$attribute => $event->{$attribute}])) {
                                    return [
                                        'status' => 'success',
                                        'message' => 'Данные обновлены',
                                    ];
                                }
                                return [
                                    'status' => 'fail',
                                    'message' => 'Ошибка при изменении статуса',
                                ];

                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Нельзя снять с публикации. Условия не соблюдены.',
                            ];

                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Ошибка авторства мероприятия',
                        ];

                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Мероприятие не найдено',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Деиствие не распознано.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Не верные параметры.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'В доступе отказано.',
        ];

    }

    /* АПИ */
    /* требования для создания заказа мероприятия:
     * запрос пришел аяксом (прямого создания заказа нет - только с кнопки на странице)
     * тариф активен
     * дата публикации тарифа актуальна
     * тариф имеет актуальную цену
     * автор мероприятия не является текущим пользователем (нельзя купить билет у самого себя)
     * автор мероприятия имеет статус Активен
     * автор мероприятия относится к Экспертам АСТ (expert, exporg) и имеет разрешение на создание услуг ($user->organization->can_service)
     * на автора мероприятия не создано голосование на исключение из Экспертов.
     */
    public function actionCreateorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $user = Yii::$app->user->identity->userAR;
        // находим тарифы
        $post = Yii::$app->request->post();
        if (!empty($post['order_data']) && is_array($post['order_data'])) {
            $tariffs = Tariff::find()->where(['IN', 'id', array_keys($post['order_data'])])->andWhere(['visible' => 1])->andWhere(['<=', 'start_publish', new \yii\db\Expression('NOW()')])->andWhere(['>=', 'end_publish', new \yii\db\Expression('NOW()')])->all();
            if (count($tariffs) != count($post['order_data'])) {
                return [
                    'status' => 'fail',
                    'message' => 'Один или несколько тарифов не могут быть приобретены.',
                ];
            }
            $event_author = $tariffs[0]->event->author;
            $event = $tariffs[0]->event;
            if ($event_author->id == $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'Нельзя купить билет у самого себя.',
                ];
            }
            if ($event_author->status != UserAR::STATUS_ACTIVE) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор мероприятия прекратил свою деятельность',
                ];
            }
            if (!in_array($event_author->role, ['expert', 'exporg'])) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор мероприятия более не сотрудничает с АСТ',
                ];
            }
            if (!$event_author->organization->can_service) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор мероприятия не может продавать билеты',
                ];
            }

            foreach ($tariffs as $tariff) {
                if ($tariff->currentPrice === false) {
                    return [
                        'status' => 'fail',
                        'message' => 'Цена на тариф неактуальна, невозможно приобрести билет',
                    ];
                }
                if ($event_author->id != $tariff->event->author->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Нельзя в одном заказе приобрести билеты у разных организаторов',
                    ];
                }
                if ($event->id != $tariff->page_id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Нельзя в одном заказе приобрести билеты на разные мероприятия',
                    ];
                }
            }
            // если все проверку пройдены - редирект на создание мероприятия
            $data = base64_encode(http_build_query($post['order_data']));
            $order_page = Ticketbuy::find()->where(['model' => Ticketbuy::class, 'visible' => 1])->one();
            if ($order_page) {
                $order_url = Url::toRoute([$order_page->getUrlPath(), 'data' => $data]);
                if (Yii::$app->user->isGuest) {
                    Yii::$app->session->remove('createorder_after_login');
                    Yii::$app->session->set('redirect_after_login', $order_url);
                    return [
                        'status' => 'need_register',
                    ];
                }
                // редирект на адрес
                return [
                    'status' => 'success',
                    'redirect_to' => $order_url,
                    'message' => 'Оформляем заказ билетов',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Продажа билетов временно приостановлена.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Параметры заказа заданы неверно',
        ];

    }

    public function actionTicketbuy($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $data = Yii::$app->request->get('data', false);
        $user = Yii::$app->user->identity->userAR;
        $event_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
        if (!empty($data)) {
            parse_str(base64_decode($data), $data);
            if (!empty($data) and is_array($data)) {
                /* находим тарифы из заказа - только актуальные и опубликованные */
                $tariffs = Tariff::find()->where(['IN', 'id', array_keys($data)])->andWhere(['visible' => 1])->andWhere(['<=', 'start_publish', new \yii\db\Expression('NOW()')])->andWhere(['>=', 'end_publish', new \yii\db\Expression('NOW()')])->all();
                if (!empty($tariffs) and (count($tariffs) == count($data))) {
                    /* принимаем что мероприятие - это мероприятие из первого попавшегося тарифа, т.к. нельзя приобрести билеты на два мероприятия в одном заказе */
                    $event = $tariffs[0]->event;
                    $event_author = $event->author;
                    if ($event_author->id != $user->id) {
                        /* проверяем возможность продажи билетов на мероприятие */
                        if ($event->canSale()) {
                            $current_summ = 0;
                            foreach ($tariffs as $tariff) {
                                /* проверяем возможность покупки билетов по тарифу */
                                if (!$event->canBuyTarif($tariff, $data[$tariff->id])) {
                                    $error_message = 'Невозможно приобрести билеты по тарифу "' . $tariff->name . '". Вернитесь к <a href="' . $tariff->event->getUrlPath() . '">мероприятияю</a> и повторите ваш заказ.';
                                }
                                /* проверяем наличие действующей цены */
                                if ($tariff->currentPrice === false) {
                                    $error_message = 'Цена на тариф неактуальна, невозможно приобрести билет. Вернитесь к <a href="' . $tariff->event->getUrlPath() . '">мероприятияю</a> и повторите ваш заказ.';
                                    break;
                                }
                                if ($event_author->id != $tariff->event->author->id) {
                                    $error_message = 'Нельзя в одном заказе приобрести билеты у разных организаторов';
                                    break;
                                }
                                if ($event->id != $tariff->page_id) {
                                    $error_message = 'Нельзя в одном заказе приобрести билеты на разные мероприятия';
                                    break;
                                }
                                $current_summ += $tariff->currentPrice * $data[$tariff->id];
                            }
                        } else {
                            $error_message = 'Продажи на данное мероприятие закрыты.';
                        }
                    } else {
                        $error_message = 'Нельзя купить билет у самого себя.';
                    }
                } else {
                    $error_message = 'Один или несколько тарифов не могут быть приобретены.';
                }
            } else {
                $error_message = 'Отсутствуют параметры заказа. Вернитесь к <a href="' . $event_catalog->getUrlPath() . '">мероприятияю</a> и повторите ваш заказ.';
            }
        } else {
            $error_message = 'Отсутствуют параметры заказа. Вернитесь к <a href="' . $event_catalog->getUrlPath() . '">мероприятияю</a> и повторите ваш заказ.';
        }

        $this->setMeta($model);
        if (!empty($error_message)) {
            // страница ошибки
            return $this->render('error_order', ['model' => $model, 'error_message' => $error_message]);
        }
        // страница формы
        $modelform = new EventOrderForm();
        $modelform->loadFromUserProfile($user->profile);
        $modelform->eventform_id = $tariffs[0]->eventform;
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();

        return $this->render($model->view, ['model' => $model, 'login_page' => $login_page, 'event' => $event, 'current_summ' => $current_summ, 'modelform' => $modelform, 'tariffs' => $tariffs, 'tickets' => $data]);


    }

    /* оформление заказа билетов */
    public function actionOrderevent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        // валидируем данные с формы
        $post = Yii::$app->request->post();
        $modelform = new EventOrderForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            /* находим первый попавшийся тариф */
            $first_tariff = Tariff::findOne((int)reset($modelform->ticket_info['tariff']));
            if (!$first_tariff) {
                return [
                    'status' => 'fail',
                    'message' => 'Заказ по одному из перечисленных тарифов невозможен',
                ];
            }
            /* если тариф существует - находим мероприятие */
            $event = $first_tariff->event;
            if (!$event) {
                return [
                    'status' => 'fail',
                    'message' => 'Мероприятие недоступно',
                ];
            }

            /* проверяем доступность продаж по мероприятию */
            if (!$event->canSale()) {
                return [
                    'status' => 'fail',
                    'message' => 'Продажи по выбранному мероприятию закрыты',
                ];
            }

            $total_summ = 0;
            /* создаем заказ */
            $new_order = new Eventsorder();
            $new_order->form_id = $first_tariff->event_form_id;
            $new_order->user_id = $user->id;
            $new_order->seller_id = $event->author->id;
            $new_order->events_id = $event->id;
            $new_order->name = 'Новый заказ';
            $new_order->price = $total_summ;
            $new_order->is_payed = 0;
            $new_order->visible = 1;
            if ($new_order->save()) {
                // заполнить номер
                $new_order->setOrderNumber();
                $new_order->name = 'Заказ №' . $new_order->orderNum;
                // записать соглашения
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $user->id;
                            $agree_sign->form_model = Formagree::TYPE_EVENTTICKET;
                            $agree_sign->form_id = $new_order->id;
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                $count_by_tariff = [];
                foreach ($modelform->ticket_info['tariff'] as $key => $tariff_id) {
                    /* считаем количество билетов по тарифу */
                    $count_by_tariff[$tariff_id] = ($count_by_tariff[$tariff_id] ? 1 : $count_by_tariff[$tariff_id] + 1);
                    /* для каждого билета смотрим тариф */
                    $tariff = Tariff::findOne((int)$tariff_id);
                    /* если тариф не существует по нему не может быть продажи для выбранного мероприятия */
                    if (empty($tariff) or (!$event->canBuyTarif($tariff, $count_by_tariff[$tariff_id]))) {
                        /* удалить заказ и вывести ошибку */
                        $new_order->delete();
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно приобрести билет по одному из тарифов.',
                        ];
                    }

                    $new_item = new EventsorderItem();
                    $new_item->eventorder_id = $new_order->id;
                    $new_item->tariff_id = $tariff->id;
                    $new_item->fio = $modelform->ticket_info['surname'][$key] . ' ' . $modelform->ticket_info['name'][$key];
                    $new_item->price = $tariff->currentPrice;

                    // сохранение доп.полей билета
                    $form_data = [];
                    $form_fields = $tariff->eventform->form_fields;
                    foreach ($tariff->eventform->form_fields as $field) {
                        $form_data[$field['name']] = $modelform->ticket_info[$field['sysname']][$key];
                    }
                    $new_item->form_data = $form_data;
                    $new_item->status = EventsorderItem::STATUS_NEW;


                    if (!$new_item->save()) {
                        // удалить заказ и вывести ошибку
                        $log_path = Yii::getAlias('@app/logs/');
                        file_put_contents($log_path . 'event_buyticket.txt', date('d.m.Y H:i:s') . ' - Ошибка создания билета ' . $new_item->id . "\r\n" . var_export($new_item->errors, true) . "\r\n", FILE_APPEND);
                        $new_order->delete();
                        return [
                            'status' => 'fail',
                            'message' => 'Добавление билета невозможно. Повторите заказ.',
                        ];
                    }
                }

                $new_order->refresh();
                /* если итоговая сумма заказа равна 0 - заказ считать оплаченным */
                if ($new_order->price == 0) {
                    $new_order->applyPayment();
                    // редирект на страницу Мои Билеты
                    $payment_page = LKEventsTickets::find()->where(['model' => LKEventsTickets::class, 'visible' => 1])->one();
                } else {
                    // релирект на страницу выбора оплаты
                    $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                }
                $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_EVENTS, 'id' => $new_order->id]) : false;
                $modelform->updateUserProfile($user->profile);
                // редирект на страницу оплаты
                return [
                    'status' => 'success',
                    'redirect_to' => $payment_url,
                    'message' => 'Заказ создан',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Создание заказа невозможно. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При оформлении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionTariffinfo()
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
        $post = Yii::$app->request->post();

        $eventsorder = Eventsorder::findOne((int)$post['ticket_id']);
        if (!$eventsorder or ($eventsorder->seller_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'Заказ не найден',
            ];
        }
        $tariff_list = [];
        $data = $eventsorder->items_group;

        foreach ($data as $name => $count) {
            $tariff_list[] = ['name' => $name, 'count' => $count];
        }
        $ret_data = [
            'status' => 'success',
            'date' => Yii::$app->formatter->asDatetime($eventsorder->created_at, 'php:d.m.Y'),
            'number' => $eventsorder->name,
            'fio' => $eventsorder->user->profile->halfname,
            'phone' => $eventsorder->user->profile->phone,
            'tariff_list' => $tariff_list,
        ];
        return $ret_data;
    }

    public function actionGettariff()
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
        $post = Yii::$app->request->post();

        $tariff = Tariff::findOne((int)$post['tariff_id']);
        if (!$tariff or ($tariff->event->author_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'Тариф не найден',
            ];
        }
        $prices = [];
        foreach ($tariff->prices as $price_item) {
            $prices[] = ['date' => Yii::$app->formatter->asDatetime($price_item->start_publish, 'php:d.m.Y'), 'price' => number_format($price_item->price, 0, '.', '')];
        }
        $tariff_data = [
            'name' => $tariff->name,
            'description' => $tariff->description,
            'start_publish' => Yii::$app->formatter->asDatetime(strtotime($tariff->start_publish), 'php:d.m.Y'),
            'end_publish' => Yii::$app->formatter->asDatetime(strtotime($tariff->end_publish), 'php:d.m.Y'),
            'limit_tickets' => $tariff->limit_tickets,
            'tickets_count' => $tariff->tickets_count,
            'visible' => $tariff->visible,
            'prices' => $prices,
        ];
        $ret_data = [
            'status' => 'success',
            'data' => $tariff_data,
        ];
        return $ret_data;
    }

    public function actionResultinfo()
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
        $post = Yii::$app->request->post();

        $result_record = Formsresult::findOne((int)$post['result_id']);
        if (!$result_record or ($result_record->form->ownermodel->author_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'Запись не найдена',
            ];
        }

        $data_list = [];
        foreach ($result_record->fields as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $data_list[] = ['name' => $name, 'text' => $value];
        }
        $ret_data = [
            'status' => 'success',
            'date' => Yii::$app->formatter->asDatetime($result_record->created_at, 'php:d.m.Y'),
            'fio' => $result_record->surname . ' ' . $result_record->name . ' ' . $result_record->patronymic,
            'email' => $result_record->email,
            'phone' => $result_record->phone,
            'data_list' => $data_list,
        ];
        return $ret_data;
    }
}
