<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\modules\pages\models\Questionnairepage;
use app\modules\questionnaire\models\Questionnaire;
use app\modules\questionnaire\models\QuestionnaireResults;
use Yii;
use yii\web\Response;

class QuestionnairepageController extends DeepController
{
    public function actionIndex($model)
    {
        if ($model instanceof Questionnairepage) {
            return $this->redirect('/');
        }

        $this->layout = '@app/views/layouts/clean';
        return $this->render($model->view, ['model' => $model]);

    }

    public function actionResult($model)
    {
        if ($model instanceof Questionnairepage) {
            return $this->redirect('/');
        }

        $this->layout = '@app/views/layouts/clean';
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionSaveform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $questionnaireData = Yii::$app->request->post('QuestionnaireResults');
        $email = $questionnaireData['email'];
        $questionnaire_id = $questionnaireData['questionnaire_id'];

        if (!empty($email) && !empty($questionnaire_id)) {
            $modelform = QuestionnaireResults::find()
                ->where(['LOWER(email)' => strtolower($email), 'questionnaire_id' => $questionnaire_id])
                ->one();

            if (!$modelform) {
                $modelform = new QuestionnaireResults();
            }

            $modelform->referer_url = Yii::$app->request->referrer;
            if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->save()) {
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                            $agree_sign->form_model = Questionnaire::class;
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }
                if ($modelform->questionnaire->send_letter) {
                    \app\helpers\Mail::sendMail(
                        $modelform,
                        $modelform->email,
                        '@app/modules/questionnaire/mails/letter',
                        $modelform->questionnaire->letter_theme ?: 'Результаты теста',
                        base_layout: '@app/mail/layouts/html_questionnaire',
                    );
                }

                return [
                    'status' => 'success',
                    'message' => 'Вы успешно подписались на рассылку',
                    'redirect' => $modelform->questionnaire->success_redirect ? $modelform->getUrlPath() : null,
                ];
            }
        }


        return [
            'status' => 'fail',
        ];
    }
}
