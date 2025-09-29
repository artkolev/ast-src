<?php

namespace app\modules\pages\controllers\frontend;

use app\models\SertificateFos as SFform;
use app\modules\contractitem\models\Contractitem;
use app\modules\eventsorder\models\EventsorderItem;
use app\modules\formslist\models\Formslist;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\sertificatefos\models\Sertificatefos;
use app\modules\users\models\UserAR;
use Yii;
use yii\web\Response;

class DocumentsController extends LKController
{
    /* Договоры */
    public function actionDocuments($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если не зарегистрирован для оказания услуг */
        if (!$user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $contracts = Contractitem::find()->where(['user_id' => $user->id, 'visible' => 1])->orderBy(['date' => SORT_DESC, 'id' => SORT_DESC])->all();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'contracts' => $contracts, 'type' => $user->organization->type_mp, 'user' => $user]);

    }

    /* проверка наличия секретного слова у сертификата */
    public function actionChecksecretword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $modelform = new SFform();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if (!empty($modelform->sert_type)) {
                $formslist = Formslist::find()->leftJoin(
                    'formsresult',
                    'formsresult.form_id = formslist.id'
                )->where([
                    'formslist.id' => $modelform->sert_type,
                    'formslist.can_sertificate' => true,
                ])->andWhere(
                    'formslist.sert_start <= NOW()'
                // убрали поле окончания выдачи сертификатов
                // )->andWhere(
                //    'formslist.sert_end >= NOW()'
                )->andWhere([
                    'formsresult.user_id' => $user->id
                ])->one();
                if ($formslist && !empty($formslist->secret_word)) {
                    return [
                        'status' => 'success',
                        'result' => 'true',
                    ];
                }
            }
        }
        return [
            'status' => 'success',
            'result' => 'false',
        ];
    }

    /* сертификаты + сертификаты с фос */
    public function actionSertificatefos()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $modelform = new SFform();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if (!empty($modelform->sert_type)) {
                $formslist = Formslist::find()->leftJoin(
                    'formsresult',
                    'formsresult.form_id = formslist.id'
                )->where([
                    'formslist.id' => $modelform->sert_type,
                    'formslist.can_sertificate' => true,
                ])->andWhere(
                    'formslist.sert_start <= NOW()'
                // убрали поле окончания выдачи сертификатов
                // )->andWhere(
                //    'formslist.sert_end >= NOW()'
                )->andWhere([
                    'formsresult.user_id' => $user->id
                ])->one();
                // получение сертификата возможно и кодовое слово совпадает
                if ($formslist) {
                    if ($formslist->secret_word == $modelform->secret_word || empty($formslist->secret_word)) {
                        $exist_sert = Sertificatefos::find()->where(['user_id' => $user->id, 'form_id' => $modelform->sert_type])->one();
                        // если пользователь еще не получил сертификат
                        if (!$exist_sert) {
                            $new_num = $formslist->getSertNum();
                            $connection = Yii::$app->db;
                            $transaction = $connection->beginTransaction();
                            try {
                                $sql = "UPDATE `formslist` SET `sert_num` = `sert_num` + 1 WHERE `id` = " . $formslist->id;
                                $connection->createCommand($sql)->execute();

                                // добавление сертификата
                                $new_sert = new Sertificatefos([
                                    'name' => $formslist->getPublicName(),
                                    'user_id' => $user->id,
                                    'form_id' => $formslist->id,
                                    'sert_num' => $new_num,
                                    'sert_date' => date('Y-m-d H:i:s'),
                                    'order' => 1,
                                    'visible' => true,
                                ]);
                                $new_sert->save();

                                $transaction->commit();
                                return [
                                    'status' => 'success',
                                    'message' => 'Успешно',
                                ];
                            } catch (yii\base\Exception $e) {
                                $transaction->rollBack();
                            }
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Сертификат уже получен',
                        ];
                    }

                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка в кодовом слове',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Не найдена запись на мероприятие',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Выберите тип сертификата и введите секретное слово',
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'Произошла ошибка',
        ];
    }

    /* документы для незареганых на МП */
    public function actionDocumentssert($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* редирект на профиль, если зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страница с сертификатами по каждому пользователю и сертификатами по билетам */
    public function actionSertificate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        $servicefosform = new SFform();

        $tickets_with_sert = EventsorderItem::find()
            ->leftJoin('eventsorder', 'eventsorder.id = eventsorder_item.eventorder_id')
            ->where(['eventsorder.is_payed' => 1, 'eventsorder.user_id' => $user->id])
            ->andWhere(['IS NOT', 'eventsorder_item.sertificate_num', new \yii\db\Expression('null')])
            ->all();

        return $this->render($model->view, ['model' => $model, 'user' => $user, 'servicefosform' => $servicefosform, 'tickets_with_sert' => $tickets_with_sert]);
    }

    /* страница с сертификатами */
    public function actionDocumentscaf($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        /** @var UserAR $user */
        $user = Yii::$app->user->identity->userAR;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам, ЭО */
        $cafedra = $user->directionM;
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);

        return $this->render($model->view, ['model' => $model, 'cafedra' => $cafedra]);
    }

    /* страница с документами */
    public function actionProgram($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если не зарегистрирован на программы */
        if (!$user->profile->in_prog) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);

    }
}
