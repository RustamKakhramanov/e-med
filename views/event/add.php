<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;

$this->title = $model->creation ? 'Добавить событие' : 'Редактировать событие';
//$this->title = 'Добавить событие';

$relJson = [];
if ($model->id) {
    foreach ($model->eventPrices as $item) {
        $relJson[] = [
            'price_id' => $item->price_id,
            'cost' => $item->price->cost,
            'title' => $item->price->title,
            'count' => $item->count
        ];
    }
}
?>

<div class="row">
    <div class="col-md-12">

        <h1><?= $this->title; ?></h1>

        <?php
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => [
                        'class' => 'event-form pb100',
                        'novalidate' => '', //ng
                    ],
                    'validateOnType' => false,
                    'validateOnBlur' => false,
                    'validateOnChange' => false,
                    'enableAjaxValidation' => true,
        ]);
        ?>
        <?= Html::activeInput('hidden', $model, 'id'); ?>
        <div class="form-group clearfix">
            <ul class="tabs tabs-small tabs-form tabs-checkbox-handler pull-left clearfix">
                <?php foreach ($model::$incomingTypes as $key => $type) { ?>
                    <li class="<?php if ($model->incoming == $key) echo 'active'; ?>"><a href="#" data-toggle="tab" data-value="<?= $key; ?>"><?= $type; ?></a></li>
                <?php }; ?>
                <?= Html::activeInput('hidden', $model, 'incoming'); ?>
            </ul>

            <ul class="tabs tabs-small tabs-form tabs-checkbox-handler pull-left ml20 clearfix">
                <?php foreach ($model::$types as $key => $type) { ?>
                    <li class="<?php if ($model->type == $key) echo 'active'; ?>"><a href="#" data-toggle="tab" data-value="<?= $key; ?>"><?= $type; ?></a></li>
                <?php } ?>
                <?= Html::activeInput('hidden', $model, 'type'); ?>
            </ul>
        </div>

        <div class="clearfix">
            <div class="pull-left" style="width:500px;">
                <?= $form->field($model, 'comment')->textArea(['rows' => 2]); ?>                
            </div>
            <!--div class="pull-left ml20" style="width:150px;">
                <label>Рекламный источник</label>
                <select class="selectpicker auto bs-select-hidden">
                    <option>ТВ реклама</option>
                    <option>Радио реклама</option>
                    <option>Отсутствует</option>
                </select>
            </div-->
        </div>

        <h2 class="patient-info">
            Пациент
            <span class="subheader">
                <?php if ($model->patient) { ?>
                    <?= $model->patient->fio; ?>
                    <?= $model->patient->sex ? 'М' : 'Ж'; ?>
                    <?= date('d.m.Y', strtotime($model->patient->birthday)); ?>
                <?php } else { ?>
                    <не выбран>
                <?php } ?>
            </span>
            <a href="#" class="patient-pick-handler ml5"><span class="ico-edit"></span></a>
        </h2>

        <div class="hidden-form-group">
            <?= $form->field($model, 'patient_id')->hiddenInput()->label(false); ?>
        </div>

        <div class="patients-pick-ctr" style="display: block;">
            <div class="tfinder-input" style="width:500px;">
                <input type="text" class="form-control input-handler" placeholder=""/>
                <div class="tfinder-input-controls">
                    <div class="clear-handler"></div>
                    <div class="modal-handler"></div>
                </div>
                <div class="tfinder-empty"></div>
            </div>
        </div>

        <h2 class="doctor-info mt20">
            Специалист
            <span class="subheader">
                <?php if ($model->doctor_id) { ?>
                    <span class="doc-name"><?= $model->doctor->initials; ?></span>
                    <span class="doc-time ml10"><?= ruDateCase($model->date) . ' в ' . date('H:i', strtotime($model->date)); ?></span>
                <?php } else { ?>
                    <span class="doc-name"><специалист не выбран></span>
                    <span class="doc-time ml10"><время не выбрано></span>
                <?php } ?>
            </span>
            <a href="#" class="doctor-pick-handler ml5"><span class="ico-edit"></span></a>
        </h2>

        <div class="hidden-form-group">
            <?= $form->field($model, 'doctor_id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'date')->hiddenInput()->label(false); ?>
        </div>

        <div class="doctor-pick-ctr" style="display: none;">
            <div style="width:500px;">
                <div class="row">
                    <div class="col-xs-7 doc-pick">
                        <div class="dropdown dropdown-search">
                            <a class="dropdown-handler btn btn-select" href="#" data-toggle="dropdown">
                                <?php if ($model->doctor_id) { ?>
                                    <?= $model->doctor->initials; ?>
                                <?php } else { ?>
                                    Выберите специалиста
                                <?php } ?>
                            </a>
                            <ul class="dropdown-menu">
                                <div class="search-ctr">
                                    <input type="text" class="form-control input-xs ac-handler" placeholder="поиск">
                                </div>
                                <div class="ac-results"></div>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-5 time-pick">
                        <div class="dropdown dropdown-search">
                            <a class="dropdown-handler btn btn-select" href="#" data-toggle="dropdown">
                                <?php if ($model->date) { ?>
                                    <?= ruDateCase($model->date) . ' в ' . date('H:i', strtotime($model->date)); ?>
                                <?php } else { ?>
                                    Выберите время
                                <?php } ?>
                            </a>
                            <ul class="dropdown-menu times-ctr">
                                <?php if ($model->doctor_id) { ?>
                                    <?php foreach ($model->doctor->miniGrid as $day) { ?>
                                        <div class="day">
                                            <div class="title"><?= $day['date']; ?></div>
                                            <div class="times">
                                                <?php foreach ($day['times'] as $time) { ?>
                                                    <span data-time="<?= $time; ?>" data-date="<?= $day['date']; ?>"><?= $time; ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    не выбран специалист
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-picker table-picker-price" data-url="/event/add-price">
            <?php $uid = uniqid(); ?>
            <div class="clearfix">
                <h2 data-toggle="collapse" data-target="#<?= $uid; ?>" class="mt10 pull-left" aria-expanded="true">
                    Услуги
                    <span class="subheader header-counter"></span>
                    <span class="ico-collapse"></span>
                </h2>
                <div class="pull-right mt5">
                    <a href="#" class="btn btn-sm btn-primary add-table-picker-handler"><i class="fa fa-plus mr5"></i>Добавить</a>
                </div>
            </div>
            <?= $form->field($model, 'selected_prices')->hiddenInput()->label(false); ?>
            <div class="collapse in" id="<?= $uid; ?>" aria-expanded="true">
                <table class="data-table table-picker-items price-items">
                    <thead>
                        <tr>
                            <th style="width:30px;"><span>№</span></th>
                            <th><span>Номенклатура</span></th>
                            <th style="width:120px;" class="text-right"><span>Базовая стоимость</span></th>
                            <th style="width:120px;" class="text-right"><span>Количество</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <?php if ($model->creation) { ?>
                <a href="/<?= Yii::$app->controller->id; ?>/cancel/<?= $model->id; ?>?back=<?= urlencode(Yii::$app->request->referrer); ?>" class="btn btn-sm btn-default cancel-handler">Отменить</a>
            <?php } else { ?>
                <a href="<?= Yii::$app->request->referrer; ?>" class="btn btn-sm btn-default cancel-handler">Отмена</a>
            <?php } ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    var creation = <?= (int) $model->creation; ?>;
    var patients = {};
    var selectedPatient;
    var xhr;
    var model_id = <?= (int) $model->id; ?>;
    var xhrDoctor;

    function searchPatient(data) {
        if (typeof xhr != 'undefined') {
            xhr.abort();
        }

        xhr = $.ajax({
            type: 'get',
            url: '/event/patient-search',
            data: {
                term: data
            },
            success: function (msg) {
                patients = {};
                if (msg) {
                    $('.patient-search-result').html(msg);
                } else {
                    $('.patient-search-result').html('<div class="placeholder"><i class="fa fa-warning mr10"></i>Совпадений не найдено</div>');
                }
            }
        });
    }

    var xhrPrice;
    var formStorage = <?= json_encode($relJson); ?>;

    function addProlong() {
        $.ajax({
            url: '/event/add-prolong/' + model_id,
            success: function () {
            }
        });
    }

    function storageSync() {
        $('#event-selected_prices').val(JSON.stringify(formStorage));
    }

    function storageAdd(entry) {
        formStorage.push(entry);
        storageSync();
        $('.price-items-counter').text(formStorage.length);
    }

    function storageRemove(key) {
        formStorage.splice(key, 1);
        storageSync();
        $('.price-items > tbody > tr:eq(' + key + ')').remove();
        $('.price-items-counter').text(formStorage.length);
        $('.price-items > tbody > tr').each(function (k) {
            $('> td:eq(0)', $(this)).text(k + 1);
        });
    }

    function storageEdit(key, entry) {
        $.extend(formStorage[key], entry);
        storageSync();
        storageRenderPartial(key);
    }

    function storageRenderPartial(key) {
        var $tr = $('.price-items > tbody > tr:eq(' + key + ')');
        $tr.html('');
        $tr.append('<td>' + (key + 1) + '</td>');
        $tr.append('<td><div class="dropdown"><a class="dropdown-handler" href="#" data-toggle="dropdown">' + formStorage[key].title + '</a><ul class="dropdown-menu"><div class="search-ctr"><input type="text" class="form-control input-xs ac-handler" placeholder="поиск"/></div><div class="ac-results"></div></ul></div><div class="action-group clearfix"><a href="#" class="action action-delete"><span class="action-icon-delete"></span></a></div></td>');
        $tr.append('<td class="text-center"><div class="inc-input clearfix"><a class="control" href="#" data-action="minus"><i class="fa fa-minus"></i></a><span class="value">' + formStorage[key].count + '</span><a class="control" href="#" data-action="plus"><i class="fa fa-plus"></i></a></div></td>');
        $tr.append('<td class="text-right">' + formStorage[key].cost + '</td>');
        $tr.append('<td class="text-right">' + (formStorage[key].cost * formStorage[key].count).toFixed(2) + '</td>');
    }

    function storageRender() {
        $.each(formStorage, function (k, v) {
            $('.price-items > tbody').append('<tr></tr>');
            storageRenderPartial(k);
        });
    }

    function searchPrice(data, $target) {
        if (typeof xhrPrice != 'undefined') {
            xhrPrice.abort();
        }

        $target.html('<em>поиск...</em>');

        xhrPrice = $.ajax({
            type: 'get',
            url: '/event/price-search',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (Object.keys(resp).length) {
                    $target.html('');
                    $.each(resp, function (k, v) {
                        $target.append('<div class="item" data-id="' + k + '" data-title="' + v.title + '" data-cost="' + v.cost + '">' + v.title + '</div>');
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

    function pickTime() {
        var time = $('#event-date').val();
        if (time) {
            var mom = moment(time);
            $('.doctor-info .doc-time').text(mom.format('DD MMMM YYYY') + ' в ' + mom.format('HH:mm'));
            $('.time-pick .dropdown-handler').text(mom.format('DD MMMM YYYY') + ' в ' + mom.format('HH:mm'));
        } else {
            $('.doctor-info .doc-time').text('<время не выбрано>');
            $('.time-pick .dropdown-handler').text('Выберите время');
        }
    }

    function pickDoctor(name) {
        $('.doctor-info .subheader .doc-name').text(name);
        $('.doc-pick .dropdown-handler').text(name);
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

    function patientQuickPick(data) {
        pickPatient(data);
    }

    function pickPatient(data) {
        $('.patient-info .subheader').html(data.fio + ' ' + (data.sex ? 'М' : 'Ж') + ' ' + data.birthday);
        $('#event-patient_id').val(data.id);
        $('.patients-pick-ctr').hide();
    }

    $(document).ready(function () {

        storageRender();

        $('#patient-search-input').on('keyup change', function () {
            var v = $.trim($(this).val());
            if (v) {
                searchPatient(v);
            } else {
                $('.patient-search-result').html('');
            }
        });

        $(document).on('click', '.patient-search-result tr', function () {
            selectedPatient = patients[$(this).attr('data-key')];
            //$('#patient-search-input').val('');
            $('.patient-search-result').html('');
            $('.patients-pick-ctr').hide();
            pickPatient();
        });

        $('.patient-pick-handler').on('click', function () {
            $('.patients-pick-ctr').toggle();
            return false;
        });

        $('.add-patient-submit').on('click', function () {

            $('.patient-create-ctr .form-group').removeClass('has-error');

            var data = $(this).closest('form').serializeObject().EventNewPatient;
            $.ajax({
                url: '/event/new-patient',
                type: 'post',
                data: {
                    EventNewPatient: data
                },
                success: function (resp) {
                    if (Object.keys(resp.errors).length) {
                        $.each(resp.errors, function (k, v) {
                            var $g = $('#' + k).parents('.form-group').last();
                            $g.addClass('has-error');
                            $('.help-block', $g).html(v);
                        });
                    } else {
                        selectedPatient = resp.result;
                        $('.patients-pick-ctr').hide();
                        pickPatient();
                    }
                }
            });
        });

        $('.doctor-pick-handler').on('click', function () {
            $('.doctor-pick-ctr').toggle();
            return false;
        });

        $(document).on('shown.bs.dropdown', '.doctor-pick-ctr .dropdown-search', function () {
            $('input', $(this)).focus().trigger('keyup.ac');
        });

        $(document).on('click.ac', '.doctor-pick-ctr .dropdown-search .dropdown-menu', function (e) {
            e.stopPropagation();
        });

        $(document).on('keyup.ac', '.doctor-pick-ctr .dropdown-search .ac-handler', function () {
            var $el = $('.ac-results', $(this).closest('.dropdown-menu'));
            searchDoctor({
                term: $(this).val()
            }, $el);
        });

        $(document).on('click.ac', '.doctor-pick-ctr .dropdown-search .ac-results .item', function (e) {

            var id = $(this).attr('data-id');
            var oldId = $('#event-doctor_id').val();

            $('#event-doctor_id').val(id);
            pickDoctor($(this).attr('data-title'));

            if (id != oldId) {
                $('#event-date').val('');
                $('.time-pick .dropdown-handler').text('Выберите время');
                pickTime();
                $.ajax({
                    url: '/event/doctor-mini-grid/' + id,
                    type: 'get',
                    dataType: 'json',
                    success: function (resp) {
                        var $ctr = $('.time-pick .times-ctr');
                        $ctr.html('');
                        $.each(resp, function (k, day) {
                            var $day = $('<div class="day"></div>');
                            $day.append('<div class="title">' + day.date + '</div>');
                            $day.append('<div class="times"></div>');
                            $.each(day.times, function (kTime, time) {
                                $('.times', $day).append('<span data-time="' + time + '" data-date="' + day.date + '">' + time + '</span>');
                            });
                            $ctr.append($day);
                        });
                    }
                });

            }

            $('.dropdown-handler', $(this).closest('.dropdown')).dropdown('toggle');
            e.stopPropagation();
        });

        $(document).on('click', '.time-pick .times-ctr .times span', function () {
            $('#event-date').val($(this).attr('data-date') + ' ' + $(this).attr('data-time') + ':00');
            pickTime();
            $('.dropdown-handler', $(this).closest('.dropdown')).dropdown('toggle');
            $('.doctor-pick-ctr').hide();
        });

        //чекбоксы в виде табов
        $('.tabs-checkbox-handler a').on('click', function () {
            $('input[type="hidden"]', $(this).closest('.tabs-checkbox-handler')).val($(this).attr('data-value'));
        });

        setTimeout(addProlong, 30000);

        //прайс
        $('.table-picker').tpicker({
            storage: formStorage,
            sync: function () {
                $('#event-selected_prices').val(JSON.stringify(formStorage));
            },
            renderView: function ($row, model) {
                $('.td-price-name .display-ctr', $row).text(model.title);
                $('.td-cost', $row).text(model.cost);
                $('.td-count .display-ctr', $row).text(model.count);
            },
            renderEdit: function ($row, model) {
                if (model.hasOwnProperty('title')) {
                    $('.ac-picker-input', $row).val(model.title);
                }

                if (model.hasOwnProperty('cost')) {
                    $('.td-cost', $row).text(model.cost);
                }

                if (!model.hasOwnProperty('count')) {
                    model.count = 1;
                }

                $('.td-count .inc-input .value', $row).text(model.count);

                var settings = this.tpicker.settings;
                $('.ac-picker-input', $row).autocomplete({
                    source: settings.source + '-ac',
                    minLength: 1,
                    appendTo: $('.ac-picker-input', $row).parent(),
                    response: function (event, ui) {
                    },
                    change: function (event, ui) {
                    },
                    select: function (event, ui) {
                        $(this).attr('data-changed', 0);
                        //запись в компонент
                        console.log(model);
                        $(this).closest('.table-picker').tpicker('pick', {
                            title: ui.item.title,
                            cost: ui.item.cost,
                            id: ui.item.id,
                            count: model.count
                        });
                    }
                }).autocomplete('instance')._renderItem = function (ul, item) {
                    return $('<li>')
                            .attr('data-value', item.id)
                            .attr('data-cost', item.cost)
                            .append(item.title)
                            .appendTo(ul);
                };

                $('.ac-picker-input', $row).on('keydown', function (event) {
                    if (!event.keyCode == 13) {
                        $(this).attr('data-changed', 1);
                    }
                });

                $('.ac-picker-input', $row).on('blur', function () {
                    if (1 * $(this).attr('data-changed')) {
                        $(this).val('');
                    }
                });

                $('.inc-input .control', $row).on('click', function () {

                    if ($(this).attr('data-action') == 'plus') {
                        model.count++;
                    } else {
                        model.count--;
                        if (model.count < 1) {
                            model.count = 1;
                        }
                    }

                    $('.value', $(this).closest('.inc-input')).text(model.count);

                    $(this).closest('.table-picker').tpicker('update', {
                        count: model.count
                    });

                    return false;
                });
            }
        });

        $('#w0').on('beforeValidate', function (event, messages, deferreds) {
            $('.patient-create-ctr .form-group').removeClass('has-error');
            $('.patient-create-ctr input').each(function () {
                if ($(this).attr('id')) {
                    $('#w0').yiiActiveForm('remove', $(this).attr('id'));
                }
            });
            return true;
        });

//        $('#patient-search-input').on('click', function(){
//            openModal({
//                html: '<h1>dsfsfsdf</h1>',
//                effect: 'slide-bottom'
//            });
//        });

        $('.tfinder-input .input-handler').autocomplete({
            //source: ,
            minLength: 1,
            appendTo: $('.tfinder-input'),
            source: function (request, response) {
                var $p = $(this.element).parent();
                console.log($p);
                $.ajax({
                    url: '/event/patient-search-ac',
                    data: {
                        term: request.term
                    },
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.length == 0) {
                            if (!$('.tfinder-empty', $p).length) {
                                $p.append('<div class="tfinder-empty"></div>');
                            }
                            $('.tfinder-empty', $p).html('Создать <span>' + request.term + '</span>').attr('data-term', request.term).show();
                        } else {
                            $('.tfinder-empty', $p).hide();
                        }

                        response(resp);
                    }
                });
            },
            select: function (event, ui) {
                pickPatient({
                    id: ui.item.id,
                    fio: ui.item.value,
                    sex: ui.item.sex,
                    birthday: ui.item.birthday
                });
            }
        }).autocomplete('instance')._renderItem = function (ul, item) {
            ul.addClass('tfinder-patients')
            var $li = $('<li></li>');
            $li.attr('data-value', item.id);
            $li.append('<table><tr><td class="tfinder_col_name">'+item.value+'</td><td class="tfinder_col_birthday">'+item.birthday+'</td></tr></table>');
            return $li.appendTo(ul);
        };

        $('.tfinder-input .tfinder-empty').on('click', function () {
            $(this).hide();
            var $tfinder = $(this).closest('.tfinder-input');
            openModal({
                centered: true,
                url: '/event/patient-add?term=' + encodeURIComponent($(this).attr('data-term')),
                onClose: function () {
                    $('.input-handler', $tfinder).trigger('keydown');
                }
            });
        });

        $('.tfinder-input .modal-handler').on('click', function () {
            openModal({
                centered: false,
                url: '/event/patient-search'
            });
        });



    });
</script>

<style>
    
    .modal-event-create .modal-inner {
        width: 900px;
        padding: 0px 20px;
    }
    
    .modal-event-create .form-end {
        left: 0px;
    }
    
    .modal-event-create  .dropdown-search .dropdown-menu {
        margin-bottom: 0px;
    }
    
    .hidden-form-group .form-group {
        margin: 0px;
    }

    .patient-selected {
        display: inline-block;
        background: #f9fafb;
        border: 1px #eceff3 solid;
        padding: 10px 15px;
        margin-bottom: 20px;
        width: 250px;
    }

    .event-form .form-group .help-block {
        bottom: -12px;
    }

    .patient-search-result {
        background: #416586;
        padding: 0px;
        margin-top: 3px;
    }

    .patient-search-result .placeholder {
        color: #fff;
        padding: 6px 10px;
    }

    .patient-search-result .data-table td {
        color: #fff;
        padding: 6px 0px;
        cursor: pointer;
    }

    .patient-search-result .data-table .col-name {
        padding-left: 10px;
    }

    .patient-search-result .data-table .col-sex {
        width: 30px;
        text-align: center;
    }

    .patient-search-result .data-table .col-birthday {
        width: 90px;
        text-align: right;
        padding-right: 10px;
    }

    .patient-search-result .data-table tr:hover td {
        background: #2d4154;
    }

    .patient-create-ctr .form-group .help-block {
        bottom: -23px;
    }

    .dropdown-menu.times-ctr {
        color: #333;
        padding: 10px;
    }

    .dropdown-menu.times-ctr .day {
        margin-top: 7px;
    }

    .dropdown-menu.times-ctr .day:first-child {
        margin-top: 0px;
    }

    .dropdown-menu.times-ctr .day .title {
        font-size: 14px;
        color: #aaadb0;
        margin: 0px 0px 5px 3px;
    }

    .dropdown-menu.times-ctr .day .times span {
        font-size: 13px;
        display: inline-block;
        border-bottom: 1px dashed;
        margin: 0px 4px 5px 4px;
        cursor: pointer;
    }

    .created .action-group {
        display: none !important;
    }

    .created .price-items .dropdown-handler {
        border-bottom: none;
    }

    .created .inc-input .control {
        display: none;
    }
    
    .tfinder-patients {
        
    }
    
    .tfinder-patients.ui-autocomplete.ui-menu .ui-menu-item {
        display: block;
    }
    .tfinder-patients table {
        width: 100%;
        table-layout: fixed;
    }
    .tfinder-patients .tfinder_col_birthday {
        width: 100px;
        text-align: right;
    }
</style>