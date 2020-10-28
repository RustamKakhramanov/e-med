<?php

/**
 * сокет сервер для работы с астером
 */

namespace bin;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Aws implements MessageComponentInterface {

    const EXTERNAL_PREFIX = '715984';

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->_log('Aws server started');
    }

    public function __destruct() {
        $this->_log('Ratchet server stopped1');
    }

    public function onOpen(ConnectionInterface $connection) {
        $this->clients->attach($connection);
        $this->_log('New connection (' . $connection->resourceId . ')');
    }

    protected function _getPushNumbers($e) {
        $data = [
            'num1' => null, //получатель
            'num2' => null, //источник
        ];

        //поиск номера получателя
        if (true) {
            $number = null;
            if ($e['event'] == 'Hangup') {
                if (preg_match('/^sip\/(\d+)-.+/i', $e['connectedlinenum'], $m)) {
                    $number = $m[1];
                }
            }

            if ($e['event'] == 'Dial' && $e['subevent'] == 'Begin') {
                if (preg_match('/^sip\/(\d+)-.+/i', $e['destination'], $m)) {
                    $number = $m[1];
                }
            }

            if ($e['event'] == 'Dial' && $e['subevent'] == 'End') {
                $number = false;
            }

            if ($e['event'] == 'Bridge') {
                if (preg_match('/^sip\/(\d+)-.+/i', $e['channel2'], $m)) {
                    $number = $m[1];
                } else {
                    if (
                            preg_match('/^' . self::EXTERNAL_PREFIX . '(\d+)/i', $e['callerid2'], $m) ||
                            preg_match('/^' . self::EXTERNAL_PREFIX . '8' . '(\d+)/i', $e['callerid2'], $m)
                            ) {
                        $number = $m[1];
                    }
                }
            }

            if ($e['event'] == 'Join') {
                $number = $e['queue'];
            }

            if ($e['event'] == 'Leave') {
                $number = $e['queue'];
            }

            $data['num1'] = $number;
        }

        //поиск номера источника
        if (true) {
            $number = null;

            if ($e['event'] == 'Dial' && $e['subevent'] == 'Begin') {
                //$number = $e['calleridnum'];
                if (preg_match('/^sip\/(\d+)-.+/i', $e['channel'], $m)) {
                    $number = $m[1];
                }
            }

            if ($e['event'] == 'Bridge') {
                if (preg_match('/^sip\/(\d+)-.+/i', $e['channel1'], $m)) {
                    $number = $m[1];
                } else {
                    $number = $e['callerid1'];
                }
            }

            $data['num2'] = $number;
        }

        return $data;
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $input = json_decode($msg, true);

        //авторизация юзера
        if (isset($input['action']) && $input['action'] == 'login') {
            $from->user = $input['data']['user_id'];
            $user = \app\models\User::findOne(['id' => $input['data']['user_id']]);
            $from->aster_ws_url = $user->branch->getExtraParam('aster');
            $from->branch_id = $user->branch_id;
            $from->number = $input['data']['number'];
        }

        //прием данных для веб-клиента
        if (isset($input['action']) && $input['action'] == 'push') {
            $event = $input['data'];
            $pushNumbers = $this->_getPushNumbers($event);

            foreach ($this->clients as $c) {
                if (
                        isset($c->user) && (
                        $c->number == $pushNumbers['num1'] ||
                        $c->number == $pushNumbers['num2'] ||
                        $pushNumbers['num1'] === false
                        )
                ) {
                    $c->send(
                            json_encode(
                                    $this->_filterPush($event, $c, $pushNumbers)
                            )
                    );
                }
            }
        }

        //получение безадресных событий
        if (isset($input['action']) && $input['action'] == 'push_query') {

            if (in_array($input['data']['event'], ['Join', 'Leave'])) {

                if ($input['data']['event'] == 'Join') {
                    \app\models\LogQueueEntry::saveJoin([
                        'uniqueid' => $input['data']['uniqueid'],
                        'number' => $input['data']['calleridnum']
                            ], $input['data']['queue'], $input['public']);
                }

                if ($input['data']['event'] == 'Leave') {
                    \app\models\LogQueueEntry::saveLeave([
                        'uniqueid' => $input['data']['uniqueid']
                            ], $input['data']['queue'], $input['public']);
                }

                $resp = $this->_sendAsterWs($input['public'], [
                    'action' => 'queuestatus',
                    'data' => ['number' => $input['data']['queue']]
                        ], true);

                $queue = [
                    'wait' => 0,
                    'items' => []
                ];

                foreach ($resp as $item) {
                    if ($item['event'] == 'QueueParams') {
                        $queue['calls'] = $item['calls'];
                        $queue['completed'] = $item['completed'];
                        $queue['abandoned'] = $item['abandoned'];
                        $queue['uniqueid'] = $item['actionid'];
                    }
                    if ($item['event'] == 'QueueEntry') {
                        if (1 * $item['wait'] > $queue['wait']) {
                            $queue['wait'] = 1 * $item['wait'];
                        }
                    }
                }

                \app\models\LogQueue::add([
                    'uniqueid' => $queue['uniqueid'],
                    'abandoned' => $queue['abandoned'],
                    'completed' => $queue['completed'],
                    'calls' => $queue['calls']
                        ], $input['data']['queue'], $input['public']);
            }

            //$input['action'];


            $queue = [
                'wait' => 0,
                'items' => []
            ];
        }

        if (isset($input['action']) && $input['action'] == 'answer') {
            $data = [
                'number' => $from->number,
                'phone' => $input['phone'],
                'channel' => ''
            ];
            foreach ($input['data'] as $prevEvents) {
                if (isset($prevEvents['destination'])) {
                    $data['channel'] = $prevEvents['destination'];
                    break;
                }
            }
            if ($data['channel']) {
                $this->_sendAsterWs($from->aster_ws_url, [
                    'action' => 'originate',
                    'data' => $data
                ]);
            }
        }

        if (isset($input['action']) && $input['action'] == 'hangup') {
            $data = [];
            foreach ($input['data'] as $prevEvents) {
                if (isset($prevEvents['destination'])) {
                    $data['Channel'] = $prevEvents['destination'];
                    break;
                }
            }

            if ($data) {
                $this->_sendAsterWs($from->aster_ws_url, [
                    'action' => 'hangup',
                    'data' => $data
                ]);
            }
        }

        if (isset($input['action']) && $input['action'] == 'reset') {
            $data = [];
            foreach ($input['data'] as $prevEvents) {
                if (isset($prevEvents['channel'])) {
                    $data['Channel'] = $prevEvents['channel'];
                    break;
                }
            }

            if ($data) {
                $this->_sendAsterWs($from->aster_ws_url, [
                    'action' => 'hangup',
                    'data' => $data
                ]);
            }
        }

        if (isset($input['action']) && $input['action'] == 'redirect') {
            $channel = false;
            foreach ($input['data'] as $prevEvents) {
                if (isset($prevEvents['channel'])) {
                    $channel = $prevEvents['channel'];
                    break;
                }
            }

            if ($channel) {
                $this->_sendAsterWs($from->aster_ws_url, [
                    'action' => 'redirect',
                    'data' => [
                        'Channel' => $channel,
                        'exten' => $input['number']
                    ]
                ]);
            }
        }

        if (isset($input['action']) && $input['action'] == 'queuestatus') {
            $from->send(
                    json_encode(
                            $this->_queueStatus($from)
                    )
            );
        }

        if (isset($input['action']) && $input['action'] == 'visor_init') {
            $resp = $this->_sendAsterWs($from->aster_ws_url, [
                'action' => 'showpeers',
                'data' => [
                    'numbers' => $input['data']['numbers']
                ]
                    ], true);

            $from->send(json_encode([
                'type' => 'init',
                'data' => $resp
            ]));
        }

        //список пропущенных
        if (isset($input['action']) && $input['action'] == 'abandonlist') {
            $resp = $this->_sendAsterWs($from->aster_ws_url, [
                'action' => 'abandonlist',
                'data' => [
                    'date' => date('Y-m-d'),
                    'queue' => $input['data']['queue']
                ]
                    ], true);
            $data = [];
            foreach ($resp as $row) {
                if ($row['enterqueue_data']) {
                    $temp = [
                        'id' => $row['callid'],
                        'queue' => $row['queuename'],
                        'date' => $row['time'],
                        'time' => date('H:i', strtotime($row['time'])),
                        'event' => $row['event'],
                        'called' => false,
                        'status' => 'wait'
                    ];
                    $temp['phone'] = $row['enterqueue_data'];
                    $temp['extra'] = [
                        $row['data1'], $row['data2'], $row['data3'], $row['data4'], $row['data5'],
                    ];

                    $callExist = \app\models\LogOutgoing::find()
                            ->where([
                                'number' => $temp['phone'],
                                'type' => \app\models\LogOutgoing::TYPE_ABANDON
                            ])
                            ->andWhere(['>=', 'date', date('Y-m-d 00:00:00')])
                            ->andWhere(['<=', 'date', date('Y-m-d 23:59:59')])
                            ->count();

                    if ($callExist) {
                        $answerExist = \app\models\LogOutgoing::find()
                            ->where([
                                'number' => $temp['phone'],
                                'answer' => true,
                                'type' => \app\models\LogOutgoing::TYPE_ABANDON
                            ])
                            ->andWhere(['>=', 'date', date('Y-m-d 00:00:00')])
                            ->andWhere(['<=', 'date', date('Y-m-d 23:59:59')])
                            ->count();
                        if ($answerExist) {
                            $temp['status'] = 'success';
                        } else {
                            if ($callExist >= 3) {
                                $temp['status'] = 'fail';
                            }
                        }
                    }

                    $data[] = $temp;
                }
            }

            $from->send(json_encode([
                'type' => 'abandonloaded',
                'data' => array_reverse($data)
            ]));
        }

        //звонок пропущенному
        if (isset($input['action']) && $input['action'] == 'abandoncall') {
            $outgoing = new \app\models\LogOutgoing();
            $outgoing->setAttributes([
                'sess_id' => $input['data']['sess_id'],
                'number' => $input['data']['item']['phone'],
                'date' => date('Y-m-d H:i:s'),
                'type' => \app\models\LogOutgoing::TYPE_ABANDON
            ]);
            $outgoing->save();

            //проверка на внешний номер
            $phone = $input['data']['item']['phone'];
            if (strlen($phone) > 4) {
                //добавить префикс для вызова на город и моб
                if (strlen($phone) == 10) {
                    $phone = self::EXTERNAL_PREFIX . '8' . $phone;
                } else {
                    $phone = self::EXTERNAL_PREFIX . $phone;
                }
            }
            
            $session = \app\models\UserSession::findOne(['id' => $input['data']['sess_id']]);

            $this->_sendAsterWs($from->aster_ws_url, [
                'action' => 'abandoncall',
                'data' => [
                    'exten' => $session->number,
                    'number' => $phone
                ]
            ]);
        }

        //список с сайта
        if (isset($input['action']) && $input['action'] == 'callmelist') {
            $from->send(json_encode([
                'type' => 'callmeloaded',
                'data' => \app\models\ApiCall::nocalledList($from->branch_id, $input['data'])
            ]));
        }

        //звонок обратная связь
        if (isset($input['action']) && $input['action'] == 'callme') {
            $outgoing = new \app\models\LogOutgoing();
            $outgoing->setAttributes([
                'sess_id' => $input['data']['sess_id'],
                'number' => $input['data']['item']['number'],
                'date' => date('Y-m-d H:i:s'),
                'type' => \app\models\LogOutgoing::TYPE_CALLME,
                'extra' => $input['data']['item']['id']
            ]);
            $outgoing->save(false);

            //проверка на внешний номер
            $phone = $input['data']['item']['number'];
            if (strlen($phone) > 4) {
                //добавить префикс для вызова на город и моб
                if (strlen($phone) == 10) {
                    $phone = self::EXTERNAL_PREFIX . '8' . $phone;
                } else {
                    $phone = self::EXTERNAL_PREFIX . $phone;
                }
            }

            $session = \app\models\UserSession::findOne(['id' => $input['data']['sess_id']]);

            //пометка что вызов совершен
            $apicall = \app\models\ApiCall::findOne(['id' => $input['data']['item']['id']]);
            if ($apicall) {
                $apicall->call = true;
                $apicall->save();
            }

            $this->_sendAsterWs($from->aster_ws_url, [
                'action' => 'abandoncall',
                'data' => [
                    'exten' => $session->number,
                    'number' => $phone
                ]
            ]);
        }
    }

    public function onClose(ConnectionInterface $connection) {
        $this->_log('Connection ' . $connection->resourceId . ' has disconnected');
        $this->clients->detach($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e) {
        $this->_log('An error has occurred: ' . $e->getMessage());
        $this->clients->detach($connection);
        $connection->close();
    }

    protected function close() {
        $this->_log('Ratchet server stopped2');
    }

    protected function _getDataNumber($e) {
        $number = null;

        if ($e['event'] == 'Hangup') {
            if (preg_match('/^sip\/(\d+)-.+/i', $e['connectedlinenum'], $m)) {
                $number = $m[1];
            }
        }

        if ($e['event'] == 'Dial' && $e['subevent'] == 'Begin') {
            if (preg_match('/^sip\/(\d+)-.+/i', $e['destination'], $m)) {
                $number = $m[1];
            }
        }

        if ($e['event'] == 'Dial' && $e['subevent'] == 'End') {
            $number = false;
        }

        if ($e['event'] == 'Bridge') {
            if (preg_match('/^sip\/(\d+)-.+/i', $e['channel2'], $m)) {
                $number = $m[1];
            }
        }

        if ($e['event'] == 'Join') {
            $number = $e['queue'];
        }

        if ($e['event'] == 'Leave') {
            $number = $e['queue'];
        }

        return $number;
    }

    /**
     * получить exten и номер
     * @param type $e
     * @return type
     */
    protected function _getDataNumbers($e) {
        $numbers = [];

        if ($e['event'] == 'Hangup') {
            $numbers = [$e['connectedlinenum'], $e['calleridnum']];
        }

        if ($e['event'] == 'Dial' && $e['subevent'] == 'Begin') {
            if (preg_match('/^sip\/(\d+)-.+/i', $e['destination'], $m)) {
                $numbers = [$m[1], $e['calleridnum']];
            }

            if (preg_match('/^sip\/(\d+)-.+/i', $e['channel'], $m)) {
                $numbers = [$m[1], $e['connectedlinenum']];
            }
        }

        if ($e['event'] == 'Dial' && $e['subevent'] == 'End') {
            $numbers = [];
        }

        if ($e['event'] == 'Bridge') {
            if (preg_match('/^sip\/(\d+)-.+/i', $e['channel1'], $m)) {
                $numbers = [$m[1], $e['callerid2']];
            }

            if (preg_match('/^sip\/(\d+)-.+/i', $e['channel2'], $m)) {
                $numbers = [$m[1], $e['callerid1']];
            }
        }

        if ($e['event'] == 'Join') {
            $numbers = [$e['queue']];
        }

        if ($e['event'] == 'Leave') {
            $numbers = [$e['queue']];
        }

        return $numbers;
    }

    /**
     * отправка на астер
     * @param type $data
     */
    protected function _sendAsterWs($url, $data, $need_receive = false) {
        if ($url) {
            $resp = null;
            $client = new \WebSocket\Client($url);
            $client->send(json_encode($data));
            $client->setTimeout(30);
            if ($need_receive) {
                $resp = json_decode($client->receive(), true);
            }

            $client->close();

            if ($need_receive) {
                return $resp;
            }
        }
    }

    /**
     * 
     * @param array $e asterisk event
     */
    protected function _filterPush($e, $client, $pushNumbers) {
        $type = false;
        $direction = false;

        if ($e['event'] == 'Dial' && $e['subevent'] == 'Begin') {
            $type = 'in';
            $e['caller_name'] = $this->_callerName($e, $client->branch_id);

            if ($pushNumbers['num1'] == $client->number) {
                $direction = 'incoming';
            }

            if ($pushNumbers['num2'] == $client->number) {
                $direction = 'outgoing';
            }
        }

        if ($e['event'] == 'Dial' && $e['subevent'] == 'End') {
            $type = 'out';
        }

        if ($e['event'] == 'Hangup') {
            $type = 'out';
        }

        if ($e['event'] == 'Bridge' && $e['bridgestate'] == 'Link') {
            //if ($pushNumbers['num1'] == $client->number) {
            $type = 'accept';
            $e['uniqueid'] = $e['uniqueid1'];
//            } else {
//                $type = 'outgoing';
//                $e['uniqueid'] = $e['uniqueid1'];
//            }

            if ($pushNumbers['num1'] == $client->number) {
                $direction = 'incoming';
            }

            if ($pushNumbers['num2'] == $client->number) {
                //todo ответил зафиксировать answer
                
                \app\models\LogOutgoing::eventAnswer($pushNumbers);
                
                $direction = 'outgoing';
            }
        }

        if ($e['event'] == 'Bridge' && $e['bridgestate'] == 'Unlink') {
            $type = 'out';
            $e['uniqueid'] = $e['uniqueid1'];
        }

        if ($e['event'] == 'Join' || $e['event'] == 'Leave') {
            //todo сделать квери стайт
            return $this->_queueStatus($client);
        }

        return [
            'type' => $type,
            'direction' => $direction,
            'event' => $e
        ];
    }

    /**
     * найти имя звонящего
     * @param type $e
     * @return string
     */
    protected function _callerName($e, $branch_id) {
        $phone = $e['calleridnum'];
        $full_phone = $phone;
        if (strlen($phone) < 11) {
            $full_phone = '7' . str_pad($phone, 10, '0', STR_PAD_LEFT);
        }

        $patient = \app\models\Patients::find()
                ->joinWith('contacts')
                ->where([
                    'patient_contact.type' => 'phone',
                    'deleted' => 0
                ])
                ->andWhere([
                    'or',
                    //'patient_contact.phone like :phone1',
                    'patient_contact.phone = :phone2'
                        ], [
                    //':phone1' => '%' . $phone,
                    ':phone2' => $full_phone,
                ])
                ->one();

        if ($patient) {
            return $patient->fio;
        }

        $ext = \app\models\Extensions::find()
                ->where([
                    'branch_id' => $branch_id,
                    'exten' => $e['calleridnum']
                ])
                ->one();

        if ($ext) {
            return $ext->name;
        }

        return false;
    }

    protected function _queueStatus($client) {
        $resp = $this->_sendAsterWs($client->aster_ws_url, [
            'action' => 'queuestatus',
            'data' => [
                'number' => $client->number
            ]
                ], true);

        $queue = [
            'wait' => 0,
            'items' => []
        ];
        foreach ($resp as $item) {
            if ($item['event'] == 'QueueParams') {
                $queue['calls'] = $item['calls'];
                $queue['holdtime'] = $item['holdtime'];
                $queue['talktime'] = $item['talktime'];
                $queue['completed'] = $item['completed'];
                $queue['abandoned'] = $item['abandoned'];
                $queue['uniqueid'] = $item['actionid'];
                $queue['queue'] = $item['queue'];
            }

            if ($item['event'] == 'QueueEntry') {
                $name = $this->_callerName($item, $client->branch_id);
                $item['name'] = $name ? $name : $item['calleridnum'];
                $queue['items'][] = $item;
                if (1 * $item['wait'] > $queue['wait']) {
                    $queue['wait'] = 1 * $item['wait'];
                }
            }
        }

        $abaCalled = \app\models\LogOutgoing::countSuccess(\app\models\LogOutgoing::TYPE_ABANDON);
        $queue['abandoned'] = $queue['abandoned'] - $abaCalled;
        $queue['callme'] = \app\models\ApiCall::countNocalled($client->branch_id);

        return [
            'type' => 'init',
            'data' => $queue
        ];
    }

    /**
     * запись лога
     * @param type $text
     */
    protected function _log($text, $print = false) {
        $fp = fopen(__DIR__ . '/../runtime/logs/aws.log', 'a');
        fwrite($fp, '[' . date('d.m.Y H:i:s') . '] ' . $text . "\n");
        fclose($fp);
        if ($print) {
            echo $text . PHP_EOL;
        }
    }

}
