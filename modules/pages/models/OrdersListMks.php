<?php
/**
 * @modelDescr Страница Личного Кабинета МКС (роль пользователя mks) - Страница содержит список заказов, требующих внимания менеджера Клиентского Сервиса
 */

namespace app\modules\pages\models;

class OrdersListMks extends Page
{
    public static $name_for_list = "страницу Заказы МКС";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'index_mks';
    public $action_id = 'pages/orders/index_mks';

}
