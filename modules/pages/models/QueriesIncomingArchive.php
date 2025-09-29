<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница списка закрытых заявок, в которых текущий пользователь является продавцом (исполнителем)
 */

namespace app\modules\pages\models;

class QueriesIncomingArchive extends Page
{
    public static $name_for_list = "страницу Архив заявок эксперта";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'archive_incoming';
    public $action_id = 'pages/queries/archiveincoming';

}
