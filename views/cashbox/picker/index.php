<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use app\helpers\Utils;
use yii\widgets\ActiveForm;

$this->title = 'Специалисты';
?>

<div class="b-picker view-outer-ctr">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

            <?php
            $formUid = uniqid();
            $form = ActiveForm::begin([
                'action' => ['picker'],
                'id' => $formUid,
                'method' => 'get',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{hint}",
                ],
                'validateOnType' => false,
                'enableAjaxValidation' => false
            ]);
            ?>
            <table class="form-table">
                <tr>
                    <td>
                        <div class="form-group">
                            <label>Название</label>
                            <?= Html::textInput('name', Yii::$app->request->get('name'), [
                                'class' => 'form-control'
                            ]); ?>
                        </div>
                    </td>
                    <td class="col_submit" style="width: 120px;">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-block btn-search">Найти</button>
                    </td>
                </tr>
            </table>

            <?= Html::hiddenInput('target', Yii::$app->request->get('target')); ?>
            <?php ActiveForm::end(); ?>

            <div class="row mt10">
                <div class="col-xs-6">
                    <div class="clearfix">
                        <h2 class="pull-left">Найдено</h2>
                        <span class="subheader pull-left"><?= Utils::human_plural_form(count($models), ['запись', 'записи', 'записей']); ?></span>
                    </div>
                </div>
            </div>

            <?php if ($models) { ?>
                <table class="data-table picker-items">
                    <thead class="h-sort">
                    <tr>
                        <th class="col_name">
                            <span>Название</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($models as $item) { ?>
                        <tr>
                            <td>
                                <?= $item->name; ?>
                                <div class="action-group clearfix">
                                    <a href="#" class="action action-pick" data-id="<?= $item->id; ?>"><span
                                                class="text-label">Выбрать</span></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                Ничего не найдено
            <?php } ?>
        </div>
    </div>
</div>

<?php
$data = [];
foreach ($models as $model) {
    $data[$model->id] = [
        'id' => $model->id,
        'name' => $model->name
    ];
}
?>

<script>
    var pickerSearchData = <?= json_encode($data); ?>;
    var pickerTarget = '<?= Yii::$app->request->get('target'); ?>';

    $(document).ready(function () {
        $('#<?= $formUid; ?>').on('submit', function () {
            var url = $(this).attr('action') + '?' + $(this).serialize();
            var $modal = $(this).closest('.modal-wrap');
            $modal.trigger('updateData', {url: url}).trigger('reload');
            return false;
        });

        $('.picker-items .action-pick').on('click', function () {
            var data = pickerSearchData[$(this).attr('data-id')];
            data.target = pickerTarget;
            $(document).trigger('cashboxPick.' + pickerTarget, data);
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('.b-picker .picker-items a').on('click', function () {
            var url = $(this).attr('href');
            var $modal = $(this).closest('.modal-wrap');
            if (url && url != '#') {
                $modal.trigger('updateData', {url: url}).trigger('reload');
                return false;
            }
        });

        $('.b-picker .picker-link__outer').on('click', function () {
            var url = $(this).attr('href');
            var $currentModal = $(this).closest('.modal-wrap');
            openModal({
                url: url,
                onClose: function () {
                    $currentModal.trigger('reload');
                }
            });
            return false;
        });
    });
</script>
