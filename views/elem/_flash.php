<?php
$flashes = Yii::$app->session->getAllFlashes();

if ($flashes) {
    ?>
    <script>
        $(function () {
            var flashes = <?= json_encode($flashes); ?>;

            $.each(flashes, function (k, v) {
                toastr[v[0]](v[1]);
            });
        });
    </script>
    <?php
}?>