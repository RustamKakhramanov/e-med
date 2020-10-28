<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tv_schedule".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $data
 * @property integer $branch_id
 * @property string $template 
 *
 * @property Branch $branch
 */
class TvSchedule extends \yii\db\ActiveRecord {

    private $_widgets = [
        'Schedule' => 'График работы',
        'Weather' => 'Погода',
        'Youtube' => 'Youtube видео',
        'Oncall' => 'Дежурные врачи',
        'Discharged' => 'Сегодня выписываются',
    ];
    private $_templates = [
        'Simple' => 'Стандартный',
        'Basic' => 'Стандартный (2 колонки)'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tv_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'code', 'branch_id', 'template'], 'required'],
            [['data'], 'string'],
            [['branch_id'], 'integer'],
            [['name', 'code', 'template'], 'string', 'max' => 255],
            [['code'], 'unique', 'message' => 'Уже используется'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'code' => 'Код',
            'data' => 'Data',
            'branch_id' => 'Branch ID',
            'template' => 'Шаблон'
        ];
    }

    public function setDefault() {
        $this->data = '{}';
        $this->template = 'Simple';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getWidgets() {
        return $this->_widgets;
    }

    public function getTemplates() {
        return $this->_templates;
    }

    /**
     * виджеты с данными
     */
    public function getWidgetsList() {
        $class = '\app\models\tv\template\\' . $this->template;
        $template = new $class;
        $list = $template->getWidgets();
        $tvData = json_decode($this->data, true);
        foreach ($list as $k => $w) {
            if (isset($tvData[$w->name])) {
                $list[$k]->setAttributes($tvData[$w->name]);
            }
        }
        
        return $list;
    }

}
