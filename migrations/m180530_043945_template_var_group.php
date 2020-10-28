<?php

use yii\db\Migration;

class m180530_043945_template_var_group extends Migration {
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('template_var_group', [
            'id' => $this->primaryKey(),
            'template_id' => $this->integer()->comment('Шаблон'),
            'name' => $this->string(255)->notNull()->comment('Название')
        ], $tableOptions);

        $this->addForeignKey('fk_template_var_group__template_id', 'template_var_group', 'template_id', 'template', 'id');

        $this->addColumn('template_var', 'group_id', $this->integer()->comment('Группа'));
        $this->addForeignKey('fk_template_var__group_id', 'template_var', 'group_id', 'template_var_group', 'id');
    }

    public function down() {
        echo "m180530_043945_template_var_group cannot be reverted.\n";

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
