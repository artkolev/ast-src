<?php

/**
 *
 * Модель наследуется от yii\web\User и добавляет генерацию auth_key при логине пользователя, если до этого auth_key не был заполнен.
 *
 */

namespace app\modules\admin\models;

class WebUser extends \yii\web\User
{
    protected function beforeLogin($identity, $cookieBased, $duration)
    {
        if (empty($identity->auth_key)) {
            $identity->generateAuthKey();
        }
        return parent::beforeLogin($identity, $cookieBased, $duration);
    }
}
