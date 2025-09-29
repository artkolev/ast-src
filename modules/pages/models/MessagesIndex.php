<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница со списком Чатов, доступных текущему пользователю
 */

namespace app\modules\pages\models;

class MessagesIndex extends Page
{
    public static $name_for_list = "страницу Мои сообщения";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'index';
    public $action_id = 'pages/message/index';

}
