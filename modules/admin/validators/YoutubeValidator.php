<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки ссылки на youtube
 */
class YoutubeValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $regex = "/^(?:(?:https|http):\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be).*(?<=\/|v\/|u\/|embed\/|shorts\/|watch\?v=)(?<!\/user\/)(?<id>[\w\-]{11})(?=\?|&|$)/";
        if (!empty($model->{$attribute}) && !preg_match($regex, $model->{$attribute})) {
            $model->addError($attribute, 'Ссылка на youtube содержит ошибки');
        }
    }
}
