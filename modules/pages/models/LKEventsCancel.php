<?php
/**
 * @modelDescr Страница Личного Кабинета - страница с формой для отмены мероприятия
 */

namespace app\modules\pages\models;

class LKEventsCancel extends Page
{
    public static $name_for_list = "страницу Отмены мероприятия в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'events_cancel';
    public $action_id = 'pages/activities/eventscancel';

}
