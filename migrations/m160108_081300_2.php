<?php

use yii\db\Schema;
use yii\db\Migration;

class m160108_081300_2 extends Migration {

    public function up() {
        $this->createTable('reception', [
            'id' => Schema::TYPE_PK,
            'doctor_id' => Schema::TYPE_INTEGER,
            'direction_id' => Schema::TYPE_INTEGER,
            'created' => Schema::TYPE_TIMESTAMP . '(6)',
            'updated' => Schema::TYPE_TIMESTAMP . '(6)',
            'template_id' => Schema::TYPE_INTEGER,
            'deleted' => Schema::TYPE_BOOLEAN
        ]);
        
        $this->addForeignKey('reception_doctor_id_fkey', 'reception', 'doctor_id', 'doctor', 'id');
        $this->addForeignKey('reception_direction_id_fkey', 'reception', 'direction_id', 'direction', 'id');
        $this->addForeignKey('reception_template_id_fkey', 'reception', 'template_id', 'template', 'id');
        
        $this->createTable('reception_var', [
            'id' => Schema::TYPE_PK,
            'reception_id' => Schema::TYPE_INTEGER,
            'var_id' => Schema::TYPE_INTEGER,
            'value' => Schema::TYPE_TEXT
        ]);
        
        $this->addForeignKey('reception_var_reception_id_fkey', 'reception_var', 'reception_id', 'reception', 'id');
        $this->addForeignKey('reception_var_template_var_id_fkey', 'reception_var', 'var_id', 'template_var', 'id');

    }

    public function down() {
        echo "m160108_081300_2 cannot be reverted.\n";

        return false;
    }

}
