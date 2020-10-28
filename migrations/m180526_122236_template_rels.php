<?php

use yii\db\Migration;

/**
 * Class m180526_122236_template_rels
 */
class m180526_122236_template_rels extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->dropColumn('template', 'doctor_id');
        $this->dropColumn('template', 'spec_id');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('template_spec', [
            'id' => $this->primaryKey(),
            'spec_id' => $this->integer()->notNull()->comment('Специализация'),
            'template_id' => $this->integer()->notNull()->comment('Шаблон')
        ], $tableOptions);

        $this->addForeignKey('fk_template_spec__spec_id', 'template_spec', 'spec_id', 'speciality', 'id');
        $this->addForeignKey('fk_template_spec__template_id', 'template_spec', 'template_id', 'template', 'id');

        $this->createTable('template_doc', [
            'id' => $this->primaryKey(),
            'doc_id' => $this->integer()->notNull()->comment('Доктор'),
            'template_id' => $this->integer()->notNull()->comment('Шаблон')
        ], $tableOptions);

        $this->addForeignKey('fk_template_doc__doc_id', 'template_doc', 'doc_id', 'doctor', 'id');
        $this->addForeignKey('fk_template_doc__template_id', 'template_doc', 'template_id', 'template', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180526_122236_template_rels cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180526_122236_template_rels cannot be reverted.\n";

        return false;
    }
    */
}
