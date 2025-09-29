<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\models\AgreementsForm;
use app\models\ContractsForm;
use app\modules\contractitem\models\Contractitem;
use app\modules\pages\models\Home;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\ServiceDocs;
use Yii;
use yii\web\Response;

class IsleController extends DeepController
{
    /* страница создания новости */
    public function actionAgreements($model)
    {
        /* редирект на логин, если неавторизован */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();

        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
            $main_page = Home::find()->where(['model' => Home::class, 'visible' => 1])->one();
            return $this->redirect($main_page->getUrlPath());
        }
        // обратная проверка - если не осталось неподтвержденных соглашений - редирект на профиль
        $modelform = new AgreementsForm();
        // если нет неотмеченных галочек и если пользователь эксперт заходит не первый раз и он не только что стал экспертом
        if (empty($modelform->getAgrees()) and !(($user->role == 'expert') and ($user->greeting))) {
            return $this->redirect($profile_page->getUrlPath());
        }
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform, 'profile_page' => $profile_page, 'user' => $user]);
    }

    /* страница подписания нового договора */
    public function actionContracts($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ, подписавшим договор на оказание услуг */
        if (!$user->organization->can_service) {
            return $this->redirect($profile_page->getUrlPath());
        }
        // обратная проверка - если текущий договор подписан - редирект на профиль

        $modelform = new ContractsForm();
        // если нет не подписанных договоров
        if (empty($modelform->getContractList())) {
            return $this->redirect($profile_page->getUrlPath());
        }
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    public function actionSignnextcontract()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }


        $modelform = new ContractsForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            /* создаем новый договор для пользователя (старый пока не деактивируем, ждем решение АСТ) */
            $user = Yii::$app->user->identity->userAR;
            /* найти договоры */
            $actual_contracts = $modelform->getContractList();
            if (!empty($actual_contracts)) {
                foreach ($actual_contracts as $contract_item) {
                    if (in_array($contract_item->id, $modelform->contracts)) {
                        /* подписать новый договор */
                        $contract = new Contractitem();
                        $contract->name = 'Договор';
                        $contract->user_id = $user->id;
                        $contract->contract_id = $contract_item->id;
                        $contract->version = $contract_item->version;
                        $contract->type = $contract_item->type;
                        $contract->content = $contract_item->content;
                        $contract->contract_name = $contract_item->name;
                        $contract->date = date('d.m.Y');
                        $contract->visible = 1;
                        if ($contract->save()) {
                            $contract->number = $contract_item->prefix . str_pad($contract->id, 7, "0", STR_PAD_LEFT) . date('y');
                            $contract->name = 'Договор №' . $contract->number . ' от ' . date('d.m.Y');
                            $contract->updateAttributes(['number' => $contract->number, 'name' => $contract->name]);
                        } else {
                            return [
                                'status' => 'fail',
                                'message' => 'Невозможно подписать договор. ' . \app\helpers\MainHelper::getHelpText(),
                            ];
                        }
                    }
                }
                $contracts_page = ServiceDocs::find()->where(['model' => ServiceDocs::class, 'visible' => 1])->one();
                return [
                    'status' => 'success',
                    'redirect_to' => (empty($contracts_page) ? '/' : $contracts_page->getUrlPath()),
                    'message' => 'Договор подписан',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Требуемые договора уже подписаны. Обновите страницу.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка в запросе',
        ];

    }
}
