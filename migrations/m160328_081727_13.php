<?php

use yii\db\Schema;
use yii\db\Migration;

class m160328_081727_13 extends Migration {

    public function up() {
        $this->update('reception', ['deleted' => 0, 'draft' => 0]);
        $this->alterColumn('reception', 'deleted', 'SET NOT NULL');
        $this->alterColumn('reception', 'draft', 'SET NOT NULL');
    }

    public function down() {
        echo "m160328_081727_13 cannot be reverted.\n";

        return false;
    }
}
