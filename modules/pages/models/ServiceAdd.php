<?php
/**
 * @modelDescr Страница Личного Кабинета - Создание Типовой услуги (с ценой)
 */

namespace app\modules\pages\models;

class ServiceAdd extends Page
{
    public static $name_for_list = "страницу Добавления типовой услуги";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_edit';
    public $action_id = 'pages/servicerun/create';

}
