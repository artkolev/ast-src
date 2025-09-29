<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница редактирования услуги, созданной текущим пользователем. (для Типовых и Индивидуальных услуг страница одна)
 */

namespace app\modules\pages\models;

class ServiceEdit extends Page
{
    public static $name_for_list = "страницу Редактирования услуги";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'service_edit';
    public $action_id = 'pages/servicerun/create';

}
