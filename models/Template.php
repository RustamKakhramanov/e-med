<?php

namespace app\models;

use Yii;
use yii\widgets\ActiveForm;

class Template extends \app\models\base\Template {

    const SIZE_A4 = 'a4';
    const SIZE_A5 = 'a5';

    protected $_sizeLabels = [
        self::SIZE_A4 => 'A4',
        self::SIZE_A5 => 'A5'
    ];

    public static $sizes = [
        self::SIZE_A4 => 620, //877
        self::SIZE_A5 => 437 //320
        //1.4187643
    ];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['created', 'updated'], 'safe'],
            [['deleted', 'draft'], 'boolean'],
            [['user_id', 'name'], 'required', 'message' => 'Требуется заполнить'],
            [['user_id', 'branch_id'], 'default', 'value' => null],
            [['user_id', 'branch_id'], 'integer'],
            [['html'], 'string'],
            [['name', 'size'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'user_id' => 'Автор',
            'draft' => 'флаг черновика',
            'html' => 'Html',
            'branch_id' => 'Branch ID',
            'size' => 'Размер документа'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptions() {
        return $this->hasMany(Reception::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateDocs() {
        return $this->hasMany(TemplateDoc::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateSpecs() {
        return $this->hasMany(TemplateSpec::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVars() {
        return $this->hasMany(TemplateVar::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateVarGroups() {
        return $this->hasMany(TemplateVarGroup::className(), ['template_id' => 'id'])->orderBy([
            TemplateVarGroup::tableName() . '.name' => SORT_ASC
        ]);
    }

    public function commonValidate() {
        return ActiveForm::validate($this) +
            $this->validateAccessSpecs(Yii::$app->request->post('access-spec', [])) +
            $this->validateAccessDocs(Yii::$app->request->post('access-docs', []));
    }

    public function validateAccessSpecs($items) {
        $res = [];
        $usedId = [];
        foreach ($items as $key => $row) {
            if (!$row['spec_id']) {
                $res['access-spec-' . $key . '-spec_id'][] = 'Требуется заполнить';
            } else {
                if (in_array($row['spec_id'], $usedId)) {
                    $res['access-spec-' . $key . '-spec_id'][] = 'Не допускается дублирование';
                } else {
                    $usedId[] = $row['spec_id'];
                }
            }
        }

        return $res;
    }

    public function validateAccessDocs($items) {
        $res = [];
        $usedId = [];
        foreach ($items as $key => $row) {
            if (!$row['doc_id']) {
                $res['access-docs-' . $key . '-doc_id'][] = 'Требуется заполнить';
            } else {
                if (in_array($row['doc_id'], $usedId)) {
                    $res['access-docs-' . $key . '-doc_id'][] = 'Не допускается дублирование';
                } else {
                    $usedId[] = $row['doc_id'];
                }
            }
        }

        return $res;
    }

    public function deleteSafe() {
        $this->deleted = true;
        $this->save(false);
    }

    /**
     * сохранить связь
     */
    public function saveRels() {
        $this
            ->_saveAccessSpecs()
            ->_saveAccessDocs();
    }

    protected function _saveAccessSpecs() {
        foreach ($this->templateSpecs as $rel) {
            $rel->delete();
        }

        foreach (Yii::$app->request->post('access-spec', []) as $row) {
            $rel = new TemplateSpec();
            $rel->setAttributes([
                'template_id' => $this->id,
                'spec_id' => $row['spec_id']
            ]);
            $rel->save();
        }

        return $this;
    }

    protected function _saveAccessDocs() {
        foreach ($this->templateDocs as $rel) {
            $rel->delete();
        }

        foreach (Yii::$app->request->post('access-docs', []) as $row) {
            $rel = new TemplateDoc();
            $rel->setAttributes([
                'template_id' => $this->id,
                'doc_id' => $row['doc_id']
            ]);
            $rel->save();
        }

        return $this;
    }

    /**
     * доп показатели шаблона по группам
     * @return array
     */
    public function getGroupVars() {
        $data = [];
        foreach ($this->templateVarGroups as $group) {
            $groupData = [
                'id' => $group->id,
                'name' => $group->name,
                'items' => []
            ];
            foreach ($group->templateVars as $var) {
                $groupData['items'][] = $var;
            }
            $data[] = $groupData;
        }

        //без группы
        $items = TemplateVar::find()
            ->where([
                'template_id' => $this->id,
                'group_id' => null
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        if ($items) {
            $data[] = [
                'id' => false,
                'name' => 'Без группы',
                'items' => $items
            ];
        }

        return $data;
    }

    /**
     * список показателей шаблона id => model
     * @return array
     */
    public function getFlatVars() {
        $data = [];
        $items = TemplateVar::find()
            ->where([
                'template_id' => $this->id
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        foreach ($items as $item) {
            $data[$item->id] = $item->toArray();
        }

        return $data;
    }


    protected function _getRelValue($method, $field, DirectionItem $directionItem, Doctor $doctor) {
        $value = null;
        try {
            switch ($method) {
                case 'patient':
                    $value = $directionItem->direction->patient->$field;
                    break;
                case 'direction':
                    if ($field == 'createdPrint') {
                        $value = date('d.m.Y', strtotime($directionItem->direction->created));
                    } else {
                        $value = $directionItem->$field;
                    }
                    break;
                case 'doctor':
                    $value = $doctor->$field;
                    break;
            }
        } catch (\Exception $e) {
//            dd([
//                $e->getMessage(),
//                $method,
//                $field
//            ]);
//            exit;
        }

        return $value;
    }

    /**
     * значения rel переменных
     * @param $directionItem
     * @return array
     */
    public function relValues($directionItem, $doctor) {
        $data = [];
        $groups = TemplateVarGroup::find()
            ->where([
                'template_id' => null
            ])
            ->all();

        foreach ($groups as $group) {
            foreach ($group->templateVars as $var) {
                $method = $var->extraData['method'];
                $field = $var->extraData['field'];
                $data[$var->id] = $this->_getRelValue($method, $field, $directionItem, $doctor);
            }
        }


        return $data;
    }

    public function getSizeSelect() {
        return $this->_sizeLabels;
    }

    public function getSizeWidth() {
        return self::$sizes[$this->size];
    }
}
