<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки количества символов в теге
 */
class TagLengthValidator extends Validator
{
    public $max; // Максимальное количество символов
    public $min; // Минимальное количество символов

    public function validateAttribute($model, $attribute)
    {
        if (!empty($model->{$attribute})) {
            foreach ($model->{$attribute} as $item) {
                if ($this->max && grapheme_strlen($item) > (int)$this->max) {
                    $model->addError($attribute, 'Значение тега «' . $item . '» должно содержать не более ' . (int)$this->max . ' символов.');
                }
                if ($this->min && grapheme_strlen($item) < (int)$this->min) {
                    $model->addError($attribute, 'Значение тега «' . $item . '» должно содержать не более ' . (int)$this->min . ' символов.');
                }
            }
        }
    }
}
