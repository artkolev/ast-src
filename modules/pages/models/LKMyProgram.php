<?php
/**
 * @modelDescr Страница Личного кабинета - Текстовая страница, функционала нет. Создана как заглушка по просьбе АСТ
 */

namespace app\modules\pages\models;

class LKMyProgram extends Page
{
    public static $name_for_list = "страницу Мои программы";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'program';
    public $action_id = 'pages/documents/program';

}
