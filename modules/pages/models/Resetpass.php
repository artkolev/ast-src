<?php
/**
 * @modelDescr Страница Восстановления пароля. Восстановление пароля идет в 3 шага. Для каждого шага свой view.
 */

namespace app\modules\pages\models;

class Resetpass extends Page
{
    public static $name_for_list = "страницу Восстановления пароля";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'setnewpass';
    public $action_id = 'pages/register/setnewpass';

}
