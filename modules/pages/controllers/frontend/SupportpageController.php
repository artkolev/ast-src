<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\models\SupportForm;
use app\modules\pages\models\SupportPage;
use app\modules\supportresult\models\Supportresult;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

class SupportpageController extends DeepController
{
    public function actionIndex($model)
    {
        $this->setMeta($model);
        $parent = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();
        $modelform = new SupportForm();
        return $this->render($model->view, ['model' => $model, 'parent' => $parent, 'modelform' => $modelform]);
    }

    public function actionSaveform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelform = new SupportForm();
        $modelform->image = UploadedFile::getInstances($modelform, 'image');
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // сохранить результат формы в модуль результатов
            $result_form = new Supportresult();

            if (!empty($modelform->image)) {
                $result_form->image_loader = $modelform->image;
            }

            if (Yii::$app->user->isGuest) {
                $result_form->name = $modelform->name;
                $result_form->surname = $modelform->surname;
                $result_form->email = $modelform->email;
                $result_form->phone = $modelform->phone;
            } else {
                $result_form->name = Yii::$app->user->identity->userAR->profile->name;
                $result_form->surname = Yii::$app->user->identity->userAR->profile->surname;
                $result_form->email = Yii::$app->user->identity->userAR->email;
                $result_form->phone = Yii::$app->user->identity->userAR->profile->phone;
            }
            $result_form->theme = $modelform->theme;
            $result_form->message = $modelform->message;
            $result_form->user_id = (Yii::$app->user->isGuest ? 0 : Yii::$app->user->id);

            if ($result_form->save()) {
                // сохранить согласия
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = (Yii::$app->user->isGuest ? '' : Yii::$app->user->id);
                            $agree_sign->form_model = $result_form::class;
                            $agree_sign->form_id = $result_form->id;
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }

                return [
                    'status' => 'success',
                    'result_id' => $result_form->id,
                    'message' => 'Сообщение отправлено',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно отправить данные. ' . \app\helpers\MainHelper::getHelpText(),
                'error' => $result_form->getErrors(),
            ];

        }
        return [
            'status' => 'fail',
            'error' => $modelform->getErrors(),
        ];
    }
}
