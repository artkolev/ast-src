<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница со списком мест обучения пользователя (Образование) и возможностью их редактировать
 */

namespace app\modules\pages\models;

class ProfileEducation extends Page
{
    public static $name_for_list = "Образование пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_education';
    public $action_id = 'pages/profile/education';

}
