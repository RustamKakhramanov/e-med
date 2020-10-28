<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\helpers\Utils;
use yii\helpers\ArrayHelper;

$this->title = 'Прием направление #' . $directionItem->numberPrint;

?>

<div class="b-reception b-reception__opened">
    <h1><?= $this->title; ?></h1>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
        //'action' => ['template/add-group', 'id' => $model->template_id],
        'method' => 'post',
        'options' => [
            'id' => $formId,
            'class' => 'reception-form mt10'
        ],
        'validateOnType' => true,
        'enableAjaxValidation' => true,
    ]);
    ?>

    <div class="b-reception-head">
        <div class="row">
            <div class="col-xs-3">
                <?=
                $form->field($model, 'template_id')->dropDownList(
                    ArrayHelper::map($templates, 'id', 'name'), [
                        'prompt' => ' Не выбран',
                        'class' => 'selectpicker form-control'
                    ]
                );
                ?>
            </div>
            <div class="pull-right pt10 pr10">
                <span class="btn btn-default js__toggle-reception-sidebar">
                    <i class="fa fa-chevron-left mr5"></i>
                    <i class="fa fa-chevron-right mr5"></i>
                    Направления
                </span>
            </div>
        </div>
    </div>

    <div class="b-reception-ctr native-scroll <?php if ($model->draft->template_id) echo 'block__loading'; ?>">
        <div class="b-reception-inner">
            <div class="b-reception-inner__placeholder">Выберите шаблон</div>
        </div>
    </div>

    <div class="b-reception-sidebar native-scroll">
        <?= $this->render('_sidebar', [
            'directions' => $directions,
            'patient_id' => $directionItem->direction->patient_id
        ]); ?>
    </div>

    <?= Html::hiddenInput('draft_id', $model->draft->id); ?>

    <div class="form-end">
        <div class="btn btn-lg btn-primary form-submit-handler">Провести прием</div>
        <div class="btn btn-lg btn-default js-print-handler ml10">Печать</div>
        <span class="ml10 mr10">или</span>
        <a href="<?= Yii::$app->request->referrer; ?>" class="btn btn-sm btn-default">Назад</a>

        <div class="reception-autosave-ctr">
            автоматически сохранено в
            <span class="reception-autosave__saved"><i class="fa fa-clock-o"></i>
                <span class="reception-autosave__saved-time"><?= date('H:i', strtotime($model->draft->updated)); ?></span>
            </span>
            <span class="reception-autosave__saved-loader hidden"><i class="fa fa-refresh fa-spin"></i></span>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .b-reception {
        position: relative;
    }

    .b-reception-head {
        position: absolute;
        left: -20px;
        right: -18px;
        top: 50px;
        height: 71px;
        background: #F5F7F9;
        border-top: 1px #ECEFF3 solid;
        border-bottom: 1px #ECEFF3 solid;
        padding: 10px 20px;
    }

    .b-reception-ctr {
        position: absolute;
        top: 140px;
        left: 0px;
        right: 0px;
        bottom: 20px;
        border: 1px #ECEFF3 solid;
        overflow: auto;
    }

    .b-reception-inner {
        position: relative;
        height: 100%;
        padding: 10px;
    }

    .b-reception-inner__placeholder {
        font-size: 24px;
        color: #eee;
        position: absolute;
        left: 0;
        right: 0;
        text-align: center;
        top: 50%;
        margin-top: -20px;
    }

    .reception-autosave-ctr {
        margin: 11px 0px 0px 10px;
        display: inline-block;
        vertical-align: top;
    }

    .b-reception-sidebar {
        display: none;
        position: absolute;
        top: 140px;
        right: 0px;
        bottom: 20px;
        width: 300px;
        border: 1px #ECEFF3 solid;
        overflow-y: auto;
    }

    .b-reception-sidebar-inner {
        position: relative;
        height: 100%;
        padding: 10px;
    }

    .b-reception-sidebar h2 {
        margin: 0;
    }

    .b-reception-sidebar .header-ctr {
        border-bottom: 1px #eceff3 solid;
        padding-bottom: 8px;
    }

    .b-reception-sidebar .rl_col_date {
        width: 65px;
    }

    .b-reception-sidebar .rl-table-rows {
        font-size: 12px;
    }

    .b-reception__opened .b-reception-sidebar {
        display: block;
    }

    .b-reception__opened .b-reception-ctr {
        right: 320px;
    }

    .js__toggle-reception-sidebar .fa-chevron-right {
        display: none;
    }

    .b-reception__opened .js__toggle-reception-sidebar .fa-chevron-right {
        display: inline-block;
    }

    .b-reception__opened .js__toggle-reception-sidebar .fa-chevron-left {
        display: none;
    }
</style>

<script>
    var autoSaveInterval;

    function pageHeight() {
        $('.b-reception').height($(window).height() - 90);
    }

    $(document).ready(function () {
        pageHeight();
        $(window).on('resize', function () {
            setTimeout(pageHeight, 100);
        });

        $('#receptionform-template_id').on('change', function () {
            clearInterval(autoSaveInterval);
            var v = $(this).val();
            if (v) {
                $('.b-reception-ctr').addClass('block__loading');
                $.ajax({
                    url: '<?=Url::to(['dashboard/reception-load-template']);?>',
                    type: 'post',
                    data: {
                        template_id: v,
                        direction_item_id: <?=$directionItem->id;?>,
                        draft_id: <?=$model->draft->id;?>
                    },
                    success: function (resp) {
                        $('.b-reception-inner').html(resp);
                    }
                });
            } else {
                $('.b-reception-inner').html('<div class="b-reception-inner__placeholder">Выберите шаблон</div>');
                $('.b-reception-ctr').removeClass('block__loading');
            }
        }).trigger('change');

        $('.js-print-handler').on('click', function () {
            if ($('#receptionform-template_id').val()) {
                window.open("<?=Url::to(['dashboard/reception-draft-print', 'id' => $model->draft->id]);?>", "_blank", "toolbar=no,top=0,left=0,width=600,height=600");
            } else {
                toastr.error('Не выбран шаблон');
            }

            return false;
        });

        $('.js__toggle-reception-sidebar').on('click', function () {
            $('.b-reception').toggleClass('b-reception__opened');
            return false;
        });

        $('.b-reception-sidebar').on('reloadData', function(){
            let $ctr = $(this);
            $ctr.addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['dashboard/reception-load-sidebar', 'id' => $directionItem->direction->patient_id]);?>',
                success: function(html) {
                    $ctr.html(html).removeClass('block__loading');
                }
            });
        });
    });
</script>