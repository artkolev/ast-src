<?php
/**
 * @modelDescr Страница сообщения об успешной активации аккаунта, скорее всего устарела. Проверить и удалить
 */

namespace app\modules\pages\models;

class Regurusrsuccess extends Page
{
    public static $name_for_list = "страницу Успешной регистрации Юрлица";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'reg_success';
    public $action_id = 'pages/register/regsuccess';

}
