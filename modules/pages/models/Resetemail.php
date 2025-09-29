<?php
/**
 * @modelDescr Страница Личного кабинета - на эту страницу происходит переход из письма, для подтверждения нового адреса email для профиля.
 */

namespace app\modules\pages\models;

class Resetemail extends Page
{
    public static $name_for_list = "страницу Смены email";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'setnewmail';
    public $action_id = 'pages/profile/setnewmail';

}
