<?php
/**
 * @modelDescr Страница Личного Кабинета - страница с формой для отмены программы ДПО
 */

namespace app\modules\pages\models;

class LKEduprogCancel extends Page
{
    public static $name_for_list = "страницу Отмены программы в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'eduprog_cancel';
    public $action_id = 'pages/eduprog/eduprog-cancel';

}
