<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class ReportQueueController extends \yii\web\Controller {

    public function behaviors() {
        return [
            //BaseControllerBehavior::className(),
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
            return $this->redirect(['@web/login']);
        },
            ]
        ];
    }

    public function actionIndex() {        
        $queues = \app\models\LogQueue::find()
                ->select('queue')
                ->where([
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->distinct()
                ->all();

        $searchModel = new \app\models\LogQueueSearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->queryParams);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
                        
            return ActiveForm::validate($searchModel);
        }
        
        return $this->render('index', [
                    'queues' => $queues,
                    'searchModel' => $searchModel,
                    //'periods' => $searchModel->validate() ? $searchModel->search() : false
        ]);
    }

    public function actionLoad() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\LogQueueSearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->get());
        

        return $this->render('load', [
                    'searchModel' => $searchModel,
                    'periods' => $searchModel->search()
        ]);
    }

    protected function _timeToMinutes($time) {
        $res = explode(':', $time);

        return 1 * $res[0] * 60 + 1 * $res[1];
    }

    protected function _minutesToHour($minutes) {

        return str_pad(floor($minutes / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }

}
