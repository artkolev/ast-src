<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница с контактами пользователя (Ссылки). В зависимости от роли содержит разные view и разные Модели для редактирования данных
 */

namespace app\modules\pages\models;

class ProfileContact extends Page
{
    public static $name_for_list = "Контакты пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_contact';
    public $action_id = 'pages/profile/contact';

}
