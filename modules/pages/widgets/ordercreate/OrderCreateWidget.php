<?php

namespace app\modules\pages\widgets\ordercreate;

use app\models\Orders;
use Yii;
use yii\base\Widget;

class OrderCreateWidget extends Widget
{
    public function run()
    {
        $model = new Orders();
        if (!Yii::$app->user->isGuest) {
            $profile = Yii::$app->user->identity?->userAR->profile ?? null;
            $model->loadFromUserProfile($profile);
        }
        return $this->render('ordercreate', ['model' => $model]);
    }
}
