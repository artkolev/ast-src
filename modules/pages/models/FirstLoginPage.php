<?php
/**
 * @modelDescr Страница для активации аккаунта, созданного из админки.
 */

namespace app\modules\pages\models;

class FirstLoginPage extends Page
{
    public static $name_for_list = "страницу Первого входа";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'firstlogin';
    public $action_id = 'pages/register/firstlogin';

}
