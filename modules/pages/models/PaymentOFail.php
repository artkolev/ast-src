<?php
/**
 * @modelDescr Страница с текстом об ошибке/отмене неклассифицированного (это не заказ услуги и не заказ мероприятия) платежа
 */

namespace app\modules\pages\models;

class PaymentOFail extends Page
{
    public static $name_for_list = "страницу НЕуспешной оплаты";
    public static $count_for_list = 1;
    public $view = 'payment_o_fail';
    public $action_id = 'pages/pages/payment-fail';

}
