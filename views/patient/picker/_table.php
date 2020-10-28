<?php
use yii\helpers\Url;
?>
<tr>
    <td>
        <?= $model->fio ?>
        <div class="action-group clearfix">
            <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
        </div>
    </td>
    <td class=""><?= $model->sex ? 'М' : 'Ж' ?></td>
    <td class=""><?= date('d.m.Y', strtotime($model->birthday)); ?></td>
    <td class=""><?= $model->age; ?></td>
    <td class=""><?= $model->iin ?></td>
</tr>