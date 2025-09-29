<?php
/**
 * @modelDescr Страница Личного кабинета - Заявка на перенос даты программы ДПО
 */

namespace app\modules\pages\models;

class LKEduprogChangeDate extends Page
{
    public static $name_for_list = "страницу Заявки на перенос даты программы ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_change_date';
    public $action_id = 'pages/eduprog/eduprog-change-date';

}
