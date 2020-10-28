<?php

use yii\db\Schema;
use yii\db\Migration;

class m160519_135940_23 extends Migration {

    public function up() {
        $this->createTable('log_queue_entry', [
            'id' => Schema::TYPE_PK,
            'uniqueid' => Schema::TYPE_STRING,
            'queue' => Schema::TYPE_STRING,
            'number' => Schema::TYPE_STRING,
            'opened' => Schema::TYPE_DATETIME,
            'closed' => Schema::TYPE_DATETIME,
            'duration' => Schema::TYPE_INTEGER,
            'branch_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('log_queue_entry_branch_id_fkey', 'log_queue_entry', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m160519_135940_23 cannot be reverted.\n";

        return false;
    }
}
