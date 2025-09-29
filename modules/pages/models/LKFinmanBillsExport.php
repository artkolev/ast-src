<?php
/**
 * @modelDescr Страница выгрузки счетов в ЛК Финансового менеджера
 */

namespace app\modules\pages\models;

class LKFinmanBillsExport extends Page
{
    public static $name_for_list = "ЛК финансового менеджера - скачать выгрузку";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'lk_finman_bills_export';
    public $action_id = 'pages/finman/billsexport';

}
