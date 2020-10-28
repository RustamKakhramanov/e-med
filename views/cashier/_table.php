<?php

use app\helpers\Utils;

?>

<tr data-id="<?= $model->id; ?>">
    <td class="col_date"><?= date('d.m.Y в H:i', strtotime($model->cashierDate)); ?></td>
    <td class="col_patient">
        <?= $model->fio; ?>
        <div class="action-group clearfix">
            <a class="action action-view" href="#" title="Открыть"><span class="action-icon-move"></span></a>
        </div>
    </td>
    <td class="col_summ"><?= Utils::ncost($model->cashierCost); ?></td>
</tr>