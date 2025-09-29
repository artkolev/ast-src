<?php

namespace app\modules\service\components;

use app\modules\service\models\Service;
use yii\web\UrlManager as YiiManager;

class UrlManager extends YiiManager
{
    /* обработка остатка адреса после точки входа в модуль Услуг */

    public static function parsePath($path, $model = null)
    {
        $model = Service::findShowInnerPage()->andWhere(['service.url' => $path[0], 'service.visible' => 1])->one();
        if ($model && (count($path) == 1)) {
            return ['pages/pages/serviceinner', ['model' => $model]];
        }
        return false;

    }
}
