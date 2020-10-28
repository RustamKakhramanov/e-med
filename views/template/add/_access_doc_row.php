<?php
/**
 * @var $model app\models\TemplateDoc
 */

use yii\helpers\Url;
use yii\helpers\Html;

$uid = uniqid();
?>

<div class="item item-<?= $uid; ?>">
    <table>
        <tr>
            <td class="rl_col_check">
                <div class="checkbox">
                    <?=
                    Html::checkbox('', false, [
                        'label' => null,
                        'id' => $uid
                    ])
                    ?>
                    <?= Html::label('&nbsp;', $uid); ?>
                </div>
            </td>
            <td class="rl_col_name">
                <div class="form-group mb0 field-access-docs-<?= $uid; ?>-doc_id">
                    <div class="relation-picker" data-id="<?= $uid; ?>">
                        <?=
                        Html::hiddenInput('access-docs[' . $uid . '][doc_id]', $model->doc_id ? $model->doc_id : null, [
                            'class' => 'form-control target_value',
                            'data-text' => $model->doc_id ? $model->doc->fio : '',
                            'id' => 'access-docs-' . $uid . '-doc_id'
                        ]);
                        ?>
                        <div class="clearfix">
                            <div class="btn-ctr pull-right">
                                <span class="item item-open-picker" title="Расширенный поиск"></span>
                                <span class="item item-clear" title="Очистить значение"></span>
                            </div>
                            <div class="search_input-ctr">
                                <?=
                                Html::input('text', '', $model->doc_id ? $model->doc->fio : '', [
                                    'class' => 'form-control search_input',
                                    'placeholder' => 'поиск'
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <p class="help-block help-block-error"></p>
                </div>
            </td>
        </tr>
    </table>

    <script>
        $(document).ready(function () {
            $('.item-<?= $uid; ?> .relation-picker').relationPicker({
                url_picker: '<?= Url::to(['doctor/picker']); ?>',
                url_ac: '<?= Url::to(['doctor/ac', 'q' => '_QUERY_']); ?>',
                event_name: 'doctorPick',
                min_length: 1
            });

            function <?= 'init_' . $uid; ?>() {
                var keys = ['doc_id'];
                $.each(keys, function (k, keyName) {
                    var n = 'access-docs-<?= $uid; ?>-' + keyName;
                    $form.yiiActiveForm('add', {
                        id: n,
                        name: n,
                        container: '.field-' + n,
                        input: '#' + n,
                        error: '.help-block',
                        enableAjaxValidation: true
                    });
                });
            }

            var $form = $('.item-<?= $uid; ?>').closest('form');
            if ($form.data('yiiActiveForm')) {
                <?= 'init_' . $uid; ?>();
            } else {
                $form.on('afterInit', function () {
                    <?= 'init_' . $uid; ?>();
                });
            }
        });
    </script>
</div>