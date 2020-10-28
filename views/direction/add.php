<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
use yii\helpers\ArrayHelper;

Yii::$app->view->params['bodyClass'] = 'master-body';
$this->title = 'Мастер направлений';

$contractJson = [];
$contractSelect = [];
foreach ($patient->CurrentContracts as $contract) {
    $contractSelect[$contract->id] = $contract->name . ($contract->typical ? ' (Типовой)' : '') . ' от ' . ruDateCase($contract->start);
    $contractJson[$contract->id] = $contract->toArray();
    $contractJson[$contract->id]['contractor'] = $contract->contractor_id ? $contract->contractor->name : null;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="master-form-container">
            <h1><?= $this->title; ?></h1>

            <?php
            $form = ActiveForm::begin([
                        'method' => 'post',
                        'options' => [
                            'class' => 'master-form',
                            'novalidate' => '', //ng
                        ],
                        'validateOnType' => true,
                        'enableAjaxValidation' => true,
            ]);
            ?>

            <div class="master-head">
                <h2 class="clearfix">
                    <?= $patient->fio; ?><span class="subheader"><?= date('d.m.Y', strtotime($patient->birthday)); ?>, <?= $patient->sex ? 'мужской' : 'женский'; ?></span>
                </h2>

                <div class="clearfix">

                    <?=
                    Html::dropDownList('Direction[contract_id]', $model->contract_id, $contractSelect, [
                        'class' => 'selectpicker auto pull-left',
                        'id' => 'direction-contract_id'
                    ]);
//                        $form->field($model, 'contract_id')->dropDownList(
//                                $contractSelect, [
//                            'class' => 'selectpicker auto',
//                                //'prompt' => ' '
//                                ]
//                        )->label(false);
                    ?>
                    <div class="payer-ctr pull-left ml20">Плательщик «<span></span>»</div>

                    <div class="pull-right shift-ctr">
                        <?php if ($shift) { ?>
                            <div class="text">Смена #<?= $shift->id; ?> от <?= date('d.m.Y', strtotime($shift->start)); ?></div><span class="btn btn-default shift-toggle-handler  ml10">Закрыть</span>
                        <?php } else { ?>
                            <div class="text"><i class="fa fa-warning text-danger mr5"></i><span class="text-danger">Смена не открыта</span></div>
                            <span class="btn btn-default shift-toggle-handler ml10">Открыть</span>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="clearfix form-ctr">
                <div class="col-xs-8 form-left">
                    <div class="pl10 pr10">
                        <?php
                        $uid = uniqid();
                        ?>
                        <h2>
                            Направления<span class="subheader direction-counter">0</span>
                        </h2>
                    </div>

                    <div class="">
                        <div class="checkbox-rows">
                            <div class="empty-label">
                                <div class="empty-header">Нет направлений</div>
                                <div class="empty-text">Чтобы добавить воспользуйтесь подбором услуг.</div>
                            </div>
                            <table class="form-table inner direction-group-actions" style="display: none;">
                                <tr>
                                    <td class="col-checkbox">
                                        <div class="checkbox">
                                            <?php $uid = uniqid(); ?>
                                            <input id="<?= $uid; ?>" type="checkbox" class="check-all-handler"/>
                                            <label for="<?= $uid; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="col-status"></td>
                                    <td class="col-data">
                                        <div class="action-group clearfix">
                                            <a href="#" class="action action-move"><span class="action-icon-move mr5"></span>Назначить</a>
                                            <a href="#" class="action action-delete"><span class="action-icon-delete mr5"></span>Удалить</a>
                                            <a href="#" class="action action-payment"><span class="action-icon-payment mr5"></span>Оплатить</a>
                                        </div>
                                    </td>
                                    <td class="col-money"></td>
                                </tr>
                            </table>
                            <div class="scroll-ctr">
                                <table class="form-table inner direction-list">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4 form-right">
                    <div class="price-picker">
                        <h2 class="pl10 pr10">Подбор услуг</h2>

                        <div class="ml10 mt15 mr10">
                            <input type="text" id="price-search-handler" class="form-control" placeholder="Поиск услуг"/>
                        </div>

                        <div class="scroll-ctr">
                            <div class="right-list" id="price-search-results">

                            </div>
                            <div class="mt15 master-catalog-price">
                                <?php
                                foreach ($priceGroups as $group) {
                                    $uid = uniqid();
                                    ?>
                                    <?php if ($group['count']) { ?>
                                        <div class="group collapsed" data-toggle="collapse" data-target="#<?= $uid; ?>">
                                            <?= $group['group']; ?><span class="subheader"><?= $group['count']; ?></span>
                                            <span class="ico-collapse"></span>
                                        </div>
                                        <div class="collapse group-items" id="<?= $uid; ?>" data-id="<?= $group['id']; ?>">
                                            <em>загрузка...</em>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="controls-ctr pl10 pr10">
                            <span class="btn btn-sm btn-primary" id="price-pick-handler"><i class="fa fa-plus mr5"></i>Добавить услуги (<b style="font-weight: 100;">0</b>)</span>
                        </div>
                    </div>

                    <div class="payment-picker" style="display: none;">
                        <h2 class="pl10 pr10">Оплата направлений</h2>

                        <div class="scroll-ctr">
                            <div class="items"></div>
                        </div>

                        <div class="controls-ctr pl10 pr10">
                            <div class="row">
                                <div class="col-xs-8">
                                    <span class="btn btn-sm btn-primary" id="payment-handler">Оплатить</span>
                                    <small class="text-muted ml5 mr5">или</small>
                                    <span class="btn btn-sm btn-default" id="payment-cancel-handler">Отменить</span>
                                </div>
                                <div class="col-xs-4 text-right mt5">
                                    <span class="total-cost"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="loading-panel"></div>
        </div>
    </div>
</div>

<style>
    .master-form .form-left .scroll-ctr,
    .master-form .form-right .price-picker .scroll-ctr {
        overflow: auto;
    }
</style>

<script>

    var xhr,
            xhrDoctor,
            priceData = {}, //позиции прайса
            formStorage = <?= json_encode($formData); ?>, //направления
            patient_id = <?= $patient->id; ?>, //пациент
            paymentIds = [], //выбранные напрв для оплаты
            contracts = <?= json_encode($contractJson); ?>,
            shift = <?= $shift ? 'true' : 'false'; ?>,
            payInterval;

    var master = {
        add: function (entry) {
            formStorage.unshift($.extend({
                count: 1,
                state: 0,
                paid: 0,
                checked: true,
                direction_id: null,
                doctor: null,
                doctor_id: null
            }, entry));
            this.render();
        },
        addFew: function (ids) {
            $.each(ids, function (key, id) {
                if (priceData.hasOwnProperty(id)) {
                    formStorage.unshift($.extend({
                        count: 1,
                        state: 0,
                        paid: 0,
                        checked: true,
                        direction_id: null,
                        doctor: null,
                        doctor_id: null
                    }, priceData[id]));
                }
            });
            this.render();
        },
        remove: function (key) {
            if (formStorage.hasOwnProperty(key)) {
                paymentIds = []; //todo            
                formStorage.splice(key, 1);
                this.render();
            }
        },
        edit: function (key, entry, needRender) {
            $.extend(formStorage[key], entry);
            if (!(typeof needRender != 'undefined' && !needRender)) {
                this.render();
            }
        },
        render: function () {
            $('.checkbox-rows .direction-list').html('');
            $('.checkbox-rows .empty-label').hide();

            var that = this,
                    counter = 0,
                    summ = 0;

            $.each(formStorage, function (k, v) {
                counter++;
                summ += v.count * v.cost;
                $('.checkbox-rows .direction-list').append(that._renderItem(k, v, counter));
            });

            $('.contract-col select').selectpicker();
            if (counter) {
                $('.checkbox-rows .empty-label').hide();
                $('.direction-list, .direction-group-actions').show();
//                $('.form-left .scroll-ctr').jScrollPane({
//                    autoReinitialise: true,
//                    verticalGutter: 0,
//                    hideFocus: true
//                });
            } else {
                $('.checkbox-rows .empty-label').show();
                $('.direction-list, .direction-group-actions').hide();
            }

            var cText = counter;
            if (summ) {
                cText += ' на сумму ' + number_format(summ, 2, ', ', ' ');
            }
            $('.direction-counter').text(cText);

            //боковая панель
            if (paymentIds.length) {
                paymentIds.sort();
                $('.form-right .price-picker').hide();
                var sum = 0;
                var $pPicker = $('.form-right .payment-picker');
                $pPicker.show();
                $('.items', $pPicker).html('');

                $.each(paymentIds, function (k, key) {
                    var v = formStorage[key];
                    sum += v.cost * v.count;
                    $('.items', $pPicker).append('<div class="item clearfix row-no-padding"><div class="col-xs-8">' + v.group + ': ' + v.title + '<div class="action-group action-group-arrow clearfix"><a class="action" href="#"><span class="action-icon-delete"></span></a></div></div><div class="col-xs-4 text-right">' + v.count + ' x ' + number_format(v.cost, 2, ', ', ' ') + '</div></div>');
                });

                $('.total-cost', $pPicker).text(number_format(sum, 2, ', ', ' '));

//                $('.payment-picker .scroll-ctr').jScrollPane({
//                    autoReinitialise: true,
//                    verticalGutter: 0,
//                    hideFocus: true
//                });
            } else {
                $('.form-right .price-picker').show();
                $('.form-right .payment-picker').hide();

//                $('.price-picker .scroll-ctr').jScrollPane({
//                    autoReinitialise: true,
//                    verticalGutter: 0,
//                    hideFocus: true
//                });
            }
        },
        _renderItem: function (k, v, counter) {
            var $tr = $('<tr class="state-' + v.state + '"></tr>');
            if (!v.state) {
                $tr.addClass('disabled-row');
            }
            var uid = uniqid();
            $tr.append('<td class="col-checkbox"><div class="checkbox"><input id="' + uid + '" type="checkbox" value="' + v.id + '"><label for="' + uid + '"></label></div></td>');

            $('.col-checkbox input[type="checkbox"]', $tr).prop('checked', (v.hasOwnProperty('checked') && v.checked));

            $tr.append('<td class="col-status"><i class="fa fa-circle dot-icon direction-state-' + v.state + '"></i></td>');

            if (v.hasOwnProperty('event_id') && v.event_id) {
                $('.col-status', $tr).append('<span class="ico-calend"></span>');
            }

            $tr.append('\n\
                <td class="col-data">\n\
                    <span class="number">' + counter + '</span><div class="service">' + v.group + ': ' + v.title + '</div>\n\
                    <div class="second-row"></div>\n\
                    <div class="third-row">\n\
                        <div class="row">\n\
                            <div class="col-xs-4 count-col"></div>\n\
                            <div class="col-xs-4 cost-col">Стоимость — ' + number_format(v.cost, 2, ', ', ' ') + '</div>\n\
                            <div class="col-xs-4 cost-discount text-right"><div class="discount discount-no">Без скидки</div>\n\
                        </div>\n\
                    </div>\n\
                    <div class="action-group action-group-text row-action-group clearfix"></div>\n\
                </td>\n\
            ');

            if (v.state == 0 || (v.hasOwnProperty('editMode') && v.editMode)) {
                $('.count-col', $tr).html('Количество<div class="inc-input ml5 clearfix"><a class="control" href="#" data-action="minus"><i class="fa fa-minus"></i></a><span class="value">' + v.count + '</span><a class="control" href="#" data-action="plus"><i class="fa fa-plus"></i></a></div>');
            } else {
                $('.count-col', $tr).text('Количество — ' + v.count);
            }

            var $detailRow = $('.col-data .second-row', $tr);
            $detailRow.addClass('second-row-sm');
            $detailRow.append('<div class="row"><div class="col-xs-4 doctor-col"></div><div class="col-xs-4 time-col"></div><div class="col-xs-4 contract-col"></div></div>');
            if (v.state == 0 || (v.hasOwnProperty('editMode') && v.editMode)) {
                $('.doctor-col', $detailRow).append('<div class="dropdown dropdown-search"><a class="dropdown-handler btn btn-select" href="#" data-toggle="dropdown"><span>' + (v.doctor_id ? v.doctor : 'Любой специалист') + '</span></a><ul class="dropdown-menu"><div class="search-ctr"><input type="text" class="form-control input-xs ac-handler" placeholder="поиск"/></div><div class="ac-results"></div></ul></div>');
                if (v.doctor_id) {
                    $('.doctor-col .dropdown-search .dropdown-handler', $detailRow).append('<div class="clear-handler"></div>');
                }
            } else {
                if (v.doctor_id) {
                    $('.doctor-col', $detailRow).append(v.doctor);
                } else {
                    $('.doctor-col', $detailRow).text('Любой специалист');
                }
            }

            if (v.state == 0 || (v.hasOwnProperty('editMode') && v.editMode)) {
                var $select = $('<select class="selectpicker"></select>');
                $.each(contracts, function (k, contract) {
                    if (v.hasOwnProperty('contract_id') && v.contract_id == contract.id) {
                        $select.append('<option value="' + contract.id + '" selected="">' + contract.name + '</option>');
                    } else {
                        $select.append('<option value="' + contract.id + '">' + contract.name + '</option>');
                    }
                });
                $('.contract-col', $detailRow).append($select);
            } else {
                $('.contract-col', $detailRow).text(contracts[v.contract_id].name);
            }

            $tr.append('<td class="col-money"><span class="sum">' + number_format((v.count * v.cost), 2, ', ', ' ') + '</span></td>');

            if (v.paid) {
                $('.col-money', $tr).prepend('<span class="badge badge-success">Оплачено</span>');
                $('.col-money .sum', $tr).addClass('paid');
            }

            if (v.state == 0) {
                $('.col-data .action-group', $tr).append('<a href="#" class="action action-move"><span class="action-icon-move"></span><span class="text-label">Назначить</span></a>');
            }

            if (v.state == 1) {
                if ((v.hasOwnProperty('editMode') && v.editMode)) {
                    $('.col-data .action-group', $tr).append('<a href="#" class="action action-save"><span class="action-icon-save"></span><span class="text-label">Сохранить</span></a>');
                } else {
                    if (!v.paid) {
                        $('.col-data .action-group', $tr).append('<a href="#" class="action action-payment"><span class="action-icon-payment"></span><span class="text-label">Оплатить</span></a>');
                        $('.col-data .action-group', $tr).append('<a href="#" class="action action-edit"><span class="action-icon-edit"></span><span class="text-label">Редактировать</span></a>');
                    }
                }
            }

            if ((v.hasOwnProperty('editMode') && v.editMode)) {
                $('.col-data .action-group', $tr).append('<a href="#" class="action action-cancel"><span class="action-icon-cancel"></span><span class="text-label">Отменить</span></a>');
            } else {
                if (!v.paid) {
                    $('.col-data .action-group', $tr).append('<a href="#" class="action action-delete"><span class="action-icon-delete"></span><span class="text-label">Удалить</span></a>');
                }
            }

            //$('.checkbox-rows .direction-list').append($tr);

            return $tr;
        },
        actionSave: function (keys) {
            var that = this,
                    items = {};
            $.each(keys, function (k, key) {
                var e = formStorage[key];
                if (!(e.hasOwnProperty('editMode') && e.editMode)) {
                    //items.push(formStorage[key]);
                    items[key] = e;
                }
            });

            if (Object.keys(items).length) {
                $.ajax({
                    url: '/direction/save-few',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        items: items,
                        patient_id: patient_id
                    },
                    success: function (resp) {
                        $.each(resp, function (k, entry) {
                            //formStorage[keys[k]] = entry;
                            formStorage[k] = entry;
                        });
                        that.render();
                    }
                });
            }
        },
        actionDelete: function (keys) {
            var that = this,
                    items = [],
                    itemsKeys = [];

            $.each(keys, function (k, key) {
                var e = formStorage[key];
                if (!(e.hasOwnProperty('paid') && e.paid)) {
                    items.push(formStorage[key]);
                    itemsKeys.push(key);
                }
            });

            if (items.length) {
                $.ajax({
                    url: '/direction/cancel-few',
                    type: 'post',
                    data: {
                        type: 'post',
                        items: items,
                        patient_id: patient_id
                    },
                    success: function (resp) {
                        $.each(itemsKeys, function (k, key) {
                            that.remove(key - k);
                        });
                    }
                });
            } else {
                $.each(itemsKeys, function (k, key) {
                    that.remove(key - k);
                });
            }
        },
        actionPayment: function (keys) {
            if (!shift) {
                bootbox.alert('Смена не открыта');
                return false;
            }

            $.each(keys, function (k, key) {
                if (!formStorage[key].paid && formStorage[key].direction_id) {
                    if ($.inArray(key, paymentIds) < 0) {
                        paymentIds.push(key);
                    }
                }
            });
            this.render();
        }
    };

    function searchPrice(term) {
        if (typeof xhr != 'undefined') {
            xhr.abort();
        }
        var $target = $('#price-search-results');
        var $pick = $('#price-pick-handler');

        $target.show().html('<em>поиск...</em>');
        $pick.hide();

        xhr = $.ajax({
            type: 'get',
            url: '/direction/price-search',
            data: {
                term: term
            },
            dataType: 'json',
            success: function (resp) {
                $.extend(priceData, resp);
                if (Object.keys(resp).length) {
                    $target.html('');
                    $.each(resp, function (k, v) {
                        var uid = uniqid();
                        $target.append('<div class="checkbox"><input id="' + uid + '" type="checkbox" name="selected_price[]" value="' + v.id + '"><label for="' + uid + '"><div class="row"><div class="col-xs-8">' + v.title + '</div><div class="col-xs-4 text-right">' + number_format(v.cost, 2, ', ', ' ') + '</div></div></label></div>');
                    });
                    $pick.show();
                } else {
                    $target.html('<em>нет результатов</em>');
                }
            },
            errors: function () {
                $target.html('<em>ошибка</em>');
            }
        });
    }

    function searchDoctor(data, $target) {
        if (typeof xhrDoctor != 'undefined') {
            xhrDoctor.abort();
        }

        $target.html('<em>поиск...</em>');

        xhrDoctor = $.ajax({
            type: 'get',
            url: '/direction/doctor-search',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (Object.keys(resp).length) {
                    $target.html('');
                    $.each(resp, function (k, v) {
                        $target.append('<div class="item" data-id="' + k + '" data-title="' + v.title + '">' + v.title + '</div>');
                    });
                } else {
                    $target.html('<em>нет результатов</em>');
                }
            },
            errors: function () {
                $target.html('<em>ошибка</em>');
            }
        });
    }

    function formHeight() {
        var $ctr = $('.form-ctr');
        $ctr.height($(window).height() - $ctr.offset().top);
        master.render();
    }

    //после успеха устройства
    function paymentCallback() {
        var items = [];
        $.each(paymentIds, function (k, key) {
            items.push(formStorage[key]);
            formStorage[key].paid = 1;
        });

        $.ajax({
            url: '/direction/pay',
            type: 'post',
            data: {
                items: items,
                patient_id: patient_id
            },
            success: function (resp) {
                paymentIds = [];
                $('.loading-panel').hide();
                master.render();
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
        //storageRender();

        $('#w0').on('keyup keypress', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#price-search-handler').on('keyup.ac', function () {
            var v = $.trim($(this).val());
            if (v.length > 2) {
                searchPrice(v, $(this));
            } else {
                $('#price-search-results').show().html('<em>запрос от 3-х символов</em>');
            }
        });

        $(document).on('show.bs.collapse', '.master-catalog-price .group-items', function (e) {
            var $p = $(this);
            if (!$('.checkbox', $p).length) {
                $.ajax({
                    url: '/direction/price-group',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        id: $p.attr('data-id')
                    },
                    success: function (resp) {
                        $.extend(priceData, resp);
                        if (Object.keys(resp).length) {
                            $p.html('');
                            $.each(resp, function (k, v) {
                                var uid = uniqid();
                                $p.append('<div class="checkbox"><input id="' + uid + '" type="checkbox" name="selected_price[]" value="' + v.id + '"><label for="' + uid + '"><div class="row"><div class="col-xs-8">' + v.title + '</div><div class="col-xs-4 text-right">' + number_format(v.cost, 2, ', ', ' ') + '</div></div></label></div>');
                            });
                        } else {
                            $p.html('<em>нет результатов</em>');
                        }
                    }
                })
            }
        });

        $(document).on('change', '.form-right .checkbox input', function (event) {
            var $p = $(this).closest('.checkbox');
            $p.toggleClass('active');

            $('#price-pick-handler b').text($('.form-right .checkbox.active').length);
        });

        $(document).on('click', '.inc-input .control', function () {
            var $p = $(this).parent();
            if ($p.hasClass('disabled')) {
                return false;
            }

            var k = $(this).closest('tr').index();
            var entry = formStorage[k];

            if ($(this).attr('data-action') == 'plus') {
                entry.count++;
            } else {
                entry.count--;
                if (entry.count < 1) {
                    entry.count = 1;
                }
            }
            master.edit(k, entry);
            return false;
        });

        $('#price-pick-handler').on('click', function () {
            var ids = [];
            $('input[name="selected_price[]"]:checked').each(function () {
                ids.push($(this).val());
                $(this).prop('checked', false);
                $(this).closest('.checkbox').removeClass('active');
            });
            $('#price-pick-handler b').text('0');
            master.addFew(ids);
        });

        $(document).on('click', '.direction-list .action-delete', function () {
            master.actionDelete([$(this).closest('tr').index()]);
            return false;
        });

        $(document).on('click', '.direction-list .action-move', function () {
            master.actionSave([$(this).closest('tr').index()]);
            return false;
        });

        $(document).on('click', '.direction-list .action-edit', function () {
            var k = $(this).closest('tr').index();
            var entry = $.extend({}, formStorage[k]);
            entry.editMode = true;
            entry.editData = $.extend({}, entry);
            master.edit(k, entry);
            return false;
        });

        $(document).on('click', '.direction-list .action-cancel', function () {
            var k = $(this).closest('tr').index();
            var entry = $.extend({}, formStorage[k]);
            entry = entry.editData;
            entry.editMode = false;
            master.edit(k, entry);
            return false;
        });

        $(document).on('click', '.direction-list .action-save', function () {
            var k = $(this).closest('tr').index();
            var entry = $.extend({}, formStorage[k]);
            entry.editMode = null;
            master.edit(k, entry, false);
            master.actionSave([k]);
            return false;
        });

        $(document).on('click', '.direction-list .action-payment', function () {
            master.actionPayment([$(this).closest('tr').index()]);
            return false;
        });

        $(document).on('click', '.direction-list .dropdown-search .clear-handler', function (e) {
            var k = $(this).closest('tr').index();
            var entry = $.extend({}, formStorage[k]);
            entry.doctor_id = null;
            entry.doctor = null;
            master.edit(k, entry);
            e.stopPropagation();
            return false;
        });

        $(document).on('click', '.payment-picker .action', function () {
            var $p = $(this).closest('.item');
            paymentIds.splice($p.index(), 1);
            master.render();
            return false;
        });

        $(document).on('change', '.form-left .direction-list .checkbox input', function (event) {
            formStorage[$(this).closest('tr').index()].checked = $(this).is(':checked');
        });

        $('.check-all-handler').on('change', function () {
            $('.direction-list input[type="checkbox"]').prop('checked', $(this).is(':checked')).trigger("change");
        });

        $('.direction-group-actions .action-move').on('click', function () {
            var keys = [];

            $('.direction-list input[type="checkbox"]:checked').each(function () {
                keys.push($(this).closest('tr').index());
            });
            master.actionSave(keys);
            return false;
        });

        $('.direction-group-actions .action-delete').on('click', function () {
            var keys = [];
            $('.direction-list input[type="checkbox"]:checked').each(function () {
                keys.push($(this).closest('tr').index());
            });
            master.actionDelete(keys);
            return false;
        });

        $('.direction-group-actions .action-payment').on('click', function () {
            var keys = [];
            $('.direction-list input[type="checkbox"]:checked').each(function () {
                keys.push($(this).closest('tr').index());
            });
            master.actionPayment(keys);
            return false;
        });

        $('#payment-cancel-handler').on('click', function () {
            paymentIds = [];
            master.render();
        });

        $('#payment-handler').on('click', function () {
            //костыль для продакшена
            return paymentCallback();


            $('.loading-panel').show();
            //код транзакции
            var uid = uniqid();
            //услуги
            var items = [];
            $.each(paymentIds, function (k, key) {
                items.push(formStorage[key]);
            });

            //число запусков
            var loops = 0;

            payInterval = setInterval(function () {
                loops++;
                if (loops >= 12) {
                    clearInterval(payInterval);
                    $('.loading-panel').hide();
                    bootbox.alert('Превышен таймаут ответа от устройства');
                    return;
                }

                $.ajax({
                    url: '/direction/send-device',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: uid,
                        items: items
                    },
                    success: function (resp) {
                        if (resp.status != -1) {
                            clearInterval(payInterval);

                            if (resp.status) {
                                paymentCallback();
                            } else {
                                $('.loading-panel').hide();
                                bootbox.alert('Произошла ошибка на устройстве');
                            }
                        }
                    }
                });

            }, 5000);
        });

        $(document).on('shown.bs.dropdown', '.col-data .dropdown', function () {
            $('input', $(this)).focus().trigger('keyup.ac');
            $('html, body').animate({
                scrollTop: $(this).offset().top
            }, 500);
        });

        $(document).on('click.ac', '.dropdown-search .dropdown-menu', function (e) {
            e.stopPropagation();
        });

        $(document).on('keyup.ac', '.col-data .ac-handler', function () {
            var $el = $('.ac-results', $(this).closest('.dropdown-menu'));
            searchDoctor({
                term: $(this).val()
            }, $el);
        });

        $(document).on('click.ac', '.col-data .ac-results .item', function (e) {
            var k = $(this).closest('tr').index();
            var entry = formStorage[k];
            entry.doctor_id = $(this).attr('data-id');
            entry.doctor = $(this).attr('data-title');
            master.edit(k, entry);

            $('.dropdown-handler', $(this).closest('.dropdown')).dropdown('toggle');
            e.stopPropagation();
        });

        $(document).on('change', '.checkbox-rows select', function () {
            var k = $(this).closest('tr').index();
            var entry = formStorage[k];
            entry.contract_id = $(this).val();
            master.edit(k, entry, false);
            if (entry.direction_id) {
                save([k]);
            }
        });

        $('#direction-contract_id').on('change', function () {
            var v = $(this).val();
            $('.payer-ctr span').text(contracts[v].contractor_id ? contracts[v].contractor : 'Физическое лицо');
        }).trigger('change');

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

    });
</script>

<style>
    .payer-ctr {
        padding-top: 7px;
    }

    .empty-label {
        display: block;
        position: absolute;
        left: 0px;
        right: 0px;
        top: 50%;
        margin-top: -30px;
        text-align: center;
        color: #C4C4C4;
        line-height: 24px;
        font-size: 16px;
    }

    .empty-label .empty-header {
        font-size: 20px;
    }

    .empty-label .empty-text {
        margin-top: 10px;
    }

</style>