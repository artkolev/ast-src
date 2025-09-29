<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр Платежей слушателя ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewMemberOrders extends Page
{
    public static $name_for_list = "страницу Заказов слушателя ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_member_orders';
    public $action_id = 'pages/eduprog/eduprog-view-member-orders';

}
