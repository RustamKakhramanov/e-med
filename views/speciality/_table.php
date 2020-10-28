<?php ?>
<tr>
    <td>
        <?= $model->name ?>
        <div class="action-group clearfix">
            <a class="action" href="/speciality/edit/<?= $model->id ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit"></span></a>
            <a class="action" href="/speciality/delete/<?= $model->id ?>" title="Удалить" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete"></span></a>
        </div>
    </td>
    <td class="text-right"><?= $model->doctorsCount;?></td>
</tr>