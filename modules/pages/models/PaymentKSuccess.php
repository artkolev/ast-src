<?php
/**
 * @modelDescr Страница с текстом об успешной оплате заказа мероприятия
 */

namespace app\modules\pages\models;

class PaymentKSuccess extends Page
{
    public static $name_for_list = "страницу Успешной оплаты Программ ДПО";
    public static $count_for_list = 1;
    public $view = 'payment_k_success';
    public $action_id = 'pages/pages/payment-success';

}
