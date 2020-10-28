<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="pl20 pr20 pb20" style="min-width: 500px;">
    <h1><?= $widget->title; ?></h1>

    <?php
    $formId = uniqid();
    $form = ActiveForm::begin([
                'method' => 'post',
                'id' => $formId,
                'options' => [
                    'class' => 'pb80',
                ],
                'validateOnType' => true,
                'enableAjaxValidation' => true,
    ]);
    ?>

    <div class="discharged-widget">       
        <span class="btn btn-default add-handler"><i class="fa fa-plus mr5"></i>Добавить запись</span>
        <div class="items">
            <?php foreach ($widget->strings as $string) {
                echo $this->render('discharged/row', [
                    'row' => $string
                ]);
            }
            ?>
        </div>
    </div>

    <input type="hidden" name="_from_form" value="1"/>

    <div class="form-end mt30" style="left:0;">
        <div class="btn btn-lg btn-primary js-submit-handler">Сохранить</div>
        <span class="ml10 mr10">или</span>
        <a href="#" class="btn btn-sm btn-default js-cancel-handler">Отменить</a>
    </div>
<?php ActiveForm::end(); ?>
</div>

<style>
    .discharged-widget .item table {
        /*        table-layout: fixed;*/
        width: 100%;
    }

    .discharged-widget .td-photo {
        width: 110px;
    }

    .discharged-widget .td-ctrl {
        width: 40px;
        padding-left: 10px;
    }

    .discharged-widget .upload-handler {
        width: 100px;
        height: 100px;
        border: 1px #416586 dotted;
        text-align: center;
        line-height: 100px;
        cursor: pointer;
        color: #416586;
        border-radius: 2px;
        font-size: 20px;
        float: left;
        background: repeating-linear-gradient( 135deg, #f4f4f4, #f4f4f4 10px, #f9f9f9 10px, #f9f9f9 20px );
    }

    .discharged-widget .upload-handler:hover {
        color: #e76f3f;
        border-color: #e76f3f;
    }

    .discharged-widget .upload-handler.uploaded {
        background-size: contain;
        background-repeat: no-repeat;
        background-position: 50% 50%;
    }

    .discharged-widget .upload-handler.uploaded .fa {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 100%;
        padding: 5px;
    }

    .discharged-widget textarea {
        height: 100px;
        resize: none;
    }
</style>

<script>
    $(document).ready(function () {
        $('#<?= $formId; ?> .js-cancel-handler').on('click', function () {
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('#<?= $formId; ?> .js-submit-handler').on('click', function () {
            $(this).closest('form').submit();
            return false;
        });

        $('#<?= $formId; ?>').on('beforeSubmit', function () {
            var d = $(this).serializeObject();
            tvData.discharged = d.hasOwnProperty('discharged') ? {strings: d.discharged} : {};
            $(this).closest('.modal-wrap').trigger('close');

            return false;
        });

        $(document).off('click', '.discharged-widget .upload-handler').on('click', '.discharged-widget .upload-handler', function () {
            $('.for-upload', $(this).closest('.item')).trigger('click');

            return false;
        });

        $(document).off('change', '.discharged-widget .for-upload').on('change', '.discharged-widget .for-upload', function () {
            var $item = $(this).closest('.item');
            var fd = new FormData();
            fd.append("file", this.files[0]);
            $.ajax({
                url: '<?= Url::to(['discharged-upload']); ?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (resp) {
                    $('.upload-handler', $item).css('background-image', 'url(' + resp.url + ')').addClass('uploaded');
                    $('.discharged-img-input', $item).val(resp.name);
                }
            });
        });

        $('#<?= $formId; ?> .add-handler').on('click', function () {
            $.ajax({
                url: '<?= Url::to(['discharged-row']); ?>',
                success: function (resp) {
                    $('.discharged-widget .items').append(resp);
                }
            });
        });
        
        $(document).off('click', '.discharged-widget .js-remove-handler').on('click', '.discharged-widget .js-remove-handler', function(){
            $(this).closest('.item').remove();
        });
    });
</script>