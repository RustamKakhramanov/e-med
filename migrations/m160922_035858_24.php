<?php
use yii\db\Schema;
use yii\db\Migration;

class m160922_035858_24 extends Migration {

    public function up() {
        $this->createTable('user_session', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER,
            'number' => Schema::TYPE_STRING,
            'created_at' => Schema::TYPE_TIMESTAMP . '(6)',
            'updated_at' => Schema::TYPE_TIMESTAMP . '(6)'
        ]);
        
        $this->addForeignKey('user_session_user_id_fkey', 'user_session', 'user_id', 'user', 'id');
    }

    public function down() {
        echo "m160922_035858_24 cannot be reverted.\n";

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
