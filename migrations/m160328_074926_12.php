<?php

/**
 * флаг черновика приема
 */
use yii\db\Schema;
use yii\db\Migration;

class m160328_074926_12 extends Migration {

    public function up() {
        $this->addColumn('reception', 'draft', Schema::TYPE_BOOLEAN);
    }

    public function down() {
        echo "m160328_074926_12 cannot be reverted.\n";

        return false;
    }

}
