<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки количества элементов в массиве. Используется для проверки количества связанных моделей.
 */
class LimitArrayValidator extends Validator
{
    public $limit; // передаваемый лимит

    public function validateAttribute($model, $attribute)
    {
        if ($model->{$attribute} && count($model->{$attribute}) > $this->limit) {
            $model->addError($attribute, "Количество не может превышать {$this->limit}.");
        }
    }
}
