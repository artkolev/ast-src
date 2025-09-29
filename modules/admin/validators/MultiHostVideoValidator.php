<?php

namespace app\modules\admin\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки ссылки на youtube, rutube, BK
 */
class MultiHostVideoValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $regexVk = '/(?:vk\.com\/video\-)[a-zA-Z0-9]+_[0-9]+/';
        $regexRutube = '/^(https?:\/\/(?:www\.)?)rutube\.ru(\/.*)?$/';
        $regexYouTube = "/^(?:(?:https|http):\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be).*(?<=\/|v\/|u\/|embed\/|shorts\/|watch\?v=)(?<!\/user\/)(?<id>[\w\-]{11})(?=\?|&|$)/";

        if (!empty($model->{$attribute})) {
            if (!preg_match($regexVk, $model->{$attribute}) && !preg_match($regexRutube, $model->{$attribute}) && !preg_match($regexYouTube, $model->{$attribute})) {
                $model->addError($attribute, 'Ссылка на видео содержит ошибки');
            }
        }
    }
}
