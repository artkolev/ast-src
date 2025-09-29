<?php

namespace app\modules\admin\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Преобразовывает относительные ссылки на абсолютные в загруженных в ckeditor выбранных полях
 */
class WrapCkUrlToAbsolute extends Behavior
{
    public $relations = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    public function beforeSave()
    {
        foreach ($this->relations as $relname) {
            if (!empty($this->owner->{$relname})) {
                // elfinder user controller
                $this->owner->{$relname} = str_replace("href=\"/files/userData/", "href=\"" . trim(\app\helpers\MainHelper::get_template_base_url(), '/') . "/files/userData/", $this->owner->{$relname});
                $this->owner->{$relname} = str_replace("src=\"/files/userData/", "src=\"" . trim(\app\helpers\MainHelper::get_template_base_url(), '/') . "/files/userData/", $this->owner->{$relname});
                // elfinder admin controller
                $this->owner->{$relname} = str_replace("href=\"/files/content/", "href=\"" . trim(\app\helpers\MainHelper::get_template_base_url(), '/') . "/files/content/", $this->owner->{$relname});
                $this->owner->{$relname} = str_replace("src=\"/files/content/", "src=\"" . trim(\app\helpers\MainHelper::get_template_base_url(), '/') . "/files/content/", $this->owner->{$relname});
            }
        }
        return true;
    }
}
