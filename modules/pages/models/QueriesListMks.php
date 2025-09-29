<?php
/**
 * @modelDescr Страница Личного Кабинета МКС (роль пользователя mks) - Страница содержит список заявок, требующих внимания менеджера Клиентского Сервиса
 */

namespace app\modules\pages\models;

class QueriesListMks extends Page
{
    public static $name_for_list = "страницу Заявки МКС";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'index_mks';
    public $action_id = 'pages/queries/indexmks';

}
