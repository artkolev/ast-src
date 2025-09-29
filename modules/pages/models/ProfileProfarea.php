<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница редактирования Проф.области пользователя (кафедры, специализации, решаемые задачи). В зависимости от роли использует разные view
 */

namespace app\modules\pages\models;

class ProfileProfarea extends Page
{
    public static $name_for_list = "Профессиональную область пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_profarea';
    public $action_id = 'pages/profile/profarea';

}
