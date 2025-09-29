<?php

namespace app\modules\admin\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class SaveSetsBehaviour extends Behavior
{
    public $attributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',

            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterFind',

            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterFind',

            BaseActiveRecord::EVENT_AFTER_REFRESH => 'afterFind',
        ];
    }

    public function beforeSave($insert)
    {
        foreach ($this->attributes as $relname) {
            if (is_array($this->owner->{$relname})) {
                $this->owner->{$relname} = implode(',', $this->owner->{$relname});
            }
        }
        return true;
    }

    public function afterFind()
    {
        foreach ($this->attributes as $relname) {
            if (is_string($this->owner->{$relname})) {
                $this->owner->{$relname} = explode(',', $this->owner->{$relname});
            }
        }
    }
}
