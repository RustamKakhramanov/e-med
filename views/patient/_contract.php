<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

$blockUid = uniqid();
?>

<h2 class="mt30" data-toggle="collapse" data-target="#<?= $blockUid; ?>">
    Список договоров
    <span class="ico-collapse"></span>
</h2>
<div class="row mt20 data-block" id="<?= $blockUid; ?>" data-type="contract">
    <div class="col-xs-3">
        <div class="add-data-block">
            <h3>Новый договор</h3>
            <?= $form->field($model, 'data_contract')->hiddenInput()->label(false); ?>

            <div class="form-group">
                <label>Название</label>
                <input type="text" class="form-control data-required" name="data[contract][name]"/>
            </div>

            <div>
                <label>Период действия</label>
                <table>
                    <tr>
                        <td>
                            <div class="form-group">
                                <?=
                                MaskedInput::widget([
                                    'name' => 'data[contract][start]',
                                    'clientOptions' => [
                                        'clearIncomplete' => true,
                                        'alias' => 'dd.mm.yyyy'
                                    ],
                                    'options' => [
                                        'class' => 'form-control data-required'
                                    ]
                                ]);
                                ?>
                            </div>
                        </td>
                        <td class="text-center pb15" style="width:20px;">
                            &mdash;
                        </td>
                        <td>
                            <div class="form-group">
                                <?=
                                MaskedInput::widget([
                                    'name' => 'data[contract][end]',
                                    'clientOptions' => [
                                        'clearIncomplete' => true,
                                        'alias' => 'dd.mm.yyyy'
                                    ],
                                    'options' => [
                                        'class' => 'form-control data-required'
                                    ]
                                ]);
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="btn btn-sm btn-primary submit-handler">Добавить</div>
        </div>
    </div>
    <div class="col-xs-9">
        <div class="result-data-block">

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        //toremove
        //$('.tabs a[href="#contracts"]').click();

        $('#contracts .submit-handler').on('click', function () {

            var $p = $(this).closest('.data-block');
            var type = $p.attr('data-type');

            //флаг валидации
            var valid = true;

            $('.form-group', $p).removeClass('has-error');
            $('.data-required', $p).each(function () {
                if ($.trim($(this).val()) == '') {
                    $(this).closest('.form-group').addClass('has-error');
                    valid = false;
                }
            });

            if (!valid) {
                return false;
            }

            var entry = $(this).closest('form').serializeObject().data[type];
            entry.contractor_id = null;
            entry.start = moment(entry.start, 'DD.MM.YYYY').format('YYYY-MM-DD');
            entry.end = moment(entry.end, 'DD.MM.YYYY').format('YYYY-MM-DD');
            entry.typical = false;

            storageAdd(type, entry);
            $('.add-data-block input[type="text"], .add-data-block textarea', $p).val('');
        });

        $(document).on('click.dl', '#contracts .data-block .action-icon-edit', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = $.extend(true, {}, formStorage[$block.attr('data-type')][$item.attr('data-key')]);
            data.start = moment(data.start, 'YYYY-MM-DD').format('DD.MM.YYYY');
            data.end = moment(data.end, 'YYYY-MM-DD').format('DD.MM.YYYY');

            var $clone = $('.add-data-block', $block).children().not('h3').clone(false);
            $('.submit-handler', $clone).remove();
            $('.form-group', $clone).removeClass('has-error');

            $('.checkbox', $clone).each(function () {
                var uid = uniqid();
                $('> input', $(this)).attr('id', uid).prop('checked', false);
                $('> label', $(this)).attr('for', uid);
            });

            $item.html($clone);

            $('.submit-handler', $item).after('\n\
                <span class="btn btn-sm btn-primary update-handler mr5">Сохранить</span>\n\
                <span class="btn btn-sm btn-default cancel-handler">Отмена</span>\n\
            ');

            $('.submit-handler', $item).remove();

            $('.bootstrap-select', $item).remove();
            $('select.selectpicker', $item).selectpicker();
            $('select.form-switcher', $item).prop('disabled', true).selectpicker('refresh');

            $('input, textarea', $item).each(function (k, v) {
                var prop = $(this).attr('name').match(/\[([a-z_]+){1}\]$/)[1];
                if (data.hasOwnProperty(prop)) {
                    if ($(this).attr('type') == 'checkbox') {
                        $(this).prop('checked', true);
                    } else {
                        $(this).val(data[prop]);
                    }
                }
            });

            $('input[data-plugin-inputmask]', $item).each(function () {
                $(this).attr('id', uniqid()).inputmask(window[$(this).attr('data-plugin-inputmask')]);
            });

            e.preventDefault();
            return false;
        });

        $(document).on('click.dl', '#contracts .data-block .update-handler', function (e) {

            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = formStorage[$block.attr('data-type')][$item.attr('data-key')];

            //флаг валидации
            var valid = true;

            $('.form-part[data-type="' + data.type + '"] .form-group', $item).removeClass('has-error');
            $('.form-part[data-type="' + data.type + '"] .data-required', $item).each(function () {
                if ($.trim($(this).val()) == '') {
                    $(this).closest('.form-group').addClass('has-error');
                    valid = false;
                }
            });

            $('.form-group', $item).removeClass('has-error');
            $('.data-required', $item).each(function () {
                if ($.trim($(this).val()) == '') {
                    $(this).closest('.form-group').addClass('has-error');
                    valid = false;
                }
            });

            if (!valid) {
                return false;
            }

            var entry = $(this).closest('form').serializeObject().data[$block.attr('data-type')];
            entry.start = moment(entry.start, 'DD.MM.YYYY').format('YYYY-MM-DD');
            entry.end = moment(entry.end, 'DD.MM.YYYY').format('YYYY-MM-DD');

            storageEdit($block.attr('data-type'), $item.attr('data-key'), entry);
        });

        $(document).on('click.dl', '#contracts .data-block .action-icon-delete', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');

            storageRemove($block.attr('data-type'), $item.attr('data-key'));

            e.preventDefault();
            return false;
        });

    });
</script>