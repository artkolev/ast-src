<?php
/**
 * @modelDescr Страница-каталог для Опросника
 */

namespace app\modules\pages\models;

class Questionnairepage extends Page
{
    public static $name_for_list = "страницу Опросника";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'questionnairepage';
    public $action_id = 'pages/questionnairepage/index';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'questionnaire'],
        ]);
    }
}
