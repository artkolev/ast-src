<?php

namespace app\modules\pages\controllers\frontend;

use app\models\ServiceForm;
use app\modules\admin\components\FilestoreModel;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\ServiceAdd;
use app\modules\pages\models\ServiceAddIndi;
use app\modules\pages\models\ServiceEdit;
use app\modules\pages\models\ServiceList;
use app\modules\reference\models\Competence;
use app\modules\reference\models\Solvtask;
use app\modules\service\models\Service;
use app\modules\servicemoder\models\Servicemoder;
use app\modules\users\models\UserAR;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

class ServicerunController extends LKController
{
    /* страница создания типовой услуги */
    public function actionCreate($model)
    {
        $hasErrors = $this->servicePolicy();
        if ($hasErrors !== false) {
            return $hasErrors;
        }

        // defaults
        $currentStep = Yii::$app->request->get('step', '1');
        $serviceId = Yii::$app->request->get('id');
        $serviceType = $model instanceof ServiceAdd ? Service::TYPE_TYPICAL : Service::TYPE_CUSTOM;
        $originalService = false;

        $service_list_page = ServiceList::find()->where(['model' => ServiceList::class, 'visible' => 1])->one();

        if ($serviceId) {
            $originalService = Service::findOne((int)$serviceId);

            $hasErrors = $this->serviceEditPolicy($originalService, $service_list_page);
            if ($hasErrors !== false) {
                return $hasErrors;
            }

            $serviceType = $originalService->type;
        } elseif ($currentStep != 1 || $model instanceof ServiceEdit) {
            return $this->redirect($service_list_page->getUrlPath());
        }

        $modelForm = new ServiceForm();
        $modelForm->service_type = $serviceType;
        $modelForm->kind = $originalService ? $originalService->kind : Service::KIND_HYBRID;

        if ($originalService) {
            $modelForm->keywords = ArrayHelper::map($originalService->keywords, 'id', 'id');
            $modelForm->loadFromService($originalService->currentModeration ?? $originalService);

            if ($currentStep == 6 && $originalService->currentModeration) {
                // проверяем есть ли изменения в услуге
                $modelForm->hasDiff = $this->diffModerate($originalService->currentModeration, $originalService);
            }
        }

        if (!in_array((int)$currentStep, [1, 2, 3, 4, 5, 6])) {
            $currentStep = '1';
        }

        $modelForm->step = 'step' . $currentStep;
        $modelForm->scenario = 'step' . $currentStep;

        $this->setMeta($model);
        return $this->render($model->view, [
            'model' => $model,
            'modelform' => $modelForm,
            'currentStep' => $currentStep,
            'original' => $originalService,
        ]);
    }

    private function servicePolicy()
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

        return false;
    }

    /* страница списка услуг */

    /**
     * @return Response|array|bool;
     */
    private function serviceEditPolicy(Service $service = null, $service_list_page = false)
    {
        $hasErrors = false;
        $hasApiErrors = false;

        $user = Yii::$app->user->identity->userAR;
        if (!$service || $service->user_id != $user->id) {
            $hasErrors = true;
            $hasApiErrors = [
                'status' => 'fail',
                'message' => 'Ошибка доступа к услуге',
            ];
        }

        if (in_array($service->status, Service::NOT_EDIT_STATUS)) {
            $hasErrors = true;
            $hasApiErrors = [
                'status' => 'fail',
                'message' => 'Услуга в настоящем статусе не может быть отредактирована.',
            ];
        }

        if ($hasErrors) {
            return $service_list_page ? $this->redirect($service_list_page->getUrlPath()) : $hasApiErrors;
        }

        return false;
    }

    private function diffModerate(Servicemoder $moderation, Service $new_service)
    {
        $moderationFields = $moderation->getAttributes();

        // убираем лишнее
        unset(
            $moderationFields['id'],
            $moderationFields['type'],
            $moderationFields['created_at'],
            $moderationFields['updated_at'],
            $moderationFields['reason'],
            $moderationFields['status'],
            $moderationFields['order'],
            $moderationFields['service_id'],
            $moderationFields['visible'],
            $moderationFields['price'],
            $moderationFields['old_price'],
        );

        // добавляем нужное
        $moderationFields['target_audience'] = ArrayHelper::map($moderation->target_audience, 'id', 'id');
        $moderationFields['competence'] = ArrayHelper::map($moderation->competence, 'id', 'id');
        $moderationFields['solvtask'] = ArrayHelper::map($moderation->solvtask, 'id', 'id');
        $moderationFields['image'] = ArrayHelper::map($moderation->image, 'id', 'id');

        $diffCount = 0;
        foreach ($moderationFields as $field => $value) {
            if ((isset($new_service->{$field}) && $value != $new_service->{$field})) {
                if (is_array($moderation->{$field})) {
                    $new_service_relation = ArrayHelper::map($new_service->{$field}, 'id', 'id');
                    if (!empty(array_diff($value, $new_service_relation))) {
                        $diffCount++;
                    }
                } else {
                    $diffCount++;
                }
            }
        }

        return $diffCount;
    }

    public function actionSavePartService()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $hasErrors = $this->servicePolicy();
        if ($hasErrors !== false) {
            return $hasErrors;
        }

        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;

        $currentStep = Yii::$app->request->get('step');
        $action2Moderate = (bool)(Yii::$app->request->get('action2Moderate', 0));

        /* проверяем указанный шаг и загружаем соответствующий сценарий для модели LKEvent */
        if (in_array($currentStep, ['1', '2', '3', '4', '5', '6'])) {
            $modelform = new ServiceForm();
            $modelform->scenario = 'step' . $currentStep;
        } else {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры',
            ];
        }

        // загружаем изображения для валидации на бэке (все изображения у нас на 5-м шаге)
        if ($currentStep == 5) {
            $modelform->image = UploadedFile::getInstances($modelform, 'image');
            $modelform->videoimage = UploadedFile::getInstance($modelform, 'videoimage');
        }

        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {

            $logfile = $modelform->service_type == Service::TYPE_CUSTOM ? 'service_indi.txt' : 'service_typical.txt';

            if (!empty($modelform->id)) {
                // редактирование услуги
                $new_service = Service::findOne($modelform->id);
                if (!$new_service) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Услуга не найдена',
                    ];
                }

                $hasErrors = $this->serviceEditPolicy($new_service, false);
                if ($hasErrors !== false) {
                    return $hasErrors;
                }
            } else {
                if (is_null($user->directionM)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка. Главная кафедра не задана',
                    ];
                }

                // создание услуги
                $new_service = new Service();
                $new_service->user_id = $user->id;
                $new_service->direction_id = $user->directionM->id;
                $new_service->visible = 1;
                $new_service->order = 1;
            }

            /* если сущность новая или у него нет текущей записи о модерации, то создаем новую модерацию */
            if ($new_service->isNewRecord or empty($new_service->currentModeration)) {
                $moderation = $new_service->addNewModeration();
            } else {
                /* достаем текущую заявку на модерацию */
                $moderation = $new_service->currentModeration;
            }

            switch ($currentStep) {
                case '1':
                    // на первом шаге заполняем необходимые данные для создания услуги - далее только модерацию
                    $new_service->name = $moderation->name = $modelform->name;
                    $new_service->type_id = $moderation->type_id = (int)$modelform->type_id;
                    $new_service->kind = $moderation->kind = (int)$modelform->kind;
                    $new_service->type = (int)$modelform->service_type;

                    $moderation->short_description = $modelform->short_description;
                    break;
                case '2':
                    $moderation->city_id = $modelform->city_id;
                    $moderation->place = $modelform->place;
                    $moderation->platform = $modelform->platform;
                    $moderation->target_audience = $modelform->target_audience;

                    // Изменение полей Цена и Старая цена не должны модерироваться. Они должны сохраняться сразу при изменении.
                    if ($new_service->price != $modelform->price || $new_service->old_price != $modelform->old_price) {
                        $thisService = Service::findOne($new_service->id);
                        $thisService->price = $modelform->price;
                        $thisService->old_price = $modelform->old_price;
                        $thisService->save();

                        if ($new_service->status == Service::STATUS_DRAFT) {
                            $moderation->price = $modelform->price;
                            $moderation->old_price = $modelform->old_price;
                        }
                    }

                    break;
                case '3':
                    $moderation->description = $modelform->description;
                    $moderation->special_descr = $modelform->special_descr;
                    $moderation->price_descr = $modelform->price_descr;
                    break;
                case '4':
                    $moderation->competence = Competence::checkOrCreate($modelform->competence);
                    $moderation->solvtask = Solvtask::checkOrCreate($modelform->solvtask);
                    if ($new_service->keywords != $modelform->keywords) {
                        $thisService = Service::findOne($new_service->id);
                        $thisService->keywords = $modelform->keywords;
                        $thisService->save();
                    }
                    break;
                case '5':
                    if (!empty($modelform->image)) {
                        $moderation->image_loader = $modelform->image;
                    }

                    $moderation->video = $modelform->video;
                    $moderation->videoimage_loader = $modelform->videoimage;

                    break;
                case '6':
                    // сразу сохраняем тк на этом шаге нет данных форм
                    $new_service->setVisibility($modelform->vis_for);
                    $new_service->save();
            }

            /* если были отмечены флаги "Удалить изображение" */
            /* удаляем то изображение, которое пользователь видел, когда нажимал "Удалить" */
            [$new_service, $moderation] = $this->deleteUnusedImages($modelform, $new_service, $moderation);
            /**
             * @var Service $new_service
             * @var Servicemoder $moderation
             */

            $saveService = false;

            /**
             * блок сценариев
             * теперь модерация сохраняется всегда
             */
            switch (true) {
                case $new_service->isNewRecord || $new_service->status == Service::STATUS_DRAFT:
                    /** сохранение черновика */
                    $new_service->status = Service::STATUS_DRAFT;
                    // если нажата кнопка на модерацию
                    if ($action2Moderate) {
                        /** черновик => первичная модерация */
                        $new_service->status = Service::STATUS_FIRST_MODERATE;
                        $moderation->status = Servicemoder::STATUS_MODERATE;
                    }
                    $saveService = true;

                    break;
                case in_array($new_service->status, [Service::STATUS_PUBLIC, Service::STATUS_WAIT_EDIT_MODERATE, Service::STATUS_WAIT_EDIT_FIRST_MODERATE]):
                    /** public => moderation && wait_edit_moderate => moderation && wait_edit_first_moderate => first_moderate*/
                    $moderation->status = Servicemoder::STATUS_NEW;
                    // если нажата кнопка на модерацию
                    if ($action2Moderate) {
                        $moderation->status = Servicemoder::STATUS_MODERATE;
                        $moderation->visible = 0;

                        // такой вызов для того чтобы не сохранять текущую услугу
                        $moderation->service->status = $new_service->status == Service::STATUS_WAIT_EDIT_FIRST_MODERATE ? Service::STATUS_FIRST_MODERATE : Service::STATUS_WAIT_MODERATE;
                        $moderation->service->save();
                    }

                    break;
            }

            /** блок сохранения */
            if ($saveService) {
                if (!$new_service->save()) {
                    /* если не сохранилось - пишем лог и выводим ошибку пользователю */
                    return $this->serviceErrorLog($logfile, $new_service);
                }
                $new_service->refresh();

                if ($new_service->status == Service::STATUS_DRAFT) {
                    $moderation->service_id = $new_service->id;
                }
            }

            /* сохраняем заявку на модерацию */
            if (!$moderation->save()) {
                /* если не сохранилось - пишем лог и выводим ошибку пользователю */
                return $this->serviceErrorLog($logfile, $moderation);
            }

            /** блок редиректов */
            /* следующий шаг */
            $next_step = ++$currentStep;
            /* если шаги закончились */
            if ($next_step > 6) {
                // готово, возвращаемся к списку
                $service_list_page = ServiceList::find()->where(['model' => ServiceList::class, 'visible' => 1])->one();
                $redirect_to = $service_list_page->getUrlPath();
                $message = $new_service->status == Service::STATUS_DRAFT ? 'Черновик сохранен' : 'Услуга отправлена на модерацию';
            } else {
                // следующий шаг
                if ($new_service->status == Service::STATUS_DRAFT) {
                    if ($new_service->type == Service::TYPE_TYPICAL) {
                        $next_page = ServiceAdd::find()->where(['model' => ServiceAdd::class, 'visible' => 1])->one();
                    } else {
                        $next_page = ServiceAddIndi::find()->where(['model' => ServiceAddIndi::class, 'visible' => 1])->one();
                    }
                } else {
                    $next_page = ServiceEdit::find()->where(['model' => ServiceEdit::class, 'visible' => 1])->one();
                }

                $redirect_to = Url::toRoute([$next_page->getUrlPath(), 'step' => $next_step, 'id' => $new_service->id]);
                $message = 'Переход на следующий шаг.';
            }

            return [
                'status' => 'success',
                'redirect_to' => $redirect_to,
                'message' => $message,
                /* если на модерации - то показать сообщение о модерации */
                'show_message' => ($moderation->status == Servicemoder::STATUS_MODERATE ? 'show' : 'hide'),
            ];

        }
        if (!empty($modelform->getErrors())) {
            return [
                'status' => 'fail',
                'message' => $modelform->getErrors()[array_key_first($modelform->getErrors())][0],
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    private function deleteUnusedImages(ServiceForm $modelform, Service $new_service, Servicemoder $moderation): array
    {
        $image_attribute = 'videoimage';
        if ($modelform->{$image_attribute . '_delete'}) {
            /* если есть текущая модерация и у модели модерации есть загруженное изображение в этом поле - удаляем */
            if (!empty($moderation) && !empty($moderation->{$image_attribute})) {
                $moderation->{$image_attribute}->delete();
            } elseif (!empty($new_service->{$image_attribute})) {
                /* иначе - если в модели услуги есть загруженное изображение - удаляем */
                $new_service->{$image_attribute}->delete();
            }
        }
        $preloaded_image = $new_service->image;
        if ($moderation) {
            $preloaded_image = array_merge($preloaded_image, $moderation->image);
            if (!empty($moderation->remove_image)) {
                foreach ($preloaded_image as $key => $image) {
                    if (in_array($image->id, $moderation->remove_image)) {
                        unset($preloaded_image[$key]);
                    }
                }
            }
        }

        $preloaded_ids = ArrayHelper::map($preloaded_image, 'id', 'id');
        /* если предзагруженные изображения отличаются от предзагруженных в форме - значит что-то удалили - отправляем в модерацию */
        $model_preloaded = $modelform->image_preload ?? null;
        if ($model_preloaded) {
            $image_to_delete = array_diff($preloaded_ids, $model_preloaded);

            if (!empty($image_to_delete)) {
                foreach ($image_to_delete as $id_to_del) {
                    // если нужно удалить изображение, привязанное к заявке на модерацию - удаляем.
                    // если нужно удалить изображение, привязанное к модели услуги - добавляем в список к удалению.
                    $image = FilestoreModel::findOne($id_to_del);
                    if (($image->keeper_class == Service::class) && ($image->keeper_id == $new_service->id)) {
                        $moderation->remove_image = array_merge($moderation->remove_image ?? [], [$id_to_del]);
                    } elseif (($image->keeper_class == Servicemoder::class) && ($image->keeper_id == $moderation->id)) {
                        $image->delete();
                    }
                }
            }
        }

        return [$new_service, $moderation];
    }

    private function serviceErrorLog(string $logfile, ActiveRecord $model): array
    {
        $log_path = Yii::getAlias('@app/logs/');
        file_put_contents($log_path . $logfile, date('d.m.Y H:i:s') . " - Ошибка сохранения заявки на модерацию к услуге \r\n" . var_export($model->errors, true) . "\r\n", FILE_APPEND);
        return [
            'status' => 'fail',
            'message' => 'При сохранении услуги возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionList($model)
    {
        $hasErrors = $this->servicePolicy();
        if ($hasErrors !== false) {
            return $hasErrors;
        }
        $user = Yii::$app->user->identity->userAR;
        // получить список услуг пользователя
        $curr_status = Yii::$app->request->get('status', '');
        // не показываем никогда услуги удаленные пользователем и отклоненные модератором
        $my_services_query = Service::find()->where(['user_id' => $user->id])->andWhere(['IN', 'service.status', [
            Service::STATUS_DRAFT,
            Service::STATUS_FIRST_MODERATE,
            Service::STATUS_WAIT_EDIT_FIRST_MODERATE,
            Service::STATUS_WAIT_MODERATE,
            Service::STATUS_WAIT_EDIT_MODERATE,
            Service::STATUS_PUBLIC,
        ]]);
        switch ($curr_status) {
            // опубликована
            case "active":
                // все активные, промодерированные
                $my_services_query->andWhere(['visible' => 1, 'status' => Service::STATUS_PUBLIC]);
                break;
            // на модерации
            case "moderation":
                // все не прошедшие модерацию, но имеющие заявку в статусе "На рассмотрении", не важно активные или нет
                $my_services_query->andWhere(['IN', 'service.status', [Service::STATUS_FIRST_MODERATE, Service::STATUS_WAIT_MODERATE]]);
                break;
            // Черновики
            case "draft":
                // все черновики
                $my_services_query
                    ->andWhere(['service.status' => Service::STATUS_DRAFT]);
                break;
            // требует правок
            case "need_edit":
                // все не прошедшие модерацию, не имеющие заявку в статусе "На рассмотрении", не важно активные или нет
                // найти все услуги с заявками на модерацию в в статусе "На рассмотрении", исключить их из списка услуг
                $my_services_query->andWhere(['IN', 'service.status', [Service::STATUS_WAIT_EDIT_FIRST_MODERATE, Service::STATUS_WAIT_EDIT_MODERATE]]);
                break;
            // не опубликована
            case "not_active":
                // все не активные, но прошедшие модерацию
                $my_services_query->andWhere(['visible' => 0, 'status' => Service::STATUS_PUBLIC]);
                break;
        }
        $my_services = $my_services_query->all();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $my_services, 'curr_status' => $curr_status]);

    }

    // функция определяет разницу между текущей модерацмей и состоянием услуги (для определенич отправить на модерацию или вернуть к списку услуг)

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
        if ($user->organization->can_service) {
            if (Yii::$app->request->isAjax) {
                $get = Yii::$app->request->get();
                if (isset($get['id']) && isset($get['attribute'])) {
                    if (in_array($get['attribute'], ['archive', 'visible'])) {
                        $service = Service::findOne($get['id']);
                        if ($service) {
                            if ($service->user_id == $user->id) {
                                $attribute = $get['attribute'];
                                if ($attribute == 'visible') {
                                    $service->{$attribute} = ($service->{$attribute} == 0) ? 1 : 0;
                                } else {
                                    if ($service->status == Service::STATUS_DRAFT) {
                                        $service->delete();
                                        return [
                                            'status' => 'success',
                                            'message' => 'Черновик удален',
                                        ];
                                    }

                                    $service->status = Service::STATUS_ARCHIVE;
                                }
                                if ($service->save()) {
                                    return [
                                        $attribute => $attribute == 'visible' ? $service->{$attribute} : $service->status,
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
                                'message' => 'Ошибка авторства услуги',
                            ];

                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Услуга не найдена',
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
        return [
            'status' => 'fail',
            'message' => 'Вы не можете редактировать услуги.',
        ];

    }
}
