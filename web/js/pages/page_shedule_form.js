//интервал ячеек в минутах
var interval = 60;

//тип отображаемого графика (по умолчанию фиксированный)
var type = 'fix';

//данные для обработки кликов выбора
var selectData = [];

//создать дни и сетки выбора времени
function createGrid() {

    $('#' + type + ' .schedule-settings').html('');

    $.each(data[type], function (k, day) {
        day.date = '2015-08-07';
        var mom = moment(day.date + ' 00:00');
        var end = moment(day.date + ' 24:00');

        var $day = $('<div class="day"></div>');

        var dUniq = uniqid();
        var colapseUniq = uniqid();
        $day.append('\n\
                        <div class="header">\n\
                            <div class="info clearfix">\n\
                                <div class="checkbox">\n\
                                    <input id="' + dUniq + '" type="checkbox"/>\n\
                                    <label for="' + dUniq + '" data-name="' + capitalize(mom.format('dddd')) + '">' + (k+1) + ' день <!-- (' + capitalize(mom.format('dddd')) + ') --></label>\n\
                                </div>\n\
                                <!-- <span class="mr30">Офтальмология</span>\n\
                                <span class="mr30">4 кабинет</span> -->\n\
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
            $('.toggler', $day).addClass('collapsed');
        }

        $('#' + type + ' .schedule-settings').append($day);

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
                data[type][k].enabled = false;
            } else {
                $label.text($label.attr('data-name'));
                data[type][k].enabled = true;
            }

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

        $('.single', $day).on('click.init', function (e_top) {

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
                $(document).off('click.timediv').on('click.timediv', function (e) {

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
    $.each(data[type][ind].times, function (kTime, time) {
        if (s < timeToInt(time.start) && timeToInt(time.end) < e) {
            var $day = $('#' + type + ' .schedule-settings .day:eq(' + ind + ')');
            $('#' + type + ' .selected-range[data-range="' + time.start + '-' + time.end + '"]').remove();
            deletedKeys.push(kTime);
        }
    });

    //вынес удаление потому что в первом $.each ебланит
    $.each(deletedKeys, function (k, kTime) {
        data[type][ind].times.splice(kTime - k, 1);
    });

    data[type][ind].times.push({
        start: time.first,
        end: time.second
    });

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
    while (s.length < size)
        s = "0" + s;
    return s;
}

//отрисовать ренжи
function createRanges() {
    $('#' + type + ' .schedule-settings .day .selected-range').remove();
    $('#' + type + ' .schedule-settings .day .selected-times').html('');

    $.each(data[type], function (k, day) {
        var $dayContent = $('#' + type + ' .schedule-settings .day:eq(' + k + ') .content');
        var totalDayLong = 0; //минуты

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

        $('.selected-range', $dayContent).on('click', function (e) {
            //e.stopPropagation();

            var $range = $(this);

            if ($range.hasClass('selected-range-clicked')) {
                $range.removeClass('selected-range-clicked');
                $('.time-div-delete', $dayContent).remove();
            } else {
                $('.selected-range', $dayContent).removeClass('selected-range-clicked');
                $range.addClass('selected-range-clicked')
                var $div = createTimeDivDelete($range);

                //если клик не по таймдиву - то удаляем все открытые
                $(document).off('click.timediv-selected').on('click.timediv-selected', function (e) {

                    //хук для первого клика
                    if (e.target == $range[0]) {
                        return;
                    }

                    //если кликнули не по кнопке удалить
                    if ($(e.target).closest('.time-div-delete')[0] != $div[0]) {
                        $range.removeClass('selected-range-clicked');
                        $('.time-div-delete', $dayContent).remove();
                    } else {
                        //экшн удаления ренжа
                        $('.time-div-delete', $dayContent).remove();
                        var rangeVal = $range.attr('data-range').split('-');
                        deleteTime(rangeVal[0], rangeVal[1], k);
                    }

                    $(document).off('click.timediv-selected');
                    return false;
                });
            }
        });
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
    $.each(data[type][k].times, function (kTime, time) {
        console.log(time);
        if (time.start == s && time.end == e) {
            key = kTime;
            return;
        }
    });
    data[type][k].times.splice(key, 1);
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

$(document).ready(function () {

    //createGrid('#fix-schedule');
    createGrid();

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

    //отрисовать ренжи после открытия дня
    $('#type-switch a').on('shown.bs.tab', function (e) {
        type = $(this).attr('href').replace('#', '');

//                    if (type == 'detail') {
//
//                    }
        selectData = [];
        createGrid();
    });

});