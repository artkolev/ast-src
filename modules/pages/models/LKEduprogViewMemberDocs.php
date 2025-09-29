<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр Документов слушателя ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewMemberDocs extends Page
{
    public static $name_for_list = "страницу Документов слушателя ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_member_docs';
    public $action_id = 'pages/eduprog/eduprog-view-member-docs';

}
