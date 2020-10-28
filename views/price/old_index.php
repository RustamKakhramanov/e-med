<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Прайс';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>
        
        <?=
        $this->render('_search', [
            'model' => $searchModel
        ]);
        ?>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['позиция', 'позиции', 'позиций']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <div class="pricelist-list">
            <?php if ($countFindRecord) { ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col_name"><a>Наименование</a></th>
                            <th class="col_service"><a>Вид услуги</a></th>
                            <th class="col_cost"><a>Базовая стоимость</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($groups as $group => $items) {
                            $uid = uniqid();
                            ?>
                            <tr>
                                <td colspan="3">
                                    <div class="row-header" data-toggle="collapse" aria-expanded="true" data-target="#<?= $uid; ?>">
                                        <?= $group; ?> <span class="ico-collapse"></span>
                                    </div>
                                    <div class="collapse in" id="<?= $uid; ?>">
                                        <table class="data-table">
                                            <?php foreach ($items as $item) { ?>
                                                <tr data-id="<?= $item->id; ?>">
                                                    <td>
                                                        <?= $item->title; ?>
                                                        <div class="action-group action-group-arrow clearfix">
                                                            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id) . '/edit/' . $item->id; ?>" class="action action-edit"><span class="action-icon-edit"></span></a>
                                                            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id) . '/delete/' . $item->id; ?>" class="action action-delete"><span class="action-icon-delete"></span></a>
                                                        </div>
                                                    </td>
                                                    <td class="col_service"><?= $item->typeText; ?></td>
                                                    <td class="col_cost"><?= number_format($item->cost, 2, ', ', ' '); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>

                <?php
            } else {
                ?>

                <?php
            }
            ?>
        </div>
    </div>
</div>