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
class AppAsset extends AssetBundle {

    const VERSION = '0.04';

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/bootstrap.min.css',
        'css/bootstrap-select.min.css',
        'css/toastr.min.css',
        'css/font-awesome.min.css',
        'css/jquery.jscrollpane.css',
        'css/jquery-ui.min.css',
        'css/gridstack.min.css',
        'css/fonts.css',
        'css/style.css',
    ];
    public $js = [
        'js/underscore-min.js',
        'js/bootstrap.min.js',
        'js/bootstrap-select.js',
        'js/typeahead.bundle.js',
        'js/toastr.min.js',
        'js/jquery.jscrollpane.min.js',
        'js/jquery.mousewheel.js',
        'js/jquery-ui.min.js',
        'js/bootbox.min.js',
        'js/moment-with-locales.js',
        'js/reconnecting-websocket.js',
        'js/gridstack.js',
        'js/gridstack.jQueryUI.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\web\AssetBundle',
        //'yii\bootstrap\BootstrapAsset',
        'yii\validators\ValidationAsset',
        'yii\widgets\MaskedInputAsset',
        'yii\widgets\ActiveFormAsset'
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
    
    public static function overrideSystemConfirm() {
        Yii::$app->view->registerJs('
            yii.confirm = function(message, ok, cancel) {
                bootbox.confirm(message, function(result) {
                    if (result) { !ok || ok(); } else { !cancel || cancel(); }
                });
            }
        ');
    }

}
