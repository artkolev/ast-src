<?php
/**
 * @modelDescr Страница с текстом об успешной оплате неклассифицированного (не заказ услуги и не заказ мероприятия) платежа
 */

namespace app\modules\pages\models;

class PaymentOSuccess extends Page
{
    public static $name_for_list = "страницу Успешной оплаты (прочее)";
    public static $count_for_list = 1;
    public $view = 'payment_o_success';
    public $action_id = 'pages/pages/payment-success';

}
