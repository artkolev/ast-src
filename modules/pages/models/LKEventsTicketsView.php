<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница с информацией о Мероприятии и списком купленных билетов текущего пользователя на выбранное мероприятие
 */

namespace app\modules\pages\models;

class LKEventsTicketsView extends Page
{
    public static $name_for_list = "страницу Просмотра купленных билетов";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'tickets_view';
    public $action_id = 'pages/activities/ticketsview';

}
