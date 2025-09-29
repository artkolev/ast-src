<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки ссылки telegram
 */
class TelegramValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/^(@|https\:\/\/t\.me\/)(?!\d)(?:(?![@#])[\w])+$/', $model->{$attribute})) {
            $model->addError($attribute, 'Ссылка на Telegram должна начинаться с «https://t.me/» или с «@»');
        }
    }
}
