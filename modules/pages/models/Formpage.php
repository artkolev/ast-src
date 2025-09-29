<?php
/**
 * @modelDescr Страница-каталог для Конструктора форм
 */

namespace app\modules\pages\models;

class Formpage extends Page
{
    public static $name_for_list = "страницу Коструктора форм";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'formpage';
    public $action_id = 'pages/formpage/index';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'formslist'],
        ]);
    }
}
