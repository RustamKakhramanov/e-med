<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\CalendarWeek\CalendarWeek;

?>
<div class="dashboard-right-controls clearfix">
    <a href="#" class="open-week-handler pull-left">
        <span class="ico-open-week-schedule"></span>
    </a>
    <div class="date-ctr pull-right" style="width: 144px;">
        <div class="input-group input-datepicker">
            <input type="text" class="form-control datepicker" id="schedule-date" value="<?= date('d.m.Y'); ?>"
                   readonly=""/>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="input-datepicker-ui"></div>
            </div>
            <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
        </div>
    </div>
</div>

<div class="schedule-outer week">
    <div class="data-div">
        <div class="schedule-head">
            <div class="time-col">
                <div class="select-period">
                    <a class="dropdown-handler" href="#" data-toggle="dropdown">
                        <span class="ico-clock"></span><span class="caret"></span></a>
                    <div class="dropdown-menu">
                        <span class="ml15">Шаг времени:</span>
                        <ul>
                            <li><a href="#" data-interval="15">15 минут</a></li>
                            <li><a href="#" data-interval="20">20 минут</a></li>
                            <li class="active"><a href="#" data-interval="30">30 минут</a></li>
                            <li><a href="#" data-interval="40">40 минут</a></li>
                            <li><a href="#" data-interval="60">60 минут</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="schedule-content">
                <div class="schedule-table clearfix">

                </div>
            </div>
        </div>

        <div class="schedule-ctr native-scroll">
            <div class="time-line"></div>
            <div class="time-col"></div>
            <div class="schedule-content">
                <div class="schedule-table clearfix"></div>
            </div>
        </div>
    </div>
    <div class="error-div"></div>
</div>

<style>
    .dashboard-right-controls > a {
        line-height: 17px;
        text-decoration: none;
        color: #416586;
        display: inline-block;
        margin-top: 8px;
        margin-left: 3px;
    }

    .dashboard-right-controls .open-week-handler span,
    .dashboard-right-controls .close-week-handler span {
        vertical-align: top;
    }

    .schedule-outer {
        top: 72px;
    }

    .schedule-outer .schedule-ctr {
        top: -1px;
        position: relative;
    }

    .schedule-outer .error-div {
        position: absolute;
        left: 0px;
        right: 0px;
        top: 50%;
        text-align: center;
        margin-top: -50px;
        color: #C4C4C4;
    }

    .schedule-outer .error-div .error-header {
        font-size: 20px;
        line-height: 24px;
    }

    .schedule-outer .error-div .error-text {
        font-size: 16px;
        line-height: 24px;
        margin-top: 10px;
    }
</style>

<script>
    //серверное время
    var currentDate = moment('<?= date('Y-m-d H:i:s'); ?>');
    var date_week = '<?=date('d.m.Y', strtotime('monday this week'));?>';
    //данные для таблицы расписания
    var data = <?=json_encode($scheduleData);?>;

    var schedule = {
        type: 'week', //день или неделя
        width: 0, //ширина ячейки
        days: 1,
        time: null, //инфа о временой колонке
        viewInterval: 30, //интервал сетки
        resizeTimer: null,
        init: function () {
            var that = this;
            this.width = Math.floor(($('.schedule-ctr').width() - 54) / this.days);
            $(window).off('resize.sh').on('resize.sh', function (e) {
                clearTimeout(that.resizeTimer);
                that.resizeTimer = setTimeout(function () {
                    that.init();
                }, 100);
            });
            this._renderWeek();
        },
        _renderWeek: function () { //отображение недели
            var that = this;
            $('.schedule-outer .error-div').hide();
            $('.schedule-outer .data-div').show();

            if (!data.length) {
                console.log('01');
                return this._renderError();
            }

            this.time = this._time();
            if (!this.time) {
                console.log('02');
                return this._renderError();
            }

            this._renderWeekHead();
            this._renderWeekGrid();
        },
        _time: function () { //заполнение колонки времени
            var
                minTime = null,
                maxTime = null;

            $.each(data, function (k, item) {
                $.each(item.times, function (kTime, time) {
                    var start = moment(time.start, 'HH:mm');
                    var end = moment(time.end, 'HH:mm');

                    if (minTime == null || start.unix() < minTime.unix()) {
                        minTime = start;
                    }
                    if (maxTime == null || end.unix() > maxTime.unix()) {
                        maxTime = end;
                    }
                });
            });

            if (!(minTime && maxTime)) {
                console.log('03');
                return this._renderError();
            }

            var result = {
                start: minTime.format('HH:mm'),
                end: maxTime.format('HH:mm')
            };

            $('.schedule-ctr .time-col .time-item').remove();
            var canMove = true;
            while (canMove) {
                $('.schedule-ctr .time-col').append('<div class="time-item">' + minTime.format('HH:mm') + '</div>');
                minTime.add(this.viewInterval, 'minutes');
                if (minTime >= maxTime) {
                    canMove = false;
                }
            }

            return result;
        },
        _renderError: function (text, header) { //если нет результата
            if (typeof text === 'undefined') {
                var text = 'С текущими критериями поиска ничего не найдено.<br/>Попробуйте их изменить.',
                    header = 'Нет результатов по запросу'
            }
            $('.schedule-outer .data-div').hide();
            $('.schedule-outer .error-div').show().html('<div class="error-header">' + header + '</div><div class="error-text">' + text + '</div>');

            return false;
        },
        _renderWeekHead: function () {
            var that = this;
            $('.schedule-head .schedule-table').html('');
            $.each(data, function (k, item) {
                var $day = $('<div class="day-col"></div>');
                $day.width(that.width);
                var mom = moment(item.date);
                $day.append('\
                    <div class="day-head">\n\
                        <strong>' + mom.format('dddd') + '</strong>\n\
                        <div class="date">' + mom.format('D MMMM') + '</div>\n\
                    </div>\n\
            ');
                $('.schedule-head .schedule-table').append($day);
            });

            $('.schedule-head .schedule-table').width(that.width * $('.schedule-head .schedule-table .day-col').length);
        },
        _renderWeekGrid: function () {
            var that = this;
            $('.schedule-ctr .schedule-table').html('');
            $.each(data, function (k, day) {
                var
                    $day = $('<div class="day-col"></div>'),
                    gridStart = moment(day.date + ' ' + that.time.start),
                    gridEnd = moment(day.date + ' ' + that.time.end),
                    canMove = true,
                    mom = moment(day.date);

                $day.width(that.width);

                while (canMove) {
                    var cell = false;
                    //поиск в периодах работы
                    $.each(day.times, function (kTime, rowTime) {
                        var dStart = moment(day.date + ' ' + rowTime.start);
                        var dEnd = moment(day.date + ' ' + rowTime.end);

                        if (gridStart >= dStart && gridStart < dEnd) {
                            cell = rowTime;
                            return;
                        }
                    });
                    var $entry;
                    if (!cell) {
                        $entry = $('<div class="entry entry-empty"></div>');
                    } else {
                        if (cell.entry) {
                            $entry = $('<div class="entry entry-exist ' + ((gridStart > currentDate) ? 'entry-actual' : 'entry-past') + '" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '" data-id="' + cell.entry.id + '"><div class="inner"></div></div>');
                            var itext;
                            if (cell.entry.creation) {
                                $entry.addClass('entry-creation');
                                itext = 'редактирует ' + cell.entry.user.name;
                            } else {
                                itext = '<div class="name"><span class="state state-' + cell.entry.state + '"></span>' + cell.entry.patient.name + '</div><div class="phone">' + (cell.entry.patient.phone ? cell.entry.patient.phone : '') + '</div><div class="cost">' + (cell.entry.cost ? cell.entry.cost : '') + '</div>';
                                //добавляем контролы
                                //$entry.append('<div class="action-group action-group-arrow clearfix"><a href="#" class="action action-edit"><span class="action-icon-edit"></span></a><a href="#" class="action action-cancel"><span class="action-icon-cancel"></span></a><a href="#" class="action action-cut"><span class="action-icon-cut"></span></a><a href="#" class="action action-copy"><span class="action-icon-copy"></span></a></div>');
                            }
                            $('.inner', $entry).html(itext);
                        } else {
                            if (gridStart > currentDate) {
                                $entry = $('<div class="entry entry-available entry-actual" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></div>');
                            } else {
                                $entry = $('<div class="entry entry-available entry-past" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner"></div></div>');
                            }
                        }
                    }
                    $entry.height(day.interval / that.viewInterval * 60);
                    $day.append($entry);
                    $('.schedule-ctr .schedule-table').append($day);

                    gridStart.add(day.interval, 'minutes');
                    if (gridStart >= gridEnd) {
                        canMove = false;
                    }
                }
            });

            var w = that.width * $('.schedule-ctr .schedule-table .day-col').length;
            $('.schedule-ctr .schedule-table').width(w);

            this._timeline();
        },
        _timeline: function () { //линия текущего времени
            var $line = $('.schedule-ctr .time-line');
            var gridStart = moment(data[0].date + ' ' + this.time.start);
            var gridEnd = moment(data[0].date + ' ' + this.time.end);

            if (currentDate >= gridStart && currentDate <= gridEnd) {
                var b = currentDate.hour() * 60 + currentDate.minute() - (gridStart.hour() * 60 + gridStart.minute());
                $line.css('top', (b / this.viewInterval * 60) + 'px').show();
                $('.schedule-ctr').scrollTop((b / this.viewInterval * 60));
            } else {
                $line.hide();
            }
        },
    };

    function weekHahdler(dateText) {
        var $parent = $('.dashboard-right-controls .input-datepicker');
        var mom = moment(dateText, 'DD.MM.YYYY').startOf('isoWeek');
        $('input[type="hidden"]', $parent).val(mom.format('DD.MM.YYYY'));
        var start = mom.format('DD MMM');
        var end = mom.add(6, 'days').format('DD MMM');
        $('input.week-text', $parent).val(start.substr(0, 6) + ' - ' + end.substr(0, 6));

        if (mom.format('DD.MM.YYYY') != dateText) {
            $('.dashboard-right').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['dashboard/load-schedule-data']);?>',
                data: {
                    date: dateText
                },
                dataType: 'json',
                success: function (resp) {
                    data = resp;
                    schedule.init();
                    $('.dashboard-right').removeClass('block__loading');
                }
            });
        }
    }

    $(document).ready(function () {
        schedule.init();

        $('.dashboard-right-controls .input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            minDate: '<?=date('d.m.Y');?>',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date).trigger('change');
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });

        $('#schedule-date').on('change', function(){
            $('.dashboard-right').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['dashboard/load-schedule-day-data']);?>',
                data: {
                    date: $(this).val()
                },
                dataType: 'json',
                success: function(resp) {
                    data = resp;
                    schedule.init();
                    $('.dashboard-right').removeClass('block__loading');
                }
            });
        });

        $('.select-period .dropdown-menu li a').on('click', function () {
            var $li = $(this).closest('li');
            if (!$li.hasClass('active')) {
                $('.select-period .dropdown-menu li').removeClass('active');
                $li.addClass('active');
                schedule.viewInterval = 1 * $(this).attr('data-interval');
                schedule.init();
            }
            return;
        });

        $('.dashboard-right-controls .open-week-handler').on('click', function () {
            $('.dashboard-right').addClass('dashboard-right__max block__loading');
            $.ajax({
                url: '<?=Url::to(['dashboard/load-schedule-week'])?>',
                success: function(html) {
                    $('.dashboard-right').html(html).removeClass('block__loading');
                }
            });
        });
    });
</script>