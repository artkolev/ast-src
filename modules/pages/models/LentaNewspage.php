<?php
/**
 * @modelDescr Страница-каталог раздела Ленты (модуль lenta) - Новости
 */

namespace app\modules\pages\models;

use app\modules\admin\components\FilestoreModel;

class LentaNewspage extends Lentapage
{
    public static $name_for_list = "каталог Ленты (Новости)";

    public $action_id = 'pages/pages/news';
    public $lentatype = \app\modules\lenta\models\News::LENTATYPE;

    public function getImage_first()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => LentaNewspage::class, 'keeper_field' => 'image_first']);
    }

    public function getImage_first_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => LentaNewspage::class, 'keeper_field' => 'image_first_mobile']);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'lenta'],
        ]);
    }
}
