<?php

use yii\db\Migration;

/**
 * Class m180719_133616_check_cash_card
 */
class m180719_133616_check_cash_card extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('check', 'payment_cash', $this->float()->comment('Оплата наличными'));
        $this->addColumn('check', 'payment_card', $this->float()->comment('Оплата платежной картой'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180719_133616_check_cash_card cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180719_133616_check_cash_card cannot be reverted.\n";

        return false;
    }
    */
}
