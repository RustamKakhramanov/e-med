<?php

use yii\db\Migration;

class m170815_013920_doc_photo extends Migration
{
    public function up()
    {
        $this->addColumn('doctor', 'photo', $this->string(255)->defaultValue(NULL)->comment('Фотография'));
    }

    public function down()
    {
        echo "m170815_013920_doc_photo cannot be reverted.\n";

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
