<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница списка закрытых заявок, в которых текущий пользователь является покупателем (заказчиком)
 */

namespace app\modules\pages\models;

class QueriesArchive extends Page
{
    public static $name_for_list = "страницу Архив заявок";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'archive';
    public $action_id = 'pages/queries/archive';

}
