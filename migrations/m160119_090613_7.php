<?php

/**
 * переменные шаблона ссылочные, пациент
 */
use yii\db\Schema;
use yii\db\Migration;

class m160119_090613_7 extends Migration {

    public function up() {

        $this->insert('template_var', array(
            'name' => 'ФИО (полное)',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'fio'
            ])
        ));
        
        $this->insert('template_var', array(
            'name' => 'ФИО (сокращенное)',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'initials'
            ])
        ));
        
        $this->insert('template_var', array(
            'name' => 'Пол',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'sexPrint'
            ])
        ));
        
        $this->insert('template_var', array(
            'name' => 'Дата рождения',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'birthdayPrint'
            ])
        ));
        
        $this->insert('template_var', array(
            'name' => 'Возраст',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'age'
            ])
        ));
        
        $this->insert('template_var', array(
            'name' => 'Иин',
            'type' => 'rel',
            'extra' => json_encode([
                'group' => 'Пациент',
                'method' => 'patient',
                'field' => 'iin'
            ])
        ));
    }

    public function down() {
        echo "m160119_090613_7 cannot be reverted.\n";

        return false;
    }

}
