<?php
/**
 * пароль для устройств
 */
use yii\db\Schema;
use yii\db\Migration;

class m160122_054338_8 extends Migration {

    public function up() {
        $this->addColumn('device', 'pass', Schema::TYPE_STRING);
    }

    public function down() {
        echo "m160122_054338_8 cannot be reverted.\n";

        return false;
    }
}
