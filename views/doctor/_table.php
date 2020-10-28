<?php
?>
<tr>
    <td>
        <?= $model->fio ?>
        <?php if ($model->fired) { ?>
            <span class="badge badge-danger">Уволен<?= $model->sex ? '' : 'а'; ?></span>
        <?php } ?>
        <div class="action-group clearfix">
            <a class="action" href="/doctor/edit/<?= $model->id ?>" title="Редактировать" data-pjax="0"><span class="action-icon-edit"></span></a>
            <a class="action" href="/doctor/delete/<?= $model->id ?>" title="Удалить" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete"></span></a>
        </div>
    </td>
    
    <td class="">
                <?php echo $model->speciality_main->name; ?>
        <?php 
        
//        foreach ($model->doctorSpecialities as $rel) {
//            debug($rel->speciality);
//            //print_r($rel->speciality);            
//        }
        
        ;?>
    </td>
    <td class=""><?= $model->subdivision->name; ?></td>
    <td class="text-center"><?= $model->sex ? 'М' : 'Ж'; ?></td>
    <td class="text-center"><?= date('d.m.Y', strtotime($model->birthday)); ?></td>
</tr>