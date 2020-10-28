<?php
/**
 * дополнение переменных шаблонов
 */
use yii\db\Schema;
use yii\db\Migration;

class m160118_111108_5 extends Migration {

    public function up() {
        $this->addColumn('reception_var', 'uid', Schema::TYPE_STRING);
        $this->addColumn('reception', 'html', Schema::TYPE_TEXT);
    }

    public function down() {
        echo "m160118_111108_5 cannot be reverted.\n";

        return false;
    }
}
