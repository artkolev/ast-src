<?php
/**
 * @modelDescr На текущий момент страница не используется - удалить
 */

namespace app\modules\pages\models;

class Serviceregquery extends Page
{
    public static $name_for_list = "страницу Регистрации для запроса";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'service_regquery';
    public $action_id = 'pages/register/servicequery';

}
