<?php

use yii\db\Migration;

/**
 * Class m180724_012237_direction_item_status
 */
class m180724_012237_direction_item_status extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('direction_item', 'status', $this->tinyInteger()->comment('Статус')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180724_012237_direction_item_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180724_012237_direction_item_status cannot be reverted.\n";

        return false;
    }
    */
}
