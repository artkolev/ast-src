<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр новостей и сообщений по программе ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientChat extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Сообщения)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_chat';
    public $action_id = 'pages/eduprog/eduprog-client-chat';

}
