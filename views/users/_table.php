<?php ?>
<tr>
    <td>
        <?= $model->username; ?>
        
        <div class="action-group clearfix">
            <a class="action" href="/<?=Yii::$app->controller->id;?>/edit/<?= $model->id ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit"></span></a>
        </div>
    </td>
    <td>
        <?= $model->fio; ?>
    </td>
    <td>
        <?= date('H:i d.m.Y', strtotime($model->created_at)); ?>
    </td>
    <td>
        <?= date('H:i d.m.Y', strtotime($model->updated_at)); ?>
    </td>
    <td>
        <?= $model->roleName; ?> (<?= $model->roleNameRu; ?>)
    </td>
</tr>