<?php

use yii\helpers\Url;

?>
<tr>
    <td class="col_has_dir">
        <?php if ($model->hasEventWithoutDirections()) { ?>
            <span class="ico-calend"></span>
        <?php } ?>
    </td>
    <td>
        <?= $model->fio ?>
        <div class="action-group clearfix">
            <a class="action" href="<?= Url::to(['patient/edit', 'id' => $model->id]); ?>" title="Редактировать"><span
                        class="action-icon-edit"></span></a>
            <a class="action" href="<?= Url::to(['patient/direction', 'id' => $model->id]); ?>"
               title="Направления"><span class="action-icon-move"></span></a>
            <a class="action" href="<?= Url::to(['patient/delete', 'id' => $model->id]); ?>" title="Удалить"
               data-method="post"
               data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete"></span></a>
        </div>
    </td>
    <td>
        <div class="clearfix">
            &ndash;
        </div>
    </td>
    <td class="text-center"><?= $model->sex ? 'М' : 'Ж' ?></td>
    <td class="text-center"><?= date('d.m.Y', strtotime($model->birthday)); ?></td>
    <td class="text-center"><?= $model->age; ?></td>
    <td class="text-right"><?= $model->iin ?></td>
</tr>