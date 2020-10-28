<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

123123


<script>
    var svWs,
            svWsUrl = '<?= Yii::$app->params['aws_path']; ?>';

    var sv = {
        data: {},
        numbers: [251, 301, 1002], //ext выбранные визором
        process: function (resp) {
            if (resp.type == 'init') {
                this._init(resp.data);
            }
        },
        _init: function (data) {
            this.data = data;
        }
    }

    $(document).ready(function () {
        svWs = new ReconnectingWebSocket(svWsUrl);
        svWs.onopen = function (e) {
            //console.log('connection queue established');
            svWs.send(JSON.stringify({
                action: 'login',
                data: {
                    user_id: <?= Yii::$app->user->identity->id; ?>,
                    number: 'supervisor-<?= Yii::$app->user->identity->id; ?>'
                }
            }));
            svWs.send(JSON.stringify({
                action: 'visor_init',
                data: {
                    numbers: sv.numbers
                }
            }));
        };
        svWs.onmessage = function (e) {
            //получили ответ
            var resp = $.parseJSON(e.data);
            console.log(resp);
            if (resp.hasOwnProperty('type')) {
                sv.process(resp);
            }
        };

    });
</script>