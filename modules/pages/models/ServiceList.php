<?php
/**
 * @modelDescr Страница Личного Кабинета - Содержит список услуг, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class ServiceList extends Page
{
    public static $name_for_list = "страницу Список услуг";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'list';
    public $action_id = 'pages/servicerun/list';

}
