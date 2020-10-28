<?php
/** @var $model \app\models\DirectionItem */

use app\helpers\Utils;
use yii\helpers\Url;

?>
<tr data-id="<?= $model->id; ?>">
    <td class="col_number">
        <?= Utils::number_pad($model->direction->id, 8); ?>
    </td>
    <td class="col_price">
        <?= $model->price->title; ?>
        <?php if ($model->direction->canEdit) { ?>
            <div class="action-group clearfix">
                <a class="action"
                   href="<?= Url::to(['patient/direction-edit', 'id' => $model->direction_id]); ?>"
                   title="Редактировать">
                    <span class="action-icon-edit"></span>
                </a>
            </div>
        <?php } ?>
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