<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
?>
<script>
    var ws,
            wsInterval,
            wsUrl = '<?= Yii::$app->params['aws_path']; ?>';

    var aster = {
        calls: {},
        current: {},
        durationInterval: false,
        push: function (data) {
            var event = data.event;

            if (!this.calls.hasOwnProperty(event.uniqueid)) {
                this.calls[event.uniqueid] = {
                    phone: '',
                    name: '',
                    direction: data.direction,
                    events: []
                };
            }
            this.calls[event.uniqueid]['events'].push(event);

            if (this.calls[event.uniqueid].direction == 'incoming') {
                if (event.hasOwnProperty('calleridnum')) {
                    this.calls[event.uniqueid].phone = event.calleridnum;
                    this.calls[event.uniqueid].name = event.caller_name;
                }
            } else {
                if (event.hasOwnProperty('connectedlinenum')) {
                    this.calls[event.uniqueid].phone = event.connectedlinenum;
                }
            }

            if (this.calls[event.uniqueid].direction == 'incoming') {
                $('.aster-panel_items').prepend('\n\
                    <div class="item clearfix" data-uid="' + event.uniqueid + '">\n\
                        <div class="col-btn col-btn-answer"><span></span></div>\n\
                        <div class="col-btn col-btn-reset"><span></span></div>\n\
                        <div class="col-info">\n\
                            <div class="from">Вызов от ' + this.calls[event.uniqueid].phone + '</div>\n\
                            <div class="contact">' + (this.calls[event.uniqueid].name ? this.calls[event.uniqueid].name : 'неизвестно') + '</div>\n\
                        </div>\n\
                    </div>\n\
                ');
            } else {
                $('.aster-panel_items').prepend('\n\
                    <div class="item clearfix" data-uid="' + event.uniqueid + '">\n\
                        <div class="col-btn col-btn-reset"><span></span></div>\n\
                        <div class="col-info">\n\
                            <div class="from">Вызываем ' + this.calls[event.uniqueid].phone + '</div>\n\
                            <div class="contact">' + (this.calls[event.uniqueid].name ? this.calls[event.uniqueid].name : 'неизвестно') + '</div>\n\
                        </div>\n\
                    </div>\n\
                ');
            }
            setTimeout(function () {
                $('.aster-panel_items .item[data-uid="' + event.uniqueid + '"]').addClass('active');
            }, 100);
        },
        remove: function (data) {
            var event = data.event;
            delete this.calls[event.uniqueid];
            $('.aster-panel_items .item[data-uid="' + event.uniqueid + '"]').removeClass('active');
            setTimeout(function () {
                $('.aster-panel_items .item[data-uid="' + event.uniqueid + '"]').remove();
            }, 150);
            if (Object.keys(this.current).length) {
                if (this.current.uid == event.uniqueid) {
                    this._sidebarOff();
                }
            }
        },
        accept: function (data) {
            var entry = $.extend({}, this.calls[data.event.uniqueid]);
            this.current = {
                uid: data.event.uniqueid,
                entry: entry
            };
            //this.remove(data);
            this._sidebar();
        },
        reset: function () {
            if (Object.keys(this.current).length) {
                ws.send(JSON.stringify({
                    action: 'reset',
                    data: this.current.entry.events
                }));
                //$('.aster-sidebar').removeClass('active');
            }
        },
        process: function (resp) {
            console.log(resp);
            if (resp.type) {
                //добавлен в очередь
                if (resp.type == 'in') {
                    this.push(resp);
                }
                //покинул очередь
                if (resp.type == 'out') {
                    this.remove(resp);
                }
                //ответ
                if (resp.type == 'accept') {
                    this.accept(resp);
                }
            }
        },
        originate: function (key) {
            var entry = this.calls[key];
            this.current = {
                uid: key,
                entry: entry
            };
            ws.send(JSON.stringify({
                action: 'answer',
                data: entry.events,
                phone: entry.phone
            }));
            //this._sidebar();
        },
        redirect: function (number) {
            if (Object.keys(this.current).length) {
                console.log({
                    action: 'redirect',
                    data: this.current.entry.events,
                    number: number
                });
                ws.send(JSON.stringify({
                    action: 'redirect',
                    data: this.current.entry.events,
                    number: number
                }));
            }
        },
        _sidebar: function () {
            $('.aster-sidebar').addClass('active');
            $('.aster-sidebar .timer span').text('00:00');
            if (this.current.entry.direction == 'incoming') {
                $('.aster-sidebar_header h1 .l').text('Вызов от');
                $('.aster-sidebar .col-redirect').show();
            } else {
                $('.aster-sidebar_header h1 .l').text('Исходящий');
                $('.aster-sidebar .col-redirect').hide();
            }
            $('.aster-sidebar_header h1 .n').text(this.current.entry.phone);
            var durationStart = moment();
            var durationCall = moment();

            if (this.durationInterval) {
                clearInterval(this.durationInterval);
            }

            this.durationInterval = setInterval(function () {
                durationCall.add(1, 's');
                var diff = moment.utc(durationCall.diff(durationStart));
                $('.aster-sidebar .timer span').text(diff.format('mm:ss'));
            }, 1000);

        },
        _sidebarOff: function () {
            this.current = {};
            $('.aster-sidebar').removeClass('active');
            clearInterval(this.durationInterval);
        },
        hangup: function (key) {
            var entry = this.calls[key];
            ws.send(JSON.stringify({
                action: 'hangup',
                data: entry.events
            }));
        }
    }

    $(document).ready(function () {

        ws = new ReconnectingWebSocket(wsUrl);

        ws.onopen = function (e) {
            console.log('connection established');
            ws.send(JSON.stringify({
                action: 'login',
                data: {
                    user_id: <?= Yii::$app->user->identity->id; ?>,
                    number: <?= Yii::$app->session->get('usess')['number']; ?>
                }
            }));
        };
        ws.onmessage = function (e) {
            //получили ответ
            var resp = $.parseJSON(e.data);
            if (resp.hasOwnProperty('type')) {
                aster.process(resp);
            }
        };

        $(document).on('click', '.aster-panel_items .item .col-btn-answer', function () {
            aster.originate($(this).closest('.item').attr('data-uid'));
            return false;
        });

        $(document).on('click', '.aster-panel_items .item .col-btn-reset', function () {
            aster.hangup($(this).closest('.item').attr('data-uid'));
            return false;
        });



        $('.aster-sidebar_content').jScrollPane({
            autoReinitialise: true,
            verticalGutter: 0,
            hideFocus: true
        });

        $('.aster-search-modal').on('click', function () {
            openModal({
                centered: false,
                url: '/event/patient-search'
            });
        });

        $('.aster-sidebar .controls-ctr .action-reset').on('click', function () {
            aster.reset();
        });

        $('.aster-sidebar .controls-ctr .action-redirect').on('click', function () {
//            var number = $('input', $(this).closest('.input-group')).val();
//            if (number) {
//                aster.redirect(number);
//                $('input', $(this).closest('.input-group')).val('');
//            }            
            var number = $('#aster-redirect-ext').val();
            if (number) {
                aster.redirect(number);
                $('#aster-redirect-ext').val('').selectpicker('refresh');
            }
        });

    });
</script>

<div class="aster-panel">

    <div class="aster-panel_items">

    </div>

</div>

<div class="aster-sidebar">
    <div class="aster-sidebar_inner">
        <div class="aster-sidebar_header pl20">
            <h1><span class="l">Вызов от</span> <span class="n"></span></h1>
            <div class="duration-ctr">
                Длительность:
                <div class="timer">
                    <span>00:00</span>
                </div>
            </div>
            <div class="pr20">
                <div class="controls-ctr row mt15">
                    <div class="col-xs-5">
                        <span class="btn btn-block btn-primary action-reset">Завершить</span>
                    </div>
                    <div class="col-xs-7 col-redirect">
                        <div class="input-group">
                            <select class="form-control selectpicker" id="aster-redirect-ext" data-live-search="true" title="Переадресация">
                                <?php foreach (Yii::$app->user->identity->branch->extensions as $e) { ?>                                
                                    <option value="<?= $e->exten; ?>">[<?= $e->exten; ?>] <?= $e->name; ?></option>
                                <?php } ?>
                            </select>
                            <span class="input-group-btn">
                                <span class="btn btn-default action-redirect">&raquo;</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>