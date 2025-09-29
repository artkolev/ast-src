<?php
/**
 * @modelDescr На текущий момент страница не используется - удалить
 */

namespace app\modules\pages\models;

class Servicepage extends Page
{
    public static $name_for_list = "каталог Услуг";
    public static $count_for_list = 1;
    public $view = 'service';
    public $action_id = 'pages/pages/service';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'service'],
        ]);
    }
}
