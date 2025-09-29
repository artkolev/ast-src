<?php

namespace app\modules\admin\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class ClearRelation extends Behavior
{
    public $relations = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function beforeDelete()
    {
        $transaction = \Yii::$app->db->getTransaction();
        $new_transaction = false;
        // запуск транзакции
        if (empty($transaction) || !$transaction->isActive) {
            $transaction = \Yii::$app->db->beginTransaction();
            $new_transaction = true;
        }

        foreach ($this->relations as $relname) {
            if (!empty($this->owner->{$relname})) {
                if (is_array($this->owner->{$relname})) {
                    foreach ($this->owner->{$relname} as $relmodel) {
                        if (!$relmodel->delete()) {
                            $transaction->rollBack();
                            throw new \Exception('Ошибка при удалении зависимых записей:<br>' . implode('<br>', $relmodel->getNameForView()));
                        }
                    }
                } else {
                    if (!$this->owner->{$relname}->delete()) {
                        $transaction->rollBack();
                        throw new \Exception('Ошибка при удалении зависимых записей:<br>' . implode('<br>', $relmodel->getNameForView()));
                    }
                }
            }
        }

        if ($new_transaction && $transaction->isActive) {
            $transaction->commit();
        }
    }
}
