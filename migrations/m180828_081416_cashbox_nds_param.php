<?php

use yii\db\Migration;

/**
 * Class m180828_081416_cashbox_nds_param
 */
class m180828_081416_cashbox_nds_param extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('cashbox', 'use_nds', $this->boolean()->defaultValue(false)->comment('Использовать НДС'));
        $this->addColumn('check', 'nds', $this->float()->comment('НДС'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180828_081416_cashbox_nds_param cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180828_081416_cashbox_nds_param cannot be reverted.\n";

        return false;
    }
    */
}
