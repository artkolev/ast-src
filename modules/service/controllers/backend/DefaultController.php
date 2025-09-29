<?php

namespace app\modules\service\controllers\backend;

use app\modules\admin\components\DeepAdminController;
use yii\helpers\Url;

class DefaultController extends DeepAdminController
{
    public $modelClass = '\app\modules\service\models\Service';

    public function getButtons()
    {
        return [
            'add_page' => [
                'class' => 'success',
                'name' => 'Создать услугу',
                'url' => Url::toRoute(['/admin/' . $this->module->id . '/' . $this->id . '/create']),
            ],
        ];
    }

    public function actionIndex()
    {
        $this->title = 'Список услуг';
        $this->modelClass = \app\modules\service\models\ServiceSearch::class;
        return parent::actionIndex();
    }

    public function actionCreate()
    {
        $this->title = 'Создание услуги';
        return parent::actionCreate();
    }

    public function actionUpdate($id, $checkVis = false)
    {
        $this->title = 'Редактирование услуги';
        return parent::actionUpdate($id, $checkVis);
    }

    public function actionView($id, $checkVis = false)
    {
        $this->title = 'Просмотр услуги';
        return parent::actionView($id, $checkVis);
    }

    /*
    public function actionDelete($id)
    {
        $this->title = 'Удаление услуги';
        return parent::actionDelete($id);
    }
    */
}
