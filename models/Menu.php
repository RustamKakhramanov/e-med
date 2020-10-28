<?php

namespace app\models;

use Yii;

/**
 * LoginForm is the model behind the login form.
 */
class Menu {

    public static $items = [
        'dashboard' => [
            'icon' => 'home',
            'title' => 'Рабочий стол'
        ],
        'patient' => [
            'icon' => 'patients',
            'title' => 'Пациенты'
        ],
        'speciality' => [
            'icon' => 'dir',
            'title' => 'Специализации'
        ],
        'subdivision' => [
            'icon' => 'dir',
            'title' => 'Подразделения'
        ],
        'doctor' => [
            'icon' => 'patients',
            'title' => 'Специалисты'
        ],
        'schedule' => [
            'icon' => 'schedule',
            'title' => 'Расписание'
        ],
        'event' => [
            'icon' => 'events',
            'title' => 'События'
        ],
        'price' => [
            'icon' => 'dir',
            'title' => 'Прайс'
        ],
        'direction' => [
            'icon' => 'direction',
            'title' => 'Направления'
        ],
        'template' => [
            'icon' => 'dir',
            'title' => 'Шаблоны протокола осмотра'
        ],
        'contractor' => [
            'icon' => 'dir',
            'title' => 'Контрагенты'
        ],
        'contract' => [
            'icon' => 'dir',
            'title' => 'Договоры'
        ],
        'check' => [
            'icon' => 'dir',
            'title' => 'Чеки'
        ],
        'report' => [
            'icon' => 'dir',
            'title' => 'Отчет направления',
            'link' => '/report/day'
        ],
        'report-queue' => [
            'icon' => 'dir',
            'title' => 'Отчет очереди',
            'link' => '/report-queue'
        ],
        'report-operator' => [
            'icon' => 'dir',
            'title' => 'Отчет оператора',
            'link' => '/report-operator'
        ],
        'report-operator-detail' => [
            'icon' => 'dir',
            'title' => 'Отчет оператора детальный',
            'link' => '/report-operator/detailed'
        ],
        'report-operator-summary' => [
            'icon' => 'dir',
            'title' => 'Отчет операторов сводный',
            'link' => '/report-operator/summary'
        ],
        'supervisor' => [
            'icon' => 'schedule',
            'title' => 'Панель супервизора',
            'link' => '/supervisor'
        ],
        'branch' => [
            'icon' => 'dir',
            'title' => 'Филиалы'
        ],
        'users' => [
            'icon' => 'patients',
            'title' => 'Пользователи'
        ],        
        'device' => [
            'icon' => 'dir',
            'title' => 'Устройства'
        ],
        'tv' => [
            'icon' => 'dir',
            'title' => 'Tv'
        ],
        'dest-template' => [
            'icon' => 'dir',
            'title' => 'Шаблоны стандартов лечения'
        ],
        'reception' => [
            'icon' => 'dir',
            'title' => 'Осмотры'
        ],
        'cashier' => [
            'icon' => 'home',
            'title' => 'Рабочий стол',
            'link' => '/cashier/index'
        ],
        'cashier-checks' => [
            'icon' => 'dir',
            'title' => 'Чеки',
            'link' => '/cashier/checks'
        ],
        'cashier-report' => [
            'icon' => 'dir',
            'title' => 'Отчет кассира',
            'link' => '/cashier/report'
        ],
        'cashbox' => [
            'icon' => 'dir',
            'title' => 'Кассы',
            'link' => '/cashbox'
        ]
    ];

}
