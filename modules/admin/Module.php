<?php

namespace app\modules\admin;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['name'] = 'Панель управления';

        /* Не учитывать в документации */
        $this->params['hide_in_docs'] = 1;
    }
}
