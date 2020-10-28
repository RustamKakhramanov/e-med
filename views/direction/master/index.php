<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
use yii\helpers\ArrayHelper;
use app\components\RelPicker\RelPicker;

Yii::$app->view->params['bodyClass'] = 'master-body';
$this->title = 'Мастер направлений';

?>

<div class="row">
    <div class="col-md-12">
        <div class="master-form-container history__open">
            <h1><?= $this->title; ?></h1>

            <?php
            $formId = uniqid();
            $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'master-form',
                    'novalidate' => '', //ng
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
            ]);
            ?>

            <div class="master-head">
                <div class="row">
                    <div class="col-xs-4">
                        <?php
                        $patientInputId = uniqid();
                        echo RelPicker::widget([
                            'uid' => $patientInputId,
                            'form' => $form,
                            'model' => $model,
                            'field' => 'patient_id',
                            'text' => $model->patient_id ? $model->patient->fio : '',
                            'groupClass' => 'mb0'
                        ]);
                        ?>
                    </div>
                    <div class="col-xs-4"></div>
                    <div class="col-xs-4">
                        <div class="pull-right hidden">
                            <div class="btn btn-default history-toggle mt10">
                                <i class="fa fa-history mr5"></i>
                                <span class="history-toggle-show">Показать</span>
                                <span class="history-toggle-hide">Скрыть</span>
                                историю
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="master-left">
                <div class="master-left__head">
                    <div class="master-left__header">
                        <h2>Услуги</h2><span class="subheader"></span>
                        <div class="btns clearfix">
                            <span class="btn btn-sm btn-default pull-right js-remove-handler"><i class="fa fa-times mr5"></i>Удалить</span>
                            <span class="btn btn-sm btn-primary pull-right mr10 js-add-hander"><i class="fa fa-plus mr5"></i>Добавить</span>
                        </div>

                    </div>
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
                                    <td class="rl_col_service">Услуга</td>
                                    <td class="rl_col_cost">Стоимость</td>
                                    <td class="rl_col_count">Кол-во</td>
                                    <td class="rl_col_summ">Сумма</td>
                                    <td class="rl_col_doctor">Специалист</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="master-left__content scroll-ctr">
                    <div class="rl-table">
                        <div class="rl-table-rows">

                        </div>
                    </div>
                </div>
                <div class="form-end mt30">
                    <div class="btn btn-lg btn-primary form-submit-handler">Оформить направление</div>
                    <span class="ml10 mr10">или</span>
                    <a href="<?= Yii::$app->request->referrer; ?>" class="btn btn-sm btn-default">Назад</a>
                </div>
            </div>
            <div class="master-right">
                <h2>История</h2>
                <div class="master-history-ctr">
                    <div class="rl-table">
                        <div class="rl-table-header mt0">
                            <table>
                                <tr>
                                    <td class="rl_col_date">Дата</td>
                                    <td class="rl_col_service">Услуга</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="master-right__content scroll-ctr">
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
    .master-form-container {
        overflow: hidden;
        position: relative;
    }

    .master-left {
        position: absolute;
        left: 0px;
        padding-left: 20px;
        padding-right: 20px;
        top: 151px;
        right: 0px;
        bottom: 70px;
        background: #fff;
        /*transition: right 0.2s linear;*/
    }

    .master-right {
        position: absolute;
        top: 151px;
        right: -370px;
        width: 350px;
        bottom: 70px;
        background: #fff;
        /*transition: right 0.2s linear;*/
        padding: 0px 20px;
        overflow: hidden;
    }

    .history__open .master-left {
        right: 370px;
    }

    .history__open .master-right {
        right: 0px;
    }

    .history-toggle-hide {
        display: none;
    }

    .history__open .history-toggle-show {
        display: none;
    }

    .history__open .history-toggle-hide {
        display: inline;
    }

    .master-left__header {
        position: relative;
    }

    .master-left__header h2 {
        display: inline-block;
    }

    .master-left__header .subheader {
        display: inline-block;
    }

    .master-left__header .btns {
        position: absolute;
        top: 18px;
        right: 0px;
    }

    .master-left__content {
        position: absolute;
        top: 105px;
        left: 20px;
        right: 20px;
        bottom: 0px;
        outline: 0;
    }

    .master-left__content:hover .jspVerticalBar {
        opacity: 1;
    }

    .master-left .rl_col_count {
        width: 80px;
    }

    .master-left .rl_col_cost {
        width: 130px;
    }

    .master-left .rl_col_summ {
        width: 130px;
    }

    .master-left .rl_col_date {
        width: 135px;
    }

    .master-left .rl_col_date .dropdown-menu {
        min-width: 210px;
    }

    .master-left .rl_col_time {
        width: 65px;
    }

    .history-toggle i.fa {
        vertical-align: top;
        margin-top: 3px;
    }

    .master-history-ctr {
        margin-top: 10px;
    }

    .master-right__content {
        position: absolute;
        top: 105px;
        left: 20px;
        right: 20px;
        bottom: 10px;
        outline: 0;
    }

    .master-right__content:hover .jspVerticalBar {
        opacity: 1;
    }

    .master-history-placeholder {
        position: absolute;
        width: 32px;
        height: 32px;
        background: url(/img/ajax-loader.gif) no-repeat;
        left: 50%;
        top: 50%;
        margin: -16px 0px 0px -16px;
    }

    .master-history-empty {
        position: absolute;
        top: 50%;
        margin-top: -30px;
        left: 0px;
        right: 0px;
        color: #9eadbc;
        text-align: center;
    }

    .history-item-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .master-history-ctr .rl-table-rows > .item {
        cursor: pointer;
    }

    .master-history-ctr .rl-table-rows > .item:hover {
        color: #446584;
    }

    .master-history-ctr .rl_col_date {
        width: 80px;
    }
</style>

<script>
    function formHeight() {
        var $ctr = $('.master-form-container');
        $ctr.height($(window).height());
    }

    function loadHistory() {
        var patient_id = $('#<?=$patientInputId;?>').val();
        if (!patient_id) {
            $('.master-right__content').html('');

            return false;
        }

        $('.master-right__content').html('<div class="master-history-placeholder"></div>');
        $.ajax({
            url: '<?=Url::to(['direction/load-history']);?>/' + patient_id,
            success: function (resp) {
                $('.master-right__content').html(resp);
            }
        });
    }

    $(document).ready(function () {
        formHeight();
        $(window).on('resize', function () {
            setTimeout(function () {
                formHeight();
            }, 100);
        });

        loadHistory();

        $('.patient_id-ctr .relation-picker').each(function () {
            $(this).relationPicker({
                url_picker: '<?= Url::to(['patient/picker']); ?>',
                url_ac: '<?= Url::to(['patient/ac', 'q' => '_QUERY_']); ?>',
                event_name: 'patientPick',
                min_length: 3
            });
        });

        $('.js-add-hander').on('click', function () {
            $('.master-left').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['direction/add-row']);?>',
                success: function (item) {
                    $('.master-left__content .rl-table-rows').append(item);
                    $('#<?=$formId;?>').trigger('recalcTotal');
                    $('.master-left').removeClass('block__loading');
                }
            });
        });

        $('.master-left__content').jScrollPane({
            autoReinitialise: true
        });

        $('.master-right__content').jScrollPane({
            autoReinitialise: true
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
        });

        $('#<?=$formId;?>').on('recalcTotal', function () {
            var c = $('.master-left__content .rl-table-rows > .item').length;
            var total = 0;
            $('.master-left__content .rl-table-rows > .item').each(function () {
                var v = parseFloat($('.input-cost', $(this)).val());
                if (v) {
                    total += v;
                }
            });

            $('.master-left__header .subheader').text(c + ' на сумму ' + num(total, 2));
        });

        $(document).on('click', '.master-history-ctr .rl-table-rows > .item', function () {
            openModal({
                url: '<?=Url::to(['direction/master-view-history']);?>/' + $(this).attr('data-id')
            });
        });

        $('#<?=$patientInputId;?>').on('change', function () {
            console.log($(this).val());
            loadHistory();
        });

        $('#<?=$formId;?>').on('beforeSubmit', function () {
            var $f = $(this);
            if (!$('.master-left__content .rl-table-rows > .item').length) {
                toastr.error('Требуется добавить услуги');
                return false;
            }

            $f.addClass('block__loading');
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                success: function (resp) {
                    $('.master-left__content .rl-table-rows > .item').remove();
                    $f.trigger('recalcTotal');
                    $('.patient_id-ctr .target_value').attr('data-text', '').val('');
                    $('.patient_id-ctr .search_input').val('');
                    loadHistory();
                    toastr.success('Успешно сохранено');
                    $f.removeClass('block__loading');
                },
                error: function () {
                    toastr.error('Произошла ошибка');
                    $f.removeClass('block__loading');
                }
            });

            return false;
        });
    });
</script>