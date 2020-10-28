<?php

foreach ($models as $model) {
    echo $this->render('_add_row', [
        'model' => $model
    ]);
}