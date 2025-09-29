<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр содержимого программы ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientContent extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Содержимое программы)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_content';
    public $action_id = 'pages/eduprog/eduprog-client-content';

}
