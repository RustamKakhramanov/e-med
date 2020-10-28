<?php
/** @var $model \app\models\DirectionItem */

use app\helpers\Utils;
use yii\helpers\Url;

?>
<tr data-id="<?=$model->id;?>">
    <td class="col_number">
        <?= $model->directionItem ? $model->directionItem->numberPrint : '-'; ?>
        <div class="action-group clearfix">
            <a class="action js-action-print" href="<?= Url::to(['reception/print', 'id' => $model->id]); ?>" title="Печать" target="_blank"><span class="action-icon-print"></span></a>
        </div>
    </td>
    <td class="col_created">
        <?= date('d.m.Y', strtotime($model->created)); ?>
    </td>
    <td class="col_patient">
        <?= $model->directionItem->direction->patient->fio; ?>
    </td>
</tr>