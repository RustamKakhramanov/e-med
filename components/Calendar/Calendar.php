<?php

namespace app\components\Calendar;

use yii\widgets\InputWidget;

class Calendar extends InputWidget {

    public $form = null;
    public $options = [];

    /**
     * @inheritdoc
     */
//    public function init()
//    {
//        parent::init();
//        $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common') . '/widgets/UploadFile/assets';
//    }

    public function run() {
        $this->registerClientScript();

        return $this->render('index', [
                    'form' => $this->form,
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'options' => $this->options,
                    'name' => $this->name
        ]);
    }

    public function registerClientScript() {
        //bundle
//        UploadFileAsset::register($this->view);
//
//        //js code
//        $this->view->registerJs('
//            $(".j-fancybox").fancybox();
//        ');
    }

}
