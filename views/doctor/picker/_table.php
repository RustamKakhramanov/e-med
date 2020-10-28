<?php
?>
<tr>
    <td>
        <div class="action-group clearfix">
            <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
        </div>
        <?= $model->fio; ?>
    </td>
    
    <td class="">
        <?php echo $model->speciality_main->name; ?>
    </td>
    <td class=""><?= $model->subdivision->name; ?></td>
</tr>