<?php

use app\helpers\Utils;
use yii\helpers\Url;

?>
<tr data-id="<?= $model->id; ?>">
    <td class="col_number">
        <?= $model->number; ?>
        <?php if ($model->back_id) { ?>
            <span class="badge badge-info" title="Чек возврата"><i class="fa fa-reply"></i></span>
        <?php } ?>
    </td>
    <td class="col_date">
        <?= date('d.m.Y', strtotime($model->created)); ?>
    </td>
    <td>
        <?= Utils::nformat($model->sum); ?>
        <div class="action-group clearfix">
            <?php if ($model->webkassa_id) { ?>
                <a class="action action-print" href="#" title="Печать"><span class="action-icon-print"></span></a>
            <?php } ?>
            <?php if (!$model->back_id) { ?>
                <a class="action action-cancel" href="#" title="Чек отмены"><span class="action-icon-cancel"></span></a>
            <?php } ?>
        </div>
    </td>
    <td>
        <?= $model->patient->fio; ?>
    </td>
    <td>
        <?= $model->user->username; ?>
    </td>
    <td>
        <?php if ($model->webkassa_id) { ?>
            <?= $model->webkassa_id; ?>
        <?php } else { ?>
            <span class="badge badge-danger">ошибка передачи</span>
            <a href="<?= Url::to(['resend-webkassa', 'id' => $model->id]); ?>"
               class="cashier-checks__send">отправить</a>
        <?php } ?>
    </td>
</tr>