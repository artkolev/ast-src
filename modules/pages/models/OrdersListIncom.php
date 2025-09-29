<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница списка текущих (незавершенных) заказов, в которых текущий пользователь является продавцом (исполнителем)
 */

namespace app\modules\pages\models;

class OrdersListIncom extends Page
{
    public static $name_for_list = "страницу Текущие заказы";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'incoming';
    public $action_id = 'pages/orders/incoming';

}
