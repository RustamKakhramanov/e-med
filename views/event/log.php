<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\Calendar\Calendar;
use app\components\SexPicker\SexPicker;

$this->title = 'История изменений';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Поле</th>
                    <th>Исходное значение</th>
                    <th>Измененное значение</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?= date('d.m.Y H:i', strtotime($model->created)); ?>
                    </td>
                    <td>
                        <?= $model->user_id ? $model->user->initials : 'Система'; ?>
                    </td>
                    <td>
                        Создание записи
                    </td>
                    <td>
                        
                    </td>
                    <td>
                        <?= $model->created; ?>
                    </td>
                </tr>
                <?php foreach ($model->logs as $log) { ?>
                    <tr>
                        <td>
                            <?= date('d.m.Y H:i', strtotime($log->date)); ?>
                        </td>
                        <td>
                            <?= $log->user_id ? $log->user->initials : 'Система'; ?>
                        </td>
                        <td>
                            <?= $model->getAttributeLabel($log->field); ?>
                        </td>
                        <td class="text-danger">
                            <?= $log->before_value; ?>
                        </td>
                        <td class="text-success">
                            <?= $log->after_value; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>