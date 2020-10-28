<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "shift".
 *
 * @property int $id
 * @property int $user_id
 * @property string $start
 * @property string $end
 * @property int $branch_id
 * @property int $cashbox_id
 *
 * @property Check[] $checks
 * @property Branch $branch
 * @property Cashbox $cashbox
 * @property User $user
 */
class Shift extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'start' => 'Start',
            'end' => 'End',
            'branch_id' => 'Branch ID',
            'cashbox_id' => 'Cashbox ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecks()
    {
        return $this->hasMany(Check::className(), ['shift_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashbox()
    {
        return $this->hasOne(Cashbox::className(), ['id' => 'cashbox_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
