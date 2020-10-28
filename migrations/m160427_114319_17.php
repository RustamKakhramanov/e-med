<?php

use yii\db\Schema;
use yii\db\Migration;

class m160427_114319_17 extends Migration {

    public function up() {
        $this->addColumn('branch', 'api_key', Schema::TYPE_STRING);        
    }

    public function down() {
        echo "m160427_114319_17 cannot be reverted.\n";

        return false;
    }

}
