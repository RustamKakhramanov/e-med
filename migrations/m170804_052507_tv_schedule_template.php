<?php

use yii\db\Migration;

class m170804_052507_tv_schedule_template extends Migration
{
    public function up()
    {
        $this->addColumn('tv_schedule', 'template', $this->string(256)->notNull()->comment('Шаблон'));
    }

    public function down()
    {
        echo "m170804_052507_tv_schedule_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
