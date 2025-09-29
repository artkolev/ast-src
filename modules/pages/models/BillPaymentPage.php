<?php
/**
 * @modelDescr Страница скачивания выставленного счета
 */

namespace app\modules\pages\models;

class BillPaymentPage extends Page
{
    public static $name_for_list = "страницу Скачивания счета";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'bill';
    public $action_id = 'pages/payment/bill';


}
