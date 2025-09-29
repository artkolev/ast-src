<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр программы ДПО раздел Новости и Сообщения в ЛК Организатора
 */

namespace app\modules\pages\models;

class LKEduprogViewNews extends Page
{
    public static $name_for_list = "страницу Просмотра новостей ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_news';
    public $action_id = 'pages/eduprog/eduprog-view-news';

}
