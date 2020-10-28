<?php

use yii\db\Schema;
use yii\db\Migration;

class m160108_102711_4 extends Migration {

    public function up() {
        $this->createTable('reception_diagnosis', [
            'id' => Schema::TYPE_PK,
            'reception_id' => Schema::TYPE_INTEGER,
            'mkb_id' => Schema::TYPE_INTEGER,
            'main' => Schema::TYPE_BOOLEAN
        ]);
        
        $this->addForeignKey('reception_diagnosis_reception_id_fkey', 'reception_diagnosis', 'reception_id', 'reception', 'id');
        $this->addForeignKey('reception_diagnosis_mkb_id_fkey', 'reception_diagnosis', 'mkb_id', 'mkb', 'id');

    }

    public function down() {
        echo "m160108_102711_4 cannot be reverted.\n";

        return false;
    }

}
