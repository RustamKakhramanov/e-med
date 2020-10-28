<?php

namespace app\models;

use Yii;
use WebSocket\Client;

/**
 * This is the model class for table "device_task".
 *
 * @property integer $id
 * @property integer $device_id
 * @property string $comand
 * @property string $extra
 * @property string $uid
 * @property integer $status
 * @property string $created
 * @property string $updated
 *
 * @property Device $device
 */
class DeviceTask extends \yii\db\ActiveRecord {

    const STATUS_WAIT = -1;
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'device_task';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['device_id', 'status'], 'integer'],
            [['extra'], 'string'],
            [['created', 'updated'], 'safe'],
            [['comand', 'uid'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'comand' => 'Comand',
            'extra' => 'Extra',
            'uid' => 'Uid',
            'status' => 'Status',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            $this->updated = date('Y-m-d H:i:s');

            if (!$this->id) {
                $this->created = date('Y-m-d H:i:s');
            }

            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice() {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * создает и возвращает статус задания
     * @return \app\models\DeviceTask
     * @throws Exception
     */
    public static function createCheck($uid, $items) {

        if (Yii::$app->user->identity) {
            $device = Yii::$app->user->identity->currentDevice;
        } else {
            $device = Device::find()->where(['serial' => 'SERIAL'])->one();
        }

        if (!$device) {
            throw new Exception('Не найдено устройство');
        }

        //проверка создана ли транзакция
        $task = DeviceTask::find()->where(['uid' => $uid])->one();
        if (!$task) {
            $task = new DeviceTask();
            $task->setAttributes([
                'uid' => $uid,
                'device_id' => $device->id,
                'comand' => 'printCheck',
                'status' => DeviceTask::STATUS_WAIT,
                'extra' => json_encode($items)
            ]);
            $task->save();

            //отправка на вс
            $client = new Client('ws://localhost:8080/');
            $client->send('{"method":"auth", "data": {"login":"MASTER","password":"123123"}}');
            $client->send(json_encode([
                'method' => 'pushTask',
                'token' => 'd022c82cad183bc8e3a15ec43e4c975f',
                'data' => [
                    'method' => 'createCheck',
                    'login' => $device->serial,
                    'uid' => $uid,
                    'methodData' => [
                        'list' => $items
                    ]
                ]
            ]));
            $client->close();
        }

        return $task;
    }

    /**
     * сохранить статус задания после ответа устройства
     * @param type $uid
     * @param type $status
     */
    public static function createChechDone($uid, $status) {
        $task = DeviceTask::find()->where(['uid' => $uid])->one();
        if ($task) {
            $task->status = $status;
            $task->save();
        }
    }

}
