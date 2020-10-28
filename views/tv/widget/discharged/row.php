<?php
$uid = uniqid();
?>

<div class="item mt20">
    <table>
        <tr>
            <td class="td-photo">
                <div class="upload-handler <?php if (isset($row)) echo 'uploaded';?>" <?php if (isset($row)) echo 'style="background-image:url(/uploads/discharged/' . $row['img'] . ')"'; ?>>
                    <i class="fa fa-upload"></i>                    
                </div>
            </td>
            <td class="td-text">
                <textarea class="form-control" name="discharged[<?= $uid; ?>][text]"><?php if (isset($row)) echo $row['text']; ?></textarea>
            </td>
            <td class="td-ctrl">
                <span class="btn btn-default js-remove-handler"><i class="fa fa-remove"></i></span>
            </td>
        </tr>
    </table>
    <div class="hidden">
        <input type="file" class="for-upload" name="<?= uniqid(); ?>"/>
    </div>
    <input type="hidden" class="discharged-img-input" name="discharged[<?= $uid; ?>][img]" value="<?php if (isset($row)) echo $row['img']; ?>"/>
</div>