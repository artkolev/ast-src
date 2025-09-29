<?php

declare(strict_types = 1);

namespace app\modules\pages\controllers\backend;

use app\modules\admin\components\DeepAdminController;
use Yii;
use yii\helpers\Url;

class DefaultController extends DeepAdminController
{
    public $modelClass = '\app\modules\pages\models\Page';

    public function getButtons()
    {
        /* получаем GET-параметры */
        $get = Yii::$app->request->get();
        /* инициализируем список */
        $buttons = [];
        /* сканируем директорию с моделями модуля */
        $models = scandir(Yii::getAlias('@app/modules/pages/models/'));
        /* перебираем файлы в директории */
        foreach ($models as $model) {
            if (in_array($model, ['.', '..'])) {
                continue;
            }
            $models = explode('.', $model);
            $extension = end($models);
            if ($extension != 'php') {
                continue;
            }
            /* получаем класс модели */
            $model = str_replace('.php', '', $model);
            $className = '\app\modules\pages\models\\' . $model;
            /* если задано свойство name_for_list - идем дальше */
            if ($className::$name_for_list !== false) {
                /* если задано свойство count_for_list - проверяем */
                if ($className::$count_for_list !== false) {
                    /* находим количество уже созданных записей данного типа */
                    $countNow = $className::find()->where(['model' => trim($className, '\\')])->count();
                    /* если количество записей достигло предела - пропускаем */
                    if ($countNow >= $className::$count_for_list) {
                        continue;
                    }
                }
                /* добавляем модель в список кнопок */
                $buttons['add_' . $model] = [
                    /* класс кнопки (стиль) */
                    'class' => 'success',
                    /* заголовок кнопки */
                    'name' => 'Создать ' . $className::$name_for_list,
                    /* адрес кнопки */
                    'url' => Url::toRoute(['/admin/' . $this->module->id . '/' . $this->id . '/create', 'model' => $className, $model . '[parent_id]' => $get['parent_id']]),
                ];
            }
        }
        return $buttons;
    }

    public function actionIndex()
    {
        $this->title = 'Список страниц';
        $this->modelClass = \app\modules\pages\models\PageSearch::class;
        return parent::actionIndex();
    }

    public function actionCreate()
    {
        $get = Yii::$app->request->get();
        if (!empty($get['model'])) {
            $this->modelClass = $get['model'];
        }
        $this->title = 'Создание страницы';
        return parent::actionCreate();
    }

    public function actionUpdate($id, $checkVis = false)
    {
        $this->title = 'Редактирование страницы';
        $row = (new \yii\db\Query())->select(['model'])->from('pages')->where(['id' => $id])->one();
        if (empty($row)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        $this->modelClass = $row['model'];
        return parent::actionUpdate($id, $checkVis);
    }

    public function actionView($id, $checkVis = false)
    {
        $this->title = 'Просмотр страницы';
        $row = (new \yii\db\Query())->select(['model'])->from('pages')->where(['id' => $id])->one();
        if (empty($row)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        $this->modelClass = $row['model'];
        return parent::actionView($id, $checkVis);
    }

    public function actionDelete($id)
    {
        $this->title = 'Удаление страницы';
        $row = (new \yii\db\Query())->select(['model'])->from('pages')->where(['id' => $id])->one();
        if (empty($row)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        $this->modelClass = $row['model'];
        return parent::actionDelete($id);
    }
}
