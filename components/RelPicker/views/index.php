<?php

use yii\helpers\Html;

$input = $form->field($model, $field, [
    'template' => '{label}{input}{error}',
    'options' => [
        'id' => $uid . '-container',
        'class' => 'form-group ' . $field . '-ctr ' . $groupClass
    ],
    'selectors' => [
        'input' => '#' . $uid,
        'container' => '#' . $uid . '-container'
    ]
])->hiddenInput([
    'class' => 'form-control target_value',
    'data-text' => $text,
    'id' => $uid,
]);
echo $input->begin();
?>
    <div class="relation-picker" data-id="<?= $uid; ?>">
        <?= Html::label($model->getAttributeLabel($field), $uid, $input->labelOptions); ?>
        <?= Html::activeHiddenInput($model, $field, [
            'class' => 'form-control target_value',
            'data-text' => $text,
            'id' => $uid
        ]) ?>
        <?= Html::error($model, $field, [
            'tag' => 'p',
            'class' => 'help-block help-block-error'
        ]); ?>
        <div class="clearfix">
            <div class="btn-ctr pull-right">
                <span class="item item-open-picker" title="Расширенный поиск"></span>
                <span class="item item-clear" title="Очистить значение"></span>
            </div>
            <div class="search_input-ctr">
                <?=
                Html::input('text', '', $text, [
                    'class' => 'form-control search_input',
                    'placeholder' => 'поиск'
                ]);
                ?>
            </div>
        </div>
    </div>
<?= $input->end(); ?>