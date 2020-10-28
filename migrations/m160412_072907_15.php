<?php

/**
 * поля для филиалов
 */

use yii\db\Schema;
use yii\db\Migration;

class m160412_072907_15 extends Migration {

    public function up() {
        $this->createTable('branch', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'deleted' => Schema::TYPE_BOOLEAN
        ]);
        
        $this->addColumn('check', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('check_branch_id_fkey', 'check', 'branch_id', 'branch', 'id');
        
        $this->addColumn('contract', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('contract_branch_id_fkey', 'contract', 'branch_id', 'branch', 'id');
        
        $this->addColumn('contractor', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('contractor_branch_id_fkey', 'contractor', 'branch_id', 'branch', 'id');
        
        $this->addColumn('dest_template', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('dest_template_branch_id_fkey', 'dest_template', 'branch_id', 'branch', 'id');
        
        $this->addColumn('device', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('device_branch_id_fkey', 'device', 'branch_id', 'branch', 'id');
        
        $this->addColumn('direction', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('direction_branch_id_fkey', 'direction', 'branch_id', 'branch', 'id');
        
        $this->addColumn('doctor', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('doctor_branch_id_fkey', 'doctor', 'branch_id', 'branch', 'id');
        
        $this->addColumn('event', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('event_branch_id_fkey', 'event', 'branch_id', 'branch', 'id');
        
        $this->addColumn('patients', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('patients_branch_id_fkey', 'patients', 'branch_id', 'branch', 'id');
        
        $this->addColumn('price', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('price_branch_id_fkey', 'price', 'branch_id', 'branch', 'id');
        
        $this->addColumn('price_group', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('price_group_branch_id_fkey', 'price_group', 'branch_id', 'branch', 'id');
        
        $this->addColumn('reception', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('reception_branch_id_fkey', 'reception', 'branch_id', 'branch', 'id');
        
        $this->addColumn('shift', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('shift_branch_id_fkey', 'shift', 'branch_id', 'branch', 'id');
        
        $this->addColumn('speciality', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('speciality_branch_id_fkey', 'speciality', 'branch_id', 'branch', 'id');
        
        $this->addColumn('subdivision', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('subdivision_branch_id_fkey', 'subdivision', 'branch_id', 'branch', 'id');
        
        $this->addColumn('template', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('template_branch_id_fkey', 'template', 'branch_id', 'branch', 'id');
        
        $this->addColumn('user', 'branch_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('user_branch_id_fkey', 'user', 'branch_id', 'branch', 'id');
    }

    public function down() {
        echo "m160412_072907_15 cannot be reverted.\n";

        return false;
    }

}
