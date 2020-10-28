<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;

Yii::$app->view->params['bodyClass'] = 'bb-body';
$this->title = 'Чеки';
?>

<div class="row">
    <div class="col-md-12">
        <div class="bb-form-container">
            <h1><?= $this->title; ?></h1>

            <?php
            $form = ActiveForm::begin([
                        'method' => 'post',
                        'options' => [
                            'class' => 'checks-form',
                            'novalidate' => '', //ng
                        ],
                        'validateOnType' => true,
                        'enableAjaxValidation' => true,
            ]);
            ?>

            <div class="bb-form-head clearfix">
                <h2 class="clearfix pull-left">
                    <?= Yii::$app->user->identity->fio; ?>
                </h2>

                <div class="pull-right shift-ctr" style="margin-top: 8px;">
                    <?php if ($shift) { ?>
                        <div class="text">Смена #<?= $shift->id; ?> от <?= date('d.m.Y', strtotime($shift->start)); ?></div><span class="btn btn-default shift-toggle-handler  ml10">Закрыть</span>
                    <?php } else { ?>
                        <div class="text"><i class="fa fa-warning text-danger mr5"></i><span class="text-danger">Смена не открыта</span></div>
                        <span class="btn btn-default shift-toggle-handler ml10">Открыть</span>
                    <?php } ?>
                </div>

            </div>

            <div class="clearfix form-ctr">

                <div class="blind"></div>

                <div class="col-xs-12 form-left">
                    <div class="search-controls mt20">

                        <?=
                        $this->render('_search', [
                            'model' => $searchModel
                        ]);
                        ?>




                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="nopaid">
                                //todo
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="paid">

                                <div class="clearfix">
                                    <h2 class="pull-left">Найдено</h2>
                                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['чек', 'чека', 'чеков']); ?></span>
                                </div>

                                <div class="check-list scroll-ctr">

                                    <?php foreach ($dataProvider->models as $model) { ?>
                                        <div class="item" data-id="<?= $model->id; ?>">
                                            <?php $uid = uniqid(); ?>
                                            <div class="header collapsed" data-target="#<?= $uid; ?>" data-toggle="collapse"  data-toggle="collapse">
                                                <table class="form-table inner">
                                                    <tr>
                                                        <td class="col-first">
                                                            <?php if ($model->back_id) { ?>
                                                                <i class="ico-invoice-active"></i>
                                                            <?php } else { ?>
                                                                <i class="ico-invoice"></i>
                                                            <?php } ?>

                                                            <span class="number"><?= $model->number; ?></span>
                                                            от <?= date('d.m.Y', strtotime($model->created)); ?>
                                                            <?= $model->patient->fio; ?>
                                                            <span class="ml5 ico-collapse"></span>
                                                        </td>
                                                        <td class="col-middle">
                                                            <?= count($model->items); ?> услуги
                                                        </td>
                                                        <td class="col-money">
                                                            <strong><?=
                                                                number_format($model->sum, 2, ', ', ' ');
                                                                ;
                                                                ?></strong>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="action-group action-group-text row-action-group clearfix">
                                                    <?php if (!$model->back_id) { ?>
                                                        <a href="#" class="action action-cancel"><span class="action-icon-cancel"></span><span class="text-label">Отменить</span></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="content collapse" id="<?= $uid; ?>" aria-expanded="true">
                                                <div class="sub-items">
                                                    <?php foreach ($model->itemsWithState as $c => $item) { ?>
                                                        <div class="sub-item">
                                                            <table class="form-table inner">
                                                                <tr>
                                                                    <td class="col-first <?php if ($item->canceled) echo 'canceled'; ?>">
                                                                        <div class="status">
                                                                            <?php if ($item->canceled) { ?>
                                                                                <i class="fa fa-circle dot-icon text-muted"></i>
                                                                            <?php } else { ?>
                                                                                <i class="fa fa-circle dot-icon text-success"></i>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="count"><?= $c + 1; ?></div>

                                                                    </td>
                                                                    <td class="col-middle"></td>
                                                                    <td class="col-money">

                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4 form-right minified">
                    <div class="cancel-ctr services-list">
                        <h3 class="ml10">Услуги</h3>
                        <div class="scroll-ctr">
                            <div class="items"></div>
                        </div>
                        <div class="footer row-no-padding">
                            <div class="col-xs-8">
                                <span class="btn btn-sm btn-primary submit-form-handler">Бить чек</span>
                                <small class="text-muted ml5 mr5">или</small>
                                <span class="btn btn-sm btn-default cancel-form-handler">Отменить</span>
                            </div>
                            <div class="col-xs-4 text-right mt5">
                                <span class="total-cost"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>

    var shift = <?= $shift ? 'true' : 'false'; ?>;
    var cancelItem; //отменяемы чек
    var cancelList = []; //услуги для отмены

    function formHeight() {
        var $ctr = $('.form-ctr');
        $ctr.height($(window).height() - $ctr.offset().top);
        //storageRender();
    }

    function renderCancelList() {
        showSidePanel();
        var total = 0;
        
        $('.cancel-ctr .items').html('');
        $('.cancel-ctr h3').text('Возвратный чек к ' + $('.number', cancelItem).text());

        if (cancelList.length) {
            $.each(cancelList, function (k, item) {
                total += 1 * item.cost;
                $('.cancel-ctr .items').append('\
                            <div class="item clearfix row-no-padding" data-id="' + item.id + '">\n\
                                <div class="col-xs-8">\n\
                                    ' + item.group + ': ' + item.name + '<div class="action-group action-group-arrow clearfix"><a class="action action-delete" href="#"><span class="action-icon-delete"></span></a></div>\n\
                                </div>\n\
                                <div class="col-xs-4 text-right">\n\
                                    ' + number_format(item.cost, 2, ', ', ' ') + '\n\
                                </div>\n\
                            </div>');
            });
        } else {
            $('.cancel-ctr .items').html('<div class=""><i class="fa fa-warning mr5"></i>Нет услуг, доступных к возврату</div>');
        }
        
        $('.cancel-ctr .total-cost').text(total ? number_format(total, 2, ', ', ' ') : '');

        $('.cancel-ctr .scroll-ctr').jScrollPane({
            autoReinitialise: true,
            verticalGutter: 0,
            hideFocus: true
        });

    }

    function showSidePanel() {
        var $l = $('.form-ctr .form-left');

        if (!$l.hasClass('col-xs-8')) {
            $l.removeClass('col-xs-12').addClass('col-xs-8');
            $('.form-ctr .form-right').toggleClass('minified');
        }

        $('.form-ctr .blind').show().hide();
    }

    function hideSidePanel() {
        var $l = $('.form-ctr .form-left');

        if ($l.hasClass('col-xs-8')) {
            $l.removeClass('col-xs-8').addClass('col-xs-12');
            $('.form-ctr .form-right').toggleClass('minified');
        }

        $('.form-ctr .blind').show().hide();
    }

    $(document).ready(function () {

        formHeight();
        $(window).on('resize', function () {
            setTimeout(function () {
                formHeight();
            }, 400);
        });

        $('.form-left .scroll-ctr').jScrollPane({
            autoReinitialise: true,
            verticalGutter: 0,
            hideFocus: true
        });

        $('#w0').on('keyup keypress', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        $(document).on('click', '.shift-toggle-handler', function () {
            $.ajax({
                url: '/direction/shift-toggle',
                type: 'get',
                dataType: 'json',
                success: function (resp) {
                    shift = resp.hasOwnProperty('id');
                    if (shift) {
                        $('.shift-ctr').html('<span class="text">Смена #' + resp.id + ' от ' + moment(resp.start).format('DD.MM.YYYY') + '</span><span class="btn btn-default shift-toggle-handler ml10">Закрыть</span>');

                    } else {
                        $('.shift-ctr').html('\n\
                        <div class="text"><i class="fa fa-warning text-danger mr5"></i><span class="text-danger">Смена не открыта</span></div>\n\
                        <span class="btn btn-default shift-toggle-handler ml10">Открыть</span>\n\
                    ');
                    }
                }
            });
        });

        $(document).on('click', '.check-list .action-cancel', function () {
            var $item = $(this).closest('.item');
            var id = $item.attr('data-id');
            $.ajax({
                url: '/check/get-cancel-items/' + id,
                dataType: 'json',
                success: function (resp) {
                    cancelList = resp;
                    cancelItem = $item;
                    renderCancelList();
                }
            });

            return false;
        });

        $(document).on('click', '.cancel-ctr .action-delete', function () {
            cancelList.splice($(this).closest('.item').index(), 1);
            renderCancelList();

            return false;
        });

        $(document).on('click', '.cancel-ctr .cancel-form-handler', function () {
            hideSidePanel();
        });

        $(document).on('click', '.cancel-ctr .submit-form-handler', function () {
            if (cancelList.length) {
                var items = cancelList;
                cancelList = [];
                hideSidePanel();

                $.ajax({
                    url: '/check/cancel-items',
                    type: 'post',
                    data: {
                        items: items,
                    },
                    dataType: 'json',
                    success: function (resp) {

                    }
                });
            }
        });
    });
</script>

<style>
    .payer-ctr {
        padding-top: 7px;
    }
</style>