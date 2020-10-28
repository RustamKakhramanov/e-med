<?php

use yii\db\Migration;

/**
 * Class m180606_021049_template_size
 */
class m180606_021049_template_size extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('template', 'size', $this->string(255)->comment('Размер документа'));
        $this->update('template',[
            'size' => \app\models\Template::SIZE_A4
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180606_021049_template_size cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_021049_template_size cannot be reverted.\n";

        return false;
    }
    */
}
