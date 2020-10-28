<?php

use yii\helpers\Url;

/** @var $modal app\models\Template */

?>

<div class="temp-vars-inner">
    <h3 class="mt10">
        Показатели
    </h3>

    <ul class="tabs tabs-small clearfix mt10">
        <li class="<?php if (!$customTab) echo 'active';?>"><a href="#vars-default" data-toggle="tab" aria-expanded="true">Предопределенные</a></li>
        <li class="<?php if ($customTab) echo 'active';?>"><a href="#vars-custom" data-toggle="tab" aria-expanded="true">Дополнительные</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane <?php if (!$customTab) echo 'active';?>" id="vars-default">
            <div class="temp-vars-list">
                <?php foreach (\app\models\TemplateVarGroup::getCommonRels() as $group) { ?>
                    <?php $collapseId = uniqid(); ?>
                    <div class="temp-vars__group">
                        <div class="temp-vars__group-header" data-toggle="collapse" data-target="#<?= $collapseId; ?>">
                            <span class="ico-collapse"></span>
                            <?= $group->name; ?>
                        </div>
                        <div class="temp-vars__group_content collapse in" id="<?= $collapseId; ?>">
                            <?php foreach ($group->templateVars as $var) { ?>
                                <div class="temp-vars__item" data-id="<?= $var->id; ?>"
                                     data-name="<?= $group->name . ':' . $var->name; ?>"
                                     data-type="<?= $var->type; ?>">
                                    <span class="temp-vars__item-name"><?= $var->name; ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="tab-pane <?php if ($customTab) echo 'active';?>" id="vars-custom">

            <span class="btn btn-default btn-xs mt10 mb10 js-add-group-handler"><i class="fa fa-plus mr5"></i>Создать группу</span>
            <span class="btn btn-default btn-xs mt10 mb10 js-add-handler"><i class="fa fa-plus mr5"></i>Создать показатель</span>

            <?php if ($model->groupVars) { ?>
                <?php foreach ($model->groupVars as $group) { ?>
                    <?php $collapseId = uniqid(); ?>
                    <div class="temp-vars__group" data-id="<?=$group['id'];?>">
                        <div class="temp-vars__group-header" data-toggle="collapse" data-target="#<?= $collapseId; ?>">
                            <span class="ico-collapse"></span>
                            <?= $group['name']; ?>
                            <?php if ($group['id']) {?>
                                <span class="temp-vars__group__edit ml5" title="Редактировать"></span>
                                <span class="temp-vars__group__remove" title="Удалить"></span>
                            <?php }?>
                        </div>
                        <div class="temp-vars__group_content collapse in" id="<?= $collapseId; ?>">
                            <?php foreach ($group['items'] as $var) { ?>
                                <div class="temp-vars__item" data-id="<?= $var->id; ?>"
                                     data-name="<?= $var->name; ?>"
                                     data-type="<?= $var->type; ?>">
                                    <span class="temp-vars__item-name"><?= $var->name; ?></span>
                                    <span class="temp-vars__item__edit ml5" title="Редактировать"></span>
                                    <span class="temp-vars__item__remove" title="Удалить"></span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="temp-vars__item-placeholder">
                    <i class="fa fa-warning"></i> Нет показателей
                </div>
            <?php } ?>

        </div>
    </div>
</div>


<script>
    var tempVars = <?=json_encode([]);?>;

    $(document).ready(function () {

        $('.temp-vars__item').on('click', function () {
            editor.execCommand('medvar', {
                startupData: {
                    id: $(this).attr('data-id'),
                    uid: uniqid(),
                    name: $(this).attr('data-name'),
                    type: $(this).attr('data-type')
                }
            });
        });

        $('.js-add-handler').on('click', function () {
            $('.template-vars').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/add-var', 'id' => $model->id]);?>',
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
        });

        $('.js-add-group-handler').on('click', function () {
            $('.template-vars').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/add-group', 'id' => $model->id]);?>',
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
        });

        $('.temp-vars__item__edit').on('click', function (e) {
            $('.template-vars').addClass('block__loading');
            var id = $(this).closest('.temp-vars__item').attr('data-id');
            $.ajax({
                url: '<?=Url::to(['template/edit-var']);?>/' + id,
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
            e.stopPropagation();
        });

        $('.temp-vars__item__remove').on('click', function (e) {
            var id = $(this).closest('.temp-vars__item').attr('data-id');
            bootbox.confirm('Подтвердите удаление', function (result) {
                if (result) {
                    $('.template-vars').addClass('block__loading');
                    $.ajax({
                        url: '<?=Url::to(['template/remove-var']);?>/' + id,
                        success: function (html) {
                            $('.template-vars').html(html).removeClass('block__loading');
                        }
                    });
                }
            });
            e.stopPropagation();
        });

        $('.temp-vars__group__edit').on('click', function(e){
            $('.template-vars').addClass('block__loading');
            var id = $(this).closest('.temp-vars__group').attr('data-id');
            $.ajax({
                url: '<?=Url::to(['template/edit-group']);?>/' + id,
                success: function (html) {
                    $('.template-vars').html(html).removeClass('block__loading');
                }
            });
            e.stopPropagation();
        });

        $('.temp-vars__group__remove').on('click', function (e) {
            var id = $(this).closest('.temp-vars__group').attr('data-id');
            bootbox.confirm('Подтвердите удаление', function (result) {
                if (result) {
                    $('.template-vars').addClass('block__loading');
                    $.ajax({
                        url: '<?=Url::to(['template/remove-group']);?>/' + id,
                        success: function (html) {
                            $('.template-vars').html(html).removeClass('block__loading');
                        }
                    });
                }
            });
            e.stopPropagation();
        });
    });
</script>