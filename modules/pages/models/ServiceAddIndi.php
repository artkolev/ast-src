<?php
/**
 * @modelDescr Страница Личного Кабинета - Создание Индивидуальной услуги (без цены)
 */

namespace app\modules\pages\models;

class ServiceAddIndi extends Page
{
    public static $name_for_list = "страницу Добавления индивидуальной услуги";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_edit';
    public $action_id = 'pages/servicerun/create';

}
