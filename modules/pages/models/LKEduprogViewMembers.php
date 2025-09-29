<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр слушателей ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewMembers extends Page
{
    public static $name_for_list = "страницу Просмотра слушателей ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_members';
    public $action_id = 'pages/eduprog/eduprog-view-members';

}
