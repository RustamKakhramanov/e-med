<?php
use yii\helpers\Url;
?>
<tr>
    <td>
        <?= $model->name ?>
        <div class="action-group clearfix">
            <a href="#" class="action action-pick" data-id="<?= $model->id; ?>"><span class="text-label">Выбрать</span></a>
        </div>
    </td>
</tr>