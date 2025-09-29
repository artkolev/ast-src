<?php

namespace app\modules\admin\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

class SaveOneVarRelation extends Behavior
{
    public $relations = [];
    public $modelVarClass = '\app\modules\admin\components\VarstoreModel';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',

            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',

            BaseActiveRecord::EVENT_AFTER_REFRESH => 'afterFind',

            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function afterFind()
    {
        $keeper_class = $this->owner::class;
        $keeper_id = $this->owner->id;
        foreach ($this->relations as $reltype => $relstack) {
            // вместо AR используем DAO для скорости
            $vars = (new \yii\db\Query())
                ->select(['keeper_field', 'value'])
                ->from($this->modelVarClass::tableName())
                ->where(['in', 'keeper_field', $relstack])
                ->andWhere(['keeper_class' => $keeper_class, 'keeper_id' => $keeper_id])
                ->all();
            // разворачиваем в удобный массив
            if ($reltype == 'single') {
                $vars_pair = ArrayHelper::map($vars, 'keeper_field', 'value');
            } elseif ($reltype == 'multiple') {
                $vars_pair = ArrayHelper::map($vars, 'value', 'value', 'keeper_field');
            }
            // присваиваем полям модели
            foreach ($vars_pair as $name => $value) {
                $this->owner->{$name} = $value;
            }
        }
    }

    public function afterSave()
    {
        $keeper_class = $this->owner::class;
        $keeper_id = $this->owner->id;
        foreach ($this->relations as $reltype => $relstack) {
            if ($reltype == 'single') {
                foreach ($relstack as $keeper_field) {
                    $var = $this->modelVarClass::find()->where(['keeper_class' => $keeper_class, 'keeper_field' => $keeper_field, 'keeper_id' => $keeper_id])->one();
                    if (!$var) {
                        $var = new $this->modelVarClass();
                        $var->keeper_class = $keeper_class;
                        $var->keeper_field = $keeper_field;
                        $var->keeper_id = $keeper_id;
                    }
                    $var->value = $this->owner->{$keeper_field};
                    $var->save();
                }
            } elseif ($reltype == 'multiple') {
                foreach ($relstack as $keeper_field) {
                    $vars = $this->modelVarClass::find()->where(['keeper_class' => $keeper_class, 'keeper_field' => $keeper_field, 'keeper_id' => $keeper_id])->all();
                    $actual_list = $this->owner->{$keeper_field};
                    foreach ($vars as $var_rec) {
                        $key = array_search($var_rec->value, $actual_list);
                        if ($key !== false) {
                            // удалить из списка значения, которые есть в базе
                            unset($actual_list[$key]);
                        } else {
                            // удалить записи, значений которых нет в новом списке
                            $var_rec->delete();
                        }
                    }
                    // для оставшихся в списке элементов создать новые записи
                    if (!empty($actual_list)) {
                        foreach ($actual_list as $value) {
                            $var = new $this->modelVarClass();
                            $var->keeper_class = $keeper_class;
                            $var->keeper_field = $keeper_field;
                            $var->keeper_id = $keeper_id;
                            $var->value = $value;
                            $var->save();
                        }
                    }
                    $vars = $this->modelVarClass::find()->where(['keeper_class' => $keeper_class, 'keeper_field' => $keeper_field, 'keeper_id' => $keeper_id])->all();
                    if ($vars) {
                        $this->owner->{$keeper_field} = ArrayHelper::map($vars, 'value', 'value');
                    }
                }
            }
        }
    }

    public function beforeDelete()
    {
        $single_stack = !empty($this->relations['single']) ? $this->relations['single'] : [];
        $multiple_stack = !empty($this->relations['multiple']) ? $this->relations['multiple'] : [];
        $relstack = array_merge($single_stack, $multiple_stack);

        $keeper_class = $this->owner::class;
        $keeper_id = $this->owner->id;

        $vars = $this->modelVarClass::find()
            ->where(['in', 'keeper_field', $relstack])
            ->andWhere(['keeper_class' => $keeper_class, 'keeper_id' => $keeper_id])
            ->all();

        foreach ($vars as $var) {
            $var->delete();
        }
    }
}
