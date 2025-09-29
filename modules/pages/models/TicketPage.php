<?php
/**
 * @modelDescr Страница не используется. Проверить и удалить
 */

namespace app\modules\pages\models;

class TicketPage extends Page
{
    public static $name_for_list = "Билеты";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'ticket';
    public $action_id = 'pages/pages/ticket';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'ticket'],
        ]);
    }
}
