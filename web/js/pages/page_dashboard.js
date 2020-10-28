//серверное время
var currentDate = moment('2015-07-28 10:00:00');
//интервал сетки
var viewInterval = 30;
//флаг уменьшеного расписания
var minified = true;
//данные для таблицы расписания
var data = {
    //для недели
    week: {
        interval: 30, //интервал приема врача в минутах
        days: [{
            day: '2015-07-27', //понедельник
            start: '09:00',
            end: '14:00',
            entries: [
                {
                    date: '2015-07-27 12:30',
                    name: 'Григорьев О.Д. 30',
                    cost: 5600,
                    phone: '+7 923 667 1382'
                }
            ]
        },
            {
                day: '2015-07-28',
                start: '09:00',
                end: '14:00',
                entries: [{
                    date: '2015-07-28 09:00',
                    name: 'Жёлудь О.Д. 48',
                    cost: 3100,
                    phone: '+7 923 533 3845'
                },
                    {
                        date: '2015-07-28 11:30',
                        name: 'Фёдоров А.Б. 17',
                        cost: 3100,
                        phone: '+7 923 533 3845'
                    },
                ]
            },
            {
                day: '2015-07-29',
                start: '09:00',
                end: '14:00',
                entries: []
            },
            {
                day: '2015-07-30',
                start: '10:00',
                end: '12:00',
                entries: [{
                    date: '2015-07-30 11:00',
                    name: 'Пецкалев А.Б. 17',
                    cost: 3100,
                    phone: '+7 923 533 3845'
                }
                ]
            },
            {
                day: '2015-07-31',
                start: '09:00',
                end: '16:00',
                entries: []
            },
            {
                day: '2015-08-01',
                start: '12:00',
                end: '16:00',
                entries: []
            },
            {
                day: '2015-08-02',
                start: '09:00',
                end: '14:00',
                entries: []
            }
        ]
    }
};

function renderTimeWeek() {
    var minTime = null;
    var maxTime = null;
    var result = {};
    $.each(data.week.days, function (k, item) {
        var start = moment(item.start, 'HH:mm');
        var end = moment(item.end, 'HH:mm');
        if (minTime == null || start.unix() < minTime.unix()) {
            minTime = start;
        }
        if (maxTime == null || end.unix() > maxTime.unix()) {
            maxTime = end;
        }
    });
    result = {
        start: minTime.format('HH:mm'),
        end: maxTime.format('HH:mm')
    };
    $('#week .schedule-ctr .time-col .time-item').remove();
    var canMove = true;
    while (canMove) {
        $('#week .schedule-ctr .time-col').append('<div class="time-item">' + minTime.format('HH:mm') + '</div>');
        minTime.add(viewInterval, 'minutes');
        if (minTime >= maxTime) {
            canMove = false;
        }
    }

    return result;
}

//расписание на неделю
function renderWeek() {

    var commonTime = renderTimeWeek();
    $('#week .schedule-table tbody').html('');
    $('#week .schedule-table tbody').append('<tr></tr>');
    //заполнение ячейками
    $.each(data.week.days, function (k, item) {

        $('#week .schedule-table tr').append('<td><div class="day-head"></div><div class="day-content"></div></td>');
        var $td = $('#week .schedule-table tr td:last');

        var mom = moment(item.day);
        $('.day-head', $td).html(mom.format('dddd') + '<div class="date">' + mom.format('D MMMM') + '</div>');
        $('.day-content', $td).html('');
        console.log($td);

        var dayStart = moment(item.day + ' ' + item.start);
        var dayEnd = moment(item.day + ' ' + item.end);
        var gridStart = moment(item.day + ' ' + commonTime.start);
        var gridEnd = moment(item.day + ' ' + commonTime.end);
        var canMove = true;

        while (canMove) {
            //поиск записей
            var entryData = false;
            $.each(item.entries, function (eKey, entry) {
                if (moment(entry.date).unix() == gridStart.unix()) {
                    entryData = entry;
                }
            });

            var $entry;

            if (entryData) { //если есть запись
                $entry = $('\
                                <div class="entry entry-exist ' + ((gridStart > currentDate) ? 'entry-actual' : 'entry-past') + '">\n\
                                    <div class="inner">\n\
                                        <div class="name">' + entryData.name + '</div>\n\
                                        <div class="cost">' + entryData.cost + '</div>\n\
                                    </div>\n\
                                </div>\n\
                            ');
                //добавляем контролы
                $entry.append('<div class="action-group action-group-arrow clearfix"><a href="#" class="action"><span class="action-icon-edit"></span></a><a href="#" class="action"><span class="action-icon-cancel"></span></a><a href="#" class="action"><span class="action-icon-cut"></span></a><a href="#" class="action"><span class="action-icon-copy"></span></a></div>');
            } else {
                if (gridStart >= currentDate) { //доступно для записи
                    if (gridStart >= dayStart && gridStart < dayEnd) {
                        $entry = $('<div class="entry entry-available"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></div>');
                    } else {
                        $entry = $('<div class="entry entry-empty"></div>');
                    }
                } else { //недоступно для записи (прошло время)
                    $entry = $('<div class="entry entry-empty"></div>');
                }
            }
            $entry.height(data.week.interval / viewInterval * 60);
            $('.day-content', $td).append($entry);

            gridStart.add(data.week.interval, 'minutes');
            if (gridStart >= gridEnd) {

                canMove = false;
            }
        }

        if (minified) {
            return false;
        }
    });

    //тайм лайн
    var $line = $('#week .schedule-ctr .time-line');
    var gridStart = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.start);
    var gridEnd = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.end);
    if (currentDate >= gridStart && currentDate <= gridEnd) {
        $line.show();
        var top = (currentDate.unix() - gridStart.unix()) / (gridEnd.unix() - gridStart.unix()) * 100;
        top = ($('#week .schedule-ctr').height() - 46) / 100 * top;
        $line.css('top', top + 'px');
    } else {
        $line.hide();
    }
}

$(document).ready(function () {

    renderWeek();

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
        style: 'btn-select btn-sm'
    });


    $('.select-period .dropdown-menu li a').on('click', function () {
        var $li = $(this).closest('li');
        if (!$li.hasClass('active')) {
            $('.select-period .dropdown-menu li').removeClass('active');
            $li.addClass('active');
            viewInterval = $(this).attr('data-interval');
            renderWeek();
        }

        return;
    });

    $('.open-week-handler, .close-week-handler').on('click', function () {
        minified = !minified;
        $('.dashboard-ctr').toggleClass('not-minified');
        renderWeek();

        return false;
    });
});
