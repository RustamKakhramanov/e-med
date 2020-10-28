<?php

use yii\db\Migration;

/**
 * Class m180512_122209_new_directions
 */
class m180512_122209_new_directions extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->dropForeignKey('reception_direction_id_fkey', 'reception');
        $this->dropForeignKey('check_items_direction_id_fkey', 'check_items');
        $this->dropTable('direction');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('direction', [
            'id' => $this->primaryKey(),
            'created' => $this->dateTime()->comment('Дата создания'),
            'user_id' => $this->integer()->notNull()->comment('Пользователь'),
            'branch_id' => $this->integer()->notNull()->comment('Филиал'),
            'patient_id' => $this->integer()->notNull()->comment('Пациент')
        ], $tableOptions);

        $this->addForeignKey('fk_direction__user_id', 'direction', 'user_id', 'user', 'id');
        $this->addForeignKey('fk_direction__branch_id', 'direction', 'branch_id', 'branch', 'id');
        $this->addForeignKey('fk_direction__patient_id', 'direction', 'patient_id', 'patients', 'id');

        $this->createTable('direction_item', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer()->comment('Заказ'),
            'doctor_id' => $this->integer()->comment('Специалист'),
            'price_id' => $this->integer()->notNull()->comment('Услуга'),
            'cost' => $this->float()->notNull()->defaultValue(0)->comment('Стоимость'),
            'count' => $this->integer()->notNull()->comment('Кол-во'),
            'paid' => $this->boolean()->defaultValue(false)->comment('Оплачено'),
            'canceled' => $this->boolean()->defaultValue(false)->comment('Отменено'),
            'cancel_reason' => $this->text()->comment('Причина отмены')
        ], $tableOptions);

        $this->addForeignKey('fk_direction_item__direction_id', 'direction_item', 'direction_id', 'direction', 'id');
        $this->addForeignKey('fk_direction_item__doctor_id', 'direction_item', 'doctor_id', 'doctor', 'id');
        $this->addForeignKey('fk_direction_item__price_id', 'direction_item', 'price_id', 'price', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180512_122209_new_directions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180512_122209_new_directions cannot be reverted.\n";

        return false;
    }
    */
}
