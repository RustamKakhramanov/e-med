<?php

namespace app\models;

use Yii;
use Carbon\Carbon;

/**
 * This is the model class for table "user_session".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $closed
 *
 * @property User $user
 */
class UserSession extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_session';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['closed'], 'boolean'],
            [['number', 'ip'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'number' => 'Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->updated_at = date('Y-m-d H:i:s');
            if (!$this->id) {
                $this->ip = Yii::$app->request->userIP;
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * проверка что пользователь не имеет активной сессии
     * @param User $user
     * @return type
     */
    public static function checkUniqUser($user) {
        $date = Carbon::create();
        $date->subMinutes(1);

        $query = \app\models\UserSession::find()
                ->where(['user_id' => $user->id])
                ->andWhere([
            'and',
            'closed = :closed',
            'updated_at >= :date'
                ], [
            ':closed' => false,
            ':date' => $date->format('Y-m-d H:i:s')
        ]);

        return $query->count();
    }

    /**
     * проверка что номер не используется в активных сессиях
     * @param str $number
     * @return type
     */
    public static function checkUniqNumber($number) {
        $date = Carbon::create();
        $date->subMinutes(1);

        $query = \app\models\UserSession::find()
                ->where(['number' => $number])
                ->andWhere([
            'and',
            'closed = :closed',
            'updated_at >= :date'
                ], [
            ':closed' => false,
            ':date' => $date->format('Y-m-d H:i:s')
        ]);


        return $query->count();
    }

    /**
     * количество успешных исходящих
     * @return type
     */
    public function getCountOutgoing($type) {
        return LogOutgoing::find()
                        ->where([
                            'sess_id' => $this->id,
                            'type' => $type,
                            'answer' => true
                        ])
                        ->count();
    }

}
