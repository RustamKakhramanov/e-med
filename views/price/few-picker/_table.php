<?php
$ids = json_decode(Yii::$app->request->post('_ids', '[]'), true);
?>
<tr>
    <td>
        <div class="action-group clearfix">
            <?php if (in_array($model->id, $ids)) {?>
                <a href="#" class="action action-unpick" data-id="<?= $model->id; ?>"><span class="text-label">Убрать</span></a>
            <?php } else {?>
                <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
            <?php } ?>
        </div>
        <span class="row-icon <?= $model->iconClass; ?>"></span>
        <?= $model->title; ?>
    </td>

    <td class="col_group">
        <?=$model->group->name;?>
    </td>
    
    <td class="col_cost">
        <?= number_format($model->cost, 2, ', ', ' '); ?>
    </td>
</tr>