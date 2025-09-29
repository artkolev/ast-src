<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница со списком мест работы пользователя (Карьера) и возможностью их редактировать
 */

namespace app\modules\pages\models;

class ProfileCareer extends Page
{
    public static $name_for_list = "Карьеру пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_career';
    public $action_id = 'pages/profile/career';

}
