<?php
/**
 * @modelDescr Страница с формой регистрации в качестве Юрлица
 */

namespace app\modules\pages\models;

class Regurusr extends Page
{
    public static $name_for_list = "страницу Регистрации Юрлица";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'regurusr';
    public $action_id = 'pages/register/urusr';

}
