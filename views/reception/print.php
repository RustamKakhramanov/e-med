<?php

/** @var $model app\models\Reception */
?>

<div class="print-ctr">
    <?= $model->html; ?>
</div>

<script>
    var values = <?=json_encode($model->templateValues);?>;

    $(document).ready(function () {
        $('html, body').width(<?=$model->template->sizeWidth;?>);

        $('.print-ctr .varbox').each(function () {
            var id = $(this).attr('data-id');
            if (values.hasOwnProperty(id)) {
                $(this)
                    .html(values[id])
                    .removeClass('varbox');
            }
        });

        window.print();
        window.close();
    });
</script>

<style>
    #yii-debug-toolbar {
        display: none !important;
    }
</style>