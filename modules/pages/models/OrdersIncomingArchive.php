<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница списка завершенных заказов, в которых текущий пользователь является продавцом (исполнителем)
 */

namespace app\modules\pages\models;

class OrdersIncomingArchive extends Page
{
    public static $name_for_list = "страницу Архив заказов эксперта";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'archive_incoming';
    public $action_id = 'pages/orders/archiveincoming';

}
