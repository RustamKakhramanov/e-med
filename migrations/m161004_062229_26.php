<?php
use yii\db\Schema;
use yii\db\Migration;

class m161004_062229_26 extends Migration {

    public function up() {
        $this->addColumn('user_session', 'closed', Schema::TYPE_BOOLEAN . ' DEFAULT false');
    }

    public function down() {
        echo "m161004_062229_26 cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
