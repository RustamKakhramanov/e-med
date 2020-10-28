<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;

$this->title = 'Отчет оператора';
?>

<script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
<script type="text/javascript" src="//www.amcharts.com/lib/3/serial.js"></script>

<div class="row">
    <div class="col-md-12">

        <div class="clearfix">
            <h1 class="pull-left">
                <?= $this->title; ?>
            </h1>
        </div>

        <div class="search-ctr no-print">

            <?php
            $form = ActiveForm::begin([
                        'action' => ['index'],
                        'id' => 'search-form',
                        'method' => 'get',
                        'validateOnType' => true,
                        'enableAjaxValidation' => true,
            ]);
            ?>

            <div class="row">
                <div class="col-xs-2">
                    <?=
                    $form->field($searchModel, 'queue')->dropDownList(
                            ArrayHelper::map($queues, 'queue', 'queue'), [
                        'class' => 'selectpicker',
                            //'prompt' => ' '
                            ]
                    );
                    ?>
                </div>
                <div class="col-xs-2">
                    <?= $form->field($searchModel, 'date_from')->widget(Calendar::className(), ['form' => $form]); ?>
                </div>
                <div class="col-xs-1">
                    <?=
                    $form->field($searchModel, 'time_start', ['options' => ['class' => 'form-group']])->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => 'h:s',
                        'clientOptions' => [
                            'clearIncomplete' => true
                        ],
                    ])->textInput(['class' => 'form-control', 'maxlength' => 8])
                    ?>
                </div>

                <div class="col-xs-2">
                    <?= $form->field($searchModel, 'date_to')->widget(Calendar::className(), ['form' => $form]); ?>
                </div>

                <div class="col-xs-1">
                    <?=
                    $form->field($searchModel, 'time_end', ['options' => ['class' => 'form-group']])->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => 'h:s',
                        'clientOptions' => [
                            'clearIncomplete' => true
                        ],
                    ])->textInput(['class' => 'form-control', 'maxlength' => 8])
                    ?>
                </div>

                <div class="col-xs-2">
                    <?=
                    $form->field($searchModel, 'group_type')->dropDownList(
                            $searchModel::$group_types, [
                        'class' => 'selectpicker'
                            ]
                    );
                    ?>
                </div>

                <div class="col-xs-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-default">Показать</button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="result-ctr mt10">
            <?php
//            if ($periods) {
//                echo $this->render('load', [
//                    'periods' => $periods,
//                    'searchModel' => $searchModel
//                ]);
//            } 
            ?>
        </div>
        <div class="page-loader"></div>
    </div>

</div>

<style>

</style>

<script>

    var chartType = 'line';

    $(document).ready(function () {
        $('.time-input').inputmask("h:s");

        $('#search-form').on('submit', function () {
            $('.page-loader').show();
            var data = $(this).serialize();
            history.pushState('', '', '/<?= Yii::$app->controller->id; ?>?' + data);
            $.ajax({
                url: '/report-queue/load',
                data: data,
                type: 'get',
                dataType: 'html',
                success: function (resp) {
                    $('.page-loader').hide();
                    $('.result-ctr').html(resp);
                }
            });
            return false;
        });
    });
</script>