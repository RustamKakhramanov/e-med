<?= $reception->html; ?>

<?php
$vars = [];
foreach ($reception->template->templateVars as $var) {
    $vars[$var['id']] = $var->toArray();
};
?>

<script>

    var viewProtocolData = {
        vars: <?= json_encode($vars); ?>,
        values: <?= json_encode($reception->templateValues); ?>
    };

    var viewProtocol = {
        getValue: function (id, uid) {
            var value = false;
            if (viewProtocolData.values.hasOwnProperty(uid)) {
                value = viewProtocolData.values[uid].value;
            }
            return value;
        },
        getType: function (id) {
            var type = false;
            if (viewProtocolData.vars.hasOwnProperty(id)) {
                type = viewProtocolData.vars[id].type;
            }
            return type;
        },
        render: function () {
            var error = false;

            $('.varbox').each(function () {
                var id = $(this).attr('data-id');
                var uid = $(this).attr('data-uid');

                if (!id || !uid) {
                    error = true;
                    return;
                }

                if (!$(this).attr('data-name')) {
                    $(this).attr('data-name', $('.name', $(this)).text());
                    $('.name', $(this)).remove();
                }
                var name = $(this).attr('data-name');
                var value = viewProtocol.getValue(id, uid);

                if (!$('.value', $(this)).length) {
                    $(this).append('<span class="value"></span>');
                }

                if (value !== false) {
                    if (viewProtocol.getType(id) == 'textarea') {
                        value = value.replace(/\n/g, '<br/>');
                    }
                    $('.value', $(this)).html(value);
                } else {
                    //$('.value', $(this)).text('<' + name + '>');
                    $('.value', $(this)).text('');
                }
            });
        }
    };

    $(document).ready(function () {
        viewProtocol.render();
    });
</script>