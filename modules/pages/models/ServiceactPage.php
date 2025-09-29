<?php
/**
 * @modelDescr Скорее всего не используется - проверить и удалить
 */

namespace app\modules\pages\models;

class ServiceactPage extends Page
{
    public static $name_for_list = "Акты";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'serviceact';
    public $action_id = 'pages/pages/serviceact';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'serviceact'],
        ]);
    }
}
