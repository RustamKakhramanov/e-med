<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;
use yii\helpers\ArrayHelper;
use app\components\RelPicker\RelPicker;

Yii::$app->view->params['bodyClass'] = 'master-body';
$this->title = 'Оплата направлений';

?>

<div class="row">
    <div class="col-md-12">
        <div class="">
            <h1><?= $this->title; ?></h1>

            <?php
            $formId = uniqid();
            $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'payment-form',
                    'novalidate' => '', //ng
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
            ]);
            ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>

</style>

<script>
    $(document).ready(function () {

    });
</script>