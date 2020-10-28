<?php

namespace app\helpers;

use Yii;

class Utils {

    /**
     * склонения количества
     *
     * @param int $number
     * @param array $titles
     * @return text
     */
    public static function human_plural_form($number, $titles = array('комментарий', 'комментария', 'комментариев'), $needNumber = true) {
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
    public static function rudate($in, $withYear = true, $shortMonth = false) {
        $time = strtotime($in);
        if ($shortMonth) {
            $months = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
        } else {
            $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        }
        $result = date('d', $time) . ' ' . $months[date('n', $time) - 1];
        if ($withYear) {
            $result .= ' ' . date('Y', $time);
        }

        return $result;
    }

    /**
     * день недели
     * @param type $in
     * @return string
     */
    public static function dayname($in) {
        $labels = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $num = date('w', strtotime($in));

        return isset($labels[$num]) ? $labels[$num] : false;
    }

    public static function getPhrase($number, $titles) {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * сколько минут/часов/дней назад
     * @param datetime $in
     * @param bool $old - применять ли для старых дат
     * @return text
     */
    public static function niceDate($in, $old = true) {

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

    /**
     * формат числа
     * @param type $num
     * @return type
     */
    public static function nformat($num) {
        return number_format($num, 0, ', ', ' ');
    }

    /**
     * форматирование цены
     * @param type $num
     */
    public static function ncost($num) {
        return rtrim(rtrim(number_format($num, 2, '.', '&thinsp;'), '0'), '.');
    }

    public static function translit($str) {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
        );

        return strtr($str, $tr);
    }

    public static function chpu($name) {
        $url = trim(translit($name));
        $url = strtolower($url);
        $url = preg_replace('/[^a-z0-9-]/ui', '-', $url);
        $url = preg_replace('/\-+/ui', '-', $url);

        return substr($url, 0, 100);
    }

    public static function mb_ucfirst($text) {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }

    public static function mb_lcfirst($text) {
        return mb_strtolower(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
    }

    public static function mb_lower($text) {
        return mb_strtolower($text, 'UTF-8');
    }

    public static function mb_upper($text) {
        return mb_strtoupper($text, 'UTF-8');
    }

    public static function number_pad($number, $n) {
        return str_pad((int)$number, $n, "0", STR_PAD_LEFT);
    }

    /**
     * Возвращает сумму прописью
     * @uses morph(...)
     */
    public static function num2str($num) {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array(// Units
            array('тиын', 'тиын', 'тиын', 1), //копейки
            array('тенге', 'тенге', 'тенге', 0), //рубли
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v))
                    continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1)
                    $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];# 20-99
                else
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];# 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1)
                    $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else
            $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    /**
     * Склоняем словоформу
     */
    public static function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20)
            return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5)
            return $f2;
        if ($n == 1)
            return $f1;
        return $f5;
    }

    public static function camel2dashed($className) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
    }
}
