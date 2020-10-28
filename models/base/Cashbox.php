<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "cashbox".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $name Название
 * @property bool $deleted
 * @property string $webkassa_id Id в вебкассе
 * @property string $bin БИН
 * @property string $nds_serie НДС Серия
 * @property string $nds_number НДС Номер
 * @property string $operator_name Наименование оператора фискальных данных
 * @property string $kkt ККТ
 * @property string $rnk РНК
 * @property string $org_name Наименование организации
 * @property bool $use_nds Использовать НДС
 *
 * @property Branch $branch
 * @property Shift[] $shifts
 */
class Cashbox extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashbox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'name'], 'required'],
            [['branch_id'], 'default', 'value' => null],
            [['branch_id'], 'integer'],
            [['deleted', 'use_nds'], 'boolean'],
            [['name', 'webkassa_id', 'bin', 'nds_serie', 'nds_number', 'operator_name', 'kkt', 'rnk', 'org_name'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'name' => 'Название',
            'deleted' => 'Deleted',
            'webkassa_id' => 'Id в вебкассе',
            'bin' => 'БИН',
            'nds_serie' => 'НДС Серия',
            'nds_number' => 'НДС Номер',
            'operator_name' => 'Наименование оператора фискальных данных',
            'kkt' => 'ККТ',
            'rnk' => 'РНК',
            'org_name' => 'Наименование организации',
            'use_nds' => 'Использовать НДС',
        ];
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
    public function getShifts()
    {
        return $this->hasMany(Shift::className(), ['cashbox_id' => 'id']);
    }
}
