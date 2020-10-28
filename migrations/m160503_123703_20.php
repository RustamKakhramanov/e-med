<?php

use yii\db\Schema;
use yii\db\Migration;

class m160503_123703_20 extends Migration {

    public function up() {
        $this->addColumn('branch', 'extra', Schema::TYPE_TEXT);
        $items = \app\models\Branch::find()
                ->where('extra is NULL')
                ->all();
        foreach ($items as $item) {
            $item->extra = '[]';
            $item->save(false);
        }
    }

    public function down() {
        echo "m160503_123703_20 cannot be reverted.\n";

        return false;
    }

}
