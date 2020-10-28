<?php
/* @var $model app\models\Direction */

use yii\helpers\Url;
use yii\helpers\Html;
use app\helpers\Utils;
use yii\widgets\ActiveForm;

?>

<div class="direction-cancel">
    <h1>Отмена направления <?= Utils::number_pad($model->direction->id, 6); ?></h1>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
        'method' => 'post',
        'options' => [
            'id' => $formId,
        ],
        'validateOnType' => true,
        'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($model, 'cancel_reason')->textarea(['rows' => 4]); ?>

    <button type="submit" class="btn btn-primary mb20">Отменить</button>

    <?php ActiveForm::end(); ?>
</div>

<style>
    .direction-cancel {
        width: 600px;
        padding: 0 20px;
    }
</style>

<script>
    $(document).ready(function () {
        $('#<?=$formId;?>').on('beforeSubmit', function () {
            var $f = $(this);
            $f.closest('.direction-cancel').addClass('block__loading');
            $.ajax({
                url: $f.attr('action'),
                type: 'post',
                data: $f.serialize() + '&_sended=1',
                success: function (resp) {
                    $('.dashboard-index .data-table tr[data-id="<?=$model->direction->id;?>"]').remove();
                    $f.closest('.modal-wrap').trigger('close');
                }
            });

            return false;
        });
    });
</script>