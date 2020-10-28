<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\Utils;

$priceIds = [];
foreach ($model->prices as $price) {
    $priceIds[] = $price->id;
}
?>

<div class="spec-price mt10">
    <div class="spec-price__left">
        <div class="spec-price__left-inner">
            <h2>Все услуги</h2>
            <div class="spec-price__left-content">
                <div class="rl-table">
                    <div class="rl-table-header mt0">
                        <table>
                            <tr>
                                <td class="rl_col_check">
                                    <div class="checkbox">
                                        <?php
                                        $uid = uniqid();
                                        echo Html::checkbox('', false, [
                                            'label' => null,
                                            'id' => $uid
                                        ])
                                        ?>
                                        <?= Html::label('&nbsp;', $uid); ?>
                                    </div>
                                </td>
                                <td class="rl_col_service">Группа / Услуга</td>
                                <td class="rl_col_cost">Стоимость</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="spec-price__left-items native-scroll">
                    <div class="rl-table">
                        <div class="rl-table-rows">
                            <?php foreach ($priceGroups as $group) { ?>
                                <div class="item item-type-group" data-group="<?= $group->id; ?>">
                                    <table>
                                        <tr>
                                            <td class="rl_col_tree">
                                                <div class="ctrl"></div>
                                            </td>
                                            <td class="rl_col_check">
                                                <div class="checkbox">
                                                    <?php
                                                    $checkboxUid = uniqid();
                                                    echo Html::checkbox('', false, [
                                                        'label' => null,
                                                        'id' => $checkboxUid
                                                    ])
                                                    ?>
                                                    <?= Html::label('&nbsp;', $checkboxUid); ?>
                                                </div>
                                            </td>
                                            <td class="rl_col_service"><strong><?= $group->name ?></strong> <?= count($group->activeItems); ?></td>
                                            <td class="rl_col_cost"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="items-in-group hidden" data-group="<?= $group->id; ?>">
                                    <?php foreach ($group->activeItems as $item) { ?>
                                        <div class="item item-type-price <?php if (in_array($item->id, $priceIds)) echo 'hidden'; ?>" data-id="<?= $item->id; ?>">
                                            <table>
                                                <tr>
                                                    <td class="rl_col_tree">
                                                        <div class="ctrl"></div>
                                                    </td>
                                                    <td class="rl_col_check">
                                                        <div class="checkbox">
                                                            <?php
                                                            $checkboxUid = uniqid();
                                                            echo Html::checkbox('', false, [
                                                                'label' => null,
                                                                'id' => $checkboxUid
                                                            ])
                                                            ?>
                                                            <?= Html::label('&nbsp;', $checkboxUid); ?>
                                                        </div>
                                                    </td>
                                                    <td class="rl_col_service">
                                                        <?= Html::label($item->title, $checkboxUid); ?>
                                                    </td>
                                                    <td class="rl_col_cost"><?= Utils::ncost($item->cost); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spec-price__right">
        <div class="spec-price__right-inner">
            <h2>Выбранные услуги</h2>
            <div class="spec-price__right-content">
                <div class="rl-table">
                    <div class="rl-table-header mt0">
                        <table>
                            <tr>
                                <td class="rl_col_check">
                                    <div class="checkbox">
                                        <?php
                                        $uid = uniqid();
                                        echo Html::checkbox('', false, [
                                            'label' => null,
                                            'id' => $uid
                                        ])
                                        ?>
                                        <?= Html::label('&nbsp;', $uid); ?>
                                    </div>
                                </td>
                                <td class="rl_col_service">Группа / Услуга</td>
                                <td class="rl_col_cost">Стоимость</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="spec-price__right-items native-scroll">
                    <div class="rl-table">
                        <div class="rl-table-rows">
                            <?php
                            foreach ($model->prices as $rel) { ?>
                                <div class="item" data-id="<?= $rel->id; ?>">
                                    <table>
                                        <tr>
                                            <td class="rl_col_check">
                                                <div class="checkbox">
                                                    <?php
                                                    $checkboxUid = uniqid();
                                                    echo Html::checkbox('', false, [
                                                        'label' => null,
                                                        'id' => $checkboxUid
                                                    ])
                                                    ?>
                                                    <?= Html::label('&nbsp;', $checkboxUid); ?>
                                                </div>
                                            </td>
                                            <td class="rl_col_service">
                                                <?= Html::label($rel->title, $checkboxUid); ?>
                                            </td>
                                            <td class="rl_col_cost"><?= Utils::ncost($rel->cost); ?></td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="price[]" value="<?= $rel->id; ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spec-price__arrow-right"><i class="fa fa-arrow-right"></i></div>
    <div class="spec-price__arrow-left"><i class="fa fa-arrow-left"></i></div>
</div>

<style>
    .spec-price {
        position: relative;
        margin-bottom: -100px;
    }

    .spec-price h2 {
        margin-top: 0;
    }

    .spec-price__left {
        position: absolute;
        left: 0;
        width: 50%;
        bottom: 70px;
        top: 0px;
    }

    .spec-price__left-inner {
        height: 100%;
        position: relative;
        margin-right: 30px;
        border: 1px #ECEFF3 solid;
        padding: 10px;
    }

    .spec-price__right {
        position: absolute;
        right: 0;
        width: 50%;
        bottom: 70px;
        top: 0px;
    }

    .spec-price__right-inner {
        position: relative;
        height: 100%;
        margin-left: 30px;
        border: 1px #ECEFF3 solid;
        padding: 10px;
    }

    .spec-price__left-content {
        position: absolute;
        left: 10px;
        top: 40px;
        right: 10px;
        bottom: 10px;
    }

    .spec-price__right-content {
        position: absolute;
        left: 10px;
        top: 40px;
        right: 10px;
        bottom: 10px;
    }

    .spec-price__arrow-right {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        border: 1px #446584 solid;
        top: 50%;
        margin-top: -32px;
        left: 50%;
        margin-left: -16px;
        text-align: center;
        line-height: 30px;
        cursor: pointer;
        color: #446584;
    }

    .spec-price__arrow-left {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 32px;
        border: 1px #446584 solid;
        top: 50%;
        margin-top: 10px;
        left: 50%;
        margin-left: -16px;
        text-align: center;
        line-height: 30px;
        cursor: pointer;
        color: #446584;
    }

    .spec-price__arrow-right:hover,
    .spec-price__arrow-left:hover {
        background: #446584;
        border-color: #446584;
        color: #fff;
    }

    .spec-price .rl_col_cost {
        text-align: right;
        width: 120px;
        padding-right: 5px !important;
    }

    .spec-price .rl_col_tree {
        width: 30px;
    }

    .spec-price__left-items {
        position: absolute;
        left: 0px;
        top: 45px;
        right: 0px;
        bottom: 0px;
        overflow: auto;
    }

    .spec-price__right-items {
        position: absolute;
        left: 0px;
        top: 45px;
        right: 0px;
        bottom: 0px;
        overflow: auto;
    }

    .spec-price .item-type-group .rl_col_tree .ctrl {
        width: 19px;
        height: 19px;
        border: 1px #e1e6ec solid;
        position: relative;
        cursor: pointer;
    }

    .spec-price .item-type-group .rl_col_tree .ctrl:after {
        content: '';
        position: absolute;
        left: 1px;
        right: 1px;
        top: 8px;
        height: 1px;
        background: #000;
    }

    .spec-price .item-type-group .rl_col_tree .ctrl:before {
        content: '';
        position: absolute;
        left: 8px;
        top: 1px;
        bottom: 1px;
        width: 1px;
        background: #000;
        transition: all 0.15s ease-in-out;
    }

    .spec-price .item-type-group__opened .rl_col_tree .ctrl:before {
        transform: rotate(90deg);
    }

    .spec-price .items-in-group .rl_col_tree .ctrl {
        position: absolute;
        width: 1px;
        top: 0px;
        left: 9px;
        bottom: 0px;
        background: #e1e6ec;
    }

    .spec-price .items-in-group > .item:first-child .rl_col_tree .ctrl {
        top: -13px;
    }

    .spec-price .items-in-group > .item:last-child .rl_col_tree .ctrl {
        bottom: 50%;
    }

    .spec-price .items-in-group > .item .rl_col_tree:after {
        content: '';
        height: 1px;
        position: absolute;
        bottom: 50%;
        left: 9px;
        right: 0px;
        background: #e1e6ec;
    }

    .spec-price .rl_col_service label {
        font-size: 14px;
        margin-top: 3px;
    }
</style>

<script>
    function priceHeight() {
        $('.spec-price').height($(window).height() - 160);
    }

    $(document).ready(function () {
        priceHeight();
        $(window).on('resize', function () {
            setTimeout(priceHeight, 100);
        });

        $(document).on('click', '.spec-price .rl_col_tree .ctrl', function () {
            var $p = $(this).closest('.item-type-group');
            var g = $p.attr('data-group');
            $p.toggleClass('item-type-group__opened');
            $('.items-in-group[data-group="' + g + '"]').toggleClass('hidden');
        });

        $(document).on('change', '.spec-price .item-type-group input[type="checkbox"]', function () {
            var $p = $(this).closest('.item-type-group');
            var checked = $(this).is(':checked');
            $('.items-in-group[data-group="' + $p.attr('data-group') + '"] .item').each(function () {
                $('input[type="checkbox"]', $(this)).prop('checked', checked);
            });
        });

        $('.spec-price__arrow-right').on('click', function () {
            $('.spec-price__left-items .item-type-price:not(.hidden) input:checked').each(function () {
                var $item = $(this).closest('.item');
                var $newItem = $item.clone();
                $item.addClass('hidden');
                var newUid = uniqid();
                $newItem.append('<input type="hidden" name="price[]" value="' + $newItem.attr('data-id') + '"/>');
                $('.rl_col_tree', $newItem).remove();
                $('input[type="checkbox"]', $newItem).attr('id', newUid).prop('checked', false);
                $('label', $newItem).attr('for', newUid);
                $newItem.appendTo('.spec-price__right-items .rl-table-rows');
            });
        });

        $('.spec-price__arrow-left').on('click', function () {
            $('.spec-price__right-items .item-type-price input:checked').each(function () {
                var $item = $(this).closest('.item');
                var $target = $('.spec-price__left-items .item-type-price[data-id="' + $item.attr('data-id') + '"]');
                $('input[type="checkbox"]', $target).prop('checked', false);
                $target.removeClass('hidden');
                $item.remove();
            });
        });

        $('.spec-price__right-content .rl-table-header input[type="checkbox"]').on('change', function () {
            var checked = $(this).is(':checked');
            $('.spec-price__right-items .item-type-price').each(function () {
                $('input[type="checkbox"]', $(this)).prop('checked', checked);
            });
        });
    });
</script>