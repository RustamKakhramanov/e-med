<?php

use yii\db\Migration;

/**
 * Class m180512_135852_patient_search_field
 */
class m180512_135852_patient_search_field extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('patients', 'search_field', $this->string(255));
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("CREATE INDEX trgm_idx_patients_fio ON patients USING gin (search_field gin_trgm_ops);");
        $command->query();

        $items = \app\models\Patients::find()->asArray()->all();
        foreach ($items as $item) {
            $this->update('patients', [
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
        echo "m180512_135852_patient_search_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180512_135852_patient_search_field cannot be reverted.\n";

        return false;
    }
    */
}
