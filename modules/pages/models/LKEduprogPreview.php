<?php
/**
 * @modelDescr Страница Личного кабинета - Предпросмотр программы ДПО
 */

namespace app\modules\pages\models;

class LKEduprogPreview extends Page
{
    public static $name_for_list = "страницу Предпросмотра программы ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_preview';
    public $action_id = 'pages/eduprog/eduprogpreview';

}
