<?php
/**
 * @modelDescr Страница Личного кабинета - Страница с информацией о выбранном мероприятии, созданным текущем пользователем
 */

namespace app\modules\pages\models;

class LKEventsView extends Page
{
    public static $name_for_list = "страницу Просмотра мероприятия в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'events_view';
    public $action_id = 'pages/activities/eventsview';

}
