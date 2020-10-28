<?php
use yii\db\Schema;
use yii\db\Migration;

class m161002_143518_25 extends Migration {

    public function up() {
        $this->createTable('log_outgoing', [
            'id' => Schema::TYPE_PK,
            'sess_id' => Schema::TYPE_INTEGER,
            'number' => Schema::TYPE_STRING,
            'date' => Schema::TYPE_TIMESTAMP . '(6)',
            'answer' => Schema::TYPE_BOOLEAN . ' DEFAULT false'
        ]);
        
        $this->addForeignKey('log_outgoing_sess_id_fkey', 'log_outgoing', 'sess_id', 'user_session', 'id');
    }

    public function down() {
        echo "m161002_143518_25 cannot be reverted.\n";

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
