<?php
$uid = uniqid();

$datetime = new \DateTime();
$datetime->modify('last Monday');
$date = $datetime->format('Y-m-d');
?>
<div class="tv-template-schedule pr20">
    <div class="item item-header clearfix">
        <table>
            <tr>
                <td class="photo"></td>
                <td class="name">
                    <span class="lpager">
                        страница <i class="current"></i> из <i class="total"></i>
                        <strong></strong>
                    </span>
                </td>

                <?php
                for ($i = 0; $i < $widget->days; $i++) {
                    $d = date('N', strtotime('+' . $i . ' day', strtotime($date)));
                    ?>
                    <td class="day">
                        <?= $widget::dayName($d); ?>
                    </td>
                <?php } ?>
            </tr>
        </table>
    </div>
    <div class="slides-ctr">
        <?php
        foreach ($widget->doctors as $index => $d) {
            $s = $d->getScheduleGrid($date, date('Y-m-d', strtotime('+' . ($widget->days - 1) . ' day', strtotime($date))));
            ?>

            <?php
            if ($index == 0 || ($index % $widget->rows == 0)) {
                ?>
                <div class="slide">
                    <?php
                }
                ?>

                <div class="item clearfix">
                    <table>
                        <tr>
                            <td class="photo">
                                <div class="photo-ctr">
                                    <img src="<?= $d->photoUrl; ?>"/>
                                </div>
                            </td>
                            <td class="name">
                                <div class="doctor-name" data-short="<?=$d->initials;?>">
                                    <?= $s[0]['doctor']['name']; ?><br/><?= $s[0]['doctor']['last_name']; ?>
                                </div>
                                <div class="main-spec">
                                    <?= $s[0]['doctor']['spec']; ?>
                                </div>
                            </td>
                            <?php foreach ($s as $day) { ?>
                                <td class="day time">
                                    <?php if ($day['periods']) { ?>
                                        <div class="time-on">
                                            <?= $widget::prettyPeriod($day['periods']); ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="time-off">Выходной</div>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>
                </div>

                <?php if ($index && ( ($index + 1) % $widget->rows == 0 || $index == (count($widget->doctors) - 1))) { ?>

                </div>

                <?php
            }
            ?>
        <?php } ?>
    </div>
</div>

<script>
    var active = -1;
    var time = <?= $widget->timer * 1000; ?>;
    var slideRows = <?= $widget->rows; ?>;

    function changeSlider() {
        var $ctr = $('.tv-template-schedule .slides-ctr');

        if (active < 0) {
            $('.slide:eq(0)', $ctr).fadeIn(500).addClass('active');
            active = 0;
        } else {
            active++;
            if (active >= $('.slide', $ctr).length) {
                active = 0;
            }

            $('.slide.active', $ctr).fadeOut(500, function () {
                $(this).removeClass('active');
                $('.slide:eq(' + active + ')', $ctr).fadeIn(500).addClass('active');
            });
        }

        $('.tv-template-schedule .lpager .current').text(active + 1);
        $('.tv-template-schedule .lpager strong').stop().css('width', '0px').animate({width: '100%'}, time);
    }

    $(document).ready(function () {
        $('.tv-template-schedule .lpager .total').text($('.tv-template-schedule .slides-ctr .slide').length);

        //добить последний слайд до конца
        var $last = $('.tv-template-schedule .slides-ctr .slide:last');
        var $first = $('.tv-template-schedule .slides-ctr .slide:first');
        if ($('.item', $last).length < slideRows) {
            var k = 0;
            while ($('.item', $last).length < slideRows) {
                var d = $('.item:eq(' + k + ')', $first);
                if (!d.length) {
                    k = 0;
                    d = $('.item:eq(0)', $first);
                }
                d.clone().appendTo($last);
                k++;
            }
        }

        changeSlider();
        var scrollOT = setInterval(changeSlider, time);
    });
</script>