<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
use yii\helpers\ArrayHelper;

$this->title = $model->id ? 'Редактировать специалиста' : 'Добавить специалиста';

$specJson = [];
if ($model->id) {
    foreach ($model->doctorSpecialities as $rel) {
        $specJson[] = [
            'speciality_id' => $rel->speciality_id,
            'duration' => $rel->duration,
            'main' => (int) $rel->main,
            'show_schedule' => (int) $rel->show_schedule
        ];
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <ul class="tabs tabs-large clearfix doctors-top-tabs">
            <li class="active"><a href="#profile" data-toggle="tab">Профиль</a></li>
            <li class=""><a href="#schedule" data-toggle="tab">График работы</a></li>
            <li class=""><a href="#price" data-toggle="tab">Услуги</a></li>
        </ul>

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

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">
                <h2 data-toggle="collapse" data-target="#unique2">
                    Основные данные
                    <span class="ico-collapse"></span>
                </h2>

                <div class="row collapse in" id="unique2">
                    <table class="form-table inner">
                        <tr>
                            <td class="col_upload-photo">
                                <div class="upload-photo">
                                    <img src="<?= $model->photoUrl; ?>"/>
                                </div>
                                <div class="hidden">
                                    <?= $form->field($model, 'photo')->hiddenInput()->label(false); ?>
                                    <input type="file" id="for-upload" name="f"/>
                                </div>
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
                                                <?=
                                                $form->field($model, 'subdivision_id')->dropDownList(
                                                        ArrayHelper::map($subdivisions, 'id', 'name'), [
                                                    'class' => 'selectpicker',
                                                    'prompt' => ' '
                                                        ]
                                                );
                                                ?>
                                            </td>
                                            <td>
                                                <div class="checkbox mt10">
                                                    <?=
                                                    Html::activeCheckbox($model, 'fired', [
                                                        'label' => null,
                                                    ])
                                                    ?>
                                                    <label for="doctor-fired"><?= $model->getAttributeLabel('fired') ?></label>
                                                </div>
                                            </td>
                                            <td>

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
                                </div>
                            </td>
                            <td rowspan="2">
                                <?= $form->field($model, 'sex')->widget(SexPicker::className(), ['form' => $form]); ?>

                                <div>

                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <h2 class="mt30" data-toggle="collapse" data-target="#unique6">
                    Специализации
                    <span class="ico-collapse"></span>
                </h2>

                <div class="row collapse in data-block" id="unique6">
                    <div class="col-xs-3">
                        <div class="add-data-block">
                            <h3>Добавить</h3>
                            <?= $form->field($model, 'selected_specs')->hiddenInput()->label(false); ?>
                            <div class="form-group">
                                <?=
                                Html::dropDownList('data[speciality_id]', null, ArrayHelper::map($specialities, 'id', 'name'), [
                                    'class' => 'selectpicker'
                                ]);
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Длительность приёма</label>
                                <?php
                                $durations = [
                                    15 => '15 минут',
                                    20 => '20 минут',
                                    30 => '30 минут',
                                    40 => '40 минут',
                                    45 => '45 минут',
                                    60 => '60 минут',
                                ];
                                echo Html::dropDownList('data[duration]', null, $durations, [
                                    'class' => 'selectpicker'
                                ]);
                                ?>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <?=
                                    Html::checkbox('data[main]', false, [
                                        'label' => null,
                                        'id' => 'checkbox-data-main'
                                    ]);
                                    ?>
                                    <label for="checkbox-data-main">Основная</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <?=
                                    Html::checkbox('data[show_schedule]', false, [
                                        'label' => null,
                                        'id' => 'checkbox-show-schedule'
                                    ]);
                                    ?>
                                    <label for="checkbox-show-schedule">Отображать в сетке расписания</label>
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

            <div role="tabpanel" class="tab-pane" id="schedule">
                <?=
                $this->render('_schedule', [
                    'model' => $model,
                    'form' => $form
                ]);
                ?>
            </div>

            <div role="tabpanel" class="tab-pane" id="price">
                <?=
                $this->render('_price', [
                    'model' => $model,
                    'form' => $form
                ]);
                ?>
            </div>
        </div>


        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="/doctor" class="btn btn-sm btn-default">Отменить</a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
    var formStorage = <?= json_encode($specJson); ?>;
    var specialities = <?= json_encode(ArrayHelper::map($specialities, 'id', 'name')); ?>;
    var doctorId = <?= $model->id ? $model->id : 0; ?>;
    var defaultPhotos = ['<?= \app\models\Doctor::PHOTO_WOMAN; ?>', '<?= \app\models\Doctor::PHOTO_MAN; ?>'];

    function storageSync() {
        $('#doctor-selected_specs').val(JSON.stringify(formStorage));
    }

    function storageAdd(entry) {
        formStorage.push(entry);
        storageSync();
        storageRender();
    }

    function storageRemove(key) {
        formStorage.splice(key, 1);
        storageSync();
        storageRender();
    }

    function storageEdit(key, entry) {
        $.extend(formStorage[key], entry);
        storageSync();
        storageRender();
    }

    function storageRender() {
        $('.data-block .result-data-block').html('');
        $.each(formStorage, function (k, v) {

            var sh = '';
            if (v.hasOwnProperty('show_schedule') && v.show_schedule) {
                sh = '<div class="mt10">Отображается в сетке расписания</div>';
            }

            $('.data-block .result-data-block').append('\n\
                <div class="item" data-key="' + k + '">\n\
                    <h3>' + (specialities[v.speciality_id]) + '</h3>\n\
                    <div class="form-group mt10">\n\
                        Прием по ' + v.duration + ' мин.\n\
                        ' + ((v.hasOwnProperty('main') && v.main) ? '<br/><span class="badge badge-success mt10">Основная</span>' : '') + '\n\
                    </div>\n\
                    ' + sh + '\n\
                    <div class="action-group mt10 clearfix">\n\
                        <a href="#" class="action"><span class="action-icon-edit"></span></a>\n\
                        <a href="#" class="action"><span class="action-icon-delete"></span></a>\n\
                    </div>\n\
                </div>\n\
            ');
        });
    }

    $(document).ready(function () {

        storageSync();
        storageRender();

        $('.add-data-block .submit-handler').on('click', function () {
            var $p = $(this).closest('.data-block');
            var entry = $(this).closest('form').serializeObject().data;
            storageAdd(entry);

            $('.add-data-block input[type="text"], .add-data-block textarea', $p).val('');
            $('.add-data-block input[type="checkbox"]', $p).prop('checked', false);
            $('form').yiiActiveForm('validateAttribute', 'doctor-selected_specs');
        });

        $(document).on('click.dl', '.data-block .action:has(.action-icon-delete)', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            storageRemove($item.attr('data-key'));
            $('form').yiiActiveForm('validateAttribute', 'doctor-selected_specs');
            e.preventDefault();
            return false;
        });

        $(document).on('click.dl', '.data-block .action:has(.action-icon-edit)', function (e) {

            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = formStorage[$item.attr('data-key')];

            if ($('.item .update-handler', $block).length) {
                storageRender();
                $('.item[name="' + $item.attr('data-key') + '"]', $block).trigger('click');
            }

            var $clone = $('.add-data-block', $block).children().not('h3').not('.field-doctor-selected_specs').clone(false);
            $('.submit-handler', $clone).remove();
            $('.form-group', $clone).removeClass('has-error');

            $('.checkbox', $clone).each(function () {
                var uid = uniqid();
                $('> input', $(this)).attr('id', uid).prop('checked', false);
                $('> label', $(this)).attr('for', uid);
            });

            $item.html($clone);

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

            $('select', $item).each(function (k, v) {
                var prop = $(this).attr('name').match(/\[([a-z_]+){1}\]$/)[1];
                if (data.hasOwnProperty(prop)) {
                    $(this).val(data[prop]).selectpicker('refresh');
                }
            });

            $('input[type="checkbox"]', $item).each(function () {
                var prop = $(this).attr('name').match(/\[([a-z_]+){1}\]$/)[1];
                if (data.hasOwnProperty(prop) && data[prop]) {
                    $(this).prop('checked', true);
                }
            });

            e.preventDefault();
            return false;
        });

        $(document).on('click.dl', '.data-block .cancel-handler', function (e) {
            storageRender();
        });

        $(document).on('click.dl', '.data-block .update-handler', function (e) {
            var $block = $(this).closest('.data-block');
            var $item = $(this).closest('.item');
            var data = formStorage[$item.attr('data-key')];
            var entry = $(this).closest('form').serializeObject().data;
            if (!entry.hasOwnProperty('main')) {
                entry.main = 0;
            }
            if (!entry.hasOwnProperty('show_schedule')) {
                entry.show_schedule = 0;
            }
            storageEdit($item.attr('data-key'), entry);
            $('form').yiiActiveForm('validateAttribute', 'doctor-selected_specs');
        });

        $('.sexpicker input[type="radio"]').on('change', function () {
            if (!$('#doctor-photo').val()) {
                $('.patients-add-form .upload-photo img').attr('src', defaultPhotos[1 * $(this).val()]);
            }
        });

        $('#<?= $form->options['id']; ?>').on('afterValidate', function (event, attribute, messages, deferreds) {
            $('.doctors-top-tabs > li > a .badge-error-count').remove();

            var ids = [];
            $('.doctors-top-tabs > li > a').each(function () {
                var count = 0;
                var tabId = $(this).attr('href');

                $.each(messages, function (k, v) {
                    if ($('#' + v.id).parents(tabId).length) {
                        count++;
                    }
                });

                if (count) {
                    $(this).append('<span class="badge-error-count">' + count + '</span>');
                }

                ids.push({
                    id: tabId,
                    count: count
                });
            });

            validateSchedule(attribute['doctor-schedule']);
        });

        $('.upload-photo').on('click', function () {
            $('#for-upload').trigger('click');
        });
        $('#for-upload').on('change', function () {
            var fd = new FormData();
            fd.append("file", this.files[0]);
            $.ajax({
                url: '<?= Url::to(['upload-photo']); ?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (resp) {
                    $('.upload-photo img').attr('src', resp.url);
                    $('#doctor-photo').val(resp.name);
                }
            });
        });
    });
</script>

<style>
    .field-doctor-selected_specs {
        position: absolute;
        left: 100%;
        top: 0px;
        width: 100%;
        z-index: 100;
    }
</style>