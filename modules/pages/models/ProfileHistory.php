<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница с формой редактирования данных О себе. В зависимости от роли содержит разные view и разные Модели для редактирования данных
 */

namespace app\modules\pages\models;

class ProfileHistory extends Page
{
    public static $name_for_list = "Историю пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_history';
    public $action_id = 'pages/profile/history';

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'seo' => 'SEO',
        ];
    }
}
