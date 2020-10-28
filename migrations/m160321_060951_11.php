<?php

/**
 * шаблоны назначений допустимые диагнозы и спеки
 */
use yii\db\Schema;
use yii\db\Migration;

class m160321_060951_11 extends Migration {

    public function up() {
        $this->createTable('dest_template_diagnosis', [
            'id' => Schema::TYPE_PK,
            'dest_template_id' => Schema::TYPE_INTEGER,
            'mkb_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('dest_template_diagnosis_mkb_id_fkey', 'dest_template_diagnosis', 'mkb_id', 'mkb', 'id');
        $this->addForeignKey('dest_template_diagnosis_dest_template_id_fkey', 'dest_template_diagnosis', 'dest_template_id', 'dest_template', 'id');
        
        $this->createTable('dest_template_spec', [
            'id' => Schema::TYPE_PK,
            'dest_template_id' => Schema::TYPE_INTEGER,
            'spec_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('dest_template_spec_dest_template_id_fkey', 'dest_template_spec', 'dest_template_id', 'dest_template', 'id');
        $this->addForeignKey('dest_template_spec_spec_id_fkey', 'dest_template_spec', 'spec_id', 'speciality', 'id');
    }

    public function down() {
        echo "m160321_060951_11 cannot be reverted.\n";

        return false;
    }

}
