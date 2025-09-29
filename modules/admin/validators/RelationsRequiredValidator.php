<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки наличия связанных данных
 */
class RelationsRequiredValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!$model->{$attribute} || (is_array($model->{$attribute}) && !count($model->{$attribute}))) {
            $model->addError($attribute, 'Необходимо заполнить «' . $model->getAttributeLabel($attribute) . '».');
        }
    }
}
