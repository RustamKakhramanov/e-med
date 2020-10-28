<?php

use yii\db\Schema;
use yii\db\Migration;

class m160503_111414_19 extends Migration {

    public function up() {
        $this->addColumn('user', 'extra', Schema::TYPE_TEXT);
        $items = \app\models\User::find()
                ->where('extra is NULL')
                ->all();
        foreach ($items as $item) {
            $item->extra = '[]';
            $item->save(false);
        }
    }

    public function down() {
        echo "m160503_111414_19 cannot be reverted.\n";

        return false;
    }
}
