<?php
/**
 * @modelDescr Страница с выбором способа оплаты для Заказов. Страница доступна только авторизованным пользователям.
 */

namespace app\modules\pages\models;

class SelectPayment extends Page
{
    public static $name_for_list = "страницу Выбора типа оплаты";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'select_payment';
    public $action_id = 'pages/payment/index';

}
