<?php

use yii\db\Migration;

/**
 * Class m180517_053851_direction_item_cancel_user
 */
class m180517_053851_direction_item_cancel_user extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('direction_item', 'cancel_user_id', $this->integer()->comment('Кто отменил'));
        $this->addForeignKey('fk_direction_item__cancel_user_id', 'direction_item', 'cancel_user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180517_053851_direction_item_cancel_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180517_053851_direction_item_cancel_user cannot be reverted.\n";

        return false;
    }
    */
}
