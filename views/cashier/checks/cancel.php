<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<div class="cashier-check-cancel view-outer-ctr">

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
        'method' => 'post',
        'options' => [
            'id' => $formId,
            'class' => 'pb80'
        ],
        'validateOnType' => true,
        'enableAjaxValidation' => true,
    ]);
    ?>

    <h1>Чек возврата к #<?= $model->check->number; ?></h1>

    <?php if ($model->check->availableToCancelItems) { ?>
        <div class="rl-table">
            <div class="rl-table-header">
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
                        <td class="rl_col_date">Дата</td>
                        <td class="rl_col_direction">Направление</td>
                        <td class="rl_col_service">Услуга</td>
                        <td class="rl_col_user">Направил</td>
                        <td class="rl_col_summ">Сумма</td>
                    </tr>
                </table>
            </div>
            <div class="rl-table-rows">
                <?php foreach ($model->check->availableToCancelItems as $rel) {
                    echo $this->render('cancel/_service', [
                        'item' => $rel->directionItem,
                        'form' => $form
                    ]);
                }; ?>
            </div>
        </div>
        <h2>Оплата</h2>
        <div class="cashier-payment">
            <div class="clearfix">
                <div class="cashier-payment-col">
                    <?= $form->field($model, 'payment_cash') ?>
                </div>
                <div class="cashier-payment-col ml20">
                    <?= $form->field($model, 'payment_card') ?>
                </div>
                <div class="cashier-payment-col ml20">
                    <div class="cashier-check-cancel__info mt20">Выбрано услуг на сумму <span>0</span></div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <span class=""><i class="fa fa-warning mr5"></i>Нет услуг, доступных к возврату</span>
    <?php } ?>

    <div class="form-end pl20">
        <span class="btn btn-lg btn-primary js-submit-handler">Сформировать</span>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отмена</a>
        <?= Html::hiddenInput('back', Yii::$app->request->referrer); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .cashier-check-cancel {
        width: 980px;
        padding: 0px 20px 20px;
    }

    .cashier-check-cancel .rl_col_date {
        width: 85px;
    }

    .cashier-check-cancel .rl_col_direction {
        width: 90px;
    }

    .cashier-check-cancel .rl_col_summ {
        width: 120px;
    }

    .cashier-check-cancel__info span {
        font-weight: 700;
    }

    .cashier-payment-col {
        width: 200px;
        float: left;
    }
</style>

<script>
    function recalcTotal() {
        var total = 0;
        $('.cashier-check-cancel .rl-table-rows > .item').each(function () {
            if ($('.checkbox > input', $(this)).is(':checked')) {
                total += parseFloat($(this).attr('data-summ'));
            }
        });

        $('.cashier-check-cancel__info span').text(num(total));
    }

    $(document).ready(function () {
        $('.cashier-check-cancel .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.cashier-check-cancel .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v).trigger('change');
            });
        });

        $('.cashier-check-cancel .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.cashier-check-cancel .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v).trigger('change');
            });
        });

        $('.cashier-check-cancel .rl-table-rows .checkbox > input').on('change', function () {
            recalcTotal();
        })

        $('.cashier-check-cancel .js-submit-handler').on('click', function () {
            $('#<?=$formId;?>').submit();
        });

        $('.cashier-check-cancel .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
        });

        $('#cashiercancelform-payment_card').on('change keyup', function () {
            $('#<?=$formId;?>').yiiActiveForm('validateAttribute', 'cashiercancelform-payment_cash');
        });

        $('#<?=$formId;?>').on('beforeValidate', function (event, messages, deferreds) {
            if (!$('.cashier-check-cancel .rl-table-rows input:checked').length) {
                toastr.error('Не выбрано ни одной услуги');

                return false;
            }
        });

        $('#<?=$formId;?>').on('beforeSubmit', function () {
            $(this).addClass('block__loading');
        });
    });
</script>