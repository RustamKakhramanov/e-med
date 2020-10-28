<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;

$uid = uniqid();

?>

<div class="item item-<?= $uid; ?>" data-id="<?= $model->id; ?>">
    <table>
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
        <td class="rl_col_service" data-cost="<?php if ($model->price_id) echo $model->price->cost; ?>">
            <div class="form-group mb0 field-items-<?= $uid; ?>-price_id">
                <div class="relation-picker" data-id="<?= $uid; ?>">
                    <?=
                    Html::hiddenInput('items[' . $uid . '][price_id]', $model->price_id ? $model->price_id : null, [
                        'class' => 'form-control target_value',
                        'data-text' => $model->price_id ? $model->price->title : '',
                        'id' => 'items-' . $uid . '-price_id'
                    ]);
                    ?>
                    <div class="clearfix">
                        <div class="btn-ctr pull-right">
                            <span class="item item-open-picker" title="Расширенный поиск"></span>
                            <span class="item item-clear" title="Очистить значение"></span>
                        </div>
                        <div class="search_input-ctr">
                            <?=
                            Html::input('text', '', $model->price_id ? $model->price->title : '', [
                                'class' => 'form-control search_input',
                                'placeholder' => 'поиск'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <p class="help-block help-block-error"></p>
            </div>
        </td>
        <td class="rl_col_cost">
            <div class="form-group mb0 field-items-<?= $uid; ?>-cost">
                <?= Html::input('text', 'items[' . $uid . '][cost]', $model->price_id ? $model->price->cost : null, [
                    'class' => 'form-control input-cost',
                    'readonly' => true,
                    'id' => 'items-' . $uid . '-cost'
                ]); ?>
                <p class="help-block help-block-error"></p>
            </div>
        </td>
        <td class="rl_col_count">
            <div class="form-group mb0 field-items-<?= $uid; ?>-count">
                <div class="inc-input clearfix text-center">
                    <a class="control" href="#" data-action="minus"><i class="fa fa-minus"></i></a>
                    <span class="value"><?= $model->count ? $model->count : 1; ?></span>
                    <a class="control" href="#" data-action="plus"><i class="fa fa-plus"></i></a>
                    <?= Html::hiddenInput('items[' . $uid . '][count]', $model->count ? $model->count : 1, [
                        'class' => 'input-count',
                        'id' => 'items-' . $uid . '-count'
                    ]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
        </td>
        <td class="rl_col_summ">
            <div class="form-group mb0">
                <input type="text" class="form-control input-summ" id="items-<?= $uid; ?>-summ"
                       value="<?php if ($model->price_id) echo $model->price->cost; ?>" readonly=""/>
            </div>
        </td>
        <td class="rl_col_doctor">
            <div class="form-group mb0 field-items-<?= $uid; ?>-doctor_id">
                <?=
                Html::dropDownList('items[' . $uid . '][doctor_id]', $model->doctor_id, $model->doctorsCanUse, [
                    'class' => 'form-control selectpicker',
                    'id' => 'items-' . $uid . '-doctor_id',
                    'prompt' => 'Не выбран'
                ]);
                ?>
                <p class="help-block help-block-error"></p>
            </div>
        </td>
    </table>
    <?= Html::hiddenInput('items[' . $uid . '][id]', $model->id); ?>
</div>

<script>
    $(document).ready(function () {
        $('.item-<?= $uid; ?> .selectpicker').selectpicker();

        $('.input-cost, .input-summ', $('.item-<?= $uid; ?>')).inputmask('num');
        $('.input-time', $('.item-<?= $uid; ?>')).inputmask('hh:mm');

        $('.item-<?= $uid; ?> .rl_col_service .relation-picker').relationPicker({
            url_picker: '<?= Url::to(['price/picker']); ?>',
            url_ac: '<?= Url::to(['price/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'pricePick',
            min_length: 1
        });

        $('.item-<?= $uid; ?> .rl_col_doctor .relation-picker').relationPicker({
            url_picker: '<?= Url::to(['doctor/picker']); ?>',
            url_ac: '<?= Url::to(['doctor/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'doctorPick',
            min_length: 1
        });

        $('.item-<?= $uid; ?> .input-datepicker-ui').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2020',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date).trigger('change');
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });
        $('.item-<?= $uid; ?> .input-datepicker .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        function <?= 'init_' . $uid; ?>() {
            var keys = ['price_id', 'count', 'cost', 'date', 'time'];
            $.each(keys, function (k, keyName) {
                var n = 'items-<?= $uid; ?>-' + keyName;
                $form.yiiActiveForm('add', {
                    id: n,
                    name: n,
                    container: '.field-' + n,
                    input: '#' + n,
                    error: '.help-block',
                    enableAjaxValidation: true
                });
            });
        }

        var $form = $('.item-<?= $uid; ?>').closest('form');
        if ($form.data('yiiActiveForm')) {
            <?= 'init_' . $uid; ?>();
        } else {
            $form.on('afterInit', function () {
                <?= 'init_' . $uid; ?>();
            });
        }

        $('.item-<?= $uid; ?> .inc-input .control').on('click', function () {
            var $p = $(this).parent();
            var v = parseInt($('input', $p).val());
            if ($(this).attr('data-action') == 'plus') {
                v++;
            } else {
                v--;
                if (v < 1) {
                    v = 1;
                }
            }
            $('input', $p).val(v).trigger('change');
            $('span.value', $p).text(v);

            return false;
        });

        $('#items-<?=$uid;?>-price_id').on('change', function () {
            var $row = $(this).closest('.item');
            if ($(this).val() == '') {
                $('.rl_col_doctor select', $row).html('<option value="">Не выбран</option>').selectpicker('refresh');
                $('.rl_col_cost input', $row).val('');
                $('.rl_col_summ input', $row).val('').trigger('change');
            } else {
                $('.rl_col_doctor .form-group', $row).addClass('block__loading');
                $('.rl_col_cost .form-group', $row).addClass('block__loading');
                $.ajax({
                    url: '<?=Url::to(['direction/master-load-service']);?>/' + $(this).val(),
                    dataType: 'json',
                    success: function (resp) {
                        $('.rl_col_cost input', $row).val(resp.cost).trigger('change');
                        $('.rl_col_doctor select', $row).html('<option value="">Не выбран</option>');
                        $.each(resp.doctors, function (id, fio) {
                            $('.rl_col_doctor select', $row).append('<option value="' + id + '">' + fio + '</option>');
                        });
                        $('.rl_col_doctor select', $row).selectpicker('refresh');
                        $('.rl_col_doctor .form-group', $row).removeClass('block__loading');
                        $('.rl_col_cost .form-group', $row).removeClass('block__loading');
                    }
                });
            }
        });

        $('#items-<?=$uid;?>-cost').on('change', function () {
            var $row = $(this).closest('.item');
            $('.input-summ', $row).val(parseFloat($('.input-cost', $row).val()) * parseFloat($('.input-count', $row).val())).trigger('change');
        });

        $('#items-<?=$uid;?>-count').on('change', function () {
            var $row = $(this).closest('.item');
            $('.input-summ', $row).val(parseFloat($('.input-cost', $row).val()) * parseFloat($('.input-count', $row).val())).trigger('change');
        });

        $('#items-<?=$uid;?>-summ').on('change', function () {
            $(this).closest('.rl-table').trigger('recalcTotal');
        });
    });
</script>