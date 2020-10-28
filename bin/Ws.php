<?php

/**
 * сокет сервер для работы с фискальником
 */
namespace bin;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\models\Device;
use app\models\DeviceTask;

class Ws implements MessageComponentInterface {

    protected $clients;
    //temp storage
    protected $_logins = [
        'MASTER' => 123123,
        'SERIAL' => 'SERIAL',
    ];

    public function __construct() {
        $this->_log('Ratchet server started');
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->_log('New connection (' . $conn->resourceId . ')');
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $input = @json_decode($msg, true);
        $this->_log('Request from ' . $from->resourceId . ': ' . print_r($input, true));

        $action = isset($input['method']) ? $input['method'] : false;

        if ($action) {
            $classAction = '_method' . ucfirst($action);
            if (method_exists($this, $classAction)) {
                if ($action == 'auth') {
                    $this->_methodAuth($from, $input);
                } else {
                    if ($this->_verifyToken($from, $input)) {
                        call_user_func_array(array($this, $classAction), [
                            $from, $input
                        ]);
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->_log('Connection ' . $conn->resourceId . ' has disconnected');
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->_log('An error has occurred: ' . $e->getMessage());
        $conn->close();
    }

    /**
     * запись лога
     * @param type $text
     */
    protected function _log($text) {
        $fp = fopen(__DIR__ . '/../runtime/logs/ws.log', 'a');
        fwrite($fp, '[' . date('d.m.Y H:i:s') . '] ' . $text . "\n");
        fclose($fp);
        echo $text . "\n";
    }

    /**
     * проверка токена
     */
    protected function _verifyToken(&$connect, $input) {
        return isset($input['token']) && isset($connect->token) && $connect->token == $input['token'];
    }

    /**
     * авторизация
     */
    protected function _methodAuth($from, $input) {
        $resp = [
            'method' => lcfirst(str_replace('_method', '', __FUNCTION__)),
            'data' => null
        ];

        if (isset($input['data']['login']) && isset($input['data']['password'])) {

            if ($input['data']['login'] == 'MASTER') {
                $from->login = $input['data']['login'];
                $from->token = md5($input['data']['login'] . $input['data']['password']);
                $resp['data'] = $from->token;
            } else {
                if (Device::find()->where(['serial' => $input['data']['login']])->andWhere(['=', 'pass', $input['data']['password']])->count()) {
                    $from->login = $input['data']['login'];
                    $from->token = md5($input['data']['login'] . $input['data']['password']);
                    $resp['data'] = $from->token;
                }
            }
            
            if ($resp['data']) {
                $this->_log('Client ' . $from->login . ' success logged');
                echo 'client [' . $from->login . '] success logged' . "\r\n";
            }
            
        }

        $from->send(json_encode($resp));
    }

    /**
     * пуш на клиента выполнить задание
     * @param type $from
     * @param array $input
     */
    protected function _methodPushTask($from, $input) {
        $resp = [
            'method' => lcfirst(str_replace('_method', '', __FUNCTION__)),
            'data' => null
        ];

        //проверка на мастер логин
        if (isset($from->login) && $from->login == 'MASTER') {

            //ид устройства
            $clientId = $input['data']['login'];

            $client = false;
            //поиск коннекта
            foreach ($this->clients as $c) {
                if (isset($c->login) && $c->login == $clientId) {
                    $client = &$c;
                    break;
                }
            }

            //если подключен
            if ($client) {
                //todo ввести id или transactionId
                $client->send(json_encode([
                    'method' => $input['data']['method'],
                    'token' => $client->token,
                    'uid' => $input['data']['uid'],
                    'data' => $input['data']['methodData']
                ]));
                $resp['data'] = true;
            }

            $from->send(json_encode($resp));
        }
    }

    /**
     * ответ клиента на печать x отчета
     * @param type $from
     * @param type $input
     */
    protected function _methodPrintReportX($from, $input) {
        $resp = [
            'method' => lcfirst(str_replace('_method', '', __FUNCTION__)),
            'data' => null
        ];

        //todo записывать в бд, или вызывать handler
//        {
//            "id": 2,
//            "method": "printReportZ",
//            "token": "ed2b1f468c5f915f3f1cf75d7068baae",
//            "data": null
//        }
//        {
//            "id": 2,
//            "method": "printReportZ",
//            "token": "ed2b1f468c5f915f3f1cf75d7068baae",
//            "data": null,
//            "error": -12
//        }
    }

    /**
     * ответ клиента на создание чека
     * @param type $from
     * @param type $input
     */
    protected function _methodCreateCheck($from, $input) {
        $resp = [
            'method' => lcfirst(str_replace('_method', '', __FUNCTION__)),
            'data' => null
        ];

        DeviceTask::createChechDone($input['uid'], $input['data']['status']);

        $from->send(json_encode($resp));
    }

}
