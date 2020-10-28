<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_queue_entry".
 *
 * @property integer $id
 * @property string $uniqueid
 * @property string $queue
 * @property string $number
 * @property string $opened
 * @property string $closed
 * @property integer $duration
 * @property integer $branch_id
 */
class LogQueueEntry extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'log_queue_entry';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['opened', 'closed'], 'safe'],
            [['branch_id', 'duration'], 'integer'],
            [['uniqueid', 'queue', 'number'], 'string', 'max' => 255],
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
            'number' => 'Number',
            'opened' => 'Opened',
            'closed' => 'Closed',
            'branch_id' => 'Branch ID',
        ];
    }

    public static function saveJoin($data, $queue, $public) {
        $branches = [];
        $list = Branch::find()->all();
        foreach ($list as $branch) {
            if ($branch->getExtraParam('aster') == $public) {
                $branches[] = $branch;
            }
        }

        foreach ($branches as $branch) {
            $model = new LogQueueEntry();
            $model->setAttributes([
                'uniqueid' => $data['uniqueid'],
                'queue' => $queue,
                'number' => $data['number'],
                'opened' => date('Y-m-d H:i:s'),
                'branch_id' => $branch->id
            ]);
            $model->save();
        }
    }

    public static function saveLeave($data, $queue, $public) {
        $branches = [];
        $list = Branch::find()->all();
        foreach ($list as $branch) {
            if ($branch->getExtraParam('aster') == $public) {
                $branches[] = $branch;
            }
        }

        foreach ($branches as $branch) {
            $model = LogQueueEntry::find()->where([
                        'uniqueid' => $data['uniqueid'],
                        'branch_id' => $branch->id
                    ])->one();
            if ($model) {
                $date = date('Y-m-d H:i:s');
                $model->setAttributes([
                    'closed' => $date,
                    'duration' => (strtotime($date) - strtotime($model->opened))
                ]);
                $model->save();
            }
        }
    }

}
