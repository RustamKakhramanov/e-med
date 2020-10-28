<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="pl20 pr20 pb20" style="min-width: 800px;">
    <h1><?= $widget->title; ?></h1>

    <?php
    $selectedIds = json_decode($widget->ids);
    $formId = uniqid();
    $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'pb80',
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($widget, 'ids')->hiddenInput()->label(false); ?>

    <div class="schedule-widget">
        <div class="row">
            <div class="col-xs-6">
                <label>Доступные специалисты:</label>
                <div class="schedule-widget_left mr20">
                    <?php foreach ($widget->availableDoctors as $key => $d) { ?>

                        <?php
                        if (in_array($d->id, $selectedIds)) {
                            continue;
                        }
                        ?>

                        <?php $uid = uniqid(); ?>
                        <div class="item-checkbox checkbox" data-sort="<?= $key; ?>">
                            <?=
                            Html::checkbox($uid, false, [
                                'label' => null,
                                'id' => $uid,
                                'value' => $d->id
                            ])
                            ?>
                            <label for="<?= $uid; ?>"><?= $d->fio; ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>    
            <div class="col-xs-6">
                <div class="ml20">
                    <label>Выбранные:</label>
                </div>
                <div class="schedule-widget_right ml20">
                    <?php foreach ($widget->availableDoctors as $key => $d) { ?>
                        <?php if (in_array($d->id, $selectedIds)) { ?>
                            <?php $uid = uniqid(); ?>
                            <div class="item-checkbox checkbox" data-sort="<?= $key; ?>">
                                <?=
                                Html::checkbox($uid, false, [
                                    'label' => null,
                                    'id' => $uid,
                                    'value' => $d->id
                                ])
                                ?>
                                <label for="<?= $uid; ?>"><?= $d->fio; ?></label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="schedule-widget_controls">
            <span class="btn btn-xs btn-default js-add-handler"><i class="fa fa-arrow-right"></i></span><br/>
            <span class="btn btn-xs btn-default mt10 js-remove-handler"><i class="fa fa-arrow-left"></i></span>
        </div>
    </div>

    <div class="row mt20">
        <div class="col-xs-3">
            <?= $form->field($widget, 'days'); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($widget, 'rows'); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($widget, 'timer'); ?>
        </div>
    </div>
    <input type="hidden" name="_from_form" value="1"/>

    <div class="form-end mt30" style="left:0;">
        <div class="btn btn-lg btn-primary js-submit-handler">Сохранить</div>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отменить</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<style>
    .schedule-widget {
        position: relative;
    }

    .schedule-widget_left, 
    .schedule-widget_right {
        height: 350px;
        border: 1px #eceff3 solid;
        overflow-y: auto;
    }

    .schedule-widget_controls {
        position: absolute;
        left: 50%;
        margin-left: -19px;
        top: 50%;
        margin-top: -37px;
    }

    .schedule-widget .item-checkbox {
        padding: 7px 0px 10px 30px;
        border-top: 1px #eceff3 solid;
        margin: 0px;
    }

    .schedule-widget .item-checkbox:first-child {
        border-top: 0;
    }

    .schedule-widget .item-checkbox:hover {
        background: #f9fafb;
    }

    .field-schedule-ids.has-error .help-block-error {
        position: static;
        display: inline-block;
    }
</style>

<script>
    var scheduleWidget = {
        data: <?= $widget->ids; ?>,
        leftColumn: null,
        rightColumn: null,
        init: function () {
            var that = this;
            that.leftColumn = $('.schedule-widget_left');
            that.rightColumn = $('.schedule-widget_right');
            $('.schedule-widget_controls .btn').on('click', function () {
                return $(this).hasClass('js-add-handler') ? that.add() : that.remove();
            });
        },
        add: function () {
            var that = this;
            $('.item-checkbox input:checked', that.leftColumn).each(function () {
                that.data.push(1 * $(this).val());
                $(this).prop('checked', false);
                $(this).closest('.item-checkbox').appendTo(that.rightColumn);
            });
            that.syncData();
            that.sort(that.rightColumn);
        },
        remove: function () {
            var that = this;
            $('.item-checkbox input:checked', that.rightColumn).each(function () {
                $(this).prop('checked', false);
                $(this).closest('.item-checkbox').appendTo(that.leftColumn);
            });
            var newData = [];
            $('.item-checkbox input', that.rightColumn).each(function () {
                newData.push(1 * $(this).val());
            });
            that.data = newData;
            that.syncData();
            that.sort(that.leftColumn);
        },
        syncData: function () {
            $('#schedule-ids').val(JSON.stringify(this.data));
        },
        sort: function ($column) {
            $column.find('.item-checkbox').sort(function (a, b) {
                return +a.dataset.sort - +b.dataset.sort;
            }).appendTo($column);
        }
    };

    $(document).ready(function () {
        scheduleWidget.init();

        $('#<?= $formId; ?> .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('#<?= $formId; ?> .js-submit-handler').on('click', function () {
            $(this).closest('form').submit();
            return false;
        });

        $('#<?= $formId; ?>').on('beforeSubmit', function () {
            var $f = $(this);
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                dataType: 'json',
                success: function (data) {
                    tvData.schedule = data;
                    $f.closest('.modal-wrap').trigger('close');
                }
            });
            return false;
        });
    });
</script>