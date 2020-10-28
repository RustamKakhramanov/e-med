<?php

use yii\db\Schema;
use yii\db\Migration;

class m160328_082846_14 extends Migration {

    public function up() {
        $this->alterColumn('reception', 'deleted', 'SET DEFAULT false');
        $this->alterColumn('reception', 'draft', 'SET DEFAULT false');
    }

    public function down() {
        echo "m160328_082846_14 cannot be reverted.\n";

        return false;
    }

}
