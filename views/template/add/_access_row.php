<?php
/**
 * @var $model app\models\TemplateSpec
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
                <div class="form-group mb0 field-access-spec-<?= $uid; ?>-spec_id">
                    <div class="relation-picker" data-id="<?= $uid; ?>">
                        <?=
                        Html::hiddenInput('access-spec[' . $uid . '][spec_id]', $model->spec_id ? $model->spec_id : null, [
                            'class' => 'form-control target_value',
                            'data-text' => $model->spec_id ? $model->spec->name : '',
                            'id' => 'access-spec-' . $uid . '-spec_id'
                        ]);
                        ?>
                        <div class="clearfix">
                            <div class="btn-ctr pull-right">
                                <span class="item item-open-picker" title="Расширенный поиск"></span>
                                <span class="item item-clear" title="Очистить значение"></span>
                            </div>
                            <div class="search_input-ctr">
                                <?=
                                Html::input('text', '', $model->spec_id ? $model->spec->name : '', [
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
                url_picker: '<?= Url::to(['speciality/picker']); ?>',
                url_ac: '<?= Url::to(['speciality/ac', 'q' => '_QUERY_']); ?>',
                event_name: 'specPick',
                min_length: 1
            });

            function <?= 'init_' . $uid; ?>() {
                var keys = ['spec_id'];
                $.each(keys, function (k, keyName) {
                    var n = 'access-spec-<?= $uid; ?>-' + keyName;
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