<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_queue".
 *
 * @property integer $id
 * @property string $uniqueid
 * @property string $queue
 * @property integer $abandoned
 * @property integer $completed
 * @property integer $calls
 * @property string $date
 * @property integer $branch_id
 *
 * @property Branch $branch
 */
class LogQueue extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'log_queue';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['abandoned', 'completed', 'calls', 'branch_id'], 'integer'],
            [['date'], 'safe'],
            [['uniqueid', 'queue'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uniqueid' => 'Uniqueid',
            'queue' => 'Queue',
            'abandoned' => 'Abandoned',
            'completed' => 'Completed',
            'calls' => 'Calls',
            'date' => 'Date',
            'branch_id' => 'Branch ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }
    
    /**
     * запись лога очереди
     * @param type $data
     * @param type $queue
     * @param type $public
     */
    public static function add($data, $queue, $public) {
        
        $branches = [];
        $list = Branch::find()->all();
        foreach ($list as $branch) {
            if ($branch->getExtraParam('aster') == $public) {
                $branches[] = $branch;
            }
        }

        foreach ($branches as $branch) {
            $dub = LogQueue::find()->where([
                        'uniqueid' => $data['uniqueid'],
                        'queue' => $queue,
                        'branch_id' => $branch->id
                    ])->count();
            if (!$dub) {
                $model = new LogQueue();
                $model->setAttributes([
                    'uniqueid' => $data['uniqueid'],
                    'queue' => $queue,
                    'abandoned' => $data['abandoned'],
                    'completed' => $data['completed'],
                    'calls' => $data['calls'],
                    'date' => date('Y-m-d H:i:s'),
                    'branch_id' => $branch->id
                ]);
                $model->save();
            }
        }
    }

}
