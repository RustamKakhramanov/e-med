<?php

namespace app\commands;

use yii\console\Controller;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use bin\Aws;

class AsterWsController extends Controller {

    public function actionIndex() {
        echo "start server\n";

        $server = IoServer::factory(
                        new HttpServer(
                        new WsServer(
                        new Aws()
                        )
                        ), 8087
        );

        $server->run();
    }

}
