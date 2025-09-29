<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр Сообщений слушателя ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewMemberChat extends Page
{
    public static $name_for_list = "страницу Сообщений слушателя ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_member_chat';
    public $action_id = 'pages/eduprog/eduprog-view-member-chat';

}
