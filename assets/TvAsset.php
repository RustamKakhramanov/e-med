<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class TvAsset extends AssetBundle {

    const VERSION = '0.01';

    public $sourcePath = '@app/assets';
    public $css = [
        'css/bootstrap.min.css',
        'css/bootstrap-select.min.css',
        'css/font-awesome.min.css',
        'css/jquery.jscrollpane.css',
        'css/fonts.css',
        'css/owl.carousel.css',
        'css/tv.css',
    ];
    public $js = [
        'js/underscore-min.js',
        'js/bootstrap.min.js',
        'js/bootstrap-select.js',
        'js/owl.carousel.min.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\AssetBundle',
        'yii\bootstrap\BootstrapAsset',
   ];
    public $jsOptions = [ 'position' => \yii\web\View::POS_HEAD];
    
    public function init() {
        foreach ($this->js as $key => $value) {
            $this->js[$key] = $value . '?v=' . self::VERSION;
        }

        foreach ($this->css as $key => $value) {
            $this->css[$key] = $value . '?v=' . self::VERSION;
        }

        parent::init();
    }
}
