<?php

/**
 * шаблоны назначений
 */
use yii\db\Schema;
use yii\db\Migration;

class m160320_142004_10 extends Migration {

    public function up() {
        $this->createTable('dest_template', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING
        ]);
        
        $this->createTable('dest_template_price', [
            'id' => Schema::TYPE_PK,
            'dest_template_id' => Schema::TYPE_INTEGER,
            'price_id' => Schema::TYPE_INTEGER
        ]);
        
        $this->addForeignKey('dest_template_price_dest_template_id_fkey', 'dest_template_price', 'dest_template_id', 'dest_template', 'id');
        $this->addForeignKey('dest_template_price_price_id_fkey', 'dest_template_price', 'price_id', 'price', 'id');
    }

    public function down() {
        echo "m160320_142004_10 cannot be reverted.\n";

        return false;
    }

}
