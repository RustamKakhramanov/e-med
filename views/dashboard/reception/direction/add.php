<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if ($direction) {
    $this->title = 'Направление #' . $direction->numberPrint . ' | ' . $patient->fio;
} else {
    $this->title = 'Новое направление | ' . $patient->fio;
}

?>

<div class="view-outer-ctr">
    <div class="row">
        <div class="col-md-12">

            <h1><?= $this->title; ?></h1>

            <?php
            $formId = uniqid();
            $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'patient-direction-form pb80',
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
            ]);
            ?>

            <div class="row">
                <div class="col-xs-6">
                    <h2 class="direction-total mt0 pull-left">Нет услуг</h2>
                </div>
                <div class="col-xs-6">
                    <div class="btns pull-right clearfix">
            <span class="btn btn-sm btn-primary pull-left mr10 js-add-hander"><i
                        class="fa fa-plus mr5"></i>Добавить услугу</span>
                        <span class="btn btn-sm btn-default pull-left mr10 js-add-few-hander"><i
                                    class="fa fa-magic mr5"></i>Подбор услуг</span>
                        <span class="btn btn-sm btn-default pull-left js-remove-handler"><i
                                    class="fa fa-times mr5"></i>Удалить</span>
                    </div>
                </div>
            </div>


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
                            <td class="rl_col_service">Услуга</td>
                            <td class="rl_col_cost">Стоимость</td>
                            <td class="rl_col_count">Кол-во</td>
                            <td class="rl_col_summ">Сумма</td>
                            <td class="rl_col_doctor">Специалист</td>
                        </tr>
                    </table>
                </div>
                <div class="rl-table-rows">
                    <?php if ($direction) {
                        foreach ($direction->directionItems as $item) {
                            echo $this->render('_add_row', [
                                'model' => $item
                            ]);
                        }
                    }; ?>
                </div>
                <!--            <div class="rl-table-footer">-->
                <!--                <table>-->
                <!--                    <tr>-->
                <!--                        <td class="rl_col_check"></td>-->
                <!--                        <td class="rl_col_service"></td>-->
                <!--                        <td class="rl_col_cost"></td>-->
                <!--                        <td class="rl_col_count text-right">-->
                <!--                            <div class="value">0</div>-->
                <!--                        </td>-->
                <!--                        <td class="rl_col_summ text-right">-->
                <!--                            <div class="value">0</div>-->
                <!--                        </td>-->
                <!--                        <td class="rl_col_doctor"></td>-->
                <!--                    </tr>-->
                <!--                </table>-->
                <!--            </div>-->
            </div>

            <div class="form-end mt30">
                <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
                <span class="ml10 mr10">или</span>
                <a href="<?= Url::to(['patient/direction', 'id' => $patient->id]); ?>"
                   class="btn btn-sm btn-default">Назад</a>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<style>
    .patient-direction-form .rl_col_count {
        width: 80px;
    }

    .patient-direction-form .rl_col_cost {
        width: 130px;
    }

    .patient-direction-form .rl_col_summ {
        width: 130px;
    }
</style>

<script>
    $(document).ready(function () {

        $('#<?=$formId;?> .rl-table').on('recalcTotal', function () {
            var total = {
                count: 0,
                summ: 0
            };
            $('.rl-table-rows > .item', $(this)).each(function () {
                var c = $('.input-count', $(this)).val() * 1;
                var s = parseFloat($('.input-cost', $(this)).val()) || 0;
                total.count += c;
                total.summ += s * c;
            });
            if (total.count) {
                $('.direction-total').text(total.count + ' ' + declOfNum(total.count, ['услуга', 'услуги', 'услуг']) + ' на сумму ' + num(total.summ));
            } else {
                $('.direction-total').text('Нет услуг');
            }
        }).trigger('recalcTotal');

        $('.js-add-hander').on('click', function () {
            $.ajax({
                url: '<?=Url::to(['patient/direction-add-row']);?>',
                success: function (item) {
                    $('#<?=$formId;?> .rl-table-rows').append(item);
                    $('#<?=$formId;?> .rl-table').trigger('recalcTotal');
                }
            });
        });

        $('.rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v);
            });
        });

        $('.js-remove-handler').on('click', function () {
            $('.rl-table-rows .item').each(function () {
                var $item = $(this);
                var $c = $('.rl_col_check .checkbox > input', $item);
                if ($c.prop('checked')) {
                    $item.remove();
                }
            });

            $('#<?=$formId;?> .rl-table').trigger('recalcTotal');
        });

        $('.js-add-few-hander').on('click', function () {
            openModal({
                url: '<?=Url::to(['price/few-picker']);?>'
            });
        });

        $(document).on('priceFewPick', function (e, data) {
            $('body').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['patient/direction-add-few-row']);?>',
                type: 'post',
                data: data,
                success: function (items) {
                    $('#<?=$formId;?> .rl-table-rows').append(items);
                    $('#<?=$formId;?> .rl-table').trigger('recalcTotal');
                    $('body').removeClass('block__loading');
                }
            });
        });

        $('#<?=$formId;?>').on('beforeSubmit', function () {
            if (!$('.rl-table-rows > .item', $(this)).length) {
                toastr.error('Отсутствуют услуги');
                return false;
            }

            var $f = $(this);
            $f.closest('.view-outer-ctr').addClass('block__loading');
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                success: function (resp) {
                    $('.b-reception-sidebar').trigger('reloadData');
                    $f.closest('.modal-wrap').trigger('close');
                }
            });

            return false;
        });
    });
</script>