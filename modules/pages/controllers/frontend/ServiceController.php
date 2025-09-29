<?php

namespace app\modules\pages\controllers\frontend;

use app\models\RegisterMarketPlace;
use app\modules\admin\components\FilestoreModel;
use app\modules\contract\models\Contract;
use app\modules\contract\models\ContractEduprog;
use app\modules\pages\models\LKEduprogEdit;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\PServiceDogovorIP;
use app\modules\pages\models\PServiceDogovorOOO;
use app\modules\pages\models\PServiceDogovorSelfbusy;
use app\modules\pages\models\PServiceIP;
use app\modules\pages\models\PServiceModerateIP;
use app\modules\pages\models\PServiceModerateLicense;
use app\modules\pages\models\PServiceModerateOOO;
use app\modules\pages\models\PServiceModerateSelfbusy;
use app\modules\pages\models\PServiceOOO;
use app\modules\pages\models\PServiceRegIP;
use app\modules\pages\models\PServiceRegOOO;
use app\modules\pages\models\PServiceRegSelfbusy;
use app\modules\pages\models\PServiceSelfbusy;
use app\modules\regservice\models\Regservice;
use app\modules\users\models\Organization;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class ServiceController extends LKController
{
    /* страница регистрации услуг в качестве физлица (текстовая) */
    public function actionRegfiz($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страница регистрации услуг в качестве Юрлица (текстовая + кнопка на регистрацию) */
    public function actionRegindexooo($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $query_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($query_form) {
            if ($user->organization->type_mp != Organization::TYPE_OOO) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
            /* если заявка на модерации - редирект на страницу с успешной отправкой */
            if ($query_form->status == Regservice::STATUS_MODERATE) {
                $moderate_page = PServiceModerateOOO::find()->where(['model' => PServiceModerateOOO::class, 'visible' => 1])->one();
                return $this->redirect($moderate_page->getUrlPath());
            }
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'query_form' => $query_form]);
    }

    /* страница регистрации услуг в качестве ИП (текстовая + кнопка на регистрацию) */
    public function actionRegindexip($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $query_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($query_form) {
            if ($user->organization->type_mp != Organization::TYPE_IP) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации Юрлица
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
            /* если заявка на модерации - редирект на страницу с успешной отправкой */
            if ($query_form->status == Regservice::STATUS_MODERATE) {
                $moderate_page = PServiceModerateIP::find()->where(['model' => PServiceModerateIP::class, 'visible' => 1])->one();
                return $this->redirect($moderate_page->getUrlPath());
            }
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'query_form' => $query_form]);
    }

    /* страница регистрации услуг в качестве самозанятого (текстовая + кнопка на регистрацию) */
    public function actionRegindexselfbusy($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $query_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($query_form) {
            if ($user->organization->type_mp != Organization::TYPE_SELFBUSY) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации Юрлица
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
            /* если заявка на модерации - редирект на страницу с успешной отправкой */
            if ($query_form->status == Regservice::STATUS_MODERATE) {
                $moderate_page = PServiceModerateSelfbusy::find()->where(['model' => PServiceModerateSelfbusy::class, 'visible' => 1])->one();
                return $this->redirect($moderate_page->getUrlPath());
            }
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'query_form' => $query_form]);
    }

    /* страница регистрации услуг в качестве Юрлица (форма регистрации) */
    public function actionRegformooo($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_OOO) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }

        $modelform = new RegisterMarketPlace();
        $modelform->loadOOO();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    /* страница регистрации услуг в качестве ИП (форма регистрации) */
    public function actionRegformip($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_IP) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации Юрлица
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }

        $modelform = new RegisterMarketPlace();
        $modelform->loadIP();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    /* страница регистрации услуг в качестве ИП (форма регистрации) */
    public function actionRegformselfbusy($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_SELFBUSY) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации Юрлица
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации самозанятого
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }

        $modelform = new RegisterMarketPlace();
        $modelform->loadSBusy();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'user' => $user]);
    }

    /* страница подписания договора в качестве Юрлица */
    public function actionRegdogovorooo($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_OOO) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }
        $contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_OOO])->orderBy(['version' => SORT_DESC])->one();
        if ($contract) {
            $reg_form->contract_id = $contract->id;
            $reg_form->updateAttributes(['contract_id' => $reg_form->contract_id]);
        }
        $contract_eduprog = false;
        $reg_form->license_contract_id = '';
        if ($user->organization->license_service) {
            // выключатель ДПО
            if (Yii::$app->params['enable_dpo']) {
                $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_OOO])->orderBy(['version' => SORT_DESC])->one();
                if ($contract_eduprog) {
                    $reg_form->license_contract_id = $contract_eduprog->id;
                }
            }
        }
        $reg_form->updateAttributes(['license_contract_id' => $reg_form->license_contract_id]);
        $form_page = PServiceRegOOO::find()->where(['model' => PServiceRegOOO::class, 'visible' => 1])->one();
        $modelform = new RegisterMarketPlace();
        $modelform->loadOOO();
        $modelform->setScenario('contract_sign');

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'contract' => $contract, 'contract_eduprog' => $contract_eduprog, 'form_page' => $form_page, 'modelform' => $modelform]);
    }

    /* страница подписания договора в качестве ИП */
    public function actionRegdogovorip($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_IP) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации ООО
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }
        $contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_IP])->orderBy(['version' => SORT_DESC])->one();
        if ($contract) {
            $reg_form->contract_id = $contract->id;
            $reg_form->updateAttributes(['contract_id' => $reg_form->contract_id]);
        }
        $contract_eduprog = false;
        $reg_form->license_contract_id = '';
        if ($user->organization->license_service) {
            if (Yii::$app->params['enable_dpo']) {
                $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_IP])->orderBy(['version' => SORT_DESC])->one();
                if ($contract_eduprog) {
                    $reg_form->license_contract_id = $contract_eduprog->id;
                }
            }
        }
        $reg_form->updateAttributes(['license_contract_id' => $reg_form->license_contract_id]);
        $form_page = PServiceRegIP::find()->where(['model' => PServiceRegIP::class, 'visible' => 1])->one();
        $modelform = new RegisterMarketPlace();
        $modelform->loadIP();
        $modelform->setScenario('contract_sign');

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'contract' => $contract, 'contract_eduprog' => $contract_eduprog, 'form_page' => $form_page, 'modelform' => $modelform]);
    }

    /* страница подписания договора в качестве ИП */
    public function actionRegdogovorselfbusy($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_SELFBUSY) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации ООО
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_IP :
                        // редирект на страницу ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        }
        $contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_SELFBUSY])->orderBy(['version' => SORT_DESC])->one();
        if ($contract) {
            $reg_form->contract_id = $contract->id;
            $reg_form->updateAttributes(['contract_id' => $reg_form->contract_id]);
        }
        $contract_eduprog = false;
        $reg_form->license_contract_id = '';
        if ($user->organization->license_service) {
            if (Yii::$app->params['enable_dpo']) {
                $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => Organization::TYPE_SELFBUSY])->orderBy(['version' => SORT_DESC])->one();
                if ($contract_eduprog) {
                    $reg_form->license_contract_id = $contract_eduprog->id;
                }
            }
        }
        $reg_form->updateAttributes(['license_contract_id' => $reg_form->license_contract_id]);
        $form_page = PServiceRegSelfbusy::find()->where(['model' => PServiceRegSelfbusy::class, 'visible' => 1])->one();
        $modelform = new RegisterMarketPlace();
        $modelform->loadSBusy();
        $modelform->setScenario('contract_sign');

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'contract' => $contract, 'contract_eduprog' => $contract_eduprog, 'form_page' => $form_page, 'modelform' => $modelform]);
    }

    /* страница регистрации на оказание лицензируемых услуг, если регистрация на МП уже есть */
    public function actionRegformlicense($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на страницу создания ДПО, если не зарегистрирован для оказания услуг - там полная инструкция должна быть */
        if (!$user->organization->can_service) {
            $eduprog_create_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
            return $this->redirect($eduprog_create_page->getUrlPath());
        }
        /* редирект на профиль, если уже зарегистрирован для оказания лицензируемых услуг */
        if ($user->organization->can_service && $user->organization->license_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $role = $user->role;
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form->status == Regservice::STATUS_MODERATE) {
            $moderate_page = PServiceModerateLicense::find()->where(['model' => PServiceModerateLicense::class, 'visible' => 1])->one();
            return $this->redirect($moderate_page->getUrlPath());
        }
        /* если существующей заявки по странным причинам не оказалось - создаем новую */
        if (!$reg_form) {
            $reg_form = new Regservice();
            $reg_form->type = $user->organization->type_mp;
            $reg_form->user_id = Yii::$app->user->id;
            $reg_form->status = Regservice::STATUS_NEW;
            $reg_form->visible = 0;

            /* заполняем договора, которые должны будут быть подписаны при успешной модерации заявки */
            $contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => $user->organization->type_mp])->orderBy(['version' => SORT_DESC])->one();
            if ($contract) {
                $reg_form->contract_id = $contract->id;
            }
        }
        $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => $user->organization->type_mp])->orderBy(['version' => SORT_DESC])->one();
        if ($contract_eduprog) {
            $reg_form->license_contract_id = $contract_eduprog->id;
        }

        $reg_form->save();

        $modelform = new RegisterMarketPlace();
        switch ($user->organization->type_mp) {
            case Organization::TYPE_OOO:
                $modelform->loadOOO();
                break;
            case Organization::TYPE_IP:
                $modelform->loadIP();
                break;
            case Organization::TYPE_SELFBUSY:
                $modelform->loadSBusy();
                break;
        }
        $modelform->setScenario('register_license');
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'user' => $user, 'contract_eduprog' => $contract_eduprog]);
    }

    /* страница успешной регистрации в качестве Юрлица */
    public function actionInfoooo($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_OOO) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации ИП
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        } else {
            // редирект на страницу регистрации услуг
            $start_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        /* если заявка еще/уже не на стадии модерации (либо еще не отправлена, либо отклонена, либо принята, хотя последний вариант вообще не должен до сюда дойти) */
        if ($reg_form->status != Regservice::STATUS_MODERATE) {
            // редирект на страницу регистрации услуг
            $start_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страница успешной регистрации в качестве ИП */
    public function actionInfoip($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_IP) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации ИП
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_SELFBUSY :
                        // редирект на страницу регистрации самозанятого
                        $regself_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                        return $this->redirect($regself_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        } else {
            // редирект на страницу регистрации услуг
            $start_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        /* если заявка еще/уже не на стадии модерации (либо еще не отправлена, либо отклонена, либо принята, хотя последний вариант вообще не должен до сюда дойти) */
        if ($reg_form->status != Regservice::STATUS_MODERATE) {
            // редирект на страницу регистрации услуг
            $start_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страница успешной регистрации в качестве самозанятого */
    public function actionInfoselfbusy($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        if ($user->organization->can_service) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* если существует заявка на регистрацию, то редирект на соответствующую страницу */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if ($reg_form) {
            if ($user->organization->type_mp != Organization::TYPE_SELFBUSY) {
                switch ($user->organization->type_mp) {
                    case Organization::TYPE_OOO :
                        // редирект на страницу регистрации ИП
                        $regooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                        return $this->redirect($regooo_page->getUrlPath());
                    case Organization::TYPE_IP :
                        // редирект на страницу регистрации самозанятого
                        $regip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                        return $this->redirect($regip_page->getUrlPath());
                    default:
                        // редирект на страницу профиля
                        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                        return $this->redirect($profile_page->getUrlPath());
                }
            }
        } else {
            // редирект на страницу регистрации услуг
            $start_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        /* если заявка еще/уже не на стадии модерации (либо еще не отправлена, либо отклонена, либо принята, хотя последний вариант вообще не должен до сюда дойти) */
        if ($reg_form->status != Regservice::STATUS_MODERATE) {
            // редирект на страницу регистрации услуг
            $start_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
            return $this->redirect($start_page->getUrlPath());
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страница успешной регистрации на оказание лицензируемых услуг */
    public function actionInfolicense($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* редирект на профиль, если уже зарегистрирован для оказания услуг */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();

        if (!$user->organization->can_service) {
            return $this->redirect($profile_page->getUrlPath());
        }

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только Экспертам, Академикам и может быть ЭО */
        if (!in_array($role, ['exporg'])) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если заявкит нет - то редирект на профиль */
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if (!$reg_form) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если заявка еще/уже не на стадии модерации */
        if ($reg_form->status != Regservice::STATUS_MODERATE) {
            return $this->redirect($profile_page->getUrlPath());
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionValidate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* Заявка на регистрацию услуг Юрлица */
        $modelform = new RegisterMarketPlace();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post())) {
            if ($modelform->license_service && empty($user->organization->license)) {
                if (empty(UploadedFile::getInstances($modelform, 'license')) && empty($modelform->license)) {
                    $modelform->addError('license', 'Необходимо загрузить файл с лицензией');
                }
            }
            switch ($modelform->type) {
                case Organization::TYPE_OOO:
                    $modelform->setScenario('regooo');
                    break;
                case Organization::TYPE_IP:
                    $modelform->setScenario('regip');
                    break;
                case Organization::TYPE_SELFBUSY:
                    $modelform->setScenario('regselfbusy');
                    break;
                default:
                    $modelform->addError('type', 'Тип регистрации неизвестен');
                    break;
            }
            return ActiveForm::validate($modelform);
        }

        return false;
    }

    public function actionValidateContract()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* Заявка на регистрацию услуг Юрлица */
        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('contract_sign');
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post())) {
            return ActiveForm::validate($modelform);
        }
        return false;
    }

    public function actionValidateLicense()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* Заявка на регистрацию услуг Юрлица */
        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('register_license');
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post())) {
            return ActiveForm::validate($modelform);
        }
        return false;
    }

    public function actionSaveooo()
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
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if (!$reg_form) {
            $reg_form = new Regservice();
            $reg_form->type = Organization::TYPE_OOO;
            $reg_form->user_id = Yii::$app->user->id;
            $reg_form->status = Regservice::STATUS_NEW;
            $reg_form->visible = 0;
        }

        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('regooo');

        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if ($modelform->license_service && empty($user->organization->license)) {
                if (empty(UploadedFile::getInstances($modelform, 'license'))) {
                    return [
                        'status' => 'fail',
                        'message' => 'Необходимо загрузить файл с лицензией',
                    ];
                }
            }

            // переносим данные в организацию
            $user->organization->type_mp = Organization::TYPE_OOO;
            $user->organization->organization_name = $modelform->organization_name;
            $user->organization->inn = $modelform->inn;

            $user->organization->ur_index = $modelform->ur_index;
            $user->organization->ur_region = $modelform->ur_region;
            $user->organization->ur_oblast = $modelform->ur_oblast;
            $user->organization->ur_city = $modelform->ur_city;
            $user->organization->ur_street = $modelform->ur_street;
            $user->organization->ur_house = $modelform->ur_house;
            $user->organization->ur_corpus = $modelform->ur_corpus;
            $user->organization->ur_room = $modelform->ur_room;

            $user->organization->match_post = $modelform->match_post;

            $user->organization->post_index = $modelform->post_index;
            $user->organization->post_region = $modelform->post_region;
            $user->organization->post_oblast = $modelform->post_oblast;
            $user->organization->post_city = $modelform->post_city;
            $user->organization->post_street = $modelform->post_street;
            $user->organization->post_house = $modelform->post_house;
            $user->organization->post_corpus = $modelform->post_corpus;
            $user->organization->post_room = $modelform->post_room;

            $user->organization->bank = $modelform->bank;
            $user->organization->raschet_account = $modelform->raschet_account;
            $user->organization->kor_account = $modelform->kor_account;
            $user->organization->bik = $modelform->bik;
            $user->organization->kpp_bank = $modelform->kpp_bank;
            $user->organization->nds = $modelform->nds;
            if ($user->role == 'exporg') {
                $user->organization->license_service = $modelform->license_service;
            }

            // сохраняем заявку и организацию
            if ($reg_form->save() && $user->save()) {
                // сохранить файлы собственноручной подписи и лицензий, если загружены
                $fileInstances = UploadedFile::getInstances($modelform, 'docs');

                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'docs';
                        $new_file_model->new_name = 'docs_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('docs', $new_file_model);
                    }
                }

                $fileInstances = UploadedFile::getInstances($modelform, 'license');
                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'license';
                        $new_file_model->new_name = 'license_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('license', $new_file_model);
                    }
                }

                // добавить согласия
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $exist_agreement = \app\modules\usersigns\models\Usersigns::find()->where([
                                'user_id' => Yii::$app->user->id,
                                'form_model' => $reg_form::class,
                                'form_id' => $reg_form->id,
                                'agreement_id' => $agree->id,
                            ])->one();
                            if (!$exist_agreement) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                                $agree_sign->form_model = $reg_form::class;
                                $agree_sign->form_id = $reg_form->id;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                }
                $dogovor_page = PServiceDogovorOOO::find()->where(['model' => PServiceDogovorOOO::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $dogovor_page->getUrlPath(),
                    'message' => 'Заявка сохранена',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                'errors' => $reg_form->getErrors(),
                'errors2' => $user->getErrors(),
                'errors3' => $user->organization->getErrors(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            'errors' => $modelform->getErrors(),
        ];
    }

    public function actionSaveip()
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
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if (!$reg_form) {
            $reg_form = new Regservice();
            $reg_form->type = Organization::TYPE_IP;
            $reg_form->user_id = Yii::$app->user->id;
            $reg_form->status = Regservice::STATUS_NEW;
            $reg_form->visible = 0;
        }

        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('regip');

        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if ($modelform->license_service && empty($user->organization->license)) {
                if (empty(UploadedFile::getInstances($modelform, 'license'))) {
                    return [
                        'status' => 'fail',
                        'message' => 'Необходимо загрузить файл с лицензией',
                    ];
                }
            }

            // переносим данные в организацию
            $user->organization->type_mp = Organization::TYPE_IP;

            $user->organization->surname = $modelform->surname;
            $user->organization->name = $modelform->name;
            $user->organization->patronymic = $modelform->patronymic;
            $user->organization->email = $modelform->email;
            $user->organization->phone = $modelform->phone;

            $user->organization->inn = $modelform->inn;

            $user->organization->ur_index = $modelform->ur_index;
            $user->organization->ur_region = $modelform->ur_region;
            $user->organization->ur_oblast = $modelform->ur_oblast;
            $user->organization->ur_city = $modelform->ur_city;
            $user->organization->ur_street = $modelform->ur_street;
            $user->organization->ur_house = $modelform->ur_house;
            $user->organization->ur_corpus = $modelform->ur_corpus;
            $user->organization->ur_room = $modelform->ur_room;

            $user->organization->match_post = $modelform->match_post;

            $user->organization->post_index = $modelform->post_index;
            $user->organization->post_region = $modelform->post_region;
            $user->organization->post_oblast = $modelform->post_oblast;
            $user->organization->post_city = $modelform->post_city;
            $user->organization->post_street = $modelform->post_street;
            $user->organization->post_house = $modelform->post_house;
            $user->organization->post_corpus = $modelform->post_corpus;
            $user->organization->post_room = $modelform->post_room;

            $user->organization->bank = $modelform->bank;
            $user->organization->raschet_account = $modelform->raschet_account;
            $user->organization->kor_account = $modelform->kor_account;
            $user->organization->bik = $modelform->bik;
            $user->organization->kpp_bank = $modelform->kpp_bank;
            $user->organization->nds = $modelform->nds;
            $user->organization->license_service = 0;

            // сохраняем заявку и организацию
            if ($reg_form->save() && $user->save()) {
                // сохранить файлы собственноручной подписи и лицензий, если загружены
                $fileInstances = UploadedFile::getInstances($modelform, 'docs');

                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'docs';
                        $new_file_model->new_name = 'docs_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('docs', $new_file_model);
                    }
                }

                $fileInstances = UploadedFile::getInstances($modelform, 'license');
                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'license';
                        $new_file_model->new_name = 'license_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('license', $new_file_model);
                    }
                }
                // добавить согласия
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $exist_agreement = \app\modules\usersigns\models\Usersigns::find()->where([
                                'user_id' => Yii::$app->user->id,
                                'form_model' => $reg_form::class,
                                'form_id' => $reg_form->id,
                                'agreement_id' => $agree->id,
                            ])->one();
                            if (!$exist_agreement) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                                $agree_sign->form_model = $reg_form::class;
                                $agree_sign->form_id = $reg_form->id;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                }
                /* страница подписания договора */
                $dogovor_page = PServiceDogovorIP::find()->where(['model' => PServiceDogovorIP::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $dogovor_page->getUrlPath(),
                    'message' => 'Заявка сохранена',
                ];
            }
        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionSaveselfbusy()
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

        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if (!$reg_form) {
            $reg_form = new Regservice();
            $reg_form->type = Organization::TYPE_SELFBUSY;
            $reg_form->user_id = Yii::$app->user->id;
            $reg_form->status = Regservice::STATUS_NEW;
            $reg_form->visible = 0;
        }

        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('regselfbusy');
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            if ($modelform->license_service && empty($user->organization->license)) {
                if (empty(UploadedFile::getInstances($modelform, 'license'))) {
                    return [
                        'status' => 'fail',
                        'message' => 'Необходимо загрузить файл с лицензией',
                    ];
                }
            }

            // переносим данные в организацию
            $user->organization->type_mp = Organization::TYPE_SELFBUSY;

            $user->organization->surname = $modelform->surname;
            $user->organization->name = $modelform->name;
            $user->organization->patronymic = $modelform->patronymic;
            $user->organization->email = $modelform->email;
            $user->organization->phone = $modelform->phone;

            $user->organization->inn = $modelform->inn;

            $user->organization->ur_index = $modelform->ur_index;
            $user->organization->ur_region = $modelform->ur_region;
            $user->organization->ur_oblast = $modelform->ur_oblast;
            $user->organization->ur_city = $modelform->ur_city;
            $user->organization->ur_street = $modelform->ur_street;
            $user->organization->ur_house = $modelform->ur_house;
            $user->organization->ur_corpus = $modelform->ur_corpus;
            $user->organization->ur_room = $modelform->ur_room;

            $user->organization->match_post = $modelform->match_post;

            $user->organization->post_index = $modelform->post_index;
            $user->organization->post_region = $modelform->post_region;
            $user->organization->post_oblast = $modelform->post_oblast;
            $user->organization->post_city = $modelform->post_city;
            $user->organization->post_street = $modelform->post_street;
            $user->organization->post_house = $modelform->post_house;
            $user->organization->post_corpus = $modelform->post_corpus;
            $user->organization->post_room = $modelform->post_room;

            $user->organization->bank = $modelform->bank;
            $user->organization->raschet_account = $modelform->raschet_account;
            $user->organization->kor_account = $modelform->kor_account;
            $user->organization->bik = $modelform->bik;
            $user->organization->kpp_bank = $modelform->kpp_bank;
            $user->organization->nds = $modelform->nds;
            $user->organization->license_service = 0;

            if ($reg_form->save() && $user->save()) {

                // сохранить файлы собственноручной подписи и лицензий, если загружены
                $fileInstances = UploadedFile::getInstances($modelform, 'docs');

                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'docs';
                        $new_file_model->new_name = 'docs_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('docs', $new_file_model);
                    }
                }

                $fileInstances = UploadedFile::getInstances($modelform, 'license');
                if (!empty($fileInstances)) {
                    foreach ($fileInstances as $key => $fileInstance) {
                        // проверить размер и расширение
                        if ($fileInstance->size > 5 * 1024 * 1024) {
                            return [
                                'status' => 'fail',
                                'message' => 'Максимальный размер файла - 5Мб.',
                            ];
                        }
                        // если проверки пройдены, сохраняем файл

                        $new_file_model = new FilestoreModel();
                        $new_file_model->file_path = 'files/upload/organization/';
                        $new_file_model->keeper_id = $user->organization->id;
                        $new_file_model->isMain = ($key == 0);
                        $new_file_model->order = $key;
                        $new_file_model->keeper_class = Organization::class;
                        $new_file_model->keeper_field = 'license';
                        $new_file_model->new_name = 'license_' . $key . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $user->organization->link('license', $new_file_model);
                    }
                }

                // добавить согласия
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $exist_agreement = \app\modules\usersigns\models\Usersigns::find()->where([
                                'user_id' => Yii::$app->user->id,
                                'form_model' => $reg_form::class,
                                'form_id' => $reg_form->id,
                                'agreement_id' => $agree->id,
                            ])->one();
                            if (!$exist_agreement) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                                $agree_sign->form_model = $reg_form::class;
                                $agree_sign->form_id = $reg_form->id;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                }
                /* страница подписания договора */
                $dogovor_page = PServiceDogovorSelfbusy::find()->where(['model' => PServiceDogovorSelfbusy::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => $dogovor_page->getUrlPath(),
                    'message' => 'Заявка сохранена',
                ];
            }
        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionSigncontract()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $contractform = new RegisterMarketPlace();
        $contractform->setScenario('contract_sign');
        if (Yii::$app->request->isAjax && $contractform->sanitize(Yii::$app->request->post()) && $contractform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            /* найти заявку */
            $modelform = Regservice::find()->where(['user_id' => $user->id])->one();
            if ($modelform) {
                if ($contractform->agree_contract) {
                    $modelform->agree_contract = 1;
                }
                if ($contractform->agree_license_contract) {
                    $modelform->agree_license_contract = 1;
                }
                $modelform->status = Regservice::STATUS_MODERATE;
                if (empty($modelform->contract_id)) {
                    $contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => $modelform->type])->orderBy(['version' => SORT_DESC])->one();
                    if ($contract) {
                        $modelform->contract_id = $contract->id;
                    }
                }
                if ($contractform->agree_license_contract && empty($modelform->license_contract_id)) {
                    $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => $modelform->type])->orderBy(['version' => SORT_DESC])->one();
                    if ($contract_eduprog) {
                        $modelform->license_contract_id = $contract_eduprog->id;
                    }
                }

                if (!empty($contractform->agrees)) {
                    foreach ($contractform->agrees as $agree) {
                        if ($contractform->dpo_agreements[$agree->id] == 1) {
                            $exist_agreement = \app\modules\usersigns\models\Usersigns::find()->where([
                                'user_id' => Yii::$app->user->id,
                                'form_model' => \app\modules\formagree\models\Formagree::TYPE_REGEDUPROG,
                                'form_id' => $modelform->id,
                                'agreement_id' => $agree->id,
                            ])->one();
                            if (!$exist_agreement) {
                                $agree_sign = new \app\modules\usersigns\models\Usersigns();
                                $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                                $agree_sign->form_model = \app\modules\formagree\models\Formagree::TYPE_REGEDUPROG;
                                $agree_sign->form_id = $modelform->id;
                                $agree_sign->agreement_id = $agree->id;
                                $agree_sign->comment = $agree->label_text;
                                $agree_sign->save();
                            }
                        }
                    }
                }

                if ($modelform->save()) {
                    // отправить уведомление админу
                    $modelform->sendModerateMail();
                    // создание/обновление компании для маркетплейса в Битрикс24
                    $modelform->updateBitrixModel();
                    // отправить пользователя на страницу успешной регистрации
                    switch ($modelform->type) {
                        case Organization::TYPE_OOO:
                            $moderate_page = PServiceModerateOOO::find()->where(['model' => PServiceModerateOOO::class, 'visible' => 1])->one();
                            break;
                        case Organization::TYPE_IP:
                            $moderate_page = PServiceModerateIP::find()->where(['model' => PServiceModerateIP::class, 'visible' => 1])->one();
                            break;
                        case Organization::TYPE_SELFBUSY:
                            $moderate_page = PServiceModerateSelfbusy::find()->where(['model' => PServiceModerateSelfbusy::class, 'visible' => 1])->one();
                            break;
                    }
                    return [
                        'status' => 'success',
                        'redirect_to' => $moderate_page->getUrlPath(),
                        'message' => 'Заявка сохранена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Ваша заявка не найдена. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка в запросе',
        ];

    }

    public function actionDeletequery()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $get = Yii::$app->request->get();
        if ($get['action'] == 'remove') {
            $user = Yii::$app->user->identity->userAR;
            /* найти заявку */
            $modelform = Regservice::find()->where(['user_id' => $user->id])->one();
            if ($modelform) {
                if ($modelform->delete()) {
                    return [
                        'status' => 'success',
                        'message' => 'Заявка сохранена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'При удалении заявки возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Ваша заявка не найдена. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка в запросе',
        ];

    }

    public function actionSavelicense()
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
        $reg_form = Regservice::find()->where(['user_id' => $user->id])->one();
        if (!$reg_form) {
            return [
                'status' => 'fail',
                'message' => 'Заявка на регистрацию не найдена. Обновите страницу',
            ];
        }

        $modelform = new RegisterMarketPlace();
        $modelform->setScenario('register_license');
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // проверяем наличие файлов лицензии
            $fileInstances = UploadedFile::getInstances($modelform, 'license');
            if (empty($user->organization->license) && empty($fileInstances)) {
                return [
                    'status' => 'fail',
                    'message' => 'Необходимо загрузить файл с лицензией',
                ];
            }
            // сохранить файлы лицензий, если загружены
            if (!empty($fileInstances)) {
                foreach ($fileInstances as $key => $fileInstance) {
                    // проверить размер и расширение
                    if ($fileInstance->size > 5 * 1024 * 1024) {
                        return [
                            'status' => 'fail',
                            'message' => 'Максимальный размер файла - 5Мб.',
                        ];
                    }
                    // если проверки пройдены, сохраняем файл
                    $new_file_model = new FilestoreModel();
                    $new_file_model->file_path = 'files/upload/organization/';
                    $new_file_model->keeper_id = $user->organization->id;
                    $new_file_model->isMain = ($key == 0);
                    $new_file_model->order = $key;
                    $new_file_model->keeper_class = Organization::class;
                    $new_file_model->keeper_field = 'license';
                    $new_file_model->new_name = 'license_' . $key . time() . rand(10, 99);
                    $new_file_model->file_loader = $fileInstance;
                    $new_file_model->description = '';
                    $res = $user->organization->link('license', $new_file_model);
                }
            }

            if ($modelform->agree_license_contract) {
                $reg_form->agree_license_contract = 1;
            }
            $reg_form->status = Regservice::STATUS_MODERATE;

            if ($reg_form->agree_license_contract && empty($reg_form->license_contract_id)) {
                $contract_eduprog = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => $reg_form->type])->orderBy(['version' => SORT_DESC])->one();
                if ($contract_eduprog) {
                    $reg_form->license_contract_id = $contract_eduprog->id;
                }
            }

            if (!empty($modelform->agrees)) {
                foreach ($modelform->agrees as $agree) {
                    if ($modelform->dpo_agreements[$agree->id] == 1) {
                        $exist_agreement = \app\modules\usersigns\models\Usersigns::find()->where([
                            'user_id' => Yii::$app->user->id,
                            'form_model' => \app\modules\formagree\models\Formagree::TYPE_REGEDUPROG,
                            'form_id' => $reg_form->id,
                            'agreement_id' => $agree->id,
                        ])->one();
                        if (!$exist_agreement) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                            $agree_sign->form_model = \app\modules\formagree\models\Formagree::TYPE_REGEDUPROG;
                            $agree_sign->form_id = $reg_form->id;
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
            }

            if ($reg_form->save()) {
                // отправить уведомление админу
                $reg_form->sendModerateMail();
                // создание/обновление компании для маркетплейса в Битрикс24
                $reg_form->updateBitrixModel();
                // отправить пользователя на страницу успешной регистрации
                $moderate_page = PServiceModerateLicense::find()->where(['model' => PServiceModerateLicense::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => ($moderate_page ? $moderate_page->getUrlPath() : '/'),
                    'message' => 'Заявка сохранена',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка в запросе',
        ];

    }

    public function actionRemoveooofiles()
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
        $post = Yii::$app->request->post();
        if (!empty($post['id'])) {
            $file = FilestoreModel::find()->where(['id' => $post['id'], 'keeper_id' => $post['keeper_id'], 'keeper_field' => 'docs', 'keeper_class' => Organization::class])->one();
            if ($file) {
                if ($user->id == $file->keeper->user_id) {
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
                    'message' => 'Невозможно удалить файл',
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

    public function actionRemoveooofileslicense()
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
        $post = Yii::$app->request->post();
        if (!empty($post['id'])) {
            $file = FilestoreModel::find()->where(['id' => $post['id'], 'keeper_id' => $post['keeper_id'], 'keeper_field' => 'license', 'keeper_class' => Organization::class])->one();
            if ($file) {
                if ($user->id == $file->keeper->user_id) {
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
                    'message' => 'Невозможно удалить файл',
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
}
