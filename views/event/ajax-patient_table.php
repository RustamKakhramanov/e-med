<?php ?>
<tr data-key="<?=$model->id;?>">
    <td class="col-name">
        <?= $model->fio ?>
    </td>
    <td class="col-sex"><?= $model->sex ? 'М' : 'Ж' ?></td>
    <td class="col-birthday"><?= date('d.m.Y', strtotime($model->birthday)); ?></td>
</tr>

<script>
    patients[<?=$model->id;?>] = <?= json_encode([
        'id' => $model->id,
        'fio' => $model->fio,
        'sex' => $model->sex,
        'birthday' => date('d.m.Y', strtotime($model->birthday))
    ]);?>;
</script>