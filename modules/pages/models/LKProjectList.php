<?php
/**
 * @modelDescr Страница Личного кабинета - Страница со списком Проектов, часть модуля lenta, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class LKProjectList extends Page
{
    public static $name_for_list = "страницу Списка проектов в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'project_list';
    public $action_id = 'pages/activities/projectlist';

}
