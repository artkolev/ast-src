<?php
/**
 * @modelDescr Страница Личного кабинета - Страница редактирования Проекта, часть модуля lenta
 */

namespace app\modules\pages\models;

class LKProjectEdit extends Page
{
    public static $name_for_list = "страницу Редактирования проекта в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'project_create';
    public $action_id = 'pages/activities/projectedit';

}
