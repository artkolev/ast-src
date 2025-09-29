<?php

namespace app\modules\pages\controllers\frontend;

use app\helpers\MainHelper;
use app\models\Avatar;
use app\models\ChangepassForm;
use app\models\ProfileAnketa;
use app\models\ProfileCafedra;
use app\models\ProfileCareer;
use app\models\ProfileContactexpert;
use app\models\ProfileContactexporg;
use app\models\ProfileEducation;
use app\models\Profileexpert;
use app\models\Profileexporg;
use app\models\Profilefizusr;
use app\models\ProfileHistory;
use app\models\ProfileHistoryExporg;
use app\models\ProfileMember;
use app\models\ProfileProfareaexpert;
use app\models\ProfileSettings;
use app\models\Profileurusr;
use app\modules\admin\components\FilestoreModel;
use app\modules\anketaquery\models\Anketaquery;
use app\modules\anocafedra\models\Anocafedra;
use app\modules\career\models\Career;
use app\modules\direction\models\Direction;
use app\modules\education\models\Education;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileCafedra as ProfileCafedraPage;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\ProfileNoCafedra;
use app\modules\pages\models\ProfilePretendent as ProfilePretendentPage;
use app\modules\profmoder\models\Avatarmoder;
use app\modules\profmoder\models\Profmoder;
use app\modules\reference\models\Competence;
use app\modules\reference\models\Solvtask;
use app\modules\settings\models\Settings;
use app\modules\users\models\Profile;
use app\modules\users\models\UserAR;
use app\modules\users\models\UserDirection;
use app\modules\users\models\UserExpert;
use app\modules\users\models\UserExporg;
use app\modules\users\models\UserFiz;
use app\modules\users\models\UserUr;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\UploadedFile;

class ProfileController extends LKController
{
    public function actionIndex($model)
    {
        /* страница доступна абсолютно всем авторизованным пользователям */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);

        $view = $model->view;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $avatarform = new Avatar();
        switch ($role) {
            case 'expert':
                $user = UserExpert::findOne(Yii::$app->user->id);
                $view = $model->view . '_expert';
                $modelform = new Profileexpert();
                break;
            case 'exporg':
                $user = UserExporg::findOne(Yii::$app->user->id);
                $view = $model->view . '_exporg';
                $modelform = new Profileexporg();
                break;
            case 'urusr':
                $user = UserUr::findOne(Yii::$app->user->id);
                $view = $model->view . '_urusr';
                $modelform = new Profileurusr();
                break;
            case 'mks':
                // у МКС пользователь как физлицо, но вью своя
                $user = UserFiz::findOne(Yii::$app->user->id);
                $view = $model->view . '_mks';
                $modelform = new Profilefizusr();
                break;
            case 'finman':
                // у ФМ пользователь как физлицо, но вью своя
                $user = UserFiz::findOne(Yii::$app->user->id);
                $view = $model->view . '_finman';
                $modelform = new Profilefizusr();
                break;
            case 'fizusr':
            default:
                $user = UserFiz::findOne(Yii::$app->user->id);
                $view = $model->view . '_fizusr';
                $modelform = new Profilefizusr();
                break;
        }
        $modelform->loadFromProfile($user);
        $is_moderated = Avatarmoder::find()->where(['user_id' => Yii::$app->user->id])->andWhere(['IN', 'status', [Avatarmoder::STATUS_NEW, Avatarmoder::STATUS_MODERATE]])->one();
        return $this->render($view, ['model' => $model, 'modelform' => $modelform, 'avatarform' => $avatarform, 'is_moderated' => $is_moderated]);
    }

    /* TODO: рефакторинг экшена */
    public function actionContact($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = UserExpert::findOne(Yii::$app->user->id);
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и ЭО */
        switch ($role) {
            case 'expert':
                $user = UserExpert::findOne(Yii::$app->user->id);
                $view = $model->view . '_expert';
                $modelform = new ProfileContactexpert();
                $modelform->loadFromProfile($user);
                break;
            case 'exporg':
                $user = UserExporg::findOne(Yii::$app->user->id);
                $view = $model->view . '_exporg';
                $modelform = new ProfileContactexporg();
                $modelform->loadFromProfile($user);
                break;
            default:
                // если у роли нет допуска - редирект на профиль
                $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                return $this->redirect($profile_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        return $this->render($view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionPretendent($model)
    {
        /* страница доступна только физлицам-претендентам */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        /* модель в любом случае грузим Эксперта, т.к. только у неё есть нужные поля */
        $user = UserExpert::findOne(Yii::$app->user->id);
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if (!in_array($role, ['fizusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если кафедра не соответствует - редирект на выбор кафедры */
        $norm_cafedra = false;
        if ($user->directionM) {
            if (!$user->directionM->coming_soon) {
                $norm_cafedra = true;
            }
        }
        if (!$norm_cafedra) {
            $cafedra_page = ProfileCafedraPage::find()->where(['model' => ProfileCafedraPage::class, 'visible' => 1])->one();
            return $this->redirect($cafedra_page->getUrlPath());
        }
        $modelform = new ProfileAnketa();
        $modelform->loadFromProfile($user);

        $education_form = new ProfileEducation();
        $career_form = new ProfileCareer();

        $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
        $can_edit = ($anketa == false);
        return $this->render($model->view, [
            'model' => $model,
            'user' => $user,
            'modelform' => $modelform,
            'education_form' => $education_form,
            'career_form' => $career_form,
            'can_edit' => $can_edit,
        ]);
    }

    public function actionCafedra($model)
    {
        /* страница доступна только физлицам-претендентам */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* пользователя в любом случае подгружаем через модель Эксперта, т.к. только у этой модели есть нужные поля */
        $user = UserExpert::findOne(Yii::$app->user->id);
        if (!in_array($role, ['fizusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
        $can_edit = ($anketa == false);

        $modelform = new ProfileCafedra();
        $modelform->loadFromProfile($user);

        return $this->render($model->view, [
            'model' => $model,
            'user' => $user,
            'modelform' => $modelform,
            'can_edit' => $can_edit
        ]);
    }

    public function actionNocafedra($model)
    {
        /* страница доступна только физлицам-претендентам у которых кафедра не выбрана или выбрана из списка формирующихся */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = UserExpert::findOne(Yii::$app->user->id);
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];

        if (!in_array($role, ['fizusr'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $direction = $user->directionM;
        $message = '';
        if ($direction) {
            if ($direction->coming_soon) {
                $message = $model->content;
            } else {
                // кафедра выбрана корректно
                $anketa = ProfilePretendentPage::find()->where(['model' => ProfilePretendentPage::class, 'visible' => 1])->one();
                return $this->redirect($anketa->getUrlPath());
            }
        } else {
            $message = $model->nocafedra;
        }

        return $this->render($model->view, [
            'model' => $model,
            'message' => $message,
        ]);
    }

    public function actionProfarea($model)
    {
        /* страница доступна только Экспертам, Академикам и ЭО */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = Yii::$app->user->identity->userAR;
        switch ($user->role) {
            case 'expert':
                $user = UserExpert::findOne($user->id);
                $view = $model->view . '_expert';
                break;
            case 'exporg':
                $user = UserExporg::findOne($user->id);
                $view = $model->view . '_exporg';
                break;
            default:
                // если у роли нет допуска - редирект на профиль
                $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                return $this->redirect($profile_page->getUrlPath());
        }
        $modelform = new ProfileProfareaexpert();
        $modelform->loadFromProfile($user);
        return $this->render($view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionEducation($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только Экспертам */
        if (!in_array($user->role, ['expert'])) {
            // если у роли нет допуска - редирект на профиль
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $view = $model->view . '_' . $user->role;
        $modelform = new ProfileEducation();
        return $this->render($view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    public function actionCareer($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только Экспертам, Академикам */
        if (!in_array($user->role, ['expert'])) {
            // если у роли нет допуска - редирект на профиль
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $view = $model->view . '_' . $user->role;
        $modelform = new ProfileCareer();
        return $this->render($view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    public function actionHistory($model)
    {
        /* страница доступна только Экспертам, Академикам и ЭО */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = Yii::$app->user->identity->userAR;

        if (!in_array($user->role, ['expert', 'exporg'])) {
            // если у роли нет допуска - редирект на профиль
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        switch ($user->role) {
            case 'expert':
                $user = UserExpert::findOne(Yii::$app->user->id);
                $view = $model->view . '_expert';
                $modelform = new ProfileHistory();
                $modelform->loadFromProfile($user);
                break;
            case 'exporg':
                $user = UserExporg::findOne(Yii::$app->user->id);
                $view = $model->view . '_exporg';
                $modelform = new ProfileHistoryExporg();
                $modelform->loadFromProfile($user);
                break;
        }
        return $this->render($view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionMembers($model)
    {
        /* страница доступна только Экспертным организациям */
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        switch ($role) {
            case 'exporg':
                $view = $model->view . '_exporg';
                $user = UserExporg::findOne(Yii::$app->user->id);
                $modelform = new ProfileMember();
                break;
            default:
                // если у роли нет допуска - редирект на профиль
                $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                return $this->redirect($profile_page->getUrlPath());
        }
        return $this->render($view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    public function actionSettings($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $this->setMeta($model);
        $user = UserAR::findOne(Yii::$app->user->id);
        $modelform = new ProfileSettings();
        $modelform->use_password = $user->use_password;
        $passform = new ChangepassForm();
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'passform' => $passform, 'user' => $user]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
        return $this->redirect($login_page->getUrlPath());
    }

    public function actionSaveavatar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить фотографию */
        $modelform = new Avatar();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;
            // вручную валидируем файл
            $fileInstance = UploadedFile::getInstance($modelform, 'image');
            if (!empty($fileInstance)) {
                // проверить размер и расширение
                if (!in_array($fileInstance->type, ["image/png", "image/jpeg"])) {
                    return [
                        'status' => 'fail',
                        'message' => 'Такой формат изображений не поддерживается.',
                    ];
                }
                if ($fileInstance->size > 1024 * 1024) {
                    return [
                        'status' => 'fail',
                        'message' => 'Максимальный размер файла - 1Мб.',
                    ];
                }
                // если проверки пройдены, сохраняем файл

                $current_order = Avatarmoder::find()->where(['user_id' => $user->id, 'status' => Avatarmoder::STATUS_MODERATE])->one();
                if (!empty($current_order)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Фотография уже была отправлена на модерацию',
                    ];
                }
                $moderation = new Avatarmoder();
                $moderation->user_id = $user->id;
                $moderation->status = Avatarmoder::STATUS_MODERATE;
                $moderation->visible = 0;
                $moderation->save();
                $new_file_model = new FilestoreModel();
                $new_file_model->file_path = 'files/upload/users/';
                $new_file_model->keeper_id = $user->profile->id;
                $new_file_model->isMain = true;
                $new_file_model->order = 0;
                $new_file_model->keeper_class = Avatarmoder::class;
                $new_file_model->keeper_field = 'image';
                $new_file_model->new_name = 'profile_' . time() . rand(10, 99);
                $new_file_model->file_loader = $fileInstance;
                $new_file_model->description = '';
                $moderation->link('image', $new_file_model);
            }
            return [
                'status' => 'success',
                'message' => 'Фотография отправлена на модерацию',
            ];
        }
    }

    public function actionSavefizusr()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить Физлицо */
        $modelform = new Profilefizusr();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $user->profile->name = $modelform->name;
            $user->profile->surname = $modelform->surname;
            $user->profile->patronymic = $modelform->patronymic;
            $user->profile->phone = $modelform->phone;
            $user->profile->city_id = $modelform->city_id;
            $message = 'Вы успешно изменили профиль';
            if ($modelform->email != $user->email) {
                $user->email_to_confirm = $modelform->email;
            }

            if ($user->save()) {
                if ($modelform->email != $user->email) {
                    $user->sendResetEmail();
                    $message = 'Вы успешно изменили профиль.<br> Для смены email следуйте инструкциям, отправленным на новый email.';
                }
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        /* изменить Юрлицо */
        $modelform = new Profileurusr();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;
            $user->organization->inn = $modelform->inn;

            $user->profile->organization_name = $modelform->organization_name;
            $user->profile->office = $modelform->office;
            $user->profile->name = $modelform->name;
            $user->profile->surname = $modelform->surname;
            $user->profile->patronymic = $modelform->patronymic;
            $user->profile->phone = $modelform->phone;
            $user->profile->city_id = $modelform->city_id;
            $message = 'Вы успешно изменили профиль';
            if ($modelform->email != $user->email) {
                $user->email_to_confirm = $modelform->email;
            }

            if ($user->save()) {
                if ($modelform->email != $user->email) {
                    $user->sendResetEmail();
                    $message = 'Вы успешно изменили профиль.<br> Для смены email следуйте инструкциям, отправленным на новый email.';
                }
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        /* изменить Эксперта */
        $modelform = new Profileexpert();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $message = 'Вы успешно изменили профиль';
            $status = 'success';

            if (($user->profile->name != $modelform->name)
                or ($user->profile->surname != $modelform->surname)
                or ($user->profile->patronymic != $modelform->patronymic)) {
                $current_order = Profmoder::find()->where(['user_id' => $user->id, 'status' => Profmoder::STATUS_MODERATE, 'type' => Profmoder::TYPE_FIO])->one();
                if (!$current_order) {
                    $profmoder = new Profmoder();
                    $profmoder->user_id = $user->id;
                    $profmoder->name = $modelform->name;
                    $profmoder->surname = $modelform->surname;
                    $profmoder->patronymic = $modelform->patronymic;
                    $profmoder->type = Profmoder::TYPE_FIO;
                    $profmoder->status = Profmoder::STATUS_MODERATE;
                    $profmoder->visible = 0;
                    if ($profmoder->save()) {
                        $message = 'Вы успешно изменили профиль.<br> Решение о смене ФИО будет вынесено на рассмотрение.';
                        // отправить письмо админу,
                        // $profmoder->sendModerateMail();
                    } else {
                        $status = 'fail';
                        $message = 'Во время создания заявки на изменение ФИО возникли ошибки. ' . \app\helpers\MainHelper::getHelpText();
                    }
                } else {
                    $status = 'fail';
                    $message = 'Вы уже отправляли запрос на изменение ФИО. Дождитесь решения по предыдущему запросу, прежде чем подавать новый.';
                }
            }

            $user->profile->phone = $modelform->phone;
            $user->profile->city_id = $modelform->city_id;
            $user->profile->assist_name = $modelform->assist_name;
            $user->profile->assist_phone = $modelform->assist_phone;
            $user->profile->assist_email = $modelform->assist_email;
            $user->profile->comfy_time_from = $modelform->comfy_time_from;
            $user->profile->comfy_time_to = $modelform->comfy_time_to;
            $user->connect = $modelform->connect;
            $user->profile->extra_direct = $modelform->extra_direct ?? '';
            $user->profile->extra_links = $modelform->extra_links ?? '';
            if ($modelform->email != $user->email) {
                $user->email_to_confirm = $modelform->email;
            }

            if ($user->save()) {
                if ($modelform->email != $user->email) {
                    $user->sendResetEmail();
                    $message = 'Вы успешно изменили профиль.<br> Для смены email следуйте инструкциям, отправленным на новый email.';
                }
                return [
                    'status' => $status,
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        /* изменить Экспертную организацию */
        $modelform = new Profileexporg();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = UserExporg::findOne(Yii::$app->user->id);

            $message = 'Вы успешно изменили профиль';
            $status = 'success';

            if ($user->profile->organization_name != $modelform->organization_name) {
                $current_order = Profmoder::find()->where(['user_id' => $user->id, 'status' => Profmoder::STATUS_MODERATE, 'type' => Profmoder::TYPE_ORG])->one();
                if (!$current_order) {
                    $profmoder = new Profmoder();
                    $profmoder->user_id = $user->id;
                    $profmoder->organization_name = $modelform->organization_name;
                    $profmoder->type = Profmoder::TYPE_ORG;
                    $profmoder->status = Profmoder::STATUS_MODERATE;
                    $profmoder->visible = 0;
                    if ($profmoder->save()) {
                        $message = 'Вы успешно изменили профиль.<br> Решение о смене ФИО будет вынесено на рассмотрение.';
                        // отправить письмо админу,
                        // $profmoder->sendModerateMail();
                    } else {
                        $status = 'fail';
                        $message = 'Во время создания заявки на изменение ФИО возникли ошибки. ' . \app\helpers\MainHelper::getHelpText();
                    }
                } else {
                    $status = 'fail';
                    $message = 'Вы уже отправляли запрос на изменение ФИО. Дождитесь решения по предыдущему запросу, прежде чем подавать новый.';
                }
            }
            if ($modelform->email != $user->email) {
                $user->email_to_confirm = $modelform->email;
            }
            $user->profileExtended->year = $modelform->year;
            $user->profile->organization_phone = $modelform->organization_phone;
            $user->profile->city_id = $modelform->city_id;
            $user->profileExtended->address = $modelform->address;
            $user->profileExtended->director_fio = $modelform->director_fio;
            $user->profileExtended->director_office = $modelform->director_office;
            $user->profile->name = $modelform->name;
            $user->profile->surname = $modelform->surname;
            $user->profile->patronymic = $modelform->patronymic;
            $user->profile->office = $modelform->office;
            $user->profile->organization_email = $modelform->organization_email;
            $user->profile->phone = $modelform->phone;
            $user->profile->comfy_time_from = $modelform->comfy_time_from;
            $user->profile->comfy_time_to = $modelform->comfy_time_to;
            $user->profile->extra_direct = $modelform->extra_direct;
            $user->profile->extra_links = $modelform->extra_links;

            $user->connect = $modelform->connect;
            if ($user->save()) {
                if ($modelform->email != $user->email) {
                    $user->sendResetEmail();
                    $message = 'Вы успешно изменили профиль.<br> Для смены email следуйте инструкциям, отправленным на новый email.';
                }
                return [
                    'status' => $status,
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        $modelform = new ChangepassForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $user->setPassword($modelform->password);
            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSavecontacts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить Контакты эксперта */
        $modelform = new ProfileContactexpert();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $user->profile->url_to_site = $modelform->url_to_site;
            $user->profile->url_to_vk = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?vk\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_vk, 'https://vk.com/');
            $user->profile->url_to_fb = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?facebook\.com\/|\/?|@?)(?'prefix'groups\/)?(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_fb, 'https://facebook.com/');
            $user->profile->url_to_insta = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?instagram\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_insta, 'https://instagram.com/');
            $user->profile->url_to_dzen = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?(dzen\.ru|zen\.yandex\.ru)\/|\/?|@?)(?'prefix'id\/)?(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_dzen, 'https://dzen.ru/');
            $user->profile->url_to_twitter = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?twitter\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_twitter, 'https://twitter.com/');
            $user->profile->url_to_youtube = MainHelper::format_socnet_url("/^(https?:\/\/)?(www\.)?youtu(\.be|be\.com)\/(?'profile'((((user)|(channel)|c)\/)([абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z0-9\-_]{1,})$)|(@[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z0-9\-_]{1,})$)|^(?'empty')$/", $modelform->url_to_youtube, 'https://youtube.com/');
            $user->profile->url_to_telegram = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?t\.me\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_telegram, 'https://t.me/');

            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        /* изменить Контакты экспертной организации */
        $modelform = new ProfileContactexporg();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = UserExporg::findOne(Yii::$app->user->id);

            $user->profile->url_to_site = $modelform->url_to_site;
            $user->profile->url_to_vk = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?vk\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_vk, 'https://vk.com/');
            $user->profile->url_to_fb = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?facebook\.com\/|\/?|@?)(?'prefix'groups\/)?(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_fb, 'https://facebook.com/');
            $user->profile->url_to_insta = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?instagram\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_insta, 'https://instagram.com/');
            $user->profile->url_to_dzen = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?(dzen\.ru|zen\.yandex\.ru)\/|\/?|@?)(?'prefix'id\/)?(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_dzen, 'https://dzen.ru/');
            $user->profile->url_to_twitter = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?twitter\.com\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_twitter, 'https://twitter.com/');
            $user->profile->url_to_youtube = MainHelper::format_socnet_url("/^(https?:\/\/)?(www\.)?youtu(\.be|be\.com)\/(?'profile'((((user)|(channel)|c)\/)([абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z0-9\-_]{1,})$)|(@[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z0-9\-_]{1,})$)|^(?'empty')$/", $modelform->url_to_youtube, 'https://youtube.com/');
            $user->profile->url_to_telegram = MainHelper::format_socnet_url("/^(((https?:\/\/)?(www\.)?t\.me\/|\/?|@?)(?'profile'[^\/\s\?]+)\/?(\?.+)?|^(?'empty')$)$/", $modelform->url_to_telegram, 'https://t.me/');

            $user->profileExtended->photos_link = $modelform->photos_link;
            $user->profileExtended->docs_link = $modelform->docs_link;
            $user->profileExtended->video_links = $modelform->video_links;
            $user->profileExtended->publications = $modelform->publications;

            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];


        }

        return [
            'status' => 'fail',
        ];
    }

    public function actionSavesettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить настройки пользователя */
        $modelform = new ProfileSettings();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $user->use_password = $modelform->use_password;
            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        $modelform = new ChangepassForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;

            $user->setPassword($modelform->password);
            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        return [
            'status' => 'fail',
        ];
    }

    public function actionSaveprofarea()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить Профобласть */
        $modelform = new ProfileProfareaexpert();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;
            $message = 'Вы успешно изменили профиль';
            $status = 'success';

            // $exist_directions = array_values(ArrayHelper::map($user->direction,'id','id'));
            $exist_keywords = array_values(ArrayHelper::map($user->keywords, 'id', 'id'));
            $exist_competence = array_values(ArrayHelper::map($user->competence, 'id', 'id'));
            $exist_solvtask = array_values(ArrayHelper::map($user->solvtask, 'id', 'id'));
            // if (empty($modelform->direction)) {$modelform->direction = [];}
            if (empty($modelform->competence)) {
                $modelform->competence = [];
            }
            if (empty($modelform->keywords)) {
                $modelform->keywords = [];
            }
            if (empty($modelform->solvtask)) {
                $modelform->solvtask = [];
            }
            if (
                /*(!empty(array_diff($modelform->direction, $exist_directions)) or !empty(array_diff($exist_directions, $modelform->direction)))
                or */
                (!empty(array_diff($modelform->keywords, $exist_keywords)) or !empty(array_diff($exist_keywords, $modelform->keywords)))
                or (!empty(array_diff($modelform->competence, $exist_competence)) or !empty(array_diff($exist_competence, $modelform->competence)))
                or (!empty(array_diff($modelform->solvtask, $exist_solvtask)) or !empty(array_diff($exist_solvtask, $modelform->solvtask)))
            ) {
                $current_order = Profmoder::find()->where(['user_id' => $user->id, 'status' => Profmoder::STATUS_MODERATE, 'type' => Profmoder::TYPE_PROF])->one();
                if (!$current_order) {
                    $profmoder = new Profmoder();
                    $profmoder->user_id = $user->id;
                    // $profmoder->direction = serialize($modelform->direction);
                    $profmoder->keywords = $modelform->keywords;
                    $profmoder->competence = serialize($modelform->competence);
                    $profmoder->solvtask = serialize($modelform->solvtask);
                    $profmoder->status = Profmoder::STATUS_MODERATE;
                    $profmoder->type = Profmoder::TYPE_PROF;
                    $profmoder->visible = 0;
                    if ($profmoder->save()) {
                        $message = 'Вы успешно изменили профиль.<br> Решение об изменении данных профессональной области вынесено на рассмотрение.';
                        // отправить письмо админу,
                        // $profmoder->sendModerateMail();
                    } else {
                        $status = 'fail';
                        $message = 'Во время создания заявки на изменение профессиональной области возникли ошибки. ' . \app\helpers\MainHelper::getHelpText();
                    }
                } else {
                    $status = 'fail';
                    $message = 'Вы уже отправляли запрос на изменение профессиональной области. Дождитесь решения по предыдущему запросу, прежде чем подавать новый.';
                }
            }

            // менять основную кафедру нельзя просто так. А как можно - еще не решили
            if ($user->save()) {
                return [
                    'status' => $status,
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSavecafedra()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить Направление */
        $modelform = new ProfileCafedra();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            /** @var UserAR $user найти пользователя, изменить его */
            $user = Yii::$app->user->identity->userAR;
            $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
            if ($role == 'fizusr') {
                // менять основную кафедру можно только до того как стал Экспертом
                $no_cafedra = ProfileNoCafedra::find()->where(['model' => ProfileNoCafedra::class, 'visible' => 1])->one();
                $anketa = ProfilePretendentPage::find()->where(['model' => ProfilePretendentPage::class, 'visible' => 1])->one();
                if ($modelform->main_direction == -1) {
                    // создать заявку на создание кафедры
                    $new_caf = new Anocafedra();
                    $new_caf->user_id = $user->id;
                    $new_caf->comment = $modelform->comment;
                    if ($userSertificateM = $user->sertificateM) {
                        $userSertificateM->delete();
                    }
                    $user->ano_cafedra = 1;
                    /* не менять порядок сохранения заявки и данных пользователя иначе в Контакт Битрикс24 не уйдет запрашиваемая кафедра */
                    if ($new_caf->save() and $user->save()) {
                        \app\helpers\Mail::sendMail(
                            $new_caf,
                            Settings::getInfo('anocafedra_moderator_email'),
                            '@app/modules/anocafedra/mails/letter',
                            'Заявка на кафедру'
                        );
                        return [
                            'status' => 'success',
                            'redirect_to' => $no_cafedra->getUrlPath(),
                            'message' => 'Заявка отправлена',
                        ];
                    }
                } else {
                    // кафедры в процессе формирования сохраняются так же как обычные, ограничения будут дальше. Единственное - для них другой текст на странице.
                    $direction = Direction::find()->where(['id' => $modelform->main_direction, 'visible' => 1, 'stels_direct' => 0])->one();
                    if ($direction) {
                        $userDirection = $user->sertificateM;
                        if (is_null($userDirection)) {
                            $userDirection = new UserDirection([
                                'user_id' => $user->id,
                                'main_direction' => true,
                                'role' => 'expert',
                            ]);
                        }
                        $userDirection->direction_id = $modelform->main_direction;

                        $user->ano_cafedra = $direction->coming_soon;
                        /* данные в Битрикс24 передаются при сохранении пользователя */
                        if ($userDirection->save() && $user->save()) {
                            return [
                                'status' => 'success',
                                'redirect_to' => ($direction->coming_soon ? $no_cafedra->getUrlPath() : $anketa->getUrlPath()),
                                'message' => 'Кафедра сохранена',
                            ];
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                        ];

                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Выбранная кафедра недоступна',
                    ];

                }
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете редактировать данные о себе.',
                ];
            }
        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSaveanketa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        // найти пользователя
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr'])) {
            return [
                'status' => 'fail',
                'message' => 'Вы не можете изменить анкету',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
        if ($anketa) {
            return [
                'status' => 'fail',
                'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
            ];
        }
        /* изменить Поле профиля */
        $post = Yii::$app->request->post();
        if (!empty($post['attribute'])) {
            $attribute = str_replace('profileanketa-', '', $post['attribute']);
            if (in_array($attribute, ['city_id', 'sex', 'birthday', 'url_to_site', 'about_myself', 'history', 'video', 'require_link', 'url_to_vk', 'url_to_fb', 'url_to_insta', 'url_to_dzen', 'url_to_twitter', 'url_to_youtube', 'url_to_telegram', 'extra_links', 'extra_direct', 'name', 'surname', 'patronymic'])) {
                $user->profile->{$attribute} = $post['value'];
            }
            if ($attribute == 'competence') {
                if (!empty($post['value'])) {
                    foreach ($post['value'] as $key => $competence) {
                        $finded = Competence::findOne((int)$competence);
                        if (!$finded) {
                            $finded = Competence::find()->where(['name' => $competence])->one();
                        }
                        if (!$finded) {
                            $finded = new Competence();
                            $finded->name = $competence;
                            $finded->visible = 1;
                            $finded->save();
                        }
                        $post['value'][$key] = $finded;
                    }
                }
                $user->competence = $post['value'];
            }
            if ($attribute == 'solvtask') {
                if (!empty($post['value'])) {
                    foreach ($post['value'] as $key => $solvtask) {
                        $finded = Solvtask::findOne((int)$solvtask);
                        if (!$finded) {
                            $finded = Solvtask::find()->where(['name' => $solvtask])->one();
                        }
                        if (!$finded) {
                            $finded = new Solvtask();
                            $finded->name = $solvtask;
                            $finded->visible = 1;
                            $finded->save();
                        }
                        $post['value'][$key] = $finded;
                    }
                }
                $user->solvtask = $post['value'];
            }
            /* данные в Битрикс24 уходят при сохранении пользователя */
            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => 'Данные успешно обновлены',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно сохранить изменения. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSaveanketafiles()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        // найти пользователя
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr'])) {
            return [
                'status' => 'fail',
                'message' => 'Вы не можете изменить анкету',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
        if ($anketa) {
            return [
                'status' => 'fail',
                'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
            ];
        }
        /* изменить загруженные файлы */
        $post = Yii::$app->request->post();
        // валидация загуженных файлов
        $fileInstance = UploadedFile::getInstanceByName('files');
        if (!empty($fileInstance)) {
            // проверить размер и расширение
            if (!in_array($fileInstance->type, ["image/png", "image/jpeg", "application/x-zip-compressed", "application/pdf", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"])) {
                return [
                    'status' => 'fail',
                    'message' => 'Загрузка документа невозможна. Данный формат файлов не поддерживается.',
                ];
            }
            if ($fileInstance->size > 2 * 1024 * 1024) {
                return [
                    'status' => 'fail',
                    'message' => 'Загрузка документа невозможна. Максимальный размер файла - 2Мб.',
                ];
            }
            // если проверки пройдены, сохраняем файл

            // если file_id заполнен - удалить прошлый файл
            if (!empty($post['file_id'])) {
                $file = FilestoreModel::find()->where(['id' => $post['file_id'], 'keeper_id' => $user->profile->id, 'keeper_field' => 'requirements', 'keeper_class' => Profile::class])->one();
                if ($file) {
                    if (!$file->delete()) {
                        return [
                            'status' => 'fail',
                            'message' => 'Ошибка удаления. ' . \app\helpers\MainHelper::getHelpText(),
                        ];
                    }
                }
            } elseif (count($user->profile->requirements) >= 20) {
                return [
                    'status' => 'fail',
                    'message' => 'Загрузка документа невозможна. Разрешена загрузка не более 20 документов.',
                ];
            }
            $new_file_model = new FilestoreModel();
            $new_file_model->file_path = 'files/upload/users/';
            $new_file_model->keeper_id = $user->profile->id;
            $new_file_model->isMain = true;
            $new_file_model->order = 0;
            $new_file_model->keeper_class = Profile::class;
            $new_file_model->keeper_field = 'requirements';
            $new_file_model->new_name = 'profile_' . time() . rand(10, 99);
            $new_file_model->file_loader = $fileInstance;
            $new_file_model->description = '';
            $res = $user->profile->link('requirements', $new_file_model);
            if (empty($new_file_model->errors)) {
                return [
                    'status' => 'success',
                    'message' => 'Документ загружен',
                    'file_id' => $new_file_model->id,
                ];
            }
            return [
                'message' => 'Невозможно загрузить документ. ' . \app\helpers\MainHelper::getHelpText(),
                'status' => 'fail',
            ];

        }
        return [
            'message' => 'Невозможно загрузить документ. ' . \app\helpers\MainHelper::getHelpText(),
            'status' => 'fail',
        ];
    }

    public function actionRemoveanketafiles()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Документ */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr'])) {
            return [
                'status' => 'fail',
                'message' => 'Вы не можете изменить анкету',
            ];
        }
        $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
        $can_edit = ($anketa == false);

        if ($can_edit) {
            $post = Yii::$app->request->post();
            if (!empty($post['id'])) {
                $file = FilestoreModel::find()->where(['id' => $post['id'], 'keeper_id' => $user->profile->id, 'keeper_field' => 'requirements', 'keeper_class' => Profile::class])->one();
                if ($file) {
                    if ($file->delete()) {
                        return [
                            'status' => 'success',
                            'message' => 'Документ удален.',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Ошибка удаления. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Документ не найден',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
        ];

    }

    public function actionSaveeducation(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $post = Yii::$app->request->post();
        $user = Yii::$app->user->identity->userAR;

        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование образования',
            ];
        }

        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }

        $modelform = new ProfileEducation();
        if (!(Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate())) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        if (empty($post['id'])) {
            // создать Образование
            $education = new Education();
            $education->attributes = $modelform->attributes;
            $education->user_id = $user->id;
            $education->visible = 1;

            if (!$education->save()) {
                return [
                    'status' => 'fail',
                    'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
                ];
            }
            return [
                'status' => 'success',
                'message' => 'Учебное заведение добавлено.',
                'data' => ['id' => $education->id, 'name' => $education->name],
                'new_educat_html' => $this->renderPartial('_education_element', ['education' => $education, 'can_edit' => true]),
            ];
        }
        // изменить Образование
        $education = Education::findOne((int)$post['id']);
        if (!$education) {
            return [
                'status' => 'fail',
                'message' => 'Учебное заведение не найдено',
            ];
        }
        if ($education->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, учебное заведение не найдено.',
            ];
        }
        $education->attributes = $modelform->attributes;
        if (!$education->save()) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'success',
            'message' => 'Учебное заведение изменено.',
            'data' => ['id' => $education->id, 'name' => $education->name],
            'new_educat_html' => $this->renderPartial('_education_element', ['education' => $education, 'can_edit' => true]),
        ];

    }

    public function actionSavecareer(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $post = Yii::$app->request->post();
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование организации',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }

        $modelform = new ProfileCareer();
        if (!(Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate())) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        if (empty($post['id'])) {
            /* создать Организацию */
            $career = new Career();
            $career->attributes = $modelform->attributes;
            $career->user_id = $user->id;
            $career->visible = 1;

            if (!$career->save()) {
                return [
                    'status' => 'fail',
                    'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Организация добавлена.',
                'data' => ['id' => $career->id, 'name' => $career->name],
                'new_career_html' => $this->renderPartial('_career_element', ['career' => $career, 'can_edit' => true]),
            ];
        }
        /* изменить Организацию */
        $career = Career::findOne((int)$post['id']);
        if (!$career) {
            return [
                'status' => 'fail',
                'message' => 'Организация не найдена',
            ];
        }
        if ($career->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, организация не найдена.',
            ];
        }
        $career->attributes = $modelform->attributes;
        if (!$career->save()) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Организация изменена.',
            'data' => ['id' => $career->id, 'name' => $career->name],
            'new_career_html' => $this->renderPartial('_career_element', ['career' => $career, 'can_edit' => true]),
        ];

    }

    public function actionClearavatar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Образование */
        $user = Yii::$app->user->identity->userAR;
        if ($user->profile->image) {
            if ($user->profile->image->delete()) {
                return [
                    'status' => 'success',
                    'message' => 'Аватар удален',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно удалить аватар',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Аватар отсутствует',
        ];

    }

    public function actionSavemember()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }

        $post = Yii::$app->request->post('ProfileExporgExtended');
        $user = UserExporg::findOne(Yii::$app->user->id);

        if (isset($post['members']) && !empty($post['members'])) {
            $user->profileExtended->members = $post['members'];
            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => 'Данные об участниках организации изменены.',
                ];
            }
        } else {
            $user->profileExtended->members = [];
            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => 'Список участников пуст.',
                ];
            }
        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionRemoveeducation(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Образование */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на удаление образования',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }

        $post = Yii::$app->request->post();
        if (empty($post['id'])) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        $education = Education::findOne((int)$post['id']);
        if (!$education) {
            return [
                'status' => 'fail',
                'message' => 'Учебное заведение не найдено',
            ];
        }
        if ($education->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, учебное заведение не найдено.',
            ];
        }
        if (!$education->delete()) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка удаления. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Учебное заведение удалено.',
        ];
    }

    public function actionRemovecareer(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* удалить Место работы */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование образования',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }
        $post = Yii::$app->request->post();
        if (empty($post['id'])) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        $career = Career::findOne((int)$post['id']);
        if (!$career) {
            return [
                'status' => 'fail',
                'message' => 'Организация не найдена',
            ];
        }
        if ($career->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, организация не найдена.',
            ];
        }
        if (!$career->delete()) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка удаления. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Организация удалена.',
        ];
    }

    public function actionNeweducation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* создать Образование */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование образования',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }
        $modelform = new ProfileEducation();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $education = new Education();
            $education->attributes = $modelform->attributes;
            $education->user_id = $user->id;
            $education->visible = 1;

            if ($education->save()) {
                return [
                    'status' => 'success',
                    'new_educat_html' => $this->renderPartial('_education_box', ['education' => $education, 'stages' => $modelform->getStageList(), 'can_edit' => true]),
                    'message' => 'Учебное заведение добавлено.',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionNewcareer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* создать Место работы */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['fizusr', 'expert'])) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на редактирование образования',
            ];
        }
        // если анкета на модерации - то нельзя изменить
        if ($user->role == 'fizusr') {
            $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
            if ($anketa) {
                return [
                    'status' => 'fail',
                    'message' => 'Ваша анкета находится на модерации. Вы не можете редактировать данные о себе.',
                ];
            }
        }
        $modelform = new ProfileCareer();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $career = new Career();
            $career->attributes = $modelform->attributes;
            $career->user_id = $user->id;
            $career->visible = 1;

            if ($career->save()) {
                return [
                    'status' => 'success',
                    'new_career_html' => $this->renderPartial('_career_box', ['career' => $career, 'can_edit' => true]),
                    'message' => 'Организация добавлена.',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Ошибка сохранения данных. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionModerateme()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* если это именно запрос */
        if (Yii::$app->request->isAjax) {
            $get = Yii::$app->request->get();
            /* и корректно передан экшен */
            if ($get['action'] == 'moderate') {
                $user = Yii::$app->user->identity->userAR;
                if ($user->role == 'fizusr') {
                    $anketa = Anketaquery::find()->where(['status' => Anketaquery::STATUS_MODERATE, 'user_id' => $user->id])->one();
                    if (empty($anketa)) {
                        // создаем заявку на Эксперта.
                        $anketa = new Anketaquery();
                        $anketa->user_id = $user->id;
                        $anketa->name = $user->profile->halfname . ' от ' . date('d.m.Y');
                        $anketa->status = Anketaquery::STATUS_MODERATE;
                        $anketa->visible = 0;
                        $anketa->direction_id = $user->directionM->id;
                        if ($anketa->save()) {
                            // отправить письмо админу,
                            $user->sendExpertModeratorEmail('new_expert');
                            // обновляем данные в Битрикс24
                            $user->pretendent = 1;
                            $user->save();
                            return [
                                'status' => 'success',
                                'message' => 'Ваша анкета отправлена на модерацию. Ожидайте результата.',
                            ];
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно отправить анкету на модерацию. ' . \app\helpers\MainHelper::getHelpText(),
                        ];

                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Ваша анкета уже находится на модерации. Ожидайте результата.',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете отправить анкету на модерацию',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Действие не выбрано. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionSavehistory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* изменить Историю эксперта */
        $modelform = new ProfileHistory();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = Yii::$app->user->identity->userAR;
            $user->profile->history = $modelform->history;
            $user->profile->video = $modelform->video;
            $user->profile->about_myself = $modelform->about_myself;
            $message = 'Вы успешно изменили профиль';
            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }

        /* изменить Историю экспертной организации */
        $modelform = new ProfileHistoryExporg();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // найти пользователя, изменить его
            $user = UserExporg::findOne(Yii::$app->user->id);

            $user->profile->history = $modelform->history;
            $user->profile->video = $modelform->video;
            $user->profile->description = $modelform->description;
            $user->profileExtended->products = $modelform->products;
            $user->profile->about_myself = $modelform->about_myself;

            $message = 'Вы успешно изменили профиль';

            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Во время сохранения данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionSetnewmail($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $get = Yii::$app->request->get();
        Yii::$app->view->title = $model->getNameForView();
        $message = '';
        if (isset($get['key']) && isset($get['hash'])) {
            $user = UserAR::find()->where(['password_reset_token' => $get['key']])->one();
            if ($user) {
                if ($get['hash'] == md5($user->email)) {
                    if (($user->status == UserAR::STATUS_ACTIVE)) {
                        $sameEmail = UserAR::find()->where(['email' => $user->email_to_confirm])->one();
                        if (empty($sameEmail)) {
                            // если пользователь был подписан на рассылку, то в заявке на рассылку тоже заменить email.
                            $subscribe = \app\modules\subscribe\models\Subscribe::find()->where(['email' => $user->email])->one();
                            if ($subscribe) {
                                /* запись не сохранять, пока не сохранится пользователь!!! иначе в Битрикс уйдет новый контакт-подписчика */
                                $subscribe->email = $user->email_to_confirm;
                            }
                            $user->password_reset_token = md5(time());
                            $user->email = $user->email_to_confirm;
                            $user->email_to_confirm = null;
                            if ($user->save()) {
                                if ($subscribe) {
                                    /* и тут лучше не сохранять, а просто обновить данные, т.к. скрипт по обновлению контакта пользователя может еще не успеть завершиться */
                                    $subscribe->updateAttributes(['email' => $subscribe->email]);
                                }
                                $message = $model->content;
                            } else {
                                $message = '<p>Невозможно изменить email. ' . \app\helpers\MainHelper::getHelpText();
                            }
                        } else {
                            $message = '<p>Такой email уже используется.</p>';
                        }
                    } else {
                        $message = '<p>Пользователь не активирован.</p>';
                    }
                } else {
                    $message = '<p>Ссылка недействительна: параметры заданы некорректно.</p>';
                }
            } else {
                $message = '<p>Ссылка недействительна: ключ задан некорректно.</p>';
            }
        } else {
            $message = '<p>Ссылка недействительна: параметры не заданы.</p>';
        }
        return $this->render($model->view, ['message' => $message, 'model' => $model]);
    }

    public function actionGetdirinfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $get = Yii::$app->request->get();
            if (!empty($get['direction'])) {
                if ($get['direction'] == -1) {
                    // выбрана кафедра "Другое"
                    return [
                        'status' => 'fail',
                    ];
                }
                $direction = Direction::findOne((int)$get['direction']);
                if ($direction) {
                    if ($direction->coming_soon) {
                        return [
                            'status' => 'success',
                            'html' => '<p>Экспертный совет кафедры опубликует требования к отбору экспертов в течение месяца. Мы пришлем вам письмо с приглашением заполнить анкету эксперта.</p><p>Если вы хотите присоединиться к данной кафедре, нажмите кнопку ПРОДОЛЖИТЬ.</p>',
                        ];
                    }
                    if (!empty($direction->register_text)) {
                        return [
                            'status' => 'success',
                            'html' => $direction->register_text,
                        ];
                    }

                }
            }
        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionGeteducation(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации.',
            ];
        }
        $get = Yii::$app->request->get();
        if (!Yii::$app->request->isAjax || empty($get['id'])) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        $education = Education::findOne((int)$get['id']);
        if (!$education) {
            return [
                'status' => 'fail',
                'message' => 'Учебное заведение не найдено.',
            ];
        }
        if ($education->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, учебное заведение не найдено.',
            ];
        }

        $profileEducation = new ProfileEducation();
        $profileEducation->attributes = $education->attributes;

        return [
            'status' => 'success',
            'message' => 'Учебное заведение успешно получено.',
            'data' => $profileEducation->attributes,
        ];
    }

    public function actionGetcareer(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации.',
            ];
        }
        $get = Yii::$app->request->get();
        if (!Yii::$app->request->isAjax || empty($get['id'])) {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        $career = Career::findOne((int)$get['id']);
        if (!$career) {
            return [
                'status' => 'fail',
                'message' => 'Организация не найдена.',
            ];
        }
        if ($career->user_id != Yii::$app->user->id) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации, организация не найдена.',
            ];
        }

        $careerEducation = new ProfileCareer();
        $careerEducation->attributes = $career->attributes;

        return [
            'status' => 'success',
            'message' => 'Организация успешно получена.',
            'data' => $careerEducation->attributes,
        ];
    }
}
