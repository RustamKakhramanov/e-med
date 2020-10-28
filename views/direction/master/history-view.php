<?php
/* @var $model app\models\Direction */

use yii\helpers\Url;
use yii\helpers\Html;
use app\helpers\Utils;

$this->title = 'Специалисты';
?>

<div class="history-view">
    <h1>Направление <?= Utils::number_pad($model->id, 6); ?></h1>

    <table class="table table-bordered">
        <tr>
            <td>Создано:</td>
            <td><?= date('d.m.Y H:i', strtotime($model->direction->created)); ?></td>
        </tr>
        <tr>
            <td>Пользователь:</td>
            <td><?= $model->direction->user->fio; ?></td>
        </tr>
        <tr>
            <td>Услуга:</td>
            <td><?= $model->price->title; ?></td>
        </tr>
        <tr>
            <td>Кол-во:</td>
            <td><?= $model->count; ?></td>
        </tr>
        <tr>
            <td>Сумма:</td>
            <td><?= Utils::ncost($model->summ); ?></td>
        </tr>
        <tr>
            <td>Специалист:</td>
            <td><?= $model->doctor_id ? $model->doctor->fio : '-'; ?></td>
        </tr>
        <tr>
            <td>Оплачено:</td>
            <td>
                <?php if ($model->paid) {?>
                    <span class="badge badge-success"><i class="fa fa-check"></i></span>
                <?php } else {?>
                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i></span>
                <?php }?>
            </td>
        </tr>
    </table>
    <span class="btn btn-primary js-history-view__close">Закрыть</span>
</div>

<style>
    .history-view {
        width: 600px;
        padding: 20px;
    }

    .history-view h1 {
        margin-top: 0;
    }
</style>

<script>
    $(document).ready(function(){
        $('.js-history-view__close').on('click', function(){
            $(this).closest('.modal-wrap').trigger('close');
        });
    })
</script>