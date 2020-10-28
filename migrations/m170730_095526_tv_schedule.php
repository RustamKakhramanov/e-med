<?php

use yii\db\Migration;

class m170730_095526_tv_schedule extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('tv_schedule', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Название'),
            'code' => $this->string(255)->notNull()->unique()->comment('Код'),
            'data' => $this->text(),
            'branch_id' => $this->integer()->notNull()
                ], $tableOptions);
        
        //$this->addPrimaryKey('pk_check', 'check', 'id');
        $this->addForeignKey('fk_tv_schedule_branch_id', 'tv_schedule', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m170730_095526_tv_schedule cannot be reverted.\n";

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
