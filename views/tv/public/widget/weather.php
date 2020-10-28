<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="tv-template-weather">
    <div class="inner">
        <?php foreach ($widget->forecast as $day) { ?>
            <div class="day">
                <div class="img-ctr">
                    <img src="<?= $day['icon']; ?>"/>
                </div>
                <div class="name"><?= $day['day']; ?></div>
                <div class="temp"><?= $day['value']; ?></div>
            </div>

        <?php } ?>
    </div>
</div>