<?php
/**
 * @modelDescr Страница Личного кабинета - Содержит список мероприятий, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class LKEventsList extends Page
{
    public static $name_for_list = "страницу Списка мероприятий в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'events_list';
    public $action_id = 'pages/activities/eventslist';

}
