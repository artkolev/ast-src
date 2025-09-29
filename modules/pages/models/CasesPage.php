<?php
/**
 * @modelDescr Страница-каталог проектов Академии
 */

namespace app\modules\pages\models;

class CasesPage extends Page
{
    public static $name_for_list = "Каталог проектов";
    public static $count_for_list = 1;
    public $view = 'cases';
    public $action_id = 'pages/pages/cases';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'cases'],
        ]);
    }
}
