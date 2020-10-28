<tr>
    <td>
        <?= $model->fio ?>
        <div class="action-group clearfix">
            <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
        </div>
    </td>
    <td class="text-center"><?= $model->sex ? 'М' : 'Ж' ?></td>
    <td class="text-center"><?= date('d.m.Y', strtotime($model->birthday)); ?></td>
    <td class="text-center"><?= $model->age; ?></td>
    <td class="text-right"><?= $model->iin ?></td>
</tr>