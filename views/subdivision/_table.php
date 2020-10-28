<?php ?>
<tr>
    <td>
        <?= $model->name ?>
        <div class="action-group clearfix">
            <a class="action" href="/<?=Yii::$app->controller->id;?>/edit/<?= $model->id ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit"></span></a>
            <a class="action" href="/<?=Yii::$app->controller->id;?>/delete/<?= $model->id ?>" title="Удалить" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete"></span></a>
        </div>
    </td>
</tr>