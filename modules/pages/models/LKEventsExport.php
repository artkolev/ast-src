<?php
/**
 * @modelDescr Страница Личного кабинета - Содержит перечень Форм из Конструктора форм, в которых для текущего пользователя заданы права на выгрузку данных
 */

namespace app\modules\pages\models;

class LKEventsExport extends Page
{
    public static $name_for_list = "страницу Выгрузки мероприятий";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'events_export';
    public $action_id = 'pages/activities/events_export';

}
