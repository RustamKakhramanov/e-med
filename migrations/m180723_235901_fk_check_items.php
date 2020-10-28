<?php

use yii\db\Migration;

/**
 * Class m180723_235901_fk_check_items
 */
class m180723_235901_fk_check_items extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addForeignKey('fk_check_item', 'check_item', 'check_id', 'check', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180723_235901_fk_check_items cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180723_235901_fk_check_items cannot be reverted.\n";

        return false;
    }
    */
}
