<?php
/**
 * @modelDescr Страница Личного кабинета - Содержит список Материалов (База знаний), часть модуля lenta, созданных текущим пользователем
 */

namespace app\modules\pages\models;

class LKMaterialList extends Page
{
    public static $name_for_list = "страницу Списка материалов в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'material_list';
    public $action_id = 'pages/activities/materiallist';

}
