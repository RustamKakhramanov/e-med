<?php

use yii\helpers\Html;
?>

<div class="hidden">
    <?= $form->field($model, 'schedule')->hiddenInput()->label(false); ?>
</div>

<ul class="tabs clearfix mt20" id="type-detail-switch">
    <li class="active"><a href="#schedule-temp" data-toggle="tab">Шаблон</a></li>
    <li class=""><a href="#schedule-calend" data-toggle="tab">Календарный</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="schedule-temp">
        <div class="row mt20">
            <div class="col-xs-2">
                <label>Периодичность</label>
                <?php
                $periods = [
                    'week' => 'Неделя',
                    'month' => 'Месяц'
                ];
                echo Html::dropDownList('schedule[temp][type]', null, $periods, [
                    'class' => 'selectpicker',
                    'id' => 'schedule-temp-type'
                ]);
                ?>
            </div>
        </div>
        <div class="schedule-settings"></div>
    </div>

    <div role="tabpanel" class="tab-pane pt20" id="schedule-calend">
        <table class="form-table auto mt5" style="min-width:0px;">
            <tr>
                <td style="width: 148px;">
                    <label>Период с</label>
                    <div class="input-group input-datepicker datepicker-sch-calend">
                        <input type="text" class="form-control datepicker" name="schedule-calend-start" readonly="" value="<?= date('d.m.Y'); ?>"/>
                        <div class="dropdown-menu dropdown-menu-left">
                            <div class="input-datepicker-ui"></div>
                        </div>
                        <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
                    </div>
                </td>
                <td style="width: 148px;">
                    <label>Период по</label>
                    <div class="input-group input-datepicker datepicker-sch-calend">
                        <input type="text" class="form-control datepicker" name="schedule-calend-end" readonly="" value="<?= date('d.m.Y', strtotime('+1 month')); ?>"/>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="input-datepicker-ui"></div>
                        </div>
                        <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
                    </div>
                </td>
                <?php if ($model->id) { ?>
                    <td>
                        <label>&nbsp;</label>
                        <a href="#" class="btn btn-primary schedule-calend_view-handler">Отобразить</a>
                    </td>
                    <td>
                        <label>&nbsp;</label>
                        <a href="#" class="btn btn-default schedule-calend_clear-handler">Очистить</a>
                    </td>
                <?php } ?>
                <td>
                    <label>&nbsp;</label>
                    <a href="#" class="btn btn-default schedule-calend_temp-handler">Заполнить по шаблону</a>
                </td>

            </tr>
        </table>
        <div class="schedule-settings"></div>
    </div>
</div>

<?php
$dataSchedule = [
    'temp' => [
        'type' => 'week',
        'items' => []
    ],
    'calend' => [
        'type' => null,
        'items' => []
    ]
];

//dd($model->scheduleTemplate['data_days']);exit;

if (!$model->id) { //создание
    $dateStart = date('Y-m-d', strtotime('Mon this week'));

    for ($i = 0; $i < 7; $i++) {
        $dataSchedule['temp']['items'][] = [
            'num' => $i,
            'enabled' => true,
            'date' => $dateStart,
            'times' => []
        ];
        $dateStart = date('Y-m-d', strtotime($dateStart . ' + 1 days'));
    }
} else { //редактирование
    if ($model->scheduleTemplate) {
        $dataSchedule['temp']['type'] = $model->scheduleTemplate['type'];
        $dataSchedule['temp']['items'] = $model->scheduleTemplate['data_days'];
        //$dataSchedule['temp']['items'] = is_string($model->scheduleTemplate['data_days']) ? json_decode($model->scheduleTemplate['data_days'], true) : $model->scheduleTemplate['data_days'];
    }
}
?>

<script>

    var interval = 60;

    var dataSchedule = <?= json_encode($dataSchedule); ?>

    //данные для обработки кликов выбора
    var selectData = [];

    //тип отображаемого графика (по умолчанию фиксированный)
    var type = 'temp';

    function scheduleSync() {
        $('#doctor-schedule').val(JSON.stringify(dataSchedule));
        $('#schedule .day .header .help-block-error').remove();
    }

    function changeTemp(type) {
        var countDays = (type == 'week') ? 7 : 31;
        var oldItems = dataSchedule.temp.items;
        dataSchedule.temp.items = [];
        dataSchedule.temp.type = type;

        var d = moment().startOf('week');
        for (var i = 0; i < countDays; i++) {
            var item = {
                num: i,
                enabled: true,
                date: d.format('YYYY-MM-DD'),
                times: []
            };

            //поиск прошлого айтема
            var prevItem = false;
            $.each(oldItems, function (k, v) {
                if (v.num == i) {
                    prevItem = v;
                    return;
                }
            });

            //если существовал прошлый - то взять его данные
            if (prevItem) {
                item.enabled = prevItem.enabled;
                item.times = prevItem.times;
            }

            dataSchedule.temp.items.push(item);

            d.add(1, 'days');
        }

        scheduleSync();
        createGrid();
    }

    //создать дни и сетки выбора времени
    function createGrid() {

        $('#schedule-' + type + ' .schedule-settings').html('');

        $.each(dataSchedule[type].items, function (k, day) {

            var mom = moment(day.date + ' 00:00');
            var end = moment(day.date + ' 24:00');

            var $day = $('<div class="day" data-num="' + day.num + '"></div>');

            var dUniq = uniqid();
            var colapseUniq = uniqid();

            //формат названия дня
            var dayName = '';
            if (type == 'temp') {
                if (dataSchedule.temp.type == 'week') {
                    dayName = capitalize(mom.format('dddd'));
                } else {
                    dayName = (day.num + 1);
                }
            } else {
                dayName = capitalize(mom.format('dddd')) + ' (' + mom.format('DD.MM.YYYY') + ')';
            }

            $day.append('\n\
                        <div class="header">\n\
                            <div class="info clearfix">\n\
                                <div class="checkbox">\n\
                                    <input id="' + dUniq + '" type="checkbox"/>\n\
                                    <label for="' + dUniq + '" data-name="' + dayName + '">' + dayName + '</label>\n\
                                </div>\n\
                                <div class="selected-times"></div>\n\
                            </div>\n\
                            <a href="#' + colapseUniq + '" class="toggler" data-toggle="collapse">\n\
                                <span class="ico-collapse"></span>\n\
                            </a>\n\
                        </div>\n\
                        <div class="content collapse" id="' + colapseUniq + '" aria-expanded="true"><table><tr><td></td></tr></table></div>\n\
                    ');

            if (day.enabled) {
                $day.addClass('day-enabled');
                $('> .content', $day).addClass('in');
                $('> .header input[type="checkbox"]', $day).prop('checked', true);
            } else {
                $('.checkbox label', $day).text($('.checkbox label', $day).text() + ' — выходной');
                $('.toggler', $day).addClass('collapsed');
            }

            $('#schedule-' + type + ' .schedule-settings').append($day);

            //включение выключение дня
            $('input[type="checkbox"]', $day).on('change', function () {
                var $d = $(this).closest('.day');
                var $toggler = $('.toggler', $d);
                var $label = $('.checkbox label', $d);

                if ($d.hasClass('day-enabled') && !$toggler.hasClass('collapsed')) {
                    $('.toggler', $d).trigger('click');
                }

                if ($d.hasClass('day-enabled')) {
                    $label.text($label.attr('data-name') + ' — выходной');
                    dataSchedule[type].items[k].enabled = false;
                } else {
                    $label.text($label.attr('data-name'));
                    dataSchedule[type].items[k].enabled = true;
                }

                scheduleSync();

                $d.toggleClass('day-enabled');
            });


            //var $day = $(type + ' .schedule-settings .day:eq(' + k + ')');

            var canMove = true;
            var $table = $('<table><tr></tr></table>');

            while (canMove) {

                $('tr', $table).append('<td><div class="single" data-time="' + mom.format('HH:mm') + '">' + mom.format('HH:mm') + '</div></td>');

                mom.add(interval, 'minutes');
                if (mom >= end) {
                    canMove = false;
                }
            }

            $('.content', $day).html($table);

            selectData.push({
                first: false,
                second: false,
                firstDiv: null,
                secondDiv: null
            });

            $('.single', $day).off('click.init').on('click.init', function (e_top) {
                var $single = $(this);

                //e.stopPropagation();

                if ($single.hasClass('clicked')) {
                    $single.removeClass('clicked');
                    $('.time-div', $day).remove();
                } else {
                    $('.single', $day).removeClass('clicked');
                    $single.addClass('clicked');
                    var $div = createTimeDiv($single.closest('td'));

                    //если клик не по таймдиву - то удаляем все открытые
                    $(document).on('click.timediv', function (e) {

                        //хук для первого клика
                        if (e.target == $single[0]) {
                            return;
                        }

                        if ($(e.target).closest('.time-div')[0] != $div[0]) {
                            $('.single', $day).removeClass('clicked');
                        } else {
                            if (!selectData[k].first) {
                                selectData[k].first = $(e.target).attr('data-time');
                                selectData[k].firstDiv = $single;
                                $single.addClass('first-set');
                            } else {
                                selectData[k].second = $(e.target).attr('data-time');
                                selectData[k].secondDiv = $single;

                                $single.addClass('second-set');
                                unionTime(k);
                            }
                        }

                        $('.time-div', $day).remove();
                        $(document).off('click.timediv');

                        return false;
                    });
                }
            });
        });

        createRanges();
    }

    //объеденить выбранные блоки времени
    function unionTime(ind) {
        var time = selectData[ind];

        //если конец перепутан с началом
        if (timeToInt(time.first) > timeToInt(time.second)) {

            var temp = {
                second: time.second,
                secondDiv: time.secondDiv
            };

            time.second = time.first;
            time.secondDiv = time.firstDiv;
            time.first = temp.second;
            time.firstDiv = temp.secondDiv;
        }

        //убрать перекрытые части
        var s = timeToInt(time.first);
        var e = timeToInt(time.second);

        var deletedKeys = [];
        $.each(dataSchedule[type].items[ind].times, function (kTime, time) {
            if (s < timeToInt(time.start) && timeToInt(time.end) < e) {
                var $day = $('#schedule-' + type + ' .schedule-settings .day:eq(' + ind + ')');
                $('#schedule-' + type + ' .selected-range[data-range="' + time.start + '-' + time.end + '"]').remove();
                deletedKeys.push(kTime);
            }
        });

        //вынес удаление потому что в первом $.each ебланит
        $.each(deletedKeys, function (k, kTime) {
            dataSchedule[type].items[ind].times.splice(kTime - k, 1);
        });

        dataSchedule[type].items[ind].times.push({
            start: time.first,
            end: time.second
        });

        scheduleSync();

        //убить классы выделения
        time.firstDiv.removeClass('first-set second-set clicked');
        time.secondDiv.removeClass('first-set second-set clicked');

        time.first = false;
        time.second = false;

        createRanges();
    }

    function timeToInt(time) {
        return 1 * time.replace(':', '');
    }

    function strToTime(str) {
        str = pad(str, 4);
        return [str.slice(0, 2), ':', str.slice(2)].join('');
    }

    function pad(num, size) {
        var s = num + "";
        if (s) {
            while (s.length < size)
                s = "0" + s;
        }
        return s;
    }

    //отрисовать ренжи
    function createRanges() {
        $('#schedule-' + type + ' .schedule-settings .day .selected-range').remove();
        $('#schedule-' + type + ' .schedule-settings .day .selected-times').html('');

        $.each(dataSchedule[type].items, function (k, day) {
            var $dayContent = $('#schedule-' + type + ' .schedule-settings .day:eq(' + k + ') .content');
            var totalDayLong = 0; //минуты
            
            if ($dayContent.length) {

                //обход периодов
                $.each(day.times, function (kTime, time) {
                    var $selected = $('<div class="selected-range" data-range="' + time.start + '-' + time.end + '">' + time.start + ' — ' + time.end + '</div>');
                    $dayContent.append($selected);

                    var diff = moment(moment().format('YYYY-MM-DD') + ' ' + time.end).unix() - moment(moment().format('YYYY-MM-DD') + ' ' + time.start).unix();
                    totalDayLong += diff / 60;
                    $('.header .selected-times', $dayContent.closest('.day')).append('<span>' + time.start + '—' + time.end + '</span>');

                    var startHour = strToTime(Math.floor(timeToInt(time.start) / 100) * 100);
                    var endHour = strToTime(Math.floor(timeToInt(time.end) / 100) * 100);

                    if (endHour === '24:00') {
                        endHour = '23:00';
                    }

                    var l = $('.single[data-time="' + startHour + '"]', $dayContent).offset().left - $dayContent.offset().left;
                    var r = $('.single[data-time="' + endHour + '"]', $dayContent).offset().left + $('.single[data-time="' + endHour + '"]', $dayContent).parent().outerWidth() - $dayContent.offset().left;

                    $selected.css({
                        left: Math.floor(l),
                        width: Math.floor(r - l - 2)
                    });
                });

                $('.header .selected-times', $dayContent.closest('.day')).append('<span class="hours">' + calcTotalTimeText(totalDayLong) + '</span>');

                $('.selected-range', $dayContent).off('click').on('click', function (e) {
                    //e.stopPropagation();

                    var $range = $(this);

                    if ($range.hasClass('selected-range-clicked')) {
                        $range.removeClass('selected-range-clicked');
                        $('.time-div-delete', $dayContent).remove();
                    } else {
                        $('.selected-range', $dayContent).removeClass('selected-range-clicked');
                        $range.addClass('selected-range-clicked');
                        var $div = createTimeDivDelete($range);

                        //если клик не по таймдиву - то удаляем все открытые
                        $(document).on('click.timediv-selected', function (e) {

                            //хук для первого клика
                            if (e.target == $range[0]) {
                                return;
                            }

                            if ($(e.target).closest('.time-div-delete')[0] != $div[0]) { //если кликнули не по кнопке удалить
                                $range.removeClass('selected-range-clicked');
                                $('.time-div-delete', $dayContent).remove();
                            } else { //экшн удаления ренжа
                                $('.time-div-delete', $dayContent).remove();
                                var rangeVal = $range.attr('data-range').split('-');
                                deleteTime(rangeVal[0], rangeVal[1], k);
                            }

                            $(document).off('click.timediv-selected');
                            return false;
                        });
                    }
                });
            }
        });
    }

    function getNumEnding(iNumber, aEndings) {
        var sEnding, i;
        iNumber = iNumber % 100;
        if (iNumber >= 11 && iNumber <= 19) {
            sEnding = aEndings[2];
        }
        else {
            i = iNumber % 10;
            switch (i)
            {
                case (1):
                    sEnding = aEndings[0];
                    break;
                case (2):
                case (3):
                case (4):
                    sEnding = aEndings[1];
                    break;
                default:
                    sEnding = aEndings[2];
            }
        }
        return sEnding;
    }

    //
    function calcTotalTimeText(minutes) {

        if (!minutes) {
            return '';
        }

        var hours = Math.floor(minutes / 60);
        var result = hours + ' ' + getNumEnding(hours, ['час', 'часа', 'часов']);

        if (minutes % 60 > 0) {
            result += ' ' + (minutes % 60) + ' ' + getNumEnding(minutes % 60, ['минута', 'минуты', 'минут']);
        }

        return result;
    }

    /**
     * удалить выбранный период
     * @param string s старт
     * @param string e конец
     * @param int k кей дня
     */
    function deleteTime(s, e, k) {
        var key;
        $.each(dataSchedule[type].items[k].times, function (kTime, time) {
            if (time.start == s && time.end == e) {
                key = kTime;
                return;
            }
        });
        dataSchedule[type].items[k].times.splice(key, 1);

        scheduleSync();
        createRanges();
    }

    //создать выбор времени
    function createTimeDiv($elem) {
        var $block = $('> div', $elem);
        var offset = $elem.offset();
        var $content = $elem.closest('.content');

        $('.time-div', $content).remove();

        var dayOffset = $content.offset();

        var left = offset.left - dayOffset.left;
        var top = offset.top - dayOffset.top + 34; // 34 - высота ячейки

        var $div = $('<div class="time-div clearfix"></div>');

        //создание минут
        var mom = moment(moment().format('YYYY-MM-DD') + ' ' + $block.attr('data-time'));
        var check24 = mom.format('HH:mm') == '23:00' ? 1 : 0;
        for (var i = 1; i <= interval / 5 + check24; i++) {
            var t = mom.format('HH:mm');
            if (t == '00:00' && check24) {
                t = '24:00';
            }
            $div.append('<a href="#" data-time="' + t + '">' + t + '</a>');
            mom.add(5, 'minutes');
        }

        $content.append($div);

        var w = $div.outerWidth(); // ширина time-div

        //пытаемся отобразить посередине от нажатой ячейки
        var l = left - w / 2 + ($elem.width() - 1) / 2;

        //если не влазит - то отображаем сколько можно
        if (l < 1) {
            l = 1;
        }

        //проверяем правый край
        if (left + w >= $content.width() + 1) {
            l = $content.width() - w - 1;
        }

        $div.css({
            left: l,
            top: top,
            opacity: 1
        });
        //$content.append($div);

        return $div;
    }

    //создать удаление ренжа
    function createTimeDivDelete($elem) {

        var offset = $elem.offset();
        var $content = $elem.closest('.content');

        $('.time-div-delete', $content).remove();

        var dayOffset = $content.offset();
        var left = offset.left - dayOffset.left;
        var $div = $('<div class="time-div-delete clearfix"><a href="#">Удалить</a></div>');
        $content.append($div);

        var w = $div.outerWidth(); // ширина time-div

        $div.css({
            left: left,
            opacity: 1
        });

        return $div;
    }

    function validateSchedule(data) {
        
        
        if (data.length) {
            data = data[0];

            if (data.hasOwnProperty('temp')) {
                $.each(data.temp, function (k, v) {
                    $('#schedule-temp .day[data-num="' + v.num + '"] .header').append('<div class="help-block-error">' + v.text + '</div>');
                });
            }

            if (data.hasOwnProperty('calend')) {
                $.each(data.calend, function (k, v) {
                    $('#schedule-calend .day[data-num="' + v.num + '"] .header').append('<div class="help-block-error">' + v.text + '</div>');
                });
            }
        }
    }

    $(document).ready(function () {

        //createGrid();
        scheduleSync();

        $('.doctors-top-tabs a').on('shown.bs.tab', function (e) {
            if ($(this).attr('href') == '#schedule') {
                createGrid();
            }
        });

        $('.selectpicker').selectpicker({
            style: 'btn-select'
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

        //перерисовать ренжи при ресайзах
        $(window).off('resize.range').on('resize.range', function () {
            setTimeout(function () {
                createRanges();
                //тригер для закрытия открытых time-div*
                $('h1').trigger('click');
            }, 100);
        });

        //отрисовать ренжи после открытия дня
        $(document).on('shown.bs.collapse', '.schedule-settings .day .content', function () {
            console.log('col');
            createRanges();
        });

        $('#type-detail-switch a').on('shown.bs.tab', function (e) {
            type = $(this).attr('href').replace('#schedule-', '');
            selectData = [];
            createGrid();
        });

        //бинд периода шаблона
        $('#schedule-temp-type').on('change', function () {
            changeTemp($(this).val());
        });


        $('.datepicker-sch-calend .input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: false,
            changeYear: false,
            minDate: '<?= date('d.m.Y'); ?>',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });

        $('.schedule-calend_temp-handler').on('click', function () {
            var start = $('input[name="schedule-calend-start"]').val();
            var end = $('input[name="schedule-calend-end"]').val();

            if (!(start && end)) {
                bootbox.alert('Требуется заполнить обе даты');
            } else {
                start = moment(start, 'DD.MM.YYYY');
                end = moment(end, 'DD.MM.YYYY');

                if (start > end) {
                    bootbox.alert('Неверный период');
                } else {

                    dataSchedule.calend = {
                        type: null,
                        items: []
                    };

                    var counter;
                    if (dataSchedule.temp.type == 'week') {
                        counter = start.isoWeekday() - 1;
                    } else {
                        counter = 1 * start.format('D') - 1;
                    }

                    while (start.unix() <= end.unix()) {

                        var item = {
                            num: counter,
                            enabled: true,
                            date: start.format('YYYY-MM-DD'),
                            times: []
                        };

                        //поиск порядкового дня в шаблоне
                        var founded = false;
                        $.each(dataSchedule.temp.items, function (k, v) {
                            if (v.num == item.num) {
                                founded = JSON.parse(JSON.stringify(v));
                                return;
                            }
                        });

                        if (founded) {
                            item.enabled = founded.enabled;
                            item.times = founded.times;
                        }
                        
                        dataSchedule.calend.items.push(item);
                        counter++;

                        //для недели обнулить
                        if (dataSchedule.temp.type == 'week' && counter == 7) {
                            counter = 0;
                        }

                        //для месяца проверить не достигли ли его конца
                        if (dataSchedule.temp.type == 'month') {
                            var mEnd = start.clone().endOf('month');
                            if ((mEnd.format('D')) == counter) {
                                counter = 0;
                            }
                        }
                        start.add(1, 'days');
                    }

                    scheduleSync();
                    createGrid();
                }
            }
        });

        $('.schedule-calend_view-handler').on('click', function () {
            var start = $('input[name="schedule-calend-start"]').val();
            var end = $('input[name="schedule-calend-end"]').val();

            if (!(start && end)) {
                bootbox.alert('Требуется заполнить обе даты');
            } else {
                start = moment(start, 'DD.MM.YYYY');
                end = moment(end, 'DD.MM.YYYY');

                if (start > end) {
                    bootbox.alert('Неверный период');
                } else {
                    $.ajax({
                        url: '/doctor/period-calend-schedule',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            id: doctorId,
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD')
                        },
                        success: function (resp) {
                            dataSchedule.calend.items = resp;
                            scheduleSync();
                            createGrid();
                        }
                    });
                }
            }
        });

        $('.schedule-calend_clear-handler').on('click', function () {
            var start = $('input[name="schedule-calend-start"]').val();
            var end = $('input[name="schedule-calend-end"]').val();

            if (!(start && end)) {
                bootbox.alert('Требуется заполнить обе даты');
            } else {
                start = moment(start, 'DD.MM.YYYY');
                end = moment(end, 'DD.MM.YYYY');

                if (start > end) {
                    bootbox.alert('Неверный период');
                } else {
                    $.ajax({
                        url: '/doctor/clear-calend-schedule',
                        type: 'post',
                        data: {
                            id: doctorId,
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD')
                        },
                        success: function () {
                            dataSchedule.calend.items = [];
                            scheduleSync();
                            createGrid();
                        }
                    });
                }
            }
        });
    });
</script>