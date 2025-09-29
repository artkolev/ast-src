<?php

namespace app\modules\admin\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class SaveOneRelation extends Behavior
{
    public $relations = [];

    public function loadRelated($data, $formName = null)
    {
        foreach ($this->relations as $relname => $relclass) {
            $this->owner->{$relname}->load($data);
        }
        return true;
    }

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_INIT => 'eventInit',
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',

            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',

            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',

            BaseActiveRecord::EVENT_AFTER_REFRESH => 'afterRefresh',

            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function eventInit()
    {
        if ($this->owner->forceInit) {
            foreach ($this->relations as $relname => $relclass) {
                if (is_null($this->owner->{$relname})) {
                    $this->owner->{$relname} = new $relclass();
                }
            }
        }
    }

    public function afterFind()
    {
        foreach ($this->relations as $relname => $relclass) {
            if (is_null($this->owner->{$relname})) {
                $this->owner->{$relname} = new $relclass();
            }
        }
    }

    public function beforeValidate()
    {
        $isValid = true;
        foreach ($this->relations as $relname => $relclass) {
            if (!$this->owner->{$relname}->validate()) {
                $this->owner->addError($relname, 'Валидация связи ' . $relname . ' не пройдена');
                $isValid = false;
            }
        }
        return $isValid;
    }

    public function afterSave()
    {
        foreach ($this->relations as $relname => $relclass) {
            $this->owner->link($relname, $this->owner->{$relname});
        }
    }

    public function afterRefresh()
    {
        foreach ($this->relations as $relname => $relclass) {
            $this->owner->{$relname}->refresh();
        }
    }

    public function beforeDelete()
    {
        foreach ($this->relations as $relname => $relclass) {
            $this->owner->{$relname}->delete();
        }
    }
}
