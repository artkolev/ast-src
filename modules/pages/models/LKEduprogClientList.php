<?php
/**
 * @modelDescr Страница Личного кабинета - Содержит список программ ДПО, в которых участвует пользователь (как слушатель или плательщик)
 */

namespace app\modules\pages\models;

class LKEduprogClientList extends Page
{
    public static $name_for_list = "страницу Списка программ ДПО в ЛК (Слушатель/Плательщик)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_list';
    public $action_id = 'pages/eduprog/eduprog-client-list';

}
