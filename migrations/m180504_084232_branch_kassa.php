<?php

use yii\db\Migration;

/**
 * Class m180504_084232_branch_kassa
 */
class m180504_084232_branch_kassa extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('branch', 'kassa', $this->boolean()->defaultValue(false)->comment('Использовать Webkassa'));
        $this->addColumn('branch', 'kassa_mode', $this->string(255)->comment('Режим Webkassa'));
        $this->addColumn('branch', 'kassa_url', $this->string(255)->comment('Адрес'));
        $this->addColumn('branch', 'kassa_login', $this->string(255)->comment('Логин'));
        $this->addColumn('branch', 'kassa_password', $this->string(255)->comment('Пароль'));
        $this->addColumn('branch', 'kassa_token', $this->string(255)->comment('Токен'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180504_084232_branch_kassa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_084232_branch_kassa cannot be reverted.\n";

        return false;
    }
    */
}
