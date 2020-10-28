<?php ?>
<tr>
    <td colspan="3">
        <?= $model->printType; ?>
        <div class="action-group clearfix">
            <a class="action" href="/<?= Yii::$app->controller->id; ?>/edit/<?= $model->id ?>?back=<?= urlencode(Yii::$app->request->url); ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit"></span></a>
            <?php if (Yii::$app->user->can('admin')) {?>
            <a class="action" href="/<?= Yii::$app->controller->id; ?>/log/<?= $model->id ?>?back=<?= urlencode(Yii::$app->request->url); ?>" title="История изменений" data-pjax="0"><span class="action-icon-print"></span></a>
            <?php }?>
            <a class="action" href="/<?= Yii::$app->controller->id; ?>/cancel/<?= $model->id ?>?back=<?= urlencode(Yii::$app->request->url); ?>" title="Удалить"><span class="action-icon-delete"></span></a>
        </div>
    </td>
    <td>
        <?= date('d.m.Y', strtotime($model->date)); ?>
    </td>
    <td colspan="3">
        <?= $model->patient ? $model->patient->fio : '&ndash;'; ?>
    </td>
    <td class="text-center">
        <?php
        if ($model->patient) {
            echo (date('Y') - date('Y', strtotime($model->patient->birthday)));
        } else {
            echo '&ndash;';
        }
        ?>
    </td>
    <td colspan="2">
        <?= $model->doctor ? $model->doctor->initials : '&ndash;'; ?>
    </td>
    <td colspan="2">
        <?= $model->doctor ? $model->doctor->speciality_main->name : '&ndash;'; ?>
    </td>
    <td class="text-right">
        <?= $model->patient ? $model->patient->getPhone() : '&ndash;'; ?>
    </td>
</tr>