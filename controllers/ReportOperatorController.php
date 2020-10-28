<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class ReportOperatorController extends \yii\web\Controller {

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
        $operatorExtens = [];
        $operators = \app\models\User::find()
                ->where([
                    'role_id' => \app\models\User::ROLE_OPERATOR,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->all();
        foreach ($operators as $o) {
            $numbers = $o->asterNumbers;
            foreach ($numbers as $num) {
                if ($num && !isset($operatorExtens[$num])) {
                    $operatorExtens[$num] = $num;
                }
            }
        }

        asort($operatorExtens);

        $searchModel = new \app\models\LogOperatorSearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($searchModel);
        }

        return $this->render('index', [
                    'operatorExtens' => $operatorExtens,
                    'searchModel' => $searchModel,
                        //'periods' => $searchModel->validate() ? $searchModel->search() : false
        ]);
    }

    public function actionLoad() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\LogOperatorSearch();
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

    public function actionDetailed() {
        $operators = \app\models\User::find()
                ->where([
                    'role_id' => \app\models\User::ROLE_OPERATOR
                ])
                ->all();

        $searchModel = new \app\models\LogOperatorDetailSearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($searchModel);
        }

        return $this->render('detailed-index', [
                    'operators' => $operators,
                    'searchModel' => $searchModel,
        ]);
    }
    
    public function actionDetailedLoad() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\LogOperatorDetailSearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->get());

        return $this->render('detailed-load', [
                    'searchModel' => $searchModel,
                    'data' => $searchModel->search()
        ]);
    }
    
    public function actionSummary() {
        $operators = \app\models\User::find()
                ->where([
                    'role_id' => \app\models\User::ROLE_OPERATOR
                ])
                ->all();

        $searchModel = new \app\models\LogOperatorSummarySearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->queryParams);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($searchModel);
        }

        return $this->render('summary-index', [
                    'operators' => $operators,
                    'searchModel' => $searchModel,
        ]);
    }
    
    public function actionSummaryLoad() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\LogOperatorSummarySearch();
        $searchModel->setDefault();
        $searchModel->load(Yii::$app->request->get());
        
        return $this->render('summary-load', [
                    'searchModel' => $searchModel,
                    'users' => $searchModel->search()
        ]);
    }

}
