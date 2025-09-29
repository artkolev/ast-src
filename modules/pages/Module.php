<?php

namespace app\modules\pages;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['name'] = 'Страницы сайта';
    }
}
