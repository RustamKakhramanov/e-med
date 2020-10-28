<?php

use yii\db\Schema;
use yii\db\Migration;

class m161007_065659_28 extends Migration {

    public function up() {
        $this->addColumn('user_session', 'ip', Schema::TYPE_STRING);
    }

    public function down() {
        echo "m161007_065659_28 cannot be reverted.\n";

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
