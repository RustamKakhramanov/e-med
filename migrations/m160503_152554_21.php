<?php

use yii\db\Schema;
use yii\db\Migration;

class m160503_152554_21 extends Migration {

    public function up() {
        $this->createTable('extensions', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'exten' => Schema::TYPE_STRING,
            'branch_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('extensions_branch_id_fkey', 'extensions', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m160503_152554_21 cannot be reverted.\n";

        return false;
    }

}
