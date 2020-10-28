<?php

use yii\db\Migration;

/**
 * Class m180724_052100_cashbox
 */
class m180724_052100_cashbox extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('cashbox', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull()->comment('Название')
        ], $tableOptions);

        $this->addForeignKey('fk_cashbox__branch_id', 'cashbox', 'branch_id', 'branch', 'id');

        $this->addColumn('shift', 'cashbox_id', $this->integer());
        $this->addForeignKey('fk_shift__cashbox_id', 'shift', 'cashbox_id', 'cashbox', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180724_052100_cashbox cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180724_052100_cashbox cannot be reverted.\n";

        return false;
    }
    */
}
