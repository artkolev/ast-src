<?php
/**
 * @modelDescr Страница с формой регистрации в качестве Физлица
 */

namespace app\modules\pages\models;

class Regfizusr extends Page
{
    public static $name_for_list = "страницу Регистрации Физлица";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'regfizusr';
    public $action_id = 'pages/register/fizusr';

}
