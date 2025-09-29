<?php
/**
 * @modelDescr Страница Личного кабинета - Страница со списком Новостей, часть модуля lenta, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class LKNewsList extends Page
{
    public static $name_for_list = "страницу Списка новостей в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'news_list';
    public $action_id = 'pages/activities/newslist';

}
