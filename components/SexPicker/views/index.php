<?php
use yii\helpers\Html;
?>

<?php
if ($model) {
    $t = explode('\\', $model::className());
    $className = end($t);
    $value = $model->$attribute;
}else{
    $className = 'SexPicker';
    $value = $options['value'];
}
$classNameLower = strtolower($className);
?>
<div class="btn-group sexpicker" data-toggle="buttons">
    <label class="btn btn-lg btn-select man <?php if($value == '1') { ?>active<?php }; ?>">
        <?php if ($model){ ?>
            <?= Html::activeRadio($model, $attribute, [
                'value' => 1,
                'label' => null,
                'uncheck' => null,
                'class' => 'j-'.$classNameLower.'-'.$attribute,
                'id' => 'Patients[sex]m'
                
            ]) ?>
        <?php } else { ?>
            <?= Html::radio($name, ($value == 1), [
                'value' => 1,
                'label' => null,
                'uncheck' => null,
                'class' => 'j-'.$classNameLower.'-'.$attribute,
                'id' => 'Patients[sex]m'
            ]) ?>
        <?php } ?>
        <span class="ico-sex-man"></span>М
    </label>
    <label class="btn btn-lg btn-select woman <?php if($value == '0'){ ?>active<?php } ?>">
        <?php if ($model){ ?>
            <?= Html::activeRadio($model, $attribute, [
                'value' => 0,
                'label' => null,
                'uncheck' => null,
                'class' => 'j-'.$classNameLower.'-'.$attribute,
                'id' => 'Patients[sex]j'
            ]) ?>
        <?php } else {?>
            <?= Html::radio($name, ($value == 0), [
                'value' => 0,
                'label' => null,
                'uncheck' => null,
                'class' => 'j-'.$classNameLower.'-'.$attribute,
                'id' => 'Patients[sex]j'
            ]) ?>
        <?php } ?>
        <span class="ico-sex-woman"></span>Ж
    </label>
</div>
