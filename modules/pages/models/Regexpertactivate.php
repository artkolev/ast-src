<?php
/**
 * @modelDescr Страница Активации аккаунта по ссылке из письма / админки для пользователя с ролью Эксперт. Функционал был заменён на подтверждение аккаунта отправкой 6-значного кода. Страница осталась в качестве поддержки активации по ссылке из Админки
 */

namespace app\modules\pages\models;

class Regexpertactivate extends Page
{
    public static $name_for_list = "страницу Подтверждения email Эксперта";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'reg_activate';
    public $action_id = 'pages/register/activate';

}
