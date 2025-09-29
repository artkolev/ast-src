<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки количества символов в тексте, без учета HTML тегов
 */
class TextLengthValidator extends Validator
{
    public $max; // Максимальное количество символов
    public $min; // Минимальное количество символов

    public function validateAttribute($model, $attribute)
    {
        $text = trim(html_entity_decode(strip_tags($model->{$attribute})));
        $text = preg_replace('(\s+)', ' ', $text);
        if ($this->max && grapheme_strlen($text) > (int)$this->max) {
            $model->addError($attribute, 'Значение «' . $model->getAttributeLabel($attribute) . '» должно содержать не более ' . (int)$this->max . ' символов.');
        }
        if ($this->min && grapheme_strlen($text) < (int)$this->min) {
            $model->addError($attribute, 'Значение «' . $model->getAttributeLabel($attribute) . '» должно содержать не менее ' . (int)$this->min . ' символов.');
        }
    }
}
