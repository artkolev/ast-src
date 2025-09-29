<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница второго шага подачи заявки на присоединение к Маркетплейс - подписание договора для ИП
 */

namespace app\modules\pages\models;

class PServiceDogovorIP extends Page
{
    public static $name_for_list = "Подписание договора услуг для ИП";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_regdogovor';
    public $action_id = 'pages/service/regdogovorip';

}
