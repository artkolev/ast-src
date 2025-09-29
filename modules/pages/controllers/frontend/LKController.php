<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\modules\contract\models\Contract;
use app\modules\contract\models\ContractEduprog;
use app\modules\contractitem\models\Contractitem;
use app\modules\formagree\models\Formagree;
use app\modules\pages\models\SignAgree;
use app\modules\pages\models\SignContractMarket;
use app\modules\users\models\Organization;
use app\modules\usersigns\models\Usersigns;
use Yii;

class LKController extends DeepController
{
    // проверка для всех страниц ЛК кроме исключительных (согласия с условиями)
    public function beforeAction($action)
    {
        $parent_result = parent::beforeAction($action);
        // получить роль пользователя
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $user = Yii::$app->user->identity->userAR;
        $role_ref_type = [
            'expert' => Formagree::TYPE_REGEXPERT,
            'exporg' => Formagree::TYPE_REGEXPORG,
            'urusr' => Formagree::TYPE_REGURUSR,
            'fizusr' => Formagree::TYPE_REGFIZUSR,
        ];
        $form_type = [$role_ref_type[$role]];

        if (
            ($user->role == 'exporg') &&
            ($user->organization->type_mp == Organization::TYPE_OOO) &&
            ($user->organization->license_service)
        ) {
            $form_type[] = \app\modules\formagree\models\Formagree::TYPE_REGEDUPROG;
        }

        // найти соглашения для роли
        $agrees = Formagree::find()->where(['visible' => 1])->andWhere(['IN', 'form_type', $form_type])->orderBy(['order' => SORT_ASC])->all();
        if (Yii::$app->request->get('greeting') == "OK") {
            $user->greeting = 0;
            $user->updateAttributes(['greeting' => $user->greeting]);
        }
        // если по всем соглашениям есть согласие - пропускаем, иначе редирект на страницу подписания соглашений.
        $show_greet = false;
        if ($user->role == 'expert' && ($user->greeting)) {
            $show_greet = true;
        } else {
            foreach ($agrees as $agree) {
                $usersign = Usersigns::find()->where(['user_id' => $user->id, 'form_model' => $form_type, 'agreement_id' => $agree->id])->one();
                if (!$usersign) {
                    // нет требуемого согласия.
                    $show_greet = true;
                }
            }
        }
        if ($show_greet) {
            $agree_page = SignAgree::find()->where(['model' => SignAgree::class, 'visible' => 1])->one();
            $this->redirect($agree_page->getUrlPath());
        }

        // проверка подписанных договоров на оказание услуг
        // если пользователь имеет разрешение на оказание услуг
        if ($user->organization->can_service) {
            // найти последнюю версию договора для выбранного пользователем типа регистрации
            $actual_contract = Contract::findByModel()->andWhere(['visible' => 1, 'type' => $user->organization->type_mp])->orderBy(['version' => SORT_DESC])->one();
            if (empty($actual_contract)) {
                // не нашли договор - ничего не делаем
                return $parent_result;
            }
            // найти подписанный, действующий договор пользователя
            $user_contract = Contractitem::find()->where(['user_id' => $user->id, 'visible' => 1, 'contract_id' => $actual_contract->id])->one();
            if (empty($user_contract)) {
                // действующий договор не подписан пользователем - требуется подписать
                $contract_page = SignContractMarket::find()->where(['model' => SignContractMarket::class, 'visible' => 1])->one();
                if ($contract_page) {
                    $this->redirect($contract_page->getUrlPath());
                } else {
                    // нет страницы подписания нового договора - выкинуть из ЛК, так админам точно доложат.
                    $this->redirect('/');
                }
            }
            if (($user->role == 'exporg') && ($user->organization->type_mp == Organization::TYPE_OOO) && $user->organization->license_service) {
                if (Yii::$app->params['enable_dpo']) {
                    // найти последнюю версию договора для выбранного пользователем типа регистрации
                    $actual_contract = ContractEduprog::findByModel()->andWhere(['visible' => 1, 'type' => $user->organization->type_mp])->orderBy(['version' => SORT_DESC])->one();
                    if (empty($actual_contract)) {
                        // не нашли договор - ничего не делаем
                        return $parent_result;
                    }
                    // найти подписанный, действующий договор пользователя
                    $user_contract = Contractitem::find()->where(['user_id' => $user->id, 'visible' => 1, 'contract_id' => $actual_contract->id])->one();
                    if (empty($user_contract)) {
                        // действующий договор не подписан пользователем - требуется подписать
                        $contract_page = SignContractMarket::find()->where(['model' => SignContractMarket::class, 'visible' => 1])->one();
                        if ($contract_page) {
                            $this->redirect($contract_page->getUrlPath());
                        } else {
                            // нет страницы подписания нового договора - выкинуть из ЛК, так админам точно доложат.
                            $this->redirect('/');
                        }
                    }
                }
            }
        }
        return $parent_result;
    }
}
