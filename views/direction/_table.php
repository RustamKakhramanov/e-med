<?php
/** @var $model \app\models\DirectionItem */
use app\helpers\Utils;
?>
<tr>
    <td class="col_number">
        <?= Utils::number_pad($model->direction->id, 8); ?>
    </td>
    <td class="col_price">
        <?= $model->price->title; ?>
    </td>
    <td class="col_patient">
        <?= $model->direction->patient->fio; ?>
    </td>
    <td class="col_doctor">
        <?= $model->doctor_id ? $model->doctor->fio : '-'?>
    </td>
    <td class="col_created">
        <?= date('d.m.Y H:i', strtotime($model->direction->created)); ?>
    </td>
    <td class="col_paid">
        <?php if ($model->paid) {?>
            <span class="badge badge-success">Оплачено</span>
        <?php } else {?>
            <span class="badge badge-inverse">Не оплачено</span>
        <?php }?>
    </td>
</tr>