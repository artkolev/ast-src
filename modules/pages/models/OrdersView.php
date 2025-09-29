<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница просмотра и работы с заказом в Личном кабинете. Содержит весь функционал по работе с заказом, в зависимости от статуса и роли пользователя в заказе (клиент/испольнитель/мкс) применяются разые view.
 */

namespace app\modules\pages\models;

class OrdersView extends Page
{
    public static $name_for_list = "страницу Просмотра заказа";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'view';
    public $action_id = 'pages/orders/view';

}
