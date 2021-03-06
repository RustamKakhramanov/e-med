<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Перенос события';
$viewUid = uniqid();
?>

<div class="row view-ctr" id="<?= $viewUid; ?>">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <?=
        $this->render('_search-cut', [
            'model' => $searchModel,
            'specialities' => $specialities,
            'subdivisions' => $subdivisions,
            'doctors' => $doctors,
            'viewUid' => $viewUid,
            'event' => $event,
            'data' => $data
        ]);
        ?>

        <div class="tab-content schedule-tab-content mt20">
            <div role="tabpanel" class="tab-pane active day">
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

            <div role="tabpanel" class="tab-pane week">
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
    //серверное время
    var cut_currentDate = moment('<?= date('Y-m-d H:i:s'); ?>');
    //интервал сетки
    var viewInterval = 30;



    var cut_schedule = {
        type: 'day', //день или неделя
        width: 0, //ширина ячейки
        time: null, //инфа о временой колонке
        viewInterval: 30, //интервал сетки
        resizeTimer: null,
        dayTab: null,
        weekTab: null,
        init: function () {
            this.dayTab = $('#' + cut_viewUid + ' .day');
            this.weekTab = $('#' + cut_viewUid + ' .week');
        },
        render: function (type) {
            var that = this;
            if (typeof type != 'undefined') {
                this.type = type;
            }
            this.width = Math.floor(($('#' + cut_viewUid + ' .schedule-tab-content').width() - 54) / 7);
            $(window).off('resize.sh.modal').on('resize.sh.modal', function (e) {
                clearTimeout(that.resizeTimer);
                that.resizeTimer = setTimeout(function () {
                    that.render();
                }, 100);
            });

            return this.type == 'day' ? this._renderDay() : this._renderWeek();
        },
        _renderDay: function () { //отображение по дням
            var
                    that = this,
                    $tab = this.dayTab,
                    $head = $('.schedule-head', $tab),
                    $ctr = $('.schedule-ctr', $tab);

            $('.data-div', $tab).show();
            $('.error-div', $tab).hide();

            if (cut_data.day.length == 0) {
                return this._renderError();
            }

            this.time = this._time();
            if (!this.time) {
                return this._renderError();
            }

            this._renderDayHead();
            this._renderDayGrid();
        },
        _renderDayHead: function () { //шапка дня
            var that = this;
            $('.schedule-head .schedule-table', that.dayTab).html('');
            $.each(cut_data.day, function (k, item) {
                var $day = $('<div class="day-col"></div>');
                $day.width(that.width);
                $day.append('\
                        <div class="day-head day-clickable" data-id="' + item.doctor.id + '">\n\
                            <div class="last_name">' + item.doctor.last_name + '</div>\n\
                            <div class="name">' + item.doctor.name + '</div>\n\
                            <div class="spec">' + item.doctor.spec + '</div>\n\
                        </div>\n\
                ');
                $('.schedule-head .schedule-table', that.dayTab).append($day);
            });
            $('.day-head', that.dayTab).on('click', function () {
                $('#schedulesearch-week_doctor_id').val($(this).attr('data-id')).selectpicker('refresh');
                $('#' + cut_viewUid + ' a[href=".week"]').trigger('click');
            });

            $('.schedule-head .schedule-table', that.dayTab).width(that.width * $('.schedule-head .schedule-table .day-col', that.dayTab).length);
        },
        _renderDayGrid: function () { //сетка дня
            var that = this;
            $('.schedule-ctr .schedule-table', that.dayTab).html('');
            $.each(cut_data.day, function (k, item) {
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
                            $entry = $('<div class="entry entry-exist ' + ((gridStart > cut_currentDate) ? 'entry-actual' : 'entry-past') + '" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '" data-id="' + cell.entry.id + '"><div class="inner"></div></div>');
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
                            if (gridStart > cut_currentDate) {
                                $entry = $('<div class="entry entry-available entry-actual" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></div>');
                            } else {
                                $entry = $('<div class="entry entry-available entry-past" data-doctor_id="' + item.doctor.id + '" data-date="' + item.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner"></div></div>');
                            }
                        }
                    }

                    $entry.height(item.interval / that.viewInterval * 60);
                    $day.append($entry);
                    $('.schedule-ctr .schedule-table', that.dayTab).append($day);

                    gridStart.add(item.interval, 'minutes');
                    if (gridStart >= gridEnd) {
                        canMove = false;
                    }
                }
            });

            var w = that.width * $(' .schedule-ctr .schedule-table .day-col', that.dayTab).length;
            $('.schedule-ctr .schedule-table', that.dayTab).width(w);

            var jsp = $('.schedule-ctr', that.dayTab).data('jsp');
            if (jsp) {
                jsp.reinitialise();
            } else {
                $('.schedule-ctr', that.dayTab).jScrollPane({
                    enableKeyboardNavigation: false,
                    autoReinitialise: false
                });
                jsp = $('.schedule-ctr', that.dayTab).data('jsp');
            }

            $('.schedule-ctr', that.dayTab).off('jsp-initialised').on('jsp-initialised', function () {
                that._timeline();
                that._renderDayScroll()
            });

            this._renderDayScroll();
        },
        _renderDayScroll: function () {
            var that = this,
                    jsp = $('.schedule-ctr', that.dayTab).data('jsp');
            console.log('_renderDayScroll');

            if ($('.schedule-ctr .day-col', that.dayTab).length > 7) {
                $('.schedule-scroll', that.dayTab).show().disableSelection();
                $('.schedule-ctr', that.dayTab).css({top: 88}); //hard

                var sdata = {
                    total: $('.schedule-ctr .schedule-table', that.dayTab).width(),
                    container: $('.schedule-ctr .schedule-content', that.dayTab).width()
                };
                sdata.hidden = sdata.total - sdata.container;
                sdata.percentDelta = $('.ui-slider', that.dayTab).width() - $('.ui-slider', that.dayTab).width() * sdata.container / sdata.total;

                function jScrollTrigger(left) {
                    var percent = Math.ceil(left / sdata.percentDelta * 100);
                    if (percent > 100) {
                        percent = 100;
                    }
                    $('.schedule-ctr .schedule-content', that.dayTab).css('margin-left', -1 * sdata.hidden / 100 * percent);
                    $('.schedule-head .schedule-table', that.dayTab).css('margin-left', -1 * sdata.hidden / 100 * percent);
                    //число скрытых элементов на стрелках
                    var
                            hiddenLeft = Math.floor(sdata.hidden / 100 * percent / that.width),
                            $leftNum = $('.schedule-scroll .control.control-left .num-hidden-cols', that.dayTab),
                            hiddenRight = Math.floor((sdata.hidden - 1 * sdata.hidden / 100 * percent) / that.width),
                            $rightNum = $('.schedule-scroll .control.control-right .num-hidden-cols', that.dayTab)
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
                $('.schedule-scroll .ui-slider-handle', that.dayTab).width(sdata.container / sdata.total * 100 + '%');

                if (parseInt($('.ui-draggable-handle', that.dayTab).css('left')) + $('.ui-draggable-handle', that.dayTab).width() > $('.ui-slider', that.dayTab).width()) {
                    console.log('alarma');
                    jScrollTrigger(0);
                    $('.schedule-scroll .ui-slider-handle', that.dayTab).css('left', '0px');
                }

                $('.schedule-scroll .ui-slider-handle', that.dayTab).draggable({
                    axis: 'x',
                    containment: 'parent',
                    drag: function (event, ui) {
                        jScrollTrigger(ui.position.left);
                    },
                    stop: function (event, ui) {
                        jScrollTrigger(ui.position.left);
                    }
                });

                jScrollTrigger(parseInt($('.schedule-scroll .ui-slider-handle', that.dayTab).css('left')));


                $('.schedule-scroll .control', that.dayTab).on('click', function (e) {
                    var w = $('.schedule-scroll .ui-slider', that.dayTab).width() - $('.ui-slider-handle', that.dayTab).width();
                    var l;
                    if ($(this).hasClass('control-right')) {
                        l = parseFloat($('.ui-slider-handle', that.dayTab).css('left')) + w / 100 * 10;
                        if (l > w) {
                            l = w;
                        }
                    } else {
                        l = parseFloat($('.ui-slider-handle', that.dayTab).css('left')) - w / 100 * 10;
                        if (l < 0) {
                            l = 0;
                        }
                    }
                    $('.ui-slider-handle', that.dayTab).css('left', l);
                    jScrollTrigger(l);
                });
            } else {
                $('.schedule-scroll', that.dayTab).hide();
                $('.schedule-ctr .schedule-content, .schedule-head .schedule-table', that.dayTab).css('margin-left', 0);
                $('.schedule-ctr', that.dayTab).css({top: 61}); //hard
            }
            jsp.reinitialise();
        },
        _time: function () { //заполнение колонки времени
            var
                    minTime = null,
                    maxTime = null;

            $.each((this.type == 'day') ? cut_data.day : cut_data.week.days, function (k, item) {
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

            $('#' + cut_viewUid + ' .' + this.type + ' .schedule-ctr .time-col .time-item').remove();
            var canMove = true;
            while (canMove) {
                $('#' + cut_viewUid + ' .' + this.type + ' .schedule-ctr .time-col').append('<div class="time-item">' + minTime.format('HH:mm') + '</div>');
                minTime.add(this.viewInterval, 'minutes');
                if (minTime >= maxTime) {
                    canMove = false;
                }
            }

            return result;
        },
        _timeline: function () { //линия текущего времени
            var $line = $('#' + cut_viewUid + ' .' + this.type + ' .schedule-ctr .time-line');
            if (this.type == 'day') {
                var gridStart = moment(cut_data.day[0].date + ' ' + this.time.start);
                var gridEnd = moment(cut_data.day[0].date + ' ' + this.time.end);
            } else {
                var gridStart = moment(cut_data.week.days[0].date + ' ' + this.time.start);
                var gridEnd = moment(cut_data.week.days[cut_data.week.days.length - 1].date + ' ' + this.time.end);

            }

            if (cut_currentDate >= gridStart && cut_currentDate <= gridEnd) {
                var b = cut_currentDate.hour() * 60 + cut_currentDate.minute() - (gridStart.hour() * 60 + gridStart.minute());
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
            $('#' + cut_viewUid + ' .' + this.type + ' .data-div').hide();
            $('#' + cut_viewUid + ' .' + this.type + ' .error-div').html('<div class="error-header">' + header + '</div><div class="error-text">' + text + '</div>').show();
            $('#' + cut_viewUid + ' .schedule-loader').hide();

            return false;
        },
        _renderWeek: function () { //отображение недели
            var that = this;
            $('.data-div', that.weekTab).show();
            $('.error-div', that.weekTab).hide();

            if (!cut_data.week.days.length) {
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
            $('.schedule-head .schedule-table', that.weekTab).html('');
            $.each(cut_data.week.days, function (k, item) {
                var $day = $('<div class="day-col"></div>');
                $day.width(that.width);
                var mom = moment(item.date);
                $day.append('\
                        <div class="day-head">\n\
                            <strong>' + mom.format('dddd') + '</strong>\n\
                            <div class="date">' + mom.format('D MMMM') + '</div>\n\
                        </div>\n\
                ');
                $('.schedule-head .schedule-table', that.weekTab).append($day);
            });

            $('.schedule-head .schedule-table', that.weekTab).width(that.width * $('.schedule-head .schedule-table .day-col', that.weekTab).length);
        },
        _renderWeekGrid: function () {
            var that = this;
            $('.schedule-ctr .schedule-table', that.weekTab).html('');
            $.each(cut_data.week.days, function (k, day) {
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
                            $entry = $('<div class="entry entry-exist ' + ((gridStart > cut_currentDate) ? 'entry-actual' : 'entry-past') + '" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '" data-id="' + cell.entry.id + '"><div class="inner"></div></div>');
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
                            if (gridStart > cut_currentDate) {
                                $entry = $('<div class="entry entry-available entry-actual" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></div>');
                            } else {
                                $entry = $('<div class="entry entry-available entry-past" data-doctor_id="' + day.doctor.id + '" data-date="' + day.date + '" data-time="' + gridStart.format('HH:mm') + '"><div class="inner"></div></div>');
                            }
                        }
                    }
                    $entry.height(day.interval / that.viewInterval * 60);
                    $day.append($entry);
                    $('.schedule-ctr .schedule-table', that.weekTab).append($day);

                    gridStart.add(day.interval, 'minutes');
                    if (gridStart >= gridEnd) {
                        canMove = false;
                    }
                }
            });

            var w = that.width * $('.schedule-ctr .schedule-table .day-col', that.weekTab).length;
            $('.schedule-ctr .schedule-table', that.weekTab).width(w);

            this._timeline();

            var jsp = $('.schedule-ctr', that.weekTab).data('jsp');
            if (jsp) {
                jsp.reinitialise();
            } else {
                $('.schedule-ctr', that.weekTab).jScrollPane({
                    enableKeyboardNavigation: false,
                    autoReinitialise: false
                });
                jsp = $('.schedule-ctr', that.weekTab).data('jsp');
            }
        }
    };

    function cut_formHeight() {
        var $ctr = $('#' + cut_viewUid + ' .schedule-tab-content');
        $ctr.height($(window).height() - $ctr.offset().top - 25);
        $('#' + cut_viewUid + ' .schedule-view-ctr').height($(window).height());
    }

    $(document).ready(function () {

        cut_schedule.init();

        cut_formHeight();
        $(window).on('resize.wsh.modal', function () {
            setTimeout(function () {
                cut_formHeight();
            }, 100);
        });

        $('#' + cut_viewUid + ' .input-datepicker-ui').datepicker({
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

        $('#' + cut_viewUid + ' .input-datepicker .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        $('#' + cut_viewUid + ' .selectpicker').selectpicker({
            style: 'btn-select'
        });

        $('#' + cut_viewUid + ' .inc-input .control').on('click', function () {
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

        $('#' + cut_viewUid + ' .select-period .dropdown-menu li a').on('click', function () {
            var $li = $(this).closest('li');
            if (!$li.hasClass('active')) {
                $('.select-period .dropdown-menu li').removeClass('active');
                $li.addClass('active');
                cut_schedule.viewInterval = 1 * $(this).attr('data-interval');
                cut_schedule.render();
            }

            return;
        });

        //todo клик вставки
        $(document).on('click', '#' + cut_viewUid + ' .entry.entry-available', function () {
            var $el = $(this);

            bootbox.confirm('Подтвердите перенос события', function (result) {
                if (result) {
                    $.ajax({
                        url: '/schedule/paste-ajax?id=<?= $event->id; ?>&doctor_id=' + $el.attr('data-doctor_id') + '&date=' + $el.attr('data-date') + '&time=' + $el.attr('data-time'),
                        type: 'get',
                        success: function (resp) {
                            console.log('suc');
                            $el.closest('.modal-wrap').trigger('close');
                        }
                    });
                }

                return;
            });


        });
    });
</script>

<style>
    .modal-event-cut .modal-inner {
        width: 900px;
        padding: 0px 20px;
    }

    .modal-event-cut .schedule-ctr {
        bottom: 20px;
    }
</style>