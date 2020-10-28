<?php

use yii\db\Migration;

/**
 * Class m180719_135055_check_items_rename
 */
class m180719_135055_check_items_rename extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->dropForeignKey('check_items_check_id_fkey', 'check_items');
        $this->renameTable('check_items', 'check_item');
        $this->renameColumn('check_item', 'direction_id', 'direction_item_id');
        $this->addForeignKey('fk_check_item_direction_item_id', 'check_item', 'direction_item_id', 'direction_item', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180719_135055_check_items_rename cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180719_135055_check_items_rename cannot be reverted.\n";

        return false;
    }
    */
}
