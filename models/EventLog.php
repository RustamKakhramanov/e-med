<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event_log".
 *
 * @property integer $id
 * @property string $date
 * @property integer $event_id
 * @property integer $user_id
 * @property string $field
 * @property string $before_value
 * @property string $after_value
 *
 * @property Event $event
 */
class EventLog extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'event_log';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['date'], 'safe'],
            [['event_id'], 'required'],
            [['event_id', 'user_id'], 'integer'],
            [['before_value', 'after_value'], 'string'],
            [['field'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'date' => 'Время изменения',
            'event_id' => 'Событие',
            'user_id' => 'Пользователь',
            'field' => 'Поле',
            'before_value' => 'Исходное значение',
            'after_value' => 'Измененное значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent() {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * создать из измененных полей
     * @param type $id event_id
     * @param type $items
     */
    public static function createLog($id, $items) {
        foreach ($items as $field => $data) {
            $model = new self;
            $model->setAttributes([
                'date' => date('Y-m-d H:i:s'),
                'event_id' => $id,
                'user_id' => @Yii::$app->user->identity->id,
                'field' => $field,
                'before_value' => (string) $data['before_value'],
                'after_value' => (string) $data['after_value']
            ]);
            $model->save();
        }
    }
    
    public function printBeforeValue(){
        
    }

}
