<?php

use yii\helpers\Html;

?>

<?= $template->html; ?>
<?= Html::hiddenInput('templateValues', json_encode($templateValues), [
    'id' => 'templateValues'
]); ?>

<?= Html::hiddenInput('templateVars', json_encode($templateVars), [
    'id' => 'templateVars'
]); ?>

<style>
    .b-reception-ctr .varbox {
        background: #EDEDED;
        border-radius: 4px;
        padding: 1px 3px 3px;
    }

    .b-reception-ctr .varbox .var-icon {
        display: none;
    }

    .b-reception-ctr .edit-input-ctr {
        position: absolute;
        z-index: 2;
        border-radius: 4px;
        background: #fff;
        box-shadow: 1px 1px 4px #35516C;
        min-height: 20px;
        min-width: 100px;
        outline: 0;
        padding: 0px 4px;
    }

    .varbox-rel-complete {
        border-bottom: 1px dotted #8dbb00;
        cursor: help;
    }

    .edit-input-ctr__dropdown {
        max-height: 140px;
        overflow: auto;
    }

    .edit-input-ctr__dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .edit-input-ctr__dropdown ul li {
        cursor: pointer;
        padding: 3px 12px;
        border-top: 1px #eee dashed;
    }

    .edit-input-ctr__dropdown ul li:hover {
        color: #476486;
    }

    .edit-input-ctr__dropdown ul li.active {
        font-weight: 700;
    }
</style>

<script>
    var relValues = <?=json_encode($relValues);?>;
    var templateValues = <?=json_encode($templateValues);?>;
    var templateVars = <?=json_encode($templateVars);?>;

    function autoSave() {
        $('.reception-autosave__saved').addClass('hidden');
        $('.reception-autosave__saved-loader').removeClass('hidden');
        var $f = $('.reception-form');
        $.ajax({
            url: $f.attr('action') + '?autosave=1',
            type: 'post',
            data: $f.serialize(),
            success: function (resp) {
                $('.reception-autosave__saved-time').text(resp);
                $('.reception-autosave__saved').removeClass('hidden');
                $('.reception-autosave__saved-loader').addClass('hidden');
            }
        });
    }

    function openEditor($target) {
        var $ctr = $('<div class="edit-input-ctr" contenteditable="true"></div>');
        var uid = $target.attr('data-uid');
        var id = $target.attr('data-id');
        var targetVar = templateVars[id];
        var value = $.trim($target.text());
        if (value == '<>') {
            value = '';
        }
        if ($ctr.inputmask) {
            $ctr.inputmask('remove');
        }

        $ctr.html(value);

        if (targetVar.type == 'select') {
            $ctr = $('<div class="edit-input-ctr edit-input-ctr__dropdown native-scroll"><ul></ul></div>');
            $.each(targetVar.extra.values, function (k, v) {
                var activeClass = (v == value) ? 'active' : '';
                $('ul', $ctr).append('<li class="' + activeClass + '" data-value="' + v + '">' + v + '</li>');
            });
            $('li', $ctr).on('click', function () {
                var newValue = $(this).attr('data-value');
                templateValues[id] = newValue;
                syncValues();
                $('.b-reception-ctr .varbox[data-id="' + id + '"]').html(newValue);
                $ctr.remove();
            });
            $ctr
                .attr('data-edit-uid', uid)
                .css({
                    top: $target.position().top,
                    left: $target.position().left
                });
            $('.b-reception-inner').append($ctr);
            $(document).on('click.seekSelectBlur', function (event) {
                if (event.target.closest('.varbox') != $target[0]) {
                    $ctr.remove();
                    $(document).off('click.seekSelectBlur');
                }
            });

            return;
        }
        if (targetVar.type == 'int') {
            $ctr.inputmask('integer');
        }
        if (targetVar.type == 'date') {
            $ctr.inputmask('dd.mm.yyyy');
        }
        if (targetVar.type == 'time') {
            $ctr.inputmask('hh:mm');
        }
        $ctr
            .attr('data-edit-uid', uid)
            .css({
                top: $target.position().top,
                left: $target.position().left
            })
            .on('keypress', function (e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    $(this).hide();
                    saveValue(id);
                    return false;
                }
            })
            .on('blur', function () {
                saveValue(id);
            });

        $('.b-reception-inner').append($ctr);
        $ctr.focus();
    }

    function saveValue(id) {
        var $ctr = $('.edit-input-ctr');
        var value = $ctr.text();
        templateValues[id] = value;
        syncValues();
        $('.b-reception-ctr .varbox[data-id="' + id + '"]').html(value != '' ? value : '<>');
        $ctr.remove();
    }

    function syncValues() {
        $('#templateValues').val(JSON.stringify(templateValues));
    }

    $(document).ready(function () {
        $('.b-reception-ctr .varbox').each(function () {
            var id = $(this).attr('data-id');
//            var uid = $(this).attr('data-uid');
//            templateValues[uid] = {
//                var_id: id,
//                value: null,
//                uid: uid
//            };
            if (relValues.hasOwnProperty(id)) {
                $(this)
                    .html(relValues[id])
                    .removeClass('varbox')
                    .addClass('varbox-rel-complete')
                    .attr('title', 'Значение заполнено из предопределенного параметра');
                templateValues[id] = relValues[id];
            } else {
                if (templateValues.hasOwnProperty(id)) {
                    var v = templateValues[id];
                    console.log(v);
                    $(this).html((v != '' && v !== null) ? v : '<>');
                } else {
                    $(this).html('<>');
                }
            }
        });
        syncValues();

        $('.b-reception-ctr .varbox').on('click', function () {
            openEditor($(this));
        });

        autoSaveInterval = setInterval(autoSave, 1000 * 30);
        $('.b-reception-ctr').removeClass('block__loading');
    });
</script>