<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "extensions".
 *
 * @property integer $id
 * @property string $name
 * @property string $exten
 * @property integer $branch_id
 *
 * @property Branch $branch
 */
class Extensions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'extensions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id'], 'integer'],
            [['name', 'exten'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'exten' => 'Exten',
            'branch_id' => 'Branch ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }
}
