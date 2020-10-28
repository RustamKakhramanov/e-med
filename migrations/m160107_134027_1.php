<?php

use yii\db\Schema;
use yii\db\Migration;

class m160107_134027_1 extends Migration {

    public function up() {
        
        $this->alterColumn('user', 'created_at', Schema::TYPE_TIMESTAMP . '(6)');
        $this->alterColumn('user', 'updated_at', Schema::TYPE_TIMESTAMP . '(6)');
        $this->addColumn('user', 'doctor_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('user_doctor_id_fkey', 'user', 'doctor_id', 'doctor', 'id');

        $this->createTable('template', [
            'id' => Schema::TYPE_PK,
            'doctor_id' => Schema::TYPE_INTEGER,
            'spec_id' => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_TIMESTAMP . '(6)',
            'updated' => Schema::TYPE_TIMESTAMP . '(6)',
            'deleted' => Schema::TYPE_BOOLEAN,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'draft' => Schema::TYPE_BOOLEAN . ' DEFAULT true NOT NULL',
            'html' => Schema::TYPE_TEXT
        ]);

        $this->addForeignKey('template_doctor_id_fkey', 'template', 'doctor_id', 'doctor', 'id');
        $this->addForeignKey('template_spec_id_fkey', 'template', 'spec_id', 'speciality', 'id');

        $this->createTable('template_var', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'type' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'deleted' => Schema::TYPE_BOOLEAN,
            'extra' => Schema::TYPE_TEXT,
            'template_id' => Schema::TYPE_INTEGER
        ]);

        $this->addForeignKey('template_var_template_id_fkey', 'template_var', 'template_id', 'template', 'id');
    }

    public function down() {
        echo "m160107_134027_1 cannot be reverted.\n";
        return false;
    }

}
