<?php

use yii\db\Migration;

/**
 * Class m180724_062024_cashbox_deleted
 */
class m180724_062024_cashbox_deleted extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('cashbox', 'deleted', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180724_062024_cashbox_deleted cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180724_062024_cashbox_deleted cannot be reverted.\n";

        return false;
    }
    */
}
