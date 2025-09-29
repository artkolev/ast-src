<?php
/**
 * @modelDescr Страница Личного кабинета - Содержит список программ ДПО, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class LKEduprogList extends Page
{
    public static $name_for_list = "страницу Списка программ ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_list';
    public $action_id = 'pages/eduprog/eduproglist';

}
