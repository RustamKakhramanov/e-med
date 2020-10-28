<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = $model->id && !$model->draft ? 'Редактировать шаблон' : 'Добавить шаблон';
Yii::$app->view->params['bodyClass'] = 'bb-body';

//$this->registerJsFile('//cdn.ckeditor.com/4.5.6/full/ckeditor.js', [
//    'position' => \yii\web\View::POS_HEAD
//]);

$this->registerJsFile('/ckeditor/ckeditor.js', [
    'position' => \yii\web\View::POS_HEAD
]);

$typesJson = json_encode($varTypes);
?>


<div class="row">
    <div class="col-md-12">
        <div class="bb-form-container">
            <h1><?= $this->title; ?></h1>

            <div class="clearfix form-ctr">
                <div class="col-xs-8 form-left">

                    <?php
                    $form = ActiveForm::begin([
                                'method' => 'post',
                                'options' => [
                                    'class' => '',
                                    'novalidate' => '', //ng
                                ],
                                'validateOnType' => true,
                                'enableAjaxValidation' => true,
                    ]);
                    ?>

                    <div class="ml10 mr10">
                        <div class="row">
                            <div class="col-xs-6">
                                <?= $form->field($model, 'name'); ?>
                            </div>

                            <div class="col-xs-3">
                                <?=
                                $form->field($model, 'spec_id')->dropDownList(
                                        ArrayHelper::map($specialities, 'id', 'name'), [
                                    'class' => 'selectpicker',
                                    'prompt' => ' '
                                        ]
                                );
                                ?>
                            </div>
                            <div class="col-xs-3">
                                <?=
                                $form->field($model, 'doctor_id')->dropDownList(
                                        ArrayHelper::map($doctors, 'id', 'fio'), [
                                    'class' => 'selectpicker',
                                    'prompt' => 'Любой',
                                    'data-live-search' => 'true'
                                        ]
                                );
                                ?>
                            </div>
                        </div>

                    </div>

                    <div class="protocol-ctr ml10 mr10">
                        <div class="head">
                            <div class="top-panel">
                                <div class="row">
                                    <div class="col-xs-8 protocol-name">
                                        Редактирование протокола осмотра
                                    </div>
                                    <div class="col-xs-4">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="editor-ctr">
                            <?= $form->field($model, 'html')->textArea() ?>
                        </div>
                        <div class="footer clearfix">
                            <div class="pull-left">
                                <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
                                <span class="ml10 mr10">или</span>
                                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id); ?>" class="btn btn-sm btn-default">Отменить</a>
                            </div>
                            <div class="pull-right save-label">
                                <!--                                Автоматически сохранено 10 минут назад-->
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
                <div class="col-xs-4 form-right var-ctr">
                    <div class="clearfix">
                        <h3 class="pull-left ml10">Выберите показатель</h3>
                        <div class="btn btn-default pull-right add-new-var-handler mt15 mr20"><span class="ico-plus"></span></div>
                    </div>

                    <div class="var-list">
                        <div class="content scroll-ctr">
                            <div class="pl20 pr20">
                                <?php
                                foreach ($relVars as $group => $vars) {
                                    $uid = uniqid();
                                    ?>
                                    <div class="group-list">
                                        <a href="#<?= $uid; ?>" data-toggle="collapse" aria-expanded="true">
                                            <?= $group; ?><span class="ico-collapse"></span>
                                        </a>
                                        <div class="list-ctr list-rel-ctr collapse in" id="<?= $uid; ?>" aria-expanded="true">
                                            <?php foreach ($vars as $k => $var) { ?>
                                                <div class="item" data-id="<?= $var['id']; ?>" data-group="<?= $group; ?>" data-key="<?= $k; ?>">
                                                    <span class="ico-template-rel"></span>
                                                    <?= $var['name']; ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="group-list">
                                    <a href="#list-ctr" data-toggle="collapse" aria-expanded="true">
                                        Кастомные<span class="ico-collapse"></span>
                                    </a>
                                    <div class="list-ctr collapse in" id="list-ctr" aria-expanded="true">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="var-add" style="display: none;">
                        <form id="var-form">
                            <div class="content scroll-ctr">
                                <div class="form-content pl20 pr20">

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var template_id = <?= $model->id; ?>;
    var editor = false;
    //объект для создания переменной
    var varItem = {};
    var varItemDummy = {
        name: '',
        type: '',
        extra: {}
    };
    var varItemEdit = false;

    var varTypes = <?= $typesJson; ?>;
    var varList = <?= json_encode($model->varsArray); ?>;
    var relVars = <?= json_encode($relVars); ?>;

    function formHeight() {
        var $ctr = $('.form-ctr');
        var h = $(window).height() - $ctr.offset().top;
        $ctr.height(h);

        if (editor) {
            editor.resize('100%', h - 250, true);
        }
    }

    function renderVarForm(key) {

        $('.var-list').hide();
        $('.var-add').show();

        var $form = $('#var-form');
        var $formContent = $('.form-content', $form);

        $formContent.html('');

        var $group = $('\
            <div class="form-group">\n\
                <label>Название</label>\n\
                <input type="text" class="form-control" name="name"/>\n\
            </div>\n\
        ');

        $('input', $group).val(varItem.name);

        $formContent.append($group);

        var $group = $('\
            <div class="form-group">\n\
                <label>Тип</label>\n\
                <select class="selectpicker" name="type"><option>-</option></select>\n\
            </div>\n\
        ');

        $.each(varTypes, function (k, v) {
            $('select', $group).append('<option value="' + k + '">' + v + '</option>');
        });

        $('select', $group).val(varItem.type).selectpicker();
        $('select', $group).on('change', function () {
            varItem = $form.serializeObject();
            return renderVarForm();
        });

        $formContent.append($group);

        if ($.inArray(varItem.type, ['string', 'int']) >= 0) {
            var $group = $('\
                <div class="form-group">\n\
                    <label>По умолчанию</label>\n\
                    <input type="text" class="form-control" name="extra[default]"/>\n\
                </div>\n\
            ');

            if (varItem.hasOwnProperty('extra') && varItem.extra.hasOwnProperty('default')) {
                $('input', $group).val(varItem.extra.default);
            }

            $formContent.append($group);
        }
        
        if (varItem.type == 'textarea') {
            var $group = $('\
                <div class="form-group">\n\
                    <label>По умолчанию</label>\n\
                    <textarea class="form-control" name="extra[default]"></textarea>\n\
                </div>\n\
            ');

            if (varItem.hasOwnProperty('extra') && varItem.extra.hasOwnProperty('default')) {
                $('textarea', $group).val(varItem.extra.default);
            }

            $formContent.append($group);
        }

        if (varItem.type == 'select') {
            var $group = $('\n\
                <div class="form-group var-select-vals">\n\
                    <label>Доступные значения</label>\n\
                    <div class="input-group">\n\
                        <input type="text" class="form-control" placeholder="новое значение">\n\
                        <span class="input-group-btn">\n\
                            <button class="btn btn-default btn-select btn-add-val-handler" type="button">+</button>\n\
                        </span>\n\
                    </div>\n\
                    <div class="val-items">\n\
                    </div>\n\
                </div>\n\
            ');

            if (varItem.hasOwnProperty('extra') && varItem.extra.hasOwnProperty('values')) {
                $.each(varItem.extra.values, function (k, v) {
                    $('.val-items', $group).append('\n\
                        <div class="item">\n\
                            <div class="value">' + v + '<input type="hidden" name="extra[values][]" value="' + v + '"/></div>\n\
                            <span class="delete-handler">&ndash;</span>\n\
                        </div>\n\
                    ');
                });
            }

            $('.btn-add-val-handler', $group).on('click', function () {
                var v = $.trim($('input', $(this).closest('.input-group')).val());
                if (v) {

                    if (!varItem.hasOwnProperty('extra')) {
                        varItem.extra = {
                            values: []
                        }
                    }

                    //                    if (!varItem.extra.hasOwnProperty('values')) {
                    //                        varItem.extra.values = [];
                    //                    }
                    varItem.extra.values.push(v);

                    return renderVarForm();
                }
            });

            $('.delete-handler', $group).on('click', function () {
                varItem.extra.values.splice($(this).closest('.item').index(), 1);
                return renderVarForm();
            });

            $formContent.append($group);
        }

        $formContent.append('\n\
            <div class="clearfix">\n\
                <button type="submit" class="btn btn-primary">Сохранить</button>\n\
                <div class="btn btn-default ml10 btn-cancel-handler">Отменить</div>\n\
            </div>\n\
        ');

        $('.btn-cancel-handler', $formContent).on('click', function () {
            renderVarList();
        });

        $form.off('submit').on('submit', function () {
            var error = false;
            varItem = $(this).serializeObject();

            if (!varItem.name || !varItem.type) {
                error = true;
            }

            if (varItem.type == 'select') {
                if (!(varItem.hasOwnProperty('extra') && varItem.extra.hasOwnProperty('values') && varItem.extra.values.length)) {
                    error = true;
                }
            }

            if (!error) {
                if (varItemEdit === false) {
                    $.ajax({
                        url: '/template/var-add/' + template_id,
                        type: 'post',
                        data: varItem,
                        dataType: 'json',
                        success: function (resp) {
                            varList = resp;
                            renderVarList();
                        }
                    });
                } else {
                    $.ajax({
                        url: '/template/var-edit/' + varItemEdit,
                        type: 'post',
                        data: varItem,
                        dataType: 'json',
                        success: function (resp) {
                            varList = resp;
                            renderVarList();
                        }
                    });
                }
            } else {
                console.log('error');
            }

            return false;
        });

    }

    function renderVarList() {

        $('.var-add').hide();
        $('.var-list').show();

        var $ctr = $('.var-list #list-ctr');
        $ctr.html('');

        $.each(varList, function (k, v) {
            if (!v.deleted) {
                $ctr.append('\n\
                    <div class="item" data-id="' + v.id + '" data-key="' + k + '">\n\
                        <span class="ico-template-' + v.type + '"></span>\n\
                        ' + v.name + '\n\
                        <div class="action-group clearfix"><a class="action action-edit" href="#" ><span class="action-icon-edit"></span></a><a class="action action-delete" href="#" ><span class="action-icon-delete"></span></a></div>\n\
                    </div>\n\
                ');
            }
        });

        $('.item', $ctr).on('click', function () {
            var entry = varList[$(this).attr('data-key')];
            editor.execCommand('medvar', {
                startupData: {
                    id: entry.id,
                    uid: uniqid(),
                    name: entry.name,
                    type: entry.type
                }
            });
        });

        $('.action-delete', $ctr).on('click', function () {
            var id = $(this).closest('.item').attr('data-id');

            $.ajax({
                url: '/template/var-delete/' + id,
                success: function (resp) {
                    varList = resp;
                    renderVarList();
                }
            });
            return false;
        });

        $('.action-edit', $ctr).on('click', function () {
            var key = $(this).closest('.item').attr('data-key');

            varItem = varList[key];
            varItemEdit = varItem.id;
            renderVarForm();

            return false;
        });
    }


    $(document).ready(function () {

        renderVarList();

        CKEDITOR.config.extraPlugins = 'quicktable,widget,lineutils,simplebox,medvar';
        CKEDITOR.config.removePlugins = 'elementspath,save,font';

        editor = CKEDITOR.replace('template-html', {
            qtRows: 10, // Count of rows in the quicktable (default: 8)
            qtColumns: 10, // Count of columns in the quicktable (default: 10)
            qtBorder: '1', // Border of the inserted table (default: '1')
            qtWidth: '100%',
            qtStyle: {'border-collapse': 'collapse'}, // Content of the style-attribute of the inserted table (default: null)
            qtClass: 'test', // Class of the inserted table (default: '')
            qtCellPadding: '0', // Cell padding of the inserted table (default: '1')
            qtCellSpacing: '0', // Cell spacing of the inserted table (default: '1')
            resize_enabled: false,
            filebrowserImageUploadUrl : '/template/upload-image/' + template_id,
            on: {
                instanceReady: function (evt) {
                    formHeight();
                }
            },
            contentsCss: [
                '/ckeditor/plugins/medvar/samples/contents.css'
            ]
        });


        $(window).on('resize', function () {
            setTimeout(function () {
                formHeight();
            }, 400);
        });

        $('.var-list .scroll-ctr, .var-add .scroll-ctr').jScrollPane({
            autoReinitialise: true,
            verticalGutter: 0,
            hideFocus: true
        });

        $('.add-new-var-handler').on('click', function () {
            varItemEdit = false;
            varItem = varItemDummy;
            renderVarForm();
        });

        $('#w0').on('beforeValidate', function (event, messages, deferreds) {
            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement();
            }
            return true;
        });

        $('.list-rel-ctr .item').on('click', function () {
            var entry = relVars[$(this).attr('data-group')][$(this).attr('data-key')];
            editor.execCommand('medvar', {
                startupData: {
                    id: entry.id,
                    uid: uniqid(),
                    name: entry.name,
                    type: entry.type
                }
            });
        });
    });
</script>