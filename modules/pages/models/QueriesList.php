<?php
/**
 * @modelDescr Страница Личного Кабинета - Список текущих заявок (незавершенных), в которых текущий пользователь является покупателем (заказчиком)
 */

namespace app\modules\pages\models;

class QueriesList extends Page
{
    public static $name_for_list = "страницу Мои заявки";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'index';
    public $action_id = 'pages/queries/index';

}
