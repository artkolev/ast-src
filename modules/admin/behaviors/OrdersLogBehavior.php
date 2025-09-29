<?php

namespace app\modules\admin\behaviors;

use app\models\OrdersLogs;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Exception;

class OrdersLogBehavior extends Behavior
{
    protected array $disabledFields = ['created_at', 'updated_at'];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    /**
     * Обработчик после вставки модели
     *
     * @return void
     * @throws Exception
     */
    public function afterInsert()
    {
        $this->saveLog('insert', 'Создание заказа');
    }

    /**
     * Метод сохранения логов
     *
     * @param $action
     * @param null $comment
     * @return void
     */
    public function saveLog($action, $comment = null): void
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        // Получаем старые значения
        $oldValues = $action === 'update' ? $this->getOldValues($owner) : [];

        // Получаем новые значения
        $newValues = $action === 'update' ? $this->getNewValues($owner) : $owner->getAttributes();

        if (empty($newValues)) {
            return;
        }

        // Вставляем запись в таблицу логов
        $log = new OrdersLogs();
        $log->entity_id = $owner->primaryKey;
        $log->entity_model = get_class($owner);
        $log->user_id = Yii::$app->user->id ?? null; // Если пользователь авторизован
        $log->old_values = json_encode($oldValues);
        $log->new_values = json_encode($newValues);
        $log->comment = $comment;
        $log->save();
    }

    // Регистрация событий

    /**
     * Получаем старые параметры модели
     *
     * @param $model
     * @return array
     */
    private function getOldValues($model)
    {
        $oldValues = [];
        foreach ($model->dirtyAttributes as $attribute => $newValue) {
            $oldValue = $model->getOldAttribute($attribute);
            if ($oldValue != $newValue && !in_array($attribute, $this->disabledFields)) {
                $oldValues[$attribute] = $oldValue;
            }
        }
        return $oldValues;
    }

    /**
     * Получаем новые параметры модели
     *
     * @param $model
     * @return array
     */
    private function getNewValues($model)
    {
        $newValues = [];
        foreach ($model->dirtyAttributes as $attribute => $newValue) {
            $oldValue = $model->getOldAttribute($attribute);
            if ($oldValue != $newValue && !in_array($attribute, $this->disabledFields)) {
                $newValues[$attribute] = $newValue;
            }
        }
        return $newValues;
    }

    /**
     * Обработчик перед обновлением модели
     *
     * @return void
     * @throws Exception
     */
    public function beforeUpdate()
    {
        $this->saveLog('update', 'Изменение заказа');
    }
}
