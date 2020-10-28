<?php

namespace app\components\SexPicker;

use yii\widgets\InputWidget;

class SexPicker extends InputWidget
{
    public $form = null;

    public $options = ['conditionRequired' => 'true'];
    
    /**
     * @inheritdoc
     */
//    public function init()
//    {
//        parent::init();
//        $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common') . '/widgets/UploadFile/assets';
//    }

    public function run()
    {
        $this->registerClientScript();

        return $this->render('index',[
            'form' => $this->form,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => $this->options,
            'name' => $this->name,
        ]);
    }

    public function registerClientScript()
    {
        //bundle
//        UploadFileAsset::register($this->view);
//
//        //js code
//        $this->view->registerJs('
//            $(".j-fancybox").fancybox();
//        ');
    }
}