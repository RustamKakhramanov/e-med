<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
use yii\widgets\MaskedInput;

$this->title = $model->id ? 'Редактировать пациента' : 'Добавить пациента';

$contactJson = [];
if ($model->id) {
    foreach ($model->contacts as $item) {
        $temp = $item->toArray();
        unset($temp['id']);
        $contactJson[] = $temp;
    }
}

//todo вынести в модель пациента
$contractsJson = [];

$default = $model->defaultContract->toArray();
$default['balance'] = $model->defaultContract->getBalance($model->id);

$contractsJson[] = $default;

if ($model->id) {
    foreach ($model->contracts as $item) {
        $temp = $item->toArray();
        $temp['balance'] = $item->getBalance($model->id);
        $contractsJson[] = $temp;
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <?php if ($model->id) { ?>
            <h1>Пациент</h1>
            <ul class="tabs tabs-large clearfix">
                <li class="active"><a href="<?= Url::to(['patient/edit', 'id' => $model->id]); ?>"><i class="fa fa-user mr5"></i>Профиль</a></li>
                <li class=""><a href="<?= Url::to(['patient/direction', 'id' => $model->id]); ?>"><i class="fa fa-indent mr5"></i>Направления</a></li>
            </ul>
        <?php } else { ?>
            <h1>Добавить пациента</h1>
        <?php }?>

        <?php
        $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'class' => 'patients-add-form pb100',
                'novalidate' => '', //ng
            ],
//                    'fieldConfig' => [
//                        'template' => "{label}\n{input}\n{hint}",
//                    ],
            'validateOnType' => true,
            'enableAjaxValidation' => true,
        ]);
        ?>

        <?= Html::activeInput('hidden', $model, 'data_document'); ?>
        <?= Html::activeInput('hidden', $model, 'data_work'); ?>
        <?= Html::activeInput('hidden', $model, 'data_family'); ?>
        <?= Html::activeInput('hidden', $model, 'data_contact'); ?>


        <h2 data-toggle="collapse" data-target="#unique2">
            Основные данные
            <span class="ico-collapse"></span>
        </h2>

        <div class="row collapse in" id="unique2">
            <table class="form-table inner">
                <tr>
                    <td class="col_upload-photo">
                        <div class="upload-photo <?= $model->sex ? 'man' : 'woman'; ?>"></div>
                    </td>
                    <td colspan="3" rowspan="2" class="hasTable">

                        <table class="form-table">
                            <tr>
                                <td colspan="2" class="hasTable">
                                    <table class="form-table">
                                        <tr>
                                            <td style="width:65%;">
                                                <?= $form->field($model, 'last_name') ?>

                                            </td>
                                            <td>
                                                <?= $form->field($model, 'first_name') ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <?= $form->field($model, 'middle_name') ?>
                                </td>
                            </tr>
                        </table>
                        <div>
                            <table class="form-table inner">
                                <tr>
                                    <td>
                                        <?= $form->field($model, 'iin') ?>
                                    </td>
                                    <td>
                                        <label>Группа здоровья</label>
                                        <select class="selectpicker">
                                        </select>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'area') ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td rowspan="2">
                        <div class="form-group form-group_date">
                            <?= $form->field($model, 'birthday')->widget(Calendar::className(), ['form' => $form]); ?>
                        </div>
                        <div>
                            <label>Группа пациентов</label>
                            <select class="selectpicker">
                            </select>
                        </div>
                    </td>
                    <td rowspan="2">
                        <?= $form->field($model, 'sex')->widget(SexPicker::className(), ['form' => $form]); ?>

                        <div>
                            <?= $form->field($model, 'nationality') ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>


        <h2 class="mt30" data-toggle="collapse" data-target="#unique3">
            Контактная информация
            <span class="ico-collapse"></span>
        </h2>

        <div class="row collapse in data-block" id="unique3" data-type="contact">
            <div class="col-xs-3">
                <div class="add-data-block " data-type="contact">
                    <h3>Добавить контакт</h3>
                    <div class="form-group">
                        <select class="selectpicker auto form-switcher" name="data[contact][type]">
                            <option value="address">Адрес</option>
                            <option value="phone">Телефон</option>
                            <option value="email">Эл. почта</option>
                        </select>
                    </div>

                    <div class="form-part" data-type="address">
                        <div class="form-group">
                            <label>Город</label>
                            <input type="text" class="form-control data-required" name="data[contact][address][city]"/>
                        </div>
                        <div class="form-group">
                            <label>Улица</label>
                            <input type="text" class="form-control data-required"
                                   name="data[contact][address][street]"/>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 form-group">
                                <label>Дом</label>
                                <input type="text" class="form-control data-required"
                                       name="data[contact][address][house]"/>
                            </div>
                            <div class="col-xs-4 form-group">
                                <label>Корпус</label>
                                <input type="text" class="form-control" name="data[contact][address][corp]"/>
                            </div>
                            <div class="col-xs-4 form-group">
                                <label>Квартира</label>
                                <input type="text" class="form-control" name="data[contact][address][room]"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-part" data-type="phone">
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" class="form-control data-required" name="data[contact][phone][phone]"/>
                        </div>
                    </div>

                    <div class="form-part" data-type="email">
                        <div class="form-group">
                            <label>Эл. почта</label>
                            <input type="text" class="form-control data-required" name="data[contact][email][email]"/>
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


        <h2 class="mt30" data-toggle="collapse" data-target="#unique4">
            Документы
            <span class="ico-collapse"></span>
        </h2>

        <div class="row collapse in data-block" id="unique4" data-type="document">
            <div class="col-xs-3">
                <div class="add-data-block">
                    <h3>Добавить документ</h3>
                    <div class="form-group">
                        <select class="selectpicker auto form-switcher" name="data[document][type]">
                            <option value="passport">Паспорт</option>
                        </select>
                    </div>

                    <div class="form-part" data-type="passport">
                        <div class="clearfix">
                            <div class="pull-left form-group" style="width: 50px;">
                                <label>Серия</label>
                                <input type="text" class="form-control data-required"
                                       name="data[document][passport][series]"/>
                            </div>
                            <div class="pull-left ml20 form-group" style="width: 65px;">
                                <label>Номер</label>
                                <input type="text" class="form-control data-required"
                                       name="data[document][passport][number]"/>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-xs-6">
                                <label>Выдан</label>
                                <input type="text" class="form-control data-required"
                                       name="data[document][passport][date_start]"/>
                            </div>
                            <div class="col-xs-6">
                                <label>Действителен</label>
                                <input type="text" class="form-control data-required"
                                       name="data[document][passport][date_end]"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Кем выдан</label>
                            <textarea class="form-control data-required"
                                      name="data[document][passport][issued]"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <input id="uniqid1" type="checkbox" checked="" name="data[document][passport][main]"/>
                                <label for="uniqid1">
                                    Основной документ
                                </label>
                            </div>
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


        <h2 class="mt30" data-toggle="collapse" data-target="#unique5">
            Работа
            <span class="ico-collapse"></span>
        </h2>

        <div class="row collapse in data-block" id="unique5" data-type="work">
            <div class="col-xs-3">
                <div class="add-data-block">
                    <h3>Добавить место</h3>
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" class="form-control data-required" name="data[work][name]"/>
                    </div>
                    <div class="form-group">
                        <label>Отрасль</label>
                        <input type="text" class="form-control" name="data[work][industry]"/>
                    </div>
                    <div class="form-group">
                        <label>Должность</label>
                        <input type="text" class="form-control" name="data[work][post]"/>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 form-group">
                            <label>Занятость</label>
                            <input type="text" class="form-control" name="data[work][employment]"/>
                        </div>
                        <div class="col-xs-6 form-group">
                            <label>Дата</label>
                            <div class="input-group input-datepicker datepicker-years">
                                <input type="text" class="form-control datepicker data-required"
                                       name="data[work][date]"/>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="input-datepicker-ui"></div>
                                </div>
                                <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
                            </div>
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

        <h2 class="mt30" data-toggle="collapse" data-target="#unique6">
            Семья
            <span class="ico-collapse"></span>
        </h2>

        <div class="row collapse in data-block" id="unique6" data-type="family">
            <div class="col-xs-3">
                <div class="add-data-block">
                    <h3>Добавить члена семьи</h3>
                    <div class="form-group">
                        <label>Родственник</label>
                        <input type="text" class="form-control data-required" name="data[family][name]"/>
                    </div>
                    <div class="form-group">
                        <label>Степень родства</label>
                        <input type="text" class="form-control data-required" name="data[family][relation]"/>
                    </div>
                    <div class="btn btn-sm btn-primary submit-handler">Добавить</div>
                </div>
            </div>
            <div class="col-xs-9">
                <div class="result-data-block">

                </div>
            </div>
        </div>
        <?=
        $this->render('_contract', [
            'model' => $model,
            'form' => $form
        ]);
        ?>


        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/patient" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>

    var formStorage = {
        contact: <?= json_encode($contactJson); ?>,
        family: <?= $model->data_family; ?>,
        work: <?= $model->data_work; ?>,
        document: <?= $model->data_document; ?>,
        contract: <?= json_encode($contractsJson); ?>
    };

    function renderContact() {
        $('.data-block[data-type="contact"] .result-data-block').html('');

        var labels = {
            address: 'Адрес',
            phone: 'Телефон',
            email: 'Эл. почта'
        };

        $.each(formStorage.contact, function (k, v) {
            var text = '';
            switch (v.type) {
                case 'address':
                    text = v.city + ', ' + v.street + ', ' + v.house + ', ' + v.corp + ', кв.' + v.room;
                    break;
                case 'phone':
                    text = v.phone;
                    break;
                case 'email':
                    text = v.email;
                    break;
            }

            $('.data-block[data-type="contact"] .result-data-block').append('\n\
                <div class="item" data-key="' + k + '">\n\
                    <h3><span class="type type-' + v.type + '"></span>' + labels[v.type] + '</h3>\n\
                    <div class="mt10">' + text + '</div>\n\
                    <div class="action-group mt10 clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                </div>\n\
            ');
        });
    }

    function renderDocument() {
        $('.data-block[data-type="document"] .result-data-block').html('');

        var labels = {
            passport: 'Паспорт'
        };

        $.each(formStorage.document, function (k, v) {
            $('.data-block[data-type="document"] .result-data-block').append('\n\
                <div class="item" data-key="' + k + '">\n\
                    <h3>Паспорт</h3>\n\
                    <div class="form-group mt10">\n\
                        <label>Серия и номер</label>\n\
                        ' + v.series + ' ' + v.number + '\n\
                    </div>\n\
                    <div class="form-group clearfix">\n\
                        <div class="pull-left" style="width:40%;">\n\
                            <label>Выдан</label>\n\
                            ' + v.date_start + '\n\
                        </div>\n\
                        <div class="pull-left" style="width:60%;">\n\
                            <div class="ml20">\n\
                                <label>Действителен до</label>\n\
                                ' + v.date_end + '\n\
                            </div>\n\
                        </div>\n\
                    </div>\n\
                    <div class="form-group">\n\
                        <label>Кем выдан</label>\n\
                        ' + v.issued + '\n\
                    </div>\n\
                    <div class="action-group mt10 clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                </div>\n\
            ');
        });
    }

    function renderWork() {
        $('.data-block[data-type="work"] .result-data-block').html('');

        $.each(formStorage.work, function (k, v) {

            $('.data-block[data-type="work"] .result-data-block').append('\n\
                <div class="item" data-key="' + k + '">\n\
                    <h3>' + (v.name) + '</h3>\n\
                    <div class="form-group mt10">\n\
                        <label>Отрасль</label>\n\
                        ' + (v.industry ? v.industry : '&ndash;') + '\n\
                    </div>\n\
                    <div class="form-group">\n\
                        <label>Должность</label>\n\
                        ' + (v.post ? v.post : '&ndash;') + '\n\
                    </div>\n\
                    <div class="row">\n\
                        <div class="col-xs-6 form-group">\n\
                            <label>Занятость</label>\n\
                            ' + (v.employment ? v.employment : '&ndash;') + '\n\
                        </div>\n\
                        <div class="col-xs-6 form-group">\n\
                            <label>Дата</label>\n\
                            ' + (v.date ? v.date : '&ndash;') + '\n\
                        </div>\n\
                    </div>\n\
                    <div class="action-group mt10 clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                </div>\n\
            ');
        });
    }

    function renderFamily() {
        $('.data-block[data-type="family"] .result-data-block').html('');

        $.each(formStorage.family, function (k, v) {

            $('.data-block[data-type="family"] .result-data-block').append('\n\
                <div class="item" data-key="' + k + '">\n\
                    <h3>' + (v.name) + '</h3>\n\
                    <div class="form-group mt10">\n\
                        <label>Степень родства</label>\n\
                        ' + v.relation + '\n\
                    </div>\n\
                    <div class="action-group mt10 clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                </div>\n\
            ');
        });
    }

    function renderContract() {
        $('.data-block[data-type="contract"] .result-data-block').html('');

        $.each(formStorage.contract, function (k, v) {
            var start = moment(v.start);
            var end = moment(v.end);

            var $node = $('<div class="item" data-key="' + k + '"></div>');
            $node.append('\n\
                <div class="inner ' + (v.typical ? 'disabled-contract' : '') + '">\n\
                    <h3>' + (v.name) + '</h3>\n\
                    <div class="form-group mt10">\n\
                        ' + (v.typical ? 'Типовой' : 'Нетиповой') + '\n\
                    </div>\n\
                    <div class="form-group">\n\
                        <label>Срок действия</label>\n\
                        ' + start.format('DD.MM.YYYY') + '—' + end.format('DD.MM.YYYY') + '\n\
                    </div>\n\
                    <div class="form-group">\n\
                        <label>Плательщик</label>\n\
                        ' + (v.contractor_id ? v.contractor.name : 'Физическое лицо') + '\n\
                    </div>\n\
                </div>\n\
            ');

            if (v.hasOwnProperty('balance')) {
                $('.inner', $node).append('\n\
                    <div class="form-group">\n\
                        <label>Баланс</label>\n\
                        <span class="' + (v.balance ? 'text-success' : 'text-danger') + '">' + number_format(v.balance, 2, ', ', ' ') + ' ₽</span>\n\
                    </div>\n\
                ');
            }

            if (!v.typical) {
                $('.inner', $node).append('\n\
                    <div class="action-group clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                ');
            }

            $('.data-block[data-type="contract"] .result-data-block').append($node);
        });
    }

    //after change
    function storageSync() {
        $.each(formStorage, function (k, v) {
            $('#patients-data_' + k).val(JSON.stringify(v));
        });
    }

    function storageAdd(type, entry) {
        formStorage[type].push(entry);
        storageSync();
        storageRender(type);
    }

    function storageRemove(type, key) {
        formStorage[type].splice(key, 1);
        storageSync();
        storageRender(type);
    }

    function storageEdit(type, key, entry) {
        $.extend(formStorage[type][key], entry);
        storageSync();
        storageRender(type);
    }

    function storageRender(type) {
        window['render' + type.charAt(0).toUpperCase() + type.substr(1)]();
    }

    function bindWorkDate() {
        $('.datepicker-years .input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2020',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');
            }
        });
    }

    function bindPhoneMask() {
        $('input[name="data[contact][phone][phone]"]').inputmask({
            mask: '+7 (999) 999 9999',
            //autoUnmask: true,
            clearIncomplete: true
        });
    }

    $(document).ready(function () {
        storageSync();

        bindWorkDate();
        bindPhoneMask();

        $.each(formStorage, function (k, v) {
            storageRender(k);
        });

        $('select.form-switcher').on('change', function () {
            var $p = $(this).closest('.add-data-block');
            $('.form-part', $p).hide();
            $('.form-part[data-type="' + $(this).val() + '"]', $p).show();
        }).trigger('change');

        $('.add-data-block .submit-handler').on('click', function () {

            var $p = $(this).closest('.data-block');
            //тип
            var type = $p.attr('data-type'); //contact work family..
            //внутренний селектор типа
            var typeInner = false;
            if ($('.add-data-block select.form-switcher', $p).length) {
                typeInner = $('.add-data-block select.form-switcher', $p).val(); //телефон адрес почта..
            }

            storageRender(type);

            //флаг валидации
            var valid = true;
            if (typeInner) {
                $('.form-part[data-type="' + typeInner + '"] .form-group', $p).removeClass('has-error');
                $('.form-part[data-type="' + typeInner + '"] .data-required', $p).each(function () {
                    if ($.trim($(this).val()) == '') {
                        $(this).closest('.form-group').addClass('has-error');
                        valid = false;
                    }
                });
            } else {
                $('.form-group', $p).removeClass('has-error');
                $('.data-required', $p).each(function () {
                    if ($.trim($(this).val()) == '') {
                        $(this).closest('.form-group').addClass('has-error');
                        valid = false;
                    }
                });
            }

            if (!valid) {
                return false;
            }

            if (typeInner) {
                var entry = $(this).closest('form').serializeObject().data[type][typeInner];
                entry.type = typeInner;
            } else {
                var entry = $(this).closest('form').serializeObject().data[type];
            }
            storageAdd(type, entry);

            $('.add-data-block input[type="text"], .add-data-block textarea', $p).val('');
        });

        $(document).on('click.dl', '.data-block .action-icon-delete', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');

            storageRemove($block.attr('data-type'), $item.attr('data-key'));

            e.preventDefault();
            return false;
        });

        $(document).on('click.dl', '.data-block .action-icon-edit', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = formStorage[$block.attr('data-type')][$item.attr('data-key')];

            var $clone = $('.add-data-block', $block).children().not('h3').clone(false);
            $('.submit-handler', $clone).remove();
            $('.form-group', $clone).removeClass('has-error');

            $('.checkbox', $clone).each(function () {
                var uid = uniqid();
                $('> input', $(this)).attr('id', uid).prop('checked', false);
                $('> label', $(this)).attr('for', uid);
            });

            $item.html($clone);

            $('.input-datepicker .dropdown-menu', $item).html('<div class="input-datepicker-ui"></div>');
            bindWorkDate();
            bindPhoneMask();

            $('.submit-handler', $item).after('\n\
                <span class="btn btn-sm btn-primary update-handler mr5">Сохранить</span>\n\
                <span class="btn btn-sm btn-default cancel-handler">Отмена</span>\n\
            ');

            $('.submit-handler', $item).remove();

            if ($('select.form-switcher', $item).length) {
                $('.form-part', $item).hide();
                $('select.form-switcher', $item).val(data.type);
                $('.form-part[data-type="' + data.type + '"]', $item).show();
            }

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

            e.preventDefault();
            return false;
        });

        $(document).on('click.dl', '.data-block .update-handler', function (e) {

            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = formStorage[$block.attr('data-type')][$item.attr('data-key')];

            //флаг валидации
            var valid = true;
            if (data.hasOwnProperty('type')) {
                $('.form-part[data-type="' + data.type + '"] .form-group', $item).removeClass('has-error');
                $('.form-part[data-type="' + data.type + '"] .data-required', $item).each(function () {
                    if ($.trim($(this).val()) == '') {
                        $(this).closest('.form-group').addClass('has-error');
                        valid = false;
                    }
                });
            } else {
                $('.form-group', $item).removeClass('has-error');
                $('.data-required', $item).each(function () {
                    if ($.trim($(this).val()) == '') {
                        $(this).closest('.form-group').addClass('has-error');
                        valid = false;
                    }
                });
            }

            if (!valid) {
                return false;
            }


            if (data.hasOwnProperty('type')) {
                var entry = $(this).closest('form').serializeObject().data[$block.attr('data-type')][data.type];
            } else {
                var entry = $(this).closest('form').serializeObject().data[$block.attr('data-type')];
            }

            storageEdit($block.attr('data-type'), $item.attr('data-key'), entry);
        });

        $(document).on('click.dl', '.data-block .cancel-handler', function (e) {
            storageRender($(this).closest('.data-block').attr('data-type'));
        });

        $('.sexpicker input[type="radio"]').on('change', function () {
            var $o = $('.patients-add-form .upload-photo');
            $o.removeClass('man woman');

            if (1 * $(this).val()) {
                $o.addClass('man');
            } else {
                $o.addClass('woman');
            }
        });


    });
</script>