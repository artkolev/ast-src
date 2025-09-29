<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр стоимости и условий по программе ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientPrice extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Стоимость и условия)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_price';
    public $action_id = 'pages/eduprog/eduprog-client-price';

}
