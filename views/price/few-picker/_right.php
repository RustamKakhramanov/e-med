<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use app\helpers\Utils;

?>

<div class="b-picker__few-price_table-head">
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            //return $this->render('_table', ['model' => $model]);
        },
        'layout' => '<table class="data-table picker-items"><thead class="h-sort">{sorter}</thead></table>',
        'sorter' => [
            'class' => app\components\LinkSorterTHead::className(),
            'attributes' => [
                'col_title' => 'title',
                'col_group' => 'group_name',
                'col_cost' => 'cost'
            ]
        ],
        'emptyText' => ''
    ])
    ?>
</div>
<div class="b-picker__few-price_table-body native-scroll">
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_table', ['model' => $model]);
        },
        'layout' => '<table class="data-table picker-items"><tbody>{items}</tbody><tfoot><tr><td colspan="3">{pager}</td></tr></tfoot></table>',
        'sorter' => [
            'class' => app\components\LinkSorterTHead::className(),
            'attributes' => [
                'col_title' => 'title',
                'col_group' => 'group_name',
                'col_cost' => 'cost'
            ]
        ],
        'emptyText' => ''
    ]);
    ?>
</div>

<?php
$data = [];
foreach ($dataProvider->getModels() as $model) {
    $data[$model->id] = [
        'id' => $model->id,
        'name' => $model->title,
        'cost' => $model->cost,
        'type' => $model->type
    ];
}
?>

<script>
    var pickerSearchData = <?= json_encode($data); ?>;
    var pickerTarget = '<?= Yii::$app->request->get('target'); ?>';

    $(document).ready(function () {
        $(document)
            .off('click', '.b-picker__few-price .action-pick')
            .on('click', '.b-picker__few-price .action-pick', function () {
                var data = pickerSearchData[$(this).attr('data-id')];
                pickedItems.add(data);
                $(this).removeClass('action-pick').addClass('action-unpick');
                $('.text-label', $(this)).text('Убрать');

                return false;
            });

        $(document)
            .off('click', '.b-picker__few-price .action-unpick')
            .on('click', '.b-picker__few-price .action-unpick', function () {
                pickedItems.remove($(this).attr('data-id'));
                $(this).removeClass('action-unpick').addClass('action-pick');
                $('.text-label', $(this)).text('Выбрать');

                return false;
            });

        $(document)
            .off('click', '.b-picker__few-price .picker-items a')
            .on('click', '.b-picker__few-price .picker-items a', function () {
                var url = $(this).attr('href');
                var $modal = $(this).closest('.modal-wrap');
                if (url && url != '#') {
                    var $picker = $(this).closest('.b-picker');
                    $picker.addClass('block__loading');
                    $modal.trigger('updateData', {url: url});//.trigger('reload');
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: $('form', $picker).serialize(),
                        success: function (right_html) {
                            $('.b-picker__few-price-right').html(right_html);
                            $picker.removeClass('block__loading');
                        }
                    });

                    return false;
                }
            });

        $(document)
            .off('click', '.b-picker__few-price .picker-link__outer')
            .on('click', '.b-picker__few-price .picker-link__outer', function () {
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