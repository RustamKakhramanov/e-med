<?php

namespace app\models;

use Yii;
use Carbon\Carbon;

class Shift extends \app\models\base\Shift {


    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'branch_id', 'cashbox_id'], 'default', 'value' => null],
            [['user_id', 'branch_id', 'cashbox_id'], 'integer'],
            [['start', 'end'], 'safe'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['cashbox_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cashbox::className(), 'targetAttribute' => ['cashbox_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecks() {
        return $this->hasMany(Check::className(), ['shift_id' => 'id']);
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
    public function getCashbox() {
        return $this->hasOne(Cashbox::className(), ['id' => 'cashbox_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    //получить текущую смену
    public static function getCurrent() {
        $shift = Shift::find()
            ->where([
                'user_id' => Yii::$app->user->identity->id,
                'cashbox_id' => Yii::$app->user->identity->cashbox->id,
                'end' => null
            ])
            ->one();

        if ($shift) {
            $start = Carbon::createFromTimestamp(strtotime($shift->start));
            $now = new Carbon();
            $diff = $start->diffInHours($now);
            if ($diff >= 24) {
                $shift->end = $start->addHours(24)->format('Y-m-d H:i:s');
                $shift->save();
                $shift = false;
            }
        }

        if (!$shift) {
            $shift = new Shift();
            $shift->setAttributes([
                'user_id' => Yii::$app->user->identity->id,
                'start' => date('Y-m-d H:i:s'),
                'branch_id' => Yii::$app->user->identity->branch_id,
                'cashbox_id' => Yii::$app->user->identity->cashbox->id,
            ]);
            $shift->save();
        }

        return $shift;
    }

    public function close() {
        $this->end = date('Y-m-d H:i:s');
        $this->save();
    }

}
