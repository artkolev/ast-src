<?php
/**
 * @modelDescr Страница оформления заказа на Мероприятие. Содержит форму для заполнения данных по каждому билету.
 */

namespace app\modules\pages\models;

class Ticketbuy extends Page
{
    public static $name_for_list = "страницу Оформления билетов";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'ticketbuy';
    public $action_id = 'pages/activities/ticketbuy';

}
