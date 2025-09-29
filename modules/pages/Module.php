<?php

declare(strict_types = 1);

namespace app\modules\pages;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init()
    {
        parent::init();

        $this->params['name'] = 'Страницы сайта';
    }
}
