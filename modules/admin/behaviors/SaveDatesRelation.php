<?php

namespace app\modules\admin\behaviors;

use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class SaveDatesRelation extends Behavior
{
    public $relations = [];

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

    public function afterFind()
    {
        foreach ($this->relations as $relname) {
            if (!empty($this->owner->{$relname})) {
                $table = Yii::$app->db->schema->getTableSchema($this->owner::tableName());
                switch ($table->columns[$relname]->type) {
                    case 'datetime':
                        $this->owner->{$relname} = date('d.m.Y H:i:s', strtotime($this->owner->{$relname}));
                        break;
                    case 'date':
                        $this->owner->{$relname} = date('d.m.Y', strtotime($this->owner->{$relname}));
                        break;
                    case 'integer':
                        // время в формате integer всегда хранится в UTC
                        if (!($this->owner->{$relname} instanceof DateTime)) {
                            $date = new DateTime();
                            $date->setTimestamp((int)$this->owner->{$relname});
                            $date->setTimezone(new DateTimeZone(Yii::$app->getTimeZone()));
                            $this->owner->{$relname} = $date;
                        }
                        break;
                }
            }
        }
    }

    public function beforeSave()
    {
        foreach ($this->relations as $relname) {
            if (!empty($this->owner->{$relname})) {
                $table = Yii::$app->db->schema->getTableSchema($this->owner::tableName());
                switch ($table->columns[$relname]->type) {
                    case 'datetime':
                        $this->owner->{$relname} = date('Y-m-d H:i:s', strtotime($this->owner->{$relname}));
                        break;
                    case 'date':
                        $this->owner->{$relname} = date('Y-m-d', strtotime($this->owner->{$relname}));
                        break;
                    case 'integer':
                        // ожидаем дату в формате строки с локальной таймзоной, число (время в UTC - Unix Timestamp), либо объект DateTime
                        if ($this->owner->{$relname} == (int)$this->owner->{$relname}) {
                            // передано число (возможно в формате строки)
                            $this->owner->{$relname} = (int)$this->owner->{$relname};
                        } elseif (is_string($this->owner->{$relname})) {
                            // передана строка, пытаемся преобразовать в дату
                            $date = DateTime::createFromFormat('d.m.Y H:i:s', $this->owner->{$relname}, new DateTimeZone(Yii::$app->getTimeZone()));
                            $this->owner->{$relname} = $date->getTimestamp();
                        } elseif ($this->owner->{$relname} instanceof DateTime) {
                            $this->owner->{$relname} = $this->owner->{$relname}->getTimestamp();
                        }
                        break;
                }

            }
        }
        return true;
    }
}
