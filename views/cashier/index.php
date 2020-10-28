<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 14.07.2018
 * Time: 16:15
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;

$this->title = 'Рабочий стол';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <?php
        $formUid = uniqid();
        $form = ActiveForm::begin([
            'action' => ['cashier/index'],
            'id' => $formUid,
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
        ]);
        ?>
        <table class="form-table">
            <tr>
                <td style="">
                    <?php
                    $uid = uniqid();
                    ?>
                    <div class="relation-picker" data-id="<?= $uid; ?>">
                        <label class="control-label" for="<?= $uid; ?>">Пациент</label>
                        <?=
                        Html::activeHiddenInput($searchModel, 'patient_id', [
                            'class' => 'form-control target_value',
                            'data-text' => $searchModel->patient_id ? $searchModel->patient->fio : '',
                            'id' => $uid
                        ]);
                        ?>
                        <div class="clearfix">
                            <div class="btn-ctr pull-right">
                                <span class="item item-open-picker" title="Расширенный поиск"></span>
                                <span class="item item-clear" title="Очистить значение"></span>
                            </div>
                            <div class="search_input-ctr">
                                <?=
                                Html::input('text', '', $searchModel->patient_id ? $searchModel->patient->fio : '', [
                                    'class' => 'form-control search_input',
                                    'placeholder' => 'поиск'
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width:140px;">
                    <?=
                    $form->field($searchModel, 'date_start')->widget(Calendar::className(), [
                        'form' => $form,
                        'options' => [
                            'readonly' => true
                        ]
                    ]);
                    ?>
                </td>
                <td style="width:140px;">
                    <?=
                    $form->field($searchModel, 'date_end')->widget(Calendar::className(), [
                        'form' => $form,
                        'options' => [
                            'readonly' => true
                        ]
                    ]);
                    ?>
                </td>
                <td class="form-group_submit" style="width: 120px;">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-block btn-search">Найти</button>
                </td>
            </tr>
        </table>
        <?php ActiveForm::end(); ?>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form(count($dataProvider->models), ['пациент', 'пациента', 'пациентов']); ?></span>
                </div>
            </div>
        </div>
        <div class="cashier-index">
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_table', ['model' => $model]);
                },
                'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="3">{pager}</td></tr></tfoot></table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_date' => 'direction_created',
                        'col_patient' => 'patients_search_field',
                        'col_summ' => 'sum'
                    ],
                    'attributesLinkFalse' => [
                        'sum' => false
                    ]
                ],
                'emptyText' => ''
            ])
            ?>
        </div>
    </div>
</div>

<style>
    .cashier-index {

    }

    .cashier-index .col_date {
        width: 180px;
    }

    .cashier-index .col_summ {
        width: 150px;
        text-align: right;
    }
</style>

<script>
    $(document).ready(function () {
        $('.cashier-index .action-view').on('click', function () {
            var id = $(this).closest('tr').attr('data-id');
            openModal({
                url: '<?=Url::to(['view']);?>/' + id
            });

            return false;
        });

        $('#<?=$formUid?> .relation-picker').relationPicker({
            url_picker: '<?= Url::to(['patient/picker']); ?>',
            url_ac: '<?= Url::to(['patient/ac', 'q' => '_QUERY_']); ?>',
            event_name: 'patientPick'
        });
    });
</script>