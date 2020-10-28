<?php

use yii\db\Migration;

/**
 * Class m180606_035533_direction_clear_empty
 */
class m180606_035533_direction_clear_empty extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->delete('reception_diagnosis');
        $items = \app\models\Reception::find()->all();
        foreach ($items as $item) {
            $rel = \app\models\DirectionItem::findOne(['id' => $item->direction_id]);
            if (!$rel) {
                $item->delete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m180606_035533_direction_clear_empty cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_035533_direction_clear_empty cannot be reverted.\n";

        return false;
    }
    */
}
