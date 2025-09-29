<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр списка заказов/платежей по программе ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientOrder extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Платежи/Заказы)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_order';
    public $action_id = 'pages/eduprog/eduprog-client-order';

}
