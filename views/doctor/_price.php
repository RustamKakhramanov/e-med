<?php
$usedIds = [];
foreach ($model->doctorPrices as $doctorPrice) {
    $usedIds[] = $doctorPrice->price_id;
}
?>


<div class="doctor-price mt10">
    <div class="doctor-price__left">
        <div class="doctor-price__left-inner">
            <h2>Все услуги</h2>
            <div class="doctor-price__left-content">
                <?php foreach ($model->priceList as $price) {
                    if (in_array($price->id, $usedIds)) {
                        continue;
                    }
                    $uid = uniqid(); ?>
                    <div class="item" data-id="<?= $price->id; ?>">
                        <div class="checkbox">
                            <input type="checkbox" name="" value="<?= $price->id; ?>" id="<?= $uid; ?>"/>
                            <label for="<?= $uid; ?>"><?= $price->title; ?></label>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="doctor-price__right">
        <div class="doctor-price__right-inner">
            <h2>Выбранные услуги</h2>
            <div class="doctor-price__right-content">
                <?php foreach ($model->doctorPrices as $row) {
                    $uid = uniqid();
                    ?>
                    <div class="item" data-id="<?= $row->price_id; ?>">
                        <div class="checkbox">
                            <input type="checkbox" name="" value="<?= $row->price_id; ?>" id="<?= $uid; ?>"/>
                            <label for="<?= $uid; ?>"><?= $row->price->title; ?></label>
                        </div>
                        <input type="hidden" name="price[]" value="<?= $row->price_id; ?>"/>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="doctor-price__arrow-right"><i class="fa fa-arrow-right"></i></div>
    <div class="doctor-price__arrow-left"><i class="fa fa-arrow-left"></i></div>
</div>

<style>
    .doctor-price {
        position: relative;
        margin-bottom: -100px;
    }

    .doctor-price h2 {
        margin-top: 0;
    }

    .doctor-price__left {
        position: absolute;
        left: 0;
        width: 50%;
        bottom: 70px;
        top: 0px;
    }

    .doctor-price__left-inner {
        height: 100%;
        position: relative;
        margin-right: 30px;
        border: 1px #ECEFF3 solid;
        padding: 10px;
    }

    .doctor-price__right {
        position: absolute;
        right: 0;
        width: 50%;
        bottom: 70px;
        top: 0px;
    }

    .doctor-price__right-inner {
        position: relative;
        height: 100%;
        margin-left: 30px;
        border: 1px #ECEFF3 solid;
        padding: 10px;
    }

    .doctor-price__left-content {
        position: absolute;
        left: 10px;
        top: 40px;
        right: 10px;
        bottom: 10px;
        overflow: auto;
    }

    .doctor-price__arrow-right {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        border: 1px #000 solid;
        top: 50%;
        margin-top: -32px;
        left: 50%;
        margin-left: -16px;
        text-align: center;
        line-height: 30px;
        cursor: pointer;
    }

    .doctor-price__arrow-left {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        border: 1px #000 solid;
        top: 50%;
        margin-top: 10px;
        left: 50%;
        margin-left: -16px;
        text-align: center;
        line-height: 30px;
        cursor: pointer;
    }

    .doctor-price__arrow-right:hover,
    .doctor-price__arrow-left:hover {
        background: #446584;
        border-color: #446584;
        color: #fff;
    }
</style>

<script>
    function priceHeight() {
        $('.doctor-price').height($(window).height() - 140);
    }

    $(document).ready(function () {
        $('.doctors-top-tabs a').on('shown.bs.tab', function (e) {
            if ($(this).attr('href') == '#price') {
                priceHeight();
            }
        });

        $('.doctor-price__arrow-right').on('click', function () {
            $('.doctor-price__left-content .item').each(function () {
                if ($('input:checked', $(this)).length) {
                    $(this).append('<input type="hidden" name="price[]" value="' + $(this).attr('data-id') + '">');
                    $('input:checked', $(this)).prop('checked', false);
                    $(this).appendTo('.doctor-price__right-content');
                }
            });
        });

        $('.doctor-price__arrow-left').on('click', function () {
            $('.doctor-price__right-content .item').each(function () {
                if ($('input:checked', $(this)).length) {
                    $('input[type="hidden"]', $(this)).remove();
                    $('input:checked', $(this)).prop('checked', false);
                    $(this).appendTo('.doctor-price__left-content');
                }
            });
        });
    });
</script>