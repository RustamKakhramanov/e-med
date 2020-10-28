<?php

use yii\db\Schema;
use yii\db\Migration;

class m161007_101856_29 extends Migration {

    public function up() {
        $this->addColumn('log_outgoing', 'type', Schema::TYPE_INTEGER);
    }

    public function down() {
        echo "m161007_101856_29 cannot be reverted.\n";

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
