<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр списка заказов ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewOrders extends Page
{
    public static $name_for_list = "страницу Просмотра заказов ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_orders';
    public $action_id = 'pages/eduprog/eduprog-view-orders';

}
