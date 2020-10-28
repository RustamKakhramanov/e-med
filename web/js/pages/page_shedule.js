var resizeTimer;

function renderTimeWeek() {
    var minTime = null;
    var maxTime = null;
    var result = {};
    $.each(data.week.days, function (k, item) {

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
    if (minTime && maxTime) {
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

    return false;
}

//расписание на неделю
function renderWeek() {

    var commonTime = renderTimeWeek();

    //заполнение ячейками
    $.each(data.week.days, function (k, item) {
        var $td = $('#week .schedule-table tr td:eq(' + k + ')');
        var mom = moment(item.date);
        $('.day-head', $td).html(mom.format('dddd') + '<div class="date">' + mom.format('D MMMM') + '</div>');
        $('.day-content', $td).html('');

        var canMove = true;
        if (commonTime){
            var gridStart = moment(item.date + ' ' + commonTime.start);
            var gridEnd = moment(item.date + ' ' + commonTime.end);
        }else{
            var gridStart = moment(item.date);
            var gridEnd = moment(item.date);
        }

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
                $entry.append('<div class="action-group action-group-arrow clearfix">' +
                    '<a href="' + entryData.url_edit + '" class="action"><span class="action-icon-edit"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-cancel"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-cut"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-copy"></span></a>' +
                    '</div>');
            } else {
                if (gridStart >= currentDate) { //доступно для записи

                    var available = false;

                    //поиск в периодах работы
                    $.each(item.times, function (kTime, rowTime) {
                        var dayStart = moment(item.date + ' ' + rowTime.start); //!
                        var dayEnd = moment(item.date + ' ' + rowTime.end); //!

                        if (gridStart >= dayStart && gridStart < dayEnd) {
                            available = true;
                            return;
                        }
                    });

                    if (available) {
                        $entry = $('<div class="entry entry-available"><a href="' + '/handling/create?user_specialist_id=' + data.week.user_specialist_id + '&specialty_id=' + data.week.specialty_id + '&date_record=' + (item.date + ' ' + gridStart.format('HH:mm')) + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></a></div>');
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

function renderTimeDay() {
    var minTime = null;
    var maxTime = null; 
    var result = {};               
    
    $.each(data.day.days, function (k, item) {
        if (item.times) {
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
        }
    });
    
    if (minTime && maxTime){
	    result = {
	        start: minTime.format('HH:mm'),
	        end: maxTime.format('HH:mm')
	    };
	    
	    $('#day .schedule-ctr .time-col .time-item').remove();
	    var canMove = true;
	    while (canMove) {
	        $('#day .schedule-ctr .time-col').append('<div class="time-item">' + minTime.format('HH:mm') + '</div>');
	        minTime.add(viewInterval, 'minutes');
	        if (minTime >= maxTime) {
	            canMove = false;
	        }
	    }

        return result;
	}

    return false;
}

//расписание на день
function renderDay() {

    var commonTime = renderTimeDay();

    $('#day .schedule-table').html('');

    //заполнение ячейками
    $.each(data.day.days, function (k, item) {
        var $day = $('<div class="day-col"></div>');
        $day.width(($('.tab-content').width() - 54) / 7);
        $day.append('\
                        <div class="day-head">\n\
                            <div class="last_name">' + item.doctor.last_name + '</div>\n\
                            <div class="name">' + item.doctor.name + '</div>\n\
                            <div class="spec">' + item.doctor.spec + '</div>\n\
                        </div>\n\
                    ');

        var canMove = true;
        if (commonTime){
            var gridStart = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.start);
            var gridEnd = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.end);
        }else{
            var gridStart = moment(currentDate.format('YYYY-MM-DD'));
            var gridEnd = moment(currentDate.format('YYYY-MM-DD'));
        }

        while (canMove) {
            //поиск записей
            var entryData = false;
            $.each(item.entries, function (eKey, entry) {
                if (entry.start == gridStart.format('HH:mm')) {
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
                $entry.append('<div class="action-group action-group-arrow clearfix">' +
                    '<a href="' + entryData.url_edit + '" class="action"><span class="action-icon-edit"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-cancel"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-cut"></span></a>' +
                    //'<a href="#" class="action"><span class="action-icon-copy"></span></a>' +
                    '</div>');
            } else {
                if (gridStart >= currentDate) { //доступно для записи
                    
                    var available = false;

                    //поиск в периодах работы
                    $.each(item.times, function (kTime, rowTime) {
                        var dStart = moment(currentDate.format('YYYY-MM-DD') + ' ' + rowTime.start); //!
                        var dEnd = moment(currentDate.format('YYYY-MM-DD') + ' ' + rowTime.end); //!

                        if (gridStart >= dStart && gridStart < dEnd) {
                            available = true;
                            return;
                        }
                    });
                    
                    //console.log(item.doctor.name + ' ' + gridStart.format('HH:mm') + ' = ' + available);
                    
                    if (available) {
                        $entry = $('<div class="entry entry-available"><a href="' + '/handling/create?user_specialist_id=' + item.doctor.user_specialist_id + '&specialty_id=' + item.doctor.specialty_id + '&date_record=' + (data.day.date + ' ' + gridStart.format('HH:mm')) + '"><div class="inner">+ ' + gridStart.format('HH:mm') + '</div></a></div>');
                    } else {
                        $entry = $('<div class="entry entry-empty"></div>');
                    }
                } else { //недоступно для записи (прошло время)
                    $entry = $('<div class="entry entry-empty"></div>');
                }
            }

            $entry.height(item.interval / viewInterval * 60);
            $day.append($entry);
            $('#day .schedule-table').append($day);

            gridStart.add(item.interval, 'minutes');
            if (gridStart >= gridEnd) {
                canMove = false;
            }

        }
    });

    var w = $('#day .schedule-table .day-col').width() * $('#day .schedule-table .day-col').size() +4;
    $('#day .schedule-table').width(w);

    $(window).resize(function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(renderDay, 100);
    });

    //тайм лайн
    var $line = $('#day .schedule-ctr .time-line');
    var gridStart = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.start);
    var gridEnd = moment(currentDate.format('YYYY-MM-DD') + ' ' + commonTime.end);
    if (currentDate >= gridStart && currentDate <= gridEnd) {
        $line.show();
        var top = (currentDate.unix() - gridStart.unix()) / (gridEnd.unix() - gridStart.unix()) * 100;
        top = ($('#day .schedule-ctr').height() - 61) / 100 * top;
        $line.css('top', top + 'px');
    } else {
        $line.hide();
    }

    if ($('#day .schedule-ctr .day-col').size() > 7) {
        $('#day .schedule-scroll').show().disableSelection();

        $('.scroll-pane').jScrollPane({
            enableKeyboardNavigation: false
        });

        var jsp = $('.scroll-pane').data('jsp');
        var w = $('#day .schedule-content').width() / $('#day .schedule-table').width() * 100;

        function jScrollTrigger(left) {
            var maxW = $('#day .ui-slider').width() - $('#day .ui-slider').width() / 100 * w;
            var percent = Math.ceil(left / maxW * 100);
            jsp.scrollToPercentX(percent / 100);

            //цифры на стрелка, сколько скрыто
            setTimeout(function () {
                var l = parseFloat($('#day .jspPane').css('left'));
                var x = ($('#day').width() - 54) / 7;

                var hiddenLeft = Math.floor(-1 * l / x);
                var $leftNum = $('#day .schedule-scroll .control.control-left .num-hidden-cols');
                if (hiddenLeft > 0) {
                    $leftNum.text(hiddenLeft).addClass('active');
                } else {
                    $leftNum.html('&ndash;').removeClass('active');
                }

                var w = $('#day').width() - 54;
                var m = $('#day .schedule-table').width();
                var hiddenRight = Math.floor(Math.abs(m - w + l) / x);

                var $rightNum = $('#day .schedule-scroll .control.control-right .num-hidden-cols');
                if (hiddenRight > 0) {
                    $rightNum.text(hiddenRight).addClass('active');
                } else {
                    $rightNum.html('&ndash;').removeClass('active');
                }
            }, 100);

        }

        $('#day .schedule-scroll .ui-slider-handle').width(w + '%');

        $('#day .schedule-scroll .ui-slider-handle').draggable({
            axis: 'x',
            containment: 'parent',
            drag: function (event, ui) {
                jScrollTrigger(ui.position.left);
            }
        });

        $('.schedule-scroll .control').on('click', function (e) {
            var w = $('#day .schedule-scroll .ui-slider').width() - $('#day .ui-slider-handle').width();
            var l;
            if ($(this).hasClass('control-right')) {
                l = parseFloat($('#day .ui-slider-handle').css('left')) + w / 100 * 20;
                if (l > w) {
                    l = w;
                }
            } else {
                l = parseFloat($('#day .ui-slider-handle').css('left')) - w / 100 * 20;
                if (l < 0) {
                    l = 0;
                }
            }
            $('#day .ui-slider-handle').css('left', l);
            jScrollTrigger(l);
        });
    }
}

$(document).ready(function ()
{
    //renderWeek();
    renderDay();

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

            if ($('#view-switch li.active a').attr('data-view') == 'week') {
                renderWeek();
            } else {
                renderDay();
            }
        }

        return;
    });

    $('#view-switch a').on('shown.bs.tab', function (e) {
        if (e.target.hash == '#week') {
            renderWeek();
        } else {
            renderDay();
        }
    });
});