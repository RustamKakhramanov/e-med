<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Шаблоны расписания';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form(count($list), ['шаблон', 'шаблона', 'шаблонов']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <table class="data-table">
            <thead class="h-sort">
                <tr>
                    <th class="col_name">
                        <span>Название</span>
                    </th>
                    <th class="col_name">
                        <span>Ссылка</span>
                    </th>                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $item) { ?>
                    <tr>
                        <td>
                            <?= $item->name; ?>
                            <div class="action-group clearfix">
                                <a class="action" href="/tv/edit/<?= $item->id ?>" title="Редактировать"><span class="action-icon-edit"></span></a>
                                <a class="action" href="/tv/delete/<?= $item->id ?>" title="Удалить" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?"><span class="action-icon-delete"></span></a>
                            </div>
                        </td>
                        <td>/tv/view/<?= $item->code; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>