<?php
/**
 * @modelDescr Страница оформления заказа на Программу ДПО. Содержит форму для заполнения данных по каждому билету.
 */

namespace app\modules\pages\models;

class EduprogTicketbuy extends Page
{
    public static $name_for_list = "страницу Оформления билетов";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'ticketbuy';
    public $action_id = 'pages/eduprog/ticketbuy';

}
