<?php
/**
 * @modelDescr Страница с текстом об успешной оплате заказа услуги
 */

namespace app\modules\pages\models;

class PaymentSSuccess extends Page
{
    public static $name_for_list = "страницу Успешной оплаты Услуги";
    public static $count_for_list = 1;
    public $view = 'payment_s_success';
    public $action_id = 'pages/pages/payment-success';

}
