<?php

use yii\db\Migration;

class m180530_045712_template_varr_data extends Migration {

    protected $_data = [
        'Пациент' => [
            [
                'type' => 'rel',
                'name' => 'ФИО (полное)',
                'extra' => '{"method":"patient","field":"fio"}'
            ],
            [
                'type' => 'rel',
                'name' => 'ФИО (сокращенное)',
                'extra' => '{"method":"patient","field":"initials"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Пол',
                'extra' => '{"method":"patient","field":"sexPrint"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Дата рождения',
                'extra' => '{"method":"patient","field":"birthdayPrint"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Возраст',
                'extra' => '{"method":"patient","field":"age"}'
            ],
            [
                'type' => 'rel',
                'name' => 'ИИН',
                'extra' => '{"method":"patient","field":"iin"}'
            ]
        ],
        'Направление' => [
            [
                'type' => 'rel',
                'name' => 'Услуга',
                'extra' => '{"method":"direction","field":"serviceName"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Номер',
                'extra' => '{"method":"direction","field":"numberPrint"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Стоимость',
                'extra' => '{"method":"direction","field":"serviceCost"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Дата',
                'extra' => '{"method":"direction","field":"createdPrint"}'
            ],
        ],
        'Специалист' => [
            [
                'type' => 'rel',
                'name' => 'ФИО',
                'extra' => '{"method":"doctor","field":"fio"}'
            ],
            [
                'type' => 'rel',
                'name' => 'Основная специализация',
                'extra' => '{"method":"doctor","field":"mainSpecPrint"}'
            ],
        ]
    ];

    public function up() {
        $this->truncateTable('reception_var');
        $this->delete('template_var');

        foreach ($this->_data as $groupName => $items) {
            $group = new \app\models\TemplateVarGroup();
            $group->name = $groupName;
            $group->save();

            foreach ($items as $item) {
                $var = new \app\models\TemplateVar();
                $var->setAttributes($item);
                $var->group_id = $group->id;
                $var->save();
            }
        }
    }

    public function down() {
        echo "m180530_045712_template_varr_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
