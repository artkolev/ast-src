<?php
/**
 * @modelDescr Страница с формой образения в Техподдержку
 */

namespace app\modules\pages\models;

class SupportPage extends Page
{
    public static $name_for_list = "страницу Формы поддержки";
    public static $count_for_list = 1;
    public $view = 'supportpage';
    public $action_id = 'pages/supportpage/index';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'formslist'],
        ]);
    }
}
