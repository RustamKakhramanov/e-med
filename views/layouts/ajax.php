<?php
/* @var $this \yii\web\View */

//инпутмаск
if ($this->js) {
    if (isset($this->js[$this::POS_HEAD])) { ?>
        <script>
            <?php foreach ($this->js[$this::POS_HEAD] as $entry) {
                echo $entry . "\n";
            }?>
        </script>
        <?php
    }
}
?>
<?= $content ?>

<?php
//скрипты форм
if ($this->js) {
    if (isset($this->js[$this::POS_READY])) {
        ?>
        <script>
            $(document).ready(function () {
                <?php
                foreach ($this->js[$this::POS_READY] as $entry) {
                    echo $entry . "\n";
                }
                ?>
            });
        </script>
        <?php
    }
}
?>