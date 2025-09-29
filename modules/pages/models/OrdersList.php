<?php
/**
 * @modelDescr Страница Личного кабинета - Список текущих заказов (незавершенных), в которых текущий пользователь является покупателем (заказчиком)
 */

namespace app\modules\pages\models;

class OrdersList extends Page
{
    public static $name_for_list = "страницу Мои заказы";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'index';
    public $action_id = 'pages/orders/index';

}
