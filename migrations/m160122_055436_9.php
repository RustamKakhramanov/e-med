<?php
/**
 * таблица лога заданий для устройства
 */
use yii\db\Schema;
use yii\db\Migration;

class m160122_055436_9 extends Migration {

    public function up() {
        $this->createTable('device_task', [
            'id' => Schema::TYPE_PK,
            'device_id' => Schema::TYPE_INTEGER,
            'comand' => Schema::TYPE_STRING,
            'extra' => Schema::TYPE_TEXT,
            'uid' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_INTEGER,
            'created' => Schema::TYPE_TIMESTAMP . '(6)',
            'updated' => Schema::TYPE_TIMESTAMP . '(6)',
        ]);
        
        $this->addForeignKey('device_task_id_fkey', 'device_task', 'device_id', 'device', 'id');
    }

    public function down() {
        echo "m160122_055436_9 cannot be reverted.\n";

        return false;
    }

}
