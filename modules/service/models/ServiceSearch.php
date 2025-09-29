<?php

namespace app\modules\service\models;

use app\helpers\Grid;
use app\modules\users\models\UserAR;
use yii\data\ActiveDataProvider;

class ServiceSearch extends Service
{
    public $author_name; // для поиска по автору

    public static function getColumns()
    {
        return [
            ['class' => 'yii\grid\CheckboxColumn'],
            Grid::editField('name'),
            [
                'attribute' => 'author_name',
                'format' => 'raw',
                'value' => (function ($model, $key, $index, $column) {
                    return $model->user->profile->halfname;
                }),
            ],
            Grid::editTextField('order'),
            Grid::editSwitchField('visible'),
            Grid::viewLink(),
            Grid::statDatetimeField('created_at'),
            Grid::statDatetimeField('updated_at'),
            ['class' => 'yii\grid\ActionColumn'],
        ];
    }

    public function search()
    {
        $query = parent::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                    'order' => SORT_ASC,
                ]
            ],
        ]);

        // если введенные данные некорректны, выводим общий список
        $query->andFilterWhere([
            'order' => $this->order,
            'visible' => $this->visible,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        if (!empty($this->author_name)) {
            // найти пользователей с подходящим ФИО
            $experts_ids = UserAR::find()
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->where(
                    ['or',
                        ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`))', $this->author_name],
                        ['LIKE', 'LCASE(CONCAT(`profile`.`name`," ",`profile`.`surname`))', $this->author_name],
                        ['LIKE', 'profile.organization_name', $this->author_name],
                    ]
                )->select('user.id')->asArray()->column();

            $query->andFilterWhere(['IN', 'user_id', $experts_ids]);
        }

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'author_name' => 'Автор',
        ]);
    }

    // сохранение/удаление/валидация

    public function rules()
    {
        return [
            [['name', 'description', 'price', 'author_name', 'visible', 'order'], 'safe'],
        ];
    }
}
