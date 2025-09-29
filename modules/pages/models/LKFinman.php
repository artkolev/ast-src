<?php
/**
 * @modelDescr Страница Работы со счетами Финансового менеджера
 */

namespace app\modules\pages\models;

class LKFinman extends Page
{
    public static $name_for_list = "ЛК финансового менеджера";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'lk_finman_index';
    public $action_id = 'pages/finman/index';

}
