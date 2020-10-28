<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridsterAsset extends AssetBundle {

//    public $basePath = '@webroot';
//    public $baseUrl = '@web';

    public $sourcePath = '@app/assets';
    public $css = [
        'css/jquery.gridster.min.css',
    ];
    public $js = [
        'js/jquery.gridster.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\validators\ValidationAsset',
        'yii\widgets\MaskedInputAsset',
        'yii\widgets\ActiveFormAsset'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
