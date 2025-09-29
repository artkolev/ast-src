<?php
/**
 * @modelDescr Страница Личного кабинета - Дополнительная информация по программе ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientDopinfo extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Дополнительная информация)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_dopinfo';
    public $action_id = 'pages/eduprog/eduprog-client-dopinfo';

}
