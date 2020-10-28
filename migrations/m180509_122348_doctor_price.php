<?php

use yii\db\Migration;

/**
 * Class m180509_122348_doctor_price
 */
class m180509_122348_doctor_price extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('doctor_price', [
            'id' => $this->primaryKey(),
            'doctor_id' => $this->integer()->notNull(),
            'price_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_doctor_price__doctor_id', 'doctor_price', 'doctor_id', 'doctor', 'id');
        $this->addForeignKey('fk_doctor_price__price_id', 'doctor_price', 'price_id', 'price', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180509_122348_doctor_price cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180509_122348_doctor_price cannot be reverted.\n";

        return false;
    }
    */
}
