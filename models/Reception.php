<?php

namespace app\models;

use Yii;

class Reception extends \app\models\base\Reception {

    public function rules() {
        return [
            [['template_id'], 'required', 'message' => 'Выберите шаблон'],
            [['doctor_id', 'direction_id', 'template_id', 'branch_id'], 'default', 'value' => null],
            [['doctor_id', 'direction_id', 'template_id', 'branch_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['deleted', 'draft'], 'boolean'],
            [['html'], 'string'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
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
    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
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
    public function getReceptionDiagnoses() {
        return $this->hasMany(ReceptionDiagnosis::className(), ['reception_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptionVars() {
        return $this->hasMany(ReceptionVar::className(), ['reception_id' => 'id']);
    }

    public function getDirectionItem() {
        return $this->hasOne(DirectionItem::className(), ['id' => 'direction_id']);
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            $this->updated = date('Y-m-d H:i:s');

            if (!$this->id) {
                $this->created = date('Y-m-d H:i:s');
            }

            return true;
        }
        return false;
    }

    /**
     * сохранить показатели, формат
     * [
     *  uid => [
     *      id => int,
     *      value => str
     *  ]
     * ]
     * @param type $data
     */
    public function saveVars($data) {
        //удалить старые значения параметров
        $oldVars = ReceptionVar::find()->where(['reception_id' => $this->id])->all();
        foreach ($oldVars as $var) {
            $var->delete();
        }

        //записать новые
        foreach ($data as $uid => $item) {
            $var = new ReceptionVar();
            $var->setAttributes([
                'reception_id' => $this->id,
                'var_id' => $item['id'],
                'value' => (string)$item['value'],
                'uid' => $uid
            ]);
            $var->save();
        }
    }

    public function saveRel() {
        if ($this->data_diagnoses) {
            //удалить старые диагнозы
            foreach ($this->receptionDiagnoses as $item) {
                $item->delete();
                //$this->unlink('receptionDiagnoses', $item, true);
            }

            //записать новые
            $items = json_decode($this->data_diagnoses, true);
            foreach ($items as $item) {
                $rd = new ReceptionDiagnosis();
                $rd->setAttributes([
                    'reception_id' => $this->id,
                    'mkb_id' => $item['id'],
                    'main' => isset($item['main']) && $item['main']
                ]);
                $rd->save();
            }
        }
    }

    public function getTemplateValues() {
        $values = [];
        foreach ($this->receptionVars as $item) {
            $values[$item->var_id] = $item->value;
        }

        return $values;
    }
}
