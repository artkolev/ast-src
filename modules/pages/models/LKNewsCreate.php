<?php
/**
 * @modelDescr Страница Личного кабинета - Страница создания Новости, часть модуля lenta
 */

namespace app\modules\pages\models;

class LKNewsCreate extends Page
{
    public static $name_for_list = "страницу Создания новости в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'news_create';
    public $action_id = 'pages/activities/newscreate';

}
