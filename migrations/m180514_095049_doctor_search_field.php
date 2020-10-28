<?php

use yii\db\Migration;

/**
 * Class m180514_095049_doctor_search_field
 */
class m180514_095049_doctor_search_field extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('doctor', 'search_field', $this->string(255));
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("CREATE INDEX trgm_idx_doctor_fio ON doctor USING gin (search_field gin_trgm_ops);");
        $command->query();

        $items = \app\models\Doctor::find()->asArray()->all();
        foreach ($items as $item) {
            $this->update('doctor', [
                'search_field' => implode(' ', [
                    $item['last_name'],
                    $item['first_name'],
                    $item['middle_name'],
                ])
            ], [
                'id' => $item['id']
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180514_095049_doctor_search_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_095049_doctor_search_field cannot be reverted.\n";

        return false;
    }
    */
}
