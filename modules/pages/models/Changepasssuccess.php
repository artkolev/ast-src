<?php
/**
 * @modelDescr Страница с текстом об успешной смене пароля (при восстановлении пароля). Устарела.
 */

namespace app\modules\pages\models;

class Changepasssuccess extends Page
{
    public static $name_for_list = "страницу Успешной смены пароля";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'changepass_success';
    public $action_id = 'pages/register/changepasssuccess';

}
