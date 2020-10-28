<?php

/**
 * генерация апи ключей
 */

use yii\base\Security;
use yii\db\Migration;

class m160427_114644_18 extends Migration {

    public function up() {
        $branches = \app\models\Branch::find()
                ->where('api_key is NULL')
                ->all();
        $sec = new Security();
        foreach ($branches as $branch) {
            $branch->api_key = $sec->generateRandomString(20);
            $branch->save();
        }
    }

    public function down() {
        echo "m160427_114644_18 cannot be reverted.\n";

        return false;
    }

}
