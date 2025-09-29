<?php
/**
 * @modelDescr Страница с выбором типа регистрации на сайте
 */

namespace app\modules\pages\models;

class Regstart extends Page
{
    public static $name_for_list = "страницу Выбора типа Регистрации";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'regstart';
    public $action_id = 'pages/register/index';

}
