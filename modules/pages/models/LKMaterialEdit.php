<?php
/**
 * @modelDescr Страница Личного кабинета - Страница редактирования Материала (База знаний), часть модуля lenta
 */

namespace app\modules\pages\models;

class LKMaterialEdit extends Page
{
    public static $name_for_list = "страницу Редактирования материала в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'material_create';
    public $action_id = 'pages/activities/materialedit';

}
