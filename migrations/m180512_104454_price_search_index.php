<?php

use yii\db\Migration;

/**
 * Class m180512_104454_price_search_index
 */
class m180512_104454_price_search_index extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("CREATE EXTENSION pg_trgm;");
        $command->query();
        $command = $connection->createCommand("CREATE INDEX trgm_idx_price_title ON price USING gin (title gin_trgm_ops);");
        $command->query();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180512_104454_price_search_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180512_104454_price_search_index cannot be reverted.\n";

        return false;
    }
    */
}
