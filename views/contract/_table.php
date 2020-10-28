<?php ?>
<tr>
    <td>
        <?= $model->name ?>
        
        <?php if ($model->typical) {?>
            <span class="badge ml5">Типовой</span>
        <?php };?>
        
        <?php if ($model->typical && $model->main) {?>
            <span class="badge badge-success ml5">Основной</span>
        <?php }?>
        
        <div class="action-group clearfix">
            <a class="action" href="/<?= Yii::$app->controller->id; ?>/edit/<?= $model->id ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit mr5"></span>Редактировать</a>
            <?php if (!$model->typical) { ?>
                <a class="action" href="/<?= Yii::$app->controller->id; ?>/delete/<?= $model->id ?>" title="Удалить" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete mr5"></span>Удалить</a>
            <?php } ?>
                
            <?php if ($model->typical && !$model->main) {?>
                <a class="action" href="/<?= Yii::$app->controller->id; ?>/do-main/<?= $model->id ?>" title="Сделать основным" data-pjax="0"><span class="action-icon-move mr5"></span>Сделать основным</a>
            <?php }?>
        </div>
    </td>
    <td>
        <?php if ($model->contractor_id) { ?>
            <?= $model->contractor->name;?>
        <?php } else { ?>
            Физ. лицо
        <?php }; ?>
    </td>
    <td class="text-center">
        <?= date('d.m.Y', strtotime($model->start)); ?>
    </td>
    <td class="text-center">
        <?= date('d.m.Y', strtotime($model->end)); ?>
    </td>
</tr>