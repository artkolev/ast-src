<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр Новостей слушателя ДПО в ЛК (Организатор)
 */

namespace app\modules\pages\models;

class LKEduprogViewMemberTrainingproc extends Page
{
    public static $name_for_list = "страница порядок обучения слушателя ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_member_trainingproc';
    public $action_id = 'pages/eduprog/eduprog-view-member-trainingproc';

}
