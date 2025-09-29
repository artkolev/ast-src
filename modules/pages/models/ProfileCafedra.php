<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница первого шага заявки на присоединение к академии (Претендент - Эксперт) с выбором основной кафедры и возможностью оставить заявку на создание новой кафедры.
 */

namespace app\modules\pages\models;

class ProfileCafedra extends Page
{
    public static $name_for_list = "заполнения Кафедры претендентом";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_cafedra';
    public $action_id = 'pages/profile/cafedra';

}
