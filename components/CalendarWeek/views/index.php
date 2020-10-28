<?php

use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
?>

<?php
$arConfig = [];
if ($model) {
    $arConfig = [
        'model' => $model,
        'attribute' => $attribute,
    ];
}

$uid = 'datepicker-' . uniqid();

$showArrows = (isset($options['showArrows']) && $options['showArrows']);
?>
<div class="form-group" id="<?= $uid; ?>">
    <?php if ($showArrows) { ?>
        <div class="range-picker-ctr">
            <div class="control control-left">←</div>
        <?php } ?>
        <div class="input-group input-datepicker datepicker-week">
            <div class="hidden">
                <?=
                $form->field($model, 'date_week')->hiddenInput([
                    'class' => 'form-control datepicker data-required'
                ])->label(false);
                ?>
            </div>
            <input class="form-control week-text" readonly="" value="1 Окт - 7 Окт"/>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="input-datepicker-ui"></div>
            </div>
            <span class="input-group-addon dropdown-handler" data-toggle="dropdown" aria-expanded="false"></span>
        </div>

        <?php if ($showArrows) { ?>
            <div class="control control-right">→</div>
        </div>
    <?php } ?>
</div>

<script>

    $(document).ready(function () {
        $('#<?= $uid;?> .input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: false,
            changeYear: false,
            minDate: '<?= date('d.m.Y'); ?>',
            onSelect: function (dateText) {
                    <?= $options['handler']; ?>(dateText);
            }
//todo
//            beforeShowDay: function (date) {
//                console.log(date);
//                var cssClass = '';
//                if (date >= startWeek && date <= endWeek)
//                    cssClass = 'ui-datepicker-current-day';
//                return [true, cssClass];
//            }
        });
    });

</script>