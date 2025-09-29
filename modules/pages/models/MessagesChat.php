<?php
/**
 * @modelDescr Страница Личного кабинета - Страница просмотра выбранного чата, в котором участвует текущий пользователь
 */

namespace app\modules\pages\models;

class MessagesChat extends Page
{
    public static $name_for_list = "страницу Чата";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'chat';
    public $action_id = 'pages/message/chat';

}
