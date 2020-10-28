<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

<script>
    var queueWs,
            queueWsInterval,
            queueWsUrl = '<?= Yii::$app->params['aws_path']; ?>';

    var queue = {
        calls: {},
        current: {},
        durationInterval: false,
        tableIntervals: false,
        listShow: false,
        abandonData: [],
        callmeData: [],
        data: {},
        push: function (data) {

        },
        remove: function (data) {

        },
        process: function (resp) {
            if (resp.type == 'init') {
                this._init(resp.data);
            }

            if (resp.type == 'in') {
                this.state();
            }

            if (resp.type == 'out') {
                this.state();
            }

            if (resp.type == 'abandonloaded') {
                this._abandonloaded(resp.data);
            }

            if (resp.type == 'callmeloaded') {
                this._callmeloaded(resp.data);
            }
        },
        _init: function (data) {
            this.data = data;
            var $p = $('.aster-queue-panel');
            var that = this;
            $('.summary-group_abandoned .col_val', $p).text(data.abandoned);
            $('.summary-group_calls .col_val', $p).text(data.calls);
            $('.summary-group_callme .col_val', $p).text(data.callme);

            var durationWait = moment.duration(data.wait, 'seconds');
            if (this.durationInterval) {
                clearInterval(this.durationInterval);
            }
            if (data.wait > 0 || data.items.length) {
                //durationWait.add(1, 's');
                $('.summary-group_time .col_val', $p).text(pad(durationWait.minutes(), 2) + ':' + pad(durationWait.seconds(), 2));
                this.durationInterval = setInterval(function () {
                    durationWait.add(1, 's');
                    $.each(that.data.items, function (k, v) {
                        that.data.items[k].wait++;
                    });
                    $('.summary-group_time .col_val', $p).text(pad(durationWait.minutes(), 2) + ':' + pad(durationWait.seconds(), 2));
                }, 1000);
            } else {
                $('.summary-group_time .col_val', $p).text('-');
            }
            $p.show();

            $('.summary-group_abandoned', $p).off('click').on('click', function () {
                that.abandonModal();
            });

            $('.summary-group_callme', $p).off('click').on('click', function () {
                that.callmeModal();
            });

            this.renderList();
        },
        renderList: function () {
            var $p = $('.aster-queue-panel');
            var $table = $('.queue-items tbody', $p);
            var that = this;
            $table.html('');
            clearInterval(this.tableInterval);

            if (this.listShow) {
                $.each(that.data.items, function (k, item) {
                    var d = moment.duration(item.wait, 'seconds');
                    $table.append('\n\
                        <tr data-id="' + item.uniqueid + '">\n\
                            <td class="td_num">' + item.position + '</td>\n\
                            <td class="td_name">' + item.name + '</td>\n\
                            <td class="td_time" data-time="' + item.wait + '">' + pad(d.minutes(), 2) + ':' + pad(d.seconds(), 2) + '</td>\n\
                        </tr>\n\
                    ');
                });

                if (that.data.items.length) {
                    var durationTableWait = moment.duration(0, 'seconds');
                    this.tableInterval = setInterval(function () {
                        durationTableWait.add(1, 's');
                        $('tr', $table).each(function () {
                            var tempDuration = moment.duration(1 * $('.td_time', $(this)).attr('data-time') + durationTableWait.asSeconds(), 'seconds');
                            $('.td_time', $(this)).text(pad(tempDuration.minutes(), 2) + ':' + pad(tempDuration.seconds(), 2));
                        });
                    }, 1000);
                } else {
                    $table.append('<tr class="placeholder"><td colspan="3">Нет абонентов в очереди</td></tr>');
                }
            }
        },
        toggle: function () {
            this.listShow = !this.listShow;
            this.renderList();
        },
        state: function () {
            queueWs.send(JSON.stringify({
                action: 'queuestatus',
                data: {}
            }));
        },
        abandonModal: function () {
            openModal({
                class: 'modal-aster-abandon',
                centered: false,
                html: '\
                    <div class="pl20 pr20 pb20" style="width:650px;">\n\
                        <h1>Пропущенные звонки</h1>\n\
                        <div class="items-ctr mt10">Загрузка..</div>\n\
                    </div>',
                onOpen: function (m) {
                }
            });

            queueWs.send(JSON.stringify({
                action: 'abandonlist',
                data: {
                    queue: <?= Yii::$app->user->identity->getExtraParam('queue_number'); ?>
                }
            }));
        },
        _abandonloaded: function (data) {
            var that = this;
            var $m = $('.modal-aster-abandon');

            if ($m.size) {
                that.abandonData = data;

                if (!$('.filter-ctr', $m).length) {
                    $('.items-ctr', $m).before('\n\
                        <ul class="tabs tabs-small tabs-form filter-ctr clearfix">\n\
                            <li class=""><a href="#" data-toggle="tab" data-status="" aria-expanded="false">Все</a></li>\n\
                            <li class="active"><a href="#" data-toggle="tab" data-status="wait" aria-expanded="true">Ожидают</a></li>\n\
                            <li class=""><a href="#" data-toggle="tab" data-status="success" aria-expanded="false">Успешные</a></li>\n\
                            <li class=""><a href="#" data-toggle="tab" data-status="fail" aria-expanded="false">Неудачные</a></li>\n\
                        </ul>\n\
                    ');

                    $('.filter-ctr a', $m).on('click', function () {
                        var status = $(this).attr('data-status');
                        if (status) {
                            $('.table-list tbody tr', $m).addClass('hidden');
                            $('.table-list tbody tr.status-' + status, $m).removeClass('hidden');
                        } else {
                            $('.table-list tbody tr', $m).removeClass('hidden');
                        }
                    });
                }

                //пропущенные
                if (data.length) {
                    var activeStatus = $('.filter-ctr .active a', $m).attr('data-status');
                    var $table = $('<table class="table-list"><thead><tr>\n\
                        <th class="td-num">#</th>\n\
                        <th class="td-phone">Телефон</th>\n\
                        <th class="td-date">Время</th>\n\
                        <th class="td-pos">Позиция</th>\n\
                        <th class="td-wait">Ожидание (сек)</th>\n\
                        <th class="td-status">Статус</th>\n\
                        <th class="td-ctrl"></th>\n\
                    </tr></thead><tbody></tbody></table>');
                    $.each(data, function (k, v) {
                        var $tr = $('<tr data-id="' + v.id + '" class="status-' + v.status + '"></tr>');
                        if (v.status != activeStatus) {
                            $tr.addClass('hidden');
                        }

                        $tr.append('<td>' + (k + 1) + '</td>');
                        $tr.append('<td>' + v.phone + '</td>');
                        $tr.append('<td>' + v.time + '</td>');
                        $tr.append('<td>' + v.extra[1] + ' &rarr; ' + v.extra[0] + '</td>');
                        $tr.append('<td>' + v.extra[2] + '</td>');
                        $tr.append('<td><span class="status-val">' + v.status + '</span></td>');
                        $tr.append('<td class="text-right"><span class="call-handler"><i class="fa fa-phone"></i></span></td>');

                        $('tbody', $table).append($tr);
                    });
                    $('.items-ctr', $m).html($table);

                    $('.call-handler', $m).off('click').on('click', function () {
                        var key = $(this).closest('tr').index();
                        that._abandonCall(key);
                    });
                } else {
                    $('.items-ctr', $m).html('<div class="empty-handler">Нет звонков</div>');
                }
            }
        },
        _abandonCall: function (key) {
            var that = this;
            queueWs.send(JSON.stringify({
                action: 'abandoncall',
                data: {
                    sess_id: <?= Yii::$app->session->get('usess')['sess_id']; ?>,
                    item: that.abandonData[key]
                }
            }));
            $('.modal-aster-abandon').trigger('close');
        },
        callmeModal: function () {
            openModal({
                class: 'modal-aster-callme',
                centered: false,
                html: '\
                    <div class="pl20 pr20 pb20" style="width:650px;">\n\
                        <h1>Звонки с сайта</h1>\n\
                        <div class="filter-ctr clearfix">\n\
                            <div class="pull-left col-date">\n\
                                <div class="input-group input-datepicker">\n\
                                    <input type="text" class="form-control datepicker" id="modal-aster-input-date" value="<?= date('d.m.Y'); ?>"/>\n\
                                    <div class="dropdown-menu dropdown-menu-right">\n\
                                        <div class="input-datepicker-ui"></div>\n\
                                    </div>\n\
                                    <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>\n\
                                </div>\n\
                            </div>\n\
                            <div class="pull-left ml20 col-status">\n\
                                <select class="form-control selectpicker" id="modal-aster-input-status">\n\
                                    <option value="">Все</option>\n\
                                    <option value="wait" selected="">Ожидают</option>\n\
                                    <option value="success">Успешные</option>\n\
                                    <option value="fail">Неудачные</option>\n\
                                </select>\n\
                            </div>\n\
                            <div class="pull-left ml20 col-submit">\n\
                                <span class="btn btn-default">Найти</span>\n\
                            </div>\n\
                        </div>\n\
                        <div class="items-ctr mt15">Загрузка..</div>\n\
                    </div>',
                onOpen: function (m) {
                    $('.selectpicker', m).selectpicker();
                    $('.input-datepicker .input-datepicker-ui', m).datepicker('destroy').datepicker({
                        dateFormat: 'dd.mm.yy',
                        prevText: '&larr;',
                        nextText: '&rarr;',
                        showOtherMonths: true,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '1950:2020',
                        onSelect: function (date) {
                            var $parent = $(this).closest('.input-datepicker');
                            $('input', $parent).val(date);
                            $('.dropdown-handler', $parent).dropdown('toggle');
                        }
                    });
                    $('.col-submit .btn', m).on('click', function () {
                        var data = {
                            queue: <?= Yii::$app->user->identity->getExtraParam('queue_number'); ?>,
                            date: $('#modal-aster-input-date').val(),
                            status: $('#modal-aster-input-status').val(),
                        };
                        queueWs.send(JSON.stringify({
                            action: 'callmelist',
                            data: data
                        }));

                        return false;
                    }).trigger('click');
                }
            });
        },
        _callmeloaded: function (data) {
            var that = this;
            var $m = $('.modal-aster-callme');
            if ($m.size) {
                that.callmeData = data;
                if (data.length) {
                    var $table = $('<table class="table-list"><thead><tr>\n\
                        <th class="td-num">#</th>\n\
                        <th class="td-phone">Телефон</th>\n\
                        <th class="td-name">Имя</th>\n\
                        <th class="td-datef">Дата</th>\n\
                        <th class="td-pos">Тема</th>\n\
                        <th class="td-status">Статус</th>\n\
                        <th class="td-ctrl"></th>\n\
                    </tr></thead><tbody></tbody></table>');
                    $.each(data, function (k, v) {
                        var $tr = $('<tr data-id="' + v.id + '" class="status-' + v.status + '"></tr>');
                        $tr.append('<td>' + (k + 1) + '</td>');
                        $tr.append('<td>' + v.number + '</td>');
                        $tr.append('<td>' + v.name + '</td>');
                        $tr.append('<td>' + v.date + '</td>');
                        $tr.append('<td>' + v.theme + '</td>');
                        $tr.append('<td><span class="status-val">' + v.status + '</span></td>');
                        $tr.append('<td class="text-right"><span class="call-handler"><i class="fa fa-phone"></i></span></td>');

                        $('tbody', $table).append($tr);
                    });
                    $('.items-ctr', $m).html($table);

                    $('.call-handler', $m).off('click').on('click', function () {
                        var key = $(this).closest('tr').index();
                        that._callmeCall(key);
                    });
                } else {
                    $('.items-ctr', $m).html('<div class="empty-handler">Нет записей</div>');
                }
            }
        },
        _callmeCall: function (key) {
            var that = this;
            queueWs.send(JSON.stringify({
                action: 'callme',
                data: {
                    sess_id: <?= Yii::$app->session->get('usess')['sess_id']; ?>,
                    item: that.callmeData[key]
                }
            }));
            $('.modal-aster-callme').trigger('close');
        }
    };

    var m;
    $(document).ready(function () {
        queueWs = new ReconnectingWebSocket(queueWsUrl);
        queueWs.onopen = function (e) {
            console.log('connection queue established');
            queueWs.send(JSON.stringify({
                action: 'login',
                data: {
                    user_id: <?= Yii::$app->user->identity->id; ?>,
                    number: <?= Yii::$app->user->identity->getExtraParam('queue_number'); ?>
                }
            }));
            queue.state();
        };
        queueWs.onmessage = function (e) {
            //получили ответ
            var resp = $.parseJSON(e.data);
            if (resp.hasOwnProperty('type')) {
                queue.process(resp);
            }
        };

        $('.aster-queue-panel .toggle-items').on('click', function () {
            $('.aster-queue-panel .queue-items').toggle();
            $(this).toggleClass('active');
            queue.toggle();
        });

    });
</script>

<div class="aster-queue-panel" style="display: block;">
    <div class="title"><?= Yii::$app->user->identity->getExtraParam('queue_number'); ?></div>
    <div class="summary">
        <div class="summary-group summary-group_abandoned has_action">
            <div class="col col_title">непринятые</div>
            <div class="col col_val">-</div>
        </div>
        <div class="summary-group summary-group_callme has_action">
            <div class="col col_title">с сайта</div>
            <div class="col col_val">-</div>
        </div>
        <div class="summary-group summary-group_calls">
            <div class="col col_title">ожидают</div>
            <div class="col col_val">-</div>
        </div>
        <div class="summary-group summary-group_time">
            <div class="col col_title">длительность</div>
            <div class="col col_val">-</div>
        </div>
    </div>
    <div class="toggle-items">
        <span class="ico-view-list"></span>
    </div>

    <div class="queue-items">
        <table>
            <thead>
                <tr>
                    <th class="td_num">Позиция</th>
                    <th class="td_name">Абонент</th>
                    <th class="td_time">Ожидание</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>