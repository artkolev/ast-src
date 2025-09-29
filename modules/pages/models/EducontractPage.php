<?php
/**
 * @modelDescr Страница-каталог договоров на ДПО
 */

namespace app\modules\pages\models;

class EducontractPage extends Page
{
    public static $name_for_list = "Каталог договоров на ДПО";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = '';
    public $action_id = 'pages/pages/none';

    // сохранение/удаление/валидация

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'educontract'],
            [['unset_from_sitemap'], 'default', 'value' => 1],
        ]);
    }
}
