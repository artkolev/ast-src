<?php
/**
 * @modelDescr Страница с текстом об успешной оплате заказа мероприятия
 */

namespace app\modules\pages\models;

class PaymentESuccess extends Page
{
    public static $name_for_list = "страницу Успешной оплаты Билетов";
    public static $count_for_list = 1;
    public $view = 'payment_e_success';
    public $action_id = 'pages/pages/payment-success';

}
