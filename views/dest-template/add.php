<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = $model->id ? 'Редактировать шаблон стандартов лечения' : 'Добавить шаблон стандартов лечения';

$priceJson = [];
if ($model->id) {
    foreach ($model->prices as $price) {
        $priceJson[] = [
            'id' => $price->id,
            'cost' => $price->cost,
            'title' => $price->title
        ];
    }
}

$specJson = [];
if ($model->id) {
    foreach ($model->specs as $spec) {
        $specJson[] = [
            'id' => $spec->id,
            'name' => $spec->name
        ];
    }
}

$diagnonsesJson = [];
if ($model->id) {
    foreach ($model->diagnoses as $item) {
        $diagnonsesJson[] = [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name
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
                        'class' => 'pb100',
                        'novalidate' => '', //ng
                    ],
                    'validateOnType' => true,
                    'enableAjaxValidation' => true,
        ]);
        ?>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'name') ?>
            </div>
        </div>

        <div class="specs-ctr">
            <?php $uid = uniqid(); ?>
            <div class="clearfix">
                <h2 data-toggle="collapse" data-target="#<?= $uid; ?>" class="mt10 pull-left" aria-expanded="true">
                    Ограничения по специализациям
                    <span class="subheader specs-count">0</span>
                    <span class="ico-collapse"></span>
                </h2>
            </div>
            <?= $form->field($model, 'allowed_spec')->hiddenInput()->label(false); ?>
            <div class="collapse in pb20" id="<?= $uid; ?>" aria-expanded="true">
                <div class="row collapse in data-block" id="<?= $uid; ?>">
                    <div class="col-xs-3">
                        <div class="add-data-block">
                            <h3>Добавить</h3>
                            <div class="form-group">
                                <?=
                                Html::dropDownList('data[speciality_id]', null, ArrayHelper::map($specs, 'id', 'name'), [
                                    'class' => 'selectpicker'
                                ]);
                                ?>
                            </div>
                            <div class="btn btn-sm btn-primary submit-handler">Добавить</div>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="result-data-block"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="diagnosis-ctr">
            <?php $uid = uniqid(); ?>
            <div class="clearfix">
                <h2 data-toggle="collapse" data-target="#<?= $uid; ?>" class="mt10 pull-left" aria-expanded="true">
                    Ограничения по диагнозам
                    <span class="subheader diagnosis-count">0</span>
                    <span class="ico-collapse"></span>
                </h2>
            </div>
            <?= $form->field($model, 'allowed_diagnosis')->hiddenInput()->label(false); ?>

            <div class="collapse in pb20" id="<?= $uid; ?>" aria-expanded="true">
                <div class="row collapse in data-block" id="<?= $uid; ?>">
                    <div class="col-xs-3">
                        <div class="add-data-block">
                            <h3>Добавить</h3>
                            <div class="form-group">
                                <div class="dropdown dropdown-search">
                                    <a class="dropdown-handler btn btn-select" href="#" data-toggle="dropdown">
                                        &nbsp;
                                    </a>
                                    <ul class="dropdown-menu">
                                        <div class="search-ctr">
                                            <input type="text" class="form-control input-xs ac-handler" placeholder="поиск">
                                        </div>
                                        <div class="ac-results"></div>
                                    </ul>
                                </div>
                            </div>
                            <div class="btn btn-sm btn-primary submit-handler">Добавить</div>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="result-data-block">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-picker table-picker-price" data-url="/dest-template/add-price">
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
            <?= $form->field($model, 'selected_price')->hiddenInput()->label(false); ?>
            <div class="collapse in" id="<?= $uid; ?>" aria-expanded="true">
                <table class="data-table table-picker-items price-items">
                    <thead>
                        <tr>
                            <th style="width:30px;"><span>№</span></th>
                            <th><span>Номенклатура</span></th>
                            <th style="width:120px;" class="text-right"><span>Базовая стоимость</span></th>
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
            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id); ?>" class="btn btn-sm btn-default">Отменить</a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
    .diagnosis-ctr .add-data-block .dropdown-search .dropdown-menu {
        min-width: 500px;
    }
</style>

<script>
    var
            xhr,
            formStorage = <?= json_encode($priceJson); ?>,
            xhrDiagnosis,
            diagnosesResp,
            diagnosisSelected,
            diagnosisEdited,
            diagnosisStorage = <?= json_encode($diagnonsesJson); ?>,
            specsStorage = <?= json_encode($specJson); ?>,
            specsEdited;
    
    function searchDiagnosis(data, $target) {
        $('.diagnosis-ctr .data-block').height('auto');
        if (typeof xhrDiagnosis != 'undefined') {
            xhrDiagnosis.abort();
        }
        
        $target.html('<em>поиск...</em>');
        xhrDiagnosis = $.ajax({
            type: 'get',
            url: '/reception/diagnosis-search',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (Object.keys(resp).length) {
                    $target.html('');
                    $.each(resp, function (k, v) {
                        $target.append('<div class="item" data-id="' + k + '"><strong>' + v.code + '</strong> ' + v.name + '</div>');
                    });
                    diagnosesResp = resp;
                } else {
                    $target.html('<em>нет результатов</em>');
                }
            },
            errors: function () {
                $target.html('<em>ошибка</em>');
            }
        });
    }
    
    function diagnosisAdd(entry) {
        diagnosisStorage.push(entry);
        diagnosisSync();
        diagnosisRender();
    }
    
    function diagnosisRemove(key) {
        diagnosisStorage.splice(key, 1);
        diagnosisSync();
        diagnosisRender();
    }
    
    function diagnosisEdit(key, entry) {
        $.extend(diagnosisStorage[key], entry);
        diagnosisSync();
        diagnosisRender();
    }
    
    function diagnosisSync() {
        $('#desttemplate-allowed_diagnosis').val(JSON.stringify(diagnosisStorage));
    }
    
    function diagnosisRender() {
        $('.diagnosis-ctr .data-block .result-data-block').html('');
        $('.diagnosis-ctr .diagnosis-count').text(diagnosisStorage.length);
        $.each(diagnosisStorage, function (k, v) {
            $('.diagnosis-ctr .data-block .result-data-block').append('\n\
                    <div class="item" data-key="' + k + '">\n\
                        <h3>' + v.code + (v.main ? '<span class="badge badge-info ml10">Основной</span>' : '') + '</h3>\n\
                        <div class="form-group mt10">\n\
                            ' + v.name + '\n\
                        </div>\n\
                        <div class="action-group mt10 clearfix">\n\
                            <a href="#" class="action action-edit"><span class="action-icon-edit"></span></a>\n\
                            <a href="#" class="action action-delete"><span class="action-icon-delete"></span></a>\n\
                        </div>\n\
                    </div>\n\
        ');
        });
    }
    
    //specs
    function specsAdd(entry) {
        specsStorage.push(entry);
        specsSync();
        specsRender();
    }
    
    function specsRemove(key) {
        specsStorage.splice(key, 1);
        specsSync();
        specsRender();
    }
    
    function specsEdit(key, entry) {
        $.extend(specsStorage[key], entry);
        specsSync();
        specsRender();
    }
    
    function specsSync() {
        $('#desttemplate-allowed_spec').val(JSON.stringify(specsStorage));
    }
    
    function specsRender() {
        $('select[name="data[speciality_id]"] option').prop('disabled', false);
        $('.specs-ctr .data-block .result-data-block').html('');
        $('.specs-ctr .specs-count').text(specsStorage.length);
        $.each(specsStorage, function (k, v) {
            $('select[name="data[speciality_id]"] option[value="' + v.id + '"]').prop('disabled', true);
            $('.specs-ctr .data-block .result-data-block').append('\n\
                    <div class="item" data-key="' + k + '">\n\
                        <h3>' + v.name + '</h3>\n\
                        <div class="action-group mt10 clearfix">\n\
                            <a href="#" class="action action-edit"><span class="action-icon-edit"></span></a>\n\
                            <a href="#" class="action action-delete"><span class="action-icon-delete"></span></a>\n\
                        </div>\n\
                    </div>\n\
            ');
        });
        $('select[name="data[speciality_id]"]').selectpicker('refresh');
    }
    
    $(document).ready(function () {
        
        specsRender();
        specsSync();
        diagnosisRender();
        diagnosisSync();
        
        //диагнозы
        $(document).on('shown.bs.dropdown', '.diagnosis-ctr .dropdown-search', function () {
            $('input', $(this)).focus().trigger('keyup.ac');
        });
        
        $(document).on('hidden.bs.dropdown', '.diagnosis-ctr .dropdown-search', function () {
            $('.diagnosis-ctr .data-block').height('auto');
        });
        
        $(document).on('click.ac', '.diagnosis-ctr .dropdown-search .dropdown-menu', function (e) {
            e.stopPropagation();
        });
        
        $(document).on('keyup.ac', '.diagnosis-ctr .dropdown-search .ac-handler', function () {
            var $el = $('.ac-results', $(this).closest('.dropdown-menu'));
            searchDiagnosis({
                term: $(this).val()
            }, $el);
        });
        
        $(document).on('click.ac', '.diagnosis-ctr .add-data-block .dropdown-search .ac-results .item', function (e) {
            diagnosesSelected = diagnosesResp[$(this).attr('data-id')];
            $('.dropdown-handler', $(this).closest('.dropdown')).html('<strong>' + diagnosesSelected.code + '</strong> ' + diagnosesSelected.name + '</strong>')
            $('.dropdown-handler', $(this).closest('.dropdown')).dropdown('toggle');
            e.stopPropagation();
        });
        
        $('.diagnosis-ctr .add-data-block .submit-handler').on('click', function () {
            if (diagnosesSelected) {
                diagnosisAdd($.extend(
                        {},
                        diagnosesSelected
                        ));
                diagnosesSelected = null;
                $('#checkbox-data-main').prop('checked', false);
                $('.diagnosis-ctr .dropdown-handler').html('&nbsp;');
            }
        });
        
        $(document).on('click.dl', '.diagnosis-ctr .data-block .action-delete', function (e) {
            var $item = $(this).closest('.item');
            diagnosisRemove($item.attr('data-key'));
            e.preventDefault();
            return false;
        });
        
        $(document).on('click.dl', '.diagnosis-ctr .data-block .action-edit', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = diagnosisStorage[$item.attr('data-key')];
            specsEdited = data;
            
            if ($('.item .update-handler', $block).length) {
                diagnosisRender();
                $('.item[name="' + $item.attr('data-key') + '"]', $block).trigger('click');
            }
            
            var $clone = $('.add-data-block', $block).children().not('h3').not('.field-reception-data_diagnoses').clone(false);
            $('.submit-handler', $clone).remove();
            $('.form-group', $clone).removeClass('has-error');
            
            $('.checkbox', $clone).each(function () {
                var uid = uniqid();
                $('> input', $(this)).attr('id', uid).prop('checked', false);
                $('> label', $(this)).attr('for', uid);
            });
            
            $item.html($clone);
            
            $('.dropdown-search .dropdown-handler', $item).html('<strong>' + data.code + '</strong> ' + data.name);
            
            $('.submit-handler', $item).after('\n\
                    <span class="btn btn-sm btn-primary update-handler mr5">Сохранить</span>\n\
                    <span class="btn btn-sm btn-default cancel-handler">Отмена</span>\n\
                ');
            
            $('.submit-handler', $item).remove();
            $('.bootstrap-select', $item).remove();
            $('select.selectpicker', $item).selectpicker();
            
            $('input[type="checkbox"]', $item).each(function () {
                var prop = $(this).attr('name').match(/\[([a-z_]+){1}\]$/)[1];
                if (data.hasOwnProperty(prop) && data[prop]) {
                    $(this).prop('checked', true);
                }
            });
            
            e.preventDefault();
            return false;
        });
        
        $(document).on('click.dl', '.diagnosis-ctr .data-block .cancel-handler', function (e) {
            diagnosisEdited = null;
            diagnosisRender();
        });
        
        $(document).on('click.dl', '.diagnosis-ctr .data-block .update-handler', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            
            if (diagnosisEdited) {
                var main = $('input[name="diagnosis[main]"]').is(':checked');
                var entry = $.extend(diagnosisEdited, {main: main});
                diagnosisEdit($item.attr('data-key'), entry);
            }
        });
        
        $(document).on('click.ac', '.diagnosis-ctr .result-data-block .dropdown-search .ac-results .item', function (e) {
            $.extend(diagnosisEdited, diagnosesResp[$(this).attr('data-id')]);
            $('.dropdown-handler', $(this).closest('.dropdown')).html('<strong>' + diagnosisEdited.code + '</strong> ' + diagnosisEdited.name + '</strong>')
            $('.dropdown-handler', $(this).closest('.dropdown')).dropdown('toggle');
            e.stopPropagation();
        });
        
        //специалзации
        $('.specs-ctr .add-data-block .submit-handler').on('click', function () {
            if (!$('select[name="data[speciality_id]"] option:selected').prop('disabled')) {
                var id = $('select[name="data[speciality_id]"]').val();
                var text = $('select[name="data[speciality_id]"] option:selected').text();
                
                specsAdd({
                    id: id,
                    name: text
                });
            }
        });
        
        $(document).on('click.dl', '.specs-ctr .data-block .action-delete', function (e) {
            var $item = $(this).closest('.item');
            specsRemove($item.attr('data-key'));
            e.preventDefault();
            return false;
        });
        
        $(document).on('click.dl', '.specs-ctr .data-block .action-edit', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = specsStorage[$item.attr('data-key')];
            specsEdited = data;
            
            if ($('.item .update-handler', $block).length) {
                specsRender();
                $('.item[name="' + $item.attr('data-key') + '"]', $block).trigger('click');
            }
            
            var $clone = $('.add-data-block', $block).children().not('h3').not('.field-reception-data_diagnoses').clone(false);
            $('.submit-handler', $clone).remove();
            $('.form-group', $clone).removeClass('has-error');
            
            $item.html($clone);
            
            $('.submit-handler', $item).after('\n\
                    <span class="btn btn-sm btn-primary update-handler mr5">Сохранить</span>\n\
                    <span class="btn btn-sm btn-default cancel-handler">Отмена</span>\n\
                ');
            
            $('.submit-handler', $item).remove();
            $('.bootstrap-select', $item).remove();
            $('select', $item).val(data.id);
            $('select option[value="' + data.id + '"]').prop('disabled', false);
            $('select.selectpicker', $item).selectpicker();
            
            $('input[type="checkbox"]', $item).each(function () {
                var prop = $(this).attr('name').match(/\[([a-z_]+){1}\]$/)[1];
                if (data.hasOwnProperty(prop) && data[prop]) {
                    $(this).prop('checked', true);
                }
            });
            
            e.preventDefault();
            return false;
        });
        
        $(document).on('click.dl', '.specs-ctr .data-block .cancel-handler', function (e) {
            specsEdited = null;
            specsRender();
        });
        
        $(document).on('click.dl', '.specs-ctr .data-block .update-handler', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            
            var id = $('select', $item).val();
            var text = $('select option:selected', $item).text();
            
            if (specsEdited) {
                specsEdit($item.attr('data-key'), {
                    id: id,
                    name: text
                });
            }
        });
        
        //прайс
        $('.table-picker').tpicker({
            storage: formStorage,
            sync: function () {
                $('#desttemplate-selected_price').val(JSON.stringify(formStorage));
            },
            renderView: function ($row, model) {
                $('.td-price-name .display-ctr', $row).text(model.title);
                $('.td-cost', $row).text(model.cost);
            },
            renderEdit: function ($row, model) {
                if (model.hasOwnProperty('title')) {
                    $('.ac-picker-input', $row).val(model.title);
                }
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
                        $(this).closest('.table-picker').tpicker('pick', {
                            title: ui.item.title,
                            cost: ui.item.cost,
                            id: ui.item.id
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
            }
        });
        
//        $('.table-picker .add-table-picker-handler').on('click', function () {
//            var $com = $(this).closest('.table-picker');
//            console.log($com.tpicker('update'));
//            var url = $com.attr('data-url');
//            $.ajax({
//                url: url,
//                type: 'get',
//                dataType: 'html',
//                success: function (html) {
//                    $('.table-picker-items tbody', $com).append(html);
//                    $('.table-picker-items tbody tr:last .action-edit', $com).trigger('click');
//                    recalcCounter();
//                }
//            });
//
//            return false;
//        });
        
    });
</script>