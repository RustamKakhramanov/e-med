<?php

use yii\db\Migration;

/**
 * Class m180823_124555_cashbox_params
 */
class m180823_124555_cashbox_params extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('cashbox', 'webkassa_id', $this->string(255)->comment('Id в вебкассе'));
        $this->addColumn('cashbox', 'bin', $this->string(255)->comment('БИН'));
        $this->addColumn('cashbox', 'nds_serie', $this->string(255)->comment('НДС Серия'));
        $this->addColumn('cashbox', 'nds_number', $this->string(255)->comment('НДС Номер'));
        $this->addColumn('cashbox', 'operator_name', $this->string(255)->comment('Наименование оператора фискальных данных'));
        $this->addColumn('cashbox', 'kkt', $this->string(255)->comment('ККТ'));
        $this->addColumn('cashbox', 'rnk', $this->string(255)->comment('РНК'));
        $this->addColumn('cashbox', 'org_name', $this->string(255)->comment('Наименование организации'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180823_124555_cashbox_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180823_124555_cashbox_params cannot be reverted.\n";

        return false;
    }
    */
}
