<?php

namespace app\components\rbac;

use Yii;
use yii\rbac\Rule;
use yii\helpers\ArrayHelper;
use app\models\User;

class UserRoleRule extends Rule {

    public $name = 'userRole';

    public function execute($user, $item, $params) {

        if (!\Yii::$app->user->isGuest) {

            $user = ArrayHelper::getValue($params, 'user', User::findOne($user));

            //наследование ролей
            if ($user) {
                $role = $user->role_id; //Значение из поля role базы данных
                if ($item->name === 'root') {
                    return $role == User::ROLE_ROOT;
                } elseif ($item->name === 'admin') {
                    return $role == User::ROLE_ADMIN;
                } elseif ($item->name === 'specialist') {
                    return $role == User::ROLE_SPECIALIST;
                } elseif ($item->name === 'operator') {
                    return $role == User::ROLE_OPERATOR;
                }
            }
        }
        return false;
    }

}
