<?php
/**
 * @modelDescr Страница Личного кабинета - Страница создания нового чата в ЛК пользователя
 */

namespace app\modules\pages\models;

class MessagesNew extends Page
{
    public static $name_for_list = "страницу Написать сообщение";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'create';
    public $action_id = 'pages/message/create';

}
