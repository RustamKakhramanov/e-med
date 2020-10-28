<?php

use yii\db\Migration;

/**
 * Class m180514_104215_patient_remove_jsonb
 */
class m180514_104215_patient_remove_jsonb extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->alterColumn('patients', 'data_document', $this->text());
        $this->alterColumn('patients', 'data_work', $this->text());
        $this->alterColumn('patients', 'data_family', $this->text());

        $this->update('patients', [
            'data_document' => '[]',
            'data_work' => '[]',
            'data_family' => '[]'
        ]);

        $this->alterColumn('patients', 'birthday', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180514_104215_patient_remove_jsonb cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_104215_patient_remove_jsonb cannot be reverted.\n";

        return false;
    }
    */
}
