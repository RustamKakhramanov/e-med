<?php

use yii\helpers\Url;

?>

<div style="margin-right: 310px;">
    <div class="row">
        <div class="col-md-10">
            <?= $form->field($model, 'name'); ?>
        </div>
        <div class="col-md-2">
            <?=
            $form->field($model, 'size')->dropDownList(
                $model->sizeSelect, [
                    'class' => 'selectpicker form-control'
                ]
            );
            ?>
        </div>
    </div>
</div>

<div class="template-ctr block__loading">
    <div class="template-body">
        <?= $form->field($model, 'html')->textArea()->label(false) ?>
    </div>
    <div class="template-vars native-scroll">
        <?= $this->render('vars/index', [
            'model' => $model,
            'customTab' => false
        ]); ?>
    </div>
</div>

<style>
    .template-body .cke_inner {
        background: #f5f7f9;
    }

    .template-body .cke_contents {
        border: 1px #eeeeee solid;
        margin: 0 auto;
    }

    .template-body .cke_top {
        background: #446584;
    }

    .temp-vars-inner {
        padding: 0px 10px;
    }

    .temp-vars-inner h3 {
        display: inline-block;
        margin: 0px;
        vertical-align: top;
    }

    .temp-vars-header .btn {

    }

    .temp-vars__group {
        margin-top: 10px;
    }

    .temp-vars__group-header .ico-collapse {
        vertical-align: top;
    }

    .temp-vars__group-header {
        font-weight: 700;
        cursor: pointer;
    }

    .temp-vars__group_content {
        padding-left: 13px;
        border-left: 1px solid rgb(238, 238, 238);
        margin-left: 12px;
    }

    .temp-vars__item .temp-vars__item-name:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .temp-vars__item-placeholder {
        margin-top: 50px;
        color: #ccc;
        text-align: center;
    }

    .temp-vars__item__edit,
    .temp-vars__item__remove {
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        width: 17px;
        cursor: pointer;
        color: #ccc;
    }

    .temp-vars__item__edit:before {
        content: "\f040";
    }

    .temp-vars__item__remove:before {
        content: "\f00d";
    }

    .temp-vars__item__edit:hover,
    .temp-vars__item__remove:hover {
        color: #476486;
    }

    .temp-vars__group__edit,
    .temp-vars__group__remove {
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        width: 17px;
        cursor: pointer;
        color: #ccc;
    }

    .temp-vars__group__edit:before {
        content: "\f040";
    }

    .temp-vars__group__remove:before {
        content: "\f00d";
    }

    .temp-vars__group__edit:hover,
    .temp-vars__group__remove:hover {
        color: #476486;
    }
</style>

<script>
    var editor;

    function editorHeight() {
        var $ctr = $('.template-ctr');
        var h = $(window).height() - 280;
        $ctr.height(h);

        if (editor) {
            editor.resize('100%', h - 44, true);
        }
    }

    function changeTemplateSize(v) {
        $('.cke_contents').width(paperSizes[v]);
        $('.cke_wysiwyg_frame').contents().find('html').css('background-image', 'url(/img/' + v + '.png)');
    }

    $(document).ready(function () {

        editorHeight();
        $(window).on('resize', function () {
            setTimeout(function () {
                editorHeight();
            }, 100)
        });

        CKEDITOR.config.extraPlugins = 'quicktable,widget,lineutils,simplebox,medvar';
        CKEDITOR.config.removePlugins = 'elementspath,save';

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
            filebrowserImageUploadUrl: '/template/upload-image/<?=$model->id;?>',
            on: {
                instanceReady: function (evt) {
                    editorHeight();
                    changeTemplateSize($('#template-size').val());
                    $('.template-ctr').removeClass('block__loading');
                }
            },
            contentsCss: [
                '/ckeditor/plugins/medvar/samples/contents.css'
            ]
        });

        $('#template-size').on('change', function () {
            changeTemplateSize($(this).val());
        });
    });
</script>