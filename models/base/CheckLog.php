<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "check_log".
 *
 * @property int $id
 * @property int $check_id
 * @property int $user_id
 * @property string $date
 * @property string $params
 * @property string $message
 */
class CheckLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_id', 'user_id'], 'default', 'value' => null],
            [['check_id', 'user_id'], 'integer'],
            [['date'], 'safe'],
            [['params', 'message'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_id' => 'Check ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'params' => 'Params',
            'message' => 'Message',
        ];
    }
}
