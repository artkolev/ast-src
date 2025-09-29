<?php

namespace app\modules\service;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['name'] = 'Услуги';
        $this->params['models_for_sitemap'] = [
            \app\modules\service\models\Service::class,
        ];
    }
}
