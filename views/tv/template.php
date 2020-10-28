<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php foreach ($template->getWidgets() as $widget) { ?>
    <?php
    $uid = uniqid();
    ?>
    <h2 class="mt30" data-toggle="collapse" data-target="#<?= $uid; ?>">
        <?= $widget->title; ?>
        <span class="ico-collapse"></span>
    </h2>

    <div class="collapse in" id="<?= $uid; ?>">
        <?php
        $view = strtolower((new \ReflectionClass($widget))->getShortName());
        echo $this->render('widget/' . $view, [
            'widget' => $widget
        ]);
        ?>
    </div>
<?php } ?>
    

