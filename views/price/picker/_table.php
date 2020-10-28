<?php
?>
<tr>
    <td>
        <div class="action-group clearfix">
            <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
        </div>
        <span class="row-icon <?= $model->iconClass; ?>"></span>
        <?= $model->title; ?>
    </td>

    <td>
        <?=$model->group->name;?>
    </td>
    
    <td class="col_cost">
        <?= number_format($model->cost, 2, ', ', ' '); ?>
    </td>
</tr>