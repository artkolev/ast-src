<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр программы ДПО раздел Новости и Сообщения
 */

namespace app\modules\pages\models;

class LKEduprogViewNewsCreate extends Page
{
    public static $name_for_list = "страницу Создания/редактирования новостей ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_news_create';
    public $action_id = 'pages/eduprog/eduprog-view-news-create';

}
