<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_outgoing".
 *
 * @property integer $id
 * @property integer $sess_id
 * @property string $number
 * @property string $date
 * @property boolean $answer
 * @property string $extra
 *
 * @property UserSession $sess
 */
class LogOutgoing extends \yii\db\ActiveRecord {

    const TYPE_ABANDON = 0;
    const TYPE_CALLME = 1;

    public static $types = [
        self::TYPE_ABANDON => 'Пропущенный',
        self::TYPE_CALLME => 'Обратный звонок'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'log_outgoing';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sess_id', 'type'], 'integer'],
            [['date'], 'safe'],
            [['answer'], 'boolean'],
            [['number', 'extra'], 'string', 'max' => 255],
            [['sess_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserSession::className(), 'targetAttribute' => ['sess_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sess_id' => 'Sess ID',
            'number' => 'Number',
            'date' => 'Date',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSess() {
        return $this->hasOne(UserSession::className(), ['id' => 'sess_id']);
    }

    public static function countSuccess($type) {
        return LogOutgoing::find()
                        ->select('number')
                        ->where([
                            'type' => $type,
                            'answer' => true
                        ])
                        ->andWhere(['>=', 'date', date('Y-m-d') . ' 00:00:00'])
                        ->andWhere(['<=', 'date', date('Y-m-d') . ' 23:59:59'])
                        ->groupBy('number')
                        ->count();
    }

    /**
     * 
     * @param type $numbers Array
      (
      [num1] => 87005714511
      [num2] => 301
      )
     */
    public static function eventAnswer($numbers) {
        //self::_log('0: ' . json_encode($numbers));
        $prev = LogOutgoing::find()
                ->select('"' . LogOutgoing::tableName() . '".*, "' . UserSession::tableName() . '"."number" as "from"')
                ->leftJoin(UserSession::tableName(), UserSession::tableName() . '.id = ' . LogOutgoing::tableName() . '.sess_id')
                ->where([
                    '"' . LogOutgoing::tableName() . '"."number"' => $numbers['num1'],
                    '"' . UserSession::tableName() . '"."number"' => $numbers['num2'],
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        
        //self::_log('1: ' . json_encode($prev->toArray()));
        
        if (!$prev) {
            //self::_log('2: ' . strlen($numbers['num1']));
            if (strlen($numbers['num1']) == 11) {     
                $filteredPhone = substr($numbers['num1'], 1);  
                //self::_log('3: ' . $filteredPhone);
                $prev = LogOutgoing::find()
                    ->select('"' . LogOutgoing::tableName() . '".*, "' . UserSession::tableName() . '"."number" as "from"')
                    ->leftJoin(UserSession::tableName(), UserSession::tableName() . '.id = ' . LogOutgoing::tableName() . '.sess_id')
                    ->where([
                        '"' . LogOutgoing::tableName() . '"."number"' => $filteredPhone,
                        '"' . UserSession::tableName() . '"."number"' => $numbers['num2'],
                    ])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();
                
                //self::_log('4: ' . json_encode($prev->toArray()));
            }
        }

        if ($prev) {            
            $prev->answer = true;
            $prev->save(false);
            if ($prev->type == self::TYPE_CALLME && $prev->extra) {
                $item = ApiCall::findOne(['id' => $prev->extra]);
                if ($item) {
                    $item->answer = true;
                    $item->save(false);
                }
            }
        }
    }
    
    protected static function _log($text) {
        $fp = fopen(__DIR__ . '/../runtime/logs/aws.log', 'a');
        fwrite($fp, '[' . date('d.m.Y H:i:s') . '] ' . $text . "\n");
        fclose($fp);
    }

}
