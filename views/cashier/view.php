<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<div class="cashier-form view-outer-ctr">

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

    <h1><?= $patient->fio; ?></h1>
    <span>ИИН: <?= $patient->iin; ?></span>
    <span class="ml20">Дата рождения: <?= $patient->birthdayPrint; ?></span>
    <span class="ml20">Пол: <?= $patient->sexPrint; ?></span>

    <h2>Неоплаченные услуги</h2>

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
            <?php foreach ($items as $item) {
                echo $this->render('view/_service', [
                    'item' => $item,
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
                <div class="cashier-payment-info mt20">Выбрано услуг на сумму <span>0</span></div>
            </div>
        </div>
    </div>

    <div class="form-end pl20">
        <span class="btn btn-lg btn-primary js-submit-handler">Оплатить</span>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отмена</a>
        <?= Html::hiddenInput('back', Yii::$app->request->referrer); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .cashier-form {
        width: 980px;
        padding: 0px 20px 20px;
    }

    .cashier-form .rl_col_date {
        width: 85px;
    }

    .cashier-form .rl_col_direction {
        width: 90px;
    }

    .cashier-form .rl_col_summ {
        width: 120px;
    }

    .cashier-payment-info span {
        font-weight: 700;
    }

    .cashier-form-end {
        background: rgba(248, 245, 229, 0.9);
        padding: 10px 20px;
        margin: 20px -20px -20px;
    }

    .cashier-payment-col {
        width: 200px;
        float: left;
    }
</style>

<script>
    function recalcTotal() {
        var total = 0;
        $('.cashier-form .rl-table-rows > .item').each(function () {
            if ($('.checkbox > input', $(this)).is(':checked')) {
                total += parseFloat($(this).attr('data-summ'));
            }
        });

        $('.cashier-payment-info span').text(num(total));
    }

    $(document).ready(function () {
        $('.cashier-form .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.cashier-form .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v).trigger('change');
            });
        });

        $('.cashier-form .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.cashier-form .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v).trigger('change');
            });
        });

        $('.cashier-form .rl-table-rows .checkbox > input').on('change', function () {
            recalcTotal();
        })

        $('.cashier-form .js-submit-handler').on('click', function () {
            $('#<?=$formId;?>').submit();
        });

        $('.cashier-form .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
        });

        $('#<?=$formId;?>').on('beforeValidate', function (event, messages, deferreds) {
            if (!$('.cashier-form .rl-table-rows input:checked').length) {
                toastr.error('Не выбрано ни одной услуги');

                return false;
            }
        });

        $('#cashierform-payment_card').on('change keyup', function () {
            $('#<?=$formId;?>').yiiActiveForm('validateAttribute', 'cashierform-payment_cash');
        });

        $('#<?=$formId;?>').on('beforeSubmit', function () {
            $(this).addClass('block__loading');
        });
    });
</script>