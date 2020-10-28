<?php

use yii\db\Migration;

/**
 * Class m180510_103019_direction_divide_date
 */
class m180510_103019_direction_divide_date extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->alterColumn('direction', 'date', $this->date()->comment('Дата'));
        $this->addColumn('direction', 'time', $this->string(5)->comment('Время')->after('date'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180510_103019_direction_divide_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_103019_direction_divide_date cannot be reverted.\n";

        return false;
    }
    */
}
