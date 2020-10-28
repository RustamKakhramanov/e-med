<?php

namespace app\models\webkassa;

use Yii;

class Client {

    const
        PAYMENT_TYPE_CASH = 0,
        PAYMENT_TYPE_CARD = 1,
        OPERATION_TYPE_BUY = 2, //продажа
        OPERATION_TYPE_CANCEL = 3, //возврат продажи
        TAX_TYPE_NO = 0, //без налога
        TAX_TYPE_NDS = 100; //ндс

    public static function check($data) {
        return self::_query($data, 'Check');
    }

    public static function historyByNumber($data) {
        return self::_query($data, 'Check/HistoryByNumber');
    }

    /**
     * запрос
     * @param type $data
     * @throws \Exception
     */
    protected static function _query($data, $method) {
        $postdata = $data;
        $postdata['token'] = self::_getToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['webkassa']['url'] . $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        $resp = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            throw new \Exception('Ошибка соединения');
        }
        $res = json_decode($resp, true);
        if (isset($res['Errors'])) {
            foreach ($res['Errors'] as $e) {
                if ($e['Code'] == 2) {
                    self::_auth();
                    return self::_query($data);
                }
            }
            throw new \Exception($res['Errors'][0]['Text']);
        }

        return $res['Data'];
    }

    protected static function _getToken() {
        $token = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'token');
        if (!$token) {
            self::_auth();
            return self::_getToken();
        }

        return $token;
    }

    protected static function _auth() {
        $data = [
            'Login' => Yii::$app->params['webkassa']['login'],
            'Password' => Yii::$app->params['webkassa']['password']
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['webkassa']['url'] . 'Authorize');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $resp = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            throw new \Exception('Ошибка авторизации');
        }
        $res = json_decode($resp, true);
        if (isset($res['Errors'])) {
            throw new \Exception('Ошибка авторизации');
        }

        $f = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'token', 'w');
        fwrite($f, $res['Data']['Token']);
        fclose($f);
    }

}
