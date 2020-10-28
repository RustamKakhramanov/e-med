<?php

function dd($var, $opt = 0) {
    $b = debug_backtrace();
    echo '<div style="font-size:10px;font-family: Courier New;color:#000;max-height:500px;width:100%;overflow-y:scroll;background:#fff;z-index:10000;text-align:left;"><hr/>';
    echo '<div style="font-size:14px;background:#ccc;padding: 5px;"><b>FILE:</b>' . $b[0]['file'] . '<br/>';

    if ($opt == 0) {
        echo '<b>LINE:</b>' . $b[0]['line'] . '<br/></div><pre>';
        print_r($var);
        echo '</pre>';
    };

    if ($opt == 1) {
        echo '<b>LINE:</b>' . $b[0]['line'] . '<br/></div><textarea style="width:100%;heigth:100%;min-height: 500px;">';
        print_r($var);
        echo '</textarea>';
    };

    echo '<hr/></div>';
}


/**
 * склонения количества
 * 
 * @param int $number
 * @param array $titles
 * @return text
 */
function human_plural_form($number, $titles = array('комментарий', 'комментария', 'комментариев'), $needNumber = true) {
    $cases = array(2, 0, 1, 1, 1, 2);
    if ($needNumber) {
        return $number . " " . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    } else {
        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}

/**
 * склонения месяца (12 июля 2015)
 * 
 * @param type $in
 * @return string
 */
function ruDateCase($in, $withYear = true) {
    $time = strtotime($in);
    $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $result = date('d', $time) . ' ' . $months[date('n', $time) - 1];
    if ($withYear) {
        $result .= ' ' . date('Y', $time);
    }
    
    return $result;
}

function getPhrase($number, $titles) {
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

/**
 * сколько минут/часов/дней назад
 * @param datetime $in
 * @param bool $old - применять ли для старых дат
 * @return text
 */
function niceDate($in, $old = true) {

    $date = strtotime($in);
    $stf = 0;
    $cur_time = time();
    $diff = $cur_time - $date;

    if ($old == false && $diff / 60 > 86400) {
        return date('d.m.Y', $date);
    }

    if ($diff / 60 > 20736000) {
        return '';
    }

    $seconds = array('секунда', 'секунды', 'секунд');
    $minutes = array('минута', 'минуты', 'минут');
    $hours = array('час', 'часа', 'часов');
    $days = array('день', 'дня', 'дней');
    $weeks = array('неделя', 'недели', 'недель');
    $months = array('месяц', 'месяца', 'месяцев');
    $years = array('год', 'года', 'лет');
    $decades = array('десятилетие', 'десятилетия', 'десятилетий');

    $phrase = array($seconds, $minutes, $hours, $days, $weeks, $months, $years, $decades);
    $length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

    for ($i = sizeof($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--)
        ;
    if ($i < 0)
        $i = 0;
    $_time = $cur_time - ($diff % $length[$i]);
    $no = floor($no);
    $value = sprintf("%d %s ", $no, getPhrase($no, $phrase[$i]));

    if (($stf == 1) && ($i >= 1) && (($cur_time - $_time) > 0))
        $value .= time_ago($_time);

    return $value . ' назад';
}
