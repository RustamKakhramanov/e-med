<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="tv-template-oncall">
    <div class="header">Дежурные врачи</div>
    <div class="items mt20">
        <?php if ($widget->todayDoctors) {?>
            <?php foreach ($widget->todayDoctors as $d) {?>
            <div class="item">
                <table>
                        <tr>
                            <td class="photo">
                                <div class="photo-ctr">
                                    <img src="<?= $d->photoUrl; ?>"/>
                                </div>
                            </td>
                            <td class="name">
                                <?=$d->fio;?>
                                <div class="spec">
                                    <?=$d->speciality_main->name;?>
                                </div>
                            </td>
                        </tr>
                    </table>
            </div>
            <?php }?>
        <?php } else {?>
            нет врачей
        <?php }?>
    </div>
</div>