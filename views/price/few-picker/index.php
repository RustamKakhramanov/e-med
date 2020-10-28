<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use app\helpers\Utils;
use yii\widgets\ActiveForm;

$this->title = 'Подбор услуг';
?>

<div class="b-picker b-picker__few-price view-outer-ctr">
    <?php
    $formUid = uniqid();
    $form = ActiveForm::begin([
        'action' => ['few-picker'],
        'id' => $formUid,
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{hint}",
        ],
        'validateOnType' => false,
        'enableAjaxValidation' => false
    ]);
    ?>
    <div class="b-picker__few-price-head">
        <h1><?= $this->title; ?></h1>
        <span class="subheader"><ничего не выбрано></span>
    </div>

    <div class="b-picker__few-price-search">
        <?=
        $this->render('_search', [
            'searchModel' => $searchModel,
            'groups' => $groups,
            'form' => $form,
            'formUid' => $formUid
        ]);
        ?>
    </div>

    <div class="b-picker__few-price-left native-scroll">
        <div class="item <?php if ($searchModel->group_id == '') echo 'active'; ?>" data-id="">Все</div>
        <?php foreach ($groups as $group) { ?>
            <div class="item <?php if ($searchModel->group_id == $group->id) echo 'active'; ?>"
                 data-id="<?= $group->id; ?>"><?= $group->name; ?></div>
        <?php } ?>
    </div>

    <div class="b-picker__few-price-right">
        <?=
        $this->render('_right', [
            'dataProvider' => $dataProvider
        ]);
        ?>
    </div>
    <div class="b-picker__few-price-footer">
        <div class="btn btn-lg btn-primary">Сохранить</div>
        <span class="ml10 mr10">или</span>
        <span class="btn btn-sm btn-default">Отмена</span>
    </div>
    <?= Html::activeHiddenInput($searchModel, 'group_id'); ?>
    <?= Html::hiddenInput('target', Yii::$app->request->get('target')); ?>
    <?= Html::hiddenInput('_ids', Yii::$app->request->get('_ids', '[]'), [
        'class' => 'input-ids'
    ]); ?>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .b-picker__few-price {
        overflow: hidden;
        position: relative;
    }

    .b-picker__few-price .col_group {
        width: 30%;
    }

    .b-picker__few-price .col_cost {
        width: 150px;
        text-align: right;
        padding-right: 10px !important;
    }

    .b-picker__few-price-head {
        border-bottom: 1px #eee solid;
        margin: 0px -20px;
        padding: 0px 20px;
    }

    .b-picker__few-price-head h1 {
        display: inline-block;
        margin: 0;
        display: inline-block;
        margin: 20px 0px 10px 0px;
    }

    .b-picker__few-price-head .btn {
        vertical-align: top;
        margin-top: 28px;
        margin-left: 10px;
    }

    .b-picker__few-price-search {
        border-bottom: 1px #eee solid;
        margin: 10px -20px 0px;
        padding: 0px 20px;
    }

    .b-picker__few-price-left {
        position: absolute;
        left: 0px;
        width: 250px;
        top: 146px;
        bottom: 70px;
        background: #f5f7f9;
        overflow: auto;
    }

    .b-picker__few-price-left .item:first-child {
        border-top: 0;
    }

    .b-picker__few-price-left .item {
        padding: 10px 20px;
        border-top: 1px #e3e8ed solid;
        cursor: pointer;
    }

    .b-picker__few-price-left .item:hover {
        background: #E3E8ED;
    }

    .b-picker__few-price-left .item.active,
    .b-picker__few-price-left .item.active:hover {
        background: #416586;
        color: #fff;
    }

    .b-picker__few-price-left-footer {
        position: absolute;
        left: 0px;
        bottom: 0px;
        right: 0px;
        padding: 10px 20px;
        border-top: 1px #eee solid;
    }

    .b-picker__few-price-right {
        position: absolute;
        right: 0px;
        left: 250px;
        top: 146px;
        bottom: 70px;
        padding: 0px 20px;
    }

    .b-picker__few-price_table-body {
        position: absolute;
        top: 48px;
        right: 20px;
        left: 20px;
        bottom: 0px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .b-picker__few-price-footer {
        position: absolute;
        bottom: 0px;
        left: 0px;
        right: 0px;
        padding: 15px 20px;
        background: #f7f6e7;
        color: #89877f;
        font-size: 12px;
    }
</style>

<script>
    function pickerHeight() {
        $('.b-picker__few-price').height($(window).height() - 120);
    }

    var pickedItems = {
        data: [],
        add: function (data) {
            this.data.push(data);
            this._syncLabel()._toIds();
        },
        remove: function (id) { //todo удалять
            var key = false;
            $.each(this.data, function (k, item) {
                if (item.id == id) {
                    key = k;
                    return;
                }
            });

            if (key !== false) {
                this.data.splice(key, 1);
            }
            this._syncLabel()._toIds();
        },
        _syncLabel: function () {
            var text = '<ничего не выбрано>';
            var title = '';
            var cost = 0;
            if (this.data.length) {
                $.each(this.data, function (k, item) {
                    if (title) {
                        title += "\n";
                    }
                    title += item.name;
                    cost += parseFloat(item.cost);
                });
                text = 'выбрано услуг: ' + this.data.length + ' на сумму: ' + num(cost);
            }
            $('.b-picker__few-price-head .subheader').text(text).attr('title', title);

            return this;
        },
        _toIds: function () {
            var ids = [];
            $.each(this.data, function (k, item) {
                ids.push(item.id);
            });
            $('.input-ids').val(JSON.stringify(ids));
        }
    };

    $(document).ready(function () {
        pickerHeight();
        $(window).on('resize', function () {
            setTimeout(pickerHeight, 100);
        });

        $('.b-picker__few-price-footer .btn-primary').on('click', function () {
            if (!pickedItems.data.length) {
                toastr.error('Услуги не выбраны');
                return;
            }

            $(document).trigger('priceFewPick', {
                items: pickedItems.data
            });
            $(this).closest('.modal-wrap').trigger('close');
        });

        $('.b-picker__few-price-footer .btn-default').on('click', function(){
            $(this).closest('.modal-wrap').trigger('close');
        });

        $('.b-picker__few-price-left .item').on('click', function(){
            if (!$(this).hasClass('active')) {
                var id = $(this).attr('data-id');
                $('.b-picker__few-price-left .item').removeClass('active');
                $(this).addClass('active');
                $('#pricesearch-group_id').val(id);
                $('#<?=$formUid;?>').submit();
            }
        });
    });
</script>
