<?php
/**
 * @modelDescr Страница с формой для ввода кода при активации аккаунта пользователя при регистрации в качестве Претендента (Физлицо + отметка "претендент", но на фронте называется Эксперт).
 */

namespace app\modules\pages\models;

class Regexpertactivateemail extends Page
{
    public static $name_for_list = "страницу подтверждения кода регистрации Эксперта";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'confirm_code';
    public $action_id = 'pages/register/confirm-code';

}
