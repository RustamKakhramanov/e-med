<?php
/**
 * связь направления с приемом
 */
use yii\db\Schema;
use yii\db\Migration;

class m160118_163507_6 extends Migration {

    public function up() {
        $this->addColumn('direction', 'reception_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('direction_reception_id_fkey', 'direction', 'reception_id', 'reception', 'id');
    }

    public function down() {
        echo "m160118_163507_6 cannot be reverted.\n";

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
