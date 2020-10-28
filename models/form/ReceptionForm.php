<?php

namespace app\models\form;

use app\models\DirectionItem;
use app\models\Reception;
use app\models\ReceptionVar;
use Yii;
use yii\base\Model;
use app\models\Template;

class ReceptionForm extends Model {

    public $template_id;
    /** @var  $_dirItem DirectionItem */
    protected $_dirItem;
    protected $_draft;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['template_id'], 'required', 'message' => 'Требуется заполнить'],
            [['template_id'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'template_id' => 'Выберите шаблон осмотра'
        ];
    }

    /**
     * @return Reception|array|null|\yii\db\ActiveRecord
     */
    public function getDraft() {
        if (!$this->_draft) {
            $model = Reception::find()
                ->where([
                    'direction_id' => $this->_dirItem->id,
                    'doctor_id' => Yii::$app->user->identity->doctor_id,
                    'draft' => true
                ])
                ->one();
            if (!$model) {
                $model = new Reception();
                $model->setAttributes([
                    'direction_id' => $this->_dirItem->id,
                    'doctor_id' => Yii::$app->user->identity->doctor_id,
                    'draft' => true,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ]);
                $model->save(false);
            }
            $this->_draft = $model;
        }

        return $this->_draft;
    }

    public function getTemplate() {
        if ($this->template_id) {
            $model = Template::findOne(['id' => $this->template_id]);
            if ($model) {
                return $model;
            }
        }
    }

    public function setDirItem(DirectionItem $model) {
        $this->_dirItem = $model;
        $this->template_id = $this->draft->template_id;
    }

    public function getDirItem() {
        return $this->_dirItem;
    }

    public function commonValidate() {
        $errors = [];
        $templateValues = json_decode(Yii::$app->request->post('templateValues', '[]'), true);
        foreach ($templateValues as $key => $row) {
            if ($row == null) {
                $errors['templateValues[' . $key . ']'] = 'Требуется заполнить';
            }
        }

        return [];
    }

    public function process() {
        $template = Template::findOne(['id' => $this->template_id]);
        $model = $this->getDraft();
        $model->setAttributes([
            'template_id' => $template->id,
            'html' => $template->html,
            'draft' => false
        ]);
        $model->save();

        $templateValues = json_decode(Yii::$app->request->post('templateValues', '[]'), true);
        foreach ($templateValues as $id => $value) {
            $receptionVar = new ReceptionVar();
            $receptionVar->setAttributes([
                'var_id' => $id,
                'value' => $value
            ]);
            $receptionVar->reception_id = $model->id;
            $receptionVar->save();
        }

        return true;
    }

    public function autosave() {
        $template = Template::findOne(['id' => $this->template_id]);
        $model = $this->getDraft();
        $model->setAttributes([
            'template_id' => $template->id,
            'html' => $template->html,
            'draft' => true
        ]);
        $model->save();

//        foreach ($model->receptionVars as $rel) {
//            $rel->delete();
//        }

        $templateValues = json_decode(Yii::$app->request->post('templateValues', '[]'), true);
        foreach ($templateValues as $id => $value) {
            $receptionVar = ReceptionVar::find()
                ->where([
                    'reception_id' => $model->id,
                    'var_id' => $id
                ])
                ->one();
            if (!$receptionVar) {
                $receptionVar = new ReceptionVar();
                $receptionVar->setAttributes([
                    'var_id' => $id,
                    'reception_id' => $model->id
                ]);
            }
            $receptionVar->value = $value;
            $receptionVar->save();
        }

        return date('H:i');
    }
}
