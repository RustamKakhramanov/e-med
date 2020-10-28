<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
?>
<?php
$formUid = uniqid();
$form = ActiveForm::begin([
            'id' => $formUid,
            //'action' => ['index'],
            'action' => Url::to([Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]),
            'method' => 'get',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
            'options' => [
                'class' => 'patients-search-form',
            ],
        ]);

$needFields = [
    'last_name',
    'first_name',
    'middle_name',
    'birthday',
    'phone',
    'email',
        //'area'
];
$fields = [];
$labels = $model->attributeLabels();
foreach ($needFields as $name) {
    $fields[$name] = isset($labels[$name]) ? $labels[$name] : $name;
}
?>

<div class="row">
    <div class="col-xs-3">
        <?=
        $form->field($model, 'quick_field')->dropDownList(
                $fields, [
            'class' => 'selectpicker'
                ]
        );
        ?>
    </div>
    <div class="col-xs-3">
<?= $form->field($model, 'quick_value'); ?>
    </div>
    <div class="col-xs-6">
        <div class="clearfix">
            <div class="pull-left">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-search">Найти</button>
            </div>
            <div class="pull-left ml20">
                <label>&nbsp;</label>
                <span class="btn btn-default quick-create-handler">Создать</span>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(function () {
        $('#patientssearch-quick_field').selectpicker();

        $('#<?= $formUid; ?>').on('submit', function () {
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                type: 'get',
                data: $form.serialize(),
                success: function (resp) {
                    $form.closest('.modal-inner').html(resp);
                }
            });
            return false;
        });
        
        $('.quick-create-handler').on('click', function(){
            var pid = $(this).closest('.modal-wrap').attr('data-uid');
            var $modal = $(this).closest('.modal-wrap');
            openModal({
                centered: true,
                parentUid: pid,
                url: '/event/patient-add'
            });
        });
    });
</script>