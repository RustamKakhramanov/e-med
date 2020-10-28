<?php


/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;

$this->title = 'Отчет оператора детальный';

$monthes = [
    '01' => 'Январь',
    '02' => 'Февраль',
    '03' => 'Март',
    '04' => 'Апрель',
    '05' => 'Май',
    '06' => 'Июнь',
    '07' => 'Июль',
    '08' => 'Август',
    '09' => 'Сентябрь',
    '10' => 'Октябрь',
    '11' => 'Ноябрь',
    '12' => 'Декабрь'
];
?>

<script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
<script type="text/javascript" src="//www.amcharts.com/lib/3/serial.js"></script>


<?php
foreach ($operators as $key => $item) {
    if (!$item->asterNumbers) {
        unset($operators[$key]);
    }
}
?>

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
                <div class="col-xs-3">
                    <?=
                    $form->field($searchModel, 'user_id')->dropDownList(
                            ArrayHelper::map($operators, 'id', 'initials'), [
                        'class' => 'selectpicker',
                        'data-live-search' => 'true',
                        'prompt' => 'выберите'
                            ]
                    );
                    ?>
                </div>

                <div class="col-xs-2">
                    <?=
                    $form->field($searchModel, 'month')->dropDownList(
                            $monthes, [
                        'class' => 'selectpicker',
                            ]
                    );
                    ?>
                </div>
                <div class="col-xs-1">
                    <?= $form->field($searchModel, 'year'); ?>
                </div>

                <div class="col-xs-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-default">Показать</button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="result-ctr mt10">

        </div>
        <div class="page-loader"></div>
    </div>

</div>

<style>

</style>

<script>
    $(document).ready(function () {

        $('#logoperatordetailsearch-year').inputmask("y", {
            alias: "date",
            placeholder: "yyyy",
            yearrange: {minyear: 2000, maxyear: (new Date()).getFullYear()}});

        $('#search-form').on('beforeSubmit', function () {
            $('.page-loader').show();
            var data = $(this).serialize();
            //history.pushState('', '', '/<?= Yii::$app->controller->id; ?>?' + data);
            $.ajax({
                url: '/report-operator/detailed-load',
                data: data,
                type: 'get',
                dataType: 'html',
                success: function (resp) {
                    $('.page-loader').hide();
                    $('.result-ctr').html(resp);
                },
                error: function () {
                    $('.page-loader').hide();
                }
            });
            return false;
        });
    });
</script>