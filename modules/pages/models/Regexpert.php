<?php
/**
 * @modelDescr Страница с формой регистрации в качестве Претендента (Физлицо + отметка "претендент", но на фронте называется регистрация Эксперта)
 */

namespace app\modules\pages\models;

class Regexpert extends Page
{
    public static $name_for_list = "страницу Регистрации Эксперта";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'regexpert';
    public $action_id = 'pages/register/expert';

}
