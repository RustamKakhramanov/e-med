<?php

namespace app\models;

use Yii;

/**
 * звонки с подключенных сайтов
 *
 * @property integer $id
 * @property integer $branch_id
 * @property string $name
 * @property string $number
 * @property string $theme
 * @property string $date
 * @property boolean $call
 * @property boolean $answer
 *
 * @property Branch $branch
 */
class ApiCall extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_call';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['branch_id'], 'integer'],
            [['date'], 'safe'],
            [['call', 'answer'], 'boolean'],
            [['number', 'theme', 'name'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'branch_id' => 'Branch ID',
            'number' => 'Number',
            'theme' => 'Theme',
            'date' => 'Date',
            'call' => 'Call',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public static function nocalledList($branch_id, $filter = []) {
        $data = [];
        $query = ApiCall::find()
                ->where([
                    'branch_id' => $branch_id,
                        //'answer' => false
                ])
                ->orderBy(['date' => SORT_DESC]);

        if (isset($filter['date'])) {
            $query
                    ->andWhere(['>=', 'date', date('Y-m-d', strtotime($filter['date'])) . ' 00:00:00'])
                    ->andWhere(['<=', 'date', date('Y-m-d', strtotime($filter['date'])) . ' 23:59:59']);
        }

        $items = $query->all();
        foreach ($items as $item) {
            $temp = [
                'id' => $item->id,
                'date' => date('d.m.Y H:i', strtotime($item->date)),
                'number' => $item->number,
                'name' => $item->name,
                'theme' => $item->theme
            ];

            $status = 'wait';
            if ($item->answer) {
                $status = 'success';
            } else {
                $countTries = LogOutgoing::find()
                        ->where([
                            'type' => LogOutgoing::TYPE_CALLME,
                            'extra' => $item->id
                        ])
                        ->count();
                if ($countTries >= 3) {
                    $status = 'fail';
                }
            }
            $temp['status'] = $status;

            $data[] = $temp;
        }

        if (isset($filter['status']) && $filter['status'] != '') {
            foreach ($data as $key => $item) {
                if ($item['status'] != $filter['status']) {
                    unset($data[$key]);
                }
            }
        }



        return array_values($data);
    }

    /**
     * получить список необзвоненых
     */
    public static function countNocalled($branch_id) {
        $c1 = ApiCall::find()
                ->where([
                    'branch_id' => $branch_id,
                    'answer' => false
                ])
                ->andWhere(['>=', 'date', date('Y-m-d') . ' 00:00:00'])
                ->andWhere(['<=', 'date', date('Y-m-d') . ' 23:59:59'])
                ->count();

        $c2 = ApiCall::find()
                ->leftJoin(LogOutgoing::tableName(), LogOutgoing::tableName() . '.extra::integer = ' . ApiCall::tableName() . '.id')
                ->where([
                    ApiCall::tableName() . '.branch_id' => $branch_id,
                    ApiCall::tableName() . '.answer' => false
                ])
                ->andWhere(['>=', ApiCall::tableName() . '.date', date('Y-m-d') . ' 00:00:00'])
                ->andWhere(['<=', ApiCall::tableName() . '.date', date('Y-m-d') . ' 23:59:59'])
                ->groupBy('api_call.id')
                ->having('count(log_outgoing.*) >= 3')
                ->count();

        return $c1 - $c2;
    }

}
