<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var $model \app\models\report\CashierReport */
?>
<?php

$formUid = uniqid();
$form = ActiveForm::begin([
    'action' => ['cashier/report'],
    'id' => $formUid,
    'method' => 'get',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}",
    ],
]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td style="width:140px;">
                <?=
                $form->field($model, 'date_start')->widget(Calendar::className(), [
                    'form' => $form,
                    'options' => [
                        'readonly' => true
                    ]
                ]);
                ?>
            </td>
            <td style="width:140px;">
                <?=
                $form->field($model, 'date_end')->widget(Calendar::className(), [
                    'form' => $form,
                    'options' => [
                        'readonly' => true
                    ]
                ]);
                ?>
            </td>
            <td style="">
                <?=
                $form->field($model, 'cashbox_id')->dropDownList(
                    $model->cashboxes, [
                        'class' => 'selectpicker',
                        'data-live-search' => 'true',
                        'prompt' => 'Все'
                    ]
                );
                ?>
            </td>
            <td style="">
                <?=
                $form->field($model, 'cashier_id')->dropDownList(
                    $model->cashbox_id ? $model->cashiersList : [], [
                        'class' => 'selectpicker',
                        'data-live-search' => 'true',
                        'prompt' => 'Все',
                        'disabled' => !(bool)$model->cashbox_id
                    ]
                );
                ?>
            </td>
            <td class="form-group_submit" style="width: 120px;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-search">Найти</button>
            </td>
        </tr>
    </table>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function(){
        $('#cashierreport-cashbox_id').on('change', function(){
            let v = $(this).val();
            let $select = $('#cashierreport-cashier_id');
            if (v) {
                let $ctr = $('.field-cashierreport-cashier_id');
                $ctr.addClass('block__loading');
                $.ajax({
                    url: '<?=Url::to(['cashier/report-load-cashiers']);?>/' + v,
                    dataType: 'json',
                    success: function(resp) {
                        $select.html('<option value="">Все</option>');
                        $.each(resp, function(id, fio) {
                            $select.append('<option value="' + id + '">' + fio + '</option>');
                        });
                        $select.prop('disabled', false).selectpicker('refresh');
                        $ctr.removeClass('block__loading');
                    }
                });
            } else {
                $select.html('').prop('disabled', true).selectpicker('refresh');
            }
        });
    });
</script>