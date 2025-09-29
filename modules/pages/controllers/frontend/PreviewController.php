<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\modules\eduprog\models\Eduprog;
use Yii;

class PreviewController extends DeepController
{
    public function actionEduprog($id)
    {
        $model = Eduprog::findOne($id);

        // можем показывать предпросмотр
        return Yii::$app->runAction('pages/pages/eduproginner', ['model' => $model, 'preview' => true]);

    }
}
