<?php

namespace app\modules\pages\models;

use app\helpers\Grid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

class PageSearch extends Page
{
    public static $name_for_list = false;
    public $child_row;

    public static function getColumns()
    {
        return [
            ['class' => 'yii\grid\CheckboxColumn'],
            Grid::parentField('child_row'),
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'name',
                'format' => 'raw',
                'value' => (function ($model, $key, $index, $column) {
                    $attribute = $column->attribute;
                    return Html::a(Html::encode($model->{$attribute}), Url::toRoute(['/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update', 'id' => $model->id]));
                }),
            ],
            Grid::editSectionField('parent_id'),
            Grid::editTextField('model'),
            Grid::editTextField('url'),
            Grid::editTextField('order'),
            Grid::editSwitchField('visible'),
            Grid::viewLink(),
            ['class' => 'yii\grid\ActionColumn'],
        ];
    }

    public function behaviors()
    {
        $orig_behaviors = parent::behaviors();
        unset($orig_behaviors['urlBehaviour']);
        return $orig_behaviors;
    }

    public function search()
    {
        $get = Yii::$app->request->get();
        $query = parent::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'order' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        // если введенные данные некорректны, выводим общий список
        if (!$this->validate()) {
            return $dataProvider;
        }
        if (isset($get['parent_id']) || !isset($this->model)) {
            $query->andFilterWhere([
                'parent_id' => (isset($get['parent_id']) ? $get['parent_id'] : 0),
            ]);
        }
        $query->andFilterWhere(['LIKE', 'name', $this->name]);
        $query->andFilterWhere(['LIKE', 'url', $this->url]);
        $query->andFilterWhere(['visible' => $this->visible]);
        $query->andFilterWhere(['order' => $this->order]);
        $query->andFilterWhere(['LIKE', 'model', $this->model]);
        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'child_row' => '',
        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return [
            [['name', 'content', 'url', 'order', 'visible', 'model'], 'safe'],
        ];
    }
}
