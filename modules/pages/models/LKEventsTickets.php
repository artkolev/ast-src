<?php
/**
 * @modelDescr Страница Личного кабинета - Страница содержит список мероприятий, на которые у пользователя есть купленные и оплаченные билеты
 */

namespace app\modules\pages\models;

class LKEventsTickets extends Page
{
    public static $name_for_list = "страницу Списка купленных билетов";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'events_tickets';
    public $action_id = 'pages/activities/eventstickets';

}
