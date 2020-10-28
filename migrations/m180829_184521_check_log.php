<?php

use yii\db\Migration;

/**
 * Class m180829_184521_check_log
 */
class m180829_184521_check_log extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('check_log', [
            'id' => $this->primaryKey(),
            'check_id' => $this->integer(),
            'user_id' => $this->integer(),
            'date' => $this->dateTime(),
            'params' => $this->text(),
            'message' => $this->text()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180829_184521_check_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180829_184521_check_log cannot be reverted.\n";

        return false;
    }
    */
}
