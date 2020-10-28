<?php

use yii\db\Migration;

/**
 * Class m180826_104329_check_webkassa_id
 */
class m180826_104329_check_webkassa_id extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('check', 'webkassa_id', $this->string(255)->comment('Фискальный признак вебкассы'));
        $this->addColumn('check', 'webkassa_data', $this->text()->comment('Данные вебкассы'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180826_104329_check_webkassa_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180826_104329_check_webkassa_id cannot be reverted.\n";

        return false;
    }
    */
}
