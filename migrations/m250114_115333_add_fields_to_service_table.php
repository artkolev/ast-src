<?php

declare(strict_types=1);

use app\modules\service\models\Service;
use yii\db\Migration;

class m250114_115333_add_fields_to_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $table = Yii::$app->db->schema->getTableSchema(Service::tableName());
        if (!isset($table->columns['text'])) {
            $this->addColumn(Service::tableName(), 'text', $this->string(255)->null());
        }
        if (!isset($table->columns['date'])) {
            $this->addColumn(Service::tableName(), 'date', $this->date()->null());
        }
        if (!isset($table->columns['datetime'])) {
            $this->addColumn(Service::tableName(), 'datetime', $this->timestamp()->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $table = Yii::$app->db->schema->getTableSchema(Service::tableName());
        if (isset($table->columns['text'])) {
            $this->dropColumn(Service::tableName(), 'text');
        }
        if (isset($table->columns['date'])) {
            $this->dropColumn(Service::tableName(), 'date');
        }
        if (isset($table->columns['datetime'])) {
            $this->dropColumn(Service::tableName(), 'datetime');
        }
    }
}
