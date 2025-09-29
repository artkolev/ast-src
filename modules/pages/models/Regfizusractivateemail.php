<?php
/**
 * @modelDescr Страница с формой для ввода кода при активации аккаунта пользователя при регистрации в качестве Физлица.
 */

namespace app\modules\pages\models;

class Regfizusractivateemail extends Page
{
    public static $name_for_list = "страницу подтверждения кода регистрации Физлица";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'confirm_code';
    public $action_id = 'pages/register/confirm-code';

}
