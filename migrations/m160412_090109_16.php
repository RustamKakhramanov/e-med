<?php

/**
 * добавление учетки рута
 */
use yii\db\Migration;

class m160412_090109_16 extends Migration {

    public function up() {
        $this->insert('user', array(
            'status_id' => \app\models\User::STATUS_ACTIVE,
            'username' => 'root',
            'password_hash' => Yii::$app->security->generatePasswordHash('123123'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'fio' => 'System Root',
            'role_id' => \app\models\User::ROLE_ROOT
        ));

        $root = \app\models\User::find()->where([
                    'username' => 'root'
                ])->one();
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole('root'), $root->id);
    }

    public function down() {
        echo "m160412_090109_16 cannot be reverted.\n";

        return false;
    }

}
