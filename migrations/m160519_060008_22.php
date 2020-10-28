<?php

use yii\db\Schema;
use yii\db\Migration;

class m160519_060008_22 extends Migration {

    public function up() {
        $this->createTable('log_queue', [
            'id' => Schema::TYPE_PK,
            'uniqueid' => Schema::TYPE_STRING,
            'queue' => Schema::TYPE_STRING,
            'abandoned' => Schema::TYPE_INTEGER,
            'completed' => Schema::TYPE_INTEGER,
            'calls' => Schema::TYPE_INTEGER,
            'date' => Schema::TYPE_DATETIME,
            'branch_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('log_queue_branch_id_fkey', 'log_queue', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m160519_060008_22 cannot be reverted.\n";

        return false;
    }

}
