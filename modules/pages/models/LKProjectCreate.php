<?php
/**
 * @modelDescr Страница Личного кабинета - Страница создания Проекта, часть модуля lenta
 */

namespace app\modules\pages\models;

class LKProjectCreate extends Page
{
    public static $name_for_list = "страницу Создания проекта в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'project_create';
    public $action_id = 'pages/activities/projectcreate';

}
