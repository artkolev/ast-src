<?php
/**
 * @modelDescr Страница Личного кабинета - Страница создания Материала (База знаний), часть модуля lenta
 */

namespace app\modules\pages\models;

class LKMaterialCreate extends Page
{
    public static $name_for_list = "страницу Создания материала в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'material_create';
    public $action_id = 'pages/activities/materialcreate';

}
