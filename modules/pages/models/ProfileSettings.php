<?php
/**
 * @modelDescr Страница Личного Кабинета
 */

namespace app\modules\pages\models;

class ProfileSettings extends Page
{
    public static $name_for_list = "Настройки";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_settings';
    public $action_id = 'pages/profile/settings';

}
