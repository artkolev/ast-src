<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\DeepAdminController;

class DefaultController extends DeepAdminController
{
    /* главная страница админки */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFiles()
    {
        return $this->render('files');
    }
}
