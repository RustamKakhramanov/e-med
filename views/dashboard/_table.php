<?php
/** @var $model \app\models\DirectionItem */

use app\helpers\Utils;
use yii\helpers\Url;

?>
<tr data-id="<?=$model->id;?>">
    <td class="col_number">
        <?= $model->numberPrint; ?>
    </td>
    <td class="col_price">
        <?= $model->price->title; ?>
        <div class="action-group clearfix">
            <a class="action" href="<?= Url::to(['dashboard/reception', 'id' => $model->id]); ?>" title="Провести прием"><span class="action-icon-move"></span></a>
            <a class="action action-cancel" href="#" data-id="<?= $model->id; ?>" title="Отменить"><span class="action-icon-cancel"></span></a>
        </div>
    </td>
    <td class="col_patient">
        <?= $model->direction->patient->fio; ?>
    </td>
    <td class="col_doctor">
        <?= $model->doctor_id ? $model->doctor->fio : '-' ?>
    </td>
    <td class="col_created">
        <?= date('d.m.Y H:i', strtotime($model->direction->created)); ?>
    </td>
    <td class="col_paid">
        <?php if ($model->paid) { ?>
            <span class="badge badge-success">Оплачено</span>
        <?php } else { ?>
            <span class="badge badge-inverse">Не оплачено</span>
        <?php } ?>
    </td>
</tr>