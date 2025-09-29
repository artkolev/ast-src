<?php
/**
 * @modelDescr Страница-каталог Афиш (модуль afisha)
 */

namespace app\modules\pages\models;

class Afishapage extends Page
{
    public static $name_for_list = "каталог Афиш";
    public static $count_for_list = 1;
    public $view = 'afisha';
    public $action_id = 'pages/pages/afisha';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'afisha'],
        ]);
    }
}
