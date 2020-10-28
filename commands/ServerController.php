<?php

/**
 * запуск ws сервера
 */

namespace app\commands;

use yii\console\Controller;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use bin\Ws;

class ServerController extends Controller {

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex() {
                
        echo "start server\n";

        $server = IoServer::factory(
                        new HttpServer(
                        new WsServer(
                        new Ws()
                        )
                        ), 8080
        );

        $server->run();
    }

}
