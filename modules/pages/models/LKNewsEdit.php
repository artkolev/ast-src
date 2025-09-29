<?php
/**
 * @modelDescr Страница Личного кабинета - Страница редактирования Новости, часть модуля lenta
 */

namespace app\modules\pages\models;

class LKNewsEdit extends Page
{
    public static $name_for_list = "страницу Редактирования новости в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'news_create';
    public $action_id = 'pages/activities/newsedit';

}
