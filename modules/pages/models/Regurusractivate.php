<?php
/**
 * @modelDescr Страница Активации аккаунта по ссылке из письма / админки для пользователя с ролью Юрлицо. Функционал был заменён на подтверждение аккаунта отправкой 6-значного кода. Страница осталась в качестве поддержки активации по ссылке из Админки
 */

namespace app\modules\pages\models;

class Regurusractivate extends Page
{
    public static $name_for_list = "страницу Подтверждения email Юрлица";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'reg_activate';
    public $action_id = 'pages/register/activate';

}
