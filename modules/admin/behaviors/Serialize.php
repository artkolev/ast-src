<?php

namespace app\modules\admin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class Serialize extends Behavior
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
            if (!empty($this->owner->{$relname}) and is_string($this->owner->{$relname})) {
                try {
                    $data = unserialize($this->owner->{$relname});
                    array_walk_recursive($data, function (&$item, $key) {
                        $item = htmlspecialchars_decode($item);
                    });
                    $this->owner->{$relname} = $data;
                } catch (\Throwable $e) {
                    $this->owner->{$relname} = [];
                }
            }
        }
    }

    public function beforeSave()
    {
        foreach ($this->relations as $relname) {
            if (!empty($this->owner->{$relname}) and is_array($this->owner->{$relname})) {
                // экранировать кавычки в полях
                $data = $this->owner->{$relname};
            } else {
                $data = [];
            }
            // костыль, придумать как сделать штатно, чтобы из админки теги не экранировались.
            // для всех кроме админа - экранировать теги.
            if (!(Yii::$app instanceof \yii\console\Application) && (Yii::$app->user->isGuest or Yii::$app->user->identity->userAR->role != 'admin')) {
                array_walk_recursive($data, function (&$item, $key) {
                    $item = htmlspecialchars($item);
                });
            }
            $this->owner->{$relname} = serialize($data);
        }
        return true;
    }
}
