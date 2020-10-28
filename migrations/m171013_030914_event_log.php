<?php

use yii\db\Migration;

class m171013_030914_event_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('event_log', [
            'id' => $this->primaryKey(),
            'date' => $this->dateTime()->comment('Время изменения'),
            'event_id' => $this->integer()->notNull()->comment('Событие'),
            'user_id' => $this->integer()->comment('Пользователь'),
            'field' => $this->string(255)->comment('Поле'),
            'before_value' => $this->text()->comment('Исходное значение'),
            'after_value' => $this->text()->comment('Измененное значение')
                ], $tableOptions);
        
        $this->addForeignKey('fk_event_log_event_id_id', 'event_log', 'event_id', 'event', 'id');
    }

    public function down()
    {
        echo "m171013_030914_event_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
