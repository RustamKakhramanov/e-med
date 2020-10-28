<?php

use yii\db\Schema;
use yii\db\Migration;

class m161004_104219_27 extends Migration {

    public function up() {
        $this->createTable('api_call', [
            'id' => Schema::TYPE_PK,
            'branch_id' => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING,
            'number' => Schema::TYPE_STRING,
            'theme' => Schema::TYPE_STRING,
            'date' => Schema::TYPE_TIMESTAMP . '(6)',
            'call' => Schema::TYPE_BOOLEAN . ' DEFAULT false',
            'answer' => Schema::TYPE_BOOLEAN . ' DEFAULT false'
        ]);
        
        $this->addForeignKey('api_call_branch_id_fkey', 'api_call', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m161004_104219_27 cannot be reverted.\n";

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
