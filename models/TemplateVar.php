<?php

namespace app\models;

class TemplateVar extends \app\models\base\TemplateVar {

    const TYPE_REL = 'rel';
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_SELECT = 'select';
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';

    //типы параметров
    public static $typesAvailable = [
        self::TYPE_REL => 'Ссылка',
        self::TYPE_STRING => 'Строка',
        self::TYPE_INT => 'Целое число',
        self::TYPE_SELECT => 'Выпадающий список',
        self::TYPE_TIME => 'Время',
        self::TYPE_DATE => 'Дата'
    ];

    public function rules() {
        return [
            ['name', 'required', 'message' => 'Обязательно'],
            [['deleted'], 'boolean'],
            [['extra'], 'string'],
            [['template_id', 'group_id'], 'integer'],
            [['name', 'type'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateVarGroup::className(), 'targetAttribute' => ['group_id' => 'id']]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'type' => 'Тип данных',
            'deleted' => 'Deleted',
            'extra' => 'json доп опции',
            'template_id' => 'Template ID',
            'group_id' => 'Группа',
        ];
    }

    public function extraValidate() {
        $errors = [];
        $post = \Yii::$app->request->post('extra', []);
        if ($this->type == 'select') {
            if (!isset($post['select'])) {
                $errors['type'][] = 'Требуется добавить значения';
            } else {
                foreach ($post['select'] as $key => $value) {
                    if ($value == '') {
                        $errors['extra-select-' . $key][] = 'Необходимо заполнить';
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptionVars() {
        return $this->hasMany(ReceptionVar::className(), ['var_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup() {
        return $this->hasOne(TemplateVarGroup::className(), ['id' => 'group_id']);
    }

    //todo разделить на типы и сделать форматы
    public function saveExtra($data) {
        $extra = [];
        if (isset($data[$this->type])) {
            if ($this->type == 'select') {
                $values = [];
                foreach ($data[$this->type] as $value) {
                    $values[$value] = $value;
                }
                $extra = [
                    'values' => $values
                ];
            }
        }

        $this->extra = json_encode($extra);
    }

    public function getExtraData() {
        return json_decode($this->extra, true);
    }

    public function fields() {
        $fields = parent::fields();

        $over = [
            'extra' => function ($model) {
                return json_decode($model->extra, true);
            }
        ];

        return array_merge($fields, $over);
    }

    public function deleteSafe() {
        $this->deleted = 1;
        $this->save(false);
    }

    public function getSelectTypesAvailable(){
        $data = self::$typesAvailable;
        unset($data[self::TYPE_REL]);

        return $data;
    }

    public static function relVarsArray() {
        $items = TemplateVar::find()
            ->where(['type' => self::TYPE_REL])
            ->orderBy('name')
            ->all();

        $vars = [];
        foreach ($items as $item) {
            $extra = json_decode($item->extra, true);
            if (!isset($vars[$extra['group']])) {
                $vars[$extra['group']] = [];
            }

            $vars[$extra['group']][] = $item->toArray();
        }

        //todo сортировка групп по названиям

        return $vars;
    }
}