<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use app\components\rbac\UserRoleRule;

class RbacController extends Controller {

    private $perms = [
        'doctor' => 'Справочник врачей',
        'patient' => 'Справочник пациентов',
        'speciality' => 'Специализации',
        'subdivision' => 'Подразделения',
        'schedule' => 'Общее расписание',
        'event' => 'События',
        'price' => 'Прайс',
        'direction' => 'Направления',
        'template' => 'Шаблоны протокола осмотра',
        'dest-template' => 'Шаблоны стандартов лечения',
        'contractor' => 'Контрагенты',
        'contract' => 'Договоры',
        'check' => 'Чеки',
        'report' => 'Отчеты',
        'users' => 'Пользователи',
        'device' => 'Устройства',
        'dashboard' => 'Рабочее место врача',
        'branch' => 'Филиалы'
    ];

    public function actionInit() {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные
        //Создадим для примера права для доступа к админке
//        $dashboard = $auth->createPermission('dashboard');
//        $dashboard->description = 'Админ панель';
//        $auth->add($dashboard);
        //Включаем наш обработчик
        $rule = new UserRoleRule();
        $auth->add($rule);

        $role = $auth->createRole('specialist');
        $role->description = User::$arRoleLabels[User::ROLE_SPECIALIST];
        $role->ruleName = $rule->name;
        $auth->add($role);
        $auth->assign($role, 5);

        $role = $auth->createRole('operator');
        $role->description = User::$arRoleLabels[User::ROLE_OPERATOR];
        $role->ruleName = $rule->name;
        $auth->add($role);

        $role = $auth->createRole('admin');
        $role->description = User::$arRoleLabels[User::ROLE_ADMIN];
        $role->ruleName = $rule->name;
        $auth->add($role);
        //$auth->addChild($admin, $patient);

        $auth->assign($role, 1);

        foreach ($this->perms as $key => $desc) {
            $perm = $auth->createPermission($key);
            $perm->description = $desc;
            $auth->add($perm);
            $auth->addChild($role, $perm);
        }
        
        $role = $auth->createRole('root');
        $role->description = User::$arRoleLabels[User::ROLE_ROOT];
        $role->ruleName = $rule->name;
        $auth->add($role);
//        
//        foreach ($this->perms as $key => $desc) {
//            $perm = $auth->createPermission($key);
//            $perm->description = $desc;
//            $auth->add($perm);
//            $auth->addChild($role, $perm);
//        }
    }


}
