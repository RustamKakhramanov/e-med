<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = $model->id ? 'Редактировать шаблон' : 'Добавить шаблон';

$this->registerJsFile('/ckeditor/ckeditor.js', [
    'position' => \yii\web\View::POS_HEAD
]);
?>


<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <?php
        $formId = uniqid();
        $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'id' => $formId,
                'class' => 'pb80'
            ],
            'validateOnType' => true,
            'enableAjaxValidation' => true,
        ]);
        ?>

        <ul class="tabs tabs-large template-top-tabs clearfix">
            <li class="active"><a href="#main-tab" data-toggle="tab">Основное</a></li>
            <li class=""><a href="#access-tab" data-toggle="tab">Настройки доступа</a></li>
        </ul>

        <div class="tab-content mt20">
            <div role="tabpanel" class="tab-pane active" id="main-tab">
                <?= $this->render('_main', [
                    'model' => $model,
                    'form' => $form
                ]); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="access-tab">
                <?= $this->render('_access', [
                    'model' => $model
                ]); ?>
            </div>
        </div>

        <div class="form-end mt30">
            <div class="btn btn-lg btn-primary form-submit-handler">Сохранить</div>
            <span class="ml10 mr10">или</span>
            <a href="<?=Url::to(['template']);?>" class="btn btn-sm btn-default">Отменить</a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
    .template-ctr {
        position: absolute;
        top: 190px;
        left: 10px;
        right: 10px;
        bottom: 136px;
    }

    .template-body {
        border: 1px #eee solid;
        position: absolute;
        left: 0px;
        top: 0px;
        right: 310px;
        bottom: 0px;
    }

    .template-vars {
        border: 1px #eee solid;
        position: absolute;
        top: 0px;
        right: 0px;
        bottom: 0px;
        width: 300px;
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>

<script>

    var paperSizes = <?=json_encode($model::$sizes);?>;

    $(document).ready(function () {
        $('#<?= $formId; ?>').on('afterValidate', function (event, attribute, messages) {
            $('.template-top-tabs > li > a .badge-error-count').remove();
            var ids = [];
            $('.template-top-tabs > li > a').each(function () {
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

        });
    });
</script>