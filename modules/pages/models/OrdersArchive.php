<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница списка завершенных заказов, в которых текущий пользователь является покупателем (заказчиком)
 */

namespace app\modules\pages\models;

class OrdersArchive extends Page
{
    public static $name_for_list = "страницу Архив заказов";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'archive';
    public $action_id = 'pages/orders/archive';

}
