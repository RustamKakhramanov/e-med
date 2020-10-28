<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Расписание, перенос события';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <?=
        $this->render('_search', [
            'model' => $searchModel,
            'specialities' => $specialities,
            'subdivisions' => $subdivisions,
            'doctors' => $doctors
        ]);
        ?>

        <div class="tab-content schedule-tab-content mt20">

            <div role="tabpanel" class="tab-pane active" id="day">
                <div class="data-div">
                    <div class="schedule-scroll">
                        <div class="slider">
                            <div class="ui-slider">
                                <span class="ui-slider-handle"></span>
                            </div>
                        </div>
                        <div class="control control-left">
                            <span class="ico-scroll-left"></span>
                            <span class="num-hidden-cols">&ndash;</span>
                        </div>
                        <div class="control control-right">
                            <span class="num-hidden-cols">10</span>
                            <span class="ico-scroll-right"></span>
                        </div>
                    </div>

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

                    <div class="schedule-ctr">
                        <div class="time-line"></div>
                        <div class="time-col"></div>
                        <div class="schedule-content scroll-pane">
                            <div class="schedule-table clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="error-div" style="display: none;"></div>
            </div>

            <div role="tabpanel" class="tab-pane " id="week">
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

                    <div class="schedule-ctr">
                        <div class="time-line"></div>
                        <div class="time-col"></div>
                        <div class="schedule-content">
                            <div class="schedule-table clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="error-div" style="display: none;"></div>
            </div>

        </div>
        <div class="schedule-loader"><div class="inner"></div></div>
    </div>

</div>

<script>
    var stype = 'day';
    //серверное время
    var currentDate = moment('<?= date('Y-m-d H:i:s'); ?>');
    //интервал сетки
    var viewInterval = 30;
    //данные для таблицы расписания
    var data = <?= json_encode($data); ?>;
    var resizeTimer;

    var schedule = {
        type: 'day', //день или неделя
        width: 0, //ширина ячейки
        time: null, //инфа о временой колонке
        viewInterval: 30, //интервал сетки
        resizeTimer: null,
        render: function (type) {
            if (typeof type != 'undefined') {
                this.type = type;
            }
            this.width = Math.floor(($('.schedule-tab-content').width() - 54) / 7);

            return this.type == 'day' ? this._renderDay() : this._renderWeek();
        },
        _renderDay: function () { //отображение по дням
            var
                    that = this,
                    $tab = $('#day'),
                    $head = $('.schedule-head', $tab),
                    $ctr = $('.schedule-ctr', $tab);

            $('.data-div', $tab).show();
            $('.error-div', $tab).hide();

            if (data.day.length == 0) {
                return this._renderError();
            }

            this.time = this._time();
            if (!this.time) {
                return this._renderError();
            }

            this._renderDayHead();
            this._renderDayGrid();

            $(window).off('resize.sh').on('resize.sh', function () {
                clearTimeout(that.resizeTimer);
                that.resizeTimer = setTimeout(function () {
                    that.render();
                }, 100);
            });

        },
        _renderDayHead: function () { //шапка дня
            var that = this;
            $('#day .schedule-head .schedule-table').html('');
            $.each(data.day, function (k, item) {
                var $day = $('<div class="day-col"></div>');
                $day.width(that.width);
                $day.append('\
                        <div class="day-head day-clickable" data-id="' + item.doctor.id + '">\n\
                            <div class="last_name">' + item.doctor.last_name + '</div>\n\
                            <div class="name">' + item.doctor.name + '</div>\n\
                            <div class="spec">' + item.doctor.spec + '</div>\n\
                        </div>\n\
                ');
                $('#day .schedule-head .schedule-table').append($day);
            });
            $('#day .day-head').on('click', function () {
                $('#schedulesearch-week_doctor_id').val($(this).attr('data-id')).selectpicker('refresh');
                $('a[href="#week"]').trigger('click');
            });

            $('#day .schedule-head .schedule-table').width(that.width * $('#day .schedule-head .schedule-table .day-col').length);
        },
        _renderDayGrid: function () { //сетка дня
            var that = this;
            $('#day .schedule-ctr .schedule-table').html('');
            $.each(data.day, function (k, item) {
                var
                        $day = $('<div class="day-col"></div>'),
                        gridStart = moment(item.date + ' ' + that.time.start),
                        gridEnd = moment(item.date + ' ' + that.time.end),
                        canMove = true;

                $day.width(that.width);
                while (canMove) {
                    var cell = false;
                    //поиск в периодах работы
                    $.each(item.times, function (kTime, rowTime) {
                        var dStart = moment(item.date + ' ' + rowTime.start);
                        var dEnd = moment(item.date + ' ' + rowTime.end);

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
                            $entry = $('<div class="entry entry-exist ' + ((gridStart > currentDate) ? 'entry-actual' : 'entry-past') + '" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '" data-id="' + cell.entry.id + '"><div class="inner"></div></div>');
                            var itext;
                            if (cell.entry.creation) {
                                itext = 'редактирует ' + cell.entry.user.name;
                            } else {
                                itext = '<div class="name"><span class="state state-' + cell.entry.state + '"></span>' + cell.entry.patient.name + '</div><div class="phone">' + (cell.entry.patient.phone ? cell.entry.patient.phone : '') + '</div><div class="cost">' + (cell.entry.cost ? cell.entry.cost : '') + '</div>';
                                //добавляем контролы
                                //$entry.append('<div class="action-group action-group-arrow clearfix"><a href="#" class="action action-edit"><span class="action-icon-edit"></span></a><a href="#" class="action action-cancel"><span class="action-icon-cancel"></span></a><a href="#" class="action action-cut"><span class="action-icon-cut"></span></a><a href="#" class="action action-copy"><span class="action-icon-copy"></span></a></div>');
                            }
                            $('.inner', $entry).html(itext);

                        } else {
                            if (gridStart > currentDate) {
                                $entry = $('<div class="entry entry-available entry-actual" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></div>');
                            } else {
                                $entry = $('<div class="entry entry-available entry-past" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner"></div></div>');
                            }
                        }
                    }

                    $entry.height(item.interval / that.viewInterval * 60);
                    $day.append($entry);
                    $('#day .schedule-ctr .schedule-table').append($day);

                    gridStart.add(item.interval, 'minutes');
                    if (gridStart >= gridEnd) {
                        canMove = false;
                    }
                }
            });

            var w = that.width * $('#day .schedule-ctr .schedule-table .day-col').length;
            $('#day .schedule-ctr .schedule-table').width(w);

            var jsp = $('#day .schedule-ctr').data('jsp');
            if (jsp) {
                jsp.reinitialise();
            } else {
                $('#day .schedule-ctr').jScrollPane({
                    enableKeyboardNavigation: false,
                    autoReinitialise: false
                });
                jsp = $('#day .schedule-ctr').data('jsp');
            }

            $('#day .schedule-ctr').off('jsp-initialised').on('jsp-initialised', function () {
                that._timeline();
                that._renderDayScroll()
            });

            this._renderDayScroll();
        },
        _renderDayScroll: function () {
            var that = this,
                    jsp = $('#day .schedule-ctr').data('jsp');
            console.log('_renderDayScroll');

            if ($('#day .schedule-ctr .day-col').length > 7) {
                $('#day .schedule-scroll').show().disableSelection();
                $('#day .schedule-ctr').css({top: 88}); //hard

                var sdata = {
                    total: $('#day .schedule-ctr .schedule-table').width(),
                    container: $('#day .schedule-ctr .schedule-content').width()
                };
                sdata.hidden = sdata.total - sdata.container;
                sdata.percentDelta = $('#day .ui-slider').width() - $('#day .ui-slider').width() * sdata.container / sdata.total;

                function jScrollTrigger(left) {
                    var percent = Math.ceil(left / sdata.percentDelta * 100);
                    if (percent > 100) {
                        percent = 100;
                    }
                    $('#day .schedule-ctr .schedule-content').css('margin-left', -1 * sdata.hidden / 100 * percent);
                    $('#day .schedule-head .schedule-table').css('margin-left', -1 * sdata.hidden / 100 * percent);
                    //число скрытых элементов на стрелках
                    var
                            hiddenLeft = Math.floor(sdata.hidden / 100 * percent / that.width),
                            $leftNum = $('#day .schedule-scroll .control.control-left .num-hidden-cols'),
                            hiddenRight = Math.floor((sdata.hidden - 1 * sdata.hidden / 100 * percent) / that.width),
                            $rightNum = $('#day .schedule-scroll .control.control-right .num-hidden-cols')
                            ;

                    if (hiddenLeft) {
                        $leftNum.text(hiddenLeft).addClass('active');
                    } else {
                        $leftNum.html('&ndash;').removeClass('active');
                    }

                    if (hiddenRight) {
                        $rightNum.text(hiddenRight).addClass('active');
                    } else {
                        $rightNum.html('&ndash;').removeClass('active');
                    }
                }

                //ширина слайд хандлера
                $('#day .schedule-scroll .ui-slider-handle').width(sdata.container / sdata.total * 100 + '%');

                if (parseInt($('.ui-draggable-handle').css('left')) + $('.ui-draggable-handle').width() > $('.ui-slider').width()) {
                    console.log('alarma');
                    jScrollTrigger(0);
                    $('#day .schedule-scroll .ui-slider-handle').css('left', '0px');
                }

                $('#day .schedule-scroll .ui-slider-handle').draggable({
                    axis: 'x',
                    containment: 'parent',
                    drag: function (event, ui) {
                        jScrollTrigger(ui.position.left);
                    },
                    stop: function (event, ui) {
                        jScrollTrigger(ui.position.left);
                    }
                });

                jScrollTrigger(parseInt($('#day .schedule-scroll .ui-slider-handle').css('left')));


                $('#day .schedule-scroll .control').on('click', function (e) {
                    var w = $('#day .schedule-scroll .ui-slider').width() - $('#day .ui-slider-handle').width();
                    var l;
                    if ($(this).hasClass('control-right')) {
                        l = parseFloat($('#day .ui-slider-handle').css('left')) + w / 100 * 10;
                        if (l > w) {
                            l = w;
                        }
                    } else {
                        l = parseFloat($('#day .ui-slider-handle').css('left')) - w / 100 * 10;
                        if (l < 0) {
                            l = 0;
                        }
                    }
                    $('#day .ui-slider-handle').css('left', l);
                    jScrollTrigger(l);
                });
            } else {
                $('#day .schedule-scroll').hide();
                $('#day .schedule-ctr .schedule-content, #day .schedule-head .schedule-table').css('margin-left', 0);
                $('#day .schedule-ctr').css({top: 61}); //hard
            }
            jsp.reinitialise();
        },
        _time: function () { //заполнение колонки времени
            var
                    minTime = null,
                    maxTime = null;

            $.each((this.type == 'day') ? data.day : data.week.days, function (k, item) {
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
                return this._renderError();
            }

            var result = {
                start: minTime.format('HH:mm'),
                end: maxTime.format('HH:mm')
            };

            $('#' + this.type + ' .schedule-ctr .time-col .time-item').remove();
            var canMove = true;
            while (canMove) {
                $('#' + this.type + ' .schedule-ctr .time-col').append('<div class="time-item">' + minTime.format('HH:mm') + '</div>');
                minTime.add(this.viewInterval, 'minutes');
                if (minTime >= maxTime) {
                    canMove = false;
                }
            }

            return result;
        },
        _timeline: function () { //линия текущего времени
            var $line = $('#' + this.type + ' .schedule-ctr .time-line');
            if (this.type == 'day') {
                var gridStart = moment(data.day[0].date + ' ' + this.time.start);
                var gridEnd = moment(data.day[0].date + ' ' + this.time.end);
            } else {
                var gridStart = moment(data.week.days[0].date + ' ' + this.time.start);
                var gridEnd = moment(data.week.days[data.week.days.length - 1].date + ' ' + this.time.end);

            }

            if (currentDate >= gridStart && currentDate <= gridEnd) {
                var b = currentDate.hour() * 60 + currentDate.minute() - (gridStart.hour() * 60 + gridStart.minute());
                $line.css('top', (b / this.viewInterval * 60) + 'px').show();
            } else {
                $line.hide();
            }
        },
        _renderError: function (text, header) { //если нет результата
            if (typeof text === 'undefined') {
                var text = 'С текущими критериями поиска ничего не найдено.<br/>Попробуйте их изменить.',
                        header = 'Нет результатов по запросу'
            }
            $('#' + this.type + ' .data-div').hide();
            $('#' + this.type + ' .error-div').html('<div class="error-header">' + header + '</div><div class="error-text">' + text + '</div>').show();
            $('.schedule-loader').hide();

            return false;
        },
        _renderWeek: function () { //отображение недели
            var that = this;

            $(window).off('resize.sh');
            clearTimeout(that.resizeTimer);
            $('#week .data-div').show();
            $('#week .error-div').hide();

            if (!data.week.days.length) {
//                if (!(minTime && maxTime)) {
                return this._renderError();
//                }
            }

            this.time = this._time();
            if (!this.time) {
                return this._renderError();
            }

            this._renderWeekHead();
            this._renderWeekGrid();
        },
        _renderWeekHead: function () {
            var that = this;
            $('#week .schedule-head .schedule-table').html('');
            $.each(data.week.days, function (k, item) {
                var $day = $('<div class="day-col"></div>');
                $day.width(that.width);
                var mom = moment(item.date);
                $day.append('\
                        <div class="day-head">\n\
                            <strong>' + mom.format('dddd') + '</strong>\n\
                            <div class="date">' + mom.format('D MMMM') + '</div>\n\
                        </div>\n\
                ');
                $('#week .schedule-head .schedule-table').append($day);
            });

            $('#week .schedule-head .schedule-table').width(that.width * $('#week .schedule-head .schedule-table .day-col').length);
        },
        _renderWeekGrid: function () {
            var that = this;
            $('#week .schedule-ctr .schedule-table').html('');
            $.each(data.week.days, function (k, day) {
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
                    $('#week .schedule-ctr .schedule-table').append($day);

                    gridStart.add(day.interval, 'minutes');
                    if (gridStart >= gridEnd) {
                        canMove = false;
                    }
                }
            });

            var w = that.width * $('#week .schedule-ctr .schedule-table .day-col').length;
            $('#week .schedule-ctr .schedule-table').width(w);

            this._timeline();

            var jsp = $('#week .schedule-ctr').data('jsp');
            if (jsp) {
                jsp.reinitialise();
            } else {
                $('#week .schedule-ctr').jScrollPane({
                    enableKeyboardNavigation: false,
                    autoReinitialise: false
                });
                jsp = $('#week .schedule-ctr').data('jsp');
            }
        }
    };

    function formHeight() {
        var $ctr = $('.schedule-tab-content');
        $ctr.height($(window).height() - $ctr.offset().top);
        $('.schedule-view-ctr').height($(window).height());
    }

    $(document).ready(function () {

        formHeight();
        $(window).on('resize', function () {
            setTimeout(function () {
                formHeight();
            }, 100);
        });

        $('.input-datepicker-ui').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });

        $('.input-datepicker .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        $('.selectpicker').selectpicker({
            style: 'btn-select'
        });

        $('.inc-input .control').on('click', function () {
            var $p = $(this).parent();
            var v = parseInt($('input', $p).val());

            if ($(this).attr('data-action') == 'plus') {
                v++;
            } else {
                v--;
                if (v < 0) {
                    v = 0;
                }
            }

            $('input', $p).val(v);
            $('span.value', $p).text(v);

            return false;
        });

        $('.select-period .dropdown-menu li a').on('click', function () {
            var $li = $(this).closest('li');
            if (!$li.hasClass('active')) {
                $('.select-period .dropdown-menu li').removeClass('active');
                $li.addClass('active');
                //data.week.interval = $(this).attr('data-interval');
                viewInterval = $(this).attr('data-interval');

                if (stype == 'day') {
                    renderDay();
                } else {
                    renderWeek();
                }
            }

            return;
        });

        //todo клик вставки
        $(document).on('click', '.entry.entry-available', function () {
            window.location = '/schedule/paste?id=<?= $event->id; ?>&doctor_id=' + $(this).attr('data-doctor_id') + '&date=' + $(this).attr('data-date') + '&time=' + $(this).attr('data-time');
        });
    });
</script>